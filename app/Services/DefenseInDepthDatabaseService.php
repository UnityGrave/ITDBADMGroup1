<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class DefenseInDepthDatabaseService
{
    /**
     * Database operation types and their required connection levels
     * Following the "Principle of Least Privilege"
     */
    const OPERATION_TYPES = [
        // READ operations - view data, browse, reports
        'READ' => 'mysql_read_only',
        
        // DATA ENTRY operations - create orders, update profiles, shopping
        'DATA_ENTRY' => 'mysql_data_entry',
        
        // ADMIN operations - manage users, business operations
        'ADMIN_OPS' => 'mysql_admin_ops',
        
        // SYSTEM operations - migrations, emergency access
        'SYSTEM_ADMIN' => 'mysql_system_admin',
    ];

    /**
     * Role-based operation permissions
     * The GATEKEEPER layer - controls what operations each Laravel role can perform
     */
    const ROLE_PERMISSIONS = [
        'Customer' => ['READ', 'DATA_ENTRY'],
        'Employee' => ['READ', 'DATA_ENTRY', 'ADMIN_OPS'],
        'Admin' => ['READ', 'DATA_ENTRY', 'ADMIN_OPS', 'SYSTEM_ADMIN'],
    ];

    /**
     * Current active operation type
     */
    protected static ?string $currentOperation = null;

    /**
     * Current active connection name
     */
    protected static ?string $currentConnection = null;

    /**
     * Execute a database operation with defense-in-depth security
     * 
     * This method acts as both:
     * 1. GATEKEEPER: Checks Laravel role permissions
     * 2. VAULT COORDINATOR: Selects appropriate database connection
     * 
     * @param string $operationType The type of operation (READ, DATA_ENTRY, ADMIN_OPS, SYSTEM_ADMIN)
     * @param callable $callback The database operation to execute
     * @param array $additionalChecks Optional additional authorization checks
     * @return mixed Result of the database operation
     * @throws \Exception If authorization fails or operation is invalid
     */
    public static function executeWithDefenseInDepth(string $operationType, callable $callback, array $additionalChecks = [])
    {
        // STEP 1: GATEKEEPER - Application-level authorization check
        static::checkGatekeeperAuthorization($operationType, $additionalChecks);
        
        // STEP 2: VAULT COORDINATOR - Select appropriate database connection
        $connection = static::getVaultConnection($operationType);
        
        // STEP 3: Execute operation with selected connection
        return static::executeWithConnection($connection, $operationType, $callback);
    }

    /**
     * GATEKEEPER: Check if current user's Laravel role allows this operation
     * 
     * @param string $operationType
     * @param array $additionalChecks
     * @throws \Exception
     */
    protected static function checkGatekeeperAuthorization(string $operationType, array $additionalChecks = []): void
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('GATEKEEPER: Unauthorized access attempt - not authenticated', [
                'operation' => $operationType,
                'ip' => request()->ip()
            ]);
            throw new \Exception('Authentication required for database operations');
        }

        $user = Auth::user();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        // Check if operation type is valid
        if (!isset(static::OPERATION_TYPES[$operationType])) {
            Log::error('GATEKEEPER: Invalid operation type requested', [
                'operation' => $operationType,
                'user_id' => $user->id,
                'user_roles' => $userRoles
            ]);
            throw new \Exception("Invalid database operation type: {$operationType}");
        }

        // Check role-based permissions
        $hasPermission = false;
        foreach ($userRoles as $role) {
            if (isset(static::ROLE_PERMISSIONS[$role]) && 
                in_array($operationType, static::ROLE_PERMISSIONS[$role])) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            Log::warning('GATEKEEPER: Access denied - insufficient role permissions', [
                'operation' => $operationType,
                'user_id' => $user->id,
                'user_roles' => $userRoles,
                'required_permissions' => static::ROLE_PERMISSIONS,
                'ip' => request()->ip()
            ]);
            throw new \Exception("Access denied: Role permissions insufficient for {$operationType} operations");
        }

        // Execute additional authorization checks if provided
        foreach ($additionalChecks as $checkDescription => $check) {
            if (!$check($user)) {
                Log::warning('GATEKEEPER: Additional authorization check failed', [
                    'check' => $checkDescription,
                    'operation' => $operationType,
                    'user_id' => $user->id
                ]);
                throw new \Exception("Access denied: {$checkDescription}");
            }
        }

        Log::info('GATEKEEPER: Authorization successful', [
            'operation' => $operationType,
            'user_id' => $user->id,
            'user_roles' => $userRoles
        ]);
    }

    /**
     * VAULT COORDINATOR: Get appropriate database connection for operation
     * 
     * @param string $operationType
     * @return string Connection name
     */
    protected static function getVaultConnection(string $operationType): string
    {
        $connection = static::OPERATION_TYPES[$operationType];
        
        Log::info('VAULT COORDINATOR: Connection selected', [
            'operation' => $operationType,
            'connection' => $connection,
            'user_id' => auth()->id()
        ]);

        return $connection;
    }

    /**
     * Execute callback with specified database connection
     * 
     * @param string $connection
     * @param string $operationType
     * @param callable $callback
     * @return mixed
     */
    protected static function executeWithConnection(string $connection, string $operationType, callable $callback)
    {
        $originalConnection = Config::get('database.default');
        static::$currentOperation = $operationType;
        static::$currentConnection = $connection;

        try {
            // Test connection before switching
            if (!static::testConnection($connection)) {
                Log::error('VAULT: Database connection test failed', [
                    'connection' => $connection,
                    'operation' => $operationType
                ]);
                throw new \Exception("Database connection failed: {$connection}");
            }

            // Switch to operation-specific connection
            Config::set('database.default', $connection);
            DB::purge();

            Log::info('VAULT: Database connection switched', [
                'from' => $originalConnection,
                'to' => $connection,
                'operation' => $operationType,
                'user_id' => auth()->id()
            ]);

            // Execute the operation
            $result = $callback();

            Log::info('VAULT: Operation completed successfully', [
                'connection' => $connection,
                'operation' => $operationType,
                'user_id' => auth()->id()
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('VAULT: Operation failed', [
                'connection' => $connection,
                'operation' => $operationType,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            throw $e;

        } finally {
            // Always restore original connection
            Config::set('database.default', $originalConnection);
            DB::purge();
            static::$currentOperation = null;
            static::$currentConnection = null;
        }
    }

    /**
     * Test database connection
     * 
     * @param string $connection
     * @return bool
     */
    protected static function testConnection(string $connection): bool
    {
        try {
            $pdo = DB::connection($connection)->getPdo();
            return $pdo !== null;
        } catch (\Exception $e) {
            Log::error("Connection test failed for {$connection}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Convenience methods for common operations
     */

    /**
     * Execute a READ operation (viewing data)
     */
    public static function executeRead(callable $callback)
    {
        return static::executeWithDefenseInDepth('READ', $callback);
    }

    /**
     * Execute a DATA ENTRY operation (creating/updating user data)
     */
    public static function executeDataEntry(callable $callback, array $additionalChecks = [])
    {
        return static::executeWithDefenseInDepth('DATA_ENTRY', $callback, $additionalChecks);
    }

    /**
     * Execute an ADMIN operation (managing users/business)
     */
    public static function executeAdminOps(callable $callback, array $additionalChecks = [])
    {
        return static::executeWithDefenseInDepth('ADMIN_OPS', $callback, $additionalChecks);
    }

    /**
     * Execute a SYSTEM ADMIN operation (migrations, emergency access)
     */
    public static function executeSystemAdmin(callable $callback, array $additionalChecks = [])
    {
        return static::executeWithDefenseInDepth('SYSTEM_ADMIN', $callback, $additionalChecks);
    }

    /**
     * Get current operation and connection info
     */
    public static function getCurrentOperationInfo(): array
    {
        return [
            'operation' => static::$currentOperation,
            'connection' => static::$currentConnection,
            'user_id' => auth()->id(),
            'timestamp' => now()
        ];
    }

    /**
     * Check if user has permission for specific operation
     */
    public static function canPerformOperation(string $operationType, User $user = null): bool
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return false;
        }

        $userRoles = $user->roles->pluck('name')->toArray();
        
        foreach ($userRoles as $role) {
            if (isset(static::ROLE_PERMISSIONS[$role]) && 
                in_array($operationType, static::ROLE_PERMISSIONS[$role])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get available operations for current user
     */
    public static function getAvailableOperations(User $user = null): array
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return [];
        }

        $userRoles = $user->roles->pluck('name')->toArray();
        $availableOperations = [];

        foreach ($userRoles as $role) {
            if (isset(static::ROLE_PERMISSIONS[$role])) {
                $availableOperations = array_merge($availableOperations, static::ROLE_PERMISSIONS[$role]);
            }
        }

        return array_unique($availableOperations);
    }

    /**
     * Get operation types and their descriptions
     */
    public static function getOperationDescriptions(): array
    {
        return [
            'READ' => [
                'description' => 'View data, browse products, read reports',
                'connection' => 'mysql_read_only',
                'privileges' => 'SELECT only'
            ],
            'DATA_ENTRY' => [
                'description' => 'Place orders, update profile, shopping cart',
                'connection' => 'mysql_data_entry',
                'privileges' => 'SELECT, INSERT, limited UPDATE'
            ],
            'ADMIN_OPS' => [
                'description' => 'Manage users, process orders, business operations',
                'connection' => 'mysql_admin_ops',
                'privileges' => 'SELECT, INSERT, UPDATE (no DELETE)'
            ],
            'SYSTEM_ADMIN' => [
                'description' => 'System maintenance, migrations, emergency access',
                'connection' => 'mysql_system_admin', 
                'privileges' => 'ALL PRIVILEGES'
            ]
        ];
    }
} 
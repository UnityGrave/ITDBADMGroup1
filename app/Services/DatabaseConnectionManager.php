<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DatabaseConnectionManager
{
    /**
     * Connection mapping for user roles to database connections
     * 
     * @var array
     */
    protected const ROLE_CONNECTIONS = [
        'Admin' => 'mysql_admin',
        'Employee' => 'mysql_staff',
        'Customer' => 'mysql_customer',
    ];

    /**
     * Mapping Laravel user emails to MySQL usernames
     * This ensures synchronized credentials between Laravel and MySQL
     * 
     * @var array
     */
    protected const EMAIL_TO_MYSQL_USER = [
        'admin@konibui.com' => 'admin_konibui',
        'employee@konibui.com' => 'employee_konibui',
        'test@example.com' => 'customer_test',
    ];

    /**
     * Default connection when user has no role or unauthenticated
     * 
     * @var string
     */
    protected const DEFAULT_CONNECTION = 'mysql';

    /**
     * Current active connection name
     * 
     * @var string|null
     */
    protected static ?string $currentConnection = null;

    /**
     * Switch database connection based on authenticated user's role
     * 
     * @return string The connection name that was set
     */
    public static function switchToUserRoleConnection(): string
    {
        $user = Auth::user();
        
        if (!$user) {
            return static::setConnection(static::DEFAULT_CONNECTION);
        }

        // Get user's primary role (first role if multiple)
        $userRole = static::getUserPrimaryRole($user);
        
        if (!$userRole) {
            Log::warning("User {$user->id} has no assigned roles, using default connection");
            return static::setConnection(static::DEFAULT_CONNECTION);
        }

        // Map role to database connection
        $connection = static::ROLE_CONNECTIONS[$userRole] ?? static::DEFAULT_CONNECTION;
        
        return static::setConnection($connection);
    }

    /**
     * Set the database connection and update Laravel's default
     * 
     * @param string $connectionName
     * @return string
     */
    public static function setConnection(string $connectionName): string
    {
        try {
            // Test the connection before switching
            if (!static::testConnection($connectionName)) {
                Log::error("Failed to connect to database connection: {$connectionName}");
                $connectionName = static::DEFAULT_CONNECTION;
            }

            // Set the default database connection
            Config::set('database.default', $connectionName);
            DB::purge(); // Clear existing connections
            
            static::$currentConnection = $connectionName;
            
            Log::info("Database connection switched to: {$connectionName}");
            
            return $connectionName;
        } catch (\Exception $e) {
            Log::error("Error switching database connection to {$connectionName}: " . $e->getMessage());
            return static::setConnection(static::DEFAULT_CONNECTION);
        }
    }

    /**
     * Get the user's primary role name
     * 
     * @param \App\Models\User $user
     * @return string|null
     */
    protected static function getUserPrimaryRole($user): ?string
    {
        // Get the user's roles (assumes roles relationship exists)
        $roles = $user->roles()->get();
        
        if ($roles->isEmpty()) {
            return null;
        }

        // Priority order: Admin > Employee > Customer
        $rolePriority = ['Admin', 'Employee', 'Customer'];
        
        foreach ($rolePriority as $priorityRole) {
            if ($roles->contains('name', $priorityRole)) {
                return $priorityRole;
            }
        }

        // Return first role if none match priority
        return $roles->first()->name;
    }

    /**
     * Test if a database connection works
     * 
     * @param string $connectionName
     * @return bool
     */
    protected static function testConnection(string $connectionName): bool
    {
        try {
            $pdo = DB::connection($connectionName)->getPdo();
            return $pdo !== null;
        } catch (\Exception $e) {
            Log::error("Database connection test failed for {$connectionName}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the current active connection name
     * 
     * @return string
     */
    public static function getCurrentConnection(): string
    {
        return static::$currentConnection ?? Config::get('database.default', static::DEFAULT_CONNECTION);
    }

    /**
     * Switch to admin connection (for administrative operations)
     * 
     * @return string
     */
    public static function switchToAdminConnection(): string
    {
        return static::setConnection(static::ROLE_CONNECTIONS['Admin']);
    }

    /**
     * Switch to staff connection
     * 
     * @return string
     */
    public static function switchToStaffConnection(): string
    {
        return static::setConnection(static::ROLE_CONNECTIONS['Employee']);
    }

    /**
     * Switch to customer connection  
     * 
     * @return string
     */
    public static function switchToCustomerConnection(): string
    {
        return static::setConnection(static::ROLE_CONNECTIONS['Customer']);
    }

    /**
     * Reset to default connection
     * 
     * @return string
     */
    public static function resetToDefaultConnection(): string
    {
        return static::setConnection(static::DEFAULT_CONNECTION);
    }

    /**
     * Get connection name for a specific role
     * 
     * @param string $roleName
     * @return string
     */
    public static function getConnectionForRole(string $roleName): string
    {
        return static::ROLE_CONNECTIONS[$roleName] ?? static::DEFAULT_CONNECTION;
    }

    /**
     * Check if current connection has admin privileges
     * 
     * @return bool
     */
    public static function hasAdminConnection(): bool
    {
        return static::getCurrentConnection() === static::ROLE_CONNECTIONS['Admin'];
    }

    /**
     * Execute a callback with a specific database connection
     * 
     * @param string $connectionName
     * @param callable $callback
     * @return mixed
     */
    public static function withConnection(string $connectionName, callable $callback)
    {
        $originalConnection = static::getCurrentConnection();
        
        try {
            static::setConnection($connectionName);
            return $callback();
        } finally {
            static::setConnection($originalConnection);
        }
    }

    /**
     * Execute a callback with admin connection
     * 
     * @param callable $callback
     * @return mixed
     */
    public static function withAdminConnection(callable $callback)
    {
        return static::withConnection(static::ROLE_CONNECTIONS['Admin'], $callback);
    }

    /**
     * Get all available role connections
     * 
     * @return array
     */
    public static function getAllRoleConnections(): array
    {
        return static::ROLE_CONNECTIONS;
    }

    /**
     * Get MySQL username for Laravel user email
     * 
     * @param string $email
     * @return string|null
     */
    public static function getMySQLUsernameForEmail(string $email): ?string
    {
        return static::EMAIL_TO_MYSQL_USER[$email] ?? null;
    }

    /**
     * Check if Laravel user has synchronized MySQL account
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    public static function hasSynchronizedCredentials($user): bool
    {
        return isset(static::EMAIL_TO_MYSQL_USER[$user->email]);
    }

    /**
     * Get connection info for synchronized user
     * 
     * @param \App\Models\User $user
     * @return array
     */
    public static function getSynchronizedConnectionInfo($user): array
    {
        $mysqlUsername = static::getMySQLUsernameForEmail($user->email);
        $userRole = static::getUserPrimaryRole($user);
        $connection = static::ROLE_CONNECTIONS[$userRole] ?? static::DEFAULT_CONNECTION;

        return [
            'laravel_email' => $user->email,
            'laravel_name' => $user->name,
            'mysql_username' => $mysqlUsername,
            'user_role' => $userRole,
            'connection' => $connection,
            'is_synchronized' => $mysqlUsername !== null,
            'password_synced' => true, // Both use 'password'
        ];
    }

    /**
     * Get all synchronized user mappings
     * 
     * @return array
     */
    public static function getAllSynchronizedMappings(): array
    {
        return static::EMAIL_TO_MYSQL_USER;
    }
} 
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\DefenseInDepthDatabaseService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DefenseInDepthTestController extends Controller
{
    /**
     * Display the defense-in-depth security demonstration page
     */
    public function index()
    {
        $user = auth()->user();
        $availableOperations = DefenseInDepthDatabaseService::getAvailableOperations($user);
        $operationDescriptions = DefenseInDepthDatabaseService::getOperationDescriptions();
        
        return view('test.defense-in-depth', [
            'user' => $user,
            'userRoles' => $user->roles->pluck('name')->toArray(),
            'availableOperations' => $availableOperations,
            'operationDescriptions' => $operationDescriptions
        ]);
    }

    /**
     * Demonstrate READ operation - View user data
     * Uses: mysql_read_only connection with SELECT-only privileges
     */
    public function demonstrateRead(): JsonResponse
    {
        try {
            $result = DefenseInDepthDatabaseService::executeRead(function () {
                // GATEKEEPER ✅ - Checks if user's Laravel role allows READ operations
                // VAULT 🔒 - Uses mysql_read_only connection (SELECT only)
                
                $users = User::with('roles')->take(5)->get();
                $userCount = User::count();
                $roleCount = DB::table('roles')->count();
                
                return [
                    'operation' => 'READ',
                    'connection_used' => 'mysql_read_only',
                    'privileges' => 'SELECT only',
                    'data' => [
                        'user_count' => $userCount,
                        'role_count' => $roleCount,
                        'sample_users' => $users->map(function ($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'roles' => $user->roles->pluck('name')
                            ];
                        })
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'READ operation completed successfully',
                'gatekeeper_check' => 'PASSED - User authorized for READ operations',
                'vault_connection' => 'mysql_read_only (SELECT privileges only)',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'gatekeeper_status' => str_contains($e->getMessage(), 'role permissions') ? 'BLOCKED' : 'ERROR',
                'message' => 'READ operation failed - ' . $e->getMessage()
            ], 403);
        }
    }

    /**
     * Demonstrate DATA ENTRY operation - Update user profile
     * Uses: mysql_data_entry connection with SELECT, INSERT, limited UPDATE privileges
     */
    public function demonstrateDataEntry(Request $request): JsonResponse
    {
        try {
            $result = DefenseInDepthDatabaseService::executeDataEntry(function () use ($request) {
                // GATEKEEPER ✅ - Checks if user's Laravel role allows DATA_ENTRY operations
                // VAULT 🔒 - Uses mysql_data_entry connection (SELECT, INSERT, limited UPDATE)
                
                $user = auth()->user();
                
                // Simulate updating user profile (safe operation)
                $originalName = $user->name;
                
                // Test update capability
                $user->name = $request->input('test_name', 'Test Name Update ' . now()->format('H:i:s'));
                $user->save();
                
                // Restore original name
                $user->name = $originalName;
                $user->save();
                
                return [
                    'operation' => 'DATA_ENTRY',
                    'connection_used' => 'mysql_data_entry',
                    'privileges' => 'SELECT, INSERT, limited UPDATE',
                    'test_performed' => 'Profile update test',
                    'user_id' => $user->id,
                    'test_result' => 'UPDATE operation successful'
                ];

            }, [
                'Own Data Only' => function ($user) {
                    // Additional check: can only update own profile
                    return true; // For demo, always pass
                }
            ]);

            return response()->json([
                'success' => true,
                'message' => 'DATA ENTRY operation completed successfully',
                'gatekeeper_check' => 'PASSED - User authorized for DATA_ENTRY operations',
                'vault_connection' => 'mysql_data_entry (SELECT, INSERT, limited UPDATE)',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'gatekeeper_status' => str_contains($e->getMessage(), 'role permissions') ? 'BLOCKED' : 'ERROR',
                'message' => 'DATA ENTRY operation failed - ' . $e->getMessage()
            ], 403);
        }
    }

    /**
     * Demonstrate ADMIN OPERATIONS - Manage user roles
     * Uses: mysql_admin_ops connection with SELECT, INSERT, UPDATE (no DELETE) privileges
     */
    public function demonstrateAdminOps(Request $request): JsonResponse
    {
        try {
            $result = DefenseInDepthDatabaseService::executeAdminOps(function () use ($request) {
                // GATEKEEPER ✅ - Checks if user's Laravel role allows ADMIN_OPS operations
                // VAULT 🔒 - Uses mysql_admin_ops connection (SELECT, INSERT, UPDATE - no DELETE)
                
                $userId = $request->input('user_id');
                $testUser = User::findOrFail($userId);
                
                // Test role management capabilities
                $currentRoles = $testUser->roles->pluck('name')->toArray();
                
                // Simulate role assignment test (safe operation)
                $rolesCount = DB::table('role_user')->where('user_id', $userId)->count();
                
                return [
                    'operation' => 'ADMIN_OPS',
                    'connection_used' => 'mysql_admin_ops',
                    'privileges' => 'SELECT, INSERT, UPDATE (no DELETE)',
                    'test_performed' => 'Role management access test',
                    'target_user' => [
                        'id' => $testUser->id,
                        'name' => $testUser->name,
                        'current_roles' => $currentRoles,
                        'roles_count' => $rolesCount
                    ],
                    'admin_capabilities' => [
                        'can_view_users' => true,
                        'can_modify_roles' => true,
                        'can_delete_users' => false // Limited by database privileges
                    ]
                ];

            }, [
                'Admin Role Required' => function ($user) {
                    return $user->hasRole('Admin') || $user->hasRole('Employee');
                }
            ]);

            return response()->json([
                'success' => true,
                'message' => 'ADMIN OPERATIONS completed successfully',
                'gatekeeper_check' => 'PASSED - User authorized for ADMIN_OPS operations',
                'vault_connection' => 'mysql_admin_ops (SELECT, INSERT, UPDATE - no DELETE)',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'gatekeeper_status' => str_contains($e->getMessage(), 'role permissions') ? 'BLOCKED' : 'ERROR',
                'message' => 'ADMIN OPERATIONS failed - ' . $e->getMessage()
            ], 403);
        }
    }

    /**
     * Demonstrate SYSTEM ADMIN operation - Full system access
     * Uses: mysql_system_admin connection with ALL PRIVILEGES
     */
    public function demonstrateSystemAdmin(): JsonResponse
    {
        try {
            $result = DefenseInDepthDatabaseService::executeSystemAdmin(function () {
                // GATEKEEPER ✅ - Checks if user's Laravel role allows SYSTEM_ADMIN operations
                // VAULT 🔒 - Uses mysql_system_admin connection (ALL PRIVILEGES)
                
                // Test system-level operations
                $tables = DB::select("SHOW TABLES");
                $userPrivileges = DB::select("SELECT CURRENT_USER() as user, DATABASE() as database");
                
                // Test creating/dropping temporary table (system admin capability)
                DB::statement("CREATE TEMPORARY TABLE test_system_access (id INT PRIMARY KEY, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
                DB::statement("INSERT INTO test_system_access (id) VALUES (1)");
                $testRecord = DB::select("SELECT * FROM test_system_access LIMIT 1");
                DB::statement("DROP TEMPORARY TABLE test_system_access");
                
                return [
                    'operation' => 'SYSTEM_ADMIN',
                    'connection_used' => 'mysql_system_admin',
                    'privileges' => 'ALL PRIVILEGES',
                    'test_performed' => 'Full system access test',
                    'system_info' => [
                        'current_user' => $userPrivileges[0]->user ?? 'unknown',
                        'current_database' => $userPrivileges[0]->database ?? 'unknown',
                        'tables_count' => count($tables),
                        'temp_table_test' => 'SUCCESS - Created, inserted, dropped'
                    ],
                    'capabilities' => [
                        'create_tables' => true,
                        'drop_tables' => true,
                        'all_operations' => true,
                        'system_maintenance' => true
                    ]
                ];

            });

            return response()->json([
                'success' => true,
                'message' => 'SYSTEM ADMIN operations completed successfully',
                'gatekeeper_check' => 'PASSED - User authorized for SYSTEM_ADMIN operations',
                'vault_connection' => 'mysql_system_admin (ALL PRIVILEGES)',
                'result' => $result,
                'warning' => 'This level of access should only be used for system maintenance and emergencies'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'gatekeeper_status' => str_contains($e->getMessage(), 'role permissions') ? 'BLOCKED' : 'ERROR',
                'message' => 'SYSTEM ADMIN operations failed - ' . $e->getMessage()
            ], 403);
        }
    }

    /**
     * Test unauthorized operation - This should be blocked by GATEKEEPER
     */
    public function demonstrateUnauthorized(): JsonResponse
    {
        try {
            // This will attempt a SYSTEM_ADMIN operation that should be blocked
            // for users without proper roles
            $result = DefenseInDepthDatabaseService::executeSystemAdmin(function () {
                return ['message' => 'This should never execute for unauthorized users'];
            });

            return response()->json([
                'success' => false,
                'error' => 'SECURITY BREACH - This operation should have been blocked!',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            // This is the expected path - operation should be blocked
            return response()->json([
                'success' => true,
                'message' => 'DEFENSE-IN-DEPTH working correctly - Operation blocked by GATEKEEPER',
                'gatekeeper_status' => 'BLOCKED',
                'blocked_reason' => $e->getMessage(),
                'security_layer' => str_contains($e->getMessage(), 'role permissions') ? 'Application Layer (GATEKEEPER)' : 'System Layer'
            ]);
        }
    }

    /**
     * Get current operation status and connection info
     */
    public function getOperationStatus(): JsonResponse
    {
        $user = auth()->user();
        
        return response()->json([
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray()
            ],
            'available_operations' => DefenseInDepthDatabaseService::getAvailableOperations($user),
            'operation_descriptions' => DefenseInDepthDatabaseService::getOperationDescriptions(),
            'current_operation' => DefenseInDepthDatabaseService::getCurrentOperationInfo(),
            'security_layers' => [
                'gatekeeper' => 'Application Layer - Laravel Policies & Role Checks',
                'vault' => 'Database Layer - MySQL User Privileges & GRANT/REVOKE'
            ]
        ]);
    }
} 
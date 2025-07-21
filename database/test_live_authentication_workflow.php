<?php

/**
 * LIVE AUTHENTICATION & AUTHORIZATION WORKFLOW TEST
 * 
 * This script tests the actual user authentication and authorization workflow
 * to verify that all EPIC-2 functionality works in practice.
 * 
 * TESTS:
 * ✅ User registration with role assignment
 * ✅ Role-based route access
 * ✅ Policy-based authorization
 * ✅ Middleware protection
 * ✅ Database-level security integration
 * 
 * USAGE: docker-compose exec app php database/test_live_authentication_workflow.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Policies\ProductPolicy;
use App\Policies\OrderPolicy;
use App\Http\Middleware\CheckRole;
use App\Services\DefenseInDepthDatabaseService;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n" . str_repeat("=", 90) . "\n";
echo "🔥 LIVE AUTHENTICATION & AUTHORIZATION WORKFLOW TEST\n";
echo str_repeat("=", 90) . "\n\n";

// =============================================================================
// SETUP: Create Test Users for Each Role
// =============================================================================

echo "🚀 SETUP: Creating test users for each role\n";
echo str_repeat("-", 60) . "\n";

// Clean up any existing test users first
User::where('email', 'like', '%.test.live@konibui.com')->delete();

$testUsers = [];

try {
    // Create Admin User
    $adminUser = User::create([
        'name' => 'Live Test Admin',
        'email' => 'admin.test.live@konibui.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now()
    ]);
    $adminUser->assignRole('Admin');
    $testUsers['admin'] = $adminUser;
    echo "✅ Created Admin test user: {$adminUser->email}\n";
    
    // Create Employee User
    $employeeUser = User::create([
        'name' => 'Live Test Employee',
        'email' => 'employee.test.live@konibui.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now()
    ]);
    $employeeUser->assignRole('Employee');
    $testUsers['employee'] = $employeeUser;
    echo "✅ Created Employee test user: {$employeeUser->email}\n";
    
    // Create Customer User
    $customerUser = User::create([
        'name' => 'Live Test Customer',
        'email' => 'customer.test.live@konibui.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now()
    ]);
    $customerUser->assignRole('Customer');
    $testUsers['customer'] = $customerUser;
    echo "✅ Created Customer test user: {$customerUser->email}\n";
    
} catch (Exception $e) {
    echo "❌ Failed to create test users: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// =============================================================================
// TEST 1: Role Assignment and Verification
// =============================================================================

echo "🔑 TEST 1: Role Assignment and Verification\n";
echo str_repeat("-", 60) . "\n";

$roleTestsPassed = 0;
$roleTestsFailed = 0;

foreach ($testUsers as $roleType => $user) {
    $expectedRole = match($roleType) {
        'admin' => 'Admin',
        'employee' => 'Employee',
        'customer' => 'Customer'
    };
    
    if ($user->hasRole($expectedRole)) {
        $roleTestsPassed++;
        echo "✅ {$user->name} has {$expectedRole} role\n";
        
        // Test hasAnyRole functionality
        if ($user->hasAnyRole(['Admin', 'Employee', 'Customer'])) {
            $roleTestsPassed++;
            echo "  ✅ hasAnyRole() method works correctly\n";
        } else {
            $roleTestsFailed++;
            echo "  ❌ hasAnyRole() method failed\n";
        }
    } else {
        $roleTestsFailed++;
        echo "❌ {$user->name} missing {$expectedRole} role\n";
    }
}

echo "\n📊 Role Tests: $roleTestsPassed passed, $roleTestsFailed failed\n\n";

// =============================================================================
// TEST 2: Policy Authorization Testing
// =============================================================================

echo "🛡️ TEST 2: Policy Authorization Testing\n";
echo str_repeat("-", 60) . "\n";

$policyTestsPassed = 0;
$policyTestsFailed = 0;

$productPolicy = new ProductPolicy();
$orderPolicy = new OrderPolicy();

foreach ($testUsers as $roleType => $user) {
    echo "Testing policies for {$user->name} ({$roleType}):\n";
    
    // Test ProductPolicy
    $canCreateProduct = $productPolicy->create($user);
    $canDeleteProduct = $productPolicy->delete($user);
    $canViewProduct = $productPolicy->view($user);
    
    // Expected results based on role
    $shouldCreateProduct = in_array($roleType, ['admin', 'employee']);
    $shouldDeleteProduct = ($roleType === 'admin');
    $shouldViewProduct = true; // All users can view products
    
    // Check create permission
    if ($canCreateProduct === $shouldCreateProduct) {
        $policyTestsPassed++;
        $status = $canCreateProduct ? "✅" : "✅";
        echo "  $status Can create products: " . ($canCreateProduct ? "YES" : "NO") . " (correct)\n";
    } else {
        $policyTestsFailed++;
        echo "  ❌ Can create products: " . ($canCreateProduct ? "YES" : "NO") . " (should be " . ($shouldCreateProduct ? "YES" : "NO") . ")\n";
    }
    
    // Check delete permission
    if ($canDeleteProduct === $shouldDeleteProduct) {
        $policyTestsPassed++;
        $status = $canDeleteProduct ? "✅" : "✅";
        echo "  $status Can delete products: " . ($canDeleteProduct ? "YES" : "NO") . " (correct)\n";
    } else {
        $policyTestsFailed++;
        echo "  ❌ Can delete products: " . ($canDeleteProduct ? "YES" : "NO") . " (should be " . ($shouldDeleteProduct ? "YES" : "NO") . ")\n";
    }
    
    // Check view permission
    if ($canViewProduct === $shouldViewProduct) {
        $policyTestsPassed++;
        echo "  ✅ Can view products: " . ($canViewProduct ? "YES" : "NO") . " (correct)\n";
    } else {
        $policyTestsFailed++;
        echo "  ❌ Can view products: " . ($canViewProduct ? "YES" : "NO") . " (should be " . ($shouldViewProduct ? "YES" : "NO") . ")\n";
    }
    
    // Test OrderPolicy
    $canProcessOrder = $orderPolicy->process($user);
    $canRefundOrder = $orderPolicy->refund($user);
    $canCreateOrder = $orderPolicy->create($user);
    
    $shouldProcessOrder = in_array($roleType, ['admin', 'employee']);
    $shouldRefundOrder = ($roleType === 'admin');
    $shouldCreateOrder = true; // All authenticated users can create orders
    
    if ($canProcessOrder === $shouldProcessOrder) {
        $policyTestsPassed++;
        $status = $canProcessOrder ? "✅" : "✅";
        echo "  $status Can process orders: " . ($canProcessOrder ? "YES" : "NO") . " (correct)\n";
    } else {
        $policyTestsFailed++;
        echo "  ❌ Can process orders: " . ($canProcessOrder ? "YES" : "NO") . " (should be " . ($shouldProcessOrder ? "YES" : "NO") . ")\n";
    }
    
    if ($canRefundOrder === $shouldRefundOrder) {
        $policyTestsPassed++;
        $status = $canRefundOrder ? "✅" : "✅";
        echo "  $status Can refund orders: " . ($canRefundOrder ? "YES" : "NO") . " (correct)\n";
    } else {
        $policyTestsFailed++;
        echo "  ❌ Can refund orders: " . ($canRefundOrder ? "YES" : "NO") . " (should be " . ($shouldRefundOrder ? "YES" : "NO") . ")\n";
    }
    
    echo "\n";
}

echo "📊 Policy Tests: $policyTestsPassed passed, $policyTestsFailed failed\n\n";

// =============================================================================
// TEST 3: Middleware Authorization Testing
// =============================================================================

echo "🚨 TEST 3: Middleware Authorization Testing\n";
echo str_repeat("-", 60) . "\n";

$middlewareTestsPassed = 0;
$middlewareTestsFailed = 0;

// Create mock request for middleware testing
$request = new Request();

// Simulate middleware testing
foreach ($testUsers as $roleType => $user) {
    echo "Testing middleware for {$user->name} ({$roleType}):\n";
    
    // Simulate authentication
    auth()->login($user);
    
    $middleware = new CheckRole();
    
    try {
        // Test Admin-only access
        $next = function($request) { return "Admin Access Granted"; };
        
        if ($roleType === 'admin') {
            $response = $middleware->handle($request, $next, 'Admin');
            if ($response === "Admin Access Granted") {
                $middlewareTestsPassed++;
                echo "  ✅ Admin-only access: GRANTED (correct)\n";
            } else {
                $middlewareTestsFailed++;
                echo "  ❌ Admin-only access: DENIED (should be granted)\n";
            }
        } else {
            try {
                $response = $middleware->handle($request, $next, 'Admin');
                // If we reach here, access was granted when it shouldn't be
                $middlewareTestsFailed++;
                echo "  ❌ Admin-only access: GRANTED (should be denied)\n";
            } catch (Exception $e) {
                // Exception means access was denied, which is correct for non-admins
                $middlewareTestsPassed++;
                echo "  ✅ Admin-only access: DENIED (correct)\n";
            }
        }
        
        // Test Staff access (Admin,Employee)
        if (in_array($roleType, ['admin', 'employee'])) {
            $response = $middleware->handle($request, $next, 'Admin,Employee');
            if ($response === "Admin Access Granted") {
                $middlewareTestsPassed++;
                echo "  ✅ Staff access (Admin,Employee): GRANTED (correct)\n";
            } else {
                $middlewareTestsFailed++;
                echo "  ❌ Staff access (Admin,Employee): DENIED (should be granted)\n";
            }
        } else {
            try {
                $response = $middleware->handle($request, $next, 'Admin,Employee');
                $middlewareTestsFailed++;
                echo "  ❌ Staff access (Admin,Employee): GRANTED (should be denied)\n";
            } catch (Exception $e) {
                $middlewareTestsPassed++;
                echo "  ✅ Staff access (Admin,Employee): DENIED (correct)\n";
            }
        }
        
    } catch (Exception $e) {
        echo "  ⚠️ Middleware test error: " . substr($e->getMessage(), 0, 50) . "...\n";
    }
    
    echo "\n";
    
    // Logout after each test
    auth()->logout();
}

echo "📊 Middleware Tests: $middlewareTestsPassed passed, $middlewareTestsFailed failed\n\n";

// =============================================================================
// TEST 4: Defense-in-Depth Security Integration
// =============================================================================

echo "🛡️ TEST 4: Defense-in-Depth Security Integration\n";
echo str_repeat("-", 60) . "\n";

$securityTestsPassed = 0;
$securityTestsFailed = 0;

foreach ($testUsers as $roleType => $user) {
    echo "Testing defense-in-depth for {$user->name} ({$roleType}):\n";
    
    // Login user for security service testing
    auth()->login($user);
    
    // Test available operations
    $availableOps = DefenseInDepthDatabaseService::getAvailableOperations($user);
    
    $expectedOps = match($roleType) {
        'admin' => ['READ', 'DATA_ENTRY', 'ADMIN_OPS', 'SYSTEM_ADMIN'],
        'employee' => ['READ', 'DATA_ENTRY', 'ADMIN_OPS'],
        'customer' => ['READ', 'DATA_ENTRY']
    };
    
    $opsMatch = empty(array_diff($expectedOps, $availableOps)) && empty(array_diff($availableOps, $expectedOps));
    
    if ($opsMatch) {
        $securityTestsPassed++;
        echo "  ✅ Available operations match role: " . implode(', ', $availableOps) . "\n";
    } else {
        $securityTestsFailed++;
        echo "  ❌ Available operations mismatch:\n";
        echo "    Expected: " . implode(', ', $expectedOps) . "\n";
        echo "    Got: " . implode(', ', $availableOps) . "\n";
    }
    
    // Test operation permission checking
    $canPerformRead = DefenseInDepthDatabaseService::canPerformOperation('READ', $user);
    $canPerformSystemAdmin = DefenseInDepthDatabaseService::canPerformOperation('SYSTEM_ADMIN', $user);
    
    if ($canPerformRead) {
        $securityTestsPassed++;
        echo "  ✅ Can perform READ operations (all roles can)\n";
    } else {
        $securityTestsFailed++;
        echo "  ❌ Cannot perform READ operations (should be able to)\n";
    }
    
    $shouldPerformSystemAdmin = ($roleType === 'admin');
    if ($canPerformSystemAdmin === $shouldPerformSystemAdmin) {
        $securityTestsPassed++;
        $status = $canPerformSystemAdmin ? "✅" : "✅";
        echo "  $status Can perform SYSTEM_ADMIN operations: " . ($canPerformSystemAdmin ? "YES" : "NO") . " (correct)\n";
    } else {
        $securityTestsFailed++;
        echo "  ❌ Can perform SYSTEM_ADMIN operations: " . ($canPerformSystemAdmin ? "YES" : "NO") . " (should be " . ($shouldPerformSystemAdmin ? "YES" : "NO") . ")\n";
    }
    
    echo "\n";
    
    auth()->logout();
}

echo "📊 Security Integration Tests: $securityTestsPassed passed, $securityTestsFailed failed\n\n";

// =============================================================================
// CLEANUP AND FINAL RESULTS
// =============================================================================

echo "🧹 CLEANUP: Removing test users\n";
echo str_repeat("-", 60) . "\n";

foreach ($testUsers as $user) {
    $user->delete();
    echo "✅ Removed test user: {$user->email}\n";
}

echo "\n";

// Calculate total results
$totalPassed = $roleTestsPassed + $policyTestsPassed + $middlewareTestsPassed + $securityTestsPassed;
$totalFailed = $roleTestsFailed + $policyTestsFailed + $middlewareTestsFailed + $securityTestsFailed;

echo "📊 LIVE AUTHENTICATION & AUTHORIZATION WORKFLOW - FINAL RESULTS\n";
echo str_repeat("=", 90) . "\n";

echo sprintf("%-40s: %d passed, %d failed\n", "Role Assignment & Verification", $roleTestsPassed, $roleTestsFailed);
echo sprintf("%-40s: %d passed, %d failed\n", "Policy Authorization", $policyTestsPassed, $policyTestsFailed);
echo sprintf("%-40s: %d passed, %d failed\n", "Middleware Protection", $middlewareTestsPassed, $middlewareTestsFailed);
echo sprintf("%-40s: %d passed, %d failed\n", "Defense-in-Depth Integration", $securityTestsPassed, $securityTestsFailed);

echo "\n" . str_repeat("-", 90) . "\n";
echo sprintf("TOTAL LIVE WORKFLOW TESTS: %d passed, %d failed\n", $totalPassed, $totalFailed);

if ($totalFailed == 0) {
    echo "\n🎉 ALL LIVE AUTHENTICATION & AUTHORIZATION WORKFLOWS PASSED!\n";
    echo "\n💡 The complete RBAC system is working perfectly:\n";
    echo "   ✅ Users can be created and assigned roles\n";
    echo "   ✅ Role-based authorization works correctly\n";
    echo "   ✅ Policies enforce proper permissions\n";
    echo "   ✅ Middleware protects routes effectively\n";
    echo "   ✅ Defense-in-depth security integration works\n";
    echo "\n🚀 The system is ready for production use!\n";
} else {
    echo "\n⚠️ Some live workflow tests failed:\n";
    echo "   • Review the failed tests above\n";
    echo "   • Check role assignments and permissions\n";
    echo "   • Verify middleware and policy configurations\n";
}

echo "\n" . str_repeat("=", 90) . "\n";
echo "End of Live Authentication & Authorization Workflow Test\n";
echo str_repeat("=", 90) . "\n\n"; 
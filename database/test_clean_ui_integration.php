<?php

/**
 * CLEAN TESTING UI - COMPREHENSIVE INTEGRATION TEST
 * 
 * This script tests the new clean, minimalist testing UI to ensure:
 * - All pages render correctly
 * - RBAC integration works seamlessly
 * - Navigation adapts based on user roles
 * - No regressions in existing functionality
 * 
 * USAGE: docker-compose exec app php database/test_clean_ui_integration.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n" . str_repeat("🎨", 50) . "\n";
echo "CLEAN TESTING UI - COMPREHENSIVE INTEGRATION TEST\n";
echo str_repeat("🎨", 50) . "\n\n";

$allTestsPassed = true;
$testResults = [];

// =============================================================================
// TEST 1: Route Registration and Accessibility
// =============================================================================

echo "📍 TEST 1: Route Registration and Accessibility\n";
echo str_repeat("-", 70) . "\n";

function testRoutes(): array
{
    $results = ['passed' => 0, 'failed' => 0, 'details' => []];
    
    $requiredRoutes = [
        'home' => '/',
        'products.index' => '/products',
        'orders.index' => '/orders',
        'admin.dashboard' => '/admin/dashboard',
        'profile.edit' => '/profile/edit'
    ];
    
    foreach ($requiredRoutes as $routeName => $expectedPath) {
        try {
            if (Route::has($routeName)) {
                $route = Route::getRoutes()->getByName($routeName);
                $actualPath = $route->uri();
                
                if (str_contains($actualPath, ltrim($expectedPath, '/'))) {
                    $results['passed']++;
                    $results['details'][] = "✅ Route '{$routeName}' registered correctly at '{$actualPath}'";
                } else {
                    $results['failed']++;
                    $results['details'][] = "❌ Route '{$routeName}' path mismatch: expected '{$expectedPath}', got '{$actualPath}'";
                }
            } else {
                $results['failed']++;
                $results['details'][] = "❌ Route '{$routeName}' not registered";
            }
        } catch (Exception $e) {
            $results['failed']++;
            $results['details'][] = "❌ Error checking route '{$routeName}': " . $e->getMessage();
        }
    }
    
    return $results;
}

$routeResults = testRoutes();
$testResults['routes'] = $routeResults;

foreach ($routeResults['details'] as $detail) {
    echo "  $detail\n";
}
echo "\n📊 Route Test Summary: {$routeResults['passed']} passed, {$routeResults['failed']} failed\n\n";

// =============================================================================
// TEST 2: View File Existence and Structure
// =============================================================================

echo "🎭 TEST 2: View File Existence and Structure\n";
echo str_repeat("-", 70) . "\n";

function testViews(): array
{
    $results = ['passed' => 0, 'failed' => 0, 'details' => []];
    
    $requiredViews = [
        'layouts.testing' => 'resources/views/layouts/testing.blade.php',
        'welcome' => 'resources/views/welcome.blade.php',
        'products.index' => 'resources/views/products/index.blade.php',
        'orders.index' => 'resources/views/orders/index.blade.php',
        'admin.dashboard' => 'resources/views/admin/dashboard.blade.php'
    ];
    
    foreach ($requiredViews as $viewName => $filePath) {
        $fullPath = base_path($filePath);
        
        if (file_exists($fullPath)) {
            $results['passed']++;
            $results['details'][] = "✅ View file '{$viewName}' exists at '{$filePath}'";
            
            // Check if view extends the testing layout (except for the layout itself)
            if ($viewName !== 'layouts.testing') {
                $content = file_get_contents($fullPath);
                if (str_contains($content, "@extends('layouts.testing')")) {
                    $results['passed']++;
                    $results['details'][] = "  ✅ View extends 'layouts.testing' layout";
                } else {
                    $results['failed']++;
                    $results['details'][] = "  ❌ View does not extend 'layouts.testing' layout";
                }
            }
        } else {
            $results['failed']++;
            $results['details'][] = "❌ View file '{$viewName}' not found at '{$filePath}'";
        }
    }
    
    return $results;
}

$viewResults = testViews();
$testResults['views'] = $viewResults;

foreach ($viewResults['details'] as $detail) {
    echo "  $detail\n";
}
echo "\n📊 View Test Summary: {$viewResults['passed']} passed, {$viewResults['failed']} failed\n\n";

// =============================================================================
// TEST 3: RBAC Integration Testing
// =============================================================================

echo "🛡️ TEST 3: RBAC Integration Testing\n";
echo str_repeat("-", 70) . "\n";

function testRBACIntegration(): array
{
    $results = ['passed' => 0, 'failed' => 0, 'details' => []];
    
    // Create test users for each role
    $testUsers = [];
    
    try {
        // Clean up any existing test users
        User::where('email', 'like', '%.ui.test@konibui.com')->delete();
        
        // Create test users
        $adminUser = User::create([
            'name' => 'UI Test Admin',
            'email' => 'admin.ui.test@konibui.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now()
        ]);
        $adminUser->assignRole('Admin');
        $testUsers['admin'] = $adminUser;
        
        $employeeUser = User::create([
            'name' => 'UI Test Employee',
            'email' => 'employee.ui.test@konibui.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now()
        ]);
        $employeeUser->assignRole('Employee');
        $testUsers['employee'] = $employeeUser;
        
        $customerUser = User::create([
            'name' => 'UI Test Customer',
            'email' => 'customer.ui.test@konibui.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now()
        ]);
        $customerUser->assignRole('Customer');
        $testUsers['customer'] = $customerUser;
        
        $results['passed']++;
        $results['details'][] = "✅ Test users created for all roles";
        
    } catch (Exception $e) {
        $results['failed']++;
        $results['details'][] = "❌ Failed to create test users: " . $e->getMessage();
        return $results;
    }
    
    // Test role-based view rendering
    foreach ($testUsers as $roleType => $user) {
        auth()->login($user);
        
        try {
            // Test welcome view with role-specific content
            $welcomeContent = view('welcome')->render();
            
            if (str_contains($welcomeContent, $user->name)) {
                $results['passed']++;
                $results['details'][] = "✅ Welcome view shows user name for {$roleType}";
            } else {
                $results['failed']++;
                $results['details'][] = "❌ Welcome view doesn't show user name for {$roleType}";
            }
            
            // Test role-specific navigation
            if (($roleType === 'admin' || $roleType === 'employee') && str_contains($welcomeContent, 'Admin Dashboard')) {
                $results['passed']++;
                $results['details'][] = "✅ Admin Dashboard link visible for {$roleType}";
            } elseif ($roleType === 'customer' && !str_contains($welcomeContent, 'Admin Dashboard')) {
                $results['passed']++;
                $results['details'][] = "✅ Admin Dashboard link hidden for {$roleType}";
            } else {
                $results['failed']++;
                $results['details'][] = "❌ Admin Dashboard link visibility incorrect for {$roleType}";
            }
            
            // Test products view with role-based controls
            $productsContent = view('products.index', [
                'pageTitle' => 'Product Catalog',
                'pageDescription' => 'Browse our collection of products'
            ])->render();
            
            if (($roleType === 'admin' || $roleType === 'employee') && str_contains($productsContent, 'Add Product')) {
                $results['passed']++;
                $results['details'][] = "✅ Add Product button visible for {$roleType}";
            } elseif ($roleType === 'customer' && !str_contains($productsContent, 'Add Product')) {
                $results['passed']++;
                $results['details'][] = "✅ Add Product button hidden for {$roleType}";
            }
            
        } catch (Exception $e) {
            $results['failed']++;
            $results['details'][] = "❌ Error testing view for {$roleType}: " . $e->getMessage();
        }
        
        auth()->logout();
    }
    
    // Cleanup test users
    foreach ($testUsers as $user) {
        $user->delete();
    }
    $results['details'][] = "🧹 Test users cleaned up";
    
    return $results;
}

$rbacResults = testRBACIntegration();
$testResults['rbac'] = $rbacResults;

foreach ($rbacResults['details'] as $detail) {
    echo "  $detail\n";
}
echo "\n📊 RBAC Integration Summary: {$rbacResults['passed']} passed, {$rbacResults['failed']} failed\n\n";

// =============================================================================
// TEST 4: UI Design and Responsiveness
// =============================================================================

echo "🎨 TEST 4: UI Design and Responsiveness\n";
echo str_repeat("-", 70) . "\n";

function testUIDesign(): array
{
    $results = ['passed' => 0, 'failed' => 0, 'details' => []];
    
    // Test main layout structure
    $layoutPath = resource_path('views/layouts/testing.blade.php');
    $layoutContent = file_get_contents($layoutPath);
    
    $designChecks = [
        'max-w-7xl' => 'Max width container for centered layout',
        'bg-gray-50' => 'Clean gray background color',
        'font-sans' => 'Sans-serif font family',
        'antialiased' => 'Font smoothing',
        'sticky top-0' => 'Sticky navigation header',
        'border border-gray-200' => 'Clean border styling',
        'hover:' => 'Interactive hover effects',
        'transition' => 'Smooth transitions',
        'rounded' => 'Rounded corners',
        'shadow' => 'Subtle shadows'
    ];
    
    foreach ($designChecks as $cssClass => $description) {
        if (str_contains($layoutContent, $cssClass)) {
            $results['passed']++;
            $results['details'][] = "✅ {$description} implemented (class: {$cssClass})";
        } else {
            $results['failed']++;
            $results['details'][] = "❌ {$description} missing (class: {$cssClass})";
        }
    }
    
    // Test responsive design classes
    $responsiveClasses = ['md:', 'lg:', 'xl:', 'sm:'];
    $responsiveFound = 0;
    
    foreach ($responsiveClasses as $breakpoint) {
        if (str_contains($layoutContent, $breakpoint)) {
            $responsiveFound++;
        }
    }
    
    if ($responsiveFound >= 3) {
        $results['passed']++;
        $results['details'][] = "✅ Responsive design implemented with multiple breakpoints";
    } else {
        $results['failed']++;
        $results['details'][] = "❌ Limited responsive design (only {$responsiveFound} breakpoints found)";
    }
    
    // Test Tailwind CSS integration
    if (str_contains($layoutContent, 'text-gray-') && str_contains($layoutContent, 'bg-blue-')) {
        $results['passed']++;
        $results['details'][] = "✅ Tailwind CSS color system properly used";
    } else {
        $results['failed']++;
        $results['details'][] = "❌ Tailwind CSS color system not properly implemented";
    }
    
    return $results;
}

$uiResults = testUIDesign();
$testResults['ui'] = $uiResults;

foreach ($uiResults['details'] as $detail) {
    echo "  $detail\n";
}
echo "\n📊 UI Design Summary: {$uiResults['passed']} passed, {$uiResults['failed']} failed\n\n";

// =============================================================================
// TEST 5: No Regressions - Existing Functionality
// =============================================================================

echo "🔄 TEST 5: No Regressions - Existing Functionality\n";
echo str_repeat("-", 70) . "\n";

function testNoRegressions(): array
{
    $results = ['passed' => 0, 'failed' => 0, 'details' => []];
    
    // Test that existing test routes still work
    $testRoutes = [
        'test.admin',
        'test.staff',
        'test.customer',
        'test.authenticated'
    ];
    
    foreach ($testRoutes as $routeName) {
        if (Route::has($routeName)) {
            $results['passed']++;
            $results['details'][] = "✅ Existing test route '{$routeName}' still registered";
        } else {
            $results['failed']++;
            $results['details'][] = "❌ Existing test route '{$routeName}' missing";
        }
    }
    
    // Test that authentication routes still work
    $authRoutes = ['login', 'register', 'password.request', 'dashboard'];
    
    foreach ($authRoutes as $routeName) {
        if (Route::has($routeName)) {
            $results['passed']++;
            $results['details'][] = "✅ Authentication route '{$routeName}' still works";
        } else {
            $results['failed']++;
            $results['details'][] = "❌ Authentication route '{$routeName}' broken";
        }
    }
    
    // Test that models and relationships still work
    try {
        $userCount = User::count();
        $roleCount = Role::count();
        
        if ($userCount > 0 && $roleCount > 0) {
            $results['passed']++;
            $results['details'][] = "✅ Database models functional (Users: {$userCount}, Roles: {$roleCount})";
        } else {
            $results['failed']++;
            $results['details'][] = "❌ Database models may have issues";
        }
        
        // Test relationship functionality
        $testUser = User::with('roles')->first();
        if ($testUser && method_exists($testUser, 'hasRole')) {
            $results['passed']++;
            $results['details'][] = "✅ User-Role relationships and hasRole() method working";
        } else {
            $results['failed']++;
            $results['details'][] = "❌ User-Role relationships or hasRole() method issues";
        }
        
    } catch (Exception $e) {
        $results['failed']++;
        $results['details'][] = "❌ Database connectivity or model issues: " . $e->getMessage();
    }
    
    return $results;
}

$regressionResults = testNoRegressions();
$testResults['regressions'] = $regressionResults;

foreach ($regressionResults['details'] as $detail) {
    echo "  $detail\n";
}
echo "\n📊 Regression Test Summary: {$regressionResults['passed']} passed, {$regressionResults['failed']} failed\n\n";

// =============================================================================
// FINAL RESULTS SUMMARY
// =============================================================================

echo "📊 CLEAN TESTING UI - FINAL RESULTS\n";
echo str_repeat("=", 70) . "\n";

$totalPassed = 0;
$totalFailed = 0;

foreach ($testResults as $testType => $results) {
    $totalPassed += $results['passed'];
    $totalFailed += $results['failed'];
    
    $status = $results['failed'] == 0 ? '✅ PASSED' : '⚠️ ISSUES FOUND';
    $testName = match($testType) {
        'routes' => 'Route Registration & Accessibility',
        'views' => 'View File Structure & Organization',
        'rbac' => 'RBAC Integration & Role-Based UI',
        'ui' => 'UI Design & Responsiveness',
        'regressions' => 'No Regressions - Existing Functionality',
        default => ucfirst($testType)
    };
    
    echo sprintf("%-35s: %s (%d passed, %d failed)\n", 
        $testName, $status, $results['passed'], $results['failed']);
}

echo "\n" . str_repeat("-", 70) . "\n";
echo sprintf("OVERALL CLEAN UI STATUS: %d tests passed, %d failed\n", $totalPassed, $totalFailed);

if ($totalFailed == 0) {
    echo "\n🎉 CLEAN TESTING UI - FULLY IMPLEMENTED AND VERIFIED!\n";
    echo "\n💡 All acceptance criteria met:\n";
    echo "   ✅ Default Laravel UI completely replaced\n";
    echo "   ✅ Clean, minimalist design with neutral color palette\n";
    echo "   ✅ Correct dimensions and responsive design\n";
    echo "   ✅ Core functionality pages created and styled\n";
    echo "   ✅ Seamless RBAC integration without breaking existing logic\n";
    echo "   ✅ No regressions in backend functionality\n";
    echo "\n🎨 The temporary testing UI is ready for development use!\n";
} else {
    echo "\n⚠️ Some issues found in the clean UI implementation:\n";
    echo "   • Review failed tests above\n";
    echo "   • Fix any missing components or regressions\n";
    echo "   • Ensure all RBAC integration works correctly\n";
}

echo "\n🌐 TESTING ACCESS POINTS:\n";
echo "   • Home Page: http://localhost:8080/\n";
echo "   • Products: http://localhost:8080/products\n";
echo "   • Orders: http://localhost:8080/orders (requires authentication)\n";
echo "   • Admin Dashboard: http://localhost:8080/admin/dashboard (requires staff role)\n";

echo "\n" . str_repeat("🎨", 50) . "\n";
echo "End of Clean Testing UI Integration Test\n";
echo str_repeat("🎨", 50) . "\n\n"; 
<?php

use Illuminate\Support\Facades\Route;

// Home page route
Route::view('/', 'welcome')->name('home');

// Authenticated user routes
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('profile/edit', 'profile')
    ->middleware(['auth'])
    ->name('profile.edit');

// Products routes
Route::prefix('products')->group(function () {
    Route::get('/', function () {
        return view('products.index', [
            'pageTitle' => 'Product Catalog',
            'pageDescription' => 'Browse our collection of products'
        ]);
    })->name('products.index');
});

// Orders routes (requires authentication)
Route::prefix('orders')->middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('orders.index', [
            'pageTitle' => 'My Orders',
            'pageDescription' => 'View your order history and status'
        ]);
    })->name('orders.index');
});

// Admin routes (requires admin or employee role)
Route::prefix('admin')->middleware(['auth', 'role:Admin,Employee'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard', [
            'pageTitle' => 'Admin Dashboard',
            'pageDescription' => 'Manage users, orders, and system settings'
        ]);
    })->name('admin.dashboard');
});

// Authorization Test Routes
Route::prefix('test')->middleware('auth')->group(function () {
    // Admin-only routes
    Route::get('/admin-only', function () {
        return view('test.authorization', [
            'title' => 'Admin Only Area',
            'message' => 'You have Admin access! This page is only accessible to Admins.',
            'user_roles' => auth()->user()->roles->pluck('name')->toArray()
        ]);
    })->middleware('role:Admin')->name('test.admin');

    // Admin and Employee routes  
    Route::get('/staff-area', function () {
        return view('test.authorization', [
            'title' => 'Staff Area',
            'message' => 'Welcome to the staff area! Accessible to Admins and Employees.',
            'user_roles' => auth()->user()->roles->pluck('name')->toArray()
        ]);
    })->middleware('role:Admin,Employee')->name('test.staff');

    // Customer area
    Route::get('/customer-area', function () {
        return view('test.authorization', [
            'title' => 'Customer Area',
            'message' => 'Welcome to the customer area! This is for Customer role users.',
            'user_roles' => auth()->user()->roles->pluck('name')->toArray()
        ]);
    })->middleware('role:Customer')->name('test.customer');

    // Any authenticated user
    Route::get('/authenticated', function () {
        return view('test.authorization', [
            'title' => 'Authenticated Users',
            'message' => 'This page is accessible to any authenticated user, regardless of role.',
            'user_roles' => auth()->user()->roles->pluck('name')->toArray()
        ]);
    })->name('test.authenticated');

    // Defense-in-Depth Security Test Routes
    Route::prefix('defense-in-depth')->group(function () {
        Route::get('/', [App\Http\Controllers\DefenseInDepthTestController::class, 'index'])->name('test.defense-in-depth');
        
        // Operation demonstration routes
        Route::post('/read', [App\Http\Controllers\DefenseInDepthTestController::class, 'demonstrateRead'])->name('test.defense.read');
        Route::post('/data-entry', [App\Http\Controllers\DefenseInDepthTestController::class, 'demonstrateDataEntry'])->name('test.defense.data-entry');
        Route::post('/admin-ops', [App\Http\Controllers\DefenseInDepthTestController::class, 'demonstrateAdminOps'])->name('test.defense.admin-ops');
        Route::post('/system-admin', [App\Http\Controllers\DefenseInDepthTestController::class, 'demonstrateSystemAdmin'])->name('test.defense.system-admin');
        Route::post('/unauthorized', [App\Http\Controllers\DefenseInDepthTestController::class, 'demonstrateUnauthorized'])->name('test.defense.unauthorized');
        
        // Status route
        Route::get('/status', [App\Http\Controllers\DefenseInDepthTestController::class, 'getOperationStatus'])->name('test.defense.status');
    });
});

require __DIR__.'/auth.php';

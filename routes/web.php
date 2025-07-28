<?php

use Illuminate\Support\Facades\Route;

use App\Models\Product;
use App\Livewire\Admin\SetsPage;
use App\Livewire\Admin\RaritiesPage;
use App\Livewire\Admin\CardsPage;
use App\Livewire\Admin\ProductsPage;

use App\Livewire\ProductListingPage;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\OrderController;


// Home page route
Route::view("/", "welcome")->name("home");

// Authenticated user routes
Route::view("dashboard", "dashboard")
    ->middleware(["auth", "verified"])
    ->name("dashboard");

Route::view("profile", "profile")
    ->middleware(["auth"])
    ->name("profile");

Route::view("profile/edit", "profile")
    ->middleware(["auth"])
    ->name("profile.edit");

// Products routes
//Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products', function () {
    return view('products.index');
})->name('products.index');
Route::get('/products/{product:sku}', \App\Livewire\ProductDetailPage::class)->name('products.show');




// Orders routes (requires authentication)
Route::prefix("orders")
    ->middleware("auth")
    ->group(function () {
        Route::get("/", [OrderController::class, 'index'])->name("orders.index");
        Route::get("/{order}", [OrderController::class, 'show'])->name("orders.show");
        
        // Admin/Employee routes for order management
        Route::middleware(['role:Admin,Employee'])->group(function () {
            Route::post('/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        });
        
        // Admin-only routes
        Route::middleware(['role:Admin'])->group(function () {
            Route::delete('/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        });
    });

// Admin routes (consolidated - requires admin or employee role for dashboard, admin-only for CRUD)
Route::prefix("admin")
    ->middleware("auth")
    ->group(function () {
        // Admin dashboard (Admin + Employee access)
        Route::get("/dashboard", function () {
            return view("admin.dashboard", [
                "pageTitle" => "Admin Dashboard",
                "pageDescription" =>
                    "Manage users, orders, and system settings",
            ]);
        })
            ->middleware("role:Admin,Employee")
            ->name("admin.dashboard");

        // TCG Management (Admin-only access)
        Route::middleware("role:Admin")->group(function () {
            Route::get("/sets", SetsPage::class)->name("admin.sets");
            Route::get("/rarities", RaritiesPage::class)->name(
                "admin.rarities",
            );
            Route::get("/cards", CardsPage::class)->name("admin.cards");
            Route::get("/products", ProductsPage::class)->name(
                "admin.products",
            );
            
            // User Management routes
            Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
            Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
            Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
        });
    });

// Testing & Authorization Routes
Route::prefix("test")
    ->middleware("auth")
    ->group(function () {
        // Role-based test routes
        Route::get("/admin-only", function () {
            return view("test.authorization", [
                "title" => "Admin Only Area",
                "message" =>
                    "You have Admin access! This page is only accessible to Admins.",
                "user_roles" => auth()->user()->roles->pluck("name")->toArray(),
            ]);
        })
            ->middleware("role:Admin")
            ->name("test.admin");

        Route::get("/staff-area", function () {
            return view("test.authorization", [
                "title" => "Staff Area",
                "message" =>
                    "Welcome to the staff area! Accessible to Admins and Employees.",
                "user_roles" => auth()->user()->roles->pluck("name")->toArray(),
            ]);
        })
            ->middleware("role:Admin,Employee")
            ->name("test.staff");

        Route::get("/customer-area", function () {
            return view("test.authorization", [
                "title" => "Customer Area",
                "message" =>
                    "Welcome to the customer area! This is for Customer role users.",
                "user_roles" => auth()->user()->roles->pluck("name")->toArray(),
            ]);
        })
            ->middleware("role:Customer")
            ->name("test.customer");

        Route::get("/authenticated", function () {
            return view("test.authorization", [
                "title" => "Authenticated Users",
                "message" =>
                    "This page is accessible to any authenticated user, regardless of role.",
                "user_roles" => auth()->user()->roles->pluck("name")->toArray(),
            ]);
        })->name("test.authenticated");

        // Defense-in-Depth Security Test Routes
        Route::prefix("defense-in-depth")->group(function () {
            Route::get("/", [
                App\Http\Controllers\DefenseInDepthTestController::class,
                "index",
            ])->name("test.defense-in-depth");

            // Operation demonstration routes
            Route::post("/read", [
                App\Http\Controllers\DefenseInDepthTestController::class,
                "demonstrateRead",
            ])->name("test.defense.read");
            Route::post("/data-entry", [
                App\Http\Controllers\DefenseInDepthTestController::class,
                "demonstrateDataEntry",
            ])->name("test.defense.data-entry");
            Route::post("/admin-ops", [
                App\Http\Controllers\DefenseInDepthTestController::class,
                "demonstrateAdminOps",
            ])->name("test.defense.admin-ops");
            Route::post("/system-admin", [
                App\Http\Controllers\DefenseInDepthTestController::class,
                "demonstrateSystemAdmin",
            ])->name("test.defense.system-admin");
            Route::post("/unauthorized", [
                App\Http\Controllers\DefenseInDepthTestController::class,
                "demonstrateUnauthorized",
            ])->name("test.defense.unauthorized");

            // Status route
            Route::get("/status", [
                App\Http\Controllers\DefenseInDepthTestController::class,
                "getOperationStatus",
            ])->name("test.defense.status");
        });
    });

// Checkout Routes
Route::get('/checkout', \App\Livewire\CheckoutPage::class)
    ->middleware(['auth', 'role:Customer'])
    ->name('checkout');

Route::get('/order/success/{order}', function ($order) {
    // Find and validate the order
    $orderModel = \App\Models\Order::where('order_number', $order)
        ->where('user_id', auth()->id())
        ->with(['orderItems.product.card.set', 'orderItems.product.card.rarity', 'user'])
        ->firstOrFail();
    
    // Render the view with explicit data (not using Livewire context)
    $orderContent = view('livewire.order-success', [
        'order' => $orderModel
    ])->render();
    
    return view('layouts.app', [
        'slot' => $orderContent
    ]);
})->middleware(['auth', 'role:Customer'])->name('order.success');

// Demo Routes
Route::get('/cart-demo', function () {
    return app(\App\Livewire\CartDemo::class)->render();
})->name('cart.demo');

require __DIR__ . "/auth.php";

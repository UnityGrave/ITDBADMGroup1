<?php

namespace App\Providers;

use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     * 
     * When the Product and Order models are created, Laravel will automatically
     * discover these policies based on naming conventions:
     * - Product model -> ProductPolicy
     * - Order model -> OrderPolicy
     * 
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Explicit policy mappings (will be used when models are created)
        // 'App\Models\Product' => ProductPolicy::class,
        // 'App\Models\Order' => OrderPolicy::class,
        
        // For now, policies exist but models don't yet
        // Laravel will auto-discover them when models are created
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register policy auto-discovery
        $this->registerPolicies();

        // Define any additional gates here if needed
        // Example: Gate::define('admin-only', fn(User $user) => $user->hasRole('Admin'));

        // Super admin gate - for ultimate access (optional)
        Gate::define('super-admin', function ($user) {
            return $user->hasRole('Admin');
        });

        // Staff gate - for admin and employee access
        Gate::define('staff', function ($user) {
            return $user->hasRole('Admin') || $user->hasRole('Employee');
        });

        // Customer gate - for customer-specific actions
        Gate::define('customer', function ($user) {
            return $user->hasRole('Customer');
        });

        // Before gate - runs before all other authorization checks
        Gate::before(function ($user, $ability) {
            // Super admins can do anything
            if ($user->hasRole('Admin')) {
                // Uncomment this line to give Admins unlimited access:
                // return true;
            }
            
            // Let other gates and policies handle authorization
            return null;
        });
    }
} 
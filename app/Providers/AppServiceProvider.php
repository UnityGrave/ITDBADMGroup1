<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\OrderProcessingService;
use App\Services\ExchangeRateService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService();
        });

        $this->app->singleton(OrderProcessingService::class, function ($app) {
            return new OrderProcessingService($app->make(CartService::class));
        });

        $this->app->singleton(ExchangeRateService::class, function ($app) {
            return new ExchangeRateService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

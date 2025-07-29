<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base Currency
    |--------------------------------------------------------------------------
    |
    | The base currency for your application. All exchange rates will be 
    | calculated relative to this currency.
    |
    */

    'base_currency' => env('CURRENCY_BASE', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Exchange Rate API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the exchange rate API service.
    |
    */

    'exchange_rate_api_key' => env('EXCHANGE_RATE_API_KEY', ''), // Free API doesn't need key
    'exchange_rate_api_url' => env('EXCHANGE_RATE_API_URL', 'https://api.fxratesapi.com'),

    /*
    |--------------------------------------------------------------------------
    | Exchange Rate Update Settings
    |--------------------------------------------------------------------------
    |
    | Settings for automatic exchange rate updates.
    |
    */

    'auto_update_rates' => env('CURRENCY_AUTO_UPDATE', true),
    'update_frequency_hours' => env('CURRENCY_UPDATE_FREQUENCY', 24),
    'cache_duration_hours' => env('CURRENCY_CACHE_DURATION', 2),

    /*
    |--------------------------------------------------------------------------
    | API Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Settings to prevent excessive API calls.
    |
    */

    'min_sync_interval_minutes' => env('CURRENCY_MIN_SYNC_INTERVAL', 60),
    'api_timeout_seconds' => env('CURRENCY_API_TIMEOUT', 30),
    'api_retry_attempts' => env('CURRENCY_API_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Default Currency Formatting
    |--------------------------------------------------------------------------
    |
    | Default formatting rules for currencies.
    |
    */

    'default_formatting' => [
        'decimal_places' => 2,
        'decimal_separator' => '.',
        'thousands_separator' => ',',
        'symbol_position' => 'before', // 'before' or 'after'
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Currencies
    |--------------------------------------------------------------------------
    |
    | List of currency codes that are actively supported.
    | These will be included in automatic exchange rate updates.
    |
    */

    'supported_currencies' => [
        'USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD', 'CHF', 'SEK', 'PHP'
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for exchange rate update logging.
    |
    */

    'log_level' => env('CURRENCY_LOG_LEVEL', 'info'),
    'log_channel' => env('CURRENCY_LOG_CHANNEL', 'daily'),

]; 
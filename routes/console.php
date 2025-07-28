<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the currency rate update command to run daily
Schedule::command('currency:update-rates')
    ->daily()
    ->at('02:00')
    ->timezone(config('app.timezone', 'UTC'))
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/currency-updates.log'));

// Add a weekly forced update to ensure rates stay fresh
Schedule::command('currency:update-rates --force')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->timezone(config('app.timezone', 'UTC'))
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/currency-updates.log'));

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds the currencies table with key international currencies for EPIC 8
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing currencies
        Currency::truncate();
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $currencies = [
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.0000, // Base currency
                'is_active' => true,
                'is_base_currency' => true,
                'formatting_rules' => [
                    'decimal_places' => 2,
                    'decimal_separator' => '.',
                    'thousands_separator' => ',',
                    'symbol_position' => 'before',
                ],
                'rate_updated_at' => now(),
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.8500, // Example rate: 1 USD = 0.85 EUR
                'is_active' => true,
                'is_base_currency' => false,
                'formatting_rules' => [
                    'decimal_places' => 2,
                    'decimal_separator' => ',',
                    'thousands_separator' => '.',
                    'symbol_position' => 'after',
                ],
                'rate_updated_at' => now(),
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound Sterling',
                'symbol' => '£',
                'exchange_rate' => 0.7500, // Example rate: 1 USD = 0.75 GBP
                'is_active' => true,
                'is_base_currency' => false,
                'formatting_rules' => [
                    'decimal_places' => 2,
                    'decimal_separator' => '.',
                    'thousands_separator' => ',',
                    'symbol_position' => 'before',
                ],
                'rate_updated_at' => now(),
            ],
            [
                'code' => 'JPY',
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'exchange_rate' => 150.0000, // Example rate: 1 USD = 150 JPY
                'is_active' => true,
                'is_base_currency' => false,
                'formatting_rules' => [
                    'decimal_places' => 0, // JPY has no decimal places
                    'decimal_separator' => '.',
                    'thousands_separator' => ',',
                    'symbol_position' => 'before',
                ],
                'rate_updated_at' => now(),
            ],
            [
                'code' => 'CAD',
                'name' => 'Canadian Dollar',
                'symbol' => 'C$',
                'exchange_rate' => 1.3500, // Example rate: 1 USD = 1.35 CAD
                'is_active' => true,
                'is_base_currency' => false,
                'formatting_rules' => [
                    'decimal_places' => 2,
                    'decimal_separator' => '.',
                    'thousands_separator' => ',',
                    'symbol_position' => 'before',
                ],
                'rate_updated_at' => now(),
            ],
            [
                'code' => 'AUD',
                'name' => 'Australian Dollar',
                'symbol' => 'A$',
                'exchange_rate' => 1.5000, // Example rate: 1 USD = 1.50 AUD
                'is_active' => true,
                'is_base_currency' => false,
                'formatting_rules' => [
                    'decimal_places' => 2,
                    'decimal_separator' => '.',
                    'thousands_separator' => ',',
                    'symbol_position' => 'before',
                ],
                'rate_updated_at' => now(),
            ],
            [
                'code' => 'CHF',
                'name' => 'Swiss Franc',
                'symbol' => 'CHF',
                'exchange_rate' => 0.9000, // Example rate: 1 USD = 0.90 CHF
                'is_active' => true,
                'is_base_currency' => false,
                'formatting_rules' => [
                    'decimal_places' => 2,
                    'decimal_separator' => '.',
                    'thousands_separator' => ',',
                    'symbol_position' => 'after',
                ],
                'rate_updated_at' => now(),
            ],
            [
                'code' => 'SEK',
                'name' => 'Swedish Krona',
                'symbol' => 'kr',
                'exchange_rate' => 10.5000, // Example rate: 1 USD = 10.50 SEK
                'is_active' => true,
                'is_base_currency' => false,
                'formatting_rules' => [
                    'decimal_places' => 2,
                    'decimal_separator' => ',',
                    'thousands_separator' => ' ',
                    'symbol_position' => 'after',
                ],
                'rate_updated_at' => now(),
            ],
            [
                'code' => 'PHP',
                'name' => 'Philippine Peso',
                'symbol' => '₱',
                'exchange_rate' => 56.5000, // Example rate: 1 USD = 56.50 PHP
                'is_active' => true,
                'is_base_currency' => false,
                'formatting_rules' => [
                    'decimal_places' => 2,
                    'decimal_separator' => '.',
                    'thousands_separator' => ',',
                    'symbol_position' => 'before',
                ],
                'rate_updated_at' => now(),
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }

        $this->command->info('Currencies seeded successfully!');
        $this->command->info('Base currency: USD');
        $this->command->info('Total currencies: ' . count($currencies));
    }
} 
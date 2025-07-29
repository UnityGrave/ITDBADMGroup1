<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the currencies table for EPIC 8: Multi-Currency Support
     * Stores currency information including exchange rates
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique()->comment('ISO 4217 currency code (e.g., USD, EUR)');
            $table->string('name', 100)->comment('Full currency name (e.g., US Dollar)');
            $table->string('symbol', 10)->comment('Currency symbol (e.g., $, €, ¥)');
            $table->decimal('exchange_rate', 15, 8)->default(1.0000)->comment('Exchange rate relative to base currency');
            $table->boolean('is_active')->default(true)->comment('Whether this currency is available for use');
            $table->boolean('is_base_currency')->default(false)->comment('Whether this is the base currency');
            $table->json('formatting_rules')->nullable()->comment('Currency-specific formatting rules (decimal places, separators)');
            $table->timestamp('rate_updated_at')->nullable()->comment('When the exchange rate was last updated');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['is_active', 'code']);
            $table->index('is_base_currency');
            $table->index('rate_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
}; 
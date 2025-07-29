<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the product_price_overrides table for EPIC 8: Multi-Currency Support
     * Allows setting specific prices for products in different currencies
     */
    public function up(): void
    {
        Schema::create('product_price_overrides', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned()->comment('Reference to the product');
            $table->string('currency_code', 3)->comment('Currency code for this price override');
            $table->bigInteger('price_cents')->comment('Override price in smallest currency unit (cents)');
            $table->boolean('is_active')->default(true)->comment('Whether this override is currently active');
            $table->text('notes')->nullable()->comment('Optional notes about this price override');
            $table->timestamp('effective_from')->nullable()->comment('When this price override becomes effective');
            $table->timestamp('effective_until')->nullable()->comment('When this price override expires');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('currency_code')->references('code')->on('currencies');
            
            // Unique constraint to prevent duplicate overrides for same product-currency combination
            $table->unique(['product_id', 'currency_code'], 'unique_product_currency_override');
            
            // Indexes for performance
            $table->index(['product_id', 'is_active']);
            $table->index(['currency_code', 'is_active']);
            $table->index('effective_from');
            $table->index('effective_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_overrides');
    }
}; 
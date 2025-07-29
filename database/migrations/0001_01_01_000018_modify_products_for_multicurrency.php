<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Modifies the products table for EPIC 8: Multi-Currency Support
     * - Renames price to base_price and converts to BIGINT (cents)
     * - Adds base_currency_code column
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add new columns first
            $table->string('base_currency_code', 3)->default('USD')->after('price')->comment('Base currency for this product');
            $table->bigInteger('base_price_cents')->after('base_currency_code')->comment('Base price in smallest currency unit (cents)');
            
            // Add foreign key constraint
            $table->foreign('base_currency_code')->references('code')->on('currencies');
            
            // Add index for performance
            $table->index('base_currency_code');
        });
        
        // Convert existing decimal prices to cents (multiply by 100)
        DB::statement('UPDATE products SET base_price_cents = ROUND(price * 100)');
        
        Schema::table('products', function (Blueprint $table) {
            // Drop the old price column after data migration
            $table->dropColumn('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Restore the original price column
            $table->decimal('price', 10, 2)->after('base_price_cents');
        });
        
        // Convert cents back to decimal prices (divide by 100)
        DB::statement('UPDATE products SET price = base_price_cents / 100');
        
        Schema::table('products', function (Blueprint $table) {
            // Drop the multi-currency columns
            $table->dropForeign(['base_currency_code']);
            $table->dropIndex(['base_currency_code']);
            $table->dropColumn(['base_currency_code', 'base_price_cents']);
        });
    }
}; 
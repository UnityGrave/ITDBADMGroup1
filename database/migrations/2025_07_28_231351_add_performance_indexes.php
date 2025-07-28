<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for better query performance
        
        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasIndex('products', 'idx_products_card_price')) {
                $table->index(['card_id', 'base_price_cents'], 'idx_products_card_price');
            }
            if (!Schema::hasIndex('products', 'idx_products_created_at')) {
                $table->index(['created_at'], 'idx_products_created_at');
            }
            if (!Schema::hasIndex('products', 'idx_products_currency_price')) {
                $table->index(['base_currency_code', 'base_price_cents'], 'idx_products_currency_price');
            }
        });
        
        // Cards table indexes
        Schema::table('cards', function (Blueprint $table) {
            if (!Schema::hasIndex('cards', 'idx_cards_name')) {
                $table->index(['name'], 'idx_cards_name');
            }
            if (!Schema::hasIndex('cards', 'idx_cards_relationships')) {
                $table->index(['set_id', 'category_id', 'rarity_id'], 'idx_cards_relationships');
            }
        });
        
        // Cart items table indexes
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasIndex('cart_items', 'idx_cart_items_user_product')) {
                $table->index(['user_id', 'product_id'], 'idx_cart_items_user_product');
            }
        });
        
        // Product price overrides table indexes
        Schema::table('product_price_overrides', function (Blueprint $table) {
            if (!Schema::hasIndex('product_price_overrides', 'idx_price_overrides_product_currency')) {
                $table->index(['product_id', 'currency_code', 'is_active'], 'idx_price_overrides_product_currency');
            }
            if (!Schema::hasIndex('product_price_overrides', 'idx_price_overrides_effective_dates')) {
                $table->index(['effective_from', 'effective_until'], 'idx_price_overrides_effective_dates');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_card_price');
            $table->dropIndex('idx_products_created_at');
            $table->dropIndex('idx_products_currency_price');
        });
        
        Schema::table('cards', function (Blueprint $table) {
            $table->dropIndex('idx_cards_name');
            $table->dropIndex('idx_cards_relationships');
        });
        
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('idx_cart_items_user_product');
        });
        
        Schema::table('product_price_overrides', function (Blueprint $table) {
            $table->dropIndex('idx_price_overrides_product_currency');
            $table->dropIndex('idx_price_overrides_effective_dates');
        });
    }
};

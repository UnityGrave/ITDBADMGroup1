<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add columns as nullable, no FK yet
        Schema::table('orders', function (Blueprint $table) {
            $table->string('currency_code', 3)->nullable()->after('total_amount');
            $table->decimal('exchange_rate', 18, 8)->nullable()->after('currency_code');
            $table->bigInteger('total_in_base_currency')->nullable()->after('exchange_rate');
            $table->index('currency_code');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->bigInteger('price_in_base_currency')->nullable()->after('unit_price');
        });

        // Step 2: Backfill existing orders with base currency and exchange rate
        $baseCurrency = DB::table('currencies')->where('is_base_currency', 1)->first();
        if ($baseCurrency) {
            $baseCode = $baseCurrency->code;
            $baseRate = $baseCurrency->exchange_rate;
            // Set all existing orders to base currency
            DB::table('orders')->whereNull('currency_code')->update([
                'currency_code' => $baseCode,
                'exchange_rate' => $baseRate,
                'total_in_base_currency' => DB::raw('total_amount'),
            ]);
            // Set all existing order_items to base price
            DB::table('order_items')->whereNull('price_in_base_currency')->update([
                'price_in_base_currency' => DB::raw('unit_price'),
            ]);
        }

        // Step 3: Alter columns to be non-nullable and add FK
        Schema::table('orders', function (Blueprint $table) {
            $table->string('currency_code', 3)->nullable(false)->change();
            $table->decimal('exchange_rate', 18, 8)->nullable(false)->change();
            $table->bigInteger('total_in_base_currency')->nullable(false)->change();
            $table->foreign('currency_code')->references('code')->on('currencies')->onDelete('restrict');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->bigInteger('price_in_base_currency')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['currency_code']);
            $table->dropIndex(['currency_code']);
            $table->dropColumn(['currency_code', 'exchange_rate', 'total_in_base_currency']);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('price_in_base_currency');
        });
    }
}; 
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make columns non-nullable
        Schema::table('orders', function (Blueprint $table) {
            $table->string('currency_code', 3)->nullable(false)->change();
            $table->decimal('exchange_rate', 18, 8)->nullable(false)->change();
            $table->bigInteger('total_in_base_currency')->nullable(false)->change();
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->bigInteger('price_in_base_currency')->nullable(false)->change();
        });

        // Add foreign key using schema builder (will skip if already exists)
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('currency_code')->references('code')->on('currencies')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        // No-op: handled by previous migration
    }
}; 
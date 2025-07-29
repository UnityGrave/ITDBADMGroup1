<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('order_items', 'price_in_base_currency')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->bigInteger('price_in_base_currency')->nullable()->after('unit_price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('order_items', 'price_in_base_currency')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('price_in_base_currency');
            });
        }
    }
}; 
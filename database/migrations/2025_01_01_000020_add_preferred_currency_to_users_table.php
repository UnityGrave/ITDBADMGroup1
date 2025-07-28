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
        Schema::table('users', function (Blueprint $table) {
            $table->string('preferred_currency', 3)->nullable()->after('remember_token');
            $table->foreign('preferred_currency')->references('code')->on('currencies')->onDelete('set null');
            $table->index('preferred_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['preferred_currency']);
            $table->dropIndex(['preferred_currency']);
            $table->dropColumn('preferred_currency');
        });
    }
}; 
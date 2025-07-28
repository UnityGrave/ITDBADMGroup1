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
        // Temporarily disable triggers
        DB::statement('SET @TRIGGER_DISABLED = 1');
        
        // Fix invalid condition values to match the ProductCondition enum
        DB::statement("UPDATE products SET `condition` = 'NM' WHERE `condition` = 'mint'");
        DB::statement("UPDATE products SET `condition` = 'NM' WHERE `condition` = 'Mint'");
        DB::statement("UPDATE products SET `condition` = 'NM' WHERE `condition` = 'MINT'");
        
        // Re-enable triggers
        DB::statement('SET @TRIGGER_DISABLED = 0');
        
        // Add any other condition mappings as needed
        // DB::statement("UPDATE products SET `condition` = 'LP' WHERE `condition` = 'lightly_played'");
        // DB::statement("UPDATE products SET `condition` = 'MP' WHERE `condition` = 'moderately_played'");
        // DB::statement("UPDATE products SET `condition` = 'HP' WHERE `condition` = 'heavily_played'");
        // DB::statement("UPDATE products SET `condition` = 'DMG' WHERE `condition` = 'damaged'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't want to reverse this as it would create invalid enum values
        // The original values were invalid anyway
    }
};

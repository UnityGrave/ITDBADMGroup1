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
        // Recreate the price history trigger with correct column references
        DB::statement("
            CREATE TRIGGER tr_products_price_history
            AFTER UPDATE ON products
            FOR EACH ROW
            BEGIN
                -- Log price changes (using base_price_cents instead of price)
                IF OLD.base_price_cents != NEW.base_price_cents THEN
                    INSERT INTO price_history (
                        product_id, old_price, new_price, change_amount,
                        change_percentage, changed_by, created_at
                    ) VALUES (
                        NEW.id, OLD.base_price_cents / 100.0, NEW.base_price_cents / 100.0, 
                        (NEW.base_price_cents - OLD.base_price_cents) / 100.0,
                        ROUND(((NEW.base_price_cents - OLD.base_price_cents) / OLD.base_price_cents) * 100, 2),
                        IFNULL(@current_user_id, 1), NOW()
                    );
                END IF;
            END
        ");
        
        // Recreate the search index trigger with correct column references
        DB::statement("
            CREATE TRIGGER tr_product_search_index_update
            AFTER UPDATE ON products
            FOR EACH ROW
            BEGIN
                -- Update search index when product details change
                IF OLD.base_price_cents != NEW.base_price_cents OR OLD.`condition` != NEW.`condition` THEN
                    INSERT INTO search_index_updates (
                        table_name, record_id, action, created_at
                    ) VALUES (
                        'products', NEW.id, 'update', NOW()
                    );
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the triggers
        DB::statement('DROP TRIGGER IF EXISTS tr_products_price_history');
        DB::statement('DROP TRIGGER IF EXISTS tr_product_search_index_update');
    }
};

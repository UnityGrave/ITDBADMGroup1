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
        // Drop the old trigger that references the non-existent 'price' column
        DB::statement('DROP TRIGGER IF EXISTS tr_products_price_history');
        
        // Create the new trigger with the correct column references
        DB::statement("
            CREATE TRIGGER tr_products_price_history
            AFTER UPDATE ON products
            FOR EACH ROW
            BEGIN
                -- Skip if triggers are disabled
                IF @TRIGGER_DISABLED = 1 THEN
                    LEAVE;
                END IF;
                
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new trigger
        DB::statement('DROP TRIGGER IF EXISTS tr_products_price_history');
        
        // Recreate the old trigger (though it won't work with the new schema)
        DB::statement("
            CREATE TRIGGER tr_products_price_history
            AFTER UPDATE ON products
            FOR EACH ROW
            BEGIN
                -- Log price changes
                IF OLD.price != NEW.price THEN
                    INSERT INTO price_history (
                        product_id, old_price, new_price, change_amount,
                        change_percentage, changed_by, created_at
                    ) VALUES (
                        NEW.id, OLD.price, NEW.price, 
                        (NEW.price - OLD.price),
                        ROUND(((NEW.price - OLD.price) / OLD.price) * 100, 2),
                        IFNULL(@current_user_id, 1), NOW()
                    );
                END IF;
            END
        ");
    }
};

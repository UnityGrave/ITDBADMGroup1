<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Create archived_inventory_adjustments table
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS archived_inventory_adjustments LIKE inventory_adjustments;
            
            -- Add archived_at column if it doesn't exist
            SET @archived_at_exists = (
                SELECT COUNT(*)
                FROM information_schema.columns
                WHERE table_name = "archived_inventory_adjustments"
                AND column_name = "archived_at"
                AND table_schema = DATABASE()
            );
            SET @sql = IF(
                @archived_at_exists = 0,
                "ALTER TABLE archived_inventory_adjustments ADD COLUMN archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                "SELECT 1"
            );
            PREPARE stmt FROM @sql;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        ');

        // Drop and recreate the sp_ArchiveOldOrders procedure with proper FK handling
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_ArchiveOldOrders;
        ');

        DB::unprepared('
            CREATE PROCEDURE sp_ArchiveOldOrders(
                IN p_days_old INT,
                OUT p_archived_count INT
            )
            BEGIN
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET p_archived_count = -1;
                    RESIGNAL;
                END;

                SET p_days_old = IFNULL(p_days_old, 365);
                SET p_archived_count = 0;
                
                START TRANSACTION;
                
                -- First, archive the main orders
                INSERT INTO archived_orders 
                SELECT *, NOW() as archived_at 
                FROM orders 
                WHERE status IN ("delivered", "cancelled", "refunded")
                AND created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                SET p_archived_count = ROW_COUNT();
                
                -- Archive order items
                INSERT INTO archived_order_items
                SELECT oi.*, NOW() as archived_at
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status IN ("delivered", "cancelled", "refunded")
                AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                -- Archive inventory adjustments (if any exist for these orders)
                INSERT INTO archived_inventory_adjustments
                SELECT ia.*, NOW() as archived_at
                FROM inventory_adjustments ia
                JOIN orders o ON ia.order_id = o.id
                WHERE o.status IN ("delivered", "cancelled", "refunded")
                AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                -- Now delete in proper order (children first, then parents)
                
                -- Delete inventory adjustments first (they reference orders)
                DELETE ia FROM inventory_adjustments ia
                JOIN orders o ON ia.order_id = o.id
                WHERE o.status IN ("delivered", "cancelled", "refunded")
                AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                -- Delete order items
                DELETE oi FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status IN ("delivered", "cancelled", "refunded")
                AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                -- Finally delete orders (no more FK references)
                DELETE FROM orders 
                WHERE status IN ("delivered", "cancelled", "refunded")
                AND created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                COMMIT;
            END
        ');
    }

    public function down()
    {
        // Restore original procedure (without FK handling)
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_ArchiveOldOrders;
        ');

        DB::unprepared('
            CREATE PROCEDURE sp_ArchiveOldOrders(
                IN p_days_old INT,
                OUT p_archived_count INT
            )
            BEGIN
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET p_archived_count = -1;
                    RESIGNAL;
                END;

                SET p_days_old = IFNULL(p_days_old, 365);
                SET p_archived_count = 0;
                
                START TRANSACTION;
                
                INSERT INTO archived_orders 
                SELECT *, NOW() as archived_at 
                FROM orders 
                WHERE status IN ("delivered", "cancelled", "refunded")
                AND created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                SET p_archived_count = ROW_COUNT();
                
                INSERT INTO archived_order_items
                SELECT oi.*, NOW() as archived_at
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status IN ("delivered", "cancelled", "refunded")
                AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                DELETE oi FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status IN ("delivered", "cancelled", "refunded")
                AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                DELETE FROM orders 
                WHERE status IN ("delivered", "cancelled", "refunded")
                AND created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                COMMIT;
            END
        ');

        // Drop the archived_inventory_adjustments table
        DB::unprepared('DROP TABLE IF EXISTS archived_inventory_adjustments');
    }
}; 
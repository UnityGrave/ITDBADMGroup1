<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop existing triggers if they exist
        DB::unprepared('
            DROP TRIGGER IF EXISTS tr_orders_inventory_update;
            DROP TRIGGER IF EXISTS tr_products_price_history;
            DROP TRIGGER IF EXISTS tr_cart_items_validation;
            DROP TRIGGER IF EXISTS tr_inventory_low_stock_alert;
            DROP TRIGGER IF EXISTS tr_user_activity_log_insert;
            DROP TRIGGER IF EXISTS tr_user_activity_log_login;
            DROP TRIGGER IF EXISTS tr_order_total_recalculation;
            DROP TRIGGER IF EXISTS tr_product_search_index_update;
            DROP TRIGGER IF EXISTS tr_product_search_index_insert;
        ');

        // 1. tr_orders_inventory_update - Automatic Inventory Management
        DB::unprepared('
            CREATE TRIGGER tr_orders_inventory_update
            AFTER UPDATE ON orders
            FOR EACH ROW
            BEGIN
                -- If order status changes to "shipped", log the shipment
                IF OLD.status != NEW.status AND NEW.status = "shipped" THEN
                    INSERT INTO order_status_log (
                        order_id, old_status, new_status, changed_by, 
                        change_reason, created_at
                    ) VALUES (
                        NEW.id, OLD.status, NEW.status, IFNULL(@current_user_id, 1),
                        "Order shipped", NOW()
                    );
                END IF;
                
                -- If order is cancelled, create inventory adjustment record (backup to stored procedure)
                IF OLD.status != NEW.status AND NEW.status = "cancelled" THEN
                    INSERT INTO inventory_adjustments (
                        order_id, adjustment_type, reason, created_at
                    ) VALUES (
                        NEW.id, "restore", "Order cancelled", NOW()
                    );
                END IF;
            END
        ');

        // 2. tr_products_price_history - Price Change Tracking
        DB::unprepared('
            CREATE TRIGGER tr_products_price_history
            AFTER UPDATE ON products
            FOR EACH ROW
            BEGIN
                -- Log price changes (convert cents to decimal for comparison)
                IF OLD.base_price_cents != NEW.base_price_cents THEN
                    INSERT INTO price_history (
                        product_id, old_price, new_price, change_amount,
                        change_percentage, changed_by, created_at
                    ) VALUES (
                        NEW.id, (OLD.base_price_cents / 100), (NEW.base_price_cents / 100), 
                        ((NEW.base_price_cents - OLD.base_price_cents) / 100),
                        ROUND(((NEW.base_price_cents - OLD.base_price_cents) / OLD.base_price_cents) * 100, 2),
                        IFNULL(@current_user_id, 1), NOW()
                    );
                END IF;
            END
        ');

        // 3. tr_cart_items_validation - Cart Item Validation
        DB::unprepared('
            CREATE TRIGGER tr_cart_items_validation
            BEFORE INSERT ON cart_items
            FOR EACH ROW
            BEGIN
                DECLARE v_stock INT;
                DECLARE v_product_exists INT DEFAULT 0;
                DECLARE v_price DECIMAL(10,2);
                DECLARE v_error_msg TEXT;
                
                -- Validate product exists and get stock
                SELECT COUNT(*) INTO v_product_exists
                FROM products p
                JOIN inventory i ON p.id = i.product_id
                WHERE p.id = NEW.product_id;
                
                IF v_product_exists = 0 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Product not found";
                END IF;
                
                -- Get stock separately
                SELECT i.stock INTO v_stock
                FROM inventory i
                WHERE i.product_id = NEW.product_id;
                
                IF NEW.quantity <= 0 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Quantity must be greater than 0";
                END IF;
                
                IF NEW.quantity > v_stock THEN
                    SET v_error_msg = CONCAT("Insufficient stock. Available: ", v_stock, ", Requested: ", NEW.quantity);
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = v_error_msg;
                END IF;
                
                -- Set timestamps
                SET NEW.created_at = NOW();
                SET NEW.updated_at = NOW();
            END
        ');

        // 4. tr_inventory_low_stock_alert - Stock Threshold Monitoring
        DB::unprepared('
            CREATE TRIGGER tr_inventory_low_stock_alert
            AFTER UPDATE ON inventory
            FOR EACH ROW
            BEGIN
                DECLARE v_product_name VARCHAR(255);
                DECLARE v_threshold INT DEFAULT 5;
                
                -- Get product name from cards table (since products reference cards)
                SELECT c.name INTO v_product_name 
                FROM products p
                JOIN cards c ON p.card_id = c.id
                WHERE p.id = NEW.product_id;
                
                -- Check if stock dropped below threshold
                IF OLD.stock > v_threshold AND NEW.stock <= v_threshold THEN
                    INSERT INTO stock_alerts (
                        product_id, product_name, current_stock, threshold_value,
                        alert_type, message, created_at
                    ) VALUES (
                        NEW.product_id, v_product_name, NEW.stock, v_threshold,
                        "low_stock", 
                        CONCAT("Product \"", v_product_name, "\" is below stock threshold. Current stock: ", CAST(NEW.stock AS CHAR)),
                        NOW()
                    );
                END IF;
                
                -- Check if stock reaches zero
                IF OLD.stock > 0 AND NEW.stock = 0 THEN
                    INSERT INTO stock_alerts (
                        product_id, product_name, current_stock, threshold_value,
                        alert_type, message, created_at
                    ) VALUES (
                        NEW.product_id, v_product_name, NEW.stock, 0,
                        "out_of_stock", 
                        CONCAT("Product \"", v_product_name, "\" is now out of stock"),
                        NOW()
                    );
                END IF;
            END
        ');

        // 5. tr_user_activity_log - User Action Tracking
        DB::unprepared('
            CREATE TRIGGER tr_user_activity_log_insert
            AFTER INSERT ON orders
            FOR EACH ROW
            BEGIN
                INSERT INTO user_activity_log (
                    user_id, activity_type, description, 
                    related_table, related_id, created_at
                ) VALUES (
                    NEW.user_id, "order_placed", 
                    CONCAT("Order placed: ", NEW.order_number, " - Total: $", CAST(NEW.total_amount AS CHAR)),
                    "orders", NEW.id, NOW()
                );
            END
        ');

        // 6. tr_order_total_calculation - Automatic Order Total Updates
        DB::unprepared('
            CREATE TRIGGER tr_order_total_recalculation
            AFTER INSERT ON order_items
            FOR EACH ROW
            BEGIN
                DECLARE v_new_subtotal DECIMAL(10,2);
                DECLARE v_tax_rate DECIMAL(5,4) DEFAULT 0.08;
                DECLARE v_shipping_cost DECIMAL(10,2);
                DECLARE v_tax_amount DECIMAL(10,2);
                DECLARE v_total_amount DECIMAL(10,2);
                
                -- Recalculate order totals
                SELECT SUM(total_price), o.shipping_cost
                INTO v_new_subtotal, v_shipping_cost
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE oi.order_id = NEW.order_id
                GROUP BY o.shipping_cost;
                
                SET v_tax_amount = v_new_subtotal * v_tax_rate;
                SET v_total_amount = v_new_subtotal + v_tax_amount + v_shipping_cost;
                
                -- Update order totals
                UPDATE orders 
                SET subtotal = v_new_subtotal,
                    tax_amount = v_tax_amount,
                    total_amount = v_total_amount,
                    updated_at = NOW()
                WHERE id = NEW.order_id;
            END
        ');

        // 7. tr_product_search_index - Search Index Maintenance
        DB::unprepared('
            CREATE TRIGGER tr_product_search_index_update
            AFTER UPDATE ON products
            FOR EACH ROW
            BEGIN
                -- Update search index when product details change
                IF OLD.base_price_cents != NEW.base_price_cents OR OLD.`condition` != NEW.`condition` THEN
                    INSERT INTO search_index_updates (
                        table_name, record_id, action, created_at
                    ) VALUES (
                        "products", NEW.id, "update", NOW()
                    );
                END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER tr_product_search_index_insert
            AFTER INSERT ON products
            FOR EACH ROW
            BEGIN
                INSERT INTO search_index_updates (
                    table_name, record_id, action, created_at
                ) VALUES (
                    "products", NEW.id, "insert", NOW()
                );
            END
        ');

        // 8. tr_cart_items_update_validation - Cart Update Validation
        DB::unprepared('
            CREATE TRIGGER tr_cart_items_update_validation
            BEFORE UPDATE ON cart_items
            FOR EACH ROW
            BEGIN
                DECLARE v_stock INT;
                DECLARE v_product_exists INT DEFAULT 0;
                DECLARE v_error_msg TEXT;
                
                -- Validate product still exists
                SELECT COUNT(*) INTO v_product_exists
                FROM products p
                JOIN inventory i ON p.id = i.product_id
                WHERE p.id = NEW.product_id;
                
                IF v_product_exists = 0 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Product not found";
                END IF;
                
                -- Get stock separately
                SELECT i.stock INTO v_stock
                FROM inventory i
                WHERE i.product_id = NEW.product_id;
                
                IF NEW.quantity <= 0 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Quantity must be greater than 0";
                END IF;
                
                IF NEW.quantity > v_stock THEN
                    SET v_error_msg = CONCAT("Insufficient stock. Available: ", v_stock, ", Requested: ", NEW.quantity);
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = v_error_msg;
                END IF;
                
                -- Update timestamp
                SET NEW.updated_at = NOW();
            END
        ');
    }

    public function down()
    {
        // Drop all triggers
        DB::unprepared('
            DROP TRIGGER IF EXISTS tr_orders_inventory_update;
            DROP TRIGGER IF EXISTS tr_products_price_history;
            DROP TRIGGER IF EXISTS tr_cart_items_validation;
            DROP TRIGGER IF EXISTS tr_cart_items_update_validation;
            DROP TRIGGER IF EXISTS tr_inventory_low_stock_alert;
            DROP TRIGGER IF EXISTS tr_user_activity_log_insert;
            DROP TRIGGER IF EXISTS tr_order_total_recalculation;
            DROP TRIGGER IF EXISTS tr_product_search_index_update;
            DROP TRIGGER IF EXISTS tr_product_search_index_insert;
        ');
    }
};

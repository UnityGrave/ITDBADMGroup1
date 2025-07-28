<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop existing procedures
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_PlaceOrder;
            DROP PROCEDURE IF EXISTS sp_CancelOrder;
            DROP PROCEDURE IF EXISTS sp_UpdateProductStock;
            DROP PROCEDURE IF EXISTS sp_GetUserOrderHistory;
            DROP PROCEDURE IF EXISTS sp_ProcessRefund;
            DROP PROCEDURE IF EXISTS sp_GetLowStockProducts;
            DROP PROCEDURE IF EXISTS sp_ArchiveOldOrders;
        ');

        // Create supporting tables first
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS inventory_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id BIGINT UNSIGNED NOT NULL,
                old_stock INT NOT NULL,
                new_stock INT NOT NULL,
                change_amount INT NOT NULL,
                reason VARCHAR(255),
                updated_by BIGINT UNSIGNED,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id),
                FOREIGN KEY (updated_by) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS refunds (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id BIGINT UNSIGNED NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                reason TEXT,
                processed_by BIGINT UNSIGNED,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id),
                FOREIGN KEY (processed_by) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS archived_orders LIKE orders;
            SET @archived_at_exists = (
                SELECT COUNT(*)
                FROM information_schema.columns
                WHERE table_name = "archived_orders"
                AND column_name = "archived_at"
                AND table_schema = DATABASE()
            );
            SET @sql = IF(
                @archived_at_exists = 0,
                "ALTER TABLE archived_orders ADD COLUMN archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                "SELECT 1"
            );
            PREPARE stmt FROM @sql;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;

            CREATE TABLE IF NOT EXISTS archived_order_items LIKE order_items;
            SET @archived_at_exists = (
                SELECT COUNT(*)
                FROM information_schema.columns
                WHERE table_name = "archived_order_items"
                AND column_name = "archived_at"
                AND table_schema = DATABASE()
            );
            SET @sql = IF(
                @archived_at_exists = 0,
                "ALTER TABLE archived_order_items ADD COLUMN archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                "SELECT 1"
            );
            PREPARE stmt FROM @sql;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        ');

        // sp_PlaceOrder
        DB::unprepared('
            CREATE PROCEDURE sp_PlaceOrder(
                IN p_user_id BIGINT UNSIGNED,
                IN p_shipping_first_name VARCHAR(50),
                IN p_shipping_last_name VARCHAR(50),
                IN p_shipping_email VARCHAR(100),
                IN p_shipping_phone VARCHAR(20),
                IN p_shipping_address_line_1 VARCHAR(255),
                IN p_shipping_address_line_2 VARCHAR(255),
                IN p_shipping_city VARCHAR(100),
                IN p_shipping_state VARCHAR(50),
                IN p_shipping_postal_code VARCHAR(20),
                IN p_shipping_country VARCHAR(50),
                IN p_payment_method VARCHAR(50),
                IN p_special_instructions TEXT,
                IN p_tax_rate DECIMAL(5,4),
                IN p_shipping_cost DECIMAL(10,2),
                OUT p_order_id BIGINT UNSIGNED,
                OUT p_order_number VARCHAR(20),
                OUT p_total_amount DECIMAL(10,2),
                OUT p_status VARCHAR(20),
                OUT p_message TEXT
            )
            BEGIN
                -- All declarations must come first
                DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0;
                DECLARE v_tax_amount DECIMAL(10,2) DEFAULT 0;
                DECLARE v_total_amount DECIMAL(10,2) DEFAULT 0;
                DECLARE v_product_id BIGINT UNSIGNED;
                DECLARE v_quantity INT;
                DECLARE v_unit_price DECIMAL(10,2);
                DECLARE v_stock INT;
                DECLARE v_product_name VARCHAR(255);
                DECLARE v_product_sku VARCHAR(50);
                DECLARE v_done INT DEFAULT FALSE;
                DECLARE v_order_number VARCHAR(20);
                DECLARE v_has_error INT DEFAULT FALSE;
                
                -- Cursor declaration must come after variable declarations
                DECLARE cart_cursor CURSOR FOR 
                    SELECT ci.product_id, ci.quantity, p.price, i.stock, c.name, p.sku
                    FROM cart_items ci
                    JOIN products p ON ci.product_id = p.id
                    JOIN inventory i ON p.id = i.product_id
                    JOIN cards c ON p.card_id = c.id
                    WHERE ci.user_id = p_user_id;
                    
                -- Handlers must be declared last before the main logic
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    IF v_has_error = FALSE THEN
                        SET p_status = "ERROR";
                        SET p_message = "Order placement failed due to database error";
                        GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
                    END IF;
                END;
                
                -- Input validation
                IF p_shipping_first_name IS NULL OR p_shipping_last_name IS NULL OR 
                   p_shipping_email IS NULL OR p_shipping_phone IS NULL OR
                   p_shipping_address_line_1 IS NULL OR p_shipping_city IS NULL OR 
                   p_shipping_state IS NULL OR p_shipping_postal_code IS NULL THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Missing required shipping information";
                END IF;
                
                -- Initialize status
                SET p_status = "ERROR";
                SET p_message = "";
                
                START TRANSACTION;
                
                IF (SELECT COUNT(*) FROM cart_items WHERE user_id = p_user_id) = 0 THEN
                    SET p_status = "ERROR";
                    SET p_message = "Cart is empty";
                    ROLLBACK;
                ELSE
                    OPEN cart_cursor;
                    read_loop: LOOP
                        FETCH cart_cursor INTO v_product_id, v_quantity, v_unit_price, v_stock, v_product_name, v_product_sku;
                        IF v_done THEN
                            LEAVE read_loop;
                        END IF;
                        
                        IF v_stock < v_quantity THEN
                            SET v_has_error = TRUE;
                            SET p_status = "ERROR";
                            SET p_message = CONCAT("Insufficient stock for product: ", v_product_name);
                            CLOSE cart_cursor;
                            ROLLBACK;
                            LEAVE read_loop;
                        END IF;
                        
                        SET v_subtotal = v_subtotal + (v_unit_price * v_quantity);
                    END LOOP;
                    CLOSE cart_cursor;
                    
                    -- Only continue if no error was set
                    IF v_has_error = FALSE THEN
                        SET v_tax_amount = v_subtotal * p_tax_rate;
                        SET v_total_amount = v_subtotal + v_tax_amount + p_shipping_cost;
                        SET v_order_number = CONCAT("ORD-", DATE_FORMAT(NOW(), "%Y%m%d"), LPAD(FLOOR(RAND() * 10000), 4, "0"));
                        
                        INSERT INTO orders (
                            order_number, user_id, status, payment_method, payment_status,
                            subtotal, tax_amount, shipping_cost, total_amount,
                            shipping_first_name, shipping_last_name, shipping_email, 
                            shipping_phone, shipping_address_line_1, shipping_address_line_2,
                            shipping_city, shipping_state, shipping_postal_code, shipping_country,
                            special_instructions, created_at, updated_at
                        ) VALUES (
                            v_order_number, p_user_id, "pending", p_payment_method, "pending",
                            v_subtotal, v_tax_amount, p_shipping_cost, v_total_amount,
                            p_shipping_first_name, p_shipping_last_name, p_shipping_email,
                            p_shipping_phone, p_shipping_address_line_1, p_shipping_address_line_2,
                            p_shipping_city, p_shipping_state, p_shipping_postal_code, p_shipping_country,
                            p_special_instructions, NOW(), NOW()
                        );
                        
                        SET p_order_id = LAST_INSERT_ID();
                        SET v_done = FALSE;
                        
                        OPEN cart_cursor;
                        insert_loop: LOOP
                            FETCH cart_cursor INTO v_product_id, v_quantity, v_unit_price, v_stock, v_product_name, v_product_sku;
                            IF v_done THEN
                                LEAVE insert_loop;
                            END IF;
                            
                            INSERT INTO order_items (
                                order_id, product_id, product_name, product_sku,
                                unit_price, quantity, total_price, created_at, updated_at
                            ) VALUES (
                                p_order_id, v_product_id, v_product_name, v_product_sku,
                                v_unit_price, v_quantity, (v_unit_price * v_quantity), NOW(), NOW()
                            );
                            
                            UPDATE inventory SET stock = stock - v_quantity WHERE product_id = v_product_id;
                        END LOOP;
                        CLOSE cart_cursor;
                        
                        DELETE FROM cart_items WHERE user_id = p_user_id;
                        
                        SET p_order_number = v_order_number;
                        SET p_total_amount = v_total_amount;
                        SET p_status = "SUCCESS";
                        SET p_message = "Order placed successfully";
                        
                        COMMIT;
                    END IF;
                END IF;
            END
        ');

        // sp_CancelOrder
        DB::unprepared('
            CREATE PROCEDURE sp_CancelOrder(
                IN p_order_id BIGINT UNSIGNED,
                IN p_user_id BIGINT UNSIGNED,
                IN p_cancel_reason TEXT,
                OUT p_status VARCHAR(20),
                OUT p_message TEXT
            )
            BEGIN
                DECLARE v_order_status VARCHAR(50);
                DECLARE v_order_user_id BIGINT UNSIGNED;
                DECLARE v_done INT DEFAULT FALSE;
                DECLARE v_product_id BIGINT UNSIGNED;
                DECLARE v_quantity INT;
                
                DECLARE order_items_cursor CURSOR FOR 
                    SELECT product_id, quantity 
                    FROM order_items 
                    WHERE order_id = p_order_id;
                
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET p_status = "ERROR";
                    GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
                END;
                
                START TRANSACTION;
                
                SELECT status, user_id INTO v_order_status, v_order_user_id 
                FROM orders 
                WHERE id = p_order_id;
                
                IF v_order_user_id IS NULL THEN
                    SET p_status = "ERROR";
                    SET p_message = "Order not found";
                    ROLLBACK;
                ELSEIF v_order_user_id != p_user_id THEN
                    SET p_status = "ERROR";
                    SET p_message = "Unauthorized: Order does not belong to user";
                    ROLLBACK;
                ELSEIF v_order_status IN ("cancelled", "shipped", "delivered") THEN
                    SET p_status = "ERROR";
                    SET p_message = CONCAT("Cannot cancel order with status: ", v_order_status);
                    ROLLBACK;
                ELSE
                    OPEN order_items_cursor;
                    restore_loop: LOOP
                        FETCH order_items_cursor INTO v_product_id, v_quantity;
                        IF v_done THEN
                            LEAVE restore_loop;
                        END IF;
                        
                        UPDATE inventory 
                        SET stock = stock + v_quantity 
                        WHERE product_id = v_product_id;
                    END LOOP;
                    CLOSE order_items_cursor;
                    
                    UPDATE orders 
                    SET status = "cancelled", 
                        notes = CONCAT(IFNULL(notes, ""), "\nCancelled: ", p_cancel_reason),
                        updated_at = NOW()
                    WHERE id = p_order_id;
                    
                    SET p_status = "SUCCESS";
                    SET p_message = "Order cancelled successfully";
                    
                    COMMIT;
                END IF;
            END
        ');

        // sp_UpdateProductStock
        DB::unprepared('
            CREATE PROCEDURE sp_UpdateProductStock(
                IN p_product_id BIGINT UNSIGNED,
                IN p_new_stock INT,
                IN p_reason VARCHAR(255),
                IN p_updated_by BIGINT UNSIGNED,
                OUT p_status VARCHAR(20),
                OUT p_message TEXT
            )
            BEGIN
                DECLARE v_current_stock INT;
                DECLARE v_product_name VARCHAR(255);
                DECLARE v_min_stock INT DEFAULT 5;
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET p_status = "ERROR";
                    GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
                END;
                
                START TRANSACTION;
                
                SELECT i.stock, c.name 
                INTO v_current_stock, v_product_name
                FROM inventory i
                JOIN products p ON i.product_id = p.id
                JOIN cards c ON p.card_id = c.id
                WHERE p.id = p_product_id;
                
                IF v_product_name IS NULL THEN
                    SET p_status = "ERROR";
                    SET p_message = "Product not found";
                    ROLLBACK;
                ELSEIF p_new_stock < 0 THEN
                    SET p_status = "ERROR";
                    SET p_message = "Stock cannot be negative";
                    ROLLBACK;
                ELSE
                    -- Update inventory
                    UPDATE inventory 
                    SET stock = p_new_stock, updated_at = NOW() 
                    WHERE product_id = p_product_id;
                    
                    -- Log to inventory_logs for tracking
                    INSERT INTO inventory_logs (
                        product_id, old_stock, new_stock, change_amount, 
                        reason, updated_by, created_at
                    ) VALUES (
                        p_product_id, v_current_stock, p_new_stock, 
                        (p_new_stock - v_current_stock), p_reason, p_updated_by, NOW()
                    );
                    
                    SET p_status = "SUCCESS";
                    SET p_message = CONCAT("Stock updated for ", v_product_name);
                    
                    IF p_new_stock <= v_min_stock THEN
                        SET p_message = CONCAT(p_message, " (WARNING: Low stock threshold reached)");
                    END IF;
                    
                    COMMIT;
                END IF;
            END
        ');

        // sp_GetUserOrderHistory
        DB::unprepared('
            CREATE PROCEDURE sp_GetUserOrderHistory(
                IN p_user_id BIGINT UNSIGNED,
                IN p_limit INT,
                IN p_offset INT,
                IN p_status VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
            )
            BEGIN
                SET p_limit = IFNULL(p_limit, 50);
                SET p_offset = IFNULL(p_offset, 0);
                
                SELECT 
                    o.id,
                    o.order_number,
                    o.status,
                    o.payment_method,
                    o.payment_status,
                    o.total_amount,
                    o.created_at,
                    o.updated_at,
                    COUNT(oi.id) as item_count,
                    SUM(oi.quantity) as total_quantity
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = p_user_id
                AND (p_status IS NULL OR o.status = p_status)
                GROUP BY o.id, o.order_number, o.status, o.payment_method, 
                         o.payment_status, o.total_amount, o.created_at, o.updated_at
                ORDER BY o.created_at DESC
                LIMIT p_limit OFFSET p_offset;
                
                SELECT COUNT(*) as total_orders
                FROM orders 
                WHERE user_id = p_user_id
                AND (p_status IS NULL OR status = p_status);
            END
        ');

        // sp_ProcessRefund
        DB::unprepared('
            CREATE PROCEDURE sp_ProcessRefund(
                IN p_order_id BIGINT UNSIGNED,
                IN p_refund_amount DECIMAL(10,2),
                IN p_refund_reason TEXT,
                IN p_processed_by BIGINT UNSIGNED,
                OUT p_status VARCHAR(20),
                OUT p_message TEXT
            )
            BEGIN
                DECLARE v_order_total DECIMAL(10,2);
                DECLARE v_order_status VARCHAR(50);
                DECLARE v_done INT DEFAULT FALSE;
                DECLARE v_product_id BIGINT UNSIGNED;
                DECLARE v_quantity INT;
                
                DECLARE order_items_cursor CURSOR FOR 
                    SELECT product_id, quantity 
                    FROM order_items 
                    WHERE order_id = p_order_id;
                
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET p_status = "ERROR";
                    GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
                END;
                
                START TRANSACTION;
                
                SELECT total_amount, status 
                INTO v_order_total, v_order_status
                FROM orders 
                WHERE id = p_order_id;
                
                IF v_order_total IS NULL THEN
                    SET p_status = "ERROR";
                    SET p_message = "Order not found";
                    ROLLBACK;
                ELSEIF v_order_status = "refunded" THEN
                    SET p_status = "ERROR";
                    SET p_message = "Order already refunded";
                    ROLLBACK;
                ELSEIF p_refund_amount > v_order_total THEN
                    SET p_status = "ERROR";
                    SET p_message = "Refund amount cannot exceed order total";
                    ROLLBACK;
                ELSE
                    IF p_refund_amount = v_order_total THEN
                        OPEN order_items_cursor;
                        refund_loop: LOOP
                            FETCH order_items_cursor INTO v_product_id, v_quantity;
                            IF v_done THEN
                                LEAVE refund_loop;
                            END IF;
                            
                            UPDATE inventory 
                            SET stock = stock + v_quantity 
                            WHERE product_id = v_product_id;
                        END LOOP;
                        CLOSE order_items_cursor;
                    END IF;
                    
                    UPDATE orders 
                    SET status = IF(p_refund_amount = v_order_total, "refunded", "processing"),
                        payment_status = "refunded",
                        notes = CONCAT(IFNULL(notes, ""), "\nRefund processed: $", p_refund_amount, " - ", p_refund_reason),
                        updated_at = NOW()
                    WHERE id = p_order_id;
                    
                    INSERT INTO refunds (
                        order_id, amount, reason, processed_by, created_at
                    ) VALUES (
                        p_order_id, p_refund_amount, p_refund_reason, p_processed_by, NOW()
                    );
                    
                    SET p_status = "SUCCESS";
                    SET p_message = CONCAT("Refund of $", p_refund_amount, " processed successfully");
                    
                    COMMIT;
                END IF;
            END
        ');

        // sp_GetLowStockProducts
        DB::unprepared('
            CREATE PROCEDURE sp_GetLowStockProducts(
                IN p_threshold INT
            )
            BEGIN
                SET p_threshold = IFNULL(p_threshold, 5);
                SELECT 
                    p.id,
                    c.name as card_name,
                    p.sku,
                    p.condition,
                    s.name as set_name,
                    r.name as rarity_name,
                    i.stock,
                    p.price
                FROM products p
                JOIN inventory i ON p.id = i.product_id
                JOIN cards c ON p.card_id = c.id
                JOIN sets s ON c.set_id = s.id
                JOIN rarities r ON c.rarity_id = r.id
                WHERE i.stock <= p_threshold
                ORDER BY i.stock ASC, c.name ASC;
            END
        ');

        // sp_ArchiveOldOrders
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
    }

    public function down()
    {
        // Drop stored procedures first
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_PlaceOrder;
            DROP PROCEDURE IF EXISTS sp_CancelOrder;
            DROP PROCEDURE IF EXISTS sp_UpdateProductStock;
            DROP PROCEDURE IF EXISTS sp_GetUserOrderHistory;
            DROP PROCEDURE IF EXISTS sp_ProcessRefund;
            DROP PROCEDURE IF EXISTS sp_GetLowStockProducts;
            DROP PROCEDURE IF EXISTS sp_ArchiveOldOrders;
        ');

        // Drop supporting tables
        DB::unprepared('
            DROP TABLE IF EXISTS archived_order_items;
            DROP TABLE IF EXISTS archived_orders;
            DROP TABLE IF EXISTS refunds;
            DROP TABLE IF EXISTS inventory_logs;
        ');
    }
};

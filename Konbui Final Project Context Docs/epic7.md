EPIC 7: Advanced Database Procedures and Triggers
Goal
To implement comprehensive stored procedures and triggers that enhance the e-commerce functionality, improve data integrity, provide audit trails, and automate critical business processes for the Pokemon card trading platform.
Context and Rationale
Building on the existing EPIC-6 commerce flow and database structure, EPIC 7 focuses on database-level automation and business logic. This epic implements stored procedures for complex operations and triggers for automatic data management, logging, and validation. These database features will improve performance, ensure data consistency, and provide comprehensive audit trails for business operations.

TICKET 7.1: Core Business Stored Procedures
Background: Implement essential stored procedures for critical business operations including order management, inventory control, and user operations.
Stored Procedures to Implement:
1. sp_PlaceOrder - Complete Order Processing
SQL
-- Handles complete order placement with inventory validation, stock updates, and transaction safety
DELIMITER //
CREATE PROCEDURE sp_PlaceOrder(
    IN p_user_id INT,
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
    IN p_payment_method ENUM('cod', 'paypal', 'stripe'),
    IN p_special_instructions TEXT,
    IN p_tax_rate DECIMAL(5,4),
    IN p_shipping_cost DECIMAL(10,2),
    OUT p_order_id INT,
    OUT p_order_number VARCHAR(20),
    OUT p_total_amount DECIMAL(10,2),
    OUT p_status VARCHAR(20),
    OUT p_message TEXT
)
BEGIN
    DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0;
    DECLARE v_tax_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_total_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_product_id INT;
    DECLARE v_quantity INT;
    DECLARE v_unit_price DECIMAL(10,2);
    DECLARE v_stock INT;
    DECLARE v_product_name VARCHAR(255);
    DECLARE v_product_sku VARCHAR(50);
    DECLARE v_done INT DEFAULT FALSE;
    DECLARE v_order_number VARCHAR(20);
    
    -- Cursor for cart items
    DECLARE cart_cursor CURSOR FOR 
        SELECT ci.product_id, ci.quantity, p.price, i.stock, p.name, p.sku
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        JOIN inventory i ON p.id = i.product_id
        WHERE ci.user_id = p_user_id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_status = 'ERROR';
        SET p_message = 'Order placement failed due to database error';
        GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
    END;
    
    START TRANSACTION;
    
    -- Check if cart has items
    IF (SELECT COUNT(*) FROM cart_items WHERE user_id = p_user_id) = 0 THEN
        SET p_status = 'ERROR';
        SET p_message = 'Cart is empty';
        ROLLBACK;
    ELSE
        -- Validate inventory for all items
        OPEN cart_cursor;
        read_loop: LOOP
            FETCH cart_cursor INTO v_product_id, v_quantity, v_unit_price, v_stock, v_product_name, v_product_sku;
            IF v_done THEN
                LEAVE read_loop;
            END IF;
            
            IF v_stock < v_quantity THEN
                SET p_status = 'ERROR';
                SET p_message = CONCAT('Insufficient stock for product: ', v_product_name, ' (Available: ', v_stock, ', Requested: ', v_quantity, ')');
                CLOSE cart_cursor;
                ROLLBACK;
            END IF;
            
            SET v_subtotal = v_subtotal + (v_unit_price * v_quantity);
        END LOOP;
        CLOSE cart_cursor;
        
        -- Calculate totals
        SET v_tax_amount = v_subtotal * p_tax_rate;
        SET v_total_amount = v_subtotal + v_tax_amount + p_shipping_cost;
        
        -- Generate unique order number
        SET v_order_number = CONCAT('ORD-', YEAR(NOW()), '-', UPPER(SUBSTRING(MD5(CONCAT(p_user_id, NOW(), RAND())), 1, 8)));
        
        -- Create order
        INSERT INTO orders (
            order_number, user_id, status, payment_method, payment_status,
            subtotal, tax_amount, shipping_cost, total_amount,
            shipping_first_name, shipping_last_name, shipping_email, shipping_phone,
            shipping_address_line_1, shipping_address_line_2, shipping_city,
            shipping_state, shipping_postal_code, shipping_country,
            special_instructions, created_at, updated_at
        ) VALUES (
            v_order_number, p_user_id, 'pending', p_payment_method, 'pending',
            v_subtotal, v_tax_amount, p_shipping_cost, v_total_amount,
            p_shipping_first_name, p_shipping_last_name, p_shipping_email, p_shipping_phone,
            p_shipping_address_line_1, p_shipping_address_line_2, p_shipping_city,
            p_shipping_state, p_shipping_postal_code, p_shipping_country,
            p_special_instructions, NOW(), NOW()
        );
        
        SET p_order_id = LAST_INSERT_ID();
        
        -- Reset cursor
        SET v_done = FALSE;
        
        -- Create order items and update inventory
        OPEN cart_cursor;
        insert_loop: LOOP
            FETCH cart_cursor INTO v_product_id, v_quantity, v_unit_price, v_stock, v_product_name, v_product_sku;
            IF v_done THEN
                LEAVE insert_loop;
            END IF;
            
            -- Insert order item
            INSERT INTO order_items (
                order_id, product_id, product_name, product_sku,
                unit_price, quantity, total_price, created_at, updated_at
            ) VALUES (
                p_order_id, v_product_id, v_product_name, v_product_sku,
                v_unit_price, v_quantity, (v_unit_price * v_quantity), NOW(), NOW()
            );
            
            -- Update inventory
            UPDATE inventory SET stock = stock - v_quantity WHERE product_id = v_product_id;
        END LOOP;
        CLOSE cart_cursor;
        
        -- Clear cart
        DELETE FROM cart_items WHERE user_id = p_user_id;
        
        SET p_order_number = v_order_number;
        SET p_total_amount = v_total_amount;
        SET p_status = 'SUCCESS';
        SET p_message = 'Order placed successfully';
        
        COMMIT;
    END IF;
END //
DELIMITER ;
2. sp_CancelOrder - Order Cancellation with Inventory Restoration
SQL
DELIMITER //
CREATE PROCEDURE sp_CancelOrder(
    IN p_order_id INT,
    IN p_user_id INT,
    IN p_cancel_reason TEXT,
    OUT p_status VARCHAR(20),
    OUT p_message TEXT
)
BEGIN
    DECLARE v_order_status VARCHAR(50);
    DECLARE v_order_user_id INT;
    DECLARE v_done INT DEFAULT FALSE;
    DECLARE v_product_id INT;
    DECLARE v_quantity INT;
    
    DECLARE order_items_cursor CURSOR FOR 
        SELECT product_id, quantity 
        FROM order_items 
        WHERE order_id = p_order_id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_status = 'ERROR';
        GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
    END;
    
    START TRANSACTION;
    
    -- Validate order exists and belongs to user
    SELECT status, user_id INTO v_order_status, v_order_user_id 
    FROM orders 
    WHERE id = p_order_id;
    
    IF v_order_user_id IS NULL THEN
        SET p_status = 'ERROR';
        SET p_message = 'Order not found';
        ROLLBACK;
    ELSEIF v_order_user_id!= p_user_id THEN
        SET p_status = 'ERROR';
        SET p_message = 'Unauthorized: Order does not belong to user';
        ROLLBACK;
    ELSEIF v_order_status IN ('cancelled', 'shipped', 'delivered') THEN
        SET p_status = 'ERROR';
        SET p_message = CONCAT('Cannot cancel order with status: ', v_order_status);
        ROLLBACK;
    ELSE
        -- Restore inventory for all order items
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
        
        -- Update order status
        UPDATE orders 
        SET status = 'cancelled', 
            notes = IFNULL(CONCAT(notes, '\n'), '') + CONCAT('Cancelled: ', p_cancel_reason),
            updated_at = NOW()
        WHERE id = p_order_id;
        
        SET p_status = 'SUCCESS';
        SET p_message = 'Order cancelled successfully';
        
        COMMIT;
    END IF;
END //
DELIMITER ;
3. sp_UpdateProductStock - Inventory Management with Validation
SQL
DELIMITER //
CREATE PROCEDURE sp_UpdateProductStock(
    IN p_product_id INT,
    IN p_new_stock INT,
    IN p_reason VARCHAR(255),
    IN p_updated_by INT,
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
        SET p_status = 'ERROR';
        GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
    END;
    
    START TRANSACTION;
    
    -- Validate product exists
    SELECT i.stock, p.name 
    INTO v_current_stock, v_product_name
    FROM inventory i
    JOIN products p ON i.product_id = p.id
    WHERE p.id = p_product_id;
    
    IF v_product_name IS NULL THEN
        SET p_status = 'ERROR';
        SET p_message = 'Product not found';
        ROLLBACK;
    ELSEIF p_new_stock < 0 THEN
        SET p_status = 'ERROR';
        SET p_message = 'Stock cannot be negative';
        ROLLBACK;
    ELSE
        -- Update inventory
        UPDATE inventory 
        SET stock = p_new_stock, updated_at = NOW() 
        WHERE product_id = p_product_id;
        
        -- Log the change
        INSERT INTO inventory_logs (
            product_id, old_stock, new_stock, change_amount, 
            reason, updated_by, created_at
        ) VALUES (
            p_product_id, v_current_stock, p_new_stock, 
            (p_new_stock - v_current_stock), p_reason, p_updated_by, NOW()
        );
        
        SET p_status = 'SUCCESS';
        SET p_message = CONCAT('Stock updated for ', v_product_name, 
                                ' from ', v_current_stock, ' to ', p_new_stock);
        
        -- Check for low stock warning
        IF p_new_stock <= v_min_stock THEN
            SET p_message = CONCAT(p_message, ' (WARNING: Low stock threshold reached)');
        END IF;
        
        COMMIT;
    END IF;
END //
DELIMITER ;
4. sp_GetUserOrderHistory - Comprehensive Order History
SQL
DELIMITER //
CREATE PROCEDURE sp_GetUserOrderHistory(
    IN p_user_id INT,
    IN p_limit INT DEFAULT 50,
    IN p_offset INT DEFAULT 0,
    IN p_status VARCHAR(50) DEFAULT NULL
)
BEGIN
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
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT p_limit OFFSET p_offset;
    
    -- Also return total count for pagination
    SELECT COUNT(*) as total_orders
    FROM orders 
    WHERE user_id = p_user_id
    AND (p_status IS NULL OR status = p_status);
END //
DELIMITER ;
5. sp_ProcessRefund - Handle Order Refunds
SQL
DELIMITER //
CREATE PROCEDURE sp_ProcessRefund(
    IN p_order_id INT,
    IN p_refund_amount DECIMAL(10,2),
    IN p_refund_reason TEXT,
    IN p_processed_by INT,
    OUT p_status VARCHAR(20),
    OUT p_message TEXT
)
BEGIN
    DECLARE v_order_total DECIMAL(10,2);
    DECLARE v_order_status VARCHAR(50);
    DECLARE v_done INT DEFAULT FALSE;
    DECLARE v_product_id INT;
    DECLARE v_quantity INT;
    
    DECLARE order_items_cursor CURSOR FOR 
        SELECT product_id, quantity 
        FROM order_items 
        WHERE order_id = p_order_id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_status = 'ERROR';
        GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
    END;
    
    START TRANSACTION;
    
    -- Validate order
    SELECT total_amount, status 
    INTO v_order_total, v_order_status
    FROM orders 
    WHERE id = p_order_id;
    
    IF v_order_total IS NULL THEN
        SET p_status = 'ERROR';
        SET p_message = 'Order not found';
        ROLLBACK;
    ELSEIF v_order_status = 'refunded' THEN
        SET p_status = 'ERROR';
        SET p_message = 'Order already refunded';
        ROLLBACK;
    ELSEIF p_refund_amount > v_order_total THEN
        SET p_status = 'ERROR';
        SET p_message = 'Refund amount cannot exceed order total';
        ROLLBACK;
    ELSE
        -- Restore inventory if full refund
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
        
        -- Update order status
        UPDATE orders 
        SET status = IF(p_refund_amount = v_order_total, 'refunded', 'partially_refunded'),
            payment_status = 'refunded',
            notes = IFNULL(CONCAT(notes, '\n'), '') + 
                   CONCAT('Refund processed: $', p_refund_amount, ' - ', p_refund_reason),
            updated_at = NOW()
        WHERE id = p_order_id;
        
        -- Record refund
        INSERT INTO refunds (
            order_id, amount, reason, processed_by, created_at
        ) VALUES (
            p_order_id, p_refund_amount, p_refund_reason, p_processed_by, NOW()
        );
        
        SET p_status = 'SUCCESS';
        SET p_message = CONCAT('Refund of $', p_refund_amount, ' processed successfully');
        
        COMMIT;
    END IF;
END //
DELIMITER ;
6. sp_GetLowStockProducts - Inventory Monitoring
SQL
DELIMITER //
CREATE PROCEDURE sp_GetLowStockProducts(
    IN p_threshold INT DEFAULT 5
)
BEGIN
    SELECT 
        p.id,
        p.name,
        p.sku,
        c.name as card_name,
        s.name as set_name,
        r.name as rarity_name,
        i.stock,
        p.price,
        p.is_active
    FROM products p
    JOIN inventory i ON p.id = i.product_id
    JOIN cards c ON p.card_id = c.id
    JOIN sets s ON c.set_id = s.id
    JOIN rarities r ON c.rarity_id = r.id
    WHERE i.stock <= p_threshold 
    AND p.is_active = 1
    ORDER BY i.stock ASC, p.name ASC;
END //
DELIMITER ;
7. sp_ArchiveOldOrders - Data Management
SQL
DELIMITER //
CREATE PROCEDURE sp_ArchiveOldOrders(
    IN p_days_old INT DEFAULT 365,
    OUT p_archived_count INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_archived_count = -1;
    END;
    
    START TRANSACTION;
    
    -- Archive orders older than specified days with completed status
    INSERT INTO archived_orders 
    SELECT *, NOW() as archived_at 
    FROM orders 
    WHERE status IN ('delivered', 'cancelled', 'refunded')
    AND created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
    
    SET p_archived_count = ROW_COUNT();
    
    -- Archive corresponding order items
    INSERT INTO archived_order_items
    SELECT oi.*, NOW() as archived_at
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status IN ('delivered', 'cancelled', 'refunded')
    AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
    
    -- Delete from main tables
    DELETE oi FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status IN ('delivered', 'cancelled', 'refunded')
    AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
    
    DELETE FROM orders 
    WHERE status IN ('delivered', 'cancelled', 'refunded')
    AND created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
    
    COMMIT;
END //
DELIMITER ;

TICKET 7.2: Automated Triggers for Data Integrity and Logging
Background: Implement triggers that automatically handle data validation, logging, and business rule enforcement.
Triggers to Implement:
1. tr_orders_inventory_update - Automatic Inventory Management
SQL
DELIMITER //
CREATE TRIGGER tr_orders_inventory_update
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    -- If order status changes to 'shipped', log the shipment
    IF OLD.status!= NEW.status AND NEW.status = 'shipped' THEN
        INSERT INTO order_status_log (
            order_id, old_status, new_status, changed_by, 
            change_reason, created_at
        ) VALUES (
            NEW.id, OLD.status, NEW.status, NEW.updated_by,
            'Order shipped', NOW()
        );
    END IF;
    
    -- If order is cancelled, restore inventory (backup to stored procedure)
    IF OLD.status!= NEW.status AND NEW.status = 'cancelled' THEN
        -- This is handled by sp_CancelOrder, but serves as backup
        INSERT INTO inventory_adjustments (
            order_id, adjustment_type, reason, created_at
        ) VALUES (
            NEW.id, 'restore', 'Order cancelled', NOW()
        );
    END IF;
END //
DELIMITER ;
2. tr_products_price_history - Price Change Tracking
SQL
DELIMITER //
CREATE TRIGGER tr_products_price_history
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    -- Log price changes
    IF OLD.price!= NEW.price THEN
        INSERT INTO price_history (
            product_id, old_price, new_price, change_amount,
            change_percentage, changed_by, created_at
        ) VALUES (
            NEW.id, OLD.price, NEW.price, 
            (NEW.price - OLD.price),
            ROUND(((NEW.price - OLD.price) / OLD.price) * 100, 2),
            NEW.updated_by, NOW()
        );
    END IF;
    
    -- Log status changes
    IF OLD.is_active!= NEW.is_active THEN
        INSERT INTO product_status_log (
            product_id, old_status, new_status, changed_by, created_at
        ) VALUES (
            NEW.id, 
            IF(OLD.is_active = 1, 'active', 'inactive'),
            IF(NEW.is_active = 1, 'active', 'inactive'),
            NEW.updated_by, NOW()
        );
    END IF;
END //
DELIMITER ;
3. tr_cart_items_validation - Cart Item Validation
SQL
DELIMITER //
CREATE TRIGGER tr_cart_items_validation
BEFORE INSERT ON cart_items
FOR EACH ROW
BEGIN
    DECLARE v_stock INT;
    DECLARE v_is_active BOOLEAN;
    DECLARE v_price DECIMAL(10,2);
    
    -- Validate product exists and is active
    SELECT i.stock, p.is_active, p.price
    INTO v_stock, v_is_active, v_price
    FROM inventory i
    JOIN products p ON i.product_id = p.id
    WHERE p.id = NEW.product_id;
    
    IF v_stock IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Product not found';
    END IF;
    
    IF v_is_active = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Product is not active';
    END IF;
    
    IF NEW.quantity <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quantity must be greater than 0';
    END IF;
    
    IF NEW.quantity > v_stock THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = CONCAT('Insufficient stock. Available: ', v_stock, ', Requested: ', NEW.quantity);
    END IF;
    
    -- Set timestamps
    SET NEW.created_at = NOW();
    SET NEW.updated_at = NOW();
END //
DELIMITER ;
4. tr_inventory_low_stock_alert - Stock Threshold Monitoring
SQL
DELIMITER //
CREATE TRIGGER tr_inventory_low_stock_alert
AFTER UPDATE ON inventory
FOR EACH ROW
BEGIN
    DECLARE v_product_name VARCHAR(255);
    DECLARE v_threshold INT DEFAULT 5;
    
    -- Check if stock dropped below threshold
    IF OLD.stock > v_threshold AND NEW.stock <= v_threshold THEN
        SELECT name INTO v_product_name 
        FROM products 
        WHERE id = NEW.product_id;
        
        INSERT INTO stock_alerts (
            product_id, product_name, current_stock, threshold_value,
            alert_type, message, created_at
        ) VALUES (
            NEW.product_id, v_product_name, NEW.stock, v_threshold,
            'low_stock', 
            CONCAT('Product "', v_product_name, '" is below stock threshold. Current stock: ', NEW.stock),
            NOW()
        );
    END IF;
    
    -- Check if stock reaches zero
    IF OLD.stock > 0 AND NEW.stock = 0 THEN
        SELECT name INTO v_product_name 
        FROM products 
        WHERE id = NEW.product_id;
        
        INSERT INTO stock_alerts (
            product_id, product_name, current_stock, threshold_value,
            alert_type, message, created_at
        ) VALUES (
            NEW.product_id, v_product_name, NEW.stock, 0,
            'out_of_stock', 
            CONCAT('Product "', v_product_name, '" is now out of stock'),
            NOW()
        );
    END IF;
END //
DELIMITER ;
5. tr_user_activity_log - User Action Tracking
SQL
DELIMITER //
CREATE TRIGGER tr_user_activity_log_insert
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    INSERT INTO user_activity_log (
        user_id, activity_type, description, 
        related_table, related_id, ip_address, created_at
    ) VALUES (
        NEW.user_id, 'order_placed', 
        CONCAT('Order placed: ', NEW.order_number, ' - Total: $', NEW.total_amount),
        'orders', NEW.id, NEW.user_ip, NOW()
    );
END //
DELIMITER ;

CREATE TRIGGER tr_user_activity_log_login
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF OLD.last_login_at!= NEW.last_login_at THEN
        INSERT INTO user_activity_log (
            user_id, activity_type, description,
            ip_address, created_at
        ) VALUES (
            NEW.id, 'login',
            CONCAT('User logged in: ', NEW.email),
            NEW.last_login_ip, NOW()
        );
    END IF;
END //
DELIMITER ;
6. tr_order_total_calculation - Automatic Order Total Updates
SQL
DELIMITER //
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
    SELECT SUM(total_price), shipping_cost
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
END //
DELIMITER ;
7. tr_product_search_index_update - Search Index Maintenance
SQL
DELIMITER //
CREATE TRIGGER tr_product_search_index_update
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    -- Update search index when product details change
    IF OLD.name!= NEW.name OR OLD.description!= NEW.description OR OLD.is_active!= NEW.is_active THEN
        INSERT INTO search_index_updates (
            table_name, record_id, action, created_at
        ) VALUES (
            'products', NEW.id, 'update', NOW()
        );
    END IF;
END //
DELIMITER ;

CREATE TRIGGER tr_product_search_index_insert
AFTER INSERT ON products
FOR EACH ROW
BEGIN
    INSERT INTO search_index_updates (
        table_name, record_id, action, created_at
    ) VALUES (
        'products', NEW.id, 'insert', NOW()
    );
END //
DELIMITER ;

TICKET 7.3: Supporting Tables and Views
Background: Create necessary supporting tables for logging and create useful views for reporting.
Required Tables:
SQL
-- Inventory change logging
CREATE TABLE inventory_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    old_stock INT NOT NULL,
    new_stock INT NOT NULL,
    change_amount INT NOT NULL,
    reason VARCHAR(255),
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Price change history
CREATE TABLE price_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    old_price DECIMAL(10,2) NOT NULL,
    new_price DECIMAL(10,2) NOT NULL,
    change_amount DECIMAL(10,2) NOT NULL,
    change_percentage DECIMAL(5,2),
    changed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (changed_by) REFERENCES users(id)
);

-- Order status logging
CREATE TABLE order_status_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    changed_by INT,
    change_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (changed_by) REFERENCES users(id)
);

-- Stock alerts
CREATE TABLE stock_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    product_name VARCHAR(255),
    current_stock INT NOT NULL,
    threshold_value INT NOT NULL,
    alert_type ENUM('low_stock', 'out_of_stock') NOT NULL,
    message TEXT,
    is_resolved BOOLEAN DEFAULT FALSE,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- User activity logging
CREATE TABLE user_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT,
    related_table VARCHAR(50),
    related_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_activity (user_id, created_at),
    INDEX idx_activity_type (activity_type, created_at)
);

-- Refunds tracking
CREATE TABLE refunds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reason TEXT,
    processed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (processed_by) REFERENCES users(id)
);

-- Archive tables
CREATE TABLE archived_orders LIKE orders;
ALTER TABLE archived_orders ADD COLUMN archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

CREATE TABLE archived_order_items LIKE order_items;
ALTER TABLE archived_order_items ADD COLUMN archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

TICKET 7.4: Testing and Validation
Background: Create comprehensive tests to validate all stored procedures and triggers are working correctly.
Acceptance Criteria:
All 7+ stored procedures execute without errors
All 7+ triggers fire correctly on their respective events
Data integrity is maintained across all operations
Error handling works as expected
Performance benchmarks are met
Documentation is complete
Test Cases Required:
Stored Procedure Tests:
Order placement with sufficient inventory
Order placement with insufficient inventory
Order cancellation and inventory restoration
Stock updates with validation
Refund processing
Low stock product identification
Order archiving
Trigger Tests:
Inventory updates on order status changes
Price change logging
Cart validation on item insertion
Stock alert generation
User activity logging
Order total recalculation

Summary
This expanded EPIC 7 provides:
7 Comprehensive Stored Procedures for core business operations
10+ Triggers for automated data management
Complete audit trail for all critical operations
Data integrity validation at database level
Performance optimization through database-level logic
Business rule enforcement through constraints and triggers
Comprehensive logging for debugging and compliance
The implementation focuses on the Pokemon card e-commerce platform's specific needs while maintaining scalability and data integrity.

The remaining epics (Order Management, Buyback System, MCP Integration) will be detailed with the same structure and level of exhaustive detail, ensuring full coverage of all product and technical requirements.


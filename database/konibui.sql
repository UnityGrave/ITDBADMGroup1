-- =============================================================================
-- KONIBUI DATABASE SCHEMA
-- =============================================================================

CREATE DATABASE IF NOT EXISTS konibui;
USE konibui;

-- =============================================================================
-- 1. CORE TABLES (Users, Authentication, Cache, Jobs)
-- =============================================================================

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) NULL,
    `preferred_currency` VARCHAR(3) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`preferred_currency`) REFERENCES `currencies`(`code`) ON DELETE SET NULL,
    INDEX `idx_preferred_currency` (`preferred_currency`)
);

-- Password reset tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email` VARCHAR(255) PRIMARY KEY,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL
);

-- Sessions table
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` VARCHAR(255) PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    INDEX `sessions_user_id_index` (`user_id`),
    INDEX `sessions_last_activity_index` (`last_activity`)
);

-- Cache tables
CREATE TABLE IF NOT EXISTS `cache` (
    `key` VARCHAR(255) PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL
);

CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key` VARCHAR(255) PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL
);

-- Job tables
CREATE TABLE IF NOT EXISTS `jobs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    INDEX `jobs_queue_index` (`queue`)
);

CREATE TABLE IF NOT EXISTS `job_batches` (
    `id` VARCHAR(255) PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `total_jobs` INT NOT NULL,
    `pending_jobs` INT NOT NULL,
    `failed_jobs` INT NOT NULL,
    `failed_job_ids` LONGTEXT NOT NULL,
    `options` MEDIUMTEXT NULL,
    `cancelled_at` INT NULL,
    `created_at` INT NOT NULL,
    `finished_at` INT NULL
);

CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` VARCHAR(255) NOT NULL UNIQUE,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Migrations table
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT NOT NULL
);

-- =============================================================================
-- 2. ROLE-BASED ACCESS CONTROL
-- =============================================================================

-- Roles table
CREATE TABLE IF NOT EXISTS `roles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
);

-- Role-User pivot table
CREATE TABLE IF NOT EXISTS `role_user` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `role_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    UNIQUE KEY `role_user_user_id_role_id_unique` (`user_id`, `role_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
);

-- =============================================================================
-- 3. TCG CORE TABLES (Cards, Sets, Rarities, Categories)
-- =============================================================================

-- Sets table
CREATE TABLE IF NOT EXISTS `sets` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
);

-- Rarities table
CREATE TABLE IF NOT EXISTS `rarities` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
);

-- Categories table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
);

-- Cards table
CREATE TABLE IF NOT EXISTS `cards` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `collector_number` VARCHAR(255) NOT NULL,
    `set_id` BIGINT UNSIGNED NOT NULL,
    `rarity_id` BIGINT UNSIGNED NOT NULL,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`set_id`) REFERENCES `sets`(`id`),
    FOREIGN KEY (`rarity_id`) REFERENCES `rarities`(`id`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
);

-- =============================================================================
-- 4. PRODUCTS AND INVENTORY TABLES
-- =============================================================================

-- Products table
CREATE TABLE IF NOT EXISTS `products` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `card_id` BIGINT UNSIGNED NOT NULL,
    `condition` VARCHAR(255) NOT NULL, -- ENUM: NM, LP, MP, HP, DMG (enforced in app/model)
    `base_currency_code` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `base_price_cents` BIGINT NOT NULL,
    `sku` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`card_id`) REFERENCES `cards`(`id`),
    FOREIGN KEY (`base_currency_code`) REFERENCES `currencies`(`code`),
    INDEX `idx_base_currency_code` (`base_currency_code`)
);

-- Inventory table
CREATE TABLE IF NOT EXISTS `inventory` (
    `product_id` BIGINT UNSIGNED PRIMARY KEY,
    `stock` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- =============================================================================
-- 5. SHOPPING CART TABLES
-- =============================================================================

-- Cart items table
CREATE TABLE IF NOT EXISTS `cart_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    UNIQUE KEY `cart_items_user_id_product_id_unique` (`user_id`, `product_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- =============================================================================
-- 6. ORDER MANAGEMENT TABLES
-- =============================================================================

-- Orders table
CREATE TABLE IF NOT EXISTS `orders` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(255) NOT NULL UNIQUE,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending',
    `payment_method` ENUM('cod', 'credit_card', 'paypal') NOT NULL DEFAULT 'cod',
    `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    `subtotal` DECIMAL(10, 2) NOT NULL,
    `tax_amount` DECIMAL(10, 2) NOT NULL,
    `shipping_cost` DECIMAL(10, 2) NOT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `currency_code` VARCHAR(3) NOT NULL,
    `exchange_rate` DECIMAL(18,8) NOT NULL,
    `total_in_base_currency` BIGINT NOT NULL,
    `shipping_first_name` VARCHAR(255) NOT NULL,
    `shipping_last_name` VARCHAR(255) NOT NULL,
    `shipping_email` VARCHAR(255) NOT NULL,
    `shipping_phone` VARCHAR(255) NOT NULL,
    `shipping_address_line_1` VARCHAR(255) NOT NULL,
    `shipping_address_line_2` VARCHAR(255) NULL,
    `shipping_city` VARCHAR(255) NOT NULL,
    `shipping_state` VARCHAR(255) NOT NULL,
    `shipping_postal_code` VARCHAR(255) NOT NULL,
    `shipping_country` VARCHAR(2) NOT NULL DEFAULT 'US',
    `special_instructions` TEXT NULL,
    `notes` TEXT NULL,
    `shipped_at` TIMESTAMP NULL,
    `delivered_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    INDEX `orders_user_id_status_index` (`user_id`, `status`),
    INDEX `orders_order_number_index` (`order_number`),
    INDEX `orders_created_at_index` (`created_at`),
    INDEX `idx_currency_code` (`currency_code`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`currency_code`) REFERENCES `currencies`(`code`) ON DELETE RESTRICT
);

-- Order items table
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `product_sku` VARCHAR(255) NOT NULL,
    `unit_price` DECIMAL(10, 2) NOT NULL,
    `price_in_base_currency` BIGINT NOT NULL,
    `quantity` INT NOT NULL,
    `total_price` DECIMAL(10, 2) NOT NULL,
    `product_details` JSON NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    INDEX `order_items_order_id_index` (`order_id`),
    INDEX `order_items_product_id_index` (`product_id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- =============================================================================
-- 7. SUPPORT TABLES (Logging, Auditing, Analytics)
-- =============================================================================

-- Order status log
CREATE TABLE IF NOT EXISTS `order_status_log` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `old_status` VARCHAR(50) NULL,
    `new_status` VARCHAR(50) NOT NULL,
    `changed_by` BIGINT UNSIGNED NULL,
    `change_reason` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`),
    FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`)
);

-- Price history
CREATE TABLE IF NOT EXISTS `price_history` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `old_price` DECIMAL(10,2) NOT NULL,
    `new_price` DECIMAL(10,2) NOT NULL,
    `change_amount` DECIMAL(10,2) NOT NULL,
    `change_percentage` DECIMAL(5,2) NULL,
    `changed_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`)
);

-- Stock alerts
CREATE TABLE IF NOT EXISTS `stock_alerts` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `product_name` VARCHAR(255) NULL,
    `current_stock` INT NOT NULL,
    `threshold_value` INT NOT NULL,
    `alert_type` ENUM('low_stock', 'out_of_stock') NOT NULL,
    `message` TEXT NULL,
    `is_resolved` BOOLEAN DEFAULT FALSE,
    `resolved_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
);

-- User activity log
CREATE TABLE IF NOT EXISTS `user_activity_log` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `activity_type` VARCHAR(50) NOT NULL,
    `description` TEXT NULL,
    `related_table` VARCHAR(50) NULL,
    `related_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_activity` (`user_id`, `created_at`),
    INDEX `idx_activity_type` (`activity_type`, `created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

-- Search index updates
CREATE TABLE IF NOT EXISTS `search_index_updates` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `table_name` VARCHAR(50) NOT NULL,
    `record_id` BIGINT UNSIGNED NOT NULL,
    `action` ENUM('insert', 'update', 'delete') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_pending_updates` (`table_name`, `record_id`)
);

-- Inventory logs
CREATE TABLE IF NOT EXISTS `inventory_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `old_stock` INT NOT NULL,
    `new_stock` INT NOT NULL,
    `change_amount` INT NOT NULL,
    `reason` VARCHAR(255) NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_product_updates` (`product_id`, `created_at`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`)
);

-- Refunds table
CREATE TABLE IF NOT EXISTS `refunds` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `reason` TEXT NULL,
    `processed_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_order_refunds` (`order_id`, `created_at`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`),
    FOREIGN KEY (`processed_by`) REFERENCES `users`(`id`)
);

-- Archived orders (for performance)
CREATE TABLE IF NOT EXISTS `archived_orders` LIKE `orders`;
ALTER TABLE `archived_orders` 
    ADD COLUMN `archived_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD INDEX `idx_archived_date` (`archived_at`);

-- Archived order items
CREATE TABLE IF NOT EXISTS `archived_order_items` LIKE `order_items`;
ALTER TABLE `archived_order_items` 
    ADD COLUMN `archived_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD INDEX `idx_archived_date` (`archived_at`);

-- Inventory adjustments
CREATE TABLE IF NOT EXISTS `inventory_adjustments` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `adjustment_type` ENUM('reduce', 'restore') NOT NULL,
    `reason` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_order_adjustments` (`order_id`, `created_at`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`)
);

-- Archived inventory adjustments
CREATE TABLE IF NOT EXISTS `archived_inventory_adjustments` LIKE `inventory_adjustments`;
ALTER TABLE `archived_inventory_adjustments` 
    ADD COLUMN `archived_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD INDEX `idx_archived_date` (`archived_at`);

-- =============================================================================
-- 8. STORED PROCEDURES
-- =============================================================================

DELIMITER //

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
    
    DECLARE cart_cursor CURSOR FOR 
        SELECT ci.product_id, ci.quantity, p.price, i.stock, c.name, p.sku
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        JOIN inventory i ON p.id = i.product_id
        JOIN cards c ON p.card_id = c.id
        WHERE ci.user_id = p_user_id;
        
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        IF v_has_error = FALSE THEN
            SET p_status = 'ERROR';
            SET p_message = 'Order placement failed due to database error';
            GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
        END IF;
    END;
    
    IF p_shipping_first_name IS NULL OR p_shipping_last_name IS NULL OR 
       p_shipping_email IS NULL OR p_shipping_phone IS NULL OR
       p_shipping_address_line_1 IS NULL OR p_shipping_city IS NULL OR 
       p_shipping_state IS NULL OR p_shipping_postal_code IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Missing required shipping information';
    END IF;
    
    SET p_status = 'ERROR';
    SET p_message = '';
    
    START TRANSACTION;
    
    IF (SELECT COUNT(*) FROM cart_items WHERE user_id = p_user_id) = 0 THEN
        SET p_status = 'ERROR';
        SET p_message = 'Cart is empty';
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
                SET p_status = 'ERROR';
                SET p_message = CONCAT('Insufficient stock for product: ', v_product_name);
                CLOSE cart_cursor;
                ROLLBACK;
                LEAVE read_loop;
            END IF;
            
            SET v_subtotal = v_subtotal + (v_unit_price * v_quantity);
        END LOOP;
        CLOSE cart_cursor;
        
        IF v_has_error = FALSE THEN
            SET v_tax_amount = v_subtotal * p_tax_rate;
            SET v_total_amount = v_subtotal + v_tax_amount + p_shipping_cost;
            SET v_order_number = CONCAT('ORD-', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 10000), 4, '0'));
            
            INSERT INTO orders (
                order_number, user_id, status, payment_method, payment_status,
                subtotal, tax_amount, shipping_cost, total_amount,
                shipping_first_name, shipping_last_name, shipping_email, 
                shipping_phone, shipping_address_line_1, shipping_address_line_2,
                shipping_city, shipping_state, shipping_postal_code, shipping_country,
                special_instructions, created_at, updated_at
            ) VALUES (
                v_order_number, p_user_id, 'pending', p_payment_method, 'pending',
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
            SET p_status = 'SUCCESS';
            SET p_message = 'Order placed successfully';
            
            COMMIT;
        END IF;
    END IF;
END//

DELIMITER ;

-- sp_CancelOrder - Order cancellation with stock restoration
DELIMITER //

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
        SET p_status = 'ERROR';
        GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
    END;
    
    START TRANSACTION;
    
    SELECT status, user_id INTO v_order_status, v_order_user_id 
    FROM orders 
    WHERE id = p_order_id;
    
    IF v_order_user_id IS NULL THEN
        SET p_status = 'ERROR';
        SET p_message = 'Order not found';
        ROLLBACK;
    ELSEIF v_order_user_id != p_user_id THEN
        SET p_status = 'ERROR';
        SET p_message = 'Unauthorized: Order does not belong to user';
        ROLLBACK;
    ELSEIF v_order_status IN ('cancelled', 'shipped', 'delivered') THEN
        SET p_status = 'ERROR';
        SET p_message = CONCAT('Cannot cancel order with status: ', v_order_status);
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
        SET status = 'cancelled', 
            notes = CONCAT(IFNULL(notes, ''), '\nCancelled: ', p_cancel_reason),
            updated_at = NOW()
        WHERE id = p_order_id;
        
        SET p_status = 'SUCCESS';
        SET p_message = 'Order cancelled successfully';
        
        COMMIT;
    END IF;
END//

DELIMITER ;

-- sp_UpdateProductStock - Stock management with logging
DELIMITER //

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
        SET p_status = 'ERROR';
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
        SET p_status = 'ERROR';
        SET p_message = 'Product not found';
        ROLLBACK;
    ELSEIF p_new_stock < 0 THEN
        SET p_status = 'ERROR';
        SET p_message = 'Stock cannot be negative';
        ROLLBACK;
    ELSE
        UPDATE inventory 
        SET stock = p_new_stock, updated_at = NOW() 
        WHERE product_id = p_product_id;
        
        INSERT INTO inventory_logs (
            product_id, old_stock, new_stock, change_amount, 
            reason, updated_by, created_at
        ) VALUES (
            p_product_id, v_current_stock, p_new_stock, 
            (p_new_stock - v_current_stock), p_reason, p_updated_by, NOW()
        );
        
        SET p_status = 'SUCCESS';
        SET p_message = CONCAT('Stock updated for ', v_product_name);
        
        IF p_new_stock <= v_min_stock THEN
            SET p_message = CONCAT(p_message, ' (WARNING: Low stock threshold reached)');
        END IF;
        
        COMMIT;
    END IF;
END//

DELIMITER ;

-- sp_GetUserOrderHistory - User order retrieval with pagination
DELIMITER //

CREATE PROCEDURE sp_GetUserOrderHistory(
    IN p_user_id BIGINT UNSIGNED,
    IN p_limit INT,
    IN p_offset INT,
    IN p_status VARCHAR(50)
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
END//

DELIMITER ;

-- sp_ProcessRefund - Refund processing with inventory restoration
DELIMITER //

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
        SET p_status = 'ERROR';
        GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
    END;
    
    START TRANSACTION;
    
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
        SET status = IF(p_refund_amount = v_order_total, 'refunded', 'processing'),
            payment_status = 'refunded',
            notes = CONCAT(IFNULL(notes, ''), '\nRefund processed: $', p_refund_amount, ' - ', p_refund_reason),
            updated_at = NOW()
        WHERE id = p_order_id;
        
        INSERT INTO refunds (
            order_id, amount, reason, processed_by, created_at
        ) VALUES (
            p_order_id, p_refund_amount, p_refund_reason, p_processed_by, NOW()
        );
        
        SET p_status = 'SUCCESS';
        SET p_message = CONCAT('Refund of $', p_refund_amount, ' processed successfully');
        
        COMMIT;
    END IF;
END//

DELIMITER ;

-- sp_GetLowStockProducts - Low stock monitoring
DELIMITER //

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
END//

DELIMITER ;

-- sp_ArchiveOldOrders - Archive old orders for performance
DELIMITER //

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
    WHERE status IN ('delivered', 'cancelled', 'refunded')
    AND created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
    
    SET p_archived_count = ROW_COUNT();
    
    -- Archive order items
    INSERT INTO archived_order_items
    SELECT oi.*, NOW() as archived_at
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status IN ('delivered', 'cancelled', 'refunded')
    AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
    
    -- Archive inventory adjustments (if any exist for these orders)
    INSERT INTO archived_inventory_adjustments
    SELECT ia.*, NOW() as archived_at
    FROM inventory_adjustments ia
    JOIN orders o ON ia.order_id = o.id
    WHERE o.status IN ('delivered', 'cancelled', 'refunded')
    AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
    
    -- Now delete in proper order (children first, then parents)
    
    -- Delete inventory adjustments first (they reference orders)
    DELETE ia FROM inventory_adjustments ia
    JOIN orders o ON ia.order_id = o.id
    WHERE o.status IN ('delivered', 'cancelled', 'refunded')
    AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
    
    -- Delete order items
    DELETE oi FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status IN ('delivered', 'cancelled', 'refunded')
    AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
    
    -- Finally delete orders (no more FK references)
    DELETE FROM orders 
    WHERE status IN ('delivered', 'cancelled', 'refunded')
    AND created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
    
    COMMIT;
END//

DELIMITER ;

-- =============================================================================
-- 9. DATABASE TRIGGERS
-- =============================================================================

DELIMITER //

-- tr_orders_inventory_update - Automatic Inventory Management
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
END//

-- tr_products_price_history - Price Change Tracking
DELIMITER //

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
END//

DELIMITER ;

-- tr_cart_items_validation - Cart Item Validation
DELIMITER //

CREATE TRIGGER tr_cart_items_validation
BEFORE INSERT ON cart_items
FOR EACH ROW
BEGIN
    DECLARE v_stock INT;
    DECLARE v_product_exists INT DEFAULT 0;
    DECLARE v_error_msg TEXT;
    
    -- Validate product exists and get stock
    SELECT COUNT(*) INTO v_product_exists
    FROM products p
    JOIN inventory i ON p.id = i.product_id
    WHERE p.id = NEW.product_id;
    
    IF v_product_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Product not found';
    END IF;
    
    -- Get stock separately
    SELECT i.stock INTO v_stock
    FROM inventory i
    WHERE i.product_id = NEW.product_id;
    
    IF NEW.quantity <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quantity must be greater than 0';
    END IF;
    
    IF NEW.quantity > v_stock THEN
        SET v_error_msg = CONCAT('Insufficient stock. Available: ', v_stock, ', Requested: ', NEW.quantity);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_msg;
    END IF;
    
    -- Set timestamps
    SET NEW.created_at = NOW();
    SET NEW.updated_at = NOW();
END//

DELIMITER ;

-- tr_inventory_low_stock_alert - Stock Threshold Monitoring
DELIMITER //

CREATE TRIGGER tr_inventory_low_stock_alert
AFTER UPDATE ON inventory
FOR EACH ROW
BEGIN
    DECLARE v_product_name VARCHAR(255);
    DECLARE v_threshold INT DEFAULT 5;
    
    -- Get product name from cards table
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
            'low_stock', 
            CONCAT('Product "', v_product_name, '" is below stock threshold. Current stock: ', CAST(NEW.stock AS CHAR)),
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
            'out_of_stock', 
            CONCAT('Product "', v_product_name, '" is now out of stock'),
            NOW()
        );
    END IF;
END//

DELIMITER ;

-- tr_user_activity_log - User Action Tracking
DELIMITER //

CREATE TRIGGER tr_user_activity_log_insert
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    INSERT INTO user_activity_log (
        user_id, activity_type, description, 
        related_table, related_id, created_at
    ) VALUES (
        NEW.user_id, 'order_placed', 
        CONCAT('Order placed: ', NEW.order_number, ' - Total: $', CAST(NEW.total_amount AS CHAR)),
        'orders', NEW.id, NOW()
    );
END//

DELIMITER ;

-- tr_order_total_calculation - Automatic Order Total Updates
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
END//

DELIMITER ;

-- tr_product_search_index - Search Index Maintenance
DELIMITER //
CREATE TRIGGER tr_product_search_index_update
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    -- Update search index when product details change
    IF OLD.price != NEW.price OR OLD.`condition` != NEW.`condition` THEN
        INSERT INTO search_index_updates (
            table_name, record_id, action, created_at
        ) VALUES (
            'products', NEW.id, 'update', NOW()
        );
    END IF;
END//
DELIMITER ;

DELIMITER //
CREATE TRIGGER tr_product_search_index_insert
AFTER INSERT ON products
FOR EACH ROW
BEGIN
    INSERT INTO search_index_updates (
        table_name, record_id, action, created_at
    ) VALUES (
        'products', NEW.id, 'insert', NOW()
    );
END//
DELIMITER ;

-- tr_cart_items_update_validation - Cart Update Validation
DELIMITER //

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
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Product not found';
    END IF;
    
    -- Get stock separately
    SELECT i.stock INTO v_stock
    FROM inventory i
    WHERE i.product_id = NEW.product_id;
    
    IF NEW.quantity <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quantity must be greater than 0';
    END IF;
    
    IF NEW.quantity > v_stock THEN
        SET v_error_msg = CONCAT('Insufficient stock. Available: ', v_stock, ', Requested: ', NEW.quantity);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_msg;
    END IF;
    
    -- Update timestamp
    SET NEW.updated_at = NOW();
END//

DELIMITER ;

-- =============================================================================
-- 10. MYSQL ROLES
-- =============================================================================

-- Create MySQL roles for database-layer security
CREATE ROLE IF NOT EXISTS 'konibui_admin';
CREATE ROLE IF NOT EXISTS 'konibui_employee'; 
CREATE ROLE IF NOT EXISTS 'konibui_customer';

-- Admin role privileges (full access)
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.users TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.roles TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.role_user TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.sessions TO 'konibui_admin';
GRANT SELECT, INSERT, DELETE ON konibui.password_reset_tokens TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache_locks TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.jobs TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.job_batches TO 'konibui_admin';
GRANT SELECT ON konibui.failed_jobs TO 'konibui_admin';
GRANT SELECT ON konibui.migrations TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.products TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.categories TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.orders TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.order_items TO 'konibui_admin';

-- Employee role privileges (limited staff operations)
GRANT SELECT, INSERT, UPDATE ON konibui.users TO 'konibui_employee';
GRANT SELECT ON konibui.roles TO 'konibui_employee';
GRANT SELECT ON konibui.role_user TO 'konibui_employee';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.sessions TO 'konibui_employee';
GRANT SELECT, INSERT, DELETE ON konibui.password_reset_tokens TO 'konibui_employee';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache TO 'konibui_employee';
GRANT SELECT ON konibui.failed_jobs TO 'konibui_employee';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.jobs TO 'konibui_employee';
GRANT SELECT ON konibui.migrations TO 'konibui_employee';
GRANT SELECT, INSERT, UPDATE ON konibui.products TO 'konibui_employee';
GRANT SELECT ON konibui.categories TO 'konibui_employee';
GRANT SELECT, UPDATE ON konibui.orders TO 'konibui_employee';
GRANT SELECT, INSERT, UPDATE ON konibui.order_items TO 'konibui_employee';

-- Customer role privileges (customer-facing operations only)
GRANT SELECT, UPDATE ON konibui.users TO 'konibui_customer';
GRANT SELECT ON konibui.roles TO 'konibui_customer';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.sessions TO 'konibui_customer';
GRANT SELECT, INSERT, DELETE ON konibui.password_reset_tokens TO 'konibui_customer';
GRANT SELECT ON konibui.products TO 'konibui_customer';
GRANT SELECT ON konibui.categories TO 'konibui_customer';
GRANT SELECT, INSERT ON konibui.orders TO 'konibui_customer';
GRANT SELECT, INSERT ON konibui.order_items TO 'konibui_customer';

FLUSH PRIVILEGES;

-- =============================================================================
-- END OF SCHEMA
-- =============================================================================
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSupportTables extends Migration
{
    public function up()
    {
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS order_status_log (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id BIGINT UNSIGNED NOT NULL,
                old_status VARCHAR(50),
                new_status VARCHAR(50) NOT NULL,
                changed_by BIGINT UNSIGNED,
                change_reason TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id),
                FOREIGN KEY (changed_by) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS price_history (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id BIGINT UNSIGNED NOT NULL,
                old_price DECIMAL(10,2) NOT NULL,
                new_price DECIMAL(10,2) NOT NULL,
                change_amount DECIMAL(10,2) NOT NULL,
                change_percentage DECIMAL(5,2),
                changed_by BIGINT UNSIGNED,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id),
                FOREIGN KEY (changed_by) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS stock_alerts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id BIGINT UNSIGNED NOT NULL,
                product_name VARCHAR(255),
                current_stock INT NOT NULL,
                threshold_value INT NOT NULL,
                alert_type ENUM("low_stock", "out_of_stock") NOT NULL,
                message TEXT,
                is_resolved BOOLEAN DEFAULT FALSE,
                resolved_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id)
            );

            CREATE TABLE IF NOT EXISTS user_activity_log (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                activity_type VARCHAR(50) NOT NULL,
                description TEXT,
                related_table VARCHAR(50),
                related_id BIGINT UNSIGNED,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                INDEX idx_user_activity (user_id, created_at),
                INDEX idx_activity_type (activity_type, created_at)
            );

            CREATE TABLE IF NOT EXISTS search_index_updates (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                table_name VARCHAR(50) NOT NULL,
                record_id BIGINT UNSIGNED NOT NULL,
                action ENUM("insert", "update", "delete") NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_pending_updates (table_name, record_id)
            );

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
                FOREIGN KEY (updated_by) REFERENCES users(id),
                INDEX idx_product_updates (product_id, created_at)
            );

            CREATE TABLE IF NOT EXISTS refunds (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id BIGINT UNSIGNED NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                reason TEXT,
                processed_by BIGINT UNSIGNED,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id),
                FOREIGN KEY (processed_by) REFERENCES users(id),
                INDEX idx_order_refunds (order_id, created_at)
            );

            CREATE TABLE IF NOT EXISTS archived_orders LIKE orders;
            ALTER TABLE archived_orders 
                ADD COLUMN archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ADD INDEX idx_archived_date (archived_at);

            CREATE TABLE IF NOT EXISTS archived_order_items LIKE order_items;
            ALTER TABLE archived_order_items 
                ADD COLUMN archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ADD INDEX idx_archived_date (archived_at);

            CREATE TABLE IF NOT EXISTS inventory_adjustments (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id BIGINT UNSIGNED NOT NULL,
                adjustment_type ENUM("reduce", "restore") NOT NULL,
                reason TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id),
                INDEX idx_order_adjustments (order_id, created_at)
            );
        ');
    }

    public function down()
    {
        // Drop tables in reverse order to handle foreign key constraints
        DB::unprepared('
            DROP TABLE IF EXISTS inventory_adjustments;
            DROP TABLE IF EXISTS archived_order_items;
            DROP TABLE IF EXISTS archived_orders;
            DROP TABLE IF EXISTS refunds;
            DROP TABLE IF EXISTS inventory_logs;
            DROP TABLE IF EXISTS search_index_updates;
            DROP TABLE IF EXISTS user_activity_log;
            DROP TABLE IF EXISTS stock_alerts;
            DROP TABLE IF EXISTS price_history;
            DROP TABLE IF EXISTS order_status_log;
        ');
    }
}

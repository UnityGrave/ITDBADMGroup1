-- =====================================================================
-- DEFENSE-IN-DEPTH SECURITY: FUTURE IMPLEMENTATION
-- =====================================================================
--
-- PURPOSE: Complete action-based MySQL users for ALL database tables (current + future)
-- STATUS:  🔄 FUTURE USE - DO NOT USE YET
-- TESTED:  ⚠️  Will fail on non-existent tables (products, orders, etc.)
--
-- This script includes privileges for business tables that don't exist yet:
-- • products, product_categories, orders, order_items
-- • shopping_cart, cart_items  
--
-- USE WHEN: After creating the e-commerce database tables
-- 
-- FOR NOW: Use defense_in_depth_current.sql instead
--
-- COMMAND (when ready): Get-Content database/sql/defense_in_depth_future.sql | docker-compose exec -T db mysql -uroot -proot_password
--
-- =====================================================================

USE konibui;

-- =====================================================================
-- STEP 1: DROP EXISTING USERS (Clean Implementation)
-- =====================================================================

-- Drop previous role-based users
DROP USER IF EXISTS 'admin_konibui'@'%';
DROP USER IF EXISTS 'employee_konibui'@'%';
DROP USER IF EXISTS 'customer_test'@'%';

-- Drop existing action-based users if they exist
DROP USER IF EXISTS 'konibui_read_only'@'%';
DROP USER IF EXISTS 'konibui_data_entry'@'%';  
DROP USER IF EXISTS 'konibui_admin_ops'@'%';
DROP USER IF EXISTS 'konibui_system_admin'@'%';

-- =====================================================================
-- STEP 2: CREATE ACTION-BASED MYSQL USERS
-- =====================================================================
-- Users are created based on what ACTIONS they can perform

-- READ-ONLY USER: For viewing data, product browsing, profile viewing
-- Used by: Any user viewing data (customers browsing, staff viewing reports)
CREATE USER 'konibui_read_only'@'%' IDENTIFIED BY 'read_secure_2024!';

-- DATA ENTRY USER: For creating orders, updating profiles, basic data operations  
-- Used by: Customers placing orders, staff processing orders, profile updates
CREATE USER 'konibui_data_entry'@'%' IDENTIFIED BY 'data_secure_2024!';

-- ADMIN OPERATIONS USER: For managing users, roles, business operations
-- Used by: Admin/Staff managing users, processing refunds, inventory management  
CREATE USER 'konibui_admin_ops'@'%' IDENTIFIED BY 'admin_secure_2024!';

-- SYSTEM ADMIN USER: For system operations, migrations, full database access
-- Used by: System maintenance, migrations, emergency operations
CREATE USER 'konibui_system_admin'@'%' IDENTIFIED BY 'system_secure_2024!';

-- =====================================================================
-- STEP 3: GRANT PRIVILEGES BASED ON ACTIONS
-- =====================================================================

-- =====================================================================
-- READ-ONLY USER: SELECT permissions only
-- =====================================================================
-- Can view all business data but cannot modify anything
GRANT SELECT ON konibui.users TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.roles TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.role_user TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.cache TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.sessions TO 'konibui_read_only'@'%';

-- Future business tables (when implemented)
GRANT SELECT ON konibui.products TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.product_categories TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.orders TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.order_items TO 'konibui_read_only'@'%';

-- =====================================================================
-- DATA ENTRY USER: SELECT + INSERT permissions for business operations
-- =====================================================================
-- Can read data and create new records (orders, profiles, etc.)
GRANT SELECT ON konibui.users TO 'konibui_data_entry'@'%';
GRANT SELECT ON konibui.roles TO 'konibui_data_entry'@'%';
GRANT SELECT ON konibui.role_user TO 'konibui_data_entry'@'%';

-- Can manage own sessions and password resets
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.sessions TO 'konibui_data_entry'@'%';
GRANT SELECT, INSERT, DELETE ON konibui.password_reset_tokens TO 'konibui_data_entry'@'%';

-- Can update user profiles (own data only - enforced by application layer)
GRANT UPDATE ON konibui.users TO 'konibui_data_entry'@'%';

-- Future business operations
GRANT SELECT, INSERT ON konibui.orders TO 'konibui_data_entry'@'%';
GRANT SELECT, INSERT ON konibui.order_items TO 'konibui_data_entry'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.shopping_cart TO 'konibui_data_entry'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cart_items TO 'konibui_data_entry'@'%';

-- =====================================================================
-- ADMIN OPERATIONS USER: Business management operations
-- =====================================================================
-- Can perform administrative business operations
GRANT SELECT, INSERT, UPDATE ON konibui.users TO 'konibui_admin_ops'@'%';
GRANT SELECT ON konibui.roles TO 'konibui_admin_ops'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.role_user TO 'konibui_admin_ops'@'%';

-- Can manage cache and system operations
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache TO 'konibui_admin_ops'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache_locks TO 'konibui_admin_ops'@'%';

-- Can view system logs for debugging
GRANT SELECT ON konibui.failed_jobs TO 'konibui_admin_ops'@'%';
GRANT SELECT ON konibui.job_batches TO 'konibui_admin_ops'@'%';

-- Future business admin operations
GRANT SELECT, INSERT, UPDATE ON konibui.products TO 'konibui_admin_ops'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.product_categories TO 'konibui_admin_ops'@'%';
GRANT SELECT, UPDATE ON konibui.orders TO 'konibui_admin_ops'@'%';  -- Can update but not delete orders
GRANT SELECT, INSERT, UPDATE ON konibui.order_items TO 'konibui_admin_ops'@'%';

-- =====================================================================
-- SYSTEM ADMIN USER: Full privileges for system operations
-- =====================================================================
-- Full access for migrations, emergency operations, system maintenance
GRANT ALL PRIVILEGES ON konibui.* TO 'konibui_system_admin'@'%';

-- =====================================================================
-- STEP 4: FLUSH PRIVILEGES
-- =====================================================================

FLUSH PRIVILEGES;

-- =====================================================================
-- STEP 5: VERIFICATION
-- =====================================================================

-- Show all created action-based users
SELECT User, Host FROM mysql.user WHERE User LIKE 'konibui_%';

-- =====================================================================
-- ACTION-BASED MAPPING REFERENCE
-- =====================================================================
-- Action Type           → MySQL User           → Use Cases
-- Read Operations       → konibui_read_only    → Browsing products, viewing profile, reports
-- Data Entry           → konibui_data_entry   → Placing orders, updating profile, cart operations
-- Admin Operations      → konibui_admin_ops    → Managing users/products, processing orders  
-- System Operations     → konibui_system_admin → Migrations, emergency access, full admin

-- =====================================================================
-- SECURITY BENEFITS
-- =====================================================================
-- 1. Principle of Least Privilege: Each connection has minimum required permissions
-- 2. Action-Based Security: Permissions match the specific operation being performed
-- 3. Defense in Depth: Even if Laravel is compromised, database limits damage
-- 4. Granular Control: Different actions use different database permissions
-- 5. Audit Trail: Database logs show which user performed what action 
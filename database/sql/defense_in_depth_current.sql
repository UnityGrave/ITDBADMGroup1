-- =====================================================================
-- DEFENSE-IN-DEPTH SECURITY: CURRENT IMPLEMENTATION
-- =====================================================================
-- 
-- PURPOSE: Sets up action-based MySQL users for existing database tables
-- STATUS:  ✅ CURRENT ACTIVE IMPLEMENTATION - USE THIS FILE
-- TESTED:  ✅ Fully tested and working
--
-- This script creates MySQL database users based on ACTIONS rather than roles:
-- • konibui_read_only     - SELECT only (view data, browse, reports)
-- • konibui_data_entry    - SELECT, INSERT, limited UPDATE (orders, profiles)
-- • konibui_admin_ops     - SELECT, INSERT, UPDATE (manage users, business ops)
-- • konibui_system_admin  - ALL PRIVILEGES (migrations, emergency access)
--
-- USAGE: Run this script to set up the defense-in-depth security system
-- COMMAND: Get-Content database/sql/defense_in_depth_current.sql | docker-compose exec -T db mysql -uroot -proot_password
--
-- =====================================================================

USE konibui;

-- =====================================================================
-- STEP 1: DROP EXISTING USERS
-- =====================================================================

DROP USER IF EXISTS 'admin_konibui'@'%';
DROP USER IF EXISTS 'employee_konibui'@'%'; 
DROP USER IF EXISTS 'customer_test'@'%';
DROP USER IF EXISTS 'konibui_read_only'@'%';
DROP USER IF EXISTS 'konibui_data_entry'@'%';
DROP USER IF EXISTS 'konibui_admin_ops'@'%';
DROP USER IF EXISTS 'konibui_system_admin'@'%';

-- =====================================================================
-- STEP 2: CREATE ACTION-BASED USERS
-- =====================================================================

CREATE USER 'konibui_read_only'@'%' IDENTIFIED BY 'read_secure_2024!';
CREATE USER 'konibui_data_entry'@'%' IDENTIFIED BY 'data_secure_2024!';
CREATE USER 'konibui_admin_ops'@'%' IDENTIFIED BY 'admin_secure_2024!';
CREATE USER 'konibui_system_admin'@'%' IDENTIFIED BY 'system_secure_2024!';

-- =====================================================================
-- STEP 3: GRANT PRIVILEGES ON EXISTING TABLES ONLY
-- =====================================================================

-- READ-ONLY USER: SELECT permissions only
GRANT SELECT ON konibui.users TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.roles TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.role_user TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.cache TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.sessions TO 'konibui_read_only'@'%';
GRANT SELECT ON konibui.migrations TO 'konibui_read_only'@'%';

-- DATA ENTRY USER: SELECT + INSERT + limited UPDATE
GRANT SELECT ON konibui.users TO 'konibui_data_entry'@'%';
GRANT SELECT ON konibui.roles TO 'konibui_data_entry'@'%';
GRANT SELECT ON konibui.role_user TO 'konibui_data_entry'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.sessions TO 'konibui_data_entry'@'%';
GRANT SELECT, INSERT, DELETE ON konibui.password_reset_tokens TO 'konibui_data_entry'@'%';
GRANT UPDATE ON konibui.users TO 'konibui_data_entry'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache TO 'konibui_data_entry'@'%';

-- ADMIN OPERATIONS USER: Business management operations
GRANT SELECT, INSERT, UPDATE ON konibui.users TO 'konibui_admin_ops'@'%';
GRANT SELECT ON konibui.roles TO 'konibui_admin_ops'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.role_user TO 'konibui_admin_ops'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache TO 'konibui_admin_ops'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache_locks TO 'konibui_admin_ops'@'%';
GRANT SELECT ON konibui.failed_jobs TO 'konibui_admin_ops'@'%';
GRANT SELECT ON konibui.job_batches TO 'konibui_admin_ops'@'%';
GRANT SELECT ON konibui.migrations TO 'konibui_admin_ops'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.sessions TO 'konibui_admin_ops'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.jobs TO 'konibui_admin_ops'@'%';

-- SYSTEM ADMIN USER: Full privileges
GRANT ALL PRIVILEGES ON konibui.* TO 'konibui_system_admin'@'%';

-- =====================================================================
-- STEP 4: FLUSH PRIVILEGES
-- =====================================================================

FLUSH PRIVILEGES;

-- Show all created users
SELECT User, Host FROM mysql.user WHERE User LIKE 'konibui_%'; 
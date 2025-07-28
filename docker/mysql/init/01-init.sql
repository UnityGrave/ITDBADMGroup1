-- Grant super privileges to konibui_system_admin
SET @had_super_privilege_check=0;
SET @had_super_privilege_check = (SELECT COUNT(*) FROM mysql.user WHERE User='konibui_system_admin' AND Super_priv='Y');

-- Only create and grant if not already done
SET @sql = IF (@had_super_privilege_check = 0,
    'GRANT SUPER ON *.* TO "konibui_system_admin"@"%"',
    'SELECT "User already has SUPER privilege"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Set global variables
SET GLOBAL log_bin_trust_function_creators = 1;

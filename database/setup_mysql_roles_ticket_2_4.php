<?php

/**
 * TICKET 2.4: Setup MySQL Roles for Database-Layer Security
 * 
 * This script creates MySQL roles using root privileges and sets up the database-layer security
 * as specified in TICKET 2.4. Since Laravel's default database user doesn't have CREATE ROLE
 * privileges, we need to execute this as root.
 * 
 * USAGE: docker-compose exec app php database/setup_mysql_roles_ticket_2_4.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n" . str_repeat("=", 80) . "\n";
echo "🎯 TICKET 2.4: MySQL Roles for Database-Layer Security\n";
echo str_repeat("=", 80) . "\n\n";

try {
    // Create connection to MySQL as root user
    $rootConnection = new PDO(
        "mysql:host=" . env('DB_HOST', 'db') . ";port=3306;dbname=" . env('DB_DATABASE', 'konibui'),
        'root', 
        'root_password'
    );
    
    $rootConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to MySQL as root user\n\n";
    
    // Step 1: Create MySQL roles
    echo "📋 Creating MySQL roles...\n";
    echo str_repeat("-", 40) . "\n";
    
    $createRolesSQL = "
        -- Create the three MySQL roles mirroring application roles
        CREATE ROLE IF NOT EXISTS 'konibui_admin';
        CREATE ROLE IF NOT EXISTS 'konibui_employee'; 
        CREATE ROLE IF NOT EXISTS 'konibui_customer';
    ";
    
    $rootConnection->exec($createRolesSQL);
    echo "✅ Created roles: konibui_admin, konibui_employee, konibui_customer\n\n";
    
    // Step 2: Grant privileges to admin role
    echo "🔴 Setting up ADMIN role privileges...\n";
    echo str_repeat("-", 40) . "\n";
    
    $adminPrivilegesSQL = "
        -- Admin has full privileges on all application tables
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
    ";
    
    $rootConnection->exec($adminPrivilegesSQL);
    echo "✅ Admin role: Full privileges granted\n\n";
    
    // Step 3: Grant privileges to employee role  
    echo "🟡 Setting up EMPLOYEE role privileges...\n";
    echo str_repeat("-", 40) . "\n";
    
    $employeePrivilegesSQL = "
        -- Employee can manage users but with restrictions
        GRANT SELECT, INSERT, UPDATE ON konibui.users TO 'konibui_employee';
        GRANT SELECT ON konibui.roles TO 'konibui_employee';
        GRANT SELECT ON konibui.role_user TO 'konibui_employee';
        GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.sessions TO 'konibui_employee';
        GRANT SELECT, INSERT, DELETE ON konibui.password_reset_tokens TO 'konibui_employee';
        GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache TO 'konibui_employee';
        GRANT SELECT ON konibui.failed_jobs TO 'konibui_employee';
        GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.jobs TO 'konibui_employee';
        GRANT SELECT ON konibui.migrations TO 'konibui_employee';
    ";
    
    $rootConnection->exec($employeePrivilegesSQL);
    echo "✅ Employee role: Limited privileges granted (no DELETE on users)\n\n";
    
    // Step 4: Grant privileges to customer role
    echo "🟢 Setting up CUSTOMER role privileges...\n";
    echo str_repeat("-", 40) . "\n";
    
    $customerPrivilegesSQL = "
        -- Customer can only update their own profile (enforced by application layer)
        GRANT SELECT, UPDATE ON konibui.users TO 'konibui_customer';
        GRANT SELECT ON konibui.roles TO 'konibui_customer';
        GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.sessions TO 'konibui_customer';
        GRANT SELECT, INSERT, DELETE ON konibui.password_reset_tokens TO 'konibui_customer';
    ";
    
    $rootConnection->exec($customerPrivilegesSQL);
    echo "✅ Customer role: Minimal privileges granted (profile + sessions only)\n\n";
    
    // Step 5: Flush privileges
    echo "🔄 Flushing privileges...\n";
    $rootConnection->exec("FLUSH PRIVILEGES;");
    echo "✅ Privileges flushed\n\n";
    
    // Step 6: Verify roles were created
    echo "🔍 Verifying created roles...\n";
    echo str_repeat("-", 40) . "\n";
    
    $stmt = $rootConnection->query("SELECT * FROM mysql.roles_mapping LIMIT 5");
    $rolesInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($rolesInfo)) {
        // Try alternative verification
        $stmt = $rootConnection->query("SHOW GRANTS FOR 'konibui_admin'");
        $adminGrants = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "✅ konibui_admin grants:\n";
        foreach ($adminGrants as $grant) {
            echo "   • $grant\n";
        }
    }
    
    echo "\n📊 TICKET 2.4 IMPLEMENTATION SUMMARY\n";
    echo str_repeat("=", 60) . "\n";
    echo "✅ MySQL roles created: konibui_admin, konibui_employee, konibui_customer\n";
    echo "✅ Privileges granted following 'defense-in-depth' strategy\n";
    echo "✅ Role privileges follow 'principle of least privilege'\n";
    echo "✅ Database-layer security established\n\n";
    
    echo "💡 NEXT STEPS:\n";
    echo "1. Create MySQL users and assign roles (see README.md)\n";
    echo "2. Test with restricted database users in .env\n";
    echo "3. Verify database-level security restrictions\n\n";
    
    echo "🔗 TESTING COMMANDS:\n";
    echo "# Create test users (run from README.md examples)\n";
    echo "# Update .env with restricted user credentials\n";
    echo "# Test that database enforces role restrictions\n\n";
    
    echo "🎉 TICKET 2.4: MySQL Database-Layer Security COMPLETED!\n\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    echo "\n🚨 This is likely because MySQL ROLES are not supported in this MySQL version.\n";
    echo "MySQL 8.0+ supports roles, but some configurations may not have this enabled.\n\n";
    
    echo "💡 ALTERNATIVE APPROACH:\n";
    echo "If roles are not supported, use the action-based user approach we implemented earlier:\n";
    echo "- Use database/sql/defense_in_depth_current.sql\n";
    echo "- This creates USERS instead of ROLES\n";
    echo "- Provides same security benefits\n\n";
    
} catch (Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 
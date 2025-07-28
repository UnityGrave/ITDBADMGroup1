<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    // Database connection
    $host = $_ENV['DB_HOST'] ?? 'db';
    $dbname = $_ENV['DB_DATABASE'] ?? 'konibui';
    $username = $_ENV['DB_USERNAME'] ?? 'konibui_user';
    $password = $_ENV['DB_PASSWORD'] ?? 'konibui_password';
    
    $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "🧹 Cleaning Database - Dropping all procedures and triggers...\n\n";

    // Get all stored procedures
    echo "📋 Finding stored procedures...\n";
    $stmt = $pdo->query("SHOW PROCEDURE STATUS WHERE Db = '{$dbname}'");
    $procedures = $stmt->fetchAll();
    
    if (!empty($procedures)) {
        echo "Found " . count($procedures) . " stored procedures:\n";
        foreach ($procedures as $proc) {
            echo "  - {$proc['Name']}\n";
        }
        
        echo "\n🗑️  Dropping stored procedures...\n";
        foreach ($procedures as $proc) {
            try {
                $pdo->exec("DROP PROCEDURE IF EXISTS `{$proc['Name']}`");
                echo "  ✅ Dropped procedure: {$proc['Name']}\n";
            } catch (Exception $e) {
                echo "  ❌ Error dropping procedure {$proc['Name']}: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "No stored procedures found.\n";
    }

    // Get all triggers
    echo "\n📋 Finding triggers...\n";
    $stmt = $pdo->query("SHOW TRIGGERS");
    $triggers = $stmt->fetchAll();
    
    if (!empty($triggers)) {
        echo "Found " . count($triggers) . " triggers:\n";
        foreach ($triggers as $trigger) {
            echo "  - {$trigger['Trigger']} on {$trigger['Table']}\n";
        }
        
        echo "\n🗑️  Dropping triggers...\n";
        foreach ($triggers as $trigger) {
            try {
                $pdo->exec("DROP TRIGGER IF EXISTS `{$trigger['Trigger']}`");
                echo "  ✅ Dropped trigger: {$trigger['Trigger']}\n";
            } catch (Exception $e) {
                echo "  ❌ Error dropping trigger {$trigger['Trigger']}: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "No triggers found.\n";
    }

    // Get all functions (if any)
    echo "\n📋 Finding functions...\n";
    $stmt = $pdo->query("SHOW FUNCTION STATUS WHERE Db = '{$dbname}'");
    $functions = $stmt->fetchAll();
    
    if (!empty($functions)) {
        echo "Found " . count($functions) . " functions:\n";
        foreach ($functions as $func) {
            echo "  - {$func['Name']}\n";
        }
        
        echo "\n🗑️  Dropping functions...\n";
        foreach ($functions as $func) {
            try {
                $pdo->exec("DROP FUNCTION IF EXISTS `{$func['Name']}`");
                echo "  ✅ Dropped function: {$func['Name']}\n";
            } catch (Exception $e) {
                echo "  ❌ Error dropping function {$func['Name']}: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "No functions found.\n";
    }

    // Get all tables
    echo "\n📋 Finding tables...\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($tables)) {
        echo "Found " . count($tables) . " tables:\n";
        foreach ($tables as $table) {
            echo "  - {$table}\n";
        }
        
        echo "\n🗑️  Dropping tables...\n";
        
        // Disable foreign key checks to avoid constraint issues
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        foreach ($tables as $table) {
            try {
                $pdo->exec("DROP TABLE IF EXISTS `{$table}`");
                echo "  ✅ Dropped table: {$table}\n";
            } catch (Exception $e) {
                echo "  ❌ Error dropping table {$table}: " . $e->getMessage() . "\n";
            }
        }
        
        // Re-enable foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    } else {
        echo "No tables found.\n";
    }

    echo "\n✨ Database cleanup completed!\n";
    echo "\nYou can now run fresh migrations:\n";
    echo "  docker-compose exec app php artisan migrate\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

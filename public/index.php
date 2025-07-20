<?php
// Simple test file for Docker setup verification
echo "<h1>Konibui Docker Environment</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Time: " . date('Y-m-d H:i:s') . "</p>";

// Test database connection
try {
    $host = $_ENV['DB_HOST'] ?? 'db';
    $dbname = $_ENV['DB_DATABASE'] ?? 'konibui';
    $username = $_ENV['DB_USERNAME'] ?? 'konibui_user';
    $password = $_ENV['DB_PASSWORD'] ?? 'konibui_password';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    echo "<p>Database: $dbname on $host</p>";
} catch (PDOException $e) {
    echo "<p style='color: orange;'>⚠ Database connection pending (this is normal on first startup)</p>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Docker Services Status</h2>";
echo "<ul>";
echo "<li><strong>Web Server (Nginx):</strong> ✓ Running (you're seeing this page)</li>";
echo "<li><strong>PHP-FPM:</strong> ✓ Running (PHP version displayed above)</li>";
echo "<li><strong>Database (MySQL):</strong> Check connection status above</li>";
echo "<li><strong>phpMyAdmin:</strong> <a href='http://localhost:8081' target='_blank'>Access here</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Install Laravel: <code>docker-compose exec app composer create-project laravel/laravel .</code></li>";
echo "<li>Set up environment: <code>cp .env.example .env</code></li>";
echo "<li>Generate app key: <code>docker-compose exec app php artisan key:generate</code></li>";
echo "<li>Run migrations: <code>docker-compose exec app php artisan migrate</code></li>";
echo "</ol>";
?>

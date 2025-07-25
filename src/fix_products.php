<?php
// Simple fix for missing status column in products table
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("❌ Database connection failed. Please check XAMPP MySQL service is running.");
}

try {
    echo "<h2>🔧 Quick Fix for Products Table</h2>";
    
    // Check if status column exists
    $result = $db->query("SHOW COLUMNS FROM products LIKE 'status'");
    if ($result->rowCount() == 0) {
        echo "<p>➤ Adding status column to products table...</p>";
        $db->exec("ALTER TABLE products ADD COLUMN status VARCHAR(50) DEFAULT 'active' AFTER stock");
        echo "<p style='color: green;'>✓ Status column added successfully!</p>";
        
        // Update all existing products to active
        $db->exec("UPDATE products SET status = 'active' WHERE status IS NULL OR status = ''");
        echo "<p style='color: green;'>✓ All existing products set to active status</p>";
    } else {
        echo "<p style='color: green;'>✓ Status column already exists!</p>";
    }
    
    echo "<hr>";
    echo "<h3>🎉 Fix completed!</h3>";
    echo "<p><a href='products.php'>➤ Test Products Page</a></p>";
    echo "<p><a href='dashboard.php'>➤ Go to Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

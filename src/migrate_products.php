<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection failed!");
}

try {
    // Check if image_url column exists
    $checkQuery = "SHOW COLUMNS FROM products LIKE 'image_url'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        // Add image_url column if it doesn't exist
        $alterQuery = "ALTER TABLE products ADD COLUMN image_url VARCHAR(255) DEFAULT NULL AFTER description";
        $db->exec($alterQuery);
        echo "Column 'image_url' added to products table successfully.\n";
    } else {
        echo "Column 'image_url' already exists in products table.\n";
    }
    
    // Check if is_active column exists
    $checkQuery = "SHOW COLUMNS FROM products LIKE 'is_active'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        // Add is_active column if it doesn't exist
        $alterQuery = "ALTER TABLE products ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER image_url";
        $db->exec($alterQuery);
        echo "Column 'is_active' added to products table successfully.\n";
    } else {
        echo "Column 'is_active' already exists in products table.\n";
    }
    
    // Update existing products with placeholder images
    $updateQuery = "UPDATE products SET image_url = 'https://via.placeholder.com/200x200/ec4899/ffffff?text=Product' WHERE image_url IS NULL OR image_url = ''";
    $db->exec($updateQuery);
    echo "Updated existing products with placeholder images.\n";
    
    echo "Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

<?php
// Fix checkout - Add missing columns to transactions table
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("❌ Database connection failed. Please check XAMPP MySQL service is running.");
}

try {
    echo "<h2>🔧 Fixing Checkout - Adding Missing Columns</h2>";
    
    // List of columns to add to transactions table
    $columns_to_add = [
        'shipping_name' => "ALTER TABLE transactions ADD COLUMN shipping_name VARCHAR(255) AFTER shipping_address",
        'shipping_phone' => "ALTER TABLE transactions ADD COLUMN shipping_phone VARCHAR(20) AFTER shipping_name",
        'shipping_city' => "ALTER TABLE transactions ADD COLUMN shipping_city VARCHAR(255) AFTER shipping_phone",
        'shipping_postal_code' => "ALTER TABLE transactions ADD COLUMN shipping_postal_code VARCHAR(10) AFTER shipping_city",
        'payment_method' => "ALTER TABLE transactions ADD COLUMN payment_method VARCHAR(100) AFTER shipping_postal_code",
        'notes' => "ALTER TABLE transactions ADD COLUMN notes TEXT AFTER payment_method"
    ];
    
    foreach ($columns_to_add as $column => $sql) {
        // Check if column exists
        $result = $db->query("SHOW COLUMNS FROM transactions LIKE '$column'");
        if ($result->rowCount() == 0) {
            echo "<p>➤ Adding $column column to transactions table...</p>";
            $db->exec($sql);
            echo "<p style='color: green;'>✓ $column column added successfully!</p>";
        } else {
            echo "<p style='color: green;'>✓ $column column already exists!</p>";
        }
    }
    
    // Also check for phone column in users table
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'phone'");
    if ($result->rowCount() == 0) {
        echo "<p>➤ Adding phone column to users table...</p>";
        $db->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email");
        echo "<p style='color: green;'>✓ phone column added to users table!</p>";
    } else {
        echo "<p style='color: green;'>✓ phone column already exists in users table!</p>";
    }
    
    // Also check for address column in users table  
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'address'");
    if ($result->rowCount() == 0) {
        echo "<p>➤ Adding address column to users table...</p>";
        $db->exec("ALTER TABLE users ADD COLUMN address TEXT AFTER phone");
        echo "<p style='color: green;'>✓ address column added to users table!</p>";
    } else {
        echo "<p style='color: green;'>✓ address column already exists in users table!</p>";
    }
    
    echo "<hr>";
    echo "<h3>🎉 Checkout fix completed!</h3>";
    echo "<p><strong>Added columns to transactions table:</strong></p>";
    echo "<ul>";
    echo "<li>shipping_name - Nama penerima</li>";
    echo "<li>shipping_phone - Nomor telepon penerima</li>";
    echo "<li>shipping_city - Kota pengiriman</li>";
    echo "<li>shipping_postal_code - Kode pos</li>";
    echo "<li>payment_method - Metode pembayaran</li>";
    echo "<li>notes - Catatan pesanan</li>";
    echo "</ul>";
    echo "<p><strong>Added columns to users table:</strong></p>";
    echo "<ul>";
    echo "<li>phone - Nomor telepon user</li>";
    echo "<li>address - Alamat user</li>";
    echo "</ul>";
    echo "<br>";
    echo "<p><a href='checkout.php'>➤ Test Checkout</a></p>";
    echo "<p><a href='dashboard.php'>➤ Go to Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

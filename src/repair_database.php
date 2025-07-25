<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    echo "Starting database repair...\n";
    
    // Check if transaction_code column exists
    $result = $db->query("SHOW COLUMNS FROM transactions LIKE 'transaction_code'");
    if ($result->rowCount() == 0) {
        echo "Adding transaction_code column to transactions table...\n";
        $db->exec("ALTER TABLE transactions ADD COLUMN transaction_code VARCHAR(100) UNIQUE AFTER user_id");
        echo "✓ transaction_code column added\n";
    } else {
        echo "✓ transaction_code column already exists\n";
    }
    
    // Check if demo customer exists with correct role
    $stmt = $db->prepare("SELECT id, name, email, role FROM users WHERE email = 'customer@merona.com'");
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        echo "Creating demo customer...\n";
        $hashed_password = password_hash('customer', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
        $stmt->execute(['Demo Customer', 'customer@merona.com', $hashed_password]);
        echo "✓ Demo customer created\n";
    } else {
        echo "✓ Demo customer exists: " . $customer['name'] . " (" . $customer['role'] . ")\n";
        
        // Update name if necessary
        if ($customer['name'] !== 'Demo Customer') {
            $stmt = $db->prepare("UPDATE users SET name = 'Demo Customer' WHERE email = 'customer@merona.com'");
            $stmt->execute();
            echo "✓ Demo customer name updated\n";
        }
        
        // Update password to ensure it's 'customer'
        $hashed_password = password_hash('customer', PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = 'customer@merona.com'");
        $stmt->execute([$hashed_password]);
        echo "✓ Demo customer password updated\n";
    }
    
    // Check if admin exists
    $stmt = $db->prepare("SELECT id, name, email, role FROM users WHERE email = 'admin@merona.com'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        echo "Creating admin user...\n";
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['Admin', 'admin@merona.com', $hashed_password]);
        echo "✓ Admin user created\n";
    } else {
        echo "✓ Admin user exists: " . $admin['name'] . " (" . $admin['role'] . ")\n";
    }
    
    echo "\nDatabase repair completed successfully!\n";
    echo "\nCredentials:\n";
    echo "Admin: admin@merona.com / admin123\n";
    echo "Customer: customer@merona.com / customer\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

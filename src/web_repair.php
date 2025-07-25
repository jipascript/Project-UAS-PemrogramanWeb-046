<!DOCTYPE html>
<html>
<head>
    <title>Database Repair - Merona Shop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Repair Tool</h1>
        
        <?php
        require_once 'config/database.php';

        $database = new Database();
        $db = $database->getConnection();

        if (!$db) {
            echo '<div class="error"><h3>❌ Database Connection Failed</h3></div>';
            echo '<p>Please make sure XAMPP MySQL service is running and database "merona_shop" exists.</p>';
            exit;
        }

        try {
            echo '<div class="info"><h3>🔧 Starting Database Repair...</h3></div>';
            echo '<pre>';
            
            // Check if transaction_code column exists
            $result = $db->query("SHOW COLUMNS FROM transactions LIKE 'transaction_code'");
            if ($result->rowCount() == 0) {
                echo "➤ Adding transaction_code column to transactions table...\n";
                $db->exec("ALTER TABLE transactions ADD COLUMN transaction_code VARCHAR(100) UNIQUE AFTER user_id");
                echo "<span class='success'>✓ transaction_code column added</span>\n";
            } else {
                echo "<span class='success'>✓ transaction_code column already exists</span>\n";
            }
            
            // Check if status column exists in products table
            $result = $db->query("SHOW COLUMNS FROM products LIKE 'status'");
            if ($result->rowCount() == 0) {
                echo "➤ Adding status column to products table...\n";
                $db->exec("ALTER TABLE products ADD COLUMN status VARCHAR(50) DEFAULT 'active' AFTER stock");
                echo "<span class='success'>✓ status column added to products table</span>\n";
                
                // Update all existing products to have 'active' status
                $db->exec("UPDATE products SET status = 'active' WHERE status IS NULL OR status = ''");
                echo "<span class='success'>✓ All existing products set to active status</span>\n";
            } else {
                echo "<span class='success'>✓ status column already exists in products table</span>\n";
            }
            
            // Fix checkout - Add missing columns to transactions table
            echo "\n➤ Checking transactions table columns...\n";
            $columns_to_add = [
                'shipping_name' => "ALTER TABLE transactions ADD COLUMN shipping_name VARCHAR(255) AFTER shipping_address",
                'shipping_phone' => "ALTER TABLE transactions ADD COLUMN shipping_phone VARCHAR(20) AFTER shipping_name", 
                'shipping_city' => "ALTER TABLE transactions ADD COLUMN shipping_city VARCHAR(255) AFTER shipping_phone",
                'shipping_postal_code' => "ALTER TABLE transactions ADD COLUMN shipping_postal_code VARCHAR(10) AFTER shipping_city",
                'payment_method' => "ALTER TABLE transactions ADD COLUMN payment_method VARCHAR(100) AFTER shipping_postal_code",
                'notes' => "ALTER TABLE transactions ADD COLUMN notes TEXT AFTER payment_method"
            ];
            
            foreach ($columns_to_add as $column => $sql) {
                $result = $db->query("SHOW COLUMNS FROM transactions LIKE '$column'");
                if ($result->rowCount() == 0) {
                    $db->exec($sql);
                    echo "<span class='success'>✓ $column column added to transactions table</span>\n";
                } else {
                    echo "<span class='success'>✓ $column column already exists in transactions table</span>\n";
                }
            }
            
            // Add missing columns to users table
            $user_columns = [
                'phone' => "ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email",
                'address' => "ALTER TABLE users ADD COLUMN address TEXT AFTER phone"
            ];
            
            foreach ($user_columns as $column => $sql) {
                $result = $db->query("SHOW COLUMNS FROM users LIKE '$column'");
                if ($result->rowCount() == 0) {
                    $db->exec($sql);
                    echo "<span class='success'>✓ $column column added to users table</span>\n";
                } else {
                    echo "<span class='success'>✓ $column column already exists in users table</span>\n";
                }
            }
            
            // Check if demo customer exists with correct role
            $stmt = $db->prepare("SELECT id, name, email, role FROM users WHERE email = 'customer@merona.com'");
            $stmt->execute();
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$customer) {
                echo "➤ Creating demo customer...\n";
                $hashed_password = password_hash('customer', PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
                $stmt->execute(['Demo Customer', 'customer@merona.com', $hashed_password]);
                echo "<span class='success'>✓ Demo customer created</span>\n";
            } else {
                echo "<span class='success'>✓ Demo customer exists: " . htmlspecialchars($customer['name']) . " (" . htmlspecialchars($customer['role']) . ")</span>\n";
                
                // Update name if necessary
                if ($customer['name'] !== 'Demo Customer') {
                    $stmt = $db->prepare("UPDATE users SET name = 'Demo Customer' WHERE email = 'customer@merona.com'");
                    $stmt->execute();
                    echo "<span class='success'>✓ Demo customer name updated</span>\n";
                }
                
                // Update password to ensure it's 'customer'
                echo "➤ Updating demo customer password...\n";
                $hashed_password = password_hash('customer', PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = 'customer@merona.com'");
                $stmt->execute([$hashed_password]);
                echo "<span class='success'>✓ Demo customer password updated</span>\n";
            }
            
            // Check if admin exists
            $stmt = $db->prepare("SELECT id, name, email, role FROM users WHERE email = 'admin@merona.com'");
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admin) {
                echo "➤ Creating admin user...\n";
                $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
                $stmt->execute(['Admin', 'admin@merona.com', $hashed_password]);
                echo "<span class='success'>✓ Admin user created</span>\n";
            } else {
                echo "<span class='success'>✓ Admin user exists: " . htmlspecialchars($admin['name']) . " (" . htmlspecialchars($admin['role']) . ")</span>\n";
            }
            
            echo "\n<span class='success'>🎉 Database repair completed successfully!</span>\n";
            echo '</pre>';
            
            echo '<div class="info"><h3>📋 Login Credentials</h3></div>';
            echo '<pre>';
            echo "<strong>Admin:</strong> admin@merona.com / admin123\n";
            echo "<strong>Customer:</strong> customer@merona.com / customer\n";
            echo '</pre>';
            
            echo '<div class="success"><h3>✅ What\'s Fixed</h3></div>';
            echo '<ul>';
            echo '<li>Added missing transaction_code column to transactions table</li>';
            echo '<li>Added missing status column to products table</li>';
            echo '<li>Ensured demo customer exists with correct credentials</li>';
            echo '<li>Updated demo customer password to match login form</li>';
            echo '<li>Verified admin user exists</li>';
            echo '</ul>';
            
            echo '<div class="info"><h3>🔄 Next Steps</h3></div>';
            echo '<ul>';
            echo '<li><a href="login.php">Test login with demo customer</a></li>';
            echo '<li><a href="dashboard.php">Access user dashboard directly</a> (if logged in)</li>';
            echo '<li><a href="admin/">Access admin panel</a> (with admin credentials)</li>';
            echo '</ul>';
            
        } catch (Exception $e) {
            echo '<div class="error"><h3>❌ Error: ' . htmlspecialchars($e->getMessage()) . '</h3></div>';
        }
        ?>
    </div>
</body>
</html>

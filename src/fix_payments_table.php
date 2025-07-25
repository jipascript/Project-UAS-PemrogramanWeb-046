<!DOCTYPE html>
<html>
<head>
    <title>Fix Payments Table - Merona Shop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-tools"></i> Fix Payments Table Structure</h1>
        
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
            echo '<div class="info"><h3>🔧 Fixing Payments Table Structure...</h3></div>';
            echo '<pre>';
            
            // Check current payments table structure
            echo "➤ Checking current payments table structure...\n";
            $result = $db->query("DESCRIBE payments");
            $current_columns = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $current_columns[] = $row['Field'];
            }
            echo "<span class='info'>Current columns: " . implode(', ', $current_columns) . "</span>\n\n";
            
            // Add missing columns
            $columns_to_add = [
                'payment_method' => "ALTER TABLE payments ADD COLUMN payment_method VARCHAR(100) AFTER transaction_id",
                'amount' => "ALTER TABLE payments ADD COLUMN amount DECIMAL(10,2) AFTER payment_method", 
                'account_name' => "ALTER TABLE payments ADD COLUMN account_name VARCHAR(255) AFTER amount",
                'account_number' => "ALTER TABLE payments ADD COLUMN account_number VARCHAR(100) AFTER account_name"
            ];
            
            foreach ($columns_to_add as $column => $sql) {
                if (!in_array($column, $current_columns)) {
                    echo "➤ Adding $column column...\n";
                    $db->exec($sql);
                    echo "<span class='success'>✓ $column column added successfully</span>\n";
                } else {
                    echo "<span class='success'>✓ $column column already exists</span>\n";
                }
            }
            
            // Check if old 'method' column exists and rename/migrate if needed
            if (in_array('method', $current_columns) && !in_array('payment_method', $current_columns)) {
                echo "\n➤ Migrating old 'method' column to 'payment_method'...\n";
                $db->exec("ALTER TABLE payments ADD COLUMN payment_method VARCHAR(100) AFTER transaction_id");
                $db->exec("UPDATE payments SET payment_method = method WHERE method IS NOT NULL");
                echo "<span class='success'>✓ Data migrated from 'method' to 'payment_method'</span>\n";
                
                // Optionally drop old column (commented out for safety)
                // $db->exec("ALTER TABLE payments DROP COLUMN method");
                // echo "<span class='success'>✓ Old 'method' column dropped</span>\n";
            }
            
            // Verify table structure
            echo "\n➤ Verifying final table structure...\n";
            $result = $db->query("DESCRIBE payments");
            $final_columns = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $final_columns[] = $row['Field'] . ' (' . $row['Type'] . ')';
            }
            echo "<span class='info'>Final structure:</span>\n";
            foreach ($final_columns as $col) {
                echo "  - $col\n";
            }
            
            echo "\n<span class='success'>🎉 Payments table structure fixed successfully!</span>\n";
            echo '</pre>';
            
            echo '<div class="info"><h3>📋 What was fixed:</h3></div>';
            echo '<ul>';
            echo '<li>Added <code>payment_method</code> column for storing payment method (OVO, GoPay, etc.)</li>';
            echo '<li>Added <code>amount</code> column for storing payment amount</li>';
            echo '<li>Added <code>account_name</code> column for account holder name</li>';
            echo '<li>Added <code>account_number</code> column for account number/phone</li>';
            echo '</ul>';
            
            echo '<hr>';
            
            echo '<div class="success"><h3>✅ Now you can test payment upload!</h3></div>';
            echo '<p><a href="upload-payment.php?code=' . (isset($_GET['code']) ? $_GET['code'] : 'TRX20250719194997') . '">Go back to Upload Payment</a></p>';
            echo '<p><a href="orders.php">View Orders</a></p>';
            
        } catch (Exception $e) {
            echo '<div class="error"><h3>❌ Error: ' . htmlspecialchars($e->getMessage()) . '</h3></div>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        }
        ?>
    </div>
</body>
</html>

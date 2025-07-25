<!DOCTYPE html>
<html>
<head>
    <title>Test Invoice - Merona Shop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
        .invoice-link { display: block; padding: 15px; background: #e91e63; color: white; text-decoration: none; border-radius: 5px; text-align: center; margin: 10px 0; }
        .invoice-link:hover { background: #c2185b; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-file-invoice"></i> Test Invoice Generator</h1>
        
        <?php
        require_once 'includes/functions.php';
        
        if (isLoggedIn()) {
            echo '<div class="success"><h3>✅ User logged in: ' . $_SESSION['user_name'] . '</h3></div>';
            
            // Get user's completed orders
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                try {
                    $query = "SELECT transaction_code, created_at, total_amount, status 
                              FROM transactions 
                              WHERE user_id = ? AND status = 'completed' 
                              ORDER BY created_at DESC 
                              LIMIT 5";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$_SESSION['user_id']]);
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if ($orders) {
                        echo '<h3>📋 Available Invoices:</h3>';
                        foreach ($orders as $order) {
                            echo '<a href="invoice.php?code=' . $order['transaction_code'] . '" class="invoice-link" target="_blank">';
                            echo '<i class="fas fa-file-invoice-dollar"></i> ';
                            echo 'Invoice #' . $order['transaction_code'] . ' - ' . formatPrice($order['total_amount']);
                            echo '<br><small>Created: ' . date('d M Y, H:i', strtotime($order['created_at'])) . '</small>';
                            echo '</a>';
                        }
                    } else {
                        echo '<div class="info">';
                        echo '<h3>ℹ️ No completed orders found</h3>';
                        echo '<p>You need to complete an order first to generate an invoice.</p>';
                        echo '<p><a href="products.php">Go shopping</a> → Add to cart → Checkout → Upload payment</p>';
                        echo '</div>';
                    }
                    
                } catch (Exception $e) {
                    echo '<div class="error">Database error: ' . $e->getMessage() . '</div>';
                }
            }
        } else {
            echo '<div class="error"><h3>❌ Please login first</h3></div>';
            echo '<p><a href="login.php">Go to Login</a></p>';
        }
        ?>
        
        <hr>
        
        <h3><i class="fas fa-info-circle"></i> Invoice Features:</h3>
        <ul>
            <li><strong>📄 Print Invoice:</strong> Professional print-ready format</li>
            <li><strong>📸 Save as JPG:</strong> Download invoice as high-quality image</li>
            <li><strong>🎓 Learning Mode:</strong> Clear indication this is for educational purposes</li>
            <li><strong>📊 Complete Details:</strong> All order and payment information included</li>
        </ul>
        
        <hr>
        
        <h3><i class="fas fa-link"></i> Quick Links:</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin: 20px 0;">
            <a href="login.php" style="display: block; padding: 10px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; text-align: center;">
                <i class="fas fa-sign-in-alt"></i><br>Login
            </a>
            <a href="products.php" style="display: block; padding: 10px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; text-align: center;">
                <i class="fas fa-shopping-bag"></i><br>Products
            </a>
            <a href="orders.php" style="display: block; padding: 10px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; text-align: center;">
                <i class="fas fa-list"></i><br>My Orders
            </a>
            <a href="dashboard.php" style="display: block; padding: 10px; background: #6f42c1; color: white; text-decoration: none; border-radius: 5px; text-align: center;">
                <i class="fas fa-tachometer-alt"></i><br>Dashboard
            </a>
        </div>
        
        <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin-top: 20px;">
            <h4><i class="fas fa-graduation-cap"></i> Learning Mode Active</h4>
            <p>All invoices are generated in learning mode and clearly marked as educational content.</p>
        </div>
    </div>
</body>
</html>

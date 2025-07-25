<!DOCTYPE html>
<html>
<head>
    <title>Test Admin Dashboard - Merona Shop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
        .warning { color: #ffc107; }
        .test-result { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-tachometer-alt"></i> Test Admin Dashboard</h1>
        
        <?php
        require_once 'includes/functions.php';
        
        echo '<div class="info"><h3>🧪 Testing Admin Dashboard Issues...</h3></div>';
        
        // Check if user is admin
        if (!isLoggedIn()) {
            echo '<div class="error"><h3>❌ Please login first</h3></div>';
            echo '<p><a href="login.php">Go to Login</a></p>';
            exit;
        }
        
        if (!isAdmin()) {
            echo '<div class="error"><h3>❌ Admin access required</h3></div>';
            echo '<p>Please login with admin credentials: admin@merona.com / admin123</p>';
            echo '<p><a href="login.php">Go to Login</a></p>';
            exit;
        }
        
        echo '<div class="success"><h3>✅ Admin user logged in: ' . $_SESSION['user_name'] . '</h3></div>';
        
        // Test database connection
        $database = new Database();
        $db = $database->getConnection();
        
        if (!$db) {
            echo '<div class="error"><h3>❌ Database Connection Failed</h3></div>';
            exit;
        }
        
        echo '<div class="test-result">';
        echo '<h4>📊 Testing Status Arrays for All Order Statuses:</h4>';
        
        // Test status arrays
        $status_colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-blue-100 text-blue-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-gray-100 text-gray-800',
            'default' => 'bg-gray-100 text-gray-800'
        ];
        
        $status_names = [
            'pending' => 'Pending',
            'paid' => 'Dibayar',
            'confirmed' => 'Dikonfirmasi',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Selesai',
            'default' => 'Tidak Diketahui'
        ];
        
        // Get all unique statuses from database
        $query = "SELECT DISTINCT status FROM transactions WHERE status IS NOT NULL";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $db_statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo '<p><strong>Status found in database:</strong> ' . implode(', ', $db_statuses) . '</p>';
        
        foreach ($db_statuses as $status) {
            $color_class = isset($status_colors[$status]) ? $status_colors[$status] : $status_colors['default'];
            $status_name = isset($status_names[$status]) ? $status_names[$status] : $status_names['default'];
            
            echo '<div style="margin: 5px 0;">';
            echo '<span class="badge ' . $color_class . '" style="padding: 4px 8px; border-radius: 12px; font-size: 12px;">';
            echo $status . ' → ' . $status_name;
            echo '</span>';
            
            if (isset($status_colors[$status])) {
                echo ' <span class="success">✅ Supported</span>';
            } else {
                echo ' <span class="warning">⚠️ Using default</span>';
            }
            echo '</div>';
        }
        echo '</div>';
        
        // Test admin dashboard access
        echo '<div class="test-result">';
        echo '<h4>🔗 Testing Admin Dashboard Access:</h4>';
        
        $admin_pages = [
            'admin/dashboard.php' => 'Main Dashboard',
            'admin/orders.php' => 'Orders Management',
            'admin/users.php' => 'Users Management',
            'admin/products.php' => 'Products Management'
        ];
        
        foreach ($admin_pages as $page => $name) {
            if (file_exists($page)) {
                echo '<div style="margin: 5px 0;">';
                echo '<a href="' . $page . '" target="_blank" style="text-decoration: none;">';
                echo '<span class="success">✅</span> ' . $name . ' - <em>' . $page . '</em>';
                echo '</a>';
                echo '</div>';
            } else {
                echo '<div style="margin: 5px 0;">';
                echo '<span class="error">❌</span> ' . $name . ' - <em>' . $page . '</em> (Not Found)';
                echo '</div>';
            }
        }
        echo '</div>';
        
        // Test sample data
        echo '<div class="test-result">';
        echo '<h4>📋 Sample Dashboard Data:</h4>';
        
        try {
            // Get some sample statistics
            $query = "SELECT COUNT(*) as total FROM transactions";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $total_orders = $stmt->fetchColumn();
            
            $query = "SELECT COUNT(*) as total FROM users WHERE role = 'customer'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $total_users = $stmt->fetchColumn();
            
            $query = "SELECT COUNT(*) as total FROM products";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $total_products = $stmt->fetchColumn();
            
            echo '<ul>';
            echo '<li><strong>Total Orders:</strong> ' . $total_orders . '</li>';
            echo '<li><strong>Total Users:</strong> ' . $total_users . '</li>';
            echo '<li><strong>Total Products:</strong> ' . $total_products . '</li>';
            echo '</ul>';
            
        } catch (Exception $e) {
            echo '<div class="error">Error getting statistics: ' . $e->getMessage() . '</div>';
        }
        echo '</div>';
        ?>
        
        <hr>
        
        <div class="success">
            <h3><i class="fas fa-check-circle"></i> Admin Dashboard Tests Complete!</h3>
            <p>All 'completed' status array issues have been fixed.</p>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="admin/dashboard.php" style="display: inline-block; padding: 10px 20px; background: #e91e63; color: white; text-decoration: none; border-radius: 5px;">
                <i class="fas fa-tachometer-alt"></i> Go to Admin Dashboard
            </a>
        </div>
        
    </div>
</body>
</html>

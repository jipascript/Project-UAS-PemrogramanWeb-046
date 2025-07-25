<!DOCTYPE html>
<html>
<head>
    <title>Learning Mode Configuration - Merona Shop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
        .warning { color: #ffc107; }
        .feature { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #007bff; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-graduation-cap"></i> Learning Mode Configuration</h1>
        
        <?php
        require_once 'includes/functions.php';
        
        echo '<div class="info"><h3>📚 Learning Mode Features Configured:</h3></div>';
        
        $features = [
            '🛒 Add to Cart' => 'Products can be added to cart with instant notifications',
            '💳 Automatic Payment Processing' => 'Any uploaded payment proof automatically approves the order',  
            '📦 Order Status Updates' => 'Orders automatically move to "completed" status after payment',
            '🔄 Real-time Cart Updates' => 'Cart count updates with visual animations',
            '📱 Responsive Notifications' => 'Toast notifications for all user actions',
            '🎯 Demo Data Available' => 'Sample products and categories pre-loaded'
        ];
        
        foreach ($features as $feature => $description) {
            echo '<div class="feature">';
            echo '<strong>' . $feature . '</strong><br>';
            echo '<span style="color: #666;">' . $description . '</span>';
            echo '</div>';
        }
        ?>
        
        <hr>
        
        <h3><i class="fas fa-cogs"></i> Learning Mode Settings:</h3>
        
        <div class="feature">
            <strong>🔓 No Real Money Required</strong><br>
            <span style="color: #666;">Students can test payment flows without actual financial transactions</span>
        </div>
        
        <div class="feature">
            <strong>📸 Any Image Accepted</strong><br>
            <span style="color: #666;">Upload any image as payment proof - system will auto-approve</span>
        </div>
        
        <div class="feature">
            <strong>⚡ Instant Processing</strong><br>
            <span style="color: #666;">No waiting time for payment verification</span>
        </div>
        
        <div class="feature">
            <strong>📊 Full Order Flow</strong><br>
            <span style="color: #666;">Complete e-commerce experience from cart to completion</span>
        </div>
        
        <hr>
        
        <h3><i class="fas fa-user-graduate"></i> Student Instructions:</h3>
        
        <ol style="line-height: 2;">
            <li><strong>Login:</strong> Use demo credentials (customer@merona.com / customer)</li>
            <li><strong>Browse:</strong> Explore products and categories</li>
            <li><strong>Add to Cart:</strong> Click cart icons to add products</li>
            <li><strong>Checkout:</strong> Fill in shipping information</li>
            <li><strong>Payment:</strong> Upload any image as payment proof</li>
            <li><strong>Completion:</strong> Order automatically processed as completed</li>
        </ol>
        
        <hr>
        
        <h3><i class="fas fa-link"></i> Quick Access Links:</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin: 20px 0;">
            <a href="login.php" style="display: block; padding: 10px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; text-align: center;">
                <i class="fas fa-sign-in-alt"></i> Login Page
            </a>
            <a href="products.php" style="display: block; padding: 10px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; text-align: center;">
                <i class="fas fa-shopping-bag"></i> Products
            </a>
            <a href="dashboard.php" style="display: block; padding: 10px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; text-align: center;">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="cart.php" style="display: block; padding: 10px; background: #fd7e14; color: white; text-decoration: none; border-radius: 5px; text-align: center;">
                <i class="fas fa-shopping-cart"></i> Cart
            </a>
            <a href="test_cart.php" style="display: block; padding: 10px; background: #6f42c1; color: white; text-decoration: none; border-radius: 5px; text-align: center;">
                <i class="fas fa-flask"></i> Test Cart
            </a>
        </div>
        
        <hr>
        
        <div class="warning" style="padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;">
            <h4><i class="fas fa-exclamation-triangle"></i> Important Notes:</h4>
            <ul>
                <li>This is a <strong>learning environment</strong> - no real transactions occur</li>
                <li>All payment processing is simulated for educational purposes</li>
                <li>Students can safely test all e-commerce features</li>
                <li>No actual money should be transferred to the provided account numbers</li>
            </ul>
        </div>
        
        <hr>
        
        <div class="success" style="text-align: center; padding: 20px;">
            <h3><i class="fas fa-check-circle"></i> Learning Mode is Active!</h3>
            <p>The system is now configured for educational use with automated payment processing.</p>
        </div>
        
    </div>
</body>
</html>

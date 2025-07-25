<!DOCTYPE html>
<html>
<head>
    <title>Test Add to Cart - Merona Shop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
        .test-item { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
        button { background: #e91e63; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #ad1457; }
        .notification { position: fixed; top: 20px; right: 20px; padding: 15px; border-radius: 5px; z-index: 1000; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>🛒 Test Add to Cart Functionality</h1>
        
        <?php
        require_once 'includes/functions.php';
        
        // Check login status
        if (isLoggedIn()) {
            echo '<div class="success"><h3>✅ User is logged in: ' . $_SESSION['user_name'] . '</h3></div>';
        } else {
            echo '<div class="error"><h3>❌ User not logged in</h3></div>';
            echo '<p><a href="login.php">Go to Login</a></p>';
        }
        
        // Get some products for testing
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            try {
                $query = "SELECT id, name, price, stock FROM products WHERE status = 'active' LIMIT 3";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo '<h3>📋 Test Products:</h3>';
                foreach ($products as $product) {
                    echo '<div class="test-item">';
                    echo '<strong>' . htmlspecialchars($product['name']) . '</strong><br>';
                    echo 'Price: ' . formatPrice($product['price']) . '<br>';
                    echo 'Stock: ' . $product['stock'] . '<br>';
                    
                    if (isLoggedIn() && $product['stock'] > 0) {
                        echo '<button onclick="testAddToCart(' . $product['id'] . ')">';
                        echo '<i class="fas fa-cart-plus"></i> Add to Cart';
                        echo '</button>';
                    }
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="error">Database error: ' . $e->getMessage() . '</div>';
            }
        }
        ?>
        
        <hr>
        
        <h3>🔧 Test Results:</h3>
        <div id="testResults"></div>
        
        <hr>
        
        <h3>🔗 Quick Links:</h3>
        <p><a href="products.php">Go to Products Page</a></p>
        <p><a href="cart.php">View Cart</a></p>
        <?php if (isLoggedIn()): ?>
            <p><a href="dashboard.php">Dashboard</a></p>
        <?php endif; ?>
    </div>

    <script>
        // Notification system
        function showNotification(type, message) {
            const existing = document.querySelector('.notification');
            if (existing) {
                existing.remove();
            }
            
            const colors = {
                success: 'background: #28a745;',
                error: 'background: #dc3545;',
                info: 'background: #007bff;',
                warning: 'background: #ffc107; color: black;'
            };
            
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.style.cssText = colors[type] + 'color: white; padding: 15px; border-radius: 5px;';
            notification.innerHTML = `
                <div>
                    <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
                    ${message}
                    <button onclick="this.parentElement.parentElement.remove()" style="float: right; background: none; border: none; color: white; cursor: pointer;">×</button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        function testAddToCart(productId) {
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            button.disabled = true;
            
            // Log test start
            const testResults = document.getElementById('testResults');
            testResults.innerHTML += '<p>🔄 Testing add to cart for product ID: ' + productId + '</p>';
            
            fetch('ajax/add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', data.message);
                    testResults.innerHTML += '<p class="success">✅ SUCCESS: ' + data.message + '</p>';
                    
                    // Test cart count update
                    updateCartCount();
                } else {
                    showNotification('error', data.message);
                    testResults.innerHTML += '<p class="error">❌ ERROR: ' + data.message + '</p>';
                }
                
                // Reset button
                button.innerHTML = originalContent;
                button.disabled = false;
            })
            .catch(error => {
                const errorMsg = 'Network error: ' + error.message;
                showNotification('error', errorMsg);
                testResults.innerHTML += '<p class="error">❌ NETWORK ERROR: ' + error.message + '</p>';
                
                button.innerHTML = originalContent;
                button.disabled = false;
            });
        }

        function updateCartCount() {
            fetch('ajax/get-cart-count.php')
                .then(response => response.json())
                .then(data => {
                    const testResults = document.getElementById('testResults');
                    testResults.innerHTML += '<p class="info">ℹ️ Cart count updated: ' + data.count + ' items</p>';
                })
                .catch(error => {
                    const testResults = document.getElementById('testResults');
                    testResults.innerHTML += '<p class="error">❌ Failed to update cart count: ' + error.message + '</p>';
                });
        }

        // Test cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
    </script>
</body>
</html>

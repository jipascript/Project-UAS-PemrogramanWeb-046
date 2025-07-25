<?php
// Admin-specific functions
require_once __DIR__ . '/../../includes/functions.php';

// Admin authentication check
function requireAdmin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit();
    }
    
    if (!isAdmin()) {
        header('Location: ../index.php');
        exit();
    }
}

// Get admin statistics
function getAdminStats() {
    global $db;
    
    $stats = [];
    
    // Total products
    $stmt = $db->prepare("SELECT COUNT(*) FROM products");
    $stmt->execute();
    $stats['total_products'] = $stmt->fetchColumn();
    
    // Total users
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'customer'");
    $stmt->execute();
    $stats['total_users'] = $stmt->fetchColumn();
    
    // Total orders
    $stmt = $db->prepare("SELECT COUNT(*) FROM transactions");
    $stmt->execute();
    $stats['total_orders'] = $stmt->fetchColumn();
    
    // Total revenue
    $stmt = $db->prepare("SELECT SUM(total_amount) FROM transactions WHERE status = 'completed'");
    $stmt->execute();
    $stats['total_revenue'] = $stmt->fetchColumn() ?: 0;
    
    return $stats;
}

// Get recent orders
function getRecentOrders($limit = 10) {
    global $db;
    
    $stmt = $db->prepare("
        SELECT t.*, u.name as customer_name 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        ORDER BY t.created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get low stock products
function getLowStockProducts($threshold = 5) {
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM products WHERE stock <= ? ORDER BY stock ASC");
    $stmt->execute([$threshold]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$product_id = (int)$input['product_id'];
$quantity = (int)$input['quantity'];

if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Check if product exists and has enough stock
$query = "SELECT id, name, stock FROM products WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
    exit();
}

if ($product['stock'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Stok tidak mencukupi']);
    exit();
}

// Check if item already in cart
$query = "SELECT id, quantity FROM carts WHERE user_id = ? AND product_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id'], $product_id]);
$cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cart_item) {
    // Update quantity
    $new_quantity = $cart_item['quantity'] + $quantity;
    if ($new_quantity > $product['stock']) {
        echo json_encode(['success' => false, 'message' => 'Jumlah melebihi stok yang tersedia']);
        exit();
    }
    
    $query = "UPDATE carts SET quantity = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $success = $stmt->execute([$new_quantity, $cart_item['id']]);
} else {
    // Add new item
    $query = "INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    $success = $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
}

if ($success) {
    logActivity($_SESSION['user_id'], "Added product '{$product['name']}' to cart");
    echo json_encode(['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menambahkan ke keranjang']);
}
?>
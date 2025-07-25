<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$transaction_id = isset($_POST['transaction_id']) ? (int)$_POST['transaction_id'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

$allowed_statuses = ['confirmed', 'shipped', 'delivered', 'cancelled'];

if ($transaction_id <= 0 || !in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak valid']);
    exit();
}

try {
    // Get transaction details
    $query = "SELECT * FROM transactions WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$transaction_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$transaction) {
        echo json_encode(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
        exit();
    }
    
    // Update transaction status
    $query = "UPDATE transactions SET status = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$status, $transaction_id]);
    
    $status_names = [
        'confirmed' => 'dikonfirmasi',
        'shipped' => 'dikirim',
        'delivered' => 'selesai',
        'cancelled' => 'dibatalkan'
    ];
    
    logActivity($_SESSION['user_id'], "Updated transaction {$transaction['transaction_code']} status to {$status}");
    
    echo json_encode([
        'success' => true, 
        'message' => "Status pesanan berhasil diubah menjadi {$status_names[$status]}",
        'new_status' => $status
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
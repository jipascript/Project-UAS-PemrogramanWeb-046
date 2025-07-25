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

$payment_id = isset($_POST['payment_id']) ? (int)$_POST['payment_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($payment_id <= 0 || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak valid']);
    exit();
}

try {
    // Get payment details
    $query = "SELECT p.*, t.transaction_code, t.user_id 
              FROM payments p 
              JOIN transactions t ON p.transaction_id = t.id 
              WHERE p.id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$payment_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$payment) {
        echo json_encode(['success' => false, 'message' => 'Pembayaran tidak ditemukan']);
        exit();
    }
    
    $db->beginTransaction();
    
    if ($action == 'approve') {
        // Update payment status to approved
        $query = "UPDATE payments SET status = 'approved', verified_at = NOW(), verified_by = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $payment_id]);
        
        // Update transaction status to confirmed
        $query = "UPDATE transactions SET status = 'confirmed' WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$payment['transaction_id']]);
        
        $message = 'Pembayaran berhasil disetujui';
        logActivity($_SESSION['user_id'], "Approved payment for transaction {$payment['transaction_code']}");
        
    } else { // reject
        // Update payment status to rejected
        $query = "UPDATE payments SET status = 'rejected', verified_at = NOW(), verified_by = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $payment_id]);
        
        // Update transaction status back to pending
        $query = "UPDATE transactions SET status = 'pending' WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$payment['transaction_id']]);
        
        $message = 'Pembayaran ditolak';
        logActivity($_SESSION['user_id'], "Rejected payment for transaction {$payment['transaction_code']}");
    }
    
    $db->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => $message,
        'new_status' => $action == 'approve' ? 'approved' : 'rejected'
    ]);
    
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
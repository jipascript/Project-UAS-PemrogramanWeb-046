<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (empty($order_id) || empty($status)) {
        setFlashMessage('error', 'Data tidak lengkap');
        header('Location: orders.php');
        exit();
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        // Update status pesanan
        $query = "UPDATE transactions SET status = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$status, $order_id]);
        
        if ($stmt->rowCount() > 0) {
            // Log aktivitas
            logActivity($_SESSION['user_id'], "Mengubah status pesanan #$order_id menjadi $status");
            
            setFlashMessage('success', 'Status pesanan berhasil diperbarui');
        } else {
            setFlashMessage('error', 'Gagal memperbarui status pesanan');
        }
        
    } catch (PDOException $e) {
        setFlashMessage('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
    
    // Redirect kembali ke halaman detail pesanan atau daftar pesanan
    $redirect = $_POST['redirect'] ?? 'orders.php';
    header("Location: $redirect");
    exit();
}

// Jika bukan POST request, redirect ke orders.php
header('Location: orders.php');
exit();
?>
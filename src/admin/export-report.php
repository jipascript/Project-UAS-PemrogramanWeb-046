<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

requireLogin();
requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get filter parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$format = $_GET['format'] ?? 'csv';

try {
    // Query untuk mendapatkan data penjualan
    $query = "SELECT 
                t.id,
                t.transaction_code,
                u.name as customer_name,
                u.email as customer_email,
                t.total_amount,
                t.status,
                t.created_at,
                GROUP_CONCAT(CONCAT(p.name, ' (', ti.quantity, 'x)') SEPARATOR ', ') as items
              FROM transactions t
              LEFT JOIN users u ON t.user_id = u.id
              LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
              LEFT JOIN products p ON ti.product_id = p.id
              WHERE DATE(t.created_at) BETWEEN ? AND ?
              GROUP BY t.id
              ORDER BY t.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$start_date, $end_date]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($format === 'csv') {
        // Set headers untuk download CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="laporan_penjualan_' . $start_date . '_to_' . $end_date . '.csv"');
        
        // Buka output stream
        $output = fopen('php://output', 'w');
        
        // Tulis header CSV
        fputcsv($output, [
            'Kode Transaksi',
            'Nama Pelanggan',
            'Email Pelanggan',
            'Total',
            'Status',
            'Tanggal',
            'Item Produk'
        ]);
        
        // Tulis data
        foreach ($transactions as $transaction) {
            fputcsv($output, [
                $transaction['transaction_code'],
                $transaction['customer_name'],
                $transaction['customer_email'],
                $transaction['total_amount'],
                $transaction['status'],
                $transaction['created_at'],
                $transaction['items']
            ]);
        }
        
        fclose($output);
        exit();
        
    } elseif ($format === 'excel') {
        // Set headers untuk download Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="laporan_penjualan_' . $start_date . '_to_' . $end_date . '.xls"');
        
        echo '<table border="1">';
        echo '<tr>';
        echo '<th>Kode Transaksi</th>';
        echo '<th>Nama Pelanggan</th>';
        echo '<th>Email Pelanggan</th>';
        echo '<th>Total</th>';
        echo '<th>Status</th>';
        echo '<th>Tanggal</th>';
        echo '<th>Item Produk</th>';
        echo '</tr>';
        
        foreach ($transactions as $transaction) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($transaction['transaction_code']) . '</td>';
            echo '<td>' . htmlspecialchars($transaction['customer_name']) . '</td>';
            echo '<td>' . htmlspecialchars($transaction['customer_email']) . '</td>';
            echo '<td>' . number_format($transaction['total_amount'], 0, ',', '.') . '</td>';
            echo '<td>' . htmlspecialchars($transaction['status']) . '</td>';
            echo '<td>' . $transaction['created_at'] . '</td>';
            echo '<td>' . htmlspecialchars($transaction['items']) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        exit();
    }
    
} catch (PDOException $e) {
    setFlashMessage('error', 'Terjadi kesalahan: ' . $e->getMessage());
    header('Location: reports.php');
    exit();
}

// Jika format tidak didukung
setFlashMessage('error', 'Format ekspor tidak didukung');
header('Location: reports.php');
exit();
?>
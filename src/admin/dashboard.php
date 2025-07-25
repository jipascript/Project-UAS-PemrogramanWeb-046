<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get dashboard statistics
$stats = [];

// Total users (customers)
$query = "SELECT COUNT(*) as total FROM users WHERE role = 'customer'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_users'] = $stmt->fetchColumn();

// Total products
$query = "SELECT COUNT(*) as total FROM products";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_products'] = $stmt->fetchColumn();

// Total orders (using transactions table)
$query = "SELECT COUNT(*) as total FROM transactions";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_orders'] = $stmt->fetchColumn();

// Total revenue
$query = "SELECT COALESCE(SUM(total_amount), 0) as total FROM transactions WHERE status != 'cancelled'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_revenue'] = $stmt->fetchColumn();

// Pending orders
$query = "SELECT COUNT(*) as total FROM transactions WHERE status = 'pending'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['pending_orders'] = $stmt->fetchColumn();

// Pending payments
$query = "SELECT COUNT(*) as total FROM payments WHERE status = 'pending'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['pending_payments'] = $stmt->fetchColumn();

// Low stock products
$query = "SELECT COUNT(*) as total FROM products WHERE stock <= 5";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['low_stock'] = $stmt->fetchColumn();

// Recent orders
$query = "SELECT t.*, u.name as user_name 
          FROM transactions t 
          JOIN users u ON t.user_id = u.id 
          ORDER BY t.created_at DESC 
          LIMIT 10";
$stmt = $db->prepare($query);
$stmt->execute();
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent payments (empty for now since we don't have payment data)
$pending_payments = [];

// Monthly sales data for chart (empty for now)
$monthly_data = [];

$page_title = 'Dashboard Admin';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold font-heading">Dashboard Admin</h1>
        <p class="text-gray-600 mt-2">Selamat datang di panel administrasi Merona Shop</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Pengguna</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_users']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-box text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Produk</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_products']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-shopping-cart text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Pesanan</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_orders']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-money-bill-wave text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatPrice($stats['total_revenue']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pesanan Pending</p>
                    <p class="text-2xl font-bold text-orange-600"><?php echo $stats['pending_orders']; ?></p>
                </div>
                <i class="fas fa-clock text-orange-500 text-2xl"></i>
            </div>
            <a href="orders.php" class="text-orange-600 hover:text-orange-800 text-sm mt-2 inline-block">
                Lihat Detail →
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pembayaran Pending</p>
                    <p class="text-2xl font-bold text-red-600"><?php echo $stats['pending_payments']; ?></p>
                </div>
                <i class="fas fa-credit-card text-red-500 text-2xl"></i>
            </div>
            <a href="orders.php" class="text-red-600 hover:text-red-800 text-sm mt-2 inline-block">
                Verifikasi →
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Stok Rendah</p>
                    <p class="text-2xl font-bold text-yellow-600"><?php echo $stats['low_stock']; ?></p>
                </div>
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
            </div>
            <a href="products.php?filter=low_stock" class="text-yellow-600 hover:text-yellow-800 text-sm mt-2 inline-block">
                Lihat Produk →
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold">Pesanan Terbaru</h2>
                <a href="orders.php" class="text-pink-600 hover:text-pink-800 text-sm">
                    Lihat Semua →
                </a>
            </div>
            
            <?php if (empty($recent_orders)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Belum ada pesanan</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recent_orders as $order): ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-semibold text-sm"><?php echo htmlspecialchars($order['user_name']); ?></p>
                                <p class="text-gray-600 text-sm">#<?php echo htmlspecialchars($order['transaction_code']); ?></p>
                                <p class="text-gray-500 text-xs"><?php echo timeAgo($order['created_at']); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold"><?php echo formatPrice($order['total_amount']); ?></p>
                                <?php
                                $status_colors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-blue-100 text-blue-800',
                                    'confirmed' => 'bg-green-100 text-green-800',
                                    'shipped' => 'bg-purple-100 text-purple-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'completed' => 'bg-gray-100 text-gray-800', // Default for completed
                                    'default' => 'bg-gray-100 text-gray-800' // Fallback for any undefined status
                                ];
                                $status_names = [
                                    'pending' => 'Pending',
                                    'paid' => 'Dibayar',
                                    'confirmed' => 'Dikonfirmasi',
                                    'shipped' => 'Dikirim',
                                    'delivered' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                    'completed' => 'Selesai', // Default for completed
                                    'default' => 'Tidak Diketahui' // Fallback for any undefined status
                                ];
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs <?php echo isset($status_colors[$order['status']]) ? $status_colors[$order['status']] : $status_colors['default']; ?>">
                                    <?php echo isset($status_names[$order['status']]) ? $status_names[$order['status']] : $status_names['default']; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold">Pembayaran Pending</h2>
                <a href="orders.php" class="text-pink-600 hover:text-pink-800 text-sm">
                    Lihat Semua →
                </a>
            </div>
            
            <?php if (empty($pending_payments)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-credit-card text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Tidak ada pembayaran pending</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($pending_payments as $payment): ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-semibold text-sm"><?php echo htmlspecialchars($payment['user_name']); ?></p>
                                <p class="text-gray-600 text-sm">#<?php echo htmlspecialchars($payment['transaction_code']); ?></p>
                                <p class="text-gray-500 text-xs"><?php echo timeAgo($payment['created_at']); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold"><?php echo formatPrice($payment['amount']); ?></p>
                                <div class="flex space-x-2 mt-2">
                                    <button onclick="verifyPayment(<?php echo $payment['id']; ?>, 'approve')" 
                                            class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="verifyPayment(<?php echo $payment['id']; ?>, 'reject')" 
                                            class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h2 class="text-xl font-semibold mb-6">Aksi Cepat</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="add-product.php" class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-all duration-300 ease-in-out transform hover:scale-105">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center relative">
                    <i class="fas fa-plus-circle text-2xl text-green-500"></i>
                    <div class="absolute inset-0 rounded-full border-2 border-green-200 animate-pulse"></div>
                </div>
                <p class="font-semibold text-gray-700">Tambah Produk</p>
            </a>
            
            <a href="orders.php" class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-all duration-300 ease-in-out transform hover:scale-105">
                <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center relative">
                    <i class="fas fa-list-alt text-2xl text-blue-500"></i>
                    <div class="absolute inset-0 rounded-full border-2 border-blue-200 animate-pulse"></div>
                </div>
                <p class="font-semibold text-gray-700">Kelola Pesanan</p>
            </a>
            
            <a href="users.php" class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-all duration-300 ease-in-out transform hover:scale-105">
                <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center relative">
                    <i class="fas fa-users text-2xl text-purple-500"></i>
                    <div class="absolute inset-0 rounded-full border-2 border-purple-200 animate-pulse"></div>
                </div>
                <p class="font-semibold text-gray-700">Kelola Pengguna</p>
            </a>
            
            <a href="reports.php" class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-all duration-300 ease-in-out transform hover:scale-105">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center relative">
                    <i class="fas fa-chart-bar text-2xl text-gray-500"></i>
                    <div class="absolute inset-0 rounded-full border-2 border-gray-200 animate-pulse"></div>
                </div>
                <p class="font-semibold text-gray-700">Laporan</p>
            </a>
        </div>
    </div>
</div>

<script>
function verifyPayment(paymentId, action) {
    if (!confirm(`Apakah Anda yakin ingin ${action === 'approve' ? 'menyetujui' : 'menolak'} pembayaran ini?`)) {
        return;
    }
    
    fetch('ajax/verify-payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `payment_id=${paymentId}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'Terjadi kesalahan saat memproses permintaan');
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 
        'bg-red-100 text-red-800 border border-red-200'
    }`;
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>

<?php include 'includes/footer.php'; ?>
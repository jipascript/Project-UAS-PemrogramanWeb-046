<?php
require_once 'includes/functions.php';
requireLogin();

$page_title = 'Riwayat Pesanan';
include 'includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Build query
$where_conditions = ["t.user_id = ?"];
$params = [$_SESSION['user_id']];

if (!empty($status_filter)) {
    $where_conditions[] = "t.status = ?";
    $params[] = $status_filter;
}

$where_clause = implode(' AND ', $where_conditions);

// Get total count
$count_query = "SELECT COUNT(*) as total FROM transactions t WHERE $where_clause";
$stmt = $db->prepare($count_query);
$stmt->execute($params);
$total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_orders / $limit);

// Get orders
$query = "SELECT t.*, 
          COUNT(ti.id) as item_count,
          GROUP_CONCAT(p.name SEPARATOR ', ') as product_names
          FROM transactions t 
          LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
          LEFT JOIN products p ON ti.product_id = p.id
          WHERE $where_clause
          GROUP BY t.id
          ORDER BY t.created_at DESC
          LIMIT $limit OFFSET $offset";

$stmt = $db->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Status colors and labels
$status_colors = [
    'pending' => 'bg-yellow-100 text-yellow-800',
    'paid' => 'bg-blue-100 text-blue-800',
    'processing' => 'bg-purple-100 text-purple-800',
    'shipped' => 'bg-indigo-100 text-indigo-800',
    'completed' => 'bg-green-100 text-green-800',
    'cancelled' => 'bg-red-100 text-red-800'
];

$status_labels = [
    'pending' => 'Menunggu Pembayaran',
    'paid' => 'Sudah Dibayar',
    'processing' => 'Diproses',
    'shipped' => 'Dikirim',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];
?>

<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold font-heading">Riwayat Pesanan</h1>
            <p class="text-gray-600 mt-2">Kelola dan pantau pesanan Anda</p>
        </div>
        
        <div class="flex items-center space-x-4">
            <select onchange="filterByStatus(this.value)" 
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                <option value="">Semua Status</option>
                <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Menunggu Pembayaran</option>
                <option value="paid" <?php echo $status_filter == 'paid' ? 'selected' : ''; ?>>Sudah Dibayar</option>
                <option value="processing" <?php echo $status_filter == 'processing' ? 'selected' : ''; ?>>Diproses</option>
                <option value="shipped" <?php echo $status_filter == 'shipped' ? 'selected' : ''; ?>>Dikirim</option>
                <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Selesai</option>
                <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Dibatalkan</option>
            </select>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="text-center py-16">
            <i class="fas fa-shopping-bag text-gray-300 text-6xl mb-4"></i>
            <h2 class="text-2xl font-semibold text-gray-600 mb-4">Belum Ada Pesanan</h2>
            <p class="text-gray-500 mb-8">Anda belum memiliki riwayat pesanan</p>
            <a href="products.php" class="btn-primary px-8 py-3 rounded-lg text-white inline-block">
                Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <!-- Orders List -->
        <div class="space-y-6 mb-8">
            <?php foreach ($orders as $order): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Order Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">
                                    Pesanan #<?php echo htmlspecialchars($order['transaction_code']); ?>
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?>
                                </p>
                            </div>
                            
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?php echo $status_colors[$order['status']]; ?>">
                                    <?php echo $status_labels[$order['status']]; ?>
                                </span>
                                <p class="text-lg font-bold text-pink-500 mt-1">
                                    <?php echo formatPrice($order['total_amount']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Content -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Produk</label>
                                <p class="mt-1 text-sm">
                                    <?php 
                                    $product_names = explode(', ', $order['product_names']);
                                    if (count($product_names) > 2) {
                                        echo htmlspecialchars(implode(', ', array_slice($product_names, 0, 2))) . ' dan ' . (count($product_names) - 2) . ' lainnya';
                                    } else {
                                        echo htmlspecialchars($order['product_names']);
                                    }
                                    ?>
                                </p>
                                <p class="text-xs text-gray-500"><?php echo $order['item_count']; ?> item</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Penerima</label>
                                <p class="mt-1 text-sm"><?php echo htmlspecialchars($order['shipping_name']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pembayaran</label>
                                <p class="mt-1 text-sm capitalize">
                                    <?php echo str_replace('_', ' ', $order['payment_method']); ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex space-x-3">
                                <a href="order-detail.php?code=<?php echo $order['transaction_code']; ?>" 
                                   class="text-pink-500 hover:text-pink-600 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>
                                    Lihat Detail
                                </a>
                                
                                <?php if ($order['status'] == 'completed'): ?>
                                    <button onclick="downloadInvoice('<?php echo $order['transaction_code']; ?>')" 
                                            class="text-blue-500 hover:text-blue-600 text-sm font-medium">
                                        <i class="fas fa-download mr-1"></i>
                                        Invoice
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($order['status'] == 'pending'): ?>
                                    <button onclick="cancelOrder('<?php echo $order['transaction_code']; ?>')" 
                                            class="text-red-500 hover:text-red-600 text-sm font-medium">
                                        <i class="fas fa-times mr-1"></i>
                                        Batalkan
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($order['status'] == 'pending' && $order['payment_method'] != 'cod'): ?>
                                <a href="upload-payment.php?code=<?php echo $order['transaction_code']; ?>" 
                                   class="btn-primary px-4 py-2 rounded-md text-white text-sm">
                                    <i class="fas fa-upload mr-1"></i>
                                    Upload Pembayaran
                                </a>
                            <?php elseif ($order['status'] == 'completed'): ?>
                                <button onclick="reorderItems('<?php echo $order['transaction_code']; ?>')" 
                                        class="border border-pink-500 text-pink-500 px-4 py-2 rounded-md hover:bg-pink-50 text-sm">
                                    <i class="fas fa-redo mr-1"></i>
                                    Pesan Lagi
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="flex justify-center">
                <nav class="flex space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                           class="px-3 py-2 border border-gray-300 rounded-md <?php echo $i == $page ? 'bg-pink-500 text-white' : 'hover:bg-gray-50'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function filterByStatus(status) {
    const url = new URL(window.location);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
}

function downloadInvoice(transactionCode) {
    window.open('invoice.php?code=' + transactionCode, '_blank');
}

function cancelOrder(transactionCode) {
    if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
        fetch('ajax/cancel-order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                transaction_code: transactionCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            showNotification('error', 'Terjadi kesalahan');
        });
    }
}

function reorderItems(transactionCode) {
    if (confirm('Tambahkan semua produk dari pesanan ini ke keranjang?')) {
        fetch('ajax/reorder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                transaction_code: transactionCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message);
                updateCartCount();
                setTimeout(() => {
                    window.location.href = 'cart.php';
                }, 2000);
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            showNotification('error', 'Terjadi kesalahan');
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>
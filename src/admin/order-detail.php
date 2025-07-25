<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get order ID
$order_id = intval($_GET['id'] ?? 0);
if ($order_id <= 0) {
    setFlashMessage('error', 'ID pesanan tidak valid');
    header('Location: orders.php');
    exit();
}

// Get order data with user information
$query = "SELECT t.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
          FROM transactions t 
          LEFT JOIN users u ON t.user_id = u.id 
          WHERE t.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    setFlashMessage('error', 'Pesanan tidak ditemukan');
    header('Location: orders.php');
    exit();
}

// Get order items
$query = "SELECT ti.*, p.name as product_name, p.image_url 
          FROM transaction_items ti 
          LEFT JOIN products p ON ti.product_id = p.id 
          WHERE ti.transaction_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if ($_POST && isset($_POST['update_status'])) {
    $new_status = $_POST['status'] ?? '';
    $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    
    if (in_array($new_status, $valid_statuses)) {
        try {
            $query = "UPDATE transactions SET status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$new_status, $order_id]);
            
            setFlashMessage('success', 'Status pesanan berhasil diperbarui');
            header('Location: order-detail.php?id=' . $order_id);
            exit();
        } catch (Exception $e) {
            setFlashMessage('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
}

$page_title = 'Detail Pesanan';
include 'includes/header.php';

// Status color mapping
$status_colors = [
    'pending' => 'bg-yellow-100 text-yellow-800',
    'processing' => 'bg-blue-100 text-blue-800',
    'shipped' => 'bg-purple-100 text-purple-800',
    'delivered' => 'bg-green-100 text-green-800',
    'cancelled' => 'bg-red-100 text-red-800'
];

$status_labels = [
    'pending' => 'Menunggu',
    'processing' => 'Diproses',
    'shipped' => 'Dikirim',
    'delivered' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center">
            <a href="orders.php" class="text-gray-600 hover:text-gray-800 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold font-heading">Detail Pesanan</h1>
                <p class="text-gray-600 mt-2">Kode: <?php echo htmlspecialchars($order['transaction_code']); ?></p>
            </div>
        </div>
        
        <!-- Status Badge -->
        <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $status_colors[$order['status']] ?? 'bg-gray-100 text-gray-800'; ?>">
            <?php echo $status_labels[$order['status']] ?? ucfirst($order['status']); ?>
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Item Pesanan</h3>
                <div class="space-y-4">
                    <?php foreach ($order_items as $item): ?>
                    <div class="flex items-center space-x-4 p-4 border rounded-lg">
                        <?php if (!empty($item['image_url'])): ?>
                        <img src="../<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="Product image" 
                             class="w-16 h-16 object-cover rounded-lg">
                        <?php else: ?>
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex-1">
                            <h4 class="font-medium"><?php echo htmlspecialchars($item['product_name'] ?? 'Produk Tidak Ditemukan'); ?></h4>
                            <p class="text-sm text-gray-600">
                                Harga: Rp <?php echo number_format($item['price'], 0, ',', '.'); ?> × <?php echo $item['quantity']; ?>
                            </p>
                        </div>
                        
                        <div class="text-right">
                            <p class="font-medium">Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Order Summary -->
                <div class="border-t pt-4 mt-4">
                    <div class="flex justify-between items-center text-lg font-semibold">
                        <span>Total Pesanan:</span>
                        <span>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Informasi Pembayaran</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Metode Pembayaran</p>
                        <p class="font-medium"><?php echo htmlspecialchars($order['payment_method'] ?? 'Belum ditentukan'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status Pembayaran</p>
                        <p class="font-medium"><?php echo htmlspecialchars($order['payment_status'] ?? 'Belum dibayar'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Informasi Pelanggan</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Nama</p>
                        <p class="font-medium"><?php echo htmlspecialchars($order['user_name'] ?? 'Guest'); ?></p>
                    </div>
                    <?php if (!empty($order['user_email'])): ?>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium"><?php echo htmlspecialchars($order['user_email']); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($order['user_phone'])): ?>
                    <div>
                        <p class="text-sm text-gray-600">Telepon</p>
                        <p class="font-medium"><?php echo htmlspecialchars($order['user_phone']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Shipping Information -->
            <?php if (!empty($order['shipping_address'])): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Alamat Pengiriman</h3>
                <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- Order Timeline -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Timeline Pesanan</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Dibuat</p>
                        <p class="font-medium"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Terakhir Diperbarui</p>
                        <p class="font-medium"><?php echo date('d/m/Y H:i', strtotime($order['updated_at'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Perbarui Status</h3>
                <form method="POST">
                    <div class="space-y-4">
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Diproses</option>
                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Dikirim</option>
                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Selesai</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Dibatalkan</option>
                        </select>
                        <button type="submit" name="update_status" 
                                class="w-full px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Perbarui Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
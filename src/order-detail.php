<?php
require_once 'includes/functions.php';
requireLogin();

$transaction_code = isset($_GET['code']) ? trim($_GET['code']) : '';

if (empty($transaction_code)) {
    header('Location: orders.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get transaction details
$query = "SELECT t.*, u.name as user_name, u.email as user_email
          FROM transactions t 
          JOIN users u ON t.user_id = u.id 
          WHERE t.transaction_code = ? AND t.user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$transaction_code, $_SESSION['user_id']]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    setFlashMessage('error', 'Pesanan tidak ditemukan');
    header('Location: orders.php');
    exit();
}

// Get transaction items
$query = "SELECT ti.*, p.name as product_name, p.image as product_image
          FROM transaction_items ti 
          JOIN products p ON ti.product_id = p.id 
          WHERE ti.transaction_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$transaction['id']]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get payment info if exists
$query = "SELECT * FROM payments WHERE transaction_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$transaction['id']]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

$page_title = 'Detail Pesanan #' . $transaction['transaction_code'];
include 'includes/header.php';

// Status colors
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
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold font-heading">Detail Pesanan</h1>
            <p class="text-gray-600 mt-2">Pesanan #<?php echo htmlspecialchars($transaction['transaction_code']); ?></p>
        </div>
        
        <div class="text-right">
            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?php echo $status_colors[$transaction['status']]; ?>">
                <?php echo $status_labels[$transaction['status']]; ?>
            </span>
            <?php if ($transaction['status'] == 'completed'): ?>
                <div class="mt-2 inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                    <i class="fas fa-graduation-cap mr-1"></i>
                    Learning Mode
                </div>
            <?php endif; ?>
            <p class="text-sm text-gray-500 mt-1">
                <?php echo date('d M Y, H:i', strtotime($transaction['created_at'])); ?>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">Produk yang Dipesan</h2>
                </div>
                
                <div class="divide-y divide-gray-200">
                    <?php foreach ($items as $item): ?>
                        <div class="p-6 flex items-center space-x-4">
                            <img src="<?php echo $item['product_image'] ? 'uploads/' . $item['product_image'] : 'https://picsum.photos/80/80?random=' . $item['product_id']; ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                 class="w-16 h-16 object-cover rounded-lg">
                            
                            <div class="flex-1">
                                <h3 class="font-semibold"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                <p class="text-gray-500">Qty: <?php echo $item['quantity']; ?></p>
                                <p class="text-pink-500 font-semibold"><?php echo formatPrice($item['price']); ?></p>
                            </div>
                            
                            <div class="text-right">
                                <p class="font-bold"><?php echo formatPrice($item['price'] * $item['quantity']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Informasi Pengiriman</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Penerima</label>
                        <p class="mt-1"><?php echo htmlspecialchars($transaction['shipping_name']); ?></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <p class="mt-1"><?php echo htmlspecialchars($transaction['shipping_phone']); ?></p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                    <p class="mt-1"><?php echo nl2br(htmlspecialchars($transaction['shipping_address'])); ?></p>
                    <p class="mt-1 text-gray-600">
                        <?php echo htmlspecialchars($transaction['shipping_city']); ?>, 
                        <?php echo htmlspecialchars($transaction['shipping_postal_code']); ?>
                    </p>
                </div>
                
                <?php if (!empty($transaction['notes'])): ?>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Catatan</label>
                        <p class="mt-1 text-gray-600"><?php echo nl2br(htmlspecialchars($transaction['notes'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Payment Information -->
            <?php if ($transaction['status'] == 'pending' && $transaction['payment_method'] != 'cod'): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-yellow-800 mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Menunggu Pembayaran
                    </h2>
                    
                    <p class="text-yellow-700 mb-4">
                        Silakan lakukan pembayaran sesuai dengan metode yang dipilih untuk melanjutkan pesanan Anda.
                    </p>
                    
                    <?php if ($transaction['payment_method'] == 'bank_transfer'): ?>
                        <div class="bg-white rounded-lg p-4">
                            <h3 class="font-semibold mb-3">Transfer Bank</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Bank BCA:</span>
                                    <span class="font-mono">1234567890 (Merona Shop)</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Bank BNI:</span>
                                    <span class="font-mono">0987654321 (Merona Shop)</span>
                                </div>
                                <div class="flex justify-between font-semibold">
                                    <span>Total Transfer:</span>
                                    <span class="text-pink-500"><?php echo formatPrice($transaction['total_amount']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($transaction['payment_method'] == 'e_wallet'): ?>
                        <div class="bg-white rounded-lg p-4">
                            <h3 class="font-semibold mb-3">E-Wallet</h3>
                            <p class="text-sm text-gray-600 mb-2">
                                Scan QR Code atau transfer ke nomor berikut:
                            </p>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>OVO/GoPay/DANA:</span>
                                    <span class="font-mono">081234567890</span>
                                </div>
                                <div class="flex justify-between font-semibold">
                                    <span>Total Transfer:</span>
                                    <span class="text-pink-500"><?php echo formatPrice($transaction['total_amount']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-4 flex space-x-4">
                        <button onclick="uploadPaymentProof()" class="btn-primary px-6 py-2 rounded-md text-white">
                            <i class="fas fa-upload mr-2"></i>
                            Upload Bukti Pembayaran
                        </button>
                        
                        <button onclick="checkPaymentStatus()" class="border border-gray-300 px-6 py-2 rounded-md hover:bg-gray-50">
                            <i class="fas fa-sync mr-2"></i>
                            Cek Status
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Payment Proof -->
            <?php if ($payment): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Bukti Pembayaran</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                            <p class="mt-1 capitalize"><?php echo str_replace('_', ' ', $payment['payment_method']); ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                            <p class="mt-1 font-semibold text-pink-500"><?php echo formatPrice($payment['amount']); ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Upload</label>
                            <p class="mt-1"><?php echo date('d M Y, H:i', strtotime($payment['created_at'])); ?></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold mt-1
                                <?php echo $payment['status'] == 'verified' ? 'bg-green-100 text-green-800' : 
                                    ($payment['status'] == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                <?php echo ucfirst($payment['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($payment['proof_image']): ?>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Transfer</label>
                            <img src="uploads/payments/<?php echo $payment['proof_image']; ?>" 
                                 alt="Bukti Pembayaran" 
                                 class="max-w-xs rounded-lg shadow-md cursor-pointer"
                                 onclick="showImageModal(this.src)">
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                <h2 class="text-xl font-semibold mb-4">Ringkasan Pesanan</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span><?php echo formatPrice($transaction['total_amount']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span class="text-green-500">Gratis</span>
                    </div>
                    <hr>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-pink-500"><?php echo formatPrice($transaction['total_amount']); ?></span>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="text-sm">
                        <span class="text-gray-600">Metode Pembayaran:</span>
                        <span class="font-semibold capitalize ml-2">
                            <?php echo str_replace('_', ' ', $transaction['payment_method']); ?>
                        </span>
                    </div>
                    
                    <div class="text-sm">
                        <span class="text-gray-600">Status:</span>
                        <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold <?php echo $status_colors[$transaction['status']]; ?>">
                            <?php echo $status_labels[$transaction['status']]; ?>
                        </span>
                    </div>
                </div>
                
                <div class="mt-6 space-y-3">
                    <a href="orders.php" class="w-full border border-gray-300 py-2 rounded-md text-center block hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Pesanan
                    </a>
                    
                    <?php if ($transaction['status'] == 'completed'): ?>
                        <button onclick="downloadInvoice()" class="w-full btn-primary py-2 rounded-md text-white">
                            <i class="fas fa-download mr-2"></i>
                            Download Invoice
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($transaction['status'] == 'pending'): ?>
                        <button onclick="cancelOrder()" class="w-full bg-red-500 text-white py-2 rounded-md hover:bg-red-600">
                            <i class="fas fa-times mr-2"></i>
                            Batalkan Pesanan
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="Bukti Pembayaran" class="max-w-full max-h-full rounded-lg">
    </div>
</div>

<script>
function showImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

function uploadPaymentProof() {
    // Redirect to payment upload page
    window.location.href = 'upload-payment.php?code=<?php echo $transaction['transaction_code']; ?>';
}

function checkPaymentStatus() {
    // Reload page to check latest status
    window.location.reload();
}

function downloadInvoice() {
    window.open('invoice.php?code=<?php echo $transaction['transaction_code']; ?>', '_blank');
}

function cancelOrder() {
    if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
        fetch('ajax/cancel-order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                transaction_code: '<?php echo $transaction['transaction_code']; ?>'
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

// Close modal when clicking outside
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
</script>

<?php include 'includes/footer.php'; ?>
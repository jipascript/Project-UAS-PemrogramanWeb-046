<?php
ob_start(); // Start output buffering
require_once 'includes/functions.php';
requireLogin();

$database = new Database();
$db = $database->getConnection();

// Get cart items
$query = "SELECT c.*, p.name, p.price, p.image, p.stock 
          FROM carts c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ? 
          ORDER BY c.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    ob_end_clean(); // Clear any output
    header('Location: cart.php');
    exit();
}

// Calculate total
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping_cost = 0; // Free shipping
$total = $subtotal + $shipping_cost;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_name = trim($_POST['shipping_name']);
    $shipping_phone = trim($_POST['shipping_phone']);
    $shipping_address = trim($_POST['shipping_address']);
    $shipping_city = trim($_POST['shipping_city']);
    $shipping_postal_code = trim($_POST['shipping_postal_code']);
    $payment_method = $_POST['payment_method'];
    $notes = trim($_POST['notes']);
    
    $errors = [];
    
    if (empty($shipping_name)) $errors[] = 'Nama penerima harus diisi';
    if (empty($shipping_phone)) $errors[] = 'Nomor telepon harus diisi';
    if (empty($shipping_address)) $errors[] = 'Alamat lengkap harus diisi';
    if (empty($shipping_city)) $errors[] = 'Kota harus diisi';
    if (empty($shipping_postal_code)) $errors[] = 'Kode pos harus diisi';
    if (empty($payment_method)) $errors[] = 'Metode pembayaran harus dipilih';
    
    if (empty($errors)) {
        try {
            $db->beginTransaction();
            
            // Create transaction
            $transaction_code = 'TRX' . date('Ymd') . rand(1000, 9999);
            
            $query = "INSERT INTO transactions (user_id, transaction_code, total_amount, shipping_name, shipping_phone, shipping_address, shipping_city, shipping_postal_code, payment_method, notes, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = $db->prepare($query);
            $stmt->execute([
                $_SESSION['user_id'], 
                $transaction_code, 
                $total, 
                $shipping_name, 
                $shipping_phone, 
                $shipping_address, 
                $shipping_city, 
                $shipping_postal_code, 
                $payment_method, 
                $notes
            ]);
            
            $transaction_id = $db->lastInsertId();
            
            // Add transaction items
            foreach ($cart_items as $item) {
                $query = "INSERT INTO transaction_items (transaction_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$transaction_id, $item['product_id'], $item['quantity'], $item['price']]);
                
                // Update product stock
                $query = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            // Clear cart
            $query = "DELETE FROM carts WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$_SESSION['user_id']]);
            
            // Log activity
            logActivity($_SESSION['user_id'], "Created order $transaction_code");
            
            $db->commit();
            
            setFlashMessage('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
            
            // Try header redirect first, fallback to JavaScript
            if (!headers_sent()) {
                ob_end_clean(); // Clear any output
                header("Location: order-detail.php?code=$transaction_code");
                exit();
            } else {
                // Fallback to JavaScript redirect
                echo "<script>window.location.href='order-detail.php?code=$transaction_code';</script>";
                exit();
            }
            
        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage();
            // For debugging - remove in production
            error_log('Checkout error: ' . $e->getMessage());
        }
    }
}

// Get user data for form
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Now it's safe to include header since no more redirects will happen
$page_title = 'Checkout';
ob_end_clean(); // Clear any output buffer content
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-8">
        <h1 class="text-3xl font-bold font-heading">Checkout</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Shipping Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-6">Informasi Pengiriman</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Penerima *</label>
                        <input type="text" name="shipping_name" 
                               value="<?php echo isset($_POST['shipping_name']) ? htmlspecialchars($_POST['shipping_name']) : htmlspecialchars($user['name']); ?>" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon *</label>
                        <input type="tel" name="shipping_phone" 
                               value="<?php echo isset($_POST['shipping_phone']) ? htmlspecialchars($_POST['shipping_phone']) : htmlspecialchars($user['phone'] ?? ''); ?>" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap *</label>
                    <textarea name="shipping_address" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                              placeholder="Jalan, nomor rumah, RT/RW, kelurahan, kecamatan"><?php echo isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kota *</label>
                        <input type="text" name="shipping_city" 
                               value="<?php echo isset($_POST['shipping_city']) ? htmlspecialchars($_POST['shipping_city']) : ''; ?>" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos *</label>
                        <input type="text" name="shipping_postal_code" 
                               value="<?php echo isset($_POST['shipping_postal_code']) ? htmlspecialchars($_POST['shipping_postal_code']) : ''; ?>" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-6">Metode Pembayaran</h2>
                
                <div class="space-y-4">
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="bank_transfer" 
                               <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank_transfer') ? 'checked' : ''; ?>
                               class="mr-3">
                        <div class="flex-1">
                            <div class="font-semibold">Transfer Bank</div>
                            <div class="text-sm text-gray-500">BCA, BNI, BRI, Mandiri</div>
                        </div>
                        <i class="fas fa-university text-gray-400"></i>
                    </label>
                    
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="e_wallet" 
                               <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'e_wallet') ? 'checked' : ''; ?>
                               class="mr-3">
                        <div class="flex-1">
                            <div class="font-semibold">E-Wallet</div>
                            <div class="text-sm text-gray-500">OVO, GoPay, DANA</div>
                        </div>
                        <i class="fas fa-mobile-alt text-gray-400"></i>
                    </label>
                    
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="cod" 
                               <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'cod') ? 'checked' : ''; ?>
                               class="mr-3">
                        <div class="flex-1">
                            <div class="font-semibold">Cash on Delivery (COD)</div>
                            <div class="text-sm text-gray-500">Bayar saat barang diterima</div>
                        </div>
                        <i class="fas fa-money-bill-wave text-gray-400"></i>
                    </label>
                </div>
            </div>

            <!-- Order Notes -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-6">Catatan Pesanan</h2>
                <textarea name="notes" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                          placeholder="Catatan tambahan untuk pesanan (opsional)"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                <h2 class="text-xl font-semibold mb-6">Ringkasan Pesanan</h2>
                
                <!-- Order Items -->
                <div class="space-y-4 mb-6">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="flex items-center space-x-3">
                            <img src="<?php echo $item['image'] ? 'uploads/' . $item['image'] : 'https://picsum.photos/60/60?random=' . $item['product_id']; ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="w-12 h-12 object-cover rounded">
                            <div class="flex-1">
                                <h4 class="font-medium text-sm"><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p class="text-sm text-gray-500">Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                            <span class="font-semibold">
                                <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <hr class="mb-4">
                
                <!-- Price Breakdown -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span><?php echo formatPrice($subtotal); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span class="text-green-500">Gratis</span>
                    </div>
                    <hr>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-pink-500"><?php echo formatPrice($total); ?></span>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="w-full btn-primary py-3 rounded-lg text-white">
                    <i class="fas fa-credit-card mr-2"></i>
                    Buat Pesanan
                </button>
                
                <div class="mt-4 text-center">
                    <a href="cart.php" class="text-pink-500 hover:text-pink-600 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali ke Keranjang
                    </a>
                </div>
                
                <!-- Security Info -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                        <span>Transaksi Anda aman dan terlindungi</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
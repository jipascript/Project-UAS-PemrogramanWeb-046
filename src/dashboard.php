<?php
require_once 'includes/functions.php';
requireLogin();

$database = new Database();
$db = $database->getConnection();

// Get user statistics
$user_id = $_SESSION['user_id'];

// Get total orders
$query = "SELECT COUNT(*) as total FROM transactions WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$total_orders = $stmt->fetchColumn();

// Get pending orders
$query = "SELECT COUNT(*) as total FROM transactions WHERE user_id = ? AND status = 'pending'";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$pending_orders = $stmt->fetchColumn();

// Get completed orders
$query = "SELECT COUNT(*) as total FROM transactions WHERE user_id = ? AND status = 'completed'";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$completed_orders = $stmt->fetchColumn();

// Get cart items count
$cart_count = getCartCount($user_id);

// Get total spent
$query = "SELECT COALESCE(SUM(total_amount), 0) as total FROM transactions WHERE user_id = ? AND status != 'cancelled'";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$total_spent = $stmt->fetchColumn();

// Get recent orders
$query = "SELECT t.*, 
          (SELECT COUNT(*) FROM transaction_items ti WHERE ti.transaction_id = t.id) as item_count
          FROM transactions t 
          WHERE t.user_id = ? 
          ORDER BY t.created_at DESC 
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get favorite products (most ordered)
$query = "SELECT p.*, c.name as category_name, SUM(ti.quantity) as total_ordered
          FROM products p
          JOIN transaction_items ti ON p.id = ti.product_id
          JOIN transactions t ON ti.transaction_id = t.id
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE t.user_id = ?
          GROUP BY p.id
          ORDER BY total_ordered DESC
          LIMIT 4";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$favorite_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Dashboard';
include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-pink-500 to-purple-600 text-white">
        <div class="container mx-auto px-4 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold font-heading">Dashboard</h1>
                    <p class="text-pink-100 mt-2">Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="flex items-center space-x-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold"><?php echo $total_orders; ?></div>
                                <div class="text-sm text-pink-100">Total Pesanan</div>
                            </div>
                            <div class="w-px h-12 bg-white bg-opacity-30"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold"><?php echo formatPrice($total_spent); ?></div>
                                <div class="text-sm text-pink-100">Total Belanja</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Keranjang</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $cart_count; ?></p>
                    </div>
                </div>
                <a href="cart.php" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                    Lihat Keranjang →
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fas fa-clock text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Pesanan Pending</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $pending_orders; ?></p>
                    </div>
                </div>
                <a href="orders.php?status=pending" class="text-orange-600 hover:text-orange-800 text-sm mt-2 inline-block">
                    Lihat Detail →
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Pesanan Selesai</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $completed_orders; ?></p>
                    </div>
                </div>
                <a href="orders.php?status=completed" class="text-green-600 hover:text-green-800 text-sm mt-2 inline-block">
                    Lihat Riwayat →
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-user text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Profil</p>
                        <p class="text-lg font-bold text-gray-900">Kelola</p>
                    </div>
                </div>
                <a href="profile.php" class="text-purple-600 hover:text-purple-800 text-sm mt-2 inline-block">
                    Edit Profil →
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Aksi Cepat</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="products.php" class="flex flex-col items-center p-4 bg-gradient-to-br from-pink-50 to-purple-50 rounded-lg hover:from-pink-100 hover:to-purple-100 transition-all duration-300 group">
                    <div class="p-3 bg-pink-500 rounded-full mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-shopping-bag text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Belanja Sekarang</span>
                </a>
                
                <a href="orders.php" class="flex flex-col items-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 group">
                    <div class="p-3 bg-blue-500 rounded-full mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-list-alt text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Riwayat Pesanan</span>
                </a>
                
                <a href="cart.php" class="flex flex-col items-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg hover:from-green-100 hover:to-emerald-100 transition-all duration-300 group">
                    <div class="p-3 bg-green-500 rounded-full mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-shopping-cart text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Keranjang Belanja</span>
                </a>
                
                <a href="profile.php" class="flex flex-col items-center p-4 bg-gradient-to-br from-orange-50 to-red-50 rounded-lg hover:from-orange-100 hover:to-red-100 transition-all duration-300 group">
                    <div class="p-3 bg-orange-500 rounded-full mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-cog text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Pengaturan</span>
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
                        <p class="text-gray-500 mb-4">Belum ada pesanan</p>
                        <a href="products.php" class="btn-primary">
                            Mulai Belanja
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recent_orders as $order): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="font-semibold text-sm">#<?php echo htmlspecialchars($order['transaction_code']); ?></p>
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo getStatusBadgeClass($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm"><?php echo $order['item_count']; ?> item - <?php echo formatPrice($order['total_amount']); ?></p>
                                    <p class="text-gray-500 text-xs"><?php echo timeAgo($order['created_at']); ?></p>
                                </div>
                                <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="text-pink-600 hover:text-pink-800 ml-4">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Favorite Products -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold">Produk Favorit</h2>
                    <a href="products.php" class="text-pink-600 hover:text-pink-800 text-sm">
                        Lihat Semua →
                    </a>
                </div>
                
                <?php if (empty($favorite_products)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-heart text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 mb-4">Belum ada produk favorit</p>
                        <a href="products.php" class="btn-primary">
                            Jelajahi Produk
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($favorite_products as $product): ?>
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="aspect-square bg-gray-100 rounded-lg mb-3 overflow-hidden">
                                    <?php if ($product['image']): ?>
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <h3 class="font-medium text-sm mb-1 line-clamp-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="text-pink-600 font-semibold text-sm"><?php echo formatPrice($product['price']); ?></p>
                                <p class="text-gray-500 text-xs">Dibeli <?php echo $product['total_ordered']; ?>x</p>
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" 
                                   class="block mt-2 text-center bg-pink-500 text-white py-1 px-3 rounded text-xs hover:bg-pink-600 transition-colors">
                                    Lihat Detail
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
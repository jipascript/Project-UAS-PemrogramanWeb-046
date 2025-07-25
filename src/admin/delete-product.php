<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get product ID
$product_id = intval($_GET['id'] ?? 0);
if ($product_id <= 0) {
    setFlashMessage('error', 'ID produk tidak valid');
    header('Location: products.php');
    exit();
}

// Get product data
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    setFlashMessage('error', 'Produk tidak ditemukan');
    header('Location: products.php');
    exit();
}

if ($_POST && isset($_POST['confirm_delete'])) {
    try {
        // Delete product image if exists
        if (!empty($product['image_url']) && file_exists('../' . $product['image_url'])) {
            unlink('../' . $product['image_url']);
        }
        
        // Delete product from database
        $query = "DELETE FROM products WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$product_id]);
        
        setFlashMessage('success', 'Produk berhasil dihapus');
        header('Location: products.php');
        exit();
    } catch (Exception $e) {
        setFlashMessage('error', 'Gagal menghapus produk: ' . $e->getMessage());
    }
}

$page_title = 'Hapus Produk';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="products.php" class="text-gray-600 hover:text-gray-800 mr-4">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold font-heading">Hapus Produk</h1>
            <p class="text-gray-600 mt-2">Konfirmasi penghapusan produk</p>
        </div>
    </div>

    <!-- Confirmation Card -->
    <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Penghapusan</h3>
            <p class="text-sm text-gray-500">
                Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        <!-- Product Info -->
        <div class="border rounded-lg p-4 mb-6 bg-gray-50">
            <div class="flex items-center space-x-4">
                <?php if (!empty($product['image_url'])): ?>
                <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="Product image" 
                     class="w-16 h-16 object-cover rounded-lg">
                <?php else: ?>
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                    <i class="fas fa-image text-gray-400"></i>
                </div>
                <?php endif; ?>
                
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p class="text-sm text-gray-500">Harga: Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                    <p class="text-sm text-gray-500">Stok: <?php echo $product['stock']; ?></p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <form method="POST" class="flex justify-center space-x-4">
            <a href="products.php" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit" name="confirm_delete" value="1"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>Ya, Hapus Produk
            </button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
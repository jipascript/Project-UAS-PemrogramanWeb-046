<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get category ID
$category_id = intval($_GET['id'] ?? 0);
if ($category_id <= 0) {
    setFlashMessage('error', 'ID kategori tidak valid');
    header('Location: categories.php');
    exit();
}

// Get category data
$query = "SELECT * FROM categories WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    setFlashMessage('error', 'Kategori tidak ditemukan');
    header('Location: categories.php');
    exit();
}

// Check if category has products
$query = "SELECT COUNT(*) as product_count FROM products WHERE category_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$category_id]);
$product_count = $stmt->fetch(PDO::FETCH_ASSOC)['product_count'];

if ($_POST && isset($_POST['confirm_delete'])) {
    if ($product_count > 0) {
        setFlashMessage('error', 'Tidak dapat menghapus kategori yang masih memiliki produk');
        header('Location: categories.php');
        exit();
    }
    
    try {
        // Delete category from database
        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$category_id]);
        
        setFlashMessage('success', 'Kategori berhasil dihapus');
        header('Location: categories.php');
        exit();
    } catch (Exception $e) {
        setFlashMessage('error', 'Gagal menghapus kategori: ' . $e->getMessage());
    }
}

$page_title = 'Hapus Kategori';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="categories.php" class="text-gray-600 hover:text-gray-800 mr-4">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold font-heading">Hapus Kategori</h1>
            <p class="text-gray-600 mt-2">Konfirmasi penghapusan kategori</p>
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
                Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        <!-- Category Info -->
        <div class="border rounded-lg p-4 mb-6 bg-gray-50">
            <h4 class="font-medium text-gray-900 mb-2"><?php echo htmlspecialchars($category['name']); ?></h4>
            <?php if (!empty($category['description'])): ?>
            <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($category['description']); ?></p>
            <?php endif; ?>
            <p class="text-sm text-gray-500">
                Status: 
                <span class="<?php echo $category['is_active'] ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo $category['is_active'] ? 'Aktif' : 'Tidak Aktif'; ?>
                </span>
            </p>
            <p class="text-sm text-gray-500">Jumlah produk: <?php echo $product_count; ?></p>
        </div>

        <?php if ($product_count > 0): ?>
        <!-- Warning for categories with products -->
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            <div class="flex">
                <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                <div>
                    <p class="font-medium">Kategori tidak dapat dihapus</p>
                    <p class="text-sm">Kategori ini masih memiliki <?php echo $product_count; ?> produk. 
                       Hapus atau pindahkan semua produk terlebih dahulu sebelum menghapus kategori.</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons for categories with products -->
        <div class="flex justify-center">
            <a href="categories.php" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
        
        <?php else: ?>
        <!-- Action Buttons for empty categories -->
        <form method="POST" class="flex justify-center space-x-4">
            <a href="categories.php" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit" name="confirm_delete" value="1"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>Ya, Hapus Kategori
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
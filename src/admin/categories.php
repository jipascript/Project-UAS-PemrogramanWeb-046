<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get all categories with safe column selection
try {
    $query = "SELECT id, name, description, 
                     IFNULL(is_active, 1) as is_active,
                     IFNULL(created_at, NOW()) as created_at
              FROM categories 
              ORDER BY name ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback query if some columns don't exist
    $query = "SELECT id, name, description FROM categories ORDER BY name ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add default values for missing columns
    foreach ($categories as &$category) {
        if (!isset($category['is_active'])) {
            $category['is_active'] = 1;
        }
        if (!isset($category['created_at'])) {
            $category['created_at'] = date('Y-m-d H:i:s');
        }
    }
}

$page_title = 'Kategori';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold font-heading">Kategori Produk</h1>
            <p class="text-gray-600 mt-2">Kelola kategori produk yang tersedia</p>
        </div>
        <a href="add-category.php" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>Tambah Kategori
        </a>
    </div>

    <!-- Categories Grid -->
    <?php if (empty($categories)): ?>
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <div class="text-gray-500">
            <i class="fas fa-tags text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Belum ada kategori</h3>
            <p class="text-gray-500 mb-6">Tambahkan kategori pertama untuk mengorganisir produk Anda</p>
            <a href="add-category.php" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Kategori
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($categories as $category): ?>
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <?php echo htmlspecialchars($category['name']); ?>
                </h3>
                <div class="flex space-x-2">
                    <a href="edit-category.php?id=<?php echo $category['id']; ?>" 
                       class="text-indigo-600 hover:text-indigo-900 transition-colors" 
                       title="Edit kategori">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="delete-category.php?id=<?php echo $category['id']; ?>" 
                       class="text-red-600 hover:text-red-900 transition-colors" 
                       title="Hapus kategori"
                       onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
            <p class="text-gray-600 text-sm mb-4 min-h-[3rem]">
                <?php 
                $description = $category['description'] ?? '';
                echo !empty($description) ? htmlspecialchars($description) : 'Tidak ada deskripsi';
                ?>
            </p>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    <?php 
                    // Count products in this category
                    $count_query = "SELECT COUNT(*) as product_count FROM products WHERE category_id = ?";
                    $count_stmt = $db->prepare($count_query);
                    $count_stmt->execute([$category['id']]);
                    $count = $count_stmt->fetchColumn();
                    echo $count . ' produk';
                    ?>
                </span>
                <?php 
                $is_active = isset($category['is_active']) ? $category['is_active'] : 1;
                $status_class = $is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                $status_text = $is_active ? 'Aktif' : 'Tidak Aktif';
                ?>
                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $status_class; ?>">
                    <?php echo $status_text; ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

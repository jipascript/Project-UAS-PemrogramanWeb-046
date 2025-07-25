<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Check for filter
$filter = $_GET['filter'] ?? '';
$where_clause = '';
if ($filter === 'low_stock') {
    $where_clause = 'WHERE p.stock <= 5';
}

// Get all products with safe column selection
try {
    $query = "SELECT p.id, p.name, p.description, p.price, p.stock, p.category_id, 
                     IFNULL(p.image_url, '') as image_url,
                     IFNULL(p.is_active, 1) as is_active,
                     IFNULL(p.created_at, NOW()) as created_at,
                     IFNULL(c.name, 'Tidak ada kategori') as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              $where_clause
              ORDER BY p.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback query if some columns don't exist
    $query = "SELECT p.id, p.name, p.description, p.price, p.stock, p.category_id, 
                     c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              $where_clause
              ORDER BY p.id DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add default values for missing columns
    foreach ($products as &$product) {
        if (!isset($product['image_url'])) {
            $product['image_url'] = '';
        }
        if (!isset($product['is_active'])) {
            $product['is_active'] = 1;
        }
        if (!isset($product['created_at'])) {
            $product['created_at'] = date('Y-m-d H:i:s');
        }
    }
}

$page_title = 'Manajemen Produk';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold font-heading">Manajemen Produk</h1>
            <p class="text-gray-600 mt-2">Kelola produk yang tersedia di toko</p>
            <?php if ($filter === 'low_stock'): ?>
            <div class="mt-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Menampilkan produk dengan stok rendah (≤5)
                </span>
                <a href="products.php" class="ml-2 text-sm text-blue-600 hover:text-blue-800">Tampilkan semua</a>
            </div>
            <?php endif; ?>
        </div>
        <div class="flex space-x-4">
            <div class="relative">
                <select onchange="window.location.href=this.value" class="border border-gray-300 rounded-lg px-4 py-2 pr-8">
                    <option value="products.php" <?php echo $filter === '' ? 'selected' : ''; ?>>Semua Produk</option>
                    <option value="products.php?filter=low_stock" <?php echo $filter === 'low_stock' ? 'selected' : ''; ?>>Stok Rendah</option>
                </select>
            </div>
            <a href="add-product.php" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Produk
            </a>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produk
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Harga
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stok
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-box-open text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada produk</p>
                                <p class="text-sm">Tambahkan produk pertama Anda untuk memulai</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <?php 
                                        $image_url = isset($product['image_url']) && !empty($product['image_url']) 
                                            ? htmlspecialchars($product['image_url']) 
                                            : 'https://via.placeholder.com/40x40/ec4899/ffffff?text=' . urlencode(substr($product['name'], 0, 1));
                                        ?>
                                        <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200" 
                                             src="<?php echo $image_url; ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             onerror="this.src='https://via.placeholder.com/40x40/ec4899/ffffff?text=<?php echo urlencode(substr($product['name'], 0, 1)); ?>'">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php 
                                            $description = $product['description'] ?? '';
                                            echo strlen($description) > 50 
                                                ? substr(htmlspecialchars($description), 0, 50) . '...' 
                                                : htmlspecialchars($description);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php echo htmlspecialchars($product['category_name'] ?? 'Tidak ada kategori'); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    <?php echo formatPrice($product['price']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php 
                                    $stock = $product['stock'] ?? 0;
                                    $stock_class = $stock <= 5 ? 'text-red-600 font-semibold' : 'text-gray-900';
                                    ?>
                                    <span class="<?php echo $stock_class; ?>">
                                        <?php echo $stock; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $is_active = isset($product['is_active']) ? $product['is_active'] : 1;
                                $status_class = $is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                $status_text = $is_active ? 'Aktif' : 'Tidak Aktif';
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="edit-product.php?id=<?php echo $product['id']; ?>" 
                                   class="text-indigo-600 hover:text-indigo-900 mr-3 transition-colors"
                                   title="Edit produk">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete-product.php?id=<?php echo $product['id']; ?>" 
                                   class="text-red-600 hover:text-red-900 transition-colors"
                                   title="Hapus produk"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

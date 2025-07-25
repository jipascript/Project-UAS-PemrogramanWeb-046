<?php
require_once 'includes/functions.php';

$database = new Database();
$db = $database->getConnection();

// Get all categories with product count
$query = "SELECT c.*, COUNT(p.id) as product_count 
          FROM categories c 
          LEFT JOIN products p ON c.id = p.category_id 
          GROUP BY c.id 
          ORDER BY c.name";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Kategori Produk';
include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-pink-500 to-purple-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold font-heading mb-4">Kategori Produk</h1>
            <p class="text-xl text-pink-100">Jelajahi koleksi fashion terlengkap kami</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        <?php if (empty($categories)): ?>
            <div class="text-center py-16">
                <i class="fas fa-tags text-6xl text-gray-300 mb-6"></i>
                <h2 class="text-2xl font-semibold text-gray-600 mb-4">Belum Ada Kategori</h2>
                <p class="text-gray-500 mb-8">Kategori produk akan segera tersedia</p>
                <a href="products.php" class="btn-primary">
                    Lihat Semua Produk
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach ($categories as $category): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="relative">
                            <div class="h-48 bg-gradient-to-br from-pink-100 to-purple-100 flex items-center justify-center">
                                <i class="fas fa-tags text-4xl text-pink-500"></i>
                            </div>
                            <div class="absolute top-4 right-4 bg-white bg-opacity-90 rounded-full px-3 py-1">
                                <span class="text-sm font-semibold text-gray-700"><?php echo $category['product_count']; ?> produk</span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($category['name']); ?></h3>
                            <?php if ($category['description']): ?>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars($category['description']); ?></p>
                            <?php endif; ?>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-pink-600 font-semibold"><?php echo $category['product_count']; ?> Produk</span>
                                <a href="products.php?category=<?php echo $category['id']; ?>" 
                                   class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition-colors">
                                    Lihat Produk
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Popular Categories Section -->
        <div class="mt-16">
            <h2 class="text-3xl font-bold text-center mb-12 font-heading">Kategori Populer</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <a href="products.php?category=1" class="group">
                    <div class="bg-gradient-to-br from-pink-400 to-red-400 rounded-lg p-8 text-center text-white hover:from-pink-500 hover:to-red-500 transition-all duration-300 transform group-hover:scale-105">
                        <i class="fas fa-tshirt text-3xl mb-4"></i>
                        <h3 class="font-semibold">Atasan</h3>
                    </div>
                </a>
                
                <a href="products.php?category=2" class="group">
                    <div class="bg-gradient-to-br from-purple-400 to-indigo-400 rounded-lg p-8 text-center text-white hover:from-purple-500 hover:to-indigo-500 transition-all duration-300 transform group-hover:scale-105">
                        <i class="fas fa-user-tie text-3xl mb-4"></i>
                        <h3 class="font-semibold">Bawahan</h3>
                    </div>
                </a>
                
                <a href="products.php?category=3" class="group">
                    <div class="bg-gradient-to-br from-green-400 to-teal-400 rounded-lg p-8 text-center text-white hover:from-green-500 hover:to-teal-500 transition-all duration-300 transform group-hover:scale-105">
                        <i class="fas fa-female text-3xl mb-4"></i>
                        <h3 class="font-semibold">Dress</h3>
                    </div>
                </a>
                
                <a href="products.php?category=4" class="group">
                    <div class="bg-gradient-to-br from-orange-400 to-yellow-400 rounded-lg p-8 text-center text-white hover:from-orange-500 hover:to-yellow-500 transition-all duration-300 transform group-hover:scale-105">
                        <i class="fas fa-gem text-3xl mb-4"></i>
                        <h3 class="font-semibold">Aksesoris</h3>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
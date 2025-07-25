<?php
require_once 'includes/functions.php';

$page_title = 'Produk';
include 'includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query
$where_conditions = ["p.status = 'active'"];
$params = [];

if ($search) {
    $where_conditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_id > 0) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_id;
}

if ($min_price > 0) {
    $where_conditions[] = "p.price >= ?";
    $params[] = $min_price;
}

if ($max_price > 0) {
    $where_conditions[] = "p.price <= ?";
    $params[] = $max_price;
}

$where_clause = implode(' AND ', $where_conditions);

// Sort options
$order_by = "p.created_at DESC";
switch ($sort) {
    case 'price_low':
        $order_by = "p.price ASC";
        break;
    case 'price_high':
        $order_by = "p.price DESC";
        break;
    case 'name':
        $order_by = "p.name ASC";
        break;
    case 'rating':
        $order_by = "avg_rating DESC";
        break;
}

// Get total count
$count_query = "SELECT COUNT(*) as total 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE $where_clause";
$stmt = $db->prepare($count_query);
$stmt->execute($params);
$total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_products / $limit);

// Get products
$query = "SELECT p.*, c.name as category_name,
          COALESCE(AVG(r.rating), 0) as avg_rating,
          COUNT(r.id) as review_count
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN reviews r ON p.id = r.product_id
          WHERE $where_clause
          GROUP BY p.id
          ORDER BY $order_by
          LIMIT $limit OFFSET $offset";

$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$query = "SELECT * FROM categories ORDER BY name";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold font-heading mb-4">Semua Produk</h1>
        <p class="text-gray-600">Temukan produk terbaik untuk kebutuhan Anda</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Produk</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Nama produk..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Price Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Min</label>
                <input type="number" name="min_price" value="<?php echo $min_price; ?>" 
                       placeholder="0" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Max</label>
                <input type="number" name="max_price" value="<?php echo $max_price; ?>" 
                       placeholder="0" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <!-- Sort -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Terbaru</option>
                    <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Harga Terendah</option>
                    <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Harga Tertinggi</option>
                    <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Nama A-Z</option>
                    <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Rating Tertinggi</option>
                </select>
            </div>

            <div class="md:col-span-2 lg:col-span-5 flex space-x-4">
                <button type="submit" class="btn-primary px-6 py-2 rounded-md text-white">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="products.php" class="border border-gray-300 px-6 py-2 rounded-md hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Results Info -->
    <div class="flex justify-between items-center mb-6">
        <p class="text-gray-600">
            Menampilkan <?php echo count($products); ?> dari <?php echo $total_products; ?> produk
        </p>
    </div>

    <!-- Products Grid -->
    <?php if (empty($products)): ?>
        <div class="text-center py-16">
            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
            <h2 class="text-2xl font-semibold text-gray-600 mb-4">Produk Tidak Ditemukan</h2>
            <p class="text-gray-500 mb-8">Coba ubah filter pencarian Anda</p>
            <a href="products.php" class="btn-primary px-8 py-3 rounded-lg text-white inline-block">
                Lihat Semua Produk
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="relative">
                        <img src="<?php echo !empty($product['image']) ? $product['image'] : 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=400&fit=crop&crop=center'; ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             class="w-full h-48 object-cover">
                        
                        <?php if ($product['stock'] <= 0): ?>
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                    Stok Habis
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-4">
                        <div class="mb-2">
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </span>
                        </div>
                        
                        <h3 class="font-semibold text-lg mb-2 line-clamp-2">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" 
                               class="hover:text-pink-500 transition-colors">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400 text-sm mr-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= round($product['avg_rating']) ? '' : 'text-gray-300'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="text-sm text-gray-500">
                                (<?php echo $product['review_count']; ?>)
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-pink-500">
                                <?php echo formatPrice($product['price']); ?>
                            </span>
                            
                            <?php if ($product['stock'] > 0): ?>
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" 
                                        class="bg-pink-500 text-white px-4 py-2 rounded-md hover:bg-pink-600 transition-colors">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-3">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" 
                               class="w-full border border-pink-500 text-pink-500 py-2 rounded-md text-center block hover:bg-pink-50 transition-colors">
                                Lihat Detail
                            </a>
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
// Notification system
function showNotification(type, message) {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle', 
        info: 'info-circle',
        warning: 'exclamation-triangle'
    };
    
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 max-w-md`;
    notification.style.animation = 'slideInRight 0.3s ease-out';
    
    notification.innerHTML = `
        <i class="fas fa-${icons[type]}"></i>
        <span class="flex-1">${message}</span>
        <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200 ml-2">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Update cart count in header
function updateCartCount() {
    fetch('ajax/get-cart-count.php')
        .then(response => response.json())
        .then(data => {
            const cartBadge = document.querySelector('.fa-shopping-cart').parentElement.querySelector('span');
            if (cartBadge && data.count > 0) {
                cartBadge.textContent = data.count;
                cartBadge.style.display = 'flex';
                // Add bounce animation
                cartBadge.style.animation = 'bounce 0.6s ease-in-out';
            }
        })
        .catch(error => {
            console.log('Could not update cart count');
        });
}

// Add to cart function
function addToCart(productId) {
    <?php if (isLoggedIn()): ?>
        // Show loading state on button
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        fetch('ajax/add-to-cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message);
                updateCartCount();
                
                // Change button to success state temporarily
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.add('bg-green-500', 'hover:bg-green-600');
                button.classList.remove('bg-pink-500', 'hover:bg-pink-600');
                
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.classList.remove('bg-green-500', 'hover:bg-green-600');
                    button.classList.add('bg-pink-500', 'hover:bg-pink-600');
                    button.disabled = false;
                }, 2000);
            } else {
                showNotification('error', data.message);
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            showNotification('error', 'Terjadi kesalahan saat menambahkan ke keranjang');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    <?php else: ?>
        showNotification('warning', 'Silakan login terlebih dahulu');
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 2000);
    <?php endif; ?>
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes bounce {
        0%, 20%, 60%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        80% {
            transform: translateY(-5px);
        }
    }
`;
document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?>
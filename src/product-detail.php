<?php
require_once 'includes/functions.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: products.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get product details
$query = "SELECT p.*, c.name as category_name,
          COALESCE(AVG(r.rating), 0) as avg_rating,
          COUNT(r.id) as review_count
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN reviews r ON p.id = r.product_id
          WHERE p.id = ? AND p.status = 'active'
          GROUP BY p.id";
$stmt = $db->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit();
}

$page_title = $product['name'];
include 'includes/header.php';

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn()) {
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    
    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        // Check if user has purchased this product
        $query = "SELECT COUNT(*) as count FROM transaction_items ti 
                  JOIN transactions t ON ti.transaction_id = t.id 
                  WHERE t.user_id = ? AND ti.product_id = ? AND t.status = 'completed'";
        $stmt = $db->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $has_purchased = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        
        if ($has_purchased) {
            // Check if user already reviewed this product
            $query = "SELECT id FROM reviews WHERE user_id = ? AND product_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            
            if ($stmt->rowCount() == 0) {
                $query = "INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$_SESSION['user_id'], $product_id, $rating, $comment]);
                
                logActivity($_SESSION['user_id'], "Added review for product '{$product['name']}'");
                setFlashMessage('success', 'Review berhasil ditambahkan');
            } else {
                setFlashMessage('error', 'Anda sudah memberikan review untuk produk ini');
            }
        } else {
            setFlashMessage('error', 'Anda harus membeli produk ini terlebih dahulu untuk memberikan review');
        }
        
        header("Location: product-detail.php?id=$product_id");
        exit();
    }
}

// Get reviews
$query = "SELECT r.*, u.name as user_name 
          FROM reviews r 
          JOIN users u ON r.user_id = u.id 
          WHERE r.product_id = ? 
          ORDER BY r.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$product_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get related products
$query = "SELECT p.*, COALESCE(AVG(r.rating), 0) as avg_rating
          FROM products p 
          LEFT JOIN reviews r ON p.id = r.product_id
          WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
          GROUP BY p.id
          ORDER BY RAND()
          LIMIT 4";
$stmt = $db->prepare($query);
$stmt->execute([$product['category_id'], $product_id]);
$related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if user has purchased this product (for review form)
$can_review = false;
if (isLoggedIn()) {
    $query = "SELECT COUNT(*) as count FROM transaction_items ti 
              JOIN transactions t ON ti.transaction_id = t.id 
              WHERE t.user_id = ? AND ti.product_id = ? AND t.status = 'completed'";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    $has_purchased = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    
    if ($has_purchased) {
        $query = "SELECT id FROM reviews WHERE user_id = ? AND product_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $can_review = $stmt->rowCount() == 0;
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="merona-shop.php" class="hover:text-pink-500">Beranda</a></li>
            <li><i class="fas fa-chevron-right"></i></li>
            <li><a href="products.php" class="hover:text-pink-500">Produk</a></li>
            <li><i class="fas fa-chevron-right"></i></li>
            <li><a href="products.php?category=<?php echo $product['category_id']; ?>" class="hover:text-pink-500">
                <?php echo htmlspecialchars($product['category_name']); ?>
            </a></li>
            <li><i class="fas fa-chevron-right"></i></li>
            <li class="text-gray-900"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
        <!-- Product Images -->
        <div>
            <div class="relative">
                <img id="mainImage" 
                     src="<?php echo !empty($product['image']) ? $product['image'] : 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=600&h=600&fit=crop&crop=center'; ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     class="w-full h-96 object-cover rounded-lg shadow-md">
                
                <?php if ($product['stock'] <= 0): ?>
                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-lg">
                        <span class="bg-red-500 text-white px-4 py-2 rounded-full text-lg font-semibold">
                            Stok Habis
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div>
            <div class="mb-4">
                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </span>
            </div>

            <h1 class="text-3xl font-bold font-heading mb-4"><?php echo htmlspecialchars($product['name']); ?></h1>

            <!-- Rating -->
            <div class="flex items-center mb-6">
                <div class="flex text-yellow-400 text-lg mr-3">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo $i <= round($product['avg_rating']) ? '' : 'text-gray-300'; ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="text-gray-600">
                    <?php echo number_format($product['avg_rating'], 1); ?> 
                    (<?php echo $product['review_count']; ?> review)
                </span>
            </div>

            <!-- Price -->
            <div class="mb-6">
                <span class="text-4xl font-bold text-pink-500">
                    <?php echo formatPrice($product['price']); ?>
                </span>
            </div>

            <!-- Stock -->
            <div class="mb-6">
                <span class="text-gray-600">Stok: </span>
                <span class="font-semibold <?php echo $product['stock'] > 0 ? 'text-green-500' : 'text-red-500'; ?>">
                    <?php echo $product['stock'] > 0 ? $product['stock'] . ' tersedia' : 'Habis'; ?>
                </span>
            </div>

            <!-- Description -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-3">Deskripsi Produk</h3>
                <p class="text-gray-600 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>
            </div>

            <!-- Add to Cart -->
            <?php if ($product['stock'] > 0): ?>
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex items-center border border-gray-300 rounded-md">
                        <button onclick="decreaseQty()" class="px-3 py-2 hover:bg-gray-100">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" 
                               class="w-16 text-center border-0 focus:outline-none">
                        <button onclick="increaseQty()" class="px-3 py-2 hover:bg-gray-100">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    
                    <button onclick="addToCart()" class="flex-1 btn-primary py-3 rounded-lg text-white">
                        <i class="fas fa-cart-plus mr-2"></i>
                        Tambah ke Keranjang
                    </button>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex space-x-4">
                <?php if (isLoggedIn()): ?>
                    <button onclick="addToWishlist(<?php echo $product['id']; ?>)" 
                            class="flex-1 border border-pink-500 text-pink-500 py-3 rounded-lg hover:bg-pink-50 transition-colors">
                        <i class="fas fa-heart mr-2"></i>
                        Tambah ke Wishlist
                    </button>
                <?php endif; ?>
                
                <button onclick="shareProduct()" 
                        class="border border-gray-300 px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-share-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Product Details Tabs -->
    <div class="bg-white rounded-lg shadow-md mb-12">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6">
                <button onclick="showTab('description')" 
                        class="tab-button py-4 border-b-2 border-transparent hover:border-pink-500 focus:outline-none"
                        data-tab="description">
                    Deskripsi
                </button>
                <button onclick="showTab('reviews')" 
                        class="tab-button py-4 border-b-2 border-transparent hover:border-pink-500 focus:outline-none"
                        data-tab="reviews">
                    Review (<?php echo $product['review_count']; ?>)
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Description Tab -->
            <div id="description-tab" class="tab-content">
                <h3 class="text-xl font-semibold mb-4">Deskripsi Produk</h3>
                <div class="prose max-w-none">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
            </div>

            <!-- Reviews Tab -->
            <div id="reviews-tab" class="tab-content hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold">Review Pelanggan</h3>
                    <?php if ($can_review): ?>
                        <button onclick="showReviewForm()" class="btn-primary px-4 py-2 rounded-md text-white">
                            Tulis Review
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Review Form -->
                <?php if ($can_review): ?>
                    <div id="review-form" class="bg-gray-50 p-6 rounded-lg mb-6 hidden">
                        <h4 class="text-lg font-semibold mb-4">Tulis Review Anda</h4>
                        <form method="POST">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                <div class="flex space-x-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button" onclick="setRating(<?php echo $i; ?>)" 
                                                class="rating-star text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none"
                                                data-rating="<?php echo $i; ?>">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="rating" id="rating-input" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                                <textarea name="comment" rows="4" required
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                                          placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
                            </div>
                            
                            <div class="flex space-x-4">
                                <button type="submit" class="btn-primary px-6 py-2 rounded-md text-white">
                                    Kirim Review
                                </button>
                                <button type="button" onclick="hideReviewForm()" 
                                        class="border border-gray-300 px-6 py-2 rounded-md hover:bg-gray-50">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Reviews List -->
                <?php if (empty($reviews)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-comments text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">Belum ada review untuk produk ini</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($reviews as $review): ?>
                            <div class="border-b border-gray-200 pb-6 last:border-b-0">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-pink-500 rounded-full flex items-center justify-center text-white font-semibold">
                                            <?php echo strtoupper(substr($review['user_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <h5 class="font-semibold"><?php echo htmlspecialchars($review['user_name']); ?></h5>
                                            <div class="flex text-yellow-400 text-sm">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? '' : 'text-gray-300'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        <?php echo timeAgo($review['created_at']); ?>
                                    </span>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
        <div class="mb-12">
            <h2 class="text-2xl font-bold font-heading mb-8">Produk Terkait</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($related_products as $related): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <img src="<?php echo !empty($related['image']) ? $related['image'] : 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=300&h=300&fit=crop&crop=center'; ?>" 
                             alt="<?php echo htmlspecialchars($related['name']); ?>"
                             class="w-full h-48 object-cover">
                        
                        <div class="p-4">
                            <h3 class="font-semibold mb-2 line-clamp-2">
                                <a href="product-detail.php?id=<?php echo $related['id']; ?>" 
                                   class="hover:text-pink-500 transition-colors">
                                    <?php echo htmlspecialchars($related['name']); ?>
                                </a>
                            </h3>
                            
                            <div class="flex items-center mb-2">
                                <div class="flex text-yellow-400 text-sm mr-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= round($related['avg_rating']) ? '' : 'text-gray-300'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-pink-500">
                                    <?php echo formatPrice($related['price']); ?>
                                </span>
                                
                                <a href="product-detail.php?id=<?php echo $related['id']; ?>" 
                                   class="text-pink-500 hover:text-pink-600">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Quantity controls
function increaseQty() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.getAttribute('max'));
    const current = parseInt(input.value);
    
    if (current < max) {
        input.value = current + 1;
    }
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    
    if (current > 1) {
        input.value = current - 1;
    }
}

// Add to cart
function addToCart() {
    <?php if (isLoggedIn()): ?>
        const quantity = parseInt(document.getElementById('quantity').value);
        
        fetch('ajax/add-to-cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: <?php echo $product['id']; ?>,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message);
                updateCartCount();
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            showNotification('error', 'Terjadi kesalahan');
        });
    <?php else: ?>
        showNotification('error', 'Silakan login terlebih dahulu');
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 2000);
    <?php endif; ?>
}

// Tabs
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-pink-500', 'text-pink-500');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active class to selected button
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('border-pink-500', 'text-pink-500');
}

// Review form
function showReviewForm() {
    document.getElementById('review-form').classList.remove('hidden');
}

function hideReviewForm() {
    document.getElementById('review-form').classList.add('hidden');
}

function setRating(rating) {
    document.getElementById('rating-input').value = rating;
    
    document.querySelectorAll('.rating-star').forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

// Share product
function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo htmlspecialchars($product['name']); ?>',
            text: 'Lihat produk ini di Merona Shop',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('success', 'Link produk berhasil disalin');
        });
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    showTab('description');
});
</script>

<?php include 'includes/footer.php'; ?>
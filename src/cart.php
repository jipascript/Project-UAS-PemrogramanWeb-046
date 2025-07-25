<?php
require_once 'includes/functions.php';
requireLogin();

$page_title = 'Shopping Cart';
include 'includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'update') {
            $cart_id = (int)$_POST['cart_id'];
            $quantity = (int)$_POST['quantity'];
            
            if ($quantity > 0) {
                $query = "UPDATE carts SET quantity = ? WHERE id = ? AND user_id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$quantity, $cart_id, $_SESSION['user_id']]);
                setFlashMessage('success', 'Keranjang berhasil diupdate');
            }
        } elseif ($_POST['action'] == 'remove') {
            $cart_id = (int)$_POST['cart_id'];
            
            $query = "DELETE FROM carts WHERE id = ? AND user_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$cart_id, $_SESSION['user_id']]);
            setFlashMessage('success', 'Produk berhasil dihapus dari keranjang');
        }
        
        header('Location: cart.php');
        exit();
    }
}

// Get cart items
$query = "SELECT c.*, p.name, p.price, p.image, p.stock 
          FROM carts c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ? 
          ORDER BY c.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-8">
        <h1 class="text-3xl font-bold font-heading">Keranjang Belanja</h1>
        <span class="ml-4 bg-pink-500 text-white px-3 py-1 rounded-full text-sm">
            <?php echo count($cart_items); ?> item
        </span>
    </div>

    <?php if (empty($cart_items)): ?>
        <div class="text-center py-16">
            <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
            <h2 class="text-2xl font-semibold text-gray-600 mb-4">Keranjang Anda Kosong</h2>
            <p class="text-gray-500 mb-8">Belum ada produk yang ditambahkan ke keranjang</p>
            <a href="products.php" class="btn-primary px-8 py-3 rounded-lg text-white inline-block">
                Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="p-6 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-center space-x-4">
                                <img src="<?php echo !empty($item['image']) ? $item['image'] : 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=100&h=100&fit=crop&crop=center'; ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     class="w-20 h-20 object-cover rounded-lg">
                                
                                <div class="flex-1">
                                    <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="text-pink-500 font-bold"><?php echo formatPrice($item['price']); ?></p>
                                    <p class="text-sm text-gray-500">Stok: <?php echo $item['stock']; ?></p>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <form method="POST" class="flex items-center space-x-2">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <button type="button" onclick="decreaseQuantity(this)" 
                                                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                            <i class="fas fa-minus text-sm"></i>
                                        </button>
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="<?php echo $item['stock']; ?>"
                                               class="w-16 text-center border border-gray-300 rounded-md py-1"
                                               onchange="this.form.submit()">
                                        <button type="button" onclick="increaseQuantity(this)" 
                                                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                            <i class="fas fa-plus text-sm"></i>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" class="ml-4">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" onclick="return confirm('Hapus produk ini dari keranjang?')"
                                                class="text-red-500 hover:text-red-700 p-2">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="text-right">
                                    <p class="font-bold text-lg">
                                        <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-xl font-semibold mb-4">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span><?php echo formatPrice($total); ?></span>
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
                    
                    <a href="checkout.php" class="w-full btn-primary py-3 rounded-lg text-white text-center block">
                        Lanjut ke Checkout
                    </a>
                    
                    <a href="products.php" class="w-full mt-3 border border-gray-300 py-3 rounded-lg text-center block hover:bg-gray-50">
                        Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function increaseQuantity(button) {
    const input = button.previousElementSibling;
    const max = parseInt(input.getAttribute('max'));
    const current = parseInt(input.value);
    
    if (current < max) {
        input.value = current + 1;
        input.form.submit();
    }
}

function decreaseQuantity(button) {
    const input = button.nextElementSibling;
    const current = parseInt(input.value);
    
    if (current > 1) {
        input.value = current - 1;
        input.form.submit();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
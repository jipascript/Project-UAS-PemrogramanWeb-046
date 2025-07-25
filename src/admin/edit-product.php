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

// Get categories for dropdown
$query = "SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validation
    if (empty($name)) {
        $errors[] = 'Nama produk harus diisi';
    }
    if ($price <= 0) {
        $errors[] = 'Harga harus lebih dari 0';
    }
    if ($stock < 0) {
        $errors[] = 'Stok tidak boleh negatif';
    }
    if ($category_id <= 0) {
        $errors[] = 'Kategori harus dipilih';
    }
    
    // Handle image upload
    $image_url = $product['image_url']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = 'product_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Delete old image if exists
                if (!empty($product['image_url']) && file_exists('../' . $product['image_url'])) {
                    unlink('../' . $product['image_url']);
                }
                $image_url = 'uploads/products/' . $new_filename;
            } else {
                $errors[] = 'Gagal mengupload gambar';
            }
        } else {
            $errors[] = 'Format gambar tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF';
        }
    }
    
    if (empty($errors)) {
        try {
            $query = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, 
                      category_id = ?, image_url = ?, is_active = ?, updated_at = NOW() 
                      WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$name, $description, $price, $stock, $category_id, $image_url, $is_active, $product_id]);
            
            setFlashMessage('success', 'Produk berhasil diperbarui');
            header('Location: products.php');
            exit();
        } catch (Exception $e) {
            $errors[] = 'Gagal memperbarui produk: ' . $e->getMessage();
        }
    }
}

$page_title = 'Edit Produk';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="products.php" class="text-gray-600 hover:text-gray-800 mr-4">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold font-heading">Edit Produk</h1>
            <p class="text-gray-600 mt-2">Perbarui informasi produk</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                           value="<?php echo htmlspecialchars($_POST['name'] ?? $product['name']); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo (($_POST['category_id'] ?? $product['category_id']) == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required
                           value="<?php echo htmlspecialchars($_POST['price'] ?? $product['price']); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                        Stok <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="stock" name="stock" min="0" required
                           value="<?php echo htmlspecialchars($_POST['stock'] ?? $product['stock']); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500"
                          placeholder="Deskripsi produk..."><?php echo htmlspecialchars($_POST['description'] ?? $product['description']); ?></textarea>
            </div>

            <!-- Current Image -->
            <?php if (!empty($product['image_url'])): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</label>
                <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="Current product image" 
                     class="w-32 h-32 object-cover rounded-lg border">
            </div>
            <?php endif; ?>

            <!-- Image Upload -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                    Gambar Produk Baru (Opsional)
                </label>
                <input type="file" id="image" name="image" accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                <p class="text-sm text-gray-500 mt-1">Format yang didukung: JPG, JPEG, PNG, GIF. Maksimal 5MB.</p>
            </div>

            <!-- Status -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           <?php echo (($_POST['is_active'] ?? $product['is_active']) == 1) ? 'checked' : ''; ?>
                           class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Produk aktif</span>
                </label>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="products.php" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Perbarui Produk
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
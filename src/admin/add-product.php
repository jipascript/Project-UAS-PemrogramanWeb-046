<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get categories for dropdown
$query = "SELECT id, name FROM categories ORDER BY name ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$success = false;

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
    
    // Handle image URL
    $image_url = trim($_POST['image_url'] ?? '');
    
    // Validate image URL if provided
    if (!empty($image_url)) {
        if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
            $errors[] = 'URL gambar tidak valid';
        }
    }
    
    if (empty($errors)) {
        try {
            $status = $is_active ? 'active' : 'inactive';
            $query = "INSERT INTO products (name, description, price, stock, category_id, image, status, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $db->prepare($query);
            $stmt->execute([$name, $description, $price, $stock, $category_id, $image_url, $status]);
            
            setFlashMessage('success', 'Produk berhasil ditambahkan');
            header('Location: products.php');
            exit();
        } catch (Exception $e) {
            $errors[] = 'Gagal menambahkan produk: ' . $e->getMessage();
        }
    }
}

$page_title = 'Tambah Produk';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="products.php" class="text-gray-600 hover:text-gray-800 mr-4">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold font-heading">Tambah Produk</h1>
            <p class="text-gray-600 mt-2">Tambahkan produk baru ke toko</p>
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
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
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
                                <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
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
                           value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                        Stok <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="stock" name="stock" min="0" required
                           value="<?php echo htmlspecialchars($_POST['stock'] ?? ''); ?>"
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
                          placeholder="Deskripsi produk..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>

            <!-- Image URL -->
            <div>
                <label for="image_url" class="block text-sm font-medium text-gray-700 mb-2">
                    URL Gambar Produk
                </label>
                <input type="url" id="image_url" name="image_url" 
                       value="<?php echo htmlspecialchars($_POST['image_url'] ?? ''); ?>"
                       placeholder="https://images.unsplash.com/photo-..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                <p class="text-sm text-gray-500 mt-1">Masukkan URL gambar dari Unsplash atau sumber online lainnya.</p>
                <!-- Preview -->
                <div id="image-preview" class="mt-3" style="display: none;">
                    <img id="preview-img" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border">
                </div>
            </div>


            <!-- Status -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           <?php echo (isset($_POST['is_active']) || !$_POST) ? 'checked' : ''; ?>
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
                    <i class="fas fa-save mr-2"></i>Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
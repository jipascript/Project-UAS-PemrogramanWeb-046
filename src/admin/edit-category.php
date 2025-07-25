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

$errors = [];

if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validation
    if (empty($name)) {
        $errors[] = 'Nama kategori harus diisi';
    }
    
    if (empty($errors)) {
        try {
            $query = "UPDATE categories SET name = ?, description = ?, is_active = ?, updated_at = NOW() 
                      WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$name, $description, $is_active, $category_id]);
            
            setFlashMessage('success', 'Kategori berhasil diperbarui');
            header('Location: categories.php');
            exit();
        } catch (Exception $e) {
            $errors[] = 'Gagal memperbarui kategori: ' . $e->getMessage();
        }
    }
}

$page_title = 'Edit Kategori';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="categories.php" class="text-gray-600 hover:text-gray-800 mr-4">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold font-heading">Edit Kategori</h1>
            <p class="text-gray-600 mt-2">Perbarui informasi kategori</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
        <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <!-- Category Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                       value="<?php echo htmlspecialchars($_POST['name'] ?? $category['name']); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500"
                          placeholder="Deskripsi kategori..."><?php echo htmlspecialchars($_POST['description'] ?? $category['description']); ?></textarea>
            </div>

            <!-- Status -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           <?php echo (($_POST['is_active'] ?? $category['is_active']) == 1) ? 'checked' : ''; ?>
                           class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Kategori aktif</span>
                </label>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="categories.php" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get user ID
$user_id = intval($_GET['id'] ?? 0);
if ($user_id <= 0) {
    setFlashMessage('error', 'ID pengguna tidak valid');
    header('Location: users.php');
    exit();
}

// Get user data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    setFlashMessage('error', 'Pengguna tidak ditemukan');
    header('Location: users.php');
    exit();
}

$errors = [];

if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $password = trim($_POST['password'] ?? '');
    
    // Validation
    if (empty($name)) {
        $errors[] = 'Nama harus diisi';
    }
    if (empty($email)) {
        $errors[] = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }
    if (!in_array($role, ['admin', 'customer'])) {
        $errors[] = 'Role tidak valid';
    }
    
    // Check if email already exists (excluding current user)
    if (empty($errors)) {
        $query = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = 'Email sudah digunakan oleh pengguna lain';
        }
    }
    
    if (empty($errors)) {
        try {
            // Update user data
            if (!empty($password)) {
                // Update with new password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET name = ?, email = ?, phone = ?, role = ?, 
                          is_active = ?, password = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$name, $email, $phone, $role, $is_active, $hashed_password, $user_id]);
            } else {
                // Update without changing password
                $query = "UPDATE users SET name = ?, email = ?, phone = ?, role = ?, 
                          is_active = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$name, $email, $phone, $role, $is_active, $user_id]);
            }
            
            setFlashMessage('success', 'Pengguna berhasil diperbarui');
            header('Location: users.php');
            exit();
        } catch (Exception $e) {
            $errors[] = 'Gagal memperbarui pengguna: ' . $e->getMessage();
        }
    }
}

$page_title = 'Edit Pengguna';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="users.php" class="text-gray-600 hover:text-gray-800 mr-4">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold font-heading">Edit Pengguna</h1>
            <p class="text-gray-600 mt-2">Perbarui informasi pengguna</p>
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                           value="<?php echo htmlspecialchars($_POST['name'] ?? $user['name']); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? $user['email']); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Telepon
                    </label>
                    <input type="tel" id="phone" name="phone"
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? $user['phone']); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select id="role" name="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">Pilih Role</option>
                        <option value="admin" <?php echo (($_POST['role'] ?? $user['role']) == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="customer" <?php echo (($_POST['role'] ?? $user['role']) == 'customer') ? 'selected' : ''; ?>>Customer</option>
                    </select>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password Baru (Kosongkan jika tidak ingin mengubah)
                </label>
                <input type="password" id="password" name="password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500"
                       placeholder="Masukkan password baru">
                <p class="text-sm text-gray-500 mt-1">Minimal 6 karakter</p>
            </div>

            <!-- Status -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           <?php echo (($_POST['is_active'] ?? $user['is_active']) == 1) ? 'checked' : ''; ?>
                           class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Pengguna aktif</span>
                </label>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="users.php" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Perbarui Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
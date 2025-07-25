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

// Prevent deleting current admin user
if ($user['id'] == $_SESSION['user_id']) {
    setFlashMessage('error', 'Anda tidak dapat menghapus akun Anda sendiri');
    header('Location: users.php');
    exit();
}

// Check if user has orders
$query = "SELECT COUNT(*) as order_count FROM transactions WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$order_count = $stmt->fetch(PDO::FETCH_ASSOC)['order_count'];

if ($_POST && isset($_POST['confirm_delete'])) {
    try {
        // Delete user from database
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$user_id]);
        
        setFlashMessage('success', 'Pengguna berhasil dihapus');
        header('Location: users.php');
        exit();
    } catch (Exception $e) {
        setFlashMessage('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
    }
}

$page_title = 'Hapus Pengguna';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="users.php" class="text-gray-600 hover:text-gray-800 mr-4">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold font-heading">Hapus Pengguna</h1>
            <p class="text-gray-600 mt-2">Konfirmasi penghapusan pengguna</p>
        </div>
    </div>

    <!-- Confirmation Card -->
    <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Penghapusan</h3>
            <p class="text-sm text-gray-500">
                Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        <!-- User Info -->
        <div class="border rounded-lg p-4 mb-6 bg-gray-50">
            <div class="space-y-2">
                <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></h4>
                <p class="text-sm text-gray-600">Email: <?php echo htmlspecialchars($user['email']); ?></p>
                <?php if (!empty($user['phone'])): ?>
                <p class="text-sm text-gray-600">Telepon: <?php echo htmlspecialchars($user['phone']); ?></p>
                <?php endif; ?>
                <p class="text-sm text-gray-600">
                    Role: 
                    <span class="<?php echo $user['role'] == 'admin' ? 'text-purple-600' : 'text-blue-600'; ?>">
                        <?php echo ucfirst($user['role']); ?>
                    </span>
                </p>
                <p class="text-sm text-gray-600">
                    Status: 
                    <span class="<?php echo $user['is_active'] ? 'text-green-600' : 'text-red-600'; ?>">
                        <?php echo $user['is_active'] ? 'Aktif' : 'Tidak Aktif'; ?>
                    </span>
                </p>
                <p class="text-sm text-gray-600">Bergabung: <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
                <?php if ($order_count > 0): ?>
                <p class="text-sm text-gray-600">Jumlah pesanan: <?php echo $order_count; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($order_count > 0): ?>
        <!-- Warning for users with orders -->
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            <div class="flex">
                <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                <div>
                    <p class="font-medium">Peringatan</p>
                    <p class="text-sm">Pengguna ini memiliki <?php echo $order_count; ?> pesanan. 
                       Menghapus pengguna akan mempengaruhi data pesanan yang terkait.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <form method="POST" class="flex justify-center space-x-4">
            <a href="users.php" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit" name="confirm_delete" value="1"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>Ya, Hapus Pengguna
            </button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
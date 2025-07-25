<?php
require_once 'includes/functions.php';
requireLogin();

$database = new Database();
$db = $database->getConnection();

// Get user profile
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Add formatted role name
$user['role_name'] = $user['role'] == 'admin' ? 'Administrator' : 'Customer';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    $errors = [];
    
    if (empty($name)) $errors[] = 'Nama harus diisi';
    if (empty($email)) $errors[] = 'Email harus diisi';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid';
    
    // Check if email already exists (except current user)
    if (!empty($email)) {
        $query = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Email sudah digunakan oleh pengguna lain';
        }
    }
    
    if (empty($errors)) {
        try {
            $query = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$name, $email, $phone, $address, $_SESSION['user_id']]);
            
            logActivity($_SESSION['user_id'], 'Updated profile information');
            setFlashMessage('success', 'Profil berhasil diperbarui');
            
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            header('Location: profile.php');
            exit();
            
        } catch (Exception $e) {
            $errors[] = 'Terjadi kesalahan saat menyimpan data';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $password_errors = [];
    
    if (empty($current_password)) $password_errors[] = 'Password saat ini harus diisi';
    if (empty($new_password)) $password_errors[] = 'Password baru harus diisi';
    if (strlen($new_password) < 6) $password_errors[] = 'Password baru minimal 6 karakter';
    if ($new_password !== $confirm_password) $password_errors[] = 'Konfirmasi password tidak cocok';
    
    // Verify current password
    if (!empty($current_password) && !password_verify($current_password, $user['password'])) {
        $password_errors[] = 'Password saat ini salah';
    }
    
    if (empty($password_errors)) {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$hashed_password, $_SESSION['user_id']]);
            
            logActivity($_SESSION['user_id'], 'Changed password');
            setFlashMessage('success', 'Password berhasil diubah');
            
            header('Location: profile.php');
            exit();
            
        } catch (Exception $e) {
            $password_errors[] = 'Terjadi kesalahan saat menyimpan password';
        }
    }
}

// Get user statistics
$stats = [];

// Total orders
$query = "SELECT COUNT(*) as total FROM transactions WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$stats['total_orders'] = $stmt->fetchColumn();

// Total spent
$query = "SELECT COALESCE(SUM(total_amount), 0) as total FROM transactions WHERE user_id = ? AND status != 'cancelled'";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$stats['total_spent'] = $stmt->fetchColumn();

// Pending orders
$query = "SELECT COUNT(*) as total FROM transactions WHERE user_id = ? AND status IN ('pending', 'paid')";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$stats['pending_orders'] = $stmt->fetchColumn();

// Reviews given
$query = "SELECT COUNT(*) as total FROM reviews WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$stats['total_reviews'] = $stmt->fetchColumn();

// Recent orders
$query = "SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Profil Saya';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold font-heading">Profil Saya</h1>
        <p class="text-gray-600 mt-2">Kelola informasi profil dan pengaturan akun Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user text-2xl text-pink-500"></i>
                    </div>
                    <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($user['name']); ?></h3>
                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($user['role_name']); ?></p>
                </div>
                
                <nav class="space-y-2">
                    <a href="#profile" onclick="showTab('profile')" 
                       class="tab-link flex items-center px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600 rounded-lg active">
                        <i class="fas fa-user mr-3"></i>
                        Informasi Profil
                    </a>
                    <a href="#password" onclick="showTab('password')" 
                       class="tab-link flex items-center px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600 rounded-lg">
                        <i class="fas fa-lock mr-3"></i>
                        Ubah Password
                    </a>
                    <a href="#statistics" onclick="showTab('statistics')" 
                       class="tab-link flex items-center px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600 rounded-lg">
                        <i class="fas fa-chart-bar mr-3"></i>
                        Statistik
                    </a>
                    <a href="orders.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600 rounded-lg">
                        <i class="fas fa-shopping-bag mr-3"></i>
                        Riwayat Pesanan
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <!-- Profile Information Tab -->
            <div id="profile-tab" class="tab-content">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-6">Informasi Profil</h2>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <ul class="list-disc list-inside">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea name="address" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" name="update_profile" class="btn-primary px-6 py-2 rounded-lg">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Tab -->
            <div id="password-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-6">Ubah Password</h2>
                    
                    <?php if (!empty($password_errors)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <ul class="list-disc list-inside">
                                <?php foreach ($password_errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini *</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru *</label>
                            <input type="password" name="new_password" required minlength="6"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <p class="text-sm text-gray-500 mt-1">Minimal 6 karakter</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru *</label>
                            <input type="password" name="confirm_password" required minlength="6"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" name="change_password" class="btn-primary px-6 py-2 rounded-lg">
                                <i class="fas fa-key mr-2"></i>
                                Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistics Tab -->
            <div id="statistics-tab" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-shopping-bag text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600">Total Pesanan</p>
                                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_orders']; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-money-bill-wave text-green-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600">Total Belanja</p>
                                    <p class="text-2xl font-bold text-gray-900"><?php echo formatPrice($stats['total_spent']); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-yellow-100 rounded-full">
                                    <i class="fas fa-clock text-yellow-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600">Pesanan Pending</p>
                                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending_orders']; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-star text-purple-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600">Review Diberikan</p>
                                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_reviews']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold mb-4">Pesanan Terbaru</h3>
                        
                        <?php if (empty($recent_orders)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">Belum ada pesanan</p>
                                <a href="products.php" class="btn-primary inline-block mt-4 px-6 py-2 rounded-lg">
                                    Mulai Belanja
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="text-left py-3">Kode Pesanan</th>
                                            <th class="text-left py-3">Tanggal</th>
                                            <th class="text-left py-3">Total</th>
                                            <th class="text-left py-3">Status</th>
                                            <th class="text-left py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_orders as $order): ?>
                                            <tr class="border-b">
                                                <td class="py-3">
                                                    <span class="font-mono text-sm"><?php echo htmlspecialchars($order['transaction_code']); ?></span>
                                                </td>
                                                <td class="py-3">
                                                    <?php echo date('d/m/Y', strtotime($order['created_at'])); ?>
                                                </td>
                                                <td class="py-3">
                                                    <?php echo formatPrice($order['total_amount']); ?>
                                                </td>
                                                <td class="py-3">
                                                    <?php
                                                    $status_colors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'paid' => 'bg-blue-100 text-blue-800',
                                                        'confirmed' => 'bg-green-100 text-green-800',
                                                        'shipped' => 'bg-purple-100 text-purple-800',
                                                        'delivered' => 'bg-green-100 text-green-800',
                                                        'cancelled' => 'bg-red-100 text-red-800'
                                                    ];
                                                    $status_names = [
                                                        'pending' => 'Menunggu Pembayaran',
                                                        'paid' => 'Dibayar',
                                                        'confirmed' => 'Dikonfirmasi',
                                                        'shipped' => 'Dikirim',
                                                        'delivered' => 'Selesai',
                                                        'cancelled' => 'Dibatalkan'
                                                    ];
                                                    ?>
                                                    <span class="px-2 py-1 rounded-full text-xs <?php echo $status_colors[$order['status']]; ?>">
                                                        <?php echo $status_names[$order['status']]; ?>
                                                    </span>
                                                </td>
                                                <td class="py-3">
                                                    <a href="order-detail.php?code=<?php echo $order['transaction_code']; ?>" 
                                                       class="text-pink-600 hover:text-pink-800 text-sm">
                                                        Lihat Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4 text-center">
                                <a href="orders.php" class="text-pink-600 hover:text-pink-800">
                                    Lihat Semua Pesanan →
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all tab links
    document.querySelectorAll('.tab-link').forEach(link => {
        link.classList.remove('active', 'bg-pink-50', 'text-pink-600');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active class to clicked tab link
    event.target.closest('.tab-link').classList.add('active', 'bg-pink-50', 'text-pink-600');
}
</script>

<style>
.tab-link.active {
    background-color: #fdf2f8;
    color: #ec4899;
}
</style>

<?php include 'includes/footer.php'; ?>
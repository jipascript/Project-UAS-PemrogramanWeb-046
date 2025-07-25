<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get all users with safe column selection
try {
    $query = "SELECT id, name, email, role, 
                     IFNULL(is_active, 1) as is_active,
                     IFNULL(created_at, NOW()) as created_at
              FROM users 
              ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback query if some columns don't exist
    $query = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add default values for missing columns
    foreach ($users as &$user) {
        if (!isset($user['is_active'])) {
            $user['is_active'] = 1;
        }
        if (!isset($user['created_at'])) {
            $user['created_at'] = date('Y-m-d H:i:s');
        }
    }
}

$page_title = 'Pengguna';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold font-heading">Pengguna</h1>
            <p class="text-gray-600 mt-2">Kelola semua pengguna sistem</p>
        </div>
        <div class="flex space-x-4">
            <select class="border border-gray-300 rounded-lg px-4 py-2">
                <option>Semua Role</option>
                <option>Admin</option>
                <option>Customer</option>
            </select>
            <a href="add-user.php" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Pengguna
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pengguna
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bergabung
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada pengguna</p>
                                <p class="text-sm">Pengguna akan muncul di sini setelah mereka mendaftar</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full border-2 border-gray-200" 
                                             src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=e91e63&color=fff" 
                                             alt="<?php echo htmlspecialchars($user['name']); ?>">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($user['name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ID: <?php echo $user['id']; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                             <?php echo $user['role'] == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $is_active = isset($user['is_active']) ? $user['is_active'] : 1;
                                $status_class = $is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                $status_text = $is_active ? 'Aktif' : 'Tidak Aktif';
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php 
                                $created_at = $user['created_at'] ?? date('Y-m-d H:i:s');
                                echo date('d M Y', strtotime($created_at)); 
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="edit-user.php?id=<?php echo $user['id']; ?>" 
                                   class="text-indigo-600 hover:text-indigo-900 mr-3 transition-colors" 
                                   title="Edit pengguna">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($user['role'] != 'admin' && $user['id'] != $_SESSION['user_id']): ?>
                                <a href="delete-user.php?id=<?php echo $user['id']; ?>" 
                                   class="text-red-600 hover:text-red-900 transition-colors" 
                                   title="Hapus pengguna"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

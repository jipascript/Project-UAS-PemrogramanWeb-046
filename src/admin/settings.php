<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $settings = [
            'site_name' => $_POST['site_name'] ?? '',
            'site_description' => $_POST['site_description'] ?? '',
            'contact_email' => $_POST['contact_email'] ?? '',
            'contact_phone' => $_POST['contact_phone'] ?? '',
            'theme_color' => $_POST['theme_color'] ?? 'pink',
            'email_notifications' => isset($_POST['email_notifications']) ? '1' : '0',
            'push_notifications' => isset($_POST['push_notifications']) ? '1' : '0',
            'two_factor_auth' => isset($_POST['two_factor_auth']) ? '1' : '0',
            'currency' => $_POST['currency'] ?? 'IDR',
            'timezone' => $_POST['timezone'] ?? 'Asia/Jakarta',
            'items_per_page' => $_POST['items_per_page'] ?? '12',
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? '1' : '0',
            'allow_registration' => isset($_POST['allow_registration']) ? '1' : '0'
        ];
        
        // Handle password change
        if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            if ($newPassword !== $confirmPassword) {
                throw new Exception('Password baru dan konfirmasi tidak cocok');
            }
            
            // Verify current password
            $userQuery = "SELECT password FROM users WHERE id = ?";
            $stmt = $db->prepare($userQuery);
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!password_verify($currentPassword, $user['password'])) {
                throw new Exception('Password saat ini salah');
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePasswordQuery = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $db->prepare($updatePasswordQuery);
            $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
        }
        
        // Update settings
        foreach ($settings as $key => $value) {
            $updateQuery = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
            $stmt = $db->prepare($updateQuery);
            $stmt->execute([$value, $key]);
        }
        
        setFlashMessage('success', 'Pengaturan berhasil disimpan!');
        header('Location: settings.php');
        exit();
        
    } catch (Exception $e) {
        setFlashMessage('error', $e->getMessage());
    }
}

// Get current settings
$settingsData = [];
try {
    $query = "SELECT setting_key, setting_value FROM settings";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $settingsData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    // Table doesn't exist, create it
    try {
        $createTableQuery = "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(255) NOT NULL UNIQUE,
            setting_value TEXT,
            setting_type VARCHAR(50) DEFAULT 'text',
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $db->exec($createTableQuery);
        
        // Insert default settings
        $defaultSettingsData = [
            ['site_name', 'Merona Shop', 'text', 'Nama toko'],
            ['site_description', 'Toko online terpercaya dengan berbagai produk berkualitas. Aman dan Terpercaya', 'textarea', 'Deskripsi toko'],
            ['contact_email', 'admin@merona.com', 'email', 'Email kontak'],
            ['contact_phone', '+62 812 3456 7890', 'tel', 'Nomor telepon'],
            ['theme_color', 'pink', 'text', 'Warna tema'],
            ['logo_url', 'https://via.placeholder.com/64', 'text', 'URL logo toko'],
            ['email_notifications', '1', 'checkbox', 'Notifikasi email'],
            ['push_notifications', '0', 'checkbox', 'Notifikasi push'],
            ['two_factor_auth', '0', 'checkbox', 'Two-factor authentication']
        ];
        
        foreach ($defaultSettingsData as $setting) {
            $insertQuery = "INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($insertQuery);
            $stmt->execute($setting);
        }
        
        // Now get the settings
        $query = "SELECT setting_key, setting_value FROM settings";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $settingsData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
    } catch (Exception $e2) {
        // Still failed, use defaults
        $settingsData = [];
    }
}

// Set default values if settings don't exist
$defaultSettings = [
    'site_name' => 'Merona Shop',
    'site_description' => 'Toko online terpercaya dengan berbagai produk berkualitas. Aman dan Terpercaya',
    'contact_email' => 'admin@merona.com',
    'contact_phone' => '+62 812 3456 7890',
    'theme_color' => 'pink',
    'logo_url' => 'https://via.placeholder.com/64',
    'email_notifications' => '1',
    'push_notifications' => '0',
    'two_factor_auth' => '0',
    'currency' => 'IDR',
    'timezone' => 'Asia/Jakarta',
    'items_per_page' => '12',
    'maintenance_mode' => '0',
    'allow_registration' => '1'
];

$settings = array_merge($defaultSettings, $settingsData);

$page_title = 'Pengaturan';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold font-heading">Pengaturan</h1>
            <p class="text-gray-600 mt-2">Konfigurasi sistem dan preferensi</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Settings Content -->
        <div class="lg:col-span-3 space-y-6">
            <form method="POST" id="settingsForm">
                <!-- General Settings -->
                <div id="general" class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Pengaturan Umum</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Toko</label>
                            <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="site_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Kontak</label>
                            <input type="email" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email']); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="tel" name="contact_phone" value="<?php echo htmlspecialchars($settings['contact_phone']); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div id="appearance" class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Pengaturan Tampilan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Logo Toko</label>
                            <div class="flex items-center space-x-4">
                                <img id="logoPreview" src="<?php echo htmlspecialchars($settings['logo_url']); ?>" alt="Logo" class="w-16 h-16 rounded-lg object-cover border-2 border-gray-200">
                                <div>
                                    <input type="file" id="logoInput" accept="image/*" class="hidden">
                                    <button type="button" id="uploadLogoBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                                        <i class="fas fa-upload mr-2"></i>Upload Logo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div id="notifications" class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Pengaturan Notifikasi</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Notifikasi Email</p>
                                <p class="text-sm text-gray-500">Terima notifikasi pesanan baru via email</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications" value="1" class="sr-only peer" <?php echo $settings['email_notifications'] ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Notifikasi Push</p>
                                <p class="text-sm text-gray-500">Terima notifikasi browser untuk aktivitas penting</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="push_notifications" value="1" class="sr-only peer" <?php echo $settings['push_notifications'] ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div id="security" class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Pengaturan Keamanan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ubah Password</label>
                            <div class="space-y-2">
                                <input type="password" name="current_password" placeholder="Password saat ini" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <input type="password" name="new_password" placeholder="Password baru" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <input type="password" name="confirm_password" placeholder="Konfirmasi password baru" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Two-Factor Authentication</p>
                                <p class="text-sm text-gray-500">Tambahkan lapisan keamanan ekstra</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="two_factor_auth" value="1" class="sr-only peer" <?php echo $settings['two_factor_auth'] ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-8 py-3 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Dihapus: Theme color selection script
// Logo upload functionality
document.getElementById('uploadLogoBtn').addEventListener('click', function() {
    document.getElementById('logoInput').click();
});

document.getElementById('logoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
        
        // Upload image
        const formData = new FormData();
        formData.append('logo', file);
        
        fetch('upload_logo.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', 'Logo berhasil diupload!');
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            showNotification('error', 'Terjadi kesalahan saat upload logo');
        });
    }
});

// Smooth scrolling for navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Show notifications
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 
        'bg-red-100 text-red-800 border border-red-200'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remove notification after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Form validation
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const newPassword = document.querySelector('input[name="new_password"]').value;
    const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
    
    if (newPassword && newPassword !== confirmPassword) {
        e.preventDefault();
        showNotification('error', 'Password baru dan konfirmasi tidak cocok!');
    }
});
</script>

<?php include 'includes/footer.php'; ?>

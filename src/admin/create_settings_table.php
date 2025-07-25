<?php
// Create settings table and populate with default data
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // If not logged in as admin, redirect to login
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit();
    }
    // If not admin, show error
    die('Access denied. Admin privileges required.');
}

$database = new Database();
$db = $database->getConnection();

$messages = [];

if (!$db) {
    die("Database connection failed!");
}

try {
    // Create settings table
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
    $messages[] = "✅ Tabel settings berhasil dibuat.";
    
    // Insert default settings if not exists
    $defaultSettings = [
        ['site_name', 'Merona Shop', 'text', 'Nama toko'],
        ['site_description', 'Toko online terpercaya dengan berbagai produk berkualitas. Aman dan Terpercaya', 'textarea', 'Deskripsi toko'],
        ['contact_email', 'admin@merona.com', 'email', 'Email kontak'],
        ['contact_phone', '+62 812 3456 7890', 'tel', 'Nomor telepon'],
        ['theme_color', 'pink', 'text', 'Warna tema'],
        ['logo_url', 'https://via.placeholder.com/64', 'text', 'URL logo toko'],
        ['email_notifications', '1', 'checkbox', 'Notifikasi email'],
        ['push_notifications', '0', 'checkbox', 'Notifikasi push'],
        ['two_factor_auth', '0', 'checkbox', 'Two-factor authentication'],
        ['currency', 'IDR', 'text', 'Mata uang'],
        ['timezone', 'Asia/Jakarta', 'text', 'Zona waktu'],
        ['items_per_page', '12', 'number', 'Item per halaman'],
        ['maintenance_mode', '0', 'checkbox', 'Mode maintenance'],
        ['allow_registration', '1', 'checkbox', 'Izinkan pendaftaran']
    ];
    
    foreach ($defaultSettings as $setting) {
        $checkQuery = "SELECT COUNT(*) FROM settings WHERE setting_key = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute([$setting[0]]);
        
        if ($stmt->fetchColumn() == 0) {
            $insertQuery = "INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($insertQuery);
            $stmt->execute($setting);
            $messages[] = "✅ Pengaturan '{$setting[3]}' berhasil ditambahkan.";
        } else {
            $messages[] = "ℹ️ Pengaturan '{$setting[3]}' sudah ada.";
        }
    }
    
    $messages[] = "🎉 Proses inisialisasi pengaturan selesai!";
    
} catch (Exception $e) {
    $messages[] = "❌ Error: " . $e->getMessage();
}

$page_title = 'Inisialisasi Pengaturan';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold font-heading">Inisialisasi Pengaturan</h1>
            <p class="text-gray-600 mt-2">Membuat tabel pengaturan dan mengisi data default</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Hasil Inisialisasi:</h2>
            
            <?php foreach ($messages as $message): ?>
                <div class="mb-3 p-3 rounded-lg <?php echo strpos($message, '✅') !== false ? 'bg-green-100 text-green-800' : (strpos($message, '❌') !== false ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'); ?>">
                    <?php echo $message; ?>
                </div>
            <?php endforeach; ?>
            
            <div class="mt-6 pt-6 border-t">
                <a href="settings.php" class="btn-primary mr-4">
                    <i class="fas fa-cog mr-2"></i>Buka Pengaturan
                </a>
                <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-home mr-2"></i>Ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

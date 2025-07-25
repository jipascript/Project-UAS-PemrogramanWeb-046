<?php
// Fix database schema untuk products table
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

$messages = [];

if (!$db) {
    die("Database connection failed!");
}

try {
    // Cek dan tambahkan kolom image_url jika tidak ada
    $checkQuery = "SHOW COLUMNS FROM products LIKE 'image_url'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        $alterQuery = "ALTER TABLE products ADD COLUMN image_url VARCHAR(255) DEFAULT NULL AFTER description";
        $db->exec($alterQuery);
        $messages[] = "✅ Kolom 'image_url' berhasil ditambahkan ke tabel products.";
    } else {
        $messages[] = "ℹ️ Kolom 'image_url' sudah ada di tabel products.";
    }
    
    // Cek dan tambahkan kolom is_active jika tidak ada
    $checkQuery = "SHOW COLUMNS FROM products LIKE 'is_active'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        $alterQuery = "ALTER TABLE products ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER image_url";
        $db->exec($alterQuery);
        $messages[] = "✅ Kolom 'is_active' berhasil ditambahkan ke tabel products.";
    } else {
        $messages[] = "ℹ️ Kolom 'is_active' sudah ada di tabel products.";
    }
    
    // Cek dan tambahkan kolom created_at jika tidak ada
    $checkQuery = "SHOW COLUMNS FROM products LIKE 'created_at'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        $alterQuery = "ALTER TABLE products ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER is_active";
        $db->exec($alterQuery);
        $messages[] = "✅ Kolom 'created_at' berhasil ditambahkan ke tabel products.";
    } else {
        $messages[] = "ℹ️ Kolom 'created_at' sudah ada di tabel products.";
    }
    
    // Update produk yang image_url nya kosong
    $updateQuery = "UPDATE products SET image_url = 'https://via.placeholder.com/200x200/ec4899/ffffff?text=Product' WHERE image_url IS NULL OR image_url = ''";
    $affectedRows = $db->exec($updateQuery);
    if ($affectedRows > 0) {
        $messages[] = "✅ Berhasil mengupdate $affectedRows produk dengan gambar placeholder.";
    } else {
        $messages[] = "ℹ️ Tidak ada produk yang perlu diupdate gambarnya.";
    }
    
    // === PERBAIKAN TABEL CATEGORIES ===
    
    // Cek dan tambahkan kolom is_active untuk categories jika tidak ada
    $checkQuery = "SHOW COLUMNS FROM categories LIKE 'is_active'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        $alterQuery = "ALTER TABLE categories ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER description";
        $db->exec($alterQuery);
        $messages[] = "✅ Kolom 'is_active' berhasil ditambahkan ke tabel categories.";
    } else {
        $messages[] = "ℹ️ Kolom 'is_active' sudah ada di tabel categories.";
    }
    
    // Cek dan tambahkan kolom created_at untuk categories jika tidak ada
    $checkQuery = "SHOW COLUMNS FROM categories LIKE 'created_at'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        $alterQuery = "ALTER TABLE categories ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER is_active";
        $db->exec($alterQuery);
        $messages[] = "✅ Kolom 'created_at' berhasil ditambahkan ke tabel categories.";
    } else {
        $messages[] = "ℹ️ Kolom 'created_at' sudah ada di tabel categories.";
    }
    
    // === PERBAIKAN TABEL USERS ===
    
    // Cek dan tambahkan kolom is_active untuk users jika tidak ada
    $checkQuery = "SHOW COLUMNS FROM users LIKE 'is_active'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        $alterQuery = "ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER role";
        $db->exec($alterQuery);
        $messages[] = "✅ Kolom 'is_active' berhasil ditambahkan ke tabel users.";
    } else {
        $messages[] = "ℹ️ Kolom 'is_active' sudah ada di tabel users.";
    }
    
    // Cek dan tambahkan kolom created_at untuk users jika tidak ada
    $checkQuery = "SHOW COLUMNS FROM users LIKE 'created_at'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        $alterQuery = "ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER is_active";
        $db->exec($alterQuery);
        $messages[] = "✅ Kolom 'created_at' berhasil ditambahkan ke tabel users.";
    } else {
        $messages[] = "ℹ️ Kolom 'created_at' sudah ada di tabel users.";
    }
    
    // === PERBAIKAN TABEL SETTINGS ===
    
    // Cek dan buat tabel settings jika belum ada
    $checkTableQuery = "SHOW TABLES LIKE 'settings'";
    $stmt = $db->prepare($checkTableQuery);
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        $createTableQuery = "CREATE TABLE settings (
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
        
        // Insert default settings
        $defaultSettings = [
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
        
        foreach ($defaultSettings as $setting) {
            $insertQuery = "INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($insertQuery);
            $stmt->execute($setting);
            $messages[] = "✅ Pengaturan '{$setting[3]}' berhasil ditambahkan.";
        }
    } else {
        $messages[] = "ℹ️ Tabel settings sudah ada.";
    }
    
    $messages[] = "🎉 Proses perbaikan database selesai!";
    
} catch (Exception $e) {
    $messages[] = "❌ Error: " . $e->getMessage();
}

$page_title = 'Perbaikan Database';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold font-heading">Perbaikan Database</h1>
            <p class="text-gray-600 mt-2">Memperbaiki struktur database untuk tabel products</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Hasil Perbaikan:</h2>
            
            <?php foreach ($messages as $message): ?>
                <div class="mb-3 p-3 rounded-lg <?php echo strpos($message, '✅') !== false ? 'bg-green-100 text-green-800' : (strpos($message, '❌') !== false ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'); ?>">
                    <?php echo $message; ?>
                </div>
            <?php endforeach; ?>
            
            <div class="mt-6 pt-6 border-t">
                <a href="products.php" class="btn-primary mr-4">
                    <i class="fas fa-box mr-2"></i>Lihat Produk
                </a>
                <a href="categories.php" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-colors mr-4">
                    <i class="fas fa-tags mr-2"></i>Lihat Kategori
                </a>
                <a href="users.php" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg transition-colors mr-4">
                    <i class="fas fa-users mr-2"></i>Lihat Pengguna
                </a>
                <a href="settings.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors mr-4">
                    <i class="fas fa-cog mr-2"></i>Pengaturan
                </a>
                <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-home mr-2"></i>Ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();
try {
    $stmt = $db->query("DESCRIBE settings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

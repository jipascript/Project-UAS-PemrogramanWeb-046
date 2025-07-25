<?php
// Simple settings initialization
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

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
        $checkQuery = "SELECT COUNT(*) FROM settings WHERE setting_key = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute([$setting[0]]);
        
        if ($stmt->fetchColumn() == 0) {
            $insertQuery = "INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($insertQuery);
            $stmt->execute($setting);
        }
    }
    
    echo "Settings table initialized successfully!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Redirect to settings page
header('Location: settings.php');
exit();
?>

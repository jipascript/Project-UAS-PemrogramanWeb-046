<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

try {
    if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error');
    }
    
    $file = $_FILES['logo'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.');
    }
    
    // Check file size (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new Exception('File size too large. Maximum 2MB allowed.');
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'logo_' . time() . '_' . uniqid() . '.' . $extension;
    $uploadPath = $uploadDir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    // Update database
    $logoUrl = 'uploads/' . $filename;
    
    // Get old logo first
    $oldLogo = null;
    try {
        $oldLogoQuery = "SELECT setting_value FROM settings WHERE setting_key = 'logo_url'";
        $stmt = $db->prepare($oldLogoQuery);
        $stmt->execute();
        $oldLogo = $stmt->fetchColumn();
    } catch (Exception $e) {
        // Table might not exist, create it
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
    }
    
    // Update or insert logo URL
    try {
        $updateQuery = "UPDATE settings SET setting_value = ? WHERE setting_key = 'logo_url'";
        $stmt = $db->prepare($updateQuery);
        $stmt->execute([$logoUrl]);
        
        // Check if any row was affected
        if ($stmt->rowCount() == 0) {
            // Insert new record
            $insertQuery = "INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES ('logo_url', ?, 'text', 'URL logo toko')";
            $stmt = $db->prepare($insertQuery);
            $stmt->execute([$logoUrl]);
        }
    } catch (Exception $e) {
        // Try insert directly
        $insertQuery = "INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES ('logo_url', ?, 'text', 'URL logo toko') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $db->prepare($insertQuery);
        $stmt->execute([$logoUrl]);
    }
    
    if ($oldLogo && $oldLogo !== $logoUrl && file_exists('../' . $oldLogo)) {
        unlink('../' . $oldLogo);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Logo uploaded successfully',
        'logo_url' => $logoUrl
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

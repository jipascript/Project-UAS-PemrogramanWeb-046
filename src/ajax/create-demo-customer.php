<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

// Check if demo customer exists
$query = "SELECT id FROM users WHERE email = 'customer@merona.com'";
$stmt = $db->prepare($query);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    // Create demo customer
    $hashed_password = password_hash('customer', PASSWORD_DEFAULT);
    $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')";
    $stmt = $db->prepare($query);
    $stmt->execute(['Demo Customer', 'customer@merona.com', $hashed_password]);
}

echo json_encode(['success' => true]);
?>
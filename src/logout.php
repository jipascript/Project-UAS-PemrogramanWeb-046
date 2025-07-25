<?php
session_start();
require_once 'includes/functions.php';

if (isLoggedIn()) {
    logActivity($_SESSION['user_id'], 'User logged out');
}

// Clear session variables
session_unset();
session_destroy();

// Start a new session for the flash message
session_start();
setFlashMessage('success', 'Anda telah logout');
header('Location: merona-shop.php');
exit();
?>

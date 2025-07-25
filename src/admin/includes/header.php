<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Admin Panel - Merona Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ec4899;
            --primary-hover: #db2777;
            --secondary-color: #8b5cf6;
            --accent-color: #06b6d4;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --border-color: #e5e7eb;
        }
        
        * {
            font-family: "Inter", sans-serif;
        }
        
        body {
            background-color: var(--bg-light);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        /* Admin Panel Toggle Button - Circular floating button */
        .admin-panel-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            border-radius: 50%;
            box-shadow: 0 8px 25px rgba(236, 72, 153, 0.4);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 1001;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 3px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Pulse effect for the toggle button */
        .admin-panel-toggle::before {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 50%;
            background: inherit;
            opacity: 0.3;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(0.95);
                opacity: 0.3;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.1;
            }
            100% {
                transform: scale(0.95);
                opacity: 0.3;
            }
        }
        
        .admin-panel-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 35px rgba(236, 72, 153, 0.6);
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        .admin-panel-toggle:active {
            transform: scale(0.95);
        }
        
        /* Hamburger lines */
        .hamburger {
            display: flex;
            flex-direction: column;
            gap: 4px;
            transition: all 0.3s ease;
        }
        
        .hamburger-line {
            width: 24px;
            height: 3px;
            background: white;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .admin-panel-toggle.active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translate(7px, 7px);
        }
        
        .admin-panel-toggle.active .hamburger-line:nth-child(2) {
            opacity: 0;
        }
        
        .admin-panel-toggle.active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
        }
        
        /* Sidebar Panel */
        .sidebar {
            position: fixed;
            top: 0;
            left: -320px;
            width: 320px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 4px 0 20px rgba(236, 72, 153, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0 20px 20px 0;
        }
        
        .sidebar.active {
            left: 0;
            box-shadow: 4px 0 30px rgba(236, 72, 153, 0.5);
        }
        
        /* Sidebar Header */
        .sidebar-header {
            background: rgba(255, 255, 255, 0.15);
            padding: 25px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .sidebar-header h2 {
            color: white;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .close-btn {
            width: 35px;
            height: 35px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .close-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.1);
        }
        
        .close-btn i {
            color: white;
            font-size: 16px;
        }
        
        /* Navigation Links */
        .sidebar-nav-link {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 12px;
            margin: 4px 12px;
            font-weight: 600;
            font-size: 16px;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transition: left 0.3s ease;
        }
        
        .sidebar-nav-link:hover::before {
            left: 0;
        }
        
        .sidebar-nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(8px);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
        }
        
        .sidebar-nav-link.active {
            background: white;
            color: var(--primary-color);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
            transform: translateX(8px);
            font-weight: 700;
        }
        
        .sidebar-nav-link.logout {
            margin-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 20px;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .sidebar-nav-link.logout:hover {
            background: rgba(255, 69, 69, 0.2);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 69, 69, 0.3);
        }
        
        .sidebar-nav-link i {
            width: 24px;
            margin-right: 16px;
            font-size: 18px;
            text-align: center;
        }
        
        .sidebar-nav-link span {
            font-size: 16px;
            position: relative;
            z-index: 1;
        }
        
        /* Main Content Area */
        .main-content {
            margin-left: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: var(--bg-light);
            min-height: 100vh;
            padding: 80px 20px 20px 20px;
            position: relative;
        }
        
        /* Overlay for darkening main content */
        .admin-panel-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(2px);
        }
        
        .admin-panel-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            transition: all 0.3s ease;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.4);
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(236, 72, 153, 0.6);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 280px;
            }
            
            .main-content {
                padding: 70px 15px 15px 15px;
            }
        }
        
        /* Scroll customization */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Admin Navigation -->
    <!-- Admin Panel Toggle Button -->
<div id="adminPanelToggle" class="admin-panel-toggle" title="Admin Panel">
  <div class="hamburger">
    <div class="hamburger-line"></div>
    <div class="hamburger-line"></div>
    <div class="hamburger-line"></div>
  </div>
</div>

<!-- Admin Panel Overlay -->
<div id="adminPanelOverlay" class="admin-panel-overlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
        <div class="close-btn" id="adminPanelClose">
            <i class="fas fa-times"></i>
        </div>
    </div>
    
    <nav class="px-4 py-6">
        <div class="space-y-2">
            <a href="dashboard.php" class="sidebar-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="products.php" class="sidebar-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i>
                <span>Manajemen Produk</span>
            </a>
            <a href="categories.php" class="sidebar-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i>
                <span>Kategori</span>
            </a>
            <a href="orders.php" class="sidebar-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                <i class="fas fa-receipt"></i>
                <span>Transaksi</span>
            </a>
            <a href="users.php" class="sidebar-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Pengguna</span>
            </a>
            <a href="reports.php" class="sidebar-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan</span>
            </a>
            <a href="settings.php" class="sidebar-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            <a href="../logout.php" class="sidebar-nav-link logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</div>

<script>
  const adminPanelToggle = document.getElementById('adminPanelToggle');
  const adminPanelOverlay = document.getElementById('adminPanelOverlay');
  const sidebar = document.getElementById('sidebar');
  const adminPanelClose = document.getElementById('adminPanelClose');

  function openAdminPanel() {
    sidebar.classList.add('active');
    adminPanelOverlay.classList.add('active');
    adminPanelToggle.classList.add('active');
  }

  function closeAdminPanel() {
    sidebar.classList.remove('active');
    adminPanelOverlay.classList.remove('active');
    adminPanelToggle.classList.remove('active');
  }

  adminPanelToggle.addEventListener('click', function(e) {
    e.stopPropagation();
    if (sidebar.classList.contains('active')) {
      closeAdminPanel();
    } else {
      openAdminPanel();
    }
  });
  
  adminPanelClose.addEventListener('click', closeAdminPanel);
  adminPanelOverlay.addEventListener('click', closeAdminPanel);
  
  // Close admin panel on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeAdminPanel();
    }
  });
</script>

    <!-- Top Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-8 ml-16">
                    <a href="../index.php" class="text-2xl font-bold text-pink-600">
                        <i class="fas fa-shopping-bag mr-2"></i>Merona
                    </a>
                    <span class="text-gray-500">Admin Panel</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="text-gray-600 hover:text-pink-600">
                        <i class="fas fa-home mr-2"></i>Lihat Toko
                    </a>
                    
                    <div class="relative">
                        <button id="profileDropdown" class="flex items-center space-x-2 text-gray-700 hover:text-pink-600">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=e91e63&color=fff" 
                                 alt="Profile" class="w-8 h-8 rounded-full">
                            <span><?php echo $_SESSION['user_name']; ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        
                        <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2">
                            <a href="../profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="../logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <div class="p-6">
            <?php
            $flash = getFlashMessage();
            if ($flash):
            ?>
            <div class="mb-6">
                <div class="bg-<?php echo $flash['type'] === 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $flash['type'] === 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $flash['type'] === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                        <?php echo $flash['message']; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Content will be inserted here -->
                
    <script>
        // Profile dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const profileDropdown = document.getElementById('profileDropdown');
            const profileMenu = document.getElementById('profileMenu');
            
            if (profileDropdown && profileMenu) {
                profileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('hidden');
                });
                
                // Close profile menu when clicking outside
                document.addEventListener('click', function() {
                    if (!profileMenu.classList.contains('hidden')) {
                        profileMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>

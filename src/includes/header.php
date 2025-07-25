<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Merona Fashion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            font-family: "Inter", sans-serif;
        }

        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: "Poppins", sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 107, 0.3);
        }

        .card-product {
            transition: all 0.3s ease;
        }

        .card-product:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .notification-dot {
            width: 8px;
            height: 8px;
            background: #ff6b6b;
            border-radius: 50%;
            position: absolute;
            top: -2px;
            right: -2px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255, 107, 107, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0); }
        }

        .hero-section {
            background: linear-gradient(135deg, #ff6b6b 0%, #4ecdc4 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,138.7C672,149,768,203,864,213.3C960,224,1056,192,1152,165.3C1248,139,1344,117,1392,106.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat;
            bottom: -50%;
            right: -50%;
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #ff6b6b;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ff5252;
        }

        .flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Flash Messages -->
    <?php
    $flash = getFlashMessage();
    if ($flash):
    ?>
    <div class="flash-message">
        <div class="bg-<?php echo $flash['type'] === 'success' ? 'green' : 'red'; ?>-500 text-white px-6 py-4 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                    <?php echo $flash['message']; ?>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    <script>
        setTimeout(function() {
            const flashMessage = document.querySelector('.flash-message');
            if (flashMessage) {
                flashMessage.remove();
            }
        }, 5000);
    </script>
    <?php endif; ?>

    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-8">
                    <a href="merona-shop.php" class="text-2xl font-bold text-pink-500 font-heading">
                        <i class="fas fa-shopping-bag mr-2"></i>Merona
                    </a>
                    <div class="hidden md:flex space-x-6">
                        <a href="merona-shop.php" class="text-gray-700 hover:text-pink-500 transition">Home</a>
                        <a href="products.php" class="text-gray-700 hover:text-pink-500 transition">Products</a>
                        <a href="categories.php" class="text-gray-700 hover:text-pink-500 transition">Categories</a>
                        <a href="about.php" class="text-gray-700 hover:text-pink-500 transition">About</a>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative hidden md:block">
                        <form action="search.php" method="GET">
                            <input type="text" name="q" placeholder="Search products..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:border-pink-500 w-64">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </form>
                    </div>

                    <?php if (isLoggedIn()): ?>
                        <!-- Cart -->
                        <a href="cart.php" class="relative">
                            <i class="fas fa-shopping-cart text-gray-700 text-xl hover:text-pink-500 transition"></i>
                            <?php $cart_count = getCartCount(); ?>
                            <?php if ($cart_count > 0): ?>
                                <span class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    <?php echo $cart_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- User Menu -->
                        <div class="relative group">
                            <div class="flex items-center space-x-2 cursor-pointer">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User'); ?>&background=ff6b6b&color=fff" 
                                     alt="Profile" class="w-10 h-10 rounded-full">
                                <span class="text-gray-700">Hi, <?php echo $_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User'; ?></span>
                                <i class="fas fa-chevron-down text-gray-500"></i>
                            </div>
                            <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2">
                                <a href="dashboard.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>My Profile
                                </a>
                                <a href="orders.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-list-alt mr-2"></i>My Orders
                                </a>
                                <?php if (isAdmin()): ?>
                                    <a href="admin/" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i>Admin Panel
                                    </a>
                                <?php endif; ?>
                                <hr class="my-2">
                                <a href="logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Auth Buttons -->
                        <div class="flex space-x-2">
                            <a href="login.php" class="px-4 py-2 text-pink-500 border border-pink-500 rounded-lg hover:bg-pink-50 transition">
                                Login
                            </a>
                            <a href="register.php" class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition">
                                Register
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile Menu Button -->
                    <button class="md:hidden" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-gray-700 text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="bg-white w-64 h-full p-4">
            <div class="flex justify-between items-center mb-8">
                <div class="text-2xl font-bold text-pink-500">Merona</div>
                <button onclick="toggleMobileMenu()">
                    <i class="fas fa-times text-gray-700 text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <a href="merona-shop.php" class="block text-gray-700 hover:text-pink-500 transition">Home</a>
                <a href="products.php" class="block text-gray-700 hover:text-pink-500 transition">Products</a>
                <a href="categories.php" class="block text-gray-700 hover:text-pink-500 transition">Categories</a>
                <a href="about.php" class="block text-gray-700 hover:text-pink-500 transition">About</a>
                <?php if (isLoggedIn()): ?>
                    <hr class="my-4">
                    <a href="profile.php" class="block text-gray-700 hover:text-pink-500 transition">My Profile</a>
                    <a href="orders.php" class="block text-gray-700 hover:text-pink-500 transition">My Orders</a>
                    <a href="cart.php" class="block text-gray-700 hover:text-pink-500 transition">Cart</a>
                    <?php if (isAdmin()): ?>
                        <a href="admin/" class="block text-gray-700 hover:text-pink-500 transition">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php" class="block text-red-500 hover:text-pink-500 transition">Logout</a>
                <?php else: ?>
                    <hr class="my-4">
                    <a href="login.php" class="block text-gray-700 hover:text-pink-500 transition">Login</a>
                    <a href="register.php" class="block text-gray-700 hover:text-pink-500 transition">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
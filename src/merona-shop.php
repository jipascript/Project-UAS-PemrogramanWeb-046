<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$database = new Database();
$db = $database->getConnection();

// Get featured products (latest products)
$query = "SELECT p.*, c.name as category_name
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.stock > 0
          ORDER BY p.created_at DESC 
          LIMIT 8";
$stmt = $db->prepare($query);
$stmt->execute();
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$query = "SELECT * FROM categories ORDER BY name";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get cart count if user is logged in
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $cart_count = getCartCount($_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Merona - Fashion That Speaks You</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav id="navbar" class="bg-white shadow-lg sticky top-0 z-50">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center space-x-8">
            <div class="text-2xl font-bold text-pink-500 font-heading">
              <i class="fas fa-shopping-bag mr-2"></i>Merona
            </div>
            <div class="hidden md:flex space-x-6">
              <a
                href="index.php"
                class="text-gray-700 hover:text-pink-500 transition"
                >Home</a
              >
              <a
                href="products.php"
                class="text-gray-700 hover:text-pink-500 transition"
                >Products</a
              >
              <a
                href="#categories"
                class="text-gray-700 hover:text-pink-500 transition"
                >Categories</a
              >
              <a
                href="#about"
                class="text-gray-700 hover:text-pink-500 transition"
                >About</a
              >
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <div class="relative hidden md:block">
              <input
                type="text"
                placeholder="Search products..."
                class="pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:border-pink-500 w-64"
              />
              <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>

            <div
              class="relative cursor-pointer"
              onclick="showPage('notifications')"
            >
              <i
                class="fas fa-bell text-gray-700 text-xl hover:text-pink-500 transition"
              ></i>
              <div class="notification-dot"></div>
            </div>

            <div class="relative cursor-pointer" onclick="window.location.href='cart.php'">
              <i
                class="fas fa-shopping-cart text-gray-700 text-xl hover:text-pink-500 transition"
              ></i>
              <?php if ($cart_count > 0): ?>
              <span
                class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                ><?php echo $cart_count; ?></span
              >
              <?php endif; ?>
            </div>

            <!-- Login and Register buttons - always visible -->
            <div id="authButtons" class="flex space-x-2">
              <button
                onclick="window.location.href='login.php'"
                class="px-4 py-2 text-pink-500 border border-pink-500 rounded-lg hover:bg-pink-50 transition"
              >
                Login
              </button>
              <button
                onclick="window.location.href='register.php'"
                class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition"
              >
                Register
              </button>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
            <div id="userMenu" class="relative">
              <div
                class="flex items-center space-x-2 cursor-pointer"
                onclick="toggleDropdown()"
              >
                <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center">
                  <i class="fas fa-user text-pink-500"></i>
                </div>
                <span class="text-gray-700">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <i class="fas fa-chevron-down text-gray-500"></i>
              </div>
              <div
                id="dropdown"
                class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2"
              >
                <a
                  href="dashboard.php"
                  class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                  ><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a
                >
                <a
                  href="profile.php"
                  class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                  ><i class="fas fa-user mr-2"></i>My Profile</a
                >
                <a
                  href="orders.php"
                  class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                  ><i class="fas fa-list-alt mr-2"></i>My Orders</a
                >
                <?php if (isAdmin()): ?>
                <a
                  href="admin/dashboard.php"
                  class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                  ><i class="fas fa-cog mr-2"></i>Admin Panel</a
                >
                <?php endif; ?>
                <hr class="my-2" />
                <a
                  href="logout.php"
                  class="block px-4 py-2 text-red-500 hover:bg-gray-100"
                  ><i class="fas fa-sign-out-alt mr-2"></i>Logout</a
                >
              </div>
            </div>
            <?php endif; ?>

            <button class="md:hidden" onclick="toggleMobileMenu()">
              <i class="fas fa-bars text-gray-700 text-xl"></i>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Menu -->
    <div
      id="mobileMenu"
      class="hidden fixed inset-0 bg-black bg-opacity-50 z-50"
    >
      <div class="bg-white w-64 h-full p-4">
        <div class="flex justify-between items-center mb-8">
          <div class="text-2xl font-bold text-pink-500">Merona</div>
          <button onclick="toggleMobileMenu()">
            <i class="fas fa-times text-gray-700 text-xl"></i>
          </button>
        </div>
        <div class="space-y-4">
          <a
            href="#"
            onclick="showPage('home'); toggleMobileMenu()"
            class="block text-gray-700 hover:text-pink-500 transition"
            >Home</a
          >
          <a
            href="#"
            onclick="showPage('products'); toggleMobileMenu()"
            class="block text-gray-700 hover:text-pink-500 transition"
            >Products</a
          >
          <a
            href="#"
            onclick="showPage('categories'); toggleMobileMenu()"
            class="block text-gray-700 hover:text-pink-500 transition"
            >Categories</a
          >
          <a
            href="#"
            onclick="showPage('about'); toggleMobileMenu()"
            class="block text-gray-700 hover:text-pink-500 transition"
            >About</a
          >
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <div id="mainContent">
      <!-- Home Page -->
      <div id="homePage" class="page">
        <!-- Hero Section -->
        <section class="hero-section text-white py-20 px-4 relative">
          <div class="container mx-auto text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 font-heading">
              New Collection 2024
            </h1>
            <p class="text-xl mb-8">Discover the latest trends in fashion</p>
            <button
              onclick="window.location.href='products.php'"
              class="btn-primary px-8 py-3 text-lg font-semibold rounded-lg text-white"
            >
              Shop Now <i class="fas fa-arrow-right ml-2"></i>
            </button>
          </div>
          <div
            class="absolute inset-0 flex items-center justify-center opacity-10"
          >
            <img
              src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/803d6bc5-3610-4e94-a588-3c80005e3922.png"
              alt="Fashion model wearing trendy summer outfit with flowing dress and accessories"
              class="w-full h-full object-cover"
            />
          </div>
        </section>

        <!-- Featured Categories -->
        <section class="py-16 px-4">
          <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12 font-heading">
              Shop by Category
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
              <div
                class="category-card cursor-pointer group"
                onclick="window.location.href='products.php?category=Atasan'"
              >
                <div class="relative overflow-hidden rounded-lg">
                  <img
                    src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/d86cbde7-ffb2-4fa1-918c-97fa04f03467.png"
                    alt="Women's tops collection featuring various blouses and shirts in pastel colors"
                    class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                  />
                  <div
                    class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center"
                  >
                    <h3 class="text-white text-2xl font-bold">Atasan</h3>
                  </div>
                </div>
              </div>
              <div
                class="category-card cursor-pointer group"
                onclick="window.location.href='products.php?category=Bawahan'"
              >
                <div class="relative overflow-hidden rounded-lg">
                  <img
                    src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/63a3b8c8-4a6d-43e1-9914-98d3fde45435.png"
                    alt="Women's bottom wear collection including jeans, skirts and pants"
                    class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                  />
                  <div
                    class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center"
                  >
                    <h3 class="text-white text-2xl font-bold">Bawahan</h3>
                  </div>
                </div>
              </div>
              <div
                class="category-card cursor-pointer group"
                onclick="window.location.href='products.php?category=Dress'"
              >
                <div class="relative overflow-hidden rounded-lg">
                  <img
                    src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/3926d70a-1c24-4e3d-8238-476a662d6a7b.png"
                    alt="Elegant dress collection for various occasions from casual to formal"
                    class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                  />
                  <div
                    class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center"
                  >
                    <h3 class="text-white text-2xl font-bold">Dress</h3>
                  </div>
                </div>
              </div>
              <div
                class="category-card cursor-pointer group"
                onclick="window.location.href='products.php?category=Outer'"
              >
                <div class="relative overflow-hidden rounded-lg">
                  <img
                    src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/8dbc1c84-bebc-4339-b1aa-b990c53b72d8.png"
                    alt="Outerwear collection featuring jackets, cardigans and blazers"
                    class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                  />
                  <div
                    class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center"
                  >
                    <h3 class="text-white text-2xl font-bold">Outer</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Best Sellers -->
        <section class="py-16 px-4 bg-gray-100">
          <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12 font-heading">
              Best Sellers
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
              <?php if (!empty($featured_products)): ?>
                <?php foreach ($featured_products as $product): ?>
                  <div class="card-product bg-white rounded-lg overflow-hidden cursor-pointer" onclick="window.location.href='product-detail.php?id=<?= $product['id'] ?>'">
                    <div class="relative">
                      <img
                        src="<?= !empty($product['image']) ? htmlspecialchars($product['image']) : 'https://via.placeholder.com/300x300?text=No+Image' ?>"
                        alt="<?= htmlspecialchars($product['name']) ?>"
                        class="w-full h-64 object-cover"
                      />
                      <?php if ($product['stock'] <= 5 && $product['stock'] > 0): ?>
                        <span class="absolute top-2 right-2 bg-orange-500 text-white px-2 py-1 text-xs rounded">Low Stock</span>
                      <?php elseif ($product['stock'] == 0): ?>
                        <span class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 text-xs rounded">Out of Stock</span>
                      <?php endif; ?>
                    </div>
                    <div class="p-4">
                      <h3 class="font-semibold mb-2"><?= htmlspecialchars($product['name']) ?></h3>
                        <div class="flex items-center mb-2">
                          <div class="text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                          </div>
                          <span class="text-sm text-gray-500 ml-2">(4.5)</span>
                        </div>
                      <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-pink-500">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                        <button
                          onclick="event.stopPropagation(); addToCart(<?= $product['id'] ?>)"
                          class="bg-pink-500 text-white p-2 rounded-lg hover:bg-pink-600 transition <?= $product['stock'] == 0 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                          <?= $product['stock'] == 0 ? 'disabled' : '' ?>
                        >
                          <i class="fas fa-shopping-cart"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <!-- Fallback static products if no data -->
                <div class="card-product bg-white rounded-lg overflow-hidden cursor-pointer" onclick="window.location.href='products.php'">
                  <div class="relative">
                    <img
                      src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/13bc1239-f2ae-4f59-9168-e94fffffb91a.png"
                      alt="Floral print summer blouse with short sleeves and v-neck design"
                      class="w-full h-64 object-cover"
                    />
                  </div>
                  <div class="p-4">
                    <h3 class="font-semibold mb-2">Floral Summer Blouse</h3>
                    <div class="flex items-center mb-2">
                      <div class="text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                      </div>
                      <span class="text-sm text-gray-500 ml-2">(4.5)</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span class="text-lg font-bold text-pink-500">Rp 239.000</span>
                      <button class="bg-pink-500 text-white p-2 rounded-lg hover:bg-pink-600 transition">
                        <i class="fas fa-shopping-cart"></i>
                      </button>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </section>

        <!-- Promo Banner -->
        <section class="py-16 px-4">
          <div class="container mx-auto">
            <div
              class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl p-8 md:p-16 text-white text-center"
            >
              <h2 class="text-3xl md:text-4xl font-bold mb-4 font-heading">
                Special Weekend Sale!
              </h2>
              <p class="text-xl mb-8">Get up to 50% OFF on selected items</p>
              <button
                onclick="window.location.href='products.php'"
                class="bg-white text-pink-500 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition"
              >
                Shop Sale Items
              </button>
            </div>
          </div>
        </section>
      </div>

      <!-- Login Page -->
      <div id="loginPage" class="page hidden">
        <div class="min-h-screen flex items-center justify-center px-4">
          <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
              <div class="text-3xl font-bold text-pink-500 mb-2 font-heading">
                <i class="fas fa-shopping-bag mr-2"></i>Merona
              </div>
              <h2 class="text-2xl font-semibold text-gray-800">
                Welcome Back!
              </h2>
              <p class="text-gray-600 mt-2">Login to continue shopping</p>
            </div>

            <form onsubmit="login(event)">
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                  <i class="fas fa-envelope mr-2"></i>Email or Username
                </label>
                <input
                  type="text"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                  placeholder="Enter your email or username"
                  required
                />
              </div>

              <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">
                  <i class="fas fa-lock mr-2"></i>Password
                </label>
                <input
                  type="password"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                  placeholder="Enter your password"
                  required
                />
              </div>

              <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                  <input type="checkbox" class="mr-2" />
                  <span class="text-sm text-gray-600">Remember me</span>
                </label>
                <a href="#" class="text-sm text-pink-500 hover:text-pink-600"
                  >Forgot Password?</a
                >
              </div>

              <button
                type="submit"
                class="w-full btn-primary py-3 rounded-lg text-white font-semibold"
              >
                Login
              </button>
            </form>

            <div class="mt-6 text-center">
              <p class="text-gray-600">
                Don't have an account?
                <a
                  href="#"
                  onclick="showPage('register')"
                  class="text-pink-500 font-semibold hover:text-pink-600"
                  >Register</a
                >
              </p>
            </div>

            <div class="mt-6">
              <div class="relative">
                <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                  <span class="px-2 bg-white text-gray-500"
                    >Or continue with</span
                  >
                </div>
              </div>

              <div class="mt-6 grid grid-cols-2 gap-4">
                <button
                  class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                >
                  <i class="fab fa-google text-red-500 mr-2"></i> Google
                </button>
                <button
                  class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                >
                  <i class="fab fa-facebook-f text-blue-600 mr-2"></i> Facebook
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Register Page -->
      <div id="registerPage" class="page hidden">
        <div class="min-h-screen flex items-center justify-center px-4 py-8">
          <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
              <div class="text-3xl font-bold text-pink-500 mb-2 font-heading">
                <i class="fas fa-shopping-bag mr-2"></i>Merona
              </div>
              <h2 class="text-2xl font-semibold text-gray-800">
                Create Account
              </h2>
              <p class="text-gray-600 mt-2">Join our fashion community</p>
            </div>

            <form onsubmit="register(event)">
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                  <i class="fas fa-user mr-2"></i>Full Name
                </label>
                <input
                  type="text"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                  placeholder="Enter your full name"
                  required
                />
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                  <i class="fas fa-envelope mr-2"></i>Email
                </label>
                <input
                  type="email"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                  placeholder="Enter your email"
                  required
                />
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                  <i class="fas fa-phone mr-2"></i>Phone Number
                </label>
                <input
                  type="tel"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                  placeholder="Enter your phone number"
                  required
                />
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                  <i class="fas fa-lock mr-2"></i>Password
                </label>
                <input
                  type="password"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                  placeholder="Create a password"
                  required
                />
              </div>

              <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">
                  <i class="fas fa-lock mr-2"></i>Confirm Password
                </label>
                <input
                  type="password"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                  placeholder="Confirm your password"
                  required
                />
              </div>

              <div class="mb-6">
                <label class="flex items-center">
                  <input type="checkbox" class="mr-2" required />
                  <span class="text-sm text-gray-600"
                    >I agree to the
                    <a href="#" class="text-pink-500 hover:text-pink-600"
                      >Terms & Conditions</a
                    ></span
                  >
                </label>
              </div>

              <button
                type="submit"
                class="w-full btn-primary py-3 rounded-lg text-white font-semibold"
              >
                Create Account
              </button>
            </form>

            <div class="mt-6 text-center">
              <p class="text-gray-600">
                Already have an account?
                <a
                  href="#"
                  onclick="showPage('login')"
                  class="text-pink-500 font-semibold hover:text-pink-600"
                  >Login</a
                >
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Page -->
      <div id="productsPage" class="page hidden">
        <div class="container mx-auto px-4 py-8">
          <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:w-1/4">
              <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h3 class="text-lg font-semibold mb-4">Filters</h3>

                <!-- Category Filter -->
                <div class="mb-6">
                  <h4 class="font-semibold mb-3">Category</h4>
                  <div class="space-y-2">
                    <label class="flex items-center">
                      <input type="checkbox" class="mr-2 text-pink-500" />
                      <span>Atasan</span>
                    </label>
                    <label class="flex items-center">
                      <input type="checkbox" class="mr-2 text-pink-500" />
                      <span>Bawahan</span>
                    </label>
                    <label class="flex items-center">
                      <input type="checkbox" class="mr-2 text-pink-500" />
                      <span>Dress</span>
                    </label>
                    <label class="flex items-center">
                      <input type="checkbox" class="mr-2 text-pink-500" />
                      <span>Outer</span>
                    </label>
                  </div>
                </div>

                <!-- Price Range -->
                <div class="mb-6">
                  <h4 class="font-semibold mb-3">Price Range</h4>
                  <div class="flex items-center space-x-2">
                    <input
                      type="number"
                      placeholder="Min"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                    />
                    <span>-</span>
                    <input
                      type="number"
                      placeholder="Max"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                    />
                  </div>
                </div>

                <!-- Size Filter -->
                <div class="mb-6">
                  <h4 class="font-semibold mb-3">Size</h4>
                  <div class="grid grid-cols-3 gap-2">
                    <button
                      class="px-3 py-2 border border-gray-300 rounded hover:border-pink-500 hover:text-pink-500 transition"
                    >
                      S
                    </button>
                    <button
                      class="px-3 py-2 border border-gray-300 rounded hover:border-pink-500 hover:text-pink-500 transition"
                    >
                      M
                    </button>
                    <button
                      class="px-3 py-2 border border-gray-300 rounded hover:border-pink-500 hover:text-pink-500 transition"
                    >
                      L
                    </button>
                    <button
                      class="px-3 py-2 border border-gray-300 rounded hover:border-pink-500 hover:text-pink-500 transition"
                    >
                      XL
                    </button>
                    <button
                      class="px-3 py-2 border border-gray-300 rounded hover:border-pink-500 hover:text-pink-500 transition"
                    >
                      XXL
                    </button>
                  </div>
                </div>

                <button
                  class="w-full bg-pink-500 text-white py-2 rounded-lg hover:bg-pink-600 transition"
                >
                  Apply Filters
                </button>
              </div>
            </div>

            <!-- Products Grid -->
            <div class="lg:w-3/4">
              <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">All Products</h2>
                <div class="flex items-center space-x-4">
                  <span class="text-gray-600">Showing 12 results</span>
                  <select
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                  >
                    <option>Sort by: Newest</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                    <option>Best Selling</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Product Card 1 -->
                <div
                  class="card-product bg-white rounded-lg overflow-hidden cursor-pointer"
                  onclick="showPage('productDetail')"
                >
                  <div class="relative">
                    <img
                      src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/007c9294-dafc-4a62-ae95-0744e713897d.png"
                      alt="Trendy crop top with tie-front detail in pastel pink color"
                      class="w-full h-64 object-cover"
                    />
                    <span
                      class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 text-xs rounded"
                      >-30%</span
                    >
                  </div>
                  <div class="p-4">
                    <h3 class="font-semibold mb-2">Tie-Front Crop Top</h3>
                    <div class="flex items-center mb-2">
                      <div class="text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                      </div>
                      <span class="text-sm text-gray-500 ml-2">(5.0)</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <div>
                        <span class="text-lg font-bold text-pink-500"
                          >Rp 139.000</span
                        >
                        <span class="text-sm text-gray-500 line-through ml-2"
                          >Rp 199.000</span
                        >
                      </div>
                      <button
                        class="bg-pink-500 text-white p-2 rounded-lg hover:bg-pink-600 transition"
                      >
                        <i class="fas fa-shopping-cart"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Product Card 2 -->
                <div
                  class="card-product bg-white rounded-lg overflow-hidden cursor-pointer"
                  onclick="showPage('productDetail')"
                >
                  <div class="relative">
                    <img
                      src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/90b51dad-21e7-4952-8f85-e29e18345fbc.png"
                      alt="A-line pleated skirt in soft lavender color with midi length"
                      class="w-full h-64 object-cover"
                    />
                    <span
                      class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 text-xs rounded"
                      >New</span
                    >
                  </div>
                  <div class="p-4">
                    <h3 class="font-semibold mb-2">Pleated A-Line Skirt</h3>
                    <div class="flex items-center mb-2">
                      <div class="text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                      </div>
                      <span class="text-sm text-gray-500 ml-2">(4.0)</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span class="text-lg font-bold text-pink-500"
                        >Rp 259.000</span
                      >
                      <button
                        class="bg-pink-500 text-white p-2 rounded-lg hover:bg-pink-600 transition"
                      >
                        <i class="fas fa-shopping-cart"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Product Card 3 -->
                <div
                  class="card-product bg-white rounded-lg overflow-hidden cursor-pointer"
                  onclick="showPage('productDetail')"
                >
                  <div class="relative">
                    <img
                      src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/06d78a14-7571-4a5a-af2f-f2efd7458064.png"
                      alt="Boho style maxi dress with floral patterns and flowing fabric"
                      class="w-full h-64 object-cover"
                    />
                  </div>
                  <div class="p-4">
                    <h3 class="font-semibold mb-2">Boho Maxi Dress</h3>
                    <div class="flex items-center mb-2">
                      <div class="text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                      </div>
                      <span class="text-sm text-gray-500 ml-2">(4.5)</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span class="text-lg font-bold text-pink-500"
                        >Rp 429.000</span
                      >
                      <button
                        class="bg-pink-500 text-white p-2 rounded-lg hover:bg-pink-600 transition"
                      >
                        <i class="fas fa-shopping-cart"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Product Card 4 -->
                <div
                  class="card-product bg-white rounded-lg overflow-hidden cursor-pointer"
                  onclick="showPage('productDetail')"
                >
                  <div class="relative">
                    <img
                      src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/327309c1-de52-46b0-a6c2-6db1aac5e714.png"
                      alt="Lightweight denim jacket in classic blue wash with button closure"
                      class="w-full h-64 object-cover"
                    />
                    <span
                      class="absolute top-2 right-2 bg-orange-500 text-white px-2 py-1 text-xs rounded"
                      >Limited</span
                    >
                  </div>
                  <div class="p-4">
                    <h3 class="font-semibold mb-2">Classic Denim Jacket</h3>
                    <div class="flex items-center mb-2">
                      <div class="text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                      </div>
                      <span class="text-sm text-gray-500 ml-2">(5.0)</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span class="text-lg font-bold text-pink-500"
                        >Rp 459.000</span
                      >
                      <button
                        class="bg-pink-500 text-white p-2 rounded-lg hover:bg-pink-600 transition"
                      >
                        <i class="fas fa-shopping-cart"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Product Card 5 -->
                <div
                  class="card-product bg-white rounded-lg overflow-hidden cursor-pointer"
                  onclick="showPage('productDetail')"
                >
                  <div class="relative">
                    <img
                      src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/fe2bada6-75e0-4f7a-a871-9ce9ed23116a.png"
                      alt="Wide-leg palazzo pants in earth tone color with high waist design"
                      class="w-full h-64 object-cover"
                    />
                  </div>
                  <div class="p-4">
                    <h3 class="font-semibold mb-2">Wide-Leg Palazzo Pants</h3>
                    <div class="flex items-center mb-2">
                      <div class="text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                      </div>
                      <span class="text-sm text-gray-500 ml-2">(4.0)</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span class="text-lg font-bold text-pink-500"
                        >Rp 289.000</span
                      >
                      <button
                        class="bg-pink-500 text-white p-2 rounded-lg hover:bg-pink-600 transition"
                      >
                        <i class="fas fa-shopping-cart"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Product Card 6 -->
                <div
                  class="card-product bg-white rounded-lg overflow-hidden cursor-pointer"
                  onclick="showPage('productDetail')"
                >
                  <div class="relative">
                    <img
                      src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/858b43d1-1928-4919-aafc-d8e9e9e01ca0.png"
                      alt="Silk pattern shirt with abstract print in vibrant colors"
                      class="w-full h-64 object-cover"
                    />
                    <span
                      class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 text-xs rounded"
                      >-15%</span
                    >
                  </div>
                  <div class="p-4">
                    <h3 class="font-semibold mb-2">Silk Pattern Shirt</h3>
                    <div class="flex items-center mb-2">
                      <div class="text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                      </div>
                      <span class="text-sm text-gray-500 ml-2">(4.5)</span>
                    </div>
                    <div class="flex items-center justify-between">
                      <div>
                        <span class="text-lg font-bold text-pink-500"
                          >Rp 339.000</span
                        >
                        <span class="text-sm text-gray-500 line-through ml-2"
                          >Rp 399.000</span
                        >
                      </div>
                      <button
                        class="bg-pink-500 text-white p-2 rounded-lg hover:bg-pink-600 transition"
                      >
                        <i class="fas fa-shopping-cart"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pagination -->
              <div class="flex justify-center items-center space-x-2 mt-8">
                <button
                  class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
                >
                  <i class="fas fa-chevron-left"></i>
                </button>
                <button class="px-3 py-2 bg-pink-500 text-white rounded">
                  1
                </button>
                <button
                  class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
                >
                  2
                </button>
                <button
                  class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
                >
                  3
                </button>
                <button
                  class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
                >
                  <i class="fas fa-chevron-right"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Product Detail Page -->
      <div id="productDetailPage" class="page hidden">
        <div class="container mx-auto px-4 py-8">
          <!-- Breadcrumb -->
          <div class="text-sm text-gray-600 mb-6">
            <a href="#" onclick="showPage('home')" class="hover:text-pink-500"
              >Home</a
            >
            >
            <a
              href="#"
              onclick="showPage('products')"
              class="hover:text-pink-500"
              >Products</a
            >
            >
            <span class="text-gray-800">Floral Summer Blouse</span>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Images -->
            <div>
              <div class="mb-4">
                <img
                  src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/28929963-83dd-477b-bdcc-86d871ed10fa.png"
                  alt="Main product image showing floral summer blouse on model"
                  class="w-full rounded-lg"
                />
              </div>
              <div class="grid grid-cols-4 gap-2">
                <img
                  src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/59046edb-305a-4de4-8015-53bc85e8e746.png"
                  alt="Product thumbnail showing front view of floral blouse"
                  class="w-full rounded-lg cursor-pointer hover:opacity-80 transition"
                />
                <img
                  src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/e79ea3d0-09f8-4702-af54-22835be6cba3.png"
                  alt="Product thumbnail showing back view of floral blouse"
                  class="w-full rounded-lg cursor-pointer hover:opacity-80 transition"
                />
                <img
                  src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/0bfa7874-242f-4f50-afbf-d90c1c8e6475.png"
                  alt="Product thumbnail showing detail of fabric pattern"
                  class="w-full rounded-lg cursor-pointer hover:opacity-80 transition"
                />
                <img
                  src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/ca2994a9-078f-4568-bf37-77cb6191d36d.png"
                  alt="Product thumbnail showing blouse styled with accessories"
                  class="w-full rounded-lg cursor-pointer hover:opacity-80 transition"
                />
              </div>
            </div>

            <!-- Product Information -->
            <div>
              <h1 class="text-3xl font-bold mb-4">Floral Summer Blouse</h1>
              <div class="flex items-center mb-4">
                <div class="text-yellow-400">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star-half-alt"></i>
                </div>
                <span class="text-gray-600 ml-2">(4.5/5.0) - 128 reviews</span>
              </div>

              <div class="mb-6">
                <span class="text-3xl font-bold text-pink-500">Rp 239.000</span>
                <span class="text-xl text-gray-500 line-through ml-2"
                  >Rp 299.000</span
                >
                <span
                  class="ml-2 bg-red-500 text-white px-2 py-1 text-sm rounded"
                  >-20%</span
                >
              </div>

              <div class="mb-6">
                <h3 class="font-semibold mb-2">Size:</h3>
                <div class="flex space-x-2">
                  <button
                    class="px-4 py-2 border-2 border-gray-300 rounded hover:border-pink-500 focus:border-pink-500 focus:bg-pink-50 transition"
                  >
                    S
                  </button>
                  <button
                    class="px-4 py-2 border-2 border-pink-500 bg-pink-50 rounded"
                  >
                    M
                  </button>
                  <button
                    class="px-4 py-2 border-2 border-gray-300 rounded hover:border-pink-500 focus:border-pink-500 focus:bg-pink-50 transition"
                  >
                    L
                  </button>
                  <button
                    class="px-4 py-2 border-2 border-gray-300 rounded hover:border-pink-500 focus:border-pink-500 focus:bg-pink-50 transition"
                  >
                    XL
                  </button>
                </div>
              </div>

              <div class="mb-6">
                <h3 class="font-semibold mb-2">Color:</h3>
                <div class="flex space-x-2">
                  <button
                    class="w-8 h-8 bg-pink-200 rounded-full border-2 border-gray-300 hover:border-pink-500 focus:border-pink-500 transition"
                  ></button>
                  <button
                    class="w-8 h-8 bg-blue-200 rounded-full border-2 border-gray-300 hover:border-pink-500 focus:border-pink-500 transition"
                  ></button>
                  <button
                    class="w-8 h-8 bg-yellow-200 rounded-full border-2 border-gray-300 hover:border-pink-500 focus:border-pink-500 transition"
                  ></button>
                </div>
              </div>

              <div class="mb-6">
                <h3 class="font-semibold mb-2">Quantity:</h3>
                <div class="flex items-center space-x-2">
                  <button
                    class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
                    onclick="decreaseQty()"
                  >
                    <i class="fas fa-minus"></i>
                  </button>
                  <input
                    type="number"
                    id="qty"
                    value="1"
                    min="1"
                    class="w-16 text-center border border-gray-300 rounded py-1"
                  />
                  <button
                    class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
                    onclick="increaseQty()"
                  >
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                  Stock: 25 pcs available
                </p>
              </div>

              <div class="flex space-x-4 mb-6">
                <button
                  class="flex-1 btn-primary py-3 rounded-lg text-white font-semibold"
                >
                  <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                </button>
                <button
                  class="flex-1 bg-gray-800 text-white py-3 rounded-lg font-semibold hover:bg-gray-900 transition"
                >
                  Buy Now
                </button>
              </div>

              <div class="border-t pt-4">
                <button
                  class="text-gray-600 hover:text-pink-500 transition mr-4"
                >
                  <i class="far fa-heart mr-2"></i>Add to Wishlist
                </button>
                <button class="text-gray-600 hover:text-pink-500 transition">
                  <i class="fas fa-share-alt mr-2"></i>Share
                </button>
              </div>
            </div>
          </div>

          <!-- Product Details Tabs -->
          <div class="mt-12 bg-white rounded-lg shadow-lg p-6">
            <div class="border-b mb-6">
              <div class="flex space-x-8">
                <button
                  class="pb-2 border-b-2 border-pink-500 text-pink-500 font-semibold"
                >
                  Description
                </button>
                <button
                  class="pb-2 text-gray-600 hover:text-pink-500 transition"
                >
                  Size Chart
                </button>
                <button
                  class="pb-2 text-gray-600 hover:text-pink-500 transition"
                >
                  Reviews (128)
                </button>
              </div>
            </div>

            <div class="tab-content">
              <h3 class="font-semibold mb-2">Product Description</h3>
              <p class="text-gray-600 mb-4">
                Elevate your summer wardrobe with our beautiful Floral Summer
                Blouse. Made from lightweight, breathable fabric, this blouse
                features a delicate floral print that's perfect for any casual
                or semi-formal occasion.
              </p>
              <h4 class="font-semibold mb-2">Features:</h4>
              <ul class="list-disc list-inside text-gray-600 mb-4">
                <li>100% Premium Cotton</li>
                <li>Short sleeves with elastic cuffs</li>
                <li>V-neck design</li>
                <li>Relaxed fit</li>
                <li>Machine washable</li>
              </ul>
              <h4 class="font-semibold mb-2">Care Instructions:</h4>
              <p class="text-gray-600">
                Machine wash cold with similar colors. Do not bleach. Tumble dry
                low. Iron on low heat if needed.
              </p>
            </div>
          </div>

          <!-- Related Products -->
          <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6">You May Also Like</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
              <div
                class="card-product bg-white rounded-lg overflow-hidden cursor-pointer"
                onclick="showPage('productDetail')"
              >
                <img
                  src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/b17540f9-312a-4dc2-9886-63162d8bbde0.png"
                  alt="Similar style blouse in different pattern"
                  class="w-full h-48 object-cover"
                />
                <div class="p-4">
                  <h3 class="font-semibold mb-2 text-sm">Striped Summer Top</h3>
                  <span class="text-lg font-bold text-pink-500"
                    >Rp 219.000</span
                  >
                </div>
              </div>
              <div
                class="card-product bg-white rounded-lg overflow-hidden cursor-pointer"
                onclick="showPage('productDetail')"
              >
                <img
                  src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/6bb27294-4e11-4ff6-b8e8-ecef84370271.png"
                  alt="Casual t-shirt in solid color"
                  class="w-full h-48 object-cover"
                />
                <div class="p-4">
                  <h3 class="font-semibold mb-2 text-sm">Basic Cotton Tee</h3>
                  <span class="text-lg font-bold text-pink-500"
                    >Rp 159.000</span
                  >
                </div>
              </div>
              <div
                class="card-product bg-white rounded-lg overflow-hidden cursor-pointer"
                onclick="showPage('productDetail')"
              >
                <img
                  src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/6a804891-1cb5-44df-8e66-78da5c34729e.png"
                  alt="Elegant blouse for office wear"
                  class="w-full h-48 object-cover"
                />
                <div class="p-4">
                  <h3 class="font-semibold mb-2 text-sm">Office Blouse</h3>
                  <span class="text-lg font-bold text-pink-500"
                    >Rp 279.000</span
                  >
                </div>
              </div>
              <div
                class="card-product bg-white rounded-lg overflow-hidden cursor-pointer"
                onclick="showPage('productDetail')"
              >
                <img
                  src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/62298ad6-ef29-460b-a2b5-fb1e9a42ff42.png"
                  alt="Trendy crop top for summer"
                  class="w-full h-48 object-cover"
                />
                <div class="p-4">
                  <h3 class="font-semibold mb-2 text-sm">Summer Crop Top</h3>
                  <span class="text-lg font-bold text-pink-500"
                    >Rp 189.000</span
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Shopping Cart Page -->
      <div id="cartPage" class="page hidden">
        <div class="container mx-auto px-4 py-8">
          <h1 class="text-3xl font-bold mb-8">Shopping Cart (3)</h1>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
              <!-- Cart Items -->
              <div class="bg-white rounded-lg shadow p-6 mb-4">
                <div
                  class="flex flex-col md:flex-row items-center space-x-4 pb-4 border-b"
                >
                  <img
                    src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/8763f099-9b57-4a6e-81fe-fcf84b1793a3.png"
                    alt="Floral summer blouse product image"
                    class="w-24 h-24 object-cover rounded"
                  />
                  <div class="flex-1">
                    <h3 class="font-semibold">Floral Summer Blouse</h3>
                    <p class="text-gray-600 text-sm">Size: M | Color: Pink</p>
                    <div class="flex items-center space-x-2 mt-2">
                      <button
                        class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
                      >
                        <i class="fas fa-minus text-xs"></i>
                      </button>
                      <input
                        type="number"
                        value="2"
                        class="w-12 text-center border border-gray-300 rounded"
                      />
                      <button
                        class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
                      >
                        <i class="fas fa-plus text-xs"></i>
                      </button>
                    </div>
                  </div>
                  <div class="text-right mt-4 md:mt-0">
                    <p class="text-lg font-bold text-pink-500">Rp 478.000</p>
                    <p class="text-sm text-gray-500">Rp 239.000 x 2</p>
                    <button class="text-red-500 hover:text-red-600 mt-2">
                      <i class="fas fa-trash"></i> Remove
                    </button>
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-lg shadow p-6 mb-4">
                <div
                  class="flex flex-col md:flex-row items-center space-x-4 pb-4 border-b"
                >
                  <img
                    src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/86f5c25a-2f47-4712-90ec-79ab4156a024.png"
                    alt="Elegant midi dress product image"
                    class="w-24 h-24 object-cover rounded"
                  />
                  <div class="flex-1">
                    <h3 class="font-semibold">Elegant Midi Dress</h3>
                    <p class="text-gray-600 text-sm">Size: L | Color: Navy</p>
                    <div class="flex items-center space-x-2 mt-2">
                      <button
                        class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
                      >
                        <i class="fas fa-minus text-xs"></i>
                      </button>
                      <input
                        type="number"
                        value="1"
                        class="w-12 text-center border border-gray-300 rounded"
                      />
                      <button
                        class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
                      >
                        <i class="fas fa-plus text-xs"></i>
                      </button>
                    </div>
                  </div>
                  <div class="text-right mt-4 md:mt-0">
                    <p class="text-lg font-bold text-pink-500">Rp 399.000</p>
                    <p class="text-sm text-gray-500">Rp 399.000 x 1</p>
                    <button class="text-red-500 hover:text-red-600 mt-2">
                      <i class="fas fa-trash"></i> Remove
                    </button>
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col md:flex-row items-center space-x-4">
                  <img
                    src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/f7f2111a-7eef-4476-b064-e60b49dbc6f8.png"
                    alt="High-waist denim jeans product image"
                    class="w-24 h-24 object-cover rounded"
                  />
                  <div class="flex-1">
                    <h3 class="font-semibold">High-waist Denim Jeans</h3>
                    <p class="text-gray-600 text-sm">
                      Size: M | Color: Light Blue
                    </p>
                    <div class="flex items-center space-x-2 mt-2">
                      <button
                        class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
                      >
                        <i class="fas fa-minus text-xs"></i>
                      </button>
                      <input
                        type="number"
                        value="1"
                        class="w-12 text-center border border-gray-300 rounded"
                      />
                      <button
                        class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
                      >
                        <i class="fas fa-plus text-xs"></i>
                      </button>
                    </div>
                  </div>
                  <div class="text-right mt-4 md:mt-0">
                    <p class="text-lg font-bold text-pink-500">Rp 329.000</p>
                    <p class="text-sm text-gray-500">Rp 329.000 x 1</p>
                    <button class="text-red-500 hover:text-red-600 mt-2">
                      <i class="fas fa-trash"></i> Remove
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
              <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h2 class="text-xl font-bold mb-4">Order Summary</h2>

                <div class="space-y-2 mb-4">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal (4 items)</span>
                    <span>Rp 1.206.000</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Shipping</span>
                    <span>Rp 15.000</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Tax</span>
                    <span>Rp 0</span>
                  </div>
                </div>

                <div class="border-t pt-4 mb-6">
                  <div class="flex justify-between items-center">
                    <span class="text-xl font-bold">Total</span>
                    <span class="text-2xl font-bold text-pink-500"
                      >Rp 1.221.000</span
                    >
                  </div>
                </div>

                <button
                  onclick="showPage('checkout')"
                  class="w-full btn-primary py-3 rounded-lg text-white font-semibold mb-3"
                >
                  Proceed to Checkout
                </button>

                <button
                  onclick="showPage('products')"
                  class="w-full bg-gray-200 py-3 rounded-lg font-semibold hover:bg-gray-300 transition"
                >
                  Continue Shopping
                </button>

                <div class="mt-6">
                  <p class="text-sm text-gray-600 mb-2">Have a coupon?</p>
                  <div class="flex space-x-2">
                    <input
                      type="text"
                      placeholder="Enter code"
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                    />
                    <button
                      class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition"
                    >
                      Apply
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Checkout Page -->
      <div id="checkoutPage" class="page hidden">
        <div class="container mx-auto px-4 py-8">
          <h1 class="text-3xl font-bold mb-8">Checkout</h1>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
              <!-- Shipping Information -->
              <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Shipping Information</h2>
                <form>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-gray-700 font-semibold mb-2"
                        >First Name</label
                      >
                      <input
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div>
                      <label class="block text-gray-700 font-semibold mb-2"
                        >Last Name</label
                      >
                      <input
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div class="md:col-span-2">
                      <label class="block text-gray-700 font-semibold mb-2"
                        >Email Address</label
                      >
                      <input
                        type="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div class="md:col-span-2">
                      <label class="block text-gray-700 font-semibold mb-2"
                        >Phone Number</label
                      >
                      <input
                        type="tel"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div class="md:col-span-2">
                      <label class="block text-gray-700 font-semibold mb-2"
                        >Street Address</label
                      >
                      <input
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div>
                      <label class="block text-gray-700 font-semibold mb-2"
                        >City</label
                      >
                      <input
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div>
                      <label class="block text-gray-700 font-semibold mb-2"
                        >Postal Code</label
                      >
                      <input
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                  </div>
                </form>
              </div>

              <!-- Payment Method -->
              <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Payment Method</h2>
                <div class="space-y-3">
                  <label
                    class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-pink-500 transition"
                  >
                    <input type="radio" name="payment" class="mr-3" />
                    <i class="fas fa-university mr-3 text-gray-600"></i>
                    <span>Bank Transfer</span>
                  </label>
                  <label
                    class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-pink-500 transition"
                  >
                    <input type="radio" name="payment" class="mr-3" />
                    <i class="fas fa-wallet mr-3 text-gray-600"></i>
                    <span>E-Wallet (OVO, GoPay, DANA)</span>
                  </label>
                  <label
                    class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-pink-500 transition"
                  >
                    <input type="radio" name="payment" class="mr-3" />
                    <i class="fas fa-credit-card mr-3 text-gray-600"></i>
                    <span>Credit/Debit Card</span>
                  </label>
                  <label
                    class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-pink-500 transition"
                  >
                    <input type="radio" name="payment" class="mr-3" />
                    <i class="fas fa-truck mr-3 text-gray-600"></i>
                    <span>Cash on Delivery (COD)</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
              <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h2 class="text-xl font-bold mb-4">Order Summary</h2>

                <!-- Order Items -->
                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                  <div class="flex items-center space-x-3">
                    <img
                      src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/dede0728-5321-4579-a6df-e2f7968f3379.png"
                      alt="Product thumbnail"
                      class="w-16 h-16 object-cover rounded"
                    />
                    <div class="flex-1">
                      <h4 class="text-sm font-semibold">
                        Floral Summer Blouse
                      </h4>
                      <p class="text-xs text-gray-600">Size: M | Qty: 2</p>
                    </div>
                    <span class="text-sm font-semibold">Rp 478.000</span>
                  </div>
                  <div class="flex items-center space-x-3">
                    <img
                      src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/f8aeb41e-301c-4883-b786-22519244cd46.png"
                      alt="Product thumbnail"
                      class="w-16 h-16 object-cover rounded"
                    />
                    <div class="flex-1">
                      <h4 class="text-sm font-semibold">Elegant Midi Dress</h4>
                      <p class="text-xs text-gray-600">Size: L | Qty: 1</p>
                    </div>
                    <span class="text-sm font-semibold">Rp 399.000</span>
                  </div>
                  <div class="flex items-center space-x-3">
                    <img
                      src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/bd834f15-63db-47dc-b8b8-c358e2a91e11.png"
                      alt="Product thumbnail"
                      class="w-16 h-16 object-cover rounded"
                    />
                    <div class="flex-1">
                      <h4 class="text-sm font-semibold">High-waist Denim</h4>
                      <p class="text-xs text-gray-600">Size: M | Qty: 1</p>
                    </div>
                    <span class="text-sm font-semibold">Rp 329.000</span>
                  </div>
                </div>

                <div class="border-t pt-4">
                  <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Subtotal</span>
                      <span>Rp 1.206.000</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Shipping</span>
                      <span>Rp 15.000</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Tax</span>
                      <span>Rp 0</span>
                    </div>
                  </div>

                  <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-6">
                      <span class="text-xl font-bold">Total</span>
                      <span class="text-2xl font-bold text-pink-500"
                        >Rp 1.221.000</span
                      >
                    </div>

                    <button
                      onclick="placeOrder()"
                      class="w-full btn-primary py-3 rounded-lg text-white font-semibold mb-3"
                    >
                      Place Order
                    </button>

                    <p class="text-xs text-gray-600 text-center">
                      By placing this order you agree to our
                      <a href="#" class="text-pink-500 hover:text-pink-600"
                        >Terms & Conditions</a
                      >
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- User Profile Page -->
      <div id="profilePage" class="page hidden">
        <div class="container mx-auto px-4 py-8">
          <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
              <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center mb-6">
                  <img
                    src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/449eb55f-3f90-4ecd-b23a-f1558c473fac.png"
                    alt="User profile picture"
                    class="w-24 h-24 rounded-full mx-auto mb-3"
                  />
                  <h3 class="font-semibold">Nana Wijaya</h3>
                  <p class="text-sm text-gray-600">Member since 2024</p>
                </div>

                <nav class="space-y-2">
                  <a
                    href="#"
                    class="sidebar-item active-page block px-4 py-2 rounded"
                  >
                    <i class="fas fa-user mr-3"></i>My Profile
                  </a>
                  <a
                    href="#"
                    onclick="showPage('orders')"
                    class="sidebar-item block px-4 py-2 rounded"
                  >
                    <i class="fas fa-box mr-3"></i>My Orders
                  </a>
                  <a href="#" class="sidebar-item block px-4 py-2 rounded">
                    <i class="fas fa-map-marker-alt mr-3"></i>Addresses
                  </a>
                  <a href="#" class="sidebar-item block px-4 py-2 rounded">
                    <i class="fas fa-heart mr-3"></i>Wishlist
                  </a>
                  <a href="#" class="sidebar-item block px-4 py-2 rounded">
                    <i class="fas fa-lock mr-3"></i>Change Password
                  </a>
                  <a
                    href="#"
                    onclick="logout()"
                    class="sidebar-item block px-4 py-2 rounded text-red-500"
                  >
                    <i class="fas fa-sign-out-alt mr-3"></i>Logout
                  </a>
                </nav>
              </div>
            </div>

            <!-- Content Area -->
            <div class="lg:col-span-3">
              <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-6">Profile Information</h2>

                <form>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-gray-700 font-semibold mb-2"
                        >First Name</label
                      >
                      <input
                        type="text"
                        value="Nana"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div>
                      <label class="block text-gray-700 font-semibold mb-2"
                        >Last Name</label
                      >
                      <input
                        type="text"
                        value="Wijaya"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div>
                      <label class="block text-gray-700 font-semibold mb-2"
                        >Email</label
                      >
                      <input
                        type="email"
                        value="nana@email.com"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div>
                      <label class="block text-gray-700 font-semibold mb-2"
                        >Phone</label
                      >
                      <input
                        type="tel"
                        value="+62 812 3456 7890"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div>
                      <label class="block text-gray-700 font-semibold mb-2"
                        >Date of Birth</label
                      >
                      <input
                        type="date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div>
                      <label class="block text-gray-700 font-semibold mb-2"
                        >Gender</label
                      >
                      <select
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      >
                        <option>Female</option>
                        <option>Male</option>
                        <option>Prefer not to say</option>
                      </select>
                    </div>
                  </div>

                  <div class="mt-6">
                    <button
                      type="submit"
                      class="btn-primary px-6 py-2 rounded-lg text-white font-semibold"
                    >
                      Update Profile
                    </button>
                  </div>
                </form>
              </div>

              <!-- Recent Orders -->
              <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h3 class="text-xl font-bold mb-4">Recent Orders</h3>
                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead>
                      <tr class="border-b">
                        <th class="text-left py-2">Order #</th>
                        <th class="text-left py-2">Date</th>
                        <th class="text-left py-2">Total</th>
                        <th class="text-left py-2">Status</th>
                        <th class="text-left py-2">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="border-b">
                        <td class="py-3">#MRN001</td>
                        <td class="py-3">Jan 15, 2024</td>
                        <td class="py-3">Rp 1.221.000</td>
                        <td class="py-3">
                          <span
                            class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs"
                            >Delivered</span
                          >
                        </td>
                        <td class="py-3">
                          <a href="#" class="text-pink-500 hover:text-pink-600"
                            >View</a
                          >
                        </td>
                      </tr>
                      <tr class="border-b">
                        <td class="py-3">#MRN002</td>
                        <td class="py-3">Jan 20, 2024</td>
                        <td class="py-3">Rp 599.000</td>
                        <td class="py-3">
                          <span
                            class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs"
                            >Processing</span
                          >
                        </td>
                        <td class="py-3">
                          <a href="#" class="text-pink-500 hover:text-pink-600"
                            >View</a
                          >
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Admin Dashboard -->
      <div id="adminPage" class="page hidden">
        <div class="flex h-screen bg-gray-100">
          <!-- Admin Sidebar -->
          <div class="w-64 bg-white shadow-lg">
            <div class="p-4">
              <h2 class="text-2xl font-bold text-pink-500">Admin Panel</h2>
            </div>
            <nav class="mt-4">
              <a
                href="#"
                onclick="showAdminSection('dashboard')"
                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 border-l-4 border-pink-500 bg-pink-50"
              >
                <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
              </a>
              <a
                href="#"
                onclick="showAdminSection('products')"
                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
              >
                <i class="fas fa-box mr-3"></i>Products
              </a>
              <a
                href="#"
                onclick="showAdminSection('orders')"
                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
              >
                <i class="fas fa-shopping-cart mr-3"></i>Orders
              </a>
              <a
                href="#"
                onclick="showAdminSection('users')"
                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
              >
                <i class="fas fa-users mr-3"></i>Users
              </a>
              <a
                href="#"
                onclick="showAdminSection('reports')"
                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
              >
                <i class="fas fa-chart-bar mr-3"></i>Reports
              </a>
              <a
                href="#"
                onclick="showAdminSection('settings')"
                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
              >
                <i class="fas fa-cog mr-3"></i>Settings
              </a>
            </nav>
          </div>

          <!-- Admin Content -->
          <div class="flex-1 overflow-y-auto">
            <div class="p-8">
              <!-- Dashboard Section -->
              <div id="adminDashboard" class="admin-section">
                <h1 class="text-3xl font-bold mb-8">Dashboard Overview</h1>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                  <div
                    class="stat-card bg-gradient-to-r from-blue-500 to-blue-600 text-white"
                  >
                    <div class="flex justify-between items-start">
                      <div>
                        <p class="text-blue-100">Total Sales</p>
                        <h3 class="text-3xl font-bold mt-2">Rp 45.2M</h3>
                        <p class="text-sm text-blue-100 mt-2">
                          +12.5% from last month
                        </p>
                      </div>
                      <i class="fas fa-chart-line text-4xl text-blue-200"></i>
                    </div>
                  </div>

                  <div
                    class="stat-card bg-gradient-to-r from-green-500 to-green-600 text-white"
                  >
                    <div class="flex justify-between items-start">
                      <div>
                        <p class="text-green-100">Total Orders</p>
                        <h3 class="text-3xl font-bold mt-2">1,250</h3>
                        <p class="text-sm text-green-100 mt-2">
                          +8% from last month
                        </p>
                      </div>
                      <i
                        class="fas fa-shopping-bag text-4xl text-green-200"
                      ></i>
                    </div>
                  </div>

                  <div
                    class="stat-card bg-gradient-to-r from-purple-500 to-purple-600 text-white"
                  >
                    <div class="flex justify-between items-start">
                      <div>
                        <p class="text-purple-100">Total Customers</p>
                        <h3 class="text-3xl font-bold mt-2">892</h3>
                        <p class="text-sm text-purple-100 mt-2">
                          +15% from last month
                        </p>
                      </div>
                      <i class="fas fa-users text-4xl text-purple-200"></i>
                    </div>
                  </div>

                  <div
                    class="stat-card bg-gradient-to-r from-pink-500 to-pink-600 text-white"
                  >
                    <div class="flex justify-between items-start">
                      <div>
                        <p class="text-pink-100">Products</p>
                        <h3 class="text-3xl font-bold mt-2">156</h3>
                        <p class="text-sm text-pink-100 mt-2">12 low stock</p>
                      </div>
                      <i class="fas fa-tshirt text-4xl text-pink-200"></i>
                    </div>
                  </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                  <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold mb-4">Sales Overview</h3>
                    <canvas id="salesChart" width="400" height="200"></canvas>
                  </div>

                  <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold mb-4">Top Categories</h3>
                    <canvas
                      id="categoryChart"
                      width="400"
                      height="200"
                    ></canvas>
                  </div>
                </div>

                <!-- Recent Orders Table -->
                <div class="bg-white rounded-lg shadow p-6">
                  <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Recent Orders</h3>
                    <a
                      href="#"
                      onclick="showAdminSection('orders')"
                      class="text-pink-500 hover:text-pink-600"
                      >View All</a
                    >
                  </div>
                  <div class="overflow-x-auto">
                    <table class="w-full">
                      <thead>
                        <tr class="border-b">
                          <th class="text-left py-2">Order ID</th>
                          <th class="text-left py-2">Customer</th>
                          <th class="text-left py-2">Date</th>
                          <th class="text-left py-2">Total</th>
                          <th class="text-left py-2">Status</th>
                          <th class="text-left py-2">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="border-b">
                          <td class="py-3">#MRN001</td>
                          <td class="py-3">Nana Wijaya</td>
                          <td class="py-3">Jan 25, 2024</td>
                          <td class="py-3">Rp 1.221.000</td>
                          <td class="py-3">
                            <span
                              class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs"
                              >Processing</span
                            >
                          </td>
                          <td class="py-3">
                            <button
                              class="text-blue-500 hover:text-blue-600 mr-2"
                            >
                              <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-500 hover:text-green-600">
                              <i class="fas fa-check"></i>
                            </button>
                          </td>
                        </tr>
                        <tr class="border-b">
                          <td class="py-3">#MRN002</td>
                          <td class="py-3">Rina Susanti</td>
                          <td class="py-3">Jan 25, 2024</td>
                          <td class="py-3">Rp 599.000</td>
                          <td class="py-3">
                            <span
                              class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs"
                              >Completed</span
                            >
                          </td>
                          <td class="py-3">
                            <button
                              class="text-blue-500 hover:text-blue-600 mr-2"
                            >
                              <i class="fas fa-eye"></i>
                            </button>
                          </td>
                        </tr>
                        <tr class="border-b">
                          <td class="py-3">#MRN003</td>
                          <td class="py-3">Dian Kartika</td>
                          <td class="py-3">Jan 24, 2024</td>
                          <td class="py-3">Rp 1.892.000</td>
                          <td class="py-3">
                            <span
                              class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs"
                              >Shipped</span
                            >
                          </td>
                          <td class="py-3">
                            <button
                              class="text-blue-500 hover:text-blue-600 mr-2"
                            >
                              <i class="fas fa-eye"></i>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- Products Management Section -->
              <div id="adminProducts" class="admin-section hidden">
                <div class="flex justify-between items-center mb-8">
                  <h1 class="text-3xl font-bold">Products Management</h1>
                  <button
                    class="btn-primary px-6 py-2 rounded-lg text-white font-semibold"
                  >
                    <i class="fas fa-plus mr-2"></i>Add Product
                  </button>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                  <div class="flex justify-between items-center mb-4">
                    <div class="flex space-x-4">
                      <input
                        type="text"
                        placeholder="Search products..."
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                      <select
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      >
                        <option>All Categories</option>
                        <option>Atasan</option>
                        <option>Bawahan</option>
                        <option>Dress</option>
                        <option>Outer</option>
                      </select>
                    </div>
                    <button class="text-gray-600 hover:text-gray-800">
                      <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                  </div>

                  <div class="overflow-x-auto">
                    <table class="w-full">
                      <thead>
                        <tr class="border-b">
                          <th class="text-left py-2">ID</th>
                          <th class="text-left py-2">Image</th>
                          <th class="text-left py-2">Name</th>
                          <th class="text-left py-2">Category</th>
                          <th class="text-left py-2">Price</th>
                          <th class="text-left py-2">Stock</th>
                          <th class="text-left py-2">Status</th>
                          <th class="text-left py-2">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="border-b">
                          <td class="py-3">#P001</td>
                          <td class="py-3">
                            <img
                              src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/c67ced42-fd70-4686-8681-bc11440f1723.png"
                              alt="Product thumbnail"
                              class="w-12 h-12 object-cover rounded"
                            />
                          </td>
                          <td class="py-3">Floral Summer Blouse</td>
                          <td class="py-3">Atasan</td>
                          <td class="py-3">Rp 239.000</td>
                          <td class="py-3">25</td>
                          <td class="py-3">
                            <span
                              class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs"
                              >Active</span
                            >
                          </td>
                          <td class="py-3">
                            <button
                              class="text-blue-500 hover:text-blue-600 mr-2"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-500 hover:text-red-600">
                              <i class="fas fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                        <tr class="border-b">
                          <td class="py-3">#P002</td>
                          <td class="py-3">
                            <img
                              src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/3e282dd7-99af-42df-b6f1-640c677b0203.png"
                              alt="Product thumbnail"
                              class="w-12 h-12 object-cover rounded"
                            />
                          </td>
                          <td class="py-3">Elegant Midi Dress</td>
                          <td class="py-3">Dress</td>
                          <td class="py-3">Rp 399.000</td>
                          <td class="py-3">15</td>
                          <td class="py-3">
                            <span
                              class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs"
                              >Active</span
                            >
                          </td>
                          <td class="py-3">
                            <button
                              class="text-blue-500 hover:text-blue-600 mr-2"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-500 hover:text-red-600">
                              <i class="fas fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                        <tr class="border-b">
                          <td class="py-3">#P003</td>
                          <td class="py-3">
                            <img
                              src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/7a23cb78-c597-47c8-9b11-55a4ed337e5b.png"
                              alt="Product thumbnail"
                              class="w-12 h-12 object-cover rounded"
                            />
                          </td>
                          <td class="py-3">High-waist Denim</td>
                          <td class="py-3">Bawahan</td>
                          <td class="py-3">Rp 329.000</td>
                          <td class="py-3">3</td>
                          <td class="py-3">
                            <span
                              class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs"
                              >Low Stock</span
                            >
                          </td>
                          <td class="py-3">
                            <button
                              class="text-blue-500 hover:text-blue-600 mr-2"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-500 hover:text-red-600">
                              <i class="fas fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <div class="flex justify-center items-center space-x-2 mt-6">
                    <button
                      class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
                    >
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="px-3 py-2 bg-pink-500 text-white rounded">
                      1
                    </button>
                    <button
                      class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
                    >
                      2
                    </button>
                    <button
                      class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
                    >
                      3
                    </button>
                    <button
                      class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
                    >
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Orders Management Section -->
              <div id="adminOrders" class="admin-section hidden">
                <h1 class="text-3xl font-bold mb-8">Orders Management</h1>

                <div class="bg-white rounded-lg shadow p-6">
                  <div class="flex justify-between items-center mb-4">
                    <div class="flex space-x-4">
                      <select
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      >
                        <option>All Orders</option>
                        <option>Pending</option>
                        <option>Processing</option>
                        <option>Shipped</option>
                        <option>Completed</option>
                        <option>Cancelled</option>
                      </select>
                      <input
                        type="date"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500"
                      />
                    </div>
                    <div class="flex space-x-2">
                      <button
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition"
                      >
                        <i class="fas fa-file-excel mr-2"></i>Export CSV
                      </button>
                      <button
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                      >
                        <i class="fas fa-print mr-2"></i>Print
                      </button>
                    </div>
                  </div>

                  <div class="overflow-x-auto">
                    <table class="w-full">
                      <thead>
                        <tr class="border-b">
                          <th class="text-left py-2">Order ID</th>
                          <th class="text-left py-2">Customer</th>
                          <th class="text-left py-2">Date</th>
                          <th class="text-left py-2">Items</th>
                          <th class="text-left py-2">Total</th>
                          <th class="text-left py-2">Payment</th>
                          <th class="text-left py-2">Status</th>
                          <th class="text-left py-2">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="border-b">
                          <td class="py-3">#MRN001</td>
                          <td class="py-3">
                            <div>
                              <p class="font-semibold">Nana Wijaya</p>
                              <p class="text-sm text-gray-600">
                                nana@email.com
                              </p>
                            </div>
                          </td>
                          <td class="py-3">Jan 25, 2024</td>
                          <td class="py-3">4 items</td>
                          <td class="py-3">Rp 1.221.000</td>
                          <td class="py-3">Bank Transfer</td>
                          <td class="py-3">
                            <select
                              class="px-2 py-1 border border-gray-300 rounded text-sm"
                            >
                              <option>Pending</option>
                              <option selected>Processing</option>
                              <option>Shipped</option>
                              <option>Completed</option>
                            </select>
                          </td>
                          <td class="py-3">
                            <button
                              class="text-blue-500 hover:text-blue-600 mr-2"
                            >
                              <i class="fas fa-eye"></i>
                            </button>
                            <button
                              class="text-green-500 hover:text-green-600 mr-2"
                            >
                              <i class="fas fa-check"></i>
                            </button>
                            <button class="text-red-500 hover:text-red-600">
                              <i class="fas fa-times"></i>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 class="text-2xl font-bold mb-4 text-pink-400">Merona</h3>
            <p class="text-gray-400">Fashion That Speaks You</p>
            <div class="flex space-x-4 mt-4">
              <a href="#" class="text-gray-400 hover:text-white"
                ><i class="fab fa-facebook-f"></i
              ></a>
              <a href="#" class="text-gray-400 hover:text-white"
                ><i class="fab fa-instagram"></i
              ></a>
              <a href="#" class="text-gray-400 hover:text-white"
                ><i class="fab fa-twitter"></i
              ></a>
              <a href="#" class="text-gray-400 hover:text-white"
                ><i class="fab fa-youtube"></i
              ></a>
            </div>
          </div>

          <div>
            <h4 class="font-semibold mb-4">Quick Links</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white">About Us</a></li>
              <li><a href="#" class="hover:text-white">Contact</a></li>
              <li><a href="#" class="hover:text-white">FAQs</a></li>
              <li><a href="#" class="hover:text-white">Size Guide</a></li>
            </ul>
          </div>

          <div>
            <h4 class="font-semibold mb-4">Customer Service</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white">Shipping Info</a></li>
              <li><a href="#" class="hover:text-white">Returns</a></li>
              <li><a href="#" class="hover:text-white">Payment Methods</a></li>
              <li><a href="#" class="hover:text-white">Track Order</a></li>
            </ul>
          </div>

          <div>
            <h4 class="font-semibold mb-4">Newsletter</h4>
            <p class="text-gray-400 mb-4">
              Subscribe to get special offers and updates
            </p>
            <form class="flex">
              <input
                type="email"
                placeholder="Your email"
                class="flex-1 px-4 py-2 rounded-l-lg text-gray-800 focus:outline-none"
              />
              <button
                type="submit"
                class="bg-pink-500 px-4 py-2 rounded-r-lg hover:bg-pink-600 transition"
              >
                <i class="fas fa-paper-plane"></i>
              </button>
            </form>
          </div>
        </div>

        <div
          class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400"
        >
          <p>
            © 2024 Merona. All rights reserved. | Made with
            <span class="text-pink-500">♥</span> for UAS Project
          </p>
        </div>
      </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script>
      // PHP-specific JavaScript functions that need server data
      function addToCart(productId, quantity = 1) {
        <?php if (isset($_SESSION['user_id'])): ?>
          fetch('ajax/add-to-cart.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=${quantity}`
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showNotification(data.message, 'success');
              updateCartCount();
            } else {
              showNotification(data.message, 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
          });
        <?php else: ?>
          showNotification('Silakan login terlebih dahulu', 'warning');
          setTimeout(() => {
            window.location.href = 'login.php';
          }, 1500);
        <?php endif; ?>
      }

      function updateCartCount() {
        <?php if (isset($_SESSION['user_id'])): ?>
          fetch('ajax/get-cart-count.php')
          .then(response => response.json())
          .then(data => {
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge) {
              if (data.count > 0) {
                cartBadge.textContent = data.count;
                cartBadge.style.display = 'block';
              } else {
                cartBadge.style.display = 'none';
              }
            }
          })
          .catch(error => {
            console.error('Error updating cart count:', error);
          });
        <?php endif; ?>
      }

      // Initialize on page load
      document.addEventListener("DOMContentLoaded", function () {
        updateCartCount();
      });
    </script>
  </body>
</html>

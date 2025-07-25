<?php
require_once 'includes/functions.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Nama harus diisi';
    }
    
    if (empty($email) || !validateEmail($email)) {
        $errors[] = 'Email tidak valid';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject harus diisi';
    }
    
    if (empty($message)) {
        $errors[] = 'Pesan harus diisi';
    }
    
    if (empty($errors)) {
        // In a real application, you would send email or save to database
        // For now, we'll just show a success message
        $success_message = 'Terima kasih! Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.';
        
        // Log the contact attempt if user is logged in
        if (isLoggedIn()) {
            logActivity($_SESSION['user_id'], 'Sent contact message: ' . $subject);
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

$page_title = 'Hubungi Kami';
include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-pink-500 to-purple-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold font-heading mb-4">Hubungi Kami</h1>
            <p class="text-xl text-pink-100">Kami siap membantu Anda kapan saja</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-2xl font-bold font-heading mb-6">Kirim Pesan</h2>
                
                <?php if ($success_message): ?>
                    <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700"><?php echo $success_message; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700"><?php echo $error_message; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" id="name" name="name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <select id="subject" name="subject" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="">Pilih Subject</option>
                            <option value="Pertanyaan Produk" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Pertanyaan Produk') ? 'selected' : ''; ?>>Pertanyaan Produk</option>
                            <option value="Keluhan Pesanan" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Keluhan Pesanan') ? 'selected' : ''; ?>>Keluhan Pesanan</option>
                            <option value="Saran & Masukan" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Saran & Masukan') ? 'selected' : ''; ?>>Saran & Masukan</option>
                            <option value="Kerjasama" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Kerjasama') ? 'selected' : ''; ?>>Kerjasama</option>
                            <option value="Lainnya" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                        <textarea id="message" name="message" rows="6" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                  placeholder="Tulis pesan Anda di sini..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-pink-500 text-white py-3 px-6 rounded-lg font-semibold hover:bg-pink-600 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Pesan
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <!-- Contact Details -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold font-heading mb-6">Informasi Kontak</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-pink-500"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Alamat</h3>
                                <p class="text-gray-600">Jl. Fashion Street No. 123<br>Jakarta Selatan, DKI Jakarta 12345</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-blue-500"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Telepon</h3>
                                <p class="text-gray-600">+62 21 1234 5678<br>+62 812 3456 7890</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-green-500"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Email</h3>
                                <p class="text-gray-600">info@merona.com<br>support@merona.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-purple-500"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Jam Operasional</h3>
                                <p class="text-gray-600">Senin - Jumat: 09:00 - 18:00<br>Sabtu - Minggu: 10:00 - 16:00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold font-heading mb-6">Ikuti Kami</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="#" class="flex items-center space-x-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fab fa-facebook-f text-blue-600 text-xl"></i>
                            <span class="font-medium text-blue-600">Facebook</span>
                        </a>
                        
                        <a href="#" class="flex items-center space-x-3 p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition-colors">
                            <i class="fab fa-instagram text-pink-600 text-xl"></i>
                            <span class="font-medium text-pink-600">Instagram</span>
                        </a>
                        
                        <a href="#" class="flex items-center space-x-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fab fa-twitter text-blue-400 text-xl"></i>
                            <span class="font-medium text-blue-400">Twitter</span>
                        </a>
                        
                        <a href="#" class="flex items-center space-x-3 p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                            <i class="fab fa-youtube text-red-600 text-xl"></i>
                            <span class="font-medium text-red-600">YouTube</span>
                        </a>
                    </div>
                </div>

                <!-- FAQ Quick Links -->
                <div class="bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg p-8 text-white">
                    <h2 class="text-2xl font-bold font-heading mb-4">Butuh Bantuan Cepat?</h2>
                    <p class="text-pink-100 mb-6">Lihat pertanyaan yang sering diajukan atau hubungi customer service kami.</p>
                    <div class="space-y-3">
                        <a href="#" class="block bg-white bg-opacity-20 rounded-lg p-3 hover:bg-opacity-30 transition-colors">
                            <i class="fas fa-question-circle mr-2"></i>FAQ
                        </a>
                        <a href="#" class="block bg-white bg-opacity-20 rounded-lg p-3 hover:bg-opacity-30 transition-colors">
                            <i class="fas fa-shipping-fast mr-2"></i>Info Pengiriman
                        </a>
                        <a href="#" class="block bg-white bg-opacity-20 rounded-lg p-3 hover:bg-opacity-30 transition-colors">
                            <i class="fas fa-undo mr-2"></i>Kebijakan Return
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
$page_title = 'Tentang Kami';
require_once 'includes/functions.php';
include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-pink-500 to-purple-600 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold font-heading mb-6">Tentang Merona</h1>
            <p class="text-xl text-pink-100 max-w-3xl mx-auto">
                Fashion yang berbicara tentang kepribadian Anda. Kami hadir untuk memberikan pengalaman berbelanja fashion terbaik dengan koleksi terkini dan kualitas premium.
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-16">
        <!-- Our Story -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
            <div>
                <h2 class="text-3xl font-bold font-heading mb-6">Cerita Kami</h2>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Merona lahir dari passion untuk fashion yang tidak hanya indah dipandang, tetapi juga nyaman dikenakan. 
                    Sejak didirikan, kami berkomitmen untuk menghadirkan koleksi fashion terdepan yang mengikuti tren global 
                    namun tetap mempertahankan sentuhan lokal yang khas.
                </p>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Dengan tim yang berpengalaman di industri fashion, kami selalu berusaha memberikan yang terbaik untuk 
                    pelanggan kami. Setiap produk dipilih dengan teliti untuk memastikan kualitas dan kenyamanan yang optimal.
                </p>
                <div class="flex space-x-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-pink-500">1000+</div>
                        <div class="text-gray-600">Produk</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-pink-500">5000+</div>
                        <div class="text-gray-600">Pelanggan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-pink-500">50+</div>
                        <div class="text-gray-600">Brand</div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="bg-gradient-to-br from-pink-100 to-purple-100 rounded-lg p-8 h-96 flex items-center justify-center">
                    <i class="fas fa-shopping-bag text-6xl text-pink-500"></i>
                </div>
            </div>
        </div>

        <!-- Our Values -->
        <div class="mb-20">
            <h2 class="text-3xl font-bold font-heading text-center mb-12">Nilai-Nilai Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-8 bg-white rounded-lg shadow-md">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-heart text-2xl text-pink-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Kualitas Premium</h3>
                    <p class="text-gray-600">
                        Kami hanya menyediakan produk dengan kualitas terbaik yang telah melalui seleksi ketat untuk memastikan kepuasan pelanggan.
                    </p>
                </div>
                
                <div class="text-center p-8 bg-white rounded-lg shadow-md">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-2xl text-purple-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Pelayanan Terbaik</h3>
                    <p class="text-gray-600">
                        Tim customer service kami siap membantu Anda 24/7 untuk memberikan pengalaman berbelanja yang menyenangkan.
                    </p>
                </div>
                
                <div class="text-center p-8 bg-white rounded-lg shadow-md">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-leaf text-2xl text-green-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Ramah Lingkungan</h3>
                    <p class="text-gray-600">
                        Kami berkomitmen untuk mendukung fashion berkelanjutan dengan memilih brand yang peduli lingkungan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="mb-20">
            <h2 class="text-3xl font-bold font-heading text-center mb-12">Tim Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-pink-400 to-purple-400 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Sarah Johnson</h3>
                    <p class="text-pink-500 mb-4">Founder & CEO</p>
                    <p class="text-gray-600 text-sm">
                        Dengan pengalaman 10+ tahun di industri fashion, Sarah memimpin visi Merona untuk menjadi brand fashion terdepan.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-400 to-indigo-400 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Michael Chen</h3>
                    <p class="text-blue-500 mb-4">Creative Director</p>
                    <p class="text-gray-600 text-sm">
                        Michael bertanggung jawab atas kurasi produk dan memastikan setiap koleksi sesuai dengan tren fashion terkini.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-green-400 to-teal-400 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Lisa Wang</h3>
                    <p class="text-green-500 mb-4">Customer Experience Manager</p>
                    <p class="text-gray-600 text-sm">
                        Lisa memastikan setiap pelanggan mendapatkan pengalaman berbelanja yang luar biasa dari awal hingga akhir.
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact CTA -->
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg p-12 text-center text-white">
            <h2 class="text-3xl font-bold font-heading mb-4">Bergabunglah dengan Komunitas Merona</h2>
            <p class="text-xl text-pink-100 mb-8 max-w-2xl mx-auto">
                Dapatkan update terbaru tentang koleksi fashion, promo eksklusif, dan tips styling dari para ahli kami.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="register.php" class="bg-white text-pink-500 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Daftar Sekarang
                </a>
                <a href="contact.php" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-pink-500 transition-colors">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="text-2xl font-bold text-pink-500 mb-4 font-heading">
                        <i class="fas fa-shopping-bag mr-2"></i>Merona
                    </div>
                    <p class="text-gray-300 mb-4">Fashion yang berbicara tentang kepribadian Anda. Temukan gaya terbaik dengan koleksi terbaru kami.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-pink-500 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-pink-500 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-pink-500 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-pink-500 transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="merona-shop.php" class="text-gray-300 hover:text-pink-500 transition">Home</a></li>
                        <li><a href="products.php" class="text-gray-300 hover:text-pink-500 transition">Products</a></li>
                        <li><a href="categories.php" class="text-gray-300 hover:text-pink-500 transition">Categories</a></li>
                        <li><a href="about.php" class="text-gray-300 hover:text-pink-500 transition">About Us</a></li>
                        <li><a href="contact.php" class="text-gray-300 hover:text-pink-500 transition">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Customer Service</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-pink-500 transition">FAQ</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-pink-500 transition">Shipping Info</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-pink-500 transition">Return Policy</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-pink-500 transition">Size Guide</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-pink-500 transition">Track Order</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Info</h3>
                    <div class="space-y-2 text-gray-300">
                        <p><i class="fas fa-map-marker-alt mr-2"></i>Jl. Fashion Street No. 123, Jakarta</p>
                        <p><i class="fas fa-phone mr-2"></i>+62 812-3456-7890</p>
                        <p><i class="fas fa-envelope mr-2"></i>info@merona.com</p>
                        <p><i class="fas fa-clock mr-2"></i>Mon - Sat: 9AM - 9PM</p>
                    </div>
                </div>
            </div>
            
            <hr class="border-gray-700 my-8">
            
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-300">&copy; 2024 Merona Fashion. All rights reserved.</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-300 hover:text-pink-500 transition">Privacy Policy</a>
                    <a href="#" class="text-gray-300 hover:text-pink-500 transition">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="fixed bottom-6 right-6 bg-pink-500 text-white p-3 rounded-full shadow-lg hover:bg-pink-600 transition-all duration-300 opacity-0 invisible">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Back to top functionality
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.classList.remove('opacity-0', 'invisible');
            } else {
                backToTop.classList.add('opacity-0', 'invisible');
            }
        });

        document.getElementById('backToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobileMenu');
            const menuButton = event.target.closest('[onclick="toggleMobileMenu()"]');
            
            if (!menu.contains(event.target) && !menuButton && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
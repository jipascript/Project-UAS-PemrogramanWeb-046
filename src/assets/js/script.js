// Merona Shop JavaScript Functions

// Global Variables
let currentPage = 'home';
let isLoggedIn = false;

// Page Navigation System
function showPage(page) {
  // Hide all pages
  document.querySelectorAll('.page').forEach(p => {
    p.classList.add('hidden');
  });
  
  // Show selected page
  const targetPage = document.getElementById(page + 'Page');
  if (targetPage) {
    targetPage.classList.remove('hidden');
    currentPage = page;
  }
  
  // Update URL without reload
  if (history.pushState) {
    history.pushState(null, null, '#' + page);
  }
  
  // Initialize page-specific functions
  initPageFunctions(page);
}

// Initialize page-specific functions
function initPageFunctions(page) {
  switch(page) {
    case 'admin':
      initCharts();
      break;
    case 'products':
      initProductFilters();
      break;
    case 'cart':
      updateCartTotals();
      break;
  }
}

// Authentication Functions
function login() {
  const email = document.getElementById('loginEmail').value;
  const password = document.getElementById('loginPassword').value;
  
  if (!email || !password) {
    showNotification('Please fill in all fields', 'error');
    return;
  }
  
  // Show loading
  const loginBtn = document.querySelector('#loginPage button[type="submit"]');
  const originalText = loginBtn.innerHTML;
  loginBtn.innerHTML = '<div class="loading-spinner"></div> Logging in...';
  loginBtn.disabled = true;
  
  // Simulate login process
  setTimeout(() => {
    isLoggedIn = true;
    showNotification('Login successful!', 'success');
    showPage('home');
    updateUserInterface();
    
    // Reset button
    loginBtn.innerHTML = originalText;
    loginBtn.disabled = false;
  }, 1500);
}

function register() {
  const name = document.getElementById('registerName').value;
  const email = document.getElementById('registerEmail').value;
  const password = document.getElementById('registerPassword').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  
  if (!name || !email || !password || !confirmPassword) {
    showNotification('Please fill in all fields', 'error');
    return;
  }
  
  if (password !== confirmPassword) {
    showNotification('Passwords do not match', 'error');
    return;
  }
  
  if (password.length < 6) {
    showNotification('Password must be at least 6 characters', 'error');
    return;
  }
  
  // Show loading
  const registerBtn = document.querySelector('#registerPage button[type="submit"]');
  const originalText = registerBtn.innerHTML;
  registerBtn.innerHTML = '<div class="loading-spinner"></div> Creating account...';
  registerBtn.disabled = true;
  
  // Simulate registration process
  setTimeout(() => {
    showNotification('Account created successfully!', 'success');
    showPage('login');
    
    // Reset button
    registerBtn.innerHTML = originalText;
    registerBtn.disabled = false;
    
    // Clear form
    document.getElementById('registerForm').reset();
  }, 2000);
}

function logout() {
  isLoggedIn = false;
  showPage('home');
  showNotification('You have been logged out', 'info');
  updateUserInterface();
}

// Update user interface based on login status
function updateUserInterface() {
  const loginBtn = document.getElementById('loginBtn');
  const registerBtn = document.getElementById('registerBtn');
  const userMenu = document.getElementById('userMenu');
  
  if (isLoggedIn) {
    if (loginBtn) loginBtn.style.display = 'none';
    if (registerBtn) registerBtn.style.display = 'none';
    if (userMenu) userMenu.style.display = 'block';
  } else {
    if (loginBtn) loginBtn.style.display = 'block';
    if (registerBtn) registerBtn.style.display = 'block';
    if (userMenu) userMenu.style.display = 'none';
  }
}

// Cart Functions
function addToCart(productId, quantity = 1) {
  if (!isLoggedIn) {
    showNotification('Please login first', 'warning');
    setTimeout(() => showPage('login'), 1500);
    return;
  }
  
  // Show loading on button
  const button = event.target.closest('button');
  const originalContent = button.innerHTML;
  button.innerHTML = '<div class="loading-spinner"></div>';
  button.disabled = true;
  
  // Simulate API call
  setTimeout(() => {
    showNotification('Product added to cart!', 'success');
    updateCartCount();
    
    // Reset button
    button.innerHTML = originalContent;
    button.disabled = false;
  }, 1000);
}

function updateCartCount() {
  // Simulate cart count update
  const cartBadge = document.querySelector('.cart-count');
  if (cartBadge && isLoggedIn) {
    const currentCount = parseInt(cartBadge.textContent) || 0;
    cartBadge.textContent = currentCount + 1;
    cartBadge.style.display = 'block';
  }
}

function removeFromCart(productId) {
  if (confirm('Remove this item from cart?')) {
    showNotification('Item removed from cart', 'info');
    updateCartTotals();
  }
}

function updateCartTotals() {
  // Calculate and update cart totals
  const cartItems = document.querySelectorAll('.cart-item');
  let subtotal = 0;
  
  cartItems.forEach(item => {
    const price = parseFloat(item.dataset.price) || 0;
    const quantity = parseInt(item.querySelector('.quantity-input').value) || 0;
    subtotal += price * quantity;
  });
  
  const shipping = subtotal > 500000 ? 0 : 15000;
  const total = subtotal + shipping;
  
  // Update display
  const subtotalEl = document.getElementById('cartSubtotal');
  const shippingEl = document.getElementById('cartShipping');
  const totalEl = document.getElementById('cartTotal');
  
  if (subtotalEl) subtotalEl.textContent = formatPrice(subtotal);
  if (shippingEl) shippingEl.textContent = shipping === 0 ? 'Free' : formatPrice(shipping);
  if (totalEl) totalEl.textContent = formatPrice(total);
}

// Product Functions
function increaseQty() {
  const qtyInput = document.getElementById('qty');
  if (qtyInput) {
    qtyInput.value = parseInt(qtyInput.value) + 1;
  }
}

function decreaseQty() {
  const qtyInput = document.getElementById('qty');
  if (qtyInput && parseInt(qtyInput.value) > 1) {
    qtyInput.value = parseInt(qtyInput.value) - 1;
  }
}

function initProductFilters() {
  // Initialize product filtering functionality
  const filterButtons = document.querySelectorAll('.filter-btn');
  const sortSelect = document.getElementById('sortSelect');
  
  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      filterProducts(btn.dataset.filter);
    });
  });
  
  if (sortSelect) {
    sortSelect.addEventListener('change', () => {
      sortProducts(sortSelect.value);
    });
  }
}

function filterProducts(category) {
  const products = document.querySelectorAll('.product-item');
  
  products.forEach(product => {
    if (category === 'all' || product.dataset.category === category) {
      product.style.display = 'block';
      product.classList.add('fade-in');
    } else {
      product.style.display = 'none';
    }
  });
}

function sortProducts(sortBy) {
  const container = document.querySelector('.products-grid');
  const products = Array.from(container.children);
  
  products.sort((a, b) => {
    switch(sortBy) {
      case 'price-low':
        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
      case 'price-high':
        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
      case 'name':
        return a.dataset.name.localeCompare(b.dataset.name);
      case 'rating':
        return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
      default:
        return 0;
    }
  });
  
  products.forEach(product => container.appendChild(product));
}

// UI Functions
function toggleDropdown() {
  const dropdown = document.getElementById('dropdown');
  if (dropdown) {
    dropdown.classList.toggle('hidden');
  }
}

function toggleMobileMenu() {
  const mobileMenu = document.getElementById('mobileMenu');
  if (mobileMenu) {
    mobileMenu.classList.toggle('hidden');
  }
}

function closeMobileMenu() {
  const mobileMenu = document.getElementById('mobileMenu');
  if (mobileMenu) {
    mobileMenu.classList.add('hidden');
  }
}

// Admin Functions
function showAdminSection(section) {
  // Hide all admin sections
  document.querySelectorAll('.admin-section').forEach(sec => {
    sec.classList.add('hidden');
  });
  
  // Show selected section
  const selectedSection = document.getElementById('admin' + section.charAt(0).toUpperCase() + section.slice(1));
  if (selectedSection) {
    selectedSection.classList.remove('hidden');
  }
  
  // Update sidebar active state
  document.querySelectorAll('#adminPage nav a').forEach(link => {
    link.classList.remove('border-l-4', 'border-pink-500', 'bg-pink-50');
  });
  
  if (event && event.target) {
    event.target.closest('a').classList.add('border-l-4', 'border-pink-500', 'bg-pink-50');
  }
}

function placeOrder() {
  const orderNumber = 'MRN' + Math.floor(Math.random() * 10000);
  showNotification(`Order placed successfully! Order #${orderNumber}`, 'success');
  
  setTimeout(() => {
    showPage('profile');
  }, 2000);
}

// Notification System
function showNotification(message, type = 'info') {
  const colors = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    info: 'bg-blue-500',
    warning: 'bg-yellow-500'
  };
  
  const icons = {
    success: 'check-circle',
    error: 'exclamation-circle',
    info: 'info-circle',
    warning: 'exclamation-triangle'
  };
  
  const notification = document.createElement('div');
  notification.className = `notification ${type} show`;
  notification.innerHTML = `
    <div class="flex items-center">
      <i class="fas fa-${icons[type]} mr-3"></i>
      <span>${message}</span>
      <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white hover:text-gray-200">
        <i class="fas fa-times"></i>
      </button>
    </div>
  `;
  
  document.body.appendChild(notification);
  
  // Auto remove after 5 seconds
  setTimeout(() => {
    if (notification.parentElement) {
      notification.classList.remove('show');
      setTimeout(() => notification.remove(), 300);
    }
  }, 5000);
}

// Chart Initialization
function initCharts() {
  // Sales Chart
  const salesCtx = document.getElementById('salesChart');
  if (salesCtx && typeof Chart !== 'undefined') {
    new Chart(salesCtx.getContext('2d'), {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: 'Sales',
          data: [30, 45, 35, 50, 45, 60],
          borderColor: '#ec4899',
          backgroundColor: 'rgba(236, 72, 153, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return 'Rp ' + value + 'M';
              }
            }
          }
        }
      }
    });
  }
  
  // Category Chart
  const categoryCtx = document.getElementById('categoryChart');
  if (categoryCtx && typeof Chart !== 'undefined') {
    new Chart(categoryCtx.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: ['Atasan', 'Bawahan', 'Dress', 'Outer'],
        datasets: [{
          data: [35, 25, 30, 10],
          backgroundColor: ['#ec4899', '#8b5cf6', '#06b6d4', '#10b981']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });
  }
}

// Utility Functions
function formatPrice(price) {
  return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
}

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

function throttle(func, limit) {
  let inThrottle;
  return function() {
    const args = arguments;
    const context = this;
    if (!inThrottle) {
      func.apply(context, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
  // Initialize the application
  showPage('home');
  updateUserInterface();
  
  // Close dropdowns when clicking outside
  document.addEventListener('click', function(event) {
    const userMenu = document.getElementById('userMenu');
    const dropdown = document.getElementById('dropdown');
    
    if (userMenu && !userMenu.contains(event.target)) {
      if (dropdown) dropdown.classList.add('hidden');
    }
  });
  
  // Handle browser back/forward buttons
  window.addEventListener('popstate', function(event) {
    const hash = window.location.hash.substring(1);
    if (hash) {
      showPage(hash);
    } else {
      showPage('home');
    }
  });
  
  // Initialize lazy loading for images
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.classList.remove('lazy');
          imageObserver.unobserve(img);
        }
      });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
      imageObserver.observe(img);
    });
  }
  
  // Show welcome notification
  setTimeout(() => {
    if (currentPage === 'home') {
      showNotification('Welcome to Merona Shop! 🛍️', 'info');
    }
  }, 2000);
});

// Export functions for global access
window.MeronaShop = {
  showPage,
  login,
  register,
  logout,
  addToCart,
  removeFromCart,
  showNotification,
  toggleDropdown,
  toggleMobileMenu,
  showAdminSection,
  placeOrder
};
# 📦 Panduan Instalasi Merona Fashion

## 🖥️ Persyaratan Sistem

### Minimum Requirements:
- **PHP**: 7.4 atau lebih tinggi
- **MySQL**: 5.7 atau lebih tinggi (Direkomendasikan MySQL 8.0+)
- **Apache/Nginx**: Web server
- **RAM**: Minimal 1GB
- **Storage**: Minimal 500MB ruang kosong
- **Browser**: Chrome, Firefox, Safari, Edge (versi terbaru)

### Recommended Setup:
- **XAMPP**: 8.0+ (Windows/macOS/Linux)
- **WAMP**: 3.2+ (Windows)
- **LAMP**: (Linux)
- **MAMP**: (macOS)

---

## 🚀 Langkah-langkah Instalasi

### 1. Persiapan Environment

#### a) Download dan Install XAMPP
1. Download XAMPP dari [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP dengan komponen:
   - ✅ Apache
   - ✅ MySQL  
   - ✅ PHP
   - ✅ phpMyAdmin

#### b) Start Services
1. Buka XAMPP Control Panel
2. Start **Apache** dan **MySQL**
3. Pastikan status menunjukkan "Running" (hijau)

### 2. Download Source Code

#### Option A: Download ZIP
1. Download project sebagai ZIP file
2. Extract ke folder `C:\xampp\htdocs\nazifah\` (Windows) atau `/opt/lampp/htdocs/nazifah/` (Linux)

#### Option B: Clone Repository (Jika menggunakan Git)
```bash
cd C:\xampp\htdocs\
git clone [repository-url] nazifah
cd nazifah
```

### 3. Setup Database

#### a) Buat Database
1. Buka browser dan akses: `http://localhost/phpmyadmin`
2. Login menggunakan:
   - **Username**: `root`
   - **Password**: (kosong, atau sesuai konfigurasi XAMPP)

#### b) Import Database
1. Klik **"New"** untuk membuat database baru
2. Nama database: `merona_shop`
3. Pilih **Collation**: `utf8mb4_general_ci`
4. Klik **"Create"**
5. Pilih database `merona_shop` yang baru dibuat
6. Klik tab **"Import"**
7. Pilih file: `sql/merona_shop.sql`
8. Klik **"Go"** untuk mengimpor

#### c) Verifikasi Database
Pastikan tabel-tabel berikut telah terbuat:
- ✅ users
- ✅ categories  
- ✅ products
- ✅ carts
- ✅ transactions
- ✅ transaction_items
- ✅ payments
- ✅ reviews
- ✅ activity_logs
- ✅ settings
- ✅ roles
- ✅ product_images

### 4. Konfigurasi Database Connection

#### Edit file `src/config/database.php`:
```php
<?php
class Database {
    private $host = 'localhost';      // Host database
    private $db_name = 'merona_shop'; // Nama database
    private $username = 'root';       // Username MySQL
    private $password = '';           // Password MySQL (kosong untuk XAMPP default)
    public $conn;
    
    // ... rest of the code
}
?>
```

#### Sesuaikan dengan konfigurasi Anda:
- **Host**: Biasanya `localhost` atau `127.0.0.1`
- **Database**: `merona_shop`
- **Username**: Default XAMPP = `root`
- **Password**: Default XAMPP = kosong `''`

### 5. Set Permissions (Linux/macOS)

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/html/nazifah/

# Set directory permissions
find /var/www/html/nazifah/ -type d -exec chmod 755 {} \;

# Set file permissions  
find /var/www/html/nazifah/ -type f -exec chmod 644 {} \;

# Set write permissions untuk upload folder
chmod -R 777 /var/www/html/nazifah/src/uploads/
```

### 6. Testing Installation

#### a) Akses Website
1. Buka browser
2. Akses: `http://localhost/nazifah/nazifah/src/`
3. Halaman utama harus muncul tanpa error

#### b) Test Login Admin
1. Klik **"Login"** 
2. Gunakan kredensial:
   - **Email**: `admin@merona.com`
   - **Password**: `admin123`
3. Harus berhasil login ke admin dashboard

#### c) Test Login Customer
1. Gunakan kredensial:
   - **Email**: `customer@merona.com`
   - **Password**: `demo123`

---

## 🔧 Konfigurasi Lanjutan

### 1. Upload Configuration

#### Set Upload Limits (php.ini):
```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M
```

### 2. Security Configuration

#### a) Hide PHP Version
Tambah di `.htaccess`:
```apache
# Hide PHP version
Header unset X-Powered-By
ServerTokens Prod
```

#### b) Secure Uploads Directory
Buat file `.htaccess` di `src/uploads/`:
```apache
# Prevent direct access to uploaded files
Options -Indexes
<Files "*.php">
    Order Deny,Allow
    Deny from all
</Files>
```

### 3. Environment Variables (Optional)

Buat file `.env` untuk konfigurasi:
```env
DB_HOST=localhost
DB_NAME=merona_shop  
DB_USER=root
DB_PASS=

APP_URL=http://localhost/nazifah/nazifah/src/
APP_DEBUG=true

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
```

---

## 🔍 Troubleshooting

### Masalah Umum:

#### 1. "Connection error" / Database tidak terhubung
**Solusi:**
- Pastikan MySQL service running di XAMPP
- Cek konfigurasi database di `src/config/database.php`
- Verifikasi username/password MySQL

#### 2. "403 Forbidden" atau halaman tidak bisa diakses
**Solusi:**
- Pastikan file berada di folder yang benar
- Cek permissions folder (Linux/macOS)
- Restart Apache service

#### 3. Gambar tidak muncul
**Solusi:**
- Cek permissions folder `src/uploads/`
- Pastikan path gambar benar
- Verify upload_max_filesize di php.ini

#### 4. Session issues / Login tidak berfungsi
**Solusi:**
```php
// Tambah di awal file PHP
session_start();
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
```

#### 5. CSS/JS tidak load
**Solusi:**
- Clear browser cache
- Cek path file CSS/JS
- Pastikan Apache mod_rewrite enabled

### Error Logs:
- **Apache**: `C:\xampp\apache\logs\error.log`
- **MySQL**: `C:\xampp\mysql\data\[computername].err`
- **PHP**: Cek di phpinfo() untuk log location

---

## ✅ Checklist Post-Installation

- [ ] Database terkoneksi tanpa error
- [ ] Admin login berhasil
- [ ] Customer registration berfungsi
- [ ] Upload gambar produk berfungsi  
- [ ] Shopping cart berfungsi
- [ ] Checkout process berfungsi
- [ ] Email notifications bekerja (jika dikonfigurasi)
- [ ] Responsive design di mobile

---

## 🚀 Production Deployment

### For Live Server:

#### 1. Database Setup
```sql
-- Buat user database terpisah
CREATE USER 'merona_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON merona_shop.* TO 'merona_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 2. Security Hardening
- Update default passwords
- Enable SSL certificate
- Configure firewall rules
- Regular security updates
- Database backup strategy

#### 3. Performance Optimization
- Enable PHP OPcache
- Configure Apache/Nginx caching
- Optimize database queries
- Compress static assets
- Use CDN for images

---

## 📞 Support

Jika mengalami kesulitan dalam instalasi:

1. **Check Documentation**: Baca kembali langkah instalasi
2. **Error Logs**: Periksa log error Apache/MySQL/PHP
3. **Community Support**: Post di forum atau grup developer
4. **Professional Support**: Hubungi developer untuk bantuan teknis

**Contact Info:**
- 📧 Email: support@merona.com
- 💬 WhatsApp: +62 812-3456-7890
- 🌐 Website: https://merona-fashion.com

---

*Instalasi berhasil! Selamat menggunakan Merona Fashion E-Commerce Platform* 🎉

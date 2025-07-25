# 🌸 Merona Fashion - E-Commerce Platform

<div align="center">

![Merona Fashion Logo](https://img.shields.io/badge/Merona-Fashion-FF69B4?style=for-the-badge&logo=shopping-bag&logoColor=white)

**Fashion That Speaks You** ✨

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)](https://javascript.com/)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)

</div>

---

## 💖 Tentang Merona Fashion

**Merona Fashion** adalah platform e-commerce modern yang didedikasikan untuk wanita yang ingin tampil memukau dengan koleksi fashion terbaru. Kami menghadirkan pengalaman berbelanja online yang menyenangkan dengan antarmuka yang cantik dan fungsionalitas yang lengkap.

### ✨ Mengapa Memilih Merona?

- 🛍️ **Koleksi Lengkap**: Atasan, Bawahan, Dress, dan Outer terkini
- 💅 **Desain Elegan**: Interface yang cantik dan user-friendly
- 🔒 **Keamanan Terjamin**: Sistem keamanan berlapis untuk transaksi
- 📱 **Responsive Design**: Sempurna di desktop, tablet, dan mobile
- ⚡ **Performa Cepat**: Loading time yang optimal untuk pengalaman terbaik

---

## 🚀 Fitur Utama

### 👥 Untuk Customer
- ✅ **Registrasi & Login** - Sistem autentikasi yang aman
- 🛒 **Shopping Cart** - Kelola pembelian dengan mudah  
- 💳 **Multiple Payment** - Bank Transfer, E-Wallet, COD
- 📦 **Order Tracking** - Pantau status pesanan real-time
- ⭐ **Product Reviews** - Beri review dan rating produk
- 💌 **Wishlist** - Simpan produk favorit

### 🔧 Untuk Admin
- 📊 **Dashboard Analytics** - Laporan penjualan dan statistik
- 📝 **Product Management** - CRUD produk dan kategori
- 👤 **User Management** - Kelola data pengguna
- 📋 **Order Management** - Proses dan kelola pesanan
- 💰 **Payment Verification** - Verifikasi bukti pembayaran
- 📈 **Sales Reports** - Export laporan dalam berbagai format

---

## 🛠️ Tech Stack

| Frontend | Backend | Database | Tools |
|----------|---------|----------|--------|
| HTML5 | PHP 7.4+ | MySQL 8.0+ | XAMPP |
| TailwindCSS | PDO | - | Visual Studio Code |
| JavaScript | Session Management | - | Git |
| Font Awesome | File Upload | - | Composer |

---

## 📚 Dokumentasi Lengkap

Dokumentasi aplikasi telah diorganisir dalam file-file terpisah untuk kemudahan akses:

### 📖 Dokumentasi Utama:
- **[📦 INSTALLATION.md](INSTALLATION.md)** - Panduan instalasi lengkap dengan troubleshooting
- **[🗄️ DATABASE.md](DATABASE.md)** - Struktur database, ERD, dan relasi tabel
- **[👥 USAGE.md](USAGE.md)** - Panduan penggunaan untuk admin dan customer
- **[📋 README.md](README.md)** - Overview aplikasi (file ini)

### 🚀 Quick Start:

1. **Instalasi Cepat**
   ```bash
   # Download/Extract project ke xampp/htdocs/nazifah/
   # Import database: sql/merona_shop.sql
   # Akses: http://localhost/nazifah/nazifah/src/
   ```

2. **Login Default**
   ```
   👨‍💼 Admin: admin@merona.com / admin123
   👤 Customer: customer@merona.com / demo123
   ```

3. **Untuk dokumentasi detail, baca:**
   - [📦 Panduan Instalasi Lengkap](INSTALLATION.md)
   - [🗄️ Struktur Database & ERD](DATABASE.md) 
   - [👥 Cara Penggunaan Aplikasi](USAGE.md)

---

## 📚 Struktur Proyek

```
nazifah/
├── 📁 admin/                 # Panel administrasi
│   ├── dashboard.php         # Dashboard utama admin
│   ├── products.php          # Manajemen produk
│   ├── orders.php           # Manajemen pesanan
│   └── users.php            # Manajemen pengguna
├── 📁 ajax/                  # Handler AJAX requests
│   ├── add-to-cart.php      # Tambah ke keranjang
│   └── verify-payment.php    # Verifikasi pembayaran
├── 📁 assets/               # Asset statis
│   ├── css/style.css        # Custom styling
│   └── js/script.js         # Custom JavaScript
├── 📁 config/               # Konfigurasi
│   └── database.php         # Koneksi database
├── 📁 database/             # Database files
│   └── merona_shop.sql      # Database schema
├── 📁 includes/             # File include
│   ├── header.php           # Header template
│   ├── footer.php           # Footer template
│   └── functions.php        # Helper functions
├── 📁 uploads/              # File uploads
│   ├── payments/            # Bukti pembayaran
│   └── products/            # Gambar produk
├── index.php               # Homepage redirect
├── merona-shop.php         # Homepage utama
├── login.php               # Halaman login
├── register.php            # Halaman registrasi
├── products.php            # Katalog produk
├── cart.php                # Keranjang belanja
├── checkout.php            # Halaman checkout
└── README.md               # Dokumentasi ini
```

---

## 🎨 Screenshots

<details>
<summary>🏠 Homepage</summary>

![Homepage](https://via.placeholder.com/800x400/FF69B4/FFFFFF?text=Merona+Homepage)
*Homepage dengan hero section dan featured products*

</details>

<details>
<summary>🛍️ Product Catalog</summary>

![Products](https://via.placeholder.com/800x400/FF1493/FFFFFF?text=Product+Catalog)
*Halaman katalog dengan filter dan pencarian*

</details>

<details>
<summary>🛒 Shopping Cart</summary>

![Cart](https://via.placeholder.com/800x400/FF6347/FFFFFF?text=Shopping+Cart)
*Keranjang belanja dengan summary*

</details>

<details>
<summary>👨‍💼 Admin Dashboard</summary>

![Admin](https://via.placeholder.com/800x400/4169E1/FFFFFF?text=Admin+Dashboard)
*Dashboard admin dengan analytics*

</details>

---

## 🔐 Keamanan

### Fitur Keamanan Implementasi:
- ✅ **SQL Injection Prevention** - Menggunakan PDO prepared statements
- ✅ **Password Encryption** - Bcrypt hashing untuk password
- ✅ **Session Management** - Secure session handling
- ✅ **CSRF Protection** - Cross-site request forgery protection
- ✅ **Input Validation** - Server-side dan client-side validation
- ✅ **File Upload Security** - Validasi dan sanitasi file upload

### Default Accounts:
```
👤 Admin Account:
Email: admin@merona.com
Password: admin123

👤 Demo Customer:
Email: customer@merona.com  
Password: customer
```

---

## 🚀 Deployment

### Production Checklist:
- [ ] Update database credentials
- [ ] Enable error logging
- [ ] Setup SSL certificate
- [ ] Configure file permissions
- [ ] Setup backup strategy
- [ ] Enable security headers

### Recommended Hosting:
- 🌐 **Shared Hosting**: Niagahoster, Hostinger
- ☁️ **VPS**: DigitalOcean, Vultr, AWS EC2
- 📊 **Requirements**: PHP 7.4+, MySQL 8.0+, 1GB RAM

---

## 🤝 Kontribusi

Kami sangat menghargai kontribusi dari komunitas! 💕

### Cara Berkontribusi:
1. 🍴 **Fork** repository ini
2. 🌿 **Create branch** untuk fitur baru (`git checkout -b feature/AmazingFeature`)
3. 💾 **Commit** perubahan (`git commit -m 'Add some AmazingFeature'`)
4. 📤 **Push** ke branch (`git push origin feature/AmazingFeature`)
5. 🔃 **Open Pull Request**

### Bug Reports & Feature Requests:
- 🐛 [Report Bug](https://github.com/username/merona-fashion/issues/new?assignees=&labels=bug&template=bug_report.md)
- 💡 [Request Feature](https://github.com/username/merona-fashion/issues/new?assignees=&labels=enhancement&template=feature_request.md)

---

## 📞 Support & Contact

### 💌 Tim Merona Fashion:
- 📧 **Email**: info@merona.com
- 📱 **WhatsApp**: +62 812-3456-7890
- 🌐 **Website**: https://merona-fashion.com
- 📍 **Alamat**: Jl. Fashion Street No. 123, Jakarta

### 🔗 Social Media:
- 📘 [Facebook](https://facebook.com/meronafashion)
- 📷 [Instagram](https://instagram.com/meronafashion)
- 🐦 [Twitter](https://twitter.com/meronafashion)
- 💼 [LinkedIn](https://linkedin.com/company/meronafashion)

---

## 📋 Changelog

### Version 2.0.0 (Current)
- ✨ Redesign complete UI/UX
- 🚀 Performance improvements
- 📱 Mobile responsive design
- 🔒 Enhanced security features
- 💳 Multiple payment methods
- 📊 Advanced admin dashboard

### Version 1.0.0
- 🎉 Initial release
- 🛒 Basic e-commerce functionality
- 👤 User authentication
- 📦 Order management

---

## 📜 License

Proyek ini dilisensikan di bawah **MIT License** - lihat file [LICENSE](LICENSE) untuk detail lebih lanjut.

```
MIT License

Copyright (c) 2024 Merona Fashion

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction...
```

---

## ⭐ Acknowledgments

Terima kasih kepada:
- 💝 **Bootstrap & TailwindCSS** - Framework CSS yang luar biasa
- 🎨 **Font Awesome** - Icon yang cantik dan lengkap  
- 📷 **Unsplash** - Gambar berkualitas tinggi
- 👥 **Contributors** - Semua yang telah berkontribusi
- ☕ **Kopi** - Fuel untuk coding session!

---

<div align="center">

**Dibuat dengan 💖 oleh Tim Merona Fashion**

⭐ **Jika proyek ini membantu, berikan star ya!** ⭐

[![GitHub stars](https://img.shields.io/github/stars/username/merona-fashion?style=social)](https://github.com/username/merona-fashion/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/username/merona-fashion?style=social)](https://github.com/username/merona-fashion/network)

</div>

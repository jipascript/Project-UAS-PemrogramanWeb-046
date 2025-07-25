# 👥 Panduan Penggunaan Merona Fashion

## 🎯 Overview Aplikasi

**Merona Fashion** adalah platform e-commerce fashion wanita yang menyediakan dua tipe pengguna:
- **👤 Admin**: Mengelola sistem, produk, pesanan, dan pengguna
- **🛍️ Customer**: Berbelanja produk fashion dan mengelola pesanan

URL Akses: `http://localhost/nazifah/nazifah/src/`

---

## 🔐 Sistem Login & Autentikasi

### Default User Accounts:

#### 👨‍💼 Admin Account:
- **Email**: `admin@merona.com`
- **Password**: `admin123`
- **Role**: Administrator

#### 👤 Customer Account:
- **Email**: `customer@merona.com`
- **Password**: `demo123`
- **Role**: Customer

### Cara Login:
1. Klik tombol **"Login"** di halaman utama
2. Masukkan email dan password
3. Klik **"Login"**
4. Sistem akan redirect sesuai role:
   - Admin → Admin Dashboard
   - Customer → Homepage

### Registrasi Customer Baru:
1. Klik **"Register"** di halaman login
2. Isi form registrasi:
   - Nama lengkap
   - Email (harus unik)
   - Password (minimal 6 karakter)
   - Konfirmasi password
3. Klik **"Register"**
4. Login menggunakan akun yang baru dibuat

---

## 🏠 Navigasi Utama (Customer)

### Header Navigation:
- **🏠 Home**: Halaman utama dengan featured products
- **🛍️ Products**: Katalog lengkap produk
- **📂 Categories**: Produk berdasarkan kategori
- **📞 Contact**: Informasi kontak toko
- **ℹ️ About**: Tentang Merona Fashion

### User Menu (Setelah Login):
- **👤 Profile**: Edit profil pengguna
- **🛒 Cart**: Keranjang belanja
- **📦 Orders**: Riwayat pesanan
- **🚪 Logout**: Keluar dari sistem

---

## 🛍️ Panduan Customer

### 1. **Browsing Produk**

#### a) Dari Homepage:
- Lihat **Featured Products** di bagian utama
- Klik **"View All Products"** untuk katalog lengkap
- Gunakan **Quick Search** di header

#### b) Dari Halaman Products:
- Lihat semua produk dalam grid layout
- Gunakan **Filter by Category** di sidebar
- Gunakan **Search Bar** untuk pencarian spesifik
- Urutkan berdasarkan:
  - Nama (A-Z)
  - Harga (Low to High / High to Low)
  - Terbaru

#### c) Detail Produk:
1. Klik produk untuk melihat detail
2. Informasi yang tersedia:
   - Gambar produk
   - Nama dan deskripsi
   - Harga
   - Stok tersedia
   - Rating dan review (jika ada)
3. **Action Buttons**:
   - **Add to Cart**: Tambah ke keranjang
   - **Buy Now**: Langsung checkout

### 2. **Shopping Cart Management**

#### a) Menambah ke Cart:
1. Dari halaman produk, klik **"Add to Cart"**
2. Produk otomatis masuk ke keranjang
3. Notifikasi konfirmasi muncul
4. Cart counter di header bertambah

#### b) Mengelola Cart:
1. Klik **Cart** di header atau **Cart Icon**
2. Di halaman cart Anda dapat:
   - **Update Quantity**: Ubah jumlah item
   - **Remove Item**: Hapus item dari cart
   - **View Total**: Lihat total harga
   - **Continue Shopping**: Kembali berbelanja
   - **Proceed to Checkout**: Lanjut ke pembayaran

### 3. **Proses Checkout**

#### a) Informasi Pengiriman:
1. Klik **"Proceed to Checkout"** dari cart
2. Isi informasi pengiriman:
   - **Nama Penerima**
   - **Alamat Lengkap**
   - **Nomor Telepon**
   - **Kota**
   - **Kode Pos**
   - **Catatan** (opsional)

#### b) Pilih Metode Pembayaran:
- **Bank Transfer**: Transfer ke rekening toko
- **E-Wallet**: Pembayaran digital
- **COD**: Cash on Delivery

#### c) Konfirmasi Pesanan:
1. Review pesanan dan total biaya
2. Pastikan informasi sudah benar
3. Klik **"Place Order"**
4. Sistem generate **Transaction Code**
5. Redirect ke halaman konfirmasi

### 4. **Pembayaran & Upload Bukti**

#### a) Untuk Bank Transfer:
1. Transfer sesuai total yang tertera ke:
   - **Bank**: [Nama Bank]
   - **No. Rekening**: [Nomor Rekening]
   - **Atas Nama**: [Nama Penerima]

#### b) Upload Bukti Pembayaran:
1. Di halaman **Order Detail** atau **My Orders**
2. Klik **"Upload Payment Proof"**
3. Isi form:
   - **Nama Rekening Pengirim**
   - **Nomor Rekening Pengirim**
   - **Tanggal Transfer**
   - **Upload Gambar** bukti transfer
4. Klik **"Submit Payment"**
5. Status berubah menjadi "Waiting for Verification"

### 5. **Order Tracking**

#### a) Melihat Pesanan:
1. Login ke akun
2. Klik **"My Orders"** di menu user
3. Lihat daftar semua pesanan dengan status:
   - **Pending**: Menunggu pembayaran
   - **Paid**: Sudah dibayar, menunggu verifikasi
   - **Processed**: Sedang diproses
   - **Shipped**: Sudah dikirim
   - **Delivered**: Sudah diterima
   - **Cancelled**: Dibatalkan

#### b) Detail Pesanan:
1. Klik **"View Details"** pada pesanan
2. Informasi yang ditampilkan:
   - Transaction Code
   - Status pesanan
   - Items yang dibeli
   - Total pembayaran
   - Informasi pengiriman
   - History status
   - Upload bukti pembayaran (jika belum)

### 6. **Profile Management**

#### a) Update Profil:
1. Klik **"Profile"** di menu user
2. Edit informasi:
   - Nama lengkap
   - Email
   - Nomor telepon
   - Alamat
3. Klik **"Update Profile"**

#### b) Ganti Password:
1. Di halaman profile
2. Isi **Current Password**
3. Isi **New Password**
4. Konfirmasi **New Password**
5. Klik **"Change Password"**

---

## 👨‍💼 Panduan Admin

### 1. **Admin Dashboard**

#### a) Overview Statistics:
- **Total Users**: Jumlah pengguna terdaftar
- **Total Products**: Jumlah produk
- **Total Orders**: Jumlah pesanan
- **Total Revenue**: Total pendapatan
- **Recent Orders**: Pesanan terbaru
- **Low Stock Alert**: Produk stok menipis

#### b) Quick Actions:
- **Add New Product**
- **View All Orders**
- **Manage Users**
- **Reports**

### 2. **Product Management**

#### a) Lihat Semua Produk:
1. Menu **"Products"** → **"All Products"**
2. Tabel berisi:
   - ID, Nama Produk
   - Kategori
   - Harga
   - Stok
   - Status
   - Actions (Edit/Delete)

#### b) Tambah Produk Baru:
1. Klik **"Add New Product"**
2. Isi form:
   - **Nama Produk**
   - **Kategori** (dropdown)
   - **Deskripsi**
   - **Harga**
   - **Stok**
   - **Status** (Active/Inactive)
   - **Upload Gambar**
3. Klik **"Save Product"**

#### c) Edit Produk:
1. Klik **"Edit"** pada produk
2. Update informasi yang diperlukan
3. Klik **"Update Product"**

#### d) Hapus Produk:
1. Klik **"Delete"** pada produk
2. Konfirmasi penghapusan
3. Produk akan dihapus permanen

### 3. **Category Management**

#### a) Lihat Kategori:
1. Menu **"Products"** → **"Categories"**
2. Lihat daftar kategori dengan:
   - Nama kategori
   - Deskripsi
   - Jumlah produk
   - Actions

#### b) Tambah Kategori:
1. Klik **"Add New Category"**
2. Isi nama dan deskripsi
3. Klik **"Save Category"**

### 4. **Order Management**

#### a) Lihat Semua Pesanan:
1. Menu **"Orders"** → **"All Orders"**
2. Tabel pesanan berisi:
   - Transaction Code
   - Customer Name
   - Total Amount
   - Status
   - Date
   - Actions

#### b) Detail Pesanan:
1. Klik **"View"** pada pesanan
2. Informasi lengkap:
   - Data customer
   - Items yang dibeli
   - Alamat pengiriman
   - Status pembayaran
   - Bukti transfer (jika ada)

#### c) Update Status Pesanan:
1. Di halaman detail pesanan
2. Pilih status baru dari dropdown:
   - Pending → Paid → Processed → Shipped → Delivered
3. Klik **"Update Status"**

#### d) Verifikasi Pembayaran:
1. Lihat bukti pembayaran yang diupload
2. Cek kesesuaian dengan data transfer
3. Update status menjadi **"Paid"** jika valid
4. Atau **"Cancelled"** jika tidak valid

### 5. **User Management**

#### a) Lihat Semua User:
1. Menu **"Users"** → **"All Users"**
2. Tabel users:
   - Name, Email
   - Role (Admin/Customer)
   - Registered Date
   - Actions

#### b) Tambah User Baru:
1. Klik **"Add New User"**
2. Isi form:
   - Nama, Email
   - Password
   - Role
   - Phone & Address (opsional)
3. Klik **"Save User"**

#### c) Edit User:
1. Klik **"Edit"** pada user
2. Update informasi
3. Bisa mengubah role atau status

### 6. **Reports & Analytics**

#### a) Sales Report:
1. Menu **"Reports"** → **"Sales Report"**
2. Filter berdasarkan:
   - Date Range
   - Status
   - Payment Method
3. Export ke Excel/PDF

#### b) Product Report:
- Produk terlaris
- Stok menipis
- Revenue per produk

#### c) Customer Report:
- New registrations
- Customer activity
- Purchase history

### 7. **Settings Management**

#### a) Site Settings:
1. Menu **"Settings"** → **"Site Settings"**
2. Update:
   - **Site Name**
   - **Logo** (upload)
   - **Contact Email**
   - **Phone Number**
   - **Address**
   - **About Us**

#### b) Payment Settings:
- Bank account details
- Payment methods
- Auto-verify settings

---

## 🔧 Tips & Best Practices

### Untuk Customer:
1. **Gunakan Search**: Manfaatkan fitur pencarian untuk menemukan produk cepat
2. **Check Stock**: Pastikan stok tersedia sebelum checkout
3. **Save Contact**: Simpan nomor customer service untuk bantuan
4. **Review Orders**: Selalu cek detail pesanan sebelum konfirmasi
5. **Upload Bukti Cepat**: Upload bukti transfer segera setelah pembayaran

### Untuk Admin:
1. **Regular Backup**: Backup database secara berkala
2. **Monitor Stock**: Pantau stok produk untuk restock tepat waktu
3. **Quick Response**: Verifikasi pembayaran secepat mungkin
4. **Update Status**: Selalu update status pesanan untuk transparansi
5. **Customer Service**: Respond customer inquiries dengan cepat

---

## ❓ Troubleshooting Umum

### Customer Issues:

#### 1. **Tidak bisa login**
- Cek email dan password
- Pastikan akun sudah terdaftar
- Clear browser cache
- Hubungi admin jika masih bermasalah

#### 2. **Produk tidak muncul di cart**
- Refresh halaman
- Cek apakah sudah login
- Pastikan produk masih tersedia

#### 3. **Checkout tidak berfungsi**
- Pastikan cart tidak kosong
- Isi semua field yang required
- Cek koneksi internet

#### 4. **Upload bukti gagal**
- Cek ukuran file (max 5MB)
- Format yang didukung: JPG, PNG, PDF
- Coba browser lain

### Admin Issues:

#### 1. **Dashboard tidak load**
- Cek koneksi database
- Restart web server
- Cek error logs

#### 2. **Upload produk gagal**
- Cek permissions folder uploads
- Pastikan ukuran gambar sesuai
- Format yang didukung: JPG, PNG

#### 3. **Report tidak generate**
- Cek date range
- Pastikan ada data
- Coba export format lain

---

## 📞 Dukungan Teknis

### Customer Support:
- 📧 **Email**: support@merona.com
- 💬 **WhatsApp**: +62 812-3456-7890
- 🕒 **Jam Operasional**: 09:00 - 21:00 WIB

### Technical Support:
- 🔧 **Developer**: tech@merona.com
- 📱 **Emergency**: +62 821-xxxx-xxxx
- 🌐 **Documentation**: [Link to docs]

---

## 🎯 Tips Optimasi Penggunaan

### Performance Tips:
1. **Clear Cache**: Clear browser cache secara berkala
2. **Optimize Images**: Kompres gambar sebelum upload
3. **Use Search**: Gunakan pencarian untuk navigasi cepat
4. **Mobile Friendly**: Aplikasi responsive, gunakan mobile untuk akses cepat

### Security Tips:
1. **Strong Password**: Gunakan password yang kuat
2. **Regular Logout**: Logout setelah selesai menggunakan
3. **Secure Connection**: Pastikan menggunakan HTTPS (jika tersedia)
4. **Update Info**: Update informasi profil secara berkala

---

*Panduan ini akan terus diupdate seiring dengan perkembangan fitur aplikasi* 📚

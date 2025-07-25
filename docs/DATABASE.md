# 🗄️ Dokumentasi Struktur Database - Merona Fashion

## 📋 Overview Database

Database **merona_shop** menggunakan MySQL dengan arsitektur relational yang dirancang untuk mendukung sistem e-commerce fashion yang lengkap. Database terdiri dari 12 tabel utama dengan relasi yang terstruktur untuk mengoptimalkan performa dan integritas data.

### Database Information:
- **Nama Database**: `merona_shop`
- **Engine**: InnoDB
- **Character Set**: utf8mb4
- **Collation**: utf8mb4_general_ci
- **Total Tables**: 12

---

## 🏗️ Entity Relationship Diagram (ERD)

```
                              📊 MERONA FASHION DATABASE ERD

    ┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
    │     ROLES       │       │      USERS      │       │ ACTIVITY_LOGS   │
    │─────────────────│       │─────────────────│       │─────────────────│
    │ • id (PK)       │       │ • id (PK)       │←─────┐│ • id (PK)       │
    │ • name          │       │ • name          │      ││ • user_id (FK)  │
    └─────────────────┘       │ • email (UQ)    │      ││ • activity      │
                              │ • phone         │      ││ • timestamp     │
                              │ • address       │      ││ • ip_address    │
                              │ • password      │      │└─────────────────┘
                              │ • role          │      │
                              │ • created_at    │      │
                              │ • updated_at    │      │
                              └─────────────────┘      │
                                       │               │
                               ┌───────┴─────────┐     │
                               │                 │     │
                               ▼                 ▼     │
                  ┌─────────────────┐  ┌─────────────────┐
                  │     CARTS       │  │ TRANSACTIONS    │
                  │─────────────────│  │─────────────────│
                  │ • id (PK)       │  │ • id (PK)       │
                  │ • user_id (FK)  │  │ • user_id (FK)  │──┐
                  │ • product_id(FK)│  │ • trans_code(UQ)│  │
                  │ • quantity      │  │ • total_amount  │  │
                  │ • created_at    │  │ • status        │  │
                  └─────────────────┘  │ • shipping_*    │  │
                           │           │ • payment_method│  │
                           │           │ • notes         │  │
                           │           │ • created_at    │  │
                           │           │ • updated_at    │  │
                           │           └─────────────────┘  │
                           │                    │           │
                           │                    ▼           │
                           │        ┌─────────────────┐     │
                           │        │TRANSACTION_ITEMS│     │
                           │        │─────────────────│     │
                           │        │ • id (PK)       │     │
                           │        │ • trans_id (FK) │─────┘
                           │        │ • product_id(FK)│
                           │        │ • quantity      │
                           │        │ • price         │
                           │        └─────────────────┘
                           │                    │
                           └────────┬───────────┘
                                    │
                                    ▼
                     ┌─────────────────┐       ┌─────────────────┐
                     │   CATEGORIES    │       │    PRODUCTS     │
                     │─────────────────│       │─────────────────│
                     │ • id (PK)       │───────│ • id (PK)       │
                     │ • name          │    ┌──│ • category_id(FK)│──┐
                     │ • description   │    │  │ • name          │  │
                     │ • created_at    │    │  │ • description   │  │
                     │ • updated_at    │    │  │ • price         │  │
                     └─────────────────┘    │  │ • stock         │  │
                                            │  │ • status        │  │
                     ┌─────────────────┐    │  │ • image         │  │
                     │PRODUCT_IMAGES   │    │  │ • created_at    │  │
                     │─────────────────│    │  │ • updated_at    │  │
                     │ • id (PK)       │    │  └─────────────────┘  │
                     │ • product_id(FK)│────┘           │           │
                     │ • image_url     │                │           │
                     └─────────────────┘                │           │
                                                        │           │
                               ┌─────────────────┐      │           │
                               │    REVIEWS      │      │           │
                               │─────────────────│      │           │
                               │ • id (PK)       │      │           │
                               │ • user_id (FK)  │──────┼───────────┘
                               │ • product_id(FK)│──────┘
                               │ • review        │
                               │ • rating        │
                               │ • created_at    │
                               └─────────────────┘

                               ┌─────────────────┐      ┌─────────────────┐
                               │    PAYMENTS     │      │    SETTINGS     │
                               │─────────────────│      │─────────────────│
                               │ • id (PK)       │      │ • id (PK)       │
                               │ • user_id (FK)  │      │ • site_name     │
                               │ • trans_id (FK) │      │ • logo          │
                               │ • payment_method│      │ • contact_email │
                               │ • amount        │      │ • phone         │
                               │ • account_name  │      │ • address       │
                               │ • account_number│      │ • about         │
                               │ • proof_image   │      │ • updated_at    │
                               │ • payment_date  │      └─────────────────┘
                               │ • status        │
                               │ • created_at    │
                               └─────────────────┘
```

---

## 📊 Detail Tabel Database

### 1. **👤 USERS** - Tabel Pengguna
Menyimpan data pengguna sistem (admin dan customer).

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik pengguna |
| `name` | varchar(255) | | NO | | Nama lengkap pengguna |
| `email` | varchar(255) | UQ | NO | | Email unik pengguna |
| `phone` | varchar(20) | | YES | NULL | Nomor telepon |
| `address` | text | | YES | NULL | Alamat lengkap |
| `password` | varchar(255) | | NO | | Password terenkripsi (bcrypt) |
| `role` | varchar(50) | | YES | 'customer' | Role: 'admin' atau 'customer' |
| `created_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu pembuatan akun |
| `updated_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu update terakhir |

**Default Data:**
- Admin: `admin@merona.com` / `admin123`
- Customer: `customer@merona.com` / `demo123`

---

### 2. **🏷️ ROLES** - Tabel Role Pengguna
Menyimpan jenis-jenis role dalam sistem.

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik role |
| `name` | varchar(100) | | NO | | Nama role |

**Data:**
- ID 1: `admin`
- ID 2: `customer`

---

### 3. **📂 CATEGORIES** - Tabel Kategori Produk
Menyimpan kategori produk fashion.

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik kategori |
| `name` | varchar(255) | | NO | | Nama kategori |
| `description` | text | | YES | NULL | Deskripsi kategori |
| `created_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu pembuatan |
| `updated_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu update |

**Default Categories:**
- Atasan - Koleksi atasan wanita
- Bawahan - Koleksi bawahan wanita  
- Dress - Koleksi dress elegant
- Outer - Koleksi jaket dan cardigan

---

### 4. **👗 PRODUCTS** - Tabel Produk
Menyimpan data produk fashion.

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik produk |
| `category_id` | int(11) | FK | YES | NULL | ID kategori (FK ke categories) |
| `name` | varchar(255) | | NO | | Nama produk |
| `description` | text | | YES | NULL | Deskripsi produk |
| `price` | decimal(10,2) | | NO | | Harga produk |
| `stock` | int(11) | | YES | 0 | Jumlah stok |
| `status` | varchar(50) | | YES | 'active' | Status: 'active' atau 'inactive' |
| `image` | varchar(255) | | YES | NULL | URL gambar produk |
| `created_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu pembuatan |
| `updated_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu update |

---

### 5. **🖼️ PRODUCT_IMAGES** - Tabel Gambar Produk
Menyimpan multiple gambar untuk setiap produk.

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik gambar |
| `product_id` | int(11) | FK | YES | NULL | ID produk (FK ke products) |
| `image_url` | varchar(255) | | YES | NULL | URL gambar |

---

### 6. **🛒 CARTS** - Tabel Keranjang Belanja
Menyimpan item dalam keranjang belanja pengguna.

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik cart |
| `user_id` | int(11) | FK | YES | NULL | ID pengguna (FK ke users) |
| `product_id` | int(11) | FK | YES | NULL | ID produk (FK ke products) |
| `quantity` | int(11) | | YES | 1 | Jumlah item |
| `created_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu ditambahkan |

---

### 7. **💳 TRANSACTIONS** - Tabel Transaksi
Menyimpan data transaksi pembelian.

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik transaksi |
| `user_id` | int(11) | FK | YES | NULL | ID pengguna (FK ke users) |
| `transaction_code` | varchar(100) | UQ | YES | NULL | Kode unik transaksi |
| `total_amount` | decimal(10,2) | | YES | NULL | Total pembayaran |
| `status` | varchar(50) | | YES | 'pending' | Status transaksi |
| `shipping_address` | text | | YES | NULL | Alamat pengiriman |
| `shipping_name` | varchar(255) | | YES | NULL | Nama penerima |
| `shipping_phone` | varchar(20) | | YES | NULL | Telp penerima |
| `shipping_city` | varchar(255) | | YES | NULL | Kota pengiriman |
| `shipping_postal_code` | varchar(10) | | YES | NULL | Kode pos |
| `payment_method` | varchar(100) | | YES | NULL | Metode pembayaran |
| `notes` | text | | YES | NULL | Catatan tambahan |
| `created_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu transaksi |
| `updated_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu update |

**Status Values:**
- `pending` - Menunggu pembayaran
- `paid` - Sudah dibayar
- `processed` - Sedang diproses
- `shipped` - Sedang dikirim
- `delivered` - Sudah diterima
- `cancelled` - Dibatalkan

---

### 8. **📦 TRANSACTION_ITEMS** - Tabel Item Transaksi
Menyimpan detail item dalam setiap transaksi.

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik item |
| `transaction_id` | int(11) | FK | YES | NULL | ID transaksi (FK ke transactions) |
| `product_id` | int(11) | FK | YES | NULL | ID produk (FK ke products) |
| `quantity` | int(11) | | YES | NULL | Jumlah item |
| `price` | decimal(10,2) | | YES | NULL | Harga per item saat transaksi |

---

### 9. **💰 PAYMENTS** - Tabel Pembayaran
Menyimpan data pembayaran dan bukti transfer.

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik pembayaran |
| `user_id` | int(11) | FK | YES | NULL | ID pengguna (FK ke users) |
| `transaction_id` | int(11) | FK | YES | NULL | ID transaksi (FK ke transactions) |
| `payment_method` | varchar(100) | | YES | NULL | Metode pembayaran |
| `amount` | decimal(10,2) | | YES | NULL | Jumlah pembayaran |
| `account_name` | varchar(255) | | YES | NULL | Nama rekening pengirim |
| `account_number` | varchar(100) | | YES | NULL | Nomor rekening pengirim |
| `proof_image` | varchar(255) | | YES | NULL | Bukti transfer (gambar) |
| `payment_date` | datetime | | YES | NULL | Tanggal pembayaran |
| `status` | varchar(50) | | YES | 'pending' | Status verifikasi |
| `created_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu upload bukti |

---

### 10. **⭐ REVIEWS** - Tabel Ulasan Produk
Menyimpan review dan rating produk dari customer.

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik review |
| `user_id` | int(11) | FK | YES | NULL | ID pengguna (FK ke users) |
| `product_id` | int(11) | FK | YES | NULL | ID produk (FK ke products) |
| `review` | text | | YES | NULL | Teks ulasan |
| `rating` | int(11) | | YES | NULL | Rating 1-5 |
| `created_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu review |

**Constraint:** `rating >= 1 AND rating <= 5`

---

### 11. **📝 ACTIVITY_LOGS** - Tabel Log Aktivitas
Menyimpan log aktivitas pengguna untuk audit trail.

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik log |
| `user_id` | int(11) | FK | YES | NULL | ID pengguna (FK ke users) |
| `activity` | text | | YES | NULL | Deskripsi aktivitas |
| `timestamp` | datetime | | YES | CURRENT_TIMESTAMP | Waktu aktivitas |
| `ip_address` | varchar(45) | | YES | NULL | IP address pengguna |

**Contoh Activities:**
- User logged in
- User logged out
- Added product to cart
- Order placed
- Payment uploaded

---

### 12. **⚙️ SETTINGS** - Tabel Pengaturan Sistem
Menyimpan konfigurasi aplikasi.

| Field | Type | Key | Null | Default | Description |
|-------|------|-----|------|---------|-------------|
| `id` | int(11) | PK | NO | AUTO_INCREMENT | ID unik setting |
| `site_name` | varchar(255) | | YES | NULL | Nama situs |
| `logo` | varchar(255) | | YES | NULL | Logo situs |
| `contact_email` | varchar(255) | | YES | NULL | Email kontak |
| `phone` | varchar(20) | | YES | NULL | Nomor telepon |
| `address` | text | | YES | NULL | Alamat toko |
| `about` | text | | YES | NULL | Tentang toko |
| `updated_at` | datetime | | YES | CURRENT_TIMESTAMP | Waktu update |

---

## 🔗 Relasi Antar Tabel

### **Primary Relationships:**

1. **users → activity_logs** (1:N)
   - `users.id` → `activity_logs.user_id`

2. **users → carts** (1:N)
   - `users.id` → `carts.user_id`

3. **users → transactions** (1:N)
   - `users.id` → `transactions.user_id`

4. **users → payments** (1:N)
   - `users.id` → `payments.user_id`

5. **users → reviews** (1:N)
   - `users.id` → `reviews.user_id`

6. **categories → products** (1:N)
   - `categories.id` → `products.category_id`

7. **products → product_images** (1:N)
   - `products.id` → `product_images.product_id`

8. **products → carts** (1:N)
   - `products.id` → `carts.product_id`

9. **products → transaction_items** (1:N)
   - `products.id` → `transaction_items.product_id`

10. **products → reviews** (1:N)
    - `products.id` → `reviews.product_id`

11. **transactions → transaction_items** (1:N)
    - `transactions.id` → `transaction_items.transaction_id`

12. **transactions → payments** (1:N)
    - `transactions.id` → `payments.transaction_id`

---

## 🔍 Indexes dan Constraints

### **Primary Keys:**
- Semua tabel memiliki primary key `id` dengan AUTO_INCREMENT

### **Foreign Key Constraints:**
```sql
-- Activity Logs
ALTER TABLE activity_logs 
ADD CONSTRAINT activity_logs_ibfk_1 
FOREIGN KEY (user_id) REFERENCES users (id);

-- Carts
ALTER TABLE carts 
ADD CONSTRAINT carts_ibfk_1 
FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

ALTER TABLE carts 
ADD CONSTRAINT carts_ibfk_2 
FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE;

-- Products
ALTER TABLE products 
ADD CONSTRAINT products_ibfk_1 
FOREIGN KEY (category_id) REFERENCES categories (id);

-- Product Images
ALTER TABLE product_images 
ADD CONSTRAINT product_images_ibfk_1 
FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE;

-- Reviews
ALTER TABLE reviews 
ADD CONSTRAINT reviews_ibfk_1 
FOREIGN KEY (user_id) REFERENCES users (id);

ALTER TABLE reviews 
ADD CONSTRAINT reviews_ibfk_2 
FOREIGN KEY (product_id) REFERENCES products (id);

-- Transactions
ALTER TABLE transactions 
ADD CONSTRAINT transactions_ibfk_1 
FOREIGN KEY (user_id) REFERENCES users (id);

-- Transaction Items
ALTER TABLE transaction_items 
ADD CONSTRAINT transaction_items_ibfk_1 
FOREIGN KEY (transaction_id) REFERENCES transactions (id) ON DELETE CASCADE;

ALTER TABLE transaction_items 
ADD CONSTRAINT transaction_items_ibfk_2 
FOREIGN KEY (product_id) REFERENCES products (id);

-- Payments
ALTER TABLE payments 
ADD CONSTRAINT payments_ibfk_1 
FOREIGN KEY (user_id) REFERENCES users (id);

ALTER TABLE payments 
ADD CONSTRAINT payments_ibfk_2 
FOREIGN KEY (transaction_id) REFERENCES transactions (id);
```

### **Unique Constraints:**
- `users.email` - Email harus unik
- `transactions.transaction_code` - Kode transaksi harus unik

### **Check Constraints:**
- `reviews.rating` - Rating harus antara 1-5

---

## 📈 Query Optimasi

### **Recommended Indexes untuk Performance:**

```sql
-- Index untuk pencarian produk
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_products_name ON products(name);

-- Index untuk cart dan transaksi
CREATE INDEX idx_carts_user ON carts(user_id);
CREATE INDEX idx_transactions_user ON transactions(user_id);
CREATE INDEX idx_transactions_status ON transactions(status);

-- Index untuk log aktivitas
CREATE INDEX idx_activity_logs_user ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_timestamp ON activity_logs(timestamp);

-- Index untuk reviews
CREATE INDEX idx_reviews_product ON reviews(product_id);
CREATE INDEX idx_reviews_rating ON reviews(rating);
```

---

## 🔧 Backup dan Maintenance

### **Regular Maintenance Tasks:**

1. **Backup Database:**
```bash
mysqldump -u root -p merona_shop > backup_merona_shop_$(date +%Y%m%d).sql
```

2. **Optimize Tables:**
```sql
OPTIMIZE TABLE users, products, transactions, carts;
```

3. **Clean Old Logs:**
```sql
DELETE FROM activity_logs WHERE timestamp < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

4. **Check Table Integrity:**
```sql
CHECK TABLE users, products, transactions;
```

---

## 📊 Database Statistics

### **Current Data Volume:**
- **Users**: 2 records (1 admin, 1 customer)
- **Categories**: 4 records
- **Products**: 10 records
- **Activity Logs**: 16+ records
- **Settings**: 1 record

### **Storage Considerations:**
- **Text fields**: UTF-8 encoding untuk international support
- **Decimal fields**: 10,2 precision untuk currency
- **Timestamps**: Automatic creation dan update tracking
- **File uploads**: Path storage untuk gambar

---

*Database documentation ini akan diupdate seiring dengan perkembangan aplikasi* 📝

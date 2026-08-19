# 🧶 Crochy — Crochet E-Commerce Website

**Crochy** adalah website e-commerce sederhana yang dibuat untuk menjual berbagai produk kerajinan **crochet/rajut**, seperti boneka rajut, gantungan kunci, dan produk handmade lainnya.

Website ini dikembangkan menggunakan **PHP, MySQL, HTML, CSS, dan JavaScript** dengan sistem autentikasi berbasis role sehingga pengguna dapat berperan sebagai **pembeli** atau **penjual**.

## 🌐 Demo Website

Website Crochy dapat diakses melalui:

* **https://crochy.rf.gd/**

---

## 📌 Fitur Utama

### 👤 Pembeli

Pembeli dapat melakukan beberapa aktivitas berikut:

* 🏠 Melihat halaman beranda
* 🛍️ Melihat katalog produk
* 🔎 Melihat informasi dan harga produk
* 🛒 Menambahkan produk ke keranjang
* ➕ Mengatur jumlah produk di keranjang
* 🎟️ Menggunakan kode promo/diskon
* 📦 Mengisi data pengiriman
* 💳 Melakukan proses pembayaran
* 🧾 Mengunggah bukti pembayaran
* 📋 Melihat riwayat pesanan
* 👤 Mengelola profil
* 🔐 Register, login, dan logout

### 🏪 Penjual / Seller

Seller Centre menyediakan fitur pengelolaan toko, antara lain:

* 📊 Dashboard penjual
* 📦 Mengelola produk
* 🎟️ Mengelola diskon
* 🖼️ Mengelola foto hero/banner
* 🛒 Mengelola pesanan
* 👥 Melihat data pelanggan
* 👤 Mengelola profil toko
* 📈 Melihat laporan penjualan
* ✅ Melakukan verifikasi pesanan

---

## 🔐 Sistem Autentikasi dan Role

Crochy menggunakan sistem **Role-Based Access Control (RBAC)**.

Terdapat dua role utama:

| Role      | Akses                                                                     |
| --------- | ------------------------------------------------------------------------- |
| `pembeli` | Beranda, produk, keranjang, checkout, pembayaran, riwayat pesanan, profil |
| `penjual` | Seller Centre, dashboard, produk, diskon, pesanan, laporan, profil, hero  |

Sistem autentikasi menggunakan:

* PHP Session
* `password_hash()`
* `password_verify()`
* Role pada tabel `users`
* Middleware sederhana melalui `seller/includes/auth_check.php`

Pengguna dengan role pembeli tidak dapat mengakses halaman Seller Centre.

---

## 🛠️ Teknologi yang Digunakan

| Teknologi       | Kegunaan                        |
| --------------- | ------------------------------- |
| **PHP**         | Backend dan pemrosesan aplikasi |
| **MySQL**       | Database                        |
| **HTML5**       | Struktur halaman                |
| **CSS3**        | Tampilan dan layout             |
| **JavaScript**  | Interaksi pada sisi client      |
| **MySQLi**      | Koneksi PHP dengan database     |
| **PHP Session** | Manajemen sesi pengguna         |
| **Git/GitHub**  | Version control dan repository  |

---

## 📁 Struktur Project

```text
crochy/
│
├── assets/
│   ├── bukti_bayar/
│   ├── hero/
│   ├── produk/
│   └── profil/
│
├── auth/
│   ├── login.php
│   ├── logout.php
│   └── register.php
│
├── css/
│   └── style.css
│
├── js/
│   └── main.js
│
├── partials/
│   ├── bootstrap.php
│   ├── footer.php
│   ├── head.php
│   └── navbar.php
│
├── seller/
│   ├── includes/
│   │   ├── auth_check.php
│   │   ├── footer.php
│   │   └── header.php
│   │
│   ├── dashboard.php
│   ├── diskon.php
│   ├── hero.php
│   ├── laporan.php
│   ├── pesanan.php
│   ├── produk.php
│   └── profil.php
│
├── checkout-alamat.php
├── checkout-bayar.php
├── config.php
├── database_localhost.sql
├── diskon.php
├── index.php
├── install_penjual.php
├── keranjang.php
├── pesanan-saya.php
├── produk.php
├── profil.php
└── cara-pesan.php
```

---

## 🗄️ Database

Crochy menggunakan database **MySQL** untuk menyimpan data aplikasi.

Beberapa data utama yang digunakan antara lain:

* `users`
* `produk`
* `diskon`
* `profil`
* `hero_foto`
* `pesanan`
* `pesanan_item`

Database lokal disediakan dalam file:

```text
database_localhost.sql
```

File tersebut dapat digunakan untuk membuat struktur database ketika menjalankan project secara lokal.

---

## ⚙️ Instalasi di Localhost

### 1. Clone Repository

```bash
git clone https://github.com/USERNAME/crochy.git
```

Masuk ke folder project:

```bash
cd crochy
```

### 2. Jalankan Web Server

Project dapat dijalankan menggunakan:

* XAMPP
* Laragon
* WAMP
* Apache + PHP + MySQL

Jika menggunakan XAMPP, letakkan folder project di:

```text
C:\xampp\htdocs\crochy
```

Kemudian jalankan:

* Apache
* MySQL

### 3. Buat Database

Buka:

```text
http://localhost/phpmyadmin
```

Buat database baru, kemudian import:

```text
database_localhost.sql
```

### 4. Konfigurasi Database

Edit:

```text
config.php
```

Sesuaikan konfigurasi database:

```php
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "crochy_db";
```

Pastikan nama database sesuai dengan database yang dibuat di MySQL.

### 5. Jalankan Website

Buka browser dan akses:

```text
http://localhost/crochy/
```

---

## 🛒 Alur Pembelian

Alur utama pembelian pada website:

```text
Beranda
   ↓
Katalog Produk
   ↓
Tambah ke Keranjang
   ↓
Keranjang
   ↓
Kode Promo
   ↓
Checkout
   ↓
Data Pengiriman
   ↓
Pembayaran
   ↓
Upload Bukti Pembayaran
   ↓
Pesanan Diproses
   ↓
Verifikasi Seller
   ↓
Pesanan Selesai
```

---

## 🏪 Alur Seller

```text
Login sebagai Penjual
        ↓
   Seller Centre
        ↓
     Dashboard
        ↓
 ┌──────┼────────┬─────────┐
 ↓      ↓        ↓         ↓
Produk Diskon  Pesanan   Laporan
 ↓      ↓        ↓         ↓
Kelola Kelola  Verifikasi Statistik
```

---

## 🔒 Keamanan

Beberapa mekanisme keamanan yang diterapkan pada aplikasi:

### Password Hashing

Password pengguna tidak disimpan secara langsung, tetapi menggunakan:

```php
password_hash()
```

Saat login, password diverifikasi menggunakan:

```php
password_verify()
```

### Prepared Statement

Beberapa query database menggunakan prepared statement, contohnya:

```php
$stmt = $koneksi->prepare(
    "SELECT id, nama, password, role FROM users WHERE email = ?"
);
```

Hal ini membantu mengurangi risiko **SQL Injection**.

### Role-Based Access Control

Halaman Seller Centre dilindungi oleh:

```text
seller/includes/auth_check.php
```

File tersebut memastikan hanya pengguna dengan role `penjual` yang dapat mengakses area seller.

### Validasi Checkout

Pada proses checkout, data produk diambil kembali dari database berdasarkan ID produk sehingga harga yang berasal dari client tidak langsung dipercaya.

---

## 📸 Tampilan Website

### Beranda

Halaman utama menampilkan branding Crochy, hero image, informasi produk, dan navigasi menuju katalog maupun proses pemesanan.

### Katalog Produk

Menampilkan koleksi produk crochet yang tersedia lengkap dengan:

* Foto produk
* Nama produk
* Deskripsi
* Harga
* Tombol tambah ke keranjang

### Keranjang

Pembeli dapat:

* Melihat produk yang dipilih
* Mengubah jumlah produk
* Menghapus produk
* Menggunakan kode promo
* Melihat subtotal
* Melihat potongan harga
* Melanjutkan checkout

### Checkout & Pembayaran

Pembeli mengisi data pengiriman dan mengunggah bukti pembayaran untuk diproses oleh seller.

### Seller Centre

Seller memiliki dashboard khusus untuk mengelola operasional toko.

---

## 🧩 Komponen Utama

### `config.php`

Digunakan untuk:

* Memulai PHP Session
* Membuat koneksi database
* Mengatur koneksi MySQL
* Menggunakan charset `utf8mb4`

### `partials/bootstrap.php`

Berfungsi sebagai bootstrap aplikasi yang digunakan oleh halaman publik untuk menyiapkan data dan koneksi yang diperlukan.

### `partials/navbar.php`

Berisi navigasi utama website.

### `partials/footer.php`

Berisi bagian footer website.

### `auth/`

Berisi sistem autentikasi:

```text
login.php
register.php
logout.php
```

### `seller/`

Berisi seluruh fitur Seller Centre.

---

## 👥 Role Pengguna

### Pembeli

Pembeli dapat:

```text
Register
   ↓
Login
   ↓
Melihat Produk
   ↓
Keranjang
   ↓
Checkout
   ↓
Pembayaran
   ↓
Melihat Pesanan
```

### Penjual

Penjual dapat:

```text
Login
   ↓
Dashboard Seller
   ↓
Kelola Produk
   ↓
Kelola Diskon
   ↓
Kelola Pesanan
   ↓
Verifikasi Pembayaran
   ↓
Laporan Penjualan
```

---

## 🚀 Deployment

Project ini telah digunakan pada hosting berbasis web dan dapat diakses melalui:

**Primary:**

https://crochy.rf.gd/

Untuk deployment sendiri, pastikan:

1. PHP telah tersedia pada hosting.
2. Database MySQL telah dibuat.
3. File `.sql` telah di-import.
4. `config.php` telah disesuaikan dengan kredensial database hosting.
5. Folder `assets/` memiliki permission yang sesuai.
6. Folder `assets/bukti_bayar/` dapat menerima upload file.
7. URL dan path file sudah sesuai dengan konfigurasi hosting.

---

## ⚠️ Catatan Keamanan untuk GitHub

**Jangan mengunggah kredensial database hosting ke repository publik.**

File `config.php` pada project deployment dapat berisi:

```php
$host   = "...";
$user   = "...";
$pass   = "...";
$dbname = "...";
```

Untuk repository GitHub publik, sebaiknya gunakan konfigurasi seperti:

```php
$host   = getenv('DB_HOST');
$user   = getenv('DB_USER');
$pass   = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
```

atau buat file konfigurasi lokal yang dimasukkan ke `.gitignore`.

Contoh `.gitignore`:

```gitignore
config.local.php
.env
*.log
assets/bukti_bayar/*
```

**Jika kredensial database yang pernah digunakan sudah terlanjur dipublikasikan, sebaiknya segera lakukan perubahan password database.**

---

## 📄 Lisensi

Project ini dibuat untuk keperluan **pembelajaran, tugas akademik, dan pengembangan website e-commerce sederhana**.

Silakan menyesuaikan lisensi sesuai kebutuhan repository.

---

## 👨‍💻 Pengembangan

Project **Crochy** dikembangkan sebagai implementasi website e-commerce sederhana yang menggabungkan:

* Frontend website
* Backend PHP
* Database MySQL
* Sistem autentikasi
* Role-Based Access Control
* Manajemen produk
* Manajemen pesanan
* Sistem diskon
* Checkout
* Pembayaran
* Seller Centre

---

## ⭐ Dukungan

Jika project ini bermanfaat, jangan lupa memberikan **⭐ Star** pada repository GitHub.

**Crochy — Handmade with Love 🧶❤️**

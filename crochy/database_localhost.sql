-- 1. TABEL: produk
CREATE TABLE IF NOT EXISTS produk (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(30) NOT NULL,
  deskripsi   VARCHAR(100) NOT NULL,
  harga       DECIMAL(12,2) NOT NULL DEFAULT 0,
  gambar      VARCHAR(30) NOT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO produk (nama, deskripsi, harga, gambar) VALUES
('Tumbler Sarung Rajut', 'Sarung tumbler rajut motif lucu, menjaga minuman tetap hangat/dingin', 45000, 'Rajutan-tumbler.jpeg'),
('Boneka Bebek Berdasi', 'Boneka rajut karakter bebek lengkap dengan dasi kecil yang kece', 78000, 'boneka-bebek-berdasi.jpeg'),
('Boneka Bebek Imut', 'Boneka rajut bebek kuning yang imut dan menggemaskan', 75000, 'boneka-bebek-imoet.jpeg'),
('Boneka Beruang Coklat', 'Boneka rajut beruang coklat yang lembut dan hangat', 90000, 'boneka-beruang.jpeg'),
('Boneka Topi Semangka', 'Boneka rajut lucu dengan topi bermotif semangka', 85000, 'boneka-topi-semangka.jpeg'),
('Gantungan Kunci Buket Bunga', 'Gantungan kunci rajut berbentuk buket bunga mini', 25000, 'gantungan-kunci-buket-bunga.jpeg'),
('Gantungan Kunci Bunga Matahari', 'Gantungan kunci rajut motif bunga matahari yang cerah', 22000, 'gantungan-kunci-bunga-matahari.jpeg'),
('Gantungan Kunci Bunga Mawar', 'Gantungan kunci rajut motif bunga mawar merah', 22000, 'gantungan-kunci-bunga-mawar.jpeg'),
('Gantungan Kunci Bunga', 'Gantungan kunci rajut motif bunga sederhana', 20000, 'gantungan-kunci-bunga.jpeg'),
('Gantungan Kunci Gurita', 'Gantungan kunci rajut karakter gurita yang lucu', 24000, 'gantungan-kunci-gurita.jpeg'),
('Gantungan Kunci Harimau Kecil', 'Gantungan kunci rajut karakter harimau kecil yang menggemaskan', 24000, 'gantungan-kunci-harimau-kecik.jpeg'),
('Gantungan Kunci Pelangi', 'Gantungan kunci rajut motif pelangi warna-warni', 23000, 'gantungan-kunci-pelangi.jpeg');

-- 2. TABEL: hero_foto
CREATE TABLE IF NOT EXISTS hero_foto (
  id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  posisi  ENUM('kiri_atas','kanan_atas','kiri_bawah','kanan_bawah','utama') NOT NULL UNIQUE,
  gambar  VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

REPLACE INTO hero_foto (posisi, gambar) VALUES
('kiri_atas', 'boneka-bebek-berdasi.jpeg'),
('kanan_atas', 'gantungan-kunci-buket-bunga.jpeg'),
('kiri_bawah', 'gantungan-kunci-gurita.jpeg'),
('kanan_bawah', 'gantungan-kunci-harimau-kecik.jpeg'),
('utama', 'gantungan-kunci-pelangi.jpeg');

-- 3. TABEL: profil
CREATE TABLE IF NOT EXISTS profil (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama             VARCHAR(30) NOT NULL,
  role             VARCHAR(30),
  foto             VARCHAR(30),
  bio              TEXT,
  alamat           VARCHAR(100),
  whatsapp         VARCHAR(30),
  email            VARCHAR(30),
  jam_operasional  VARCHAR(30),
  nama_bank        VARCHAR(30) DEFAULT NULL,
  no_rekening      VARCHAR(30)  DEFAULT NULL,
  atas_nama        VARCHAR(30) DEFAULT NULL
) ENGINE=InnoDB;

INSERT INTO profil (nama, role, foto, bio, alamat, whatsapp, email, jam_operasional)
SELECT * FROM (SELECT 'Asykar Diarra', 'Founder & Perajin Crochy', 'asykar.jpg', 'Halo! Saya adalah pembuat Crochy.', 'Pekanbaru, Riau, Indonesia', '6285122185211', 'crochy.handmade@gmail.com', 'Senin - Sabtu, 09.00 - 20.00 WIB') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM profil);

-- 4. TABEL: diskon
CREATE TABLE IF NOT EXISTS diskon (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  judul            VARCHAR(30) NOT NULL,
  deskripsi        VARCHAR(100),
  persen           TINYINT UNSIGNED NOT NULL,
  kode_promo       VARCHAR(30) NOT NULL UNIQUE,
  berlaku_sampai   DATE DEFAULT NULL,
  aktif            TINYINT(1) NOT NULL DEFAULT 1,
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

REPLACE INTO diskon (judul, deskripsi, persen, kode_promo, berlaku_sampai, aktif) VALUES
('Diskon Pelanggan Baru', 'Khusus untuk pembelian pertama', 10, 'CROCHYBARU', '2026-12-31', 1),
('Promo Akhir Bulan', 'Diskon spesial akhir bulan', 15, 'AKHIRBULAN', '2026-07-31', 1),
('Beli 2 Diskon Lebih', 'Diskon tambahan', 5, 'CROCHYHEMAT', NULL, 0);

-- 5. TABEL: users
CREATE TABLE IF NOT EXISTS users (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(30) NOT NULL,
  email       VARCHAR(50) NOT NULL UNIQUE,
  password    VARCHAR(30) NOT NULL,
  role        ENUM('pembeli','penjual') NOT NULL DEFAULT 'pembeli',
  whatsapp    VARCHAR(30) DEFAULT NULL,
  alamat      VARCHAR(50) DEFAULT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 6. TABEL: pesanan
CREATE TABLE IF NOT EXISTS pesanan (
  id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id             INT UNSIGNED NOT NULL,
  nama_penerima       VARCHAR(30) NOT NULL,
  alamat_pengiriman   VARCHAR(100) NOT NULL,
  kode_promo          VARCHAR(30)  DEFAULT NULL,
  persen_diskon       TINYINT UNSIGNED NOT NULL DEFAULT 0,
  subtotal            DECIMAL(12,2) NOT NULL DEFAULT 0,
  potongan            DECIMAL(12,2) NOT NULL DEFAULT 0,
  total               DECIMAL(12,2) NOT NULL DEFAULT 0,
  bukti_bayar         VARCHAR(50) NOT NULL,
  status              ENUM('menunggu_verifikasi','diproses','dikirim','selesai','ditolak') NOT NULL DEFAULT 'menunggu_verifikasi',
  created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pesanan_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- 7. TABEL: pesanan_item
CREATE TABLE IF NOT EXISTS pesanan_item (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pesanan_id    INT UNSIGNED NOT NULL,
  produk_id     INT UNSIGNED DEFAULT NULL,
  nama_produk   VARCHAR(30) NOT NULL,
  harga_satuan  DECIMAL(12,2) NOT NULL,
  jumlah        INT UNSIGNED NOT NULL,
  CONSTRAINT fk_item_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
  CONSTRAINT fk_item_produk  FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE SET NULL
) ENGINE=InnoDB;
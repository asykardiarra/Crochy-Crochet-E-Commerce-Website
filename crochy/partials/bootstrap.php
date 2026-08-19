<?php
// =============================================
// BOOTSTRAP - dipanggil di AWAL setiap halaman (index.php, produk.php, dst)
// Isinya: koneksi DB, cek status login (RBAC), dan ambil semua data
// yang dipakai bersama oleh navbar/footer/halaman (profil, produk, diskon, hero).
// =============================================
require __DIR__ . "/../config.php";

// ===== RBAC: cek status login =====
$userLogin = null;
if (!empty($_SESSION['user_id'])) {
  $userLogin = [
    'id'       => $_SESSION['user_id'],
    'nama'     => $_SESSION['user_nama'] ?? '',
    'role'     => $_SESSION['user_role'] ?? 'pembeli',
    'whatsapp' => $_SESSION['user_whatsapp'] ?? '',
    'alamat'   => $_SESSION['user_alamat'] ?? '',
  ];
}
// customerLogin dipakai khusus untuk auto-isi form checkout (hanya relevan buat pembeli)
$customerLogin = ($userLogin && $userLogin['role'] === 'pembeli') ? $userLogin : null;

// ===== Profil toko (dipakai navbar, footer, halaman Profil, & nomor WA checkout) =====
$profil = $koneksi->query("SELECT * FROM profil LIMIT 1")->fetch_assoc();

// ===== Semua produk (dipakai halaman Beranda/Produk untuk render, & dikirim ke JS) =====
$semuaProduk = [];
$hasilProduk = $koneksi->query("SELECT * FROM produk ORDER BY id ASC");
if ($hasilProduk) {
  while ($row = $hasilProduk->fetch_assoc()) {
    $semuaProduk[] = $row;
  }
}

// ===== Diskon aktif (dipakai halaman Diskon & dikirim ke JS untuk validasi kode promo) =====
$daftarDiskon = [];
$hasilDiskon = $koneksi->query("SELECT * FROM diskon WHERE aktif = 1 ORDER BY id ASC");
if ($hasilDiskon) {
  while ($row = $hasilDiskon->fetch_assoc()) {
    $daftarDiskon[] = $row;
  }
}

// ===== Foto hero (dipakai halaman Beranda) =====
$heroFoto = [];
$hasilHero = $koneksi->query("SELECT * FROM hero_foto");
if ($hasilHero) {
  while ($row = $hasilHero->fetch_assoc()) {
    $heroFoto[$row['posisi']] = $row;
  }
}

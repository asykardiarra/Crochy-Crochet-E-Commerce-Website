<?php
require __DIR__ . "/../config.php";

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

$customerLogin = ($userLogin && $userLogin['role'] === 'pembeli') ? $userLogin : null;
$profil = $koneksi->query("SELECT * FROM profil LIMIT 1")->fetch_assoc();
$semuaProduk = [];
$hasilProduk = $koneksi->query("SELECT * FROM produk ORDER BY id ASC");
if ($hasilProduk) {
  while ($row = $hasilProduk->fetch_assoc()) {
    $semuaProduk[] = $row;
  }
}

$daftarDiskon = [];
$hasilDiskon = $koneksi->query("SELECT * FROM diskon WHERE aktif = 1 ORDER BY id ASC");
if ($hasilDiskon) {
  while ($row = $hasilDiskon->fetch_assoc()) {
    $daftarDiskon[] = $row;
  }
}

$heroFoto = [];
$hasilHero = $koneksi->query("SELECT * FROM hero_foto");
if ($hasilHero) {
  while ($row = $hasilHero->fetch_assoc()) {
    $heroFoto[$row['posisi']] = $row;
  }
}

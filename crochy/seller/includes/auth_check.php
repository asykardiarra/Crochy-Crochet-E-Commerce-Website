<?php
// Panggil require "config.php" SEBELUM file ini di setiap halaman Seller Centre.
// RBAC: hanya user dengan role 'penjual' yang boleh mengakses Seller Centre.
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'penjual') {
  header("Location: ../auth/login.php");
  exit;
}

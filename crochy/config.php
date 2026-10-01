<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$host   = "sql210.infinityfree.com";      
$user   = "if0_42364052";      
$pass   = "DXPcBEyBL0m8HU"; 
$dbname = "if0_42364052_crochy_db";

$koneksi = new mysqli($host, $user, $pass, $dbname);

if ($koneksi->connect_error) {
  die("Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");
?>
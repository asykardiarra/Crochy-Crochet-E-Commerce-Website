<?php
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'penjual') {
  header("Location: ../auth/login.php");
  exit;
}

<?php
require "../config.php";

// ===== Redirect tujuan setelah login (dipakai alur checkout: keranjang -> login -> checkout-alamat) =====
// Dibatasi hanya ke file .php di dalam folder utama (whitelist sederhana) supaya aman dari open-redirect.
$redirectAman = ['checkout-alamat.php', 'checkout-bayar.php', 'pesanan-saya.php', 'keranjang.php'];
$redirect = $_GET['redirect'] ?? ($_POST['redirect'] ?? '');
if (!in_array($redirect, $redirectAman, true)) {
  $redirect = '';
}

if (!empty($_SESSION['user_id'])) {
  if ($_SESSION['user_role'] === 'penjual') {
    header("Location: ../seller/dashboard.php");
  } else {
    header("Location: " . ($redirect !== '' ? '../' . $redirect : '../index.php'));
  }
  exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email    = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($email === '' || $password === '') {
    $error = "Email dan password wajib diisi.";
  } elseif (mb_strlen($email) > 150) {
    $error = "Email maksimal 150 karakter.";
  } elseif (strlen($password) > 72) {
    $error = "Password maksimal 72 karakter.";
  } else {
    $stmt = $koneksi->prepare("SELECT id, nama, password, role, whatsapp, alamat FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
      // ===== RBAC: simpan role ke sesi, dipakai di seluruh sistem untuk menentukan akses & tampilan =====
      $_SESSION['user_id']       = $user['id'];
      $_SESSION['user_nama']     = $user['nama'];
      $_SESSION['user_role']     = $user['role']; // 'pembeli' atau 'penjual'
      $_SESSION['user_whatsapp'] = $user['whatsapp'];
      $_SESSION['user_alamat']   = $user['alamat'];

      if ($user['role'] === 'penjual') {
        header("Location: ../seller/dashboard.php");
      } else {
        header("Location: " . ($redirect !== '' ? '../' . $redirect : '../index.php'));
      }
      exit;
    } else {
      $error = "Email atau password salah.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Crochy</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../css/style.css"/>
</head>
<body>
<div class="auth-page">
  <div class="auth-box">
    <a href="../index.php" class="auth-logo">Crochy</a>
    <p class="auth-subtitle">Login untuk melanjutkan</p>

    <?php if ($error): ?>
      <div class="auth-msg err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required maxlength="150" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="6" maxlength="72">
      </div>
      <button type="submit" class="auth-submit">Login</button>
    </form>

    <div class="auth-footer">Belum punya akun? <a href="register.php<?= $redirect !== '' ? '?redirect=' . urlencode($redirect) : '' ?>">Daftar di sini</a></div>
    <a href="../index.php" class="auth-back" style="color:var(--warm-600)">Kembali ke Beranda</a>
  </div>
</div>
</body>
</html>

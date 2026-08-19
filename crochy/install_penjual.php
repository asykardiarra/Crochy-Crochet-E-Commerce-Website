<?php
/**
 * INSTALL PENJUAL — jalankan SEKALI saja untuk membuat SATU-SATUNYA akun penjual
 * (pemilik toko / Seller Centre). Toko ini hanya boleh punya 1 akun penjual.
 * Setelah berhasil membuat akun, HAPUS file ini dari hosting/server kamu.
 */
require "config.php";

$sukses = "";
$error = "";

$penjualAda = $koneksi->query("SELECT id, nama, email FROM users WHERE role = 'penjual' LIMIT 1")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$penjualAda) {
  $nama     = trim($_POST['nama'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($nama === '' || $email === '' || $password === '') {
    $error = "Nama, email, dan password wajib diisi.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Format email tidak valid.";
  } elseif (strlen($password) < 6) {
    $error = "Password minimal 6 karakter.";
  } else {
    $cek = $koneksi->prepare("SELECT id FROM users WHERE email = ?");
    $cek->bind_param("s", $email);
    $cek->execute();
    if ($cek->get_result()->num_rows > 0) {
      $error = "Email sudah dipakai. Coba email lain.";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $koneksi->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'penjual')");
      $stmt->bind_param("sss", $nama, $email, $hash);
      if ($stmt->execute()) {
        $sukses = "Akun penjual '$nama' berhasil dibuat! Silakan HAPUS file install_penjual.php sekarang, lalu login di auth/login.php";
      } else {
        $error = "Gagal menyimpan akun penjual: " . $koneksi->error;
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Install Penjual - Crochy</title>
<style>
  body{font-family:sans-serif;background:#f8e8d8;display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0;}
  .box{background:#fff;padding:32px;border-radius:12px;max-width:380px;width:90%;box-shadow:0 4px 20px rgba(0,0,0,.1);}
  h1{font-size:20px;margin-top:0;color:#8B4513;}
  label{display:block;margin:12px 0 4px;font-size:14px;color:#333;}
  input{width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;box-sizing:border-box;font-size:14px;}
  button{margin-top:18px;width:100%;padding:10px;background:#8B4513;color:#fff;border:none;border-radius:6px;font-size:15px;cursor:pointer;}
  .ok{background:#e6f4ea;color:#1e7e34;padding:10px;border-radius:6px;font-size:14px;margin-bottom:10px;}
  .err{background:#fdecea;color:#c0392b;padding:10px;border-radius:6px;font-size:14px;margin-bottom:10px;}
  .info{background:#eaf2fd;color:#2c5282;padding:10px;border-radius:6px;font-size:14px;margin-bottom:10px;}
  p.warn{font-size:13px;color:#c0392b;}
</style>
</head>
<body>
  <div class="box">
    <h1>🧶 Setup Akun Penjual Crochy</h1>
    <p class="warn">⚠️ Jalankan sekali saja, lalu hapus file ini dari server.</p>

    <?php if ($sukses): ?><div class="ok"><?= htmlspecialchars($sukses) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if ($penjualAda && !$sukses): ?>
      <div class="info">
        Toko ini sudah punya akun penjual: <strong><?= htmlspecialchars($penjualAda['nama']) ?></strong>
        (<?= htmlspecialchars($penjualAda['email']) ?>).<br><br>
        Hanya boleh ada 1 akun penjual, jadi form pembuatan akun baru dinonaktifkan.
        Silakan langsung login di <code>auth/login.php</code>, lalu HAPUS file ini dari server.
      </div>
    <?php elseif (!$sukses): ?>
    <form method="post">
      <label for="nama">Nama Pemilik Toko</label>
      <input type="text" id="nama" name="nama" required>
      <label for="email">Email Penjual</label>
      <input type="email" id="email" name="email" required>
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required minlength="6">
      <button type="submit">Buat Akun Penjual</button>
    </form>
    <?php endif; ?>
  </div>
</body>
</html>

<?php
require "../config.php";
require "includes/auth_check.php";

$error = "";
$sukses = "";
$folderUpload = "../assets/hero/";
$posisiList = ['kiri_atas','kanan_atas','kiri_bawah','kanan_bawah','utama'];
$labelPosisi = [
  'kiri_atas' => 'Kiri Atas', 'kanan_atas' => 'Kanan Atas',
  'kiri_bawah' => 'Kiri Bawah', 'kanan_bawah' => 'Kanan Bawah', 'utama' => 'Foto Utama'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $posisi = $_POST['posisi'] ?? '';
  $gambarLama = trim($_POST['gambar_lama'] ?? '');

  if (!in_array($posisi, $posisiList)) {
    $error = "Posisi tidak valid.";
  } elseif (empty($_FILES['gambar']['name'])) {
    $error = "Pilih gambar untuk diunggah.";
  } else {
    $ekstensiOk = ['jpg','jpeg','png','webp'];
    $ekstensi = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    if (!in_array($ekstensi, $ekstensiOk)) {
      $error = "Format gambar harus jpg, jpeg, png, atau webp.";
    } elseif ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
      $error = "Ukuran gambar maksimal 2MB.";
    } else {
      $namaGambar = "hero-" . $posisi . "-" . time() . "." . $ekstensi;
      if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $folderUpload . $namaGambar)) {
        $error = "Gagal mengunggah gambar.";
      } else {
        if ($gambarLama && file_exists($folderUpload . $gambarLama)) {
          @unlink($folderUpload . $gambarLama);
        }
        $stmt = $koneksi->prepare("REPLACE INTO hero_foto (posisi, gambar) VALUES (?, ?)");
        $stmt->bind_param("ss", $posisi, $namaGambar);
        if ($stmt->execute()) {
          header("Location: hero.php?sukses=1");
          exit;
        } else {
          $error = "Gagal menyimpan ke database: " . $koneksi->error;
        }
      }
    }
  }
}

$heroFoto = [];
$hasil = $koneksi->query("SELECT * FROM hero_foto");
while ($row = $hasil->fetch_assoc()) $heroFoto[$row['posisi']] = $row;

if (!empty($_GET['sukses'])) $sukses = "Foto hero berhasil diperbarui.";

$activePage = 'hero';
$pageTitle  = 'Foto Hero';
require "includes/header.php";
?>

<div class="admin-topbar">
  <div class="admin-title">Kelola Foto Hero</div>
</div>

<?php if ($sukses): ?><div class="auth-msg ok"><?= htmlspecialchars($sukses) ?></div><?php endif; ?>
<?php if ($error): ?><div class="auth-msg err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="admin-form-row">
  <?php foreach ($posisiList as $posisi): $foto = $heroFoto[$posisi] ?? null; ?>
  <div class="admin-card" style="flex:1;min-width:220px">
    <h4 style="margin-top:0;color:var(--warm-900)"><?= $labelPosisi[$posisi] ?></h4>
    <?php if ($foto): ?>
      <img src="../assets/hero/<?= htmlspecialchars($foto['gambar']) ?>" class="admin-current-img" style="width:100%;height:140px"><br>
    <?php else: ?>
      <p class="teks-kosong" style="font-size:.78rem">Belum ada foto</p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data" style="margin-top:.6rem">
      <input type="hidden" name="posisi" value="<?= $posisi ?>">
      <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($foto['gambar'] ?? '') ?>">
      <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp" required style="font-size:.78rem;margin-bottom:.6rem">
      <button type="submit" class="admin-btn primary" style="width:100%">Ganti Foto</button>
    </form>
  </div>
  <?php endforeach; ?>
</div>

<?php require "includes/footer.php"; ?>

<?php
require "../config.php";
require "includes/auth_check.php";

$error = "";
$sukses = "";
$folderUpload = "../assets/produk/";

if (!empty($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $cek = $koneksi->prepare("SELECT gambar FROM produk WHERE id = ?");
  $cek->bind_param("i", $id);
  $cek->execute();
  $row = $cek->get_result()->fetch_assoc();

  $stmt = $koneksi->prepare("DELETE FROM produk WHERE id = ?");
  $stmt->bind_param("i", $id);
  if ($stmt->execute()) {
    if ($row && $row['gambar'] && file_exists($folderUpload . $row['gambar'])) {
      @unlink($folderUpload . $row['gambar']);
    }
    header("Location: produk.php?sukses=hapus");
    exit;
  } else {
    $error = "Gagal menghapus produk.";
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id        = (int)($_POST['id'] ?? 0);
  $nama      = trim($_POST['nama'] ?? '');
  $deskripsi = trim($_POST['deskripsi'] ?? '');
  $harga     = (float)($_POST['harga'] ?? 0);
  $gambarLama = trim($_POST['gambar_lama'] ?? '');
  $namaGambar = $gambarLama;

  if ($nama === '' || $deskripsi === '' || $harga <= 0) {
    $error = "Nama, deskripsi, dan harga wajib diisi dengan benar.";
  } else {
    // Upload gambar baru jika ada
    if (!empty($_FILES['gambar']['name'])) {
      $ekstensiOk = ['jpg','jpeg','png','webp'];
      $ekstensi = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
      if (!in_array($ekstensi, $ekstensiOk)) {
        $error = "Format gambar harus jpg, jpeg, png, atau webp.";
      } elseif ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
        $error = "Ukuran gambar maksimal 2MB.";
      } else {
        $namaGambar = "produk-" . time() . "-" . rand(100,999) . "." . $ekstensi;
        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $folderUpload . $namaGambar)) {
          $error = "Gagal mengunggah gambar.";
        } elseif ($gambarLama && file_exists($folderUpload . $gambarLama)) {
          @unlink($folderUpload . $gambarLama);
        }
      }
    } elseif ($id === 0 && $gambarLama === '') {
      $error = "Gambar produk wajib diunggah.";
    }

    if (!$error) {
      if ($id > 0) {
        $stmt = $koneksi->prepare("UPDATE produk SET nama=?, deskripsi=?, harga=?, gambar=? WHERE id=?");
        $stmt->bind_param("ssdsi", $nama, $deskripsi, $harga, $namaGambar, $id);
      } else {
        $stmt = $koneksi->prepare("INSERT INTO produk (nama, deskripsi, harga, gambar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $nama, $deskripsi, $harga, $namaGambar);
      }
      if ($stmt->execute()) {
        header("Location: produk.php?sukses=" . ($id > 0 ? "edit" : "tambah"));
        exit;
      } else {
        $error = "Gagal menyimpan produk: " . $koneksi->error;
      }
    }
  }
}

$editData = null;
if (!empty($_GET['edit'])) {
  $idEdit = (int)$_GET['edit'];
  $stmt = $koneksi->prepare("SELECT * FROM produk WHERE id = ?");
  $stmt->bind_param("i", $idEdit);
  $stmt->execute();
  $editData = $stmt->get_result()->fetch_assoc();
}

$daftarProduk = $koneksi->query("SELECT * FROM produk ORDER BY id DESC");

if (!empty($_GET['sukses'])) {
  $pesan = ['tambah' => 'Produk berhasil ditambahkan.', 'edit' => 'Produk berhasil diperbarui.', 'hapus' => 'Produk berhasil dihapus.'];
  $sukses = $pesan[$_GET['sukses']] ?? '';
}

$activePage = 'produk';
$pageTitle  = 'Produk';
require "includes/header.php";
?>

<div class="admin-topbar">
  <div class="admin-title">Kelola Produk</div>
</div>

<?php if ($sukses): ?><div class="auth-msg ok"><?= htmlspecialchars($sukses) ?></div><?php endif; ?>
<?php if ($error): ?><div class="auth-msg err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="admin-card">
  <h3 style="margin-top:0;color:var(--warm-900);font-family:'Cormorant Garamond',serif"><?= $editData ? 'Edit Produk' : 'Tambah Produk Baru' ?></h3>
  <form class="admin-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)($editData['id'] ?? 0) ?>">
    <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($editData['gambar'] ?? '') ?>">

    <div class="admin-form-row">
      <div class="form-group">
        <label>Nama Produk</label>
        <input type="text" name="nama" required value="<?= htmlspecialchars($editData['nama'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Harga (Rp)</label>
        <input type="number" name="harga" min="1" step="1" required value="<?= htmlspecialchars($editData['harga'] ?? '') ?>">
      </div>
    </div>

    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="deskripsi" required><?= htmlspecialchars($editData['deskripsi'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label>Gambar Produk <?= $editData ? '(kosongkan jika tidak ingin mengganti)' : '' ?></label>
      <?php if (!empty($editData['gambar'])): ?>
        <img src="../assets/produk/<?= htmlspecialchars($editData['gambar']) ?>" class="admin-current-img"><br>
      <?php endif; ?>
      <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp">
    </div>

    <button type="submit" class="admin-btn primary"><?= $editData ? 'Simpan Perubahan' : 'Tambah Produk' ?></button>
    <?php if ($editData): ?><a href="produk.php" class="admin-btn edit">Batal</a><?php endif; ?>
  </form>
</div>

<div class="admin-card">
  <table class="admin-table">
    <thead>
      <tr><th>Gambar</th><th>Nama</th><th>Harga</th><th>Deskripsi</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php if ($daftarProduk->num_rows === 0): ?>
        <tr><td colspan="5" class="teks-kosong">Belum ada produk.</td></tr>
      <?php else: while ($p = $daftarProduk->fetch_assoc()): ?>
      <tr>
        <td><img src="../assets/produk/<?= htmlspecialchars($p['gambar']) ?>" alt=""></td>
        <td><?= htmlspecialchars($p['nama']) ?></td>
        <td>Rp <?= number_format($p['harga'],0,',','.') ?></td>
        <td style="max-width:260px;white-space:normal"><?= htmlspecialchars($p['deskripsi']) ?></td>
        <td>
          <a href="produk.php?edit=<?= $p['id'] ?>" class="admin-btn edit">Edit</a>
          <a href="produk.php?delete=<?= $p['id'] ?>" class="admin-btn delete" onclick="return confirm('Hapus produk ini?')">Hapus</a>
        </td>
      </tr>
      <?php endwhile; endif; ?>
    </tbody>
  </table>
</div>

<?php require "includes/footer.php"; ?>

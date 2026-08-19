<?php
require "../config.php";
require "includes/auth_check.php";

$error = "";
$sukses = "";

// ===== HAPUS DISKON =====
if (!empty($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $stmt = $koneksi->prepare("DELETE FROM diskon WHERE id = ?");
  $stmt->bind_param("i", $id);
  if ($stmt->execute()) {
    header("Location: diskon.php?sukses=hapus");
    exit;
  }
  $error = "Gagal menghapus diskon.";
}

// ===== SIMPAN (TAMBAH / EDIT) DISKON =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id             = (int)($_POST['id'] ?? 0);
  $judul          = trim($_POST['judul'] ?? '');
  $deskripsi      = trim($_POST['deskripsi'] ?? '');
  $persen         = (int)($_POST['persen'] ?? 0);
  $kode_promo     = strtoupper(trim($_POST['kode_promo'] ?? ''));
  $berlaku_sampai = trim($_POST['berlaku_sampai'] ?? '') ?: null;
  $aktif          = isset($_POST['aktif']) ? 1 : 0;

  if ($judul === '' || $kode_promo === '' || $persen <= 0 || $persen > 100) {
    $error = "Judul, kode promo, dan persen (1-100) wajib diisi dengan benar.";
  } else {
    $cek = $koneksi->prepare("SELECT id FROM diskon WHERE kode_promo = ? AND id != ?");
    $cek->bind_param("si", $kode_promo, $id);
    $cek->execute();
    if ($cek->get_result()->num_rows > 0) {
      $error = "Kode promo sudah dipakai diskon lain.";
    } else {
      if ($id > 0) {
        $stmt = $koneksi->prepare("UPDATE diskon SET judul=?, deskripsi=?, persen=?, kode_promo=?, berlaku_sampai=?, aktif=? WHERE id=?");
        $stmt->bind_param("ssissii", $judul, $deskripsi, $persen, $kode_promo, $berlaku_sampai, $aktif, $id);
      } else {
        $stmt = $koneksi->prepare("INSERT INTO diskon (judul, deskripsi, persen, kode_promo, berlaku_sampai, aktif) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissi", $judul, $deskripsi, $persen, $kode_promo, $berlaku_sampai, $aktif);
      }
      if ($stmt->execute()) {
        header("Location: diskon.php?sukses=" . ($id > 0 ? "edit" : "tambah"));
        exit;
      } else {
        $error = "Gagal menyimpan diskon: " . $koneksi->error;
      }
    }
  }
}

$editData = null;
if (!empty($_GET['edit'])) {
  $idEdit = (int)$_GET['edit'];
  $stmt = $koneksi->prepare("SELECT * FROM diskon WHERE id = ?");
  $stmt->bind_param("i", $idEdit);
  $stmt->execute();
  $editData = $stmt->get_result()->fetch_assoc();
}

$daftarDiskon = $koneksi->query("SELECT * FROM diskon ORDER BY id DESC");

if (!empty($_GET['sukses'])) {
  $pesan = ['tambah' => 'Diskon berhasil ditambahkan.', 'edit' => 'Diskon berhasil diperbarui.', 'hapus' => 'Diskon berhasil dihapus.'];
  $sukses = $pesan[$_GET['sukses']] ?? '';
}

$activePage = 'diskon';
$pageTitle  = 'Diskon';
require "includes/header.php";
?>

<div class="admin-topbar">
  <div class="admin-title">Kelola Diskon</div>
</div>

<?php if ($sukses): ?><div class="auth-msg ok"><?= htmlspecialchars($sukses) ?></div><?php endif; ?>
<?php if ($error): ?><div class="auth-msg err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="admin-card">
  <h3 style="margin-top:0;color:var(--warm-900);font-family:'Cormorant Garamond',serif"><?= $editData ? 'Edit Diskon' : 'Tambah Diskon Baru' ?></h3>
  <form class="admin-form" method="post">
    <input type="hidden" name="id" value="<?= (int)($editData['id'] ?? 0) ?>">

    <div class="admin-form-row">
      <div class="form-group">
        <label>Judul</label>
        <input type="text" name="judul" required value="<?= htmlspecialchars($editData['judul'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Kode Promo</label>
        <input type="text" name="kode_promo" required value="<?= htmlspecialchars($editData['kode_promo'] ?? '') ?>">
      </div>
    </div>

    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="deskripsi"><?= htmlspecialchars($editData['deskripsi'] ?? '') ?></textarea>
    </div>

    <div class="admin-form-row">
      <div class="form-group">
        <label>Persen Diskon (%)</label>
        <input type="number" name="persen" min="1" max="100" required value="<?= htmlspecialchars($editData['persen'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Berlaku Sampai (opsional)</label>
        <input type="date" name="berlaku_sampai" value="<?= htmlspecialchars($editData['berlaku_sampai'] ?? '') ?>">
      </div>
    </div>

    <div class="form-group">
      <label style="display:flex;align-items:center;gap:.5rem;text-transform:none;font-size:.85rem">
        <input type="checkbox" name="aktif" style="width:auto" <?= (!$editData || $editData['aktif']) ? 'checked' : '' ?>> Aktifkan diskon ini
      </label>
    </div>

    <button type="submit" class="admin-btn primary"><?= $editData ? 'Simpan Perubahan' : 'Tambah Diskon' ?></button>
    <?php if ($editData): ?><a href="diskon.php" class="admin-btn edit">Batal</a><?php endif; ?>
  </form>
</div>

<div class="admin-card">
  <table class="admin-table">
    <thead>
      <tr><th>Judul</th><th>Kode</th><th>Persen</th><th>Berlaku Sampai</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php if ($daftarDiskon->num_rows === 0): ?>
        <tr><td colspan="6" class="teks-kosong">Belum ada diskon.</td></tr>
      <?php else: while ($d = $daftarDiskon->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($d['judul']) ?></td>
        <td><?= htmlspecialchars($d['kode_promo']) ?></td>
        <td><?= (int)$d['persen'] ?>%</td>
        <td><?= $d['berlaku_sampai'] ? date('d M Y', strtotime($d['berlaku_sampai'])) : '-' ?></td>
        <td><span class="admin-badge <?= $d['aktif'] ? 'aktif' : 'nonaktif' ?>"><?= $d['aktif'] ? 'Aktif' : 'Nonaktif' ?></span></td>
        <td>
          <a href="diskon.php?edit=<?= $d['id'] ?>" class="admin-btn edit">Edit</a>
          <a href="diskon.php?delete=<?= $d['id'] ?>" class="admin-btn delete" onclick="return confirm('Hapus diskon ini?')">Hapus</a>
        </td>
      </tr>
      <?php endwhile; endif; ?>
    </tbody>
  </table>
</div>

<?php require "includes/footer.php"; ?>

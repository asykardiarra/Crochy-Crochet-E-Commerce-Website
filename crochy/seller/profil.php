<?php
require "../config.php";
require "includes/auth_check.php";

$error = "";
$sukses = "";
$folderUpload = "../assets/profil/";

$profil = $koneksi->query("SELECT * FROM profil LIMIT 1")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama            = trim($_POST['nama'] ?? '');
  $role            = trim($_POST['role'] ?? '');
  $bio             = trim($_POST['bio'] ?? '');
  $alamat          = trim($_POST['alamat'] ?? '');
  $whatsapp        = trim($_POST['whatsapp'] ?? '');
  $email           = trim($_POST['email'] ?? '');
  $jam_operasional = trim($_POST['jam_operasional'] ?? '');
  $nama_bank       = trim($_POST['nama_bank'] ?? '');
  $no_rekening     = trim($_POST['no_rekening'] ?? '');
  $atas_nama       = trim($_POST['atas_nama'] ?? '');
  $fotoLama        = trim($_POST['foto_lama'] ?? '');
  $namaFoto        = $fotoLama;

  if ($nama === '') {
    $error = "Nama wajib diisi.";
  } else {
    if (!empty($_FILES['foto']['name'])) {
      $ekstensiOk = ['jpg','jpeg','png','webp'];
      $ekstensi = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
      if (!in_array($ekstensi, $ekstensiOk)) {
        $error = "Format foto harus jpg, jpeg, png, atau webp.";
      } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
        $error = "Ukuran foto maksimal 2MB.";
      } else {
        $namaFoto = "profil-" . time() . "." . $ekstensi;
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $folderUpload . $namaFoto)) {
          $error = "Gagal mengunggah foto.";
        } elseif ($fotoLama && file_exists($folderUpload . $fotoLama)) {
          @unlink($folderUpload . $fotoLama);
        }
      }
    }

    if (!$error) {
      if ($profil) {
        $stmt = $koneksi->prepare("UPDATE profil SET nama=?, role=?, foto=?, bio=?, alamat=?, whatsapp=?, email=?, jam_operasional=?, nama_bank=?, no_rekening=?, atas_nama=? WHERE id=?");
        $stmt->bind_param("sssssssssssi", $nama, $role, $namaFoto, $bio, $alamat, $whatsapp, $email, $jam_operasional, $nama_bank, $no_rekening, $atas_nama, $profil['id']);
      } else {
        $stmt = $koneksi->prepare("INSERT INTO profil (nama, role, foto, bio, alamat, whatsapp, email, jam_operasional, nama_bank, no_rekening, atas_nama) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssssss", $nama, $role, $namaFoto, $bio, $alamat, $whatsapp, $email, $jam_operasional, $nama_bank, $no_rekening, $atas_nama);
      }
      if ($stmt->execute()) {
        header("Location: profil.php?sukses=1");
        exit;
      } else {
        $error = "Gagal menyimpan profil: " . $koneksi->error;
      }
    }
  }
  // Supaya form tetap terisi ulang kalau ada error, timpa $profil dengan input terbaru
  $profil = array_merge($profil ?? [], compact('nama','role','bio','alamat','whatsapp','email','jam_operasional','nama_bank','no_rekening','atas_nama'), ['foto' => $namaFoto]);
}

if (!empty($_GET['sukses'])) $sukses = "Profil toko berhasil diperbarui.";

$activePage = 'profil';
$pageTitle  = 'Profil Toko';
require "includes/header.php";
?>

<div class="admin-topbar">
  <div class="admin-title">Profil Toko</div>
</div>

<?php if ($sukses): ?><div class="auth-msg ok"><?= htmlspecialchars($sukses) ?></div><?php endif; ?>
<?php if ($error): ?><div class="auth-msg err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="admin-card">
  <form class="admin-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($profil['foto'] ?? '') ?>">

    <div class="form-group">
      <label>Foto Profil</label>
      <?php if (!empty($profil['foto'])): ?>
        <img src="../assets/profil/<?= htmlspecialchars($profil['foto']) ?>" class="admin-current-img"><br>
      <?php endif; ?>
      <input type="file" name="foto" accept=".jpg,.jpeg,.png,.webp">
    </div>

    <div class="admin-form-row">
      <div class="form-group">
        <label>Nama</label>
        <input type="text" name="nama" required value="<?= htmlspecialchars($profil['nama'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Role / Jabatan</label>
        <input type="text" name="role" value="<?= htmlspecialchars($profil['role'] ?? '') ?>">
      </div>
    </div>

    <div class="form-group">
      <label>Bio</label>
      <textarea name="bio"><?= htmlspecialchars($profil['bio'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label>Alamat</label>
      <input type="text" name="alamat" value="<?= htmlspecialchars($profil['alamat'] ?? '') ?>">
    </div>

    <div class="admin-form-row">
      <div class="form-group">
        <label>WhatsApp (kontak toko)</label>
        <input type="text" name="whatsapp" placeholder="Contoh: 6285122185211" value="<?= htmlspecialchars($profil['whatsapp'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($profil['email'] ?? '') ?>">
      </div>
    </div>

    <div class="form-group">
      <label>Jam Operasional</label>
      <input type="text" name="jam_operasional" value="<?= htmlspecialchars($profil['jam_operasional'] ?? '') ?>">
    </div>

    <h3 style="color:var(--warm-900);font-family:'Cormorant Garamond',serif;margin-bottom:.6rem">💳 Info Rekening / E-Wallet Pembayaran</h3>
    <p style="color:var(--warm-800);font-size:.82rem;margin-top:0;margin-bottom:1rem">Ditampilkan ke pembeli di halaman konfirmasi pembayaran (checkout langkah 3).</p>
    <div class="admin-form-row">
      <div class="form-group">
        <label>Nama Bank / E-Wallet</label>
        <input type="text" name="nama_bank" placeholder="Contoh: BCA / Dana / OVO" value="<?= htmlspecialchars($profil['nama_bank'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>No. Rekening / No. HP</label>
        <input type="text" name="no_rekening" placeholder="Contoh: 1234567890" value="<?= htmlspecialchars($profil['no_rekening'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Atas Nama</label>
        <input type="text" name="atas_nama" placeholder="Contoh: Asykar Diarra" value="<?= htmlspecialchars($profil['atas_nama'] ?? '') ?>">
      </div>
    </div>

    <button type="submit" class="admin-btn primary">Simpan Profil</button>
  </form>
</div>

<?php require "includes/footer.php"; ?>

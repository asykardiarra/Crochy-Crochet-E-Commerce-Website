<?php
require "../config.php";
require "includes/auth_check.php";

$error = "";
$sukses = "";

$statusValid = ['menunggu_verifikasi','diproses','dikirim','selesai','ditolak'];
$labelStatus = [
  'menunggu_verifikasi' => 'Menunggu Verifikasi',
  'diproses'            => 'Diproses',
  'dikirim'             => 'Dikirim',
  'selesai'             => 'Selesai',
  'ditolak'             => 'Ditolak/Dibatalkan',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $idPesanan   = (int)($_POST['pesanan_id'] ?? 0);
  $statusBaru  = $_POST['status_baru'] ?? '';

  if ($idPesanan <= 0 || !in_array($statusBaru, $statusValid, true)) {
    $error = "Data tidak valid.";
  } else {
    $stmt = $koneksi->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $statusBaru, $idPesanan);
    if ($stmt->execute()) {
      header("Location: pesanan.php?sukses=1" . (!empty($_POST['filter']) ? "&filter=" . urlencode($_POST['filter']) : ""));
      exit;
    } else {
      $error = "Gagal memperbarui status pesanan.";
    }
  }
}

if (!empty($_GET['sukses'])) $sukses = "Status pesanan berhasil diperbarui.";

$filterAktif = $_GET['filter'] ?? '';
if (!in_array($filterAktif, $statusValid, true)) $filterAktif = '';

$sql = "SELECT p.*, u.email AS email_pembeli, u.whatsapp AS wa_pembeli
        FROM pesanan p
        JOIN users u ON u.id = p.user_id";
if ($filterAktif !== '') {
  $sql .= " WHERE p.status = '" . $koneksi->real_escape_string($filterAktif) . "'";
}
$sql .= " ORDER BY p.created_at DESC";
$hasilPesanan = $koneksi->query($sql);

$daftarPesanan = [];
while ($p = $hasilPesanan->fetch_assoc()) {
  $stmtItem = $koneksi->prepare("SELECT * FROM pesanan_item WHERE pesanan_id = ?");
  $stmtItem->bind_param("i", $p['id']);
  $stmtItem->execute();
  $p['items'] = $stmtItem->get_result()->fetch_all(MYSQLI_ASSOC);
  $daftarPesanan[] = $p;
}

$activePage = 'pesanan';
$pageTitle  = 'Pesanan Masuk';
require "includes/header.php";
?>

<div class="admin-topbar">
  <div class="admin-title">Pesanan Masuk</div>
</div>

<?php if ($sukses): ?><div class="auth-msg ok"><?= htmlspecialchars($sukses) ?></div><?php endif; ?>
<?php if ($error): ?><div class="auth-msg err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="admin-card" style="padding:1rem 1.6rem">
  <div class="filter-status-row">
    <a href="pesanan.php" class="admin-btn <?= $filterAktif === '' ? 'primary' : 'edit' ?>">Semua</a>
    <?php foreach ($labelStatus as $key => $label): ?>
      <a href="pesanan.php?filter=<?= $key ?>" class="admin-btn <?= $filterAktif === $key ? 'primary' : 'edit' ?>"><?= htmlspecialchars($label) ?></a>
    <?php endforeach; ?>
  </div>
</div>

<?php if (empty($daftarPesanan)): ?>
  <div class="admin-card"><p class="teks-kosong">Belum ada pesanan<?= $filterAktif ? ' dengan status ini' : '' ?>.</p></div>
<?php else: foreach ($daftarPesanan as $p): ?>
  <div class="admin-card pesanan-card">
    <div class="pesanan-card-head">
      <div>
        <strong>Pesanan #<?= (int)$p['id'] ?></strong>
        <small style="display:block;color:var(--warm-800)"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?> WIB</small>
      </div>
      <span class="status-badge status-<?= htmlspecialchars($p['status']) ?>"><?= htmlspecialchars($labelStatus[$p['status']] ?? $p['status']) ?></span>
    </div>

    <div class="pesanan-card-body">
      <div class="pesanan-alamat" style="margin-bottom:.8rem">
        <span class="label">Pembeli:</span> <?= htmlspecialchars($p['nama_penerima']) ?>
        (<?= htmlspecialchars($p['email_pembeli']) ?><?= $p['wa_pembeli'] ? ', WA: ' . htmlspecialchars($p['wa_pembeli']) : '' ?>)<br>
        <span class="label">Alamat:</span> <?= htmlspecialchars($p['alamat_pengiriman']) ?>
      </div>
      <?php foreach ($p['items'] as $it): ?>
        <div class="cart-item">
          <div class="cart-item-info">
            <h4><?= htmlspecialchars($it['nama_produk']) ?> (<?= (int)$it['jumlah'] ?>x)</h4>
            <small>Rp <?= number_format($it['harga_satuan'] * $it['jumlah'], 0, ',', '.') ?></small>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="pesanan-card-foot">
      <div class="ringkasan-total-wrap">
        <div>Subtotal: <span>Rp <?= number_format($p['subtotal'], 0, ',', '.') ?></span></div>
        <?php if ($p['potongan'] > 0): ?>
          <div>Potongan<?= $p['kode_promo'] ? ' (' . htmlspecialchars($p['kode_promo']) . ')' : '' ?>: <span class="potongan-harga">-Rp <?= number_format($p['potongan'], 0, ',', '.') ?></span></div>
        <?php endif; ?>
        <div class="ringkasan-total-final">Total: <span>Rp <?= number_format($p['total'], 0, ',', '.') ?></span></div>
      </div>
      <a href="../assets/bukti_bayar/<?= htmlspecialchars($p['bukti_bayar']) ?>" target="_blank" class="btn-ghost btn-lihat-bukti">🧾 Lihat Bukti Bayar</a>

      <form method="post" class="pesanan-status-form">
        <input type="hidden" name="pesanan_id" value="<?= (int)$p['id'] ?>">
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filterAktif) ?>">
        <select name="status_baru">
          <?php foreach ($labelStatus as $key => $label): ?>
            <option value="<?= $key ?>" <?= $p['status'] === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="admin-btn primary">Ubah Status</button>
      </form>
    </div>
  </div>
<?php endforeach; endif; ?>

<?php require "includes/footer.php"; ?>

<?php
require "partials/bootstrap.php";
$pageTitle = "Profil";
$halamanAktif = "profil";
require "partials/head.php";
?>

<!-- ═══ PROFIL ═══ -->
<section id="profil">
  <div class="section-header">
    <p class="section-eyebrow">Tentang Kami</p>
    <h2 class="section-title">Profil <em>Crochy</em></h2>
    <div class="section-rule"></div>
  </div>
  <div class="profil-wrap">
    <div class="profil-foto">
      <img src="<?= !empty($profil['foto']) ? 'assets/profil/' . htmlspecialchars($profil['foto']) : 'https://placehold.co/400x400/8B4513/F8E8D8?text=Crochy' ?>" alt="Foto Pembuat Crochy">
      <div class="nama-pembuat"><?= htmlspecialchars($profil['nama'] ?? '') ?></div>
      <div class="role-pembuat"><?= htmlspecialchars($profil['role'] ?? '') ?></div>
    </div>
    <div class="profil-info">
      <h3>👋 Tentang Saya</h3>
      <p class="bio"><?= nl2br(htmlspecialchars($profil['bio'] ?? '')) ?></p>

      <h3 class="profil-subheading">📍 Informasi Toko</h3>
      <div class="info-item">
        <span class="icon">🏠</span>
        <span class="label">Alamat</span>
        <span class="value"><?= htmlspecialchars($profil['alamat'] ?? '') ?></span>
      </div>
      <div class="info-item">
        <span class="icon">📞</span>
        <span class="label">WhatsApp</span>
        <span class="value"><?= htmlspecialchars($profil['whatsapp'] ?? '') ?></span>
      </div>
      <div class="info-item">
        <span class="icon">📧</span>
        <span class="label">Email</span>
        <span class="value"><?= htmlspecialchars($profil['email'] ?? '') ?></span>
      </div>
      <div class="info-item">
        <span class="icon">🕐</span>
        <span class="label">Jam Operasional</span>
        <span class="value"><?= htmlspecialchars($profil['jam_operasional'] ?? '') ?></span>
      </div>
    </div>
  </div>
</section>

<?php require "partials/footer.php"; ?>

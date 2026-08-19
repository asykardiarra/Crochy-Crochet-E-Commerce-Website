// =============================================
// CROCHY - MAIN SCRIPT (versi PHP + MySQL, dengan fitur kode promo)
// Kartu produk sudah dirender langsung oleh PHP (lihat index.php),
// jadi di sini kita tidak lagi fetch JSON. Variabel `produkCrochy` dan
// `daftarDiskonAktif` sudah tersedia karena disuntik oleh PHP lewat
// <script> di index.php, tepat sebelum file ini dimuat.
// =============================================

// ===== NAV TOGGLE =====
function toggleNav() {
  document.getElementById('navLinks').classList.toggle('open');
}
document.querySelectorAll('.nav-links a').forEach(a =>
  a.addEventListener('click', () => document.getElementById('navLinks').classList.remove('open'))
);

// ===== DATA PRODUK & DISKON =====
// `produkCrochy` dan `daftarDiskonAktif` TIDAK dideklarasikan di sini —
// keduanya sudah dibuat oleh <script> inline di index.php (isinya hasil
// query database). Kalau kamu buka main.js ini sendirian tanpa lewat
// index.php, keduanya akan undefined; itu wajar, karena file ini
// didesain nempel ke index.php.

// ===== KERANJANG dengan LocalStorage =====
let keranjangBelanja = [];
let promoAktif = null; // { kode, persen } atau null kalau belum ada promo yang valid

function loadKeranjang() {
  const saved = localStorage.getItem('crochy_cart');
  if (saved) {
    try {
      keranjangBelanja = JSON.parse(saved);
    } catch (e) {
      keranjangBelanja = [];
    }
  }
  // ===== PATCH: promo ikut disimpan di localStorage supaya tetap terpakai =====
  // saat pindah halaman (Keranjang -> Data Pengiriman -> Pembayaran).
  const savedPromo = localStorage.getItem('crochy_promo');
  if (savedPromo) {
    try {
      promoAktif = JSON.parse(savedPromo);
    } catch (e) {
      promoAktif = null;
    }
  }
  tampilkanKeranjang();
  tampilkanChipPromo();
}

function saveKeranjang() {
  localStorage.setItem('crochy_cart', JSON.stringify(keranjangBelanja));
}

function savePromo() {
  if (promoAktif) {
    localStorage.setItem('crochy_promo', JSON.stringify(promoAktif));
  } else {
    localStorage.removeItem('crochy_promo');
  }
}

function tambahKeKeranjang(idProduk, tombolEl) {
  const produkPilihan = produkCrochy.find(p => Number(p.id) === Number(idProduk));
  if (!produkPilihan) {
    console.error('Produk dengan id', idProduk, 'tidak ditemukan di produkCrochy.');
    return;
  }
  const cekProduk = keranjangBelanja.find(item => item.id === idProduk);
  if (cekProduk) {
    cekProduk.jumlah += 1;
  } else {
    keranjangBelanja.push({ ...produkPilihan, jumlah: 1 });
  }
  saveKeranjang();
  tampilkanKeranjang();

  const badge = document.getElementById('badgeCount');
  badge.classList.add('pop');
  setTimeout(() => badge.classList.remove('pop'), 300);

  // ===== PATCH: Efek "ditambahkan ke keranjang" =====
  // 1) Tombol berubah sesaat jadi "✓ Ditambahkan"
  if (tombolEl) {
    const teksAsli = tombolEl.textContent;
    tombolEl.textContent = '✓ Ditambahkan';
    tombolEl.classList.add('added');
    tombolEl.disabled = true;
    setTimeout(() => {
      tombolEl.textContent = teksAsli;
      tombolEl.classList.remove('added');
      tombolEl.disabled = false;
    }, 1100);
  }
  // 2) Toast notifikasi di pojok bawah
  tampilkanToast(`🧶 ${produkPilihan.nama} ditambahkan ke keranjang`);
}

// Menampilkan toast singkat, otomatis hilang sendiri (animasi diatur lewat CSS)
function tampilkanToast(pesan) {
  const container = document.getElementById('toast-container');
  if (!container) return;
  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.innerHTML = `<span class="toast-icon">✅</span><span>${pesan}</span>`;
  container.appendChild(toast);
  setTimeout(() => toast.remove(), 2400);
}

// ===== PATCH: KODE PROMO =====
function terapkanKodePromo() {
  const inputEl = document.getElementById('kode-promo-input');
  const status = document.getElementById('status-promo');
  const inputKode = inputEl.value.trim().toUpperCase();

  if (!inputKode) {
    status.textContent = '⚠️ Masukkan kode promo dulu ya.';
    status.classList.remove('promo-status-error');
    status.classList.add('promo-status-warn');
    return;
  }

  const daftar = (typeof daftarDiskonAktif !== 'undefined') ? daftarDiskonAktif : [];
  const cocok = daftar.find(d => d.kode.toUpperCase() === inputKode);

  if (cocok) {
    promoAktif = cocok;
    savePromo();
    status.textContent = '';
    status.classList.remove('promo-status-warn', 'promo-status-error');
    tampilkanToast(`🏷️ Kode "${cocok.kode}" berhasil diterapkan (-${cocok.persen}%)`);
    tampilkanChipPromo();
  } else {
    promoAktif = null;
    status.textContent = '❌ Kode promo tidak valid atau sudah tidak berlaku';
    status.classList.remove('promo-status-warn');
    status.classList.add('promo-status-error');
  }
  tampilkanKeranjang();
}

// Ganti kolom input jadi "chip" begitu kode promo berhasil diterapkan
function tampilkanChipPromo() {
  const inputRow = document.getElementById('promo-input-row');
  const chipWrap = document.getElementById('promo-chip-wrap');
  const inputEl = document.getElementById('kode-promo-input');

  if (!promoAktif) {
    chipWrap.style.display = 'none';
    chipWrap.innerHTML = '';
    inputRow.style.display = 'flex';
    return;
  }

  inputRow.style.display = 'none';
  chipWrap.style.display = 'block';
  chipWrap.innerHTML = `
    <div class="promo-chip">
      <div class="promo-chip-text">🏷️ <span class="kode">${promoAktif.kode}</span> diterapkan (-${promoAktif.persen}%)</div>
      <button type="button" class="promo-chip-remove" onclick="hapusKodePromo()" aria-label="Hapus kode promo">✕</button>
    </div>
  `;
  inputEl.value = '';
}

function hapusKodePromo() {
  promoAktif = null;
  savePromo();
  const status = document.getElementById('status-promo');
  if (status) status.textContent = '';
  tampilkanChipPromo();
  tampilkanKeranjang();
}

// Hitung subtotal, potongan, dan total berdasarkan isi keranjang + promoAktif
function hitungTotal() {
  const subtotal = keranjangBelanja.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
  const potongan = promoAktif ? Math.round(subtotal * promoAktif.persen / 100) : 0;
  const total = subtotal - potongan;
  return { subtotal, potongan, total };
}
// ===== END PATCH =====

function tampilkanKeranjang() {
  // Badge jumlah item di navbar ada di SEMUA halaman, selalu diperbarui
  const totalItems = keranjangBelanja.reduce((sum, item) => sum + item.jumlah, 0);
  const badgeCount = document.getElementById("badgeCount");
  if (badgeCount) badgeCount.textContent = totalItems;

  // Elemen di bawah ini HANYA ada di halaman keranjang.php.
  // Kalau halaman ini bukan keranjang.php, cukup update badge di atas lalu berhenti.
  const wadahKeranjang = document.getElementById("isi-keranjang");
  if (!wadahKeranjang) return;

  const teksTotalHarga = document.getElementById("total-harga");

  wadahKeranjang.innerHTML = "";

  if (keranjangBelanja.length === 0) {
    wadahKeranjang.innerHTML = "<p class='teks-kosong'>Keranjang masih kosong 🧶</p>";
    teksTotalHarga.innerText = "Rp 0";
    // ===== PATCH: sembunyikan rincian diskon kalau keranjang kosong =====
    const rincian = document.getElementById('rincian-diskon');
    if (rincian) rincian.style.display = 'none';
    return;
  }

  keranjangBelanja.forEach((item, index) => {
    wadahKeranjang.innerHTML += `
      <div class="cart-item">
        <div class="cart-item-info">
          <h4>${item.nama} (${item.jumlah}x)</h4>
          <small>Rp ${(item.harga * item.jumlah).toLocaleString("id-ID")}</small>
        </div>
        <button class="btn-hapus" onclick="hapusDariKeranjang(${index})">Hapus</button>
      </div>
    `;
  });

  // ===== PATCH: tampilkan subtotal, potongan, dan total (bukan cuma total polos) =====
  const { subtotal, potongan, total } = hitungTotal();
  const totalBaru = "Rp " + total.toLocaleString("id-ID");
  if (teksTotalHarga.innerText !== totalBaru) {
    teksTotalHarga.innerText = totalBaru;
    teksTotalHarga.classList.remove('updated');
    // trigger reflow supaya animasi CSS bisa diulang tiap kali total berubah
    void teksTotalHarga.offsetWidth;
    teksTotalHarga.classList.add('updated');
  }

  const rincian = document.getElementById('rincian-diskon');
  const subtotalEl = document.getElementById('subtotal-harga');
  const potonganEl = document.getElementById('potongan-harga');
  if (rincian && subtotalEl && potonganEl) {
    if (promoAktif && potongan > 0) {
      rincian.style.display = 'block';
      subtotalEl.textContent = "Rp " + subtotal.toLocaleString("id-ID");
      potonganEl.textContent = "-Rp " + potongan.toLocaleString("id-ID");
    } else {
      rincian.style.display = 'none';
    }
  }
}

function hapusDariKeranjang(indexBarang) {
  keranjangBelanja.splice(indexBarang, 1);
  saveKeranjang();
  tampilkanKeranjang();
}

function hapusSemuaKeranjang() {
  keranjangBelanja = [];
  promoAktif = null; // ===== PATCH: reset promo juga saat keranjang dikosongkan =====
  saveKeranjang();
  savePromo();
  tampilkanKeranjang();
  tampilkanChipPromo(); // kembalikan kolom input promo ke kondisi kosong
  const status = document.getElementById('status-promo');
  if (status) status.textContent = '';
}

// ===== ALUR CHECKOUT (Keranjang -> Data Pengiriman -> Pembayaran) =====
// Langkah 1 (keranjang.php): validasi keranjang & status login, lalu lanjut ke Data Pengiriman.
function lanjutKeCheckout() {
  if (keranjangBelanja.length === 0) {
    alert("Keranjang masih kosong! Silakan tambahkan produk terlebih dahulu.");
    return;
  }
  if (typeof userLoginSaatIni === 'undefined' || !userLoginSaatIni) {
    alert("Silakan login terlebih dahulu untuk melanjutkan pemesanan.");
    window.location.href = "auth/login.php?redirect=checkout-alamat.php";
    return;
  }
  if (userLoginSaatIni.role === 'penjual') {
    alert("Akun Penjual tidak dapat melakukan pemesanan. Silakan login sebagai Pembeli.");
    return;
  }
  window.location.href = "checkout-alamat.php";
}

// Render ringkasan pesanan (read-only) di halaman Data Pengiriman & Pembayaran
function renderRingkasanPesanan(elId) {
  const wadah = document.getElementById(elId);
  if (!wadah) return;
  if (keranjangBelanja.length === 0) {
    wadah.innerHTML = "<p class='teks-kosong'>Keranjang masih kosong 🧶</p>";
    return;
  }
  const { subtotal, potongan, total } = hitungTotal();
  let html = "";
  keranjangBelanja.forEach(item => {
    html += `
      <div class="cart-item">
        <div class="cart-item-info">
          <h4>${item.nama} (${item.jumlah}x)</h4>
          <small>Rp ${(item.harga * item.jumlah).toLocaleString("id-ID")}</small>
        </div>
      </div>`;
  });
  html += `<div class="ringkasan-total-wrap">`;
  html += `<div>Subtotal: <span>Rp ${subtotal.toLocaleString("id-ID")}</span></div>`;
  if (promoAktif && potongan > 0) {
    html += `<div>Potongan (${promoAktif.kode} -${promoAktif.persen}%): <span class="potongan-harga">-Rp ${potongan.toLocaleString("id-ID")}</span></div>`;
  }
  html += `<div class="ringkasan-total-final">Total: <span>Rp ${total.toLocaleString("id-ID")}</span></div>`;
  html += `</div>`;
  wadah.innerHTML = html;
}

// Langkah 2 (checkout-alamat.php): simpan nama & alamat, lanjut ke Pembayaran.
function simpanAlamatLanjutBayar() {
  if (keranjangBelanja.length === 0) {
    alert("Keranjang masih kosong!");
    window.location.href = "keranjang.php";
    return;
  }

  const namaEl = document.getElementById("nama-penerima");
  const alamatEl = document.getElementById("alamat-pengiriman");
  const nama = namaEl.value.trim();
  const alamat = alamatEl.value.trim();

  if (!nama) {
    alert("Mohon isi Nama Lengkap Anda.");
    namaEl.focus();
    return;
  }
  if (nama.length > 50) {
    alert("Nama penerima maksimal 50 karakter.");
    namaEl.focus();
    return;
  }
  if (!alamat) {
    alert("Mohon isi Alamat Pengiriman Anda.");
    alamatEl.focus();
    return;
  }
  if (alamat.length > 100) {
    alert("Alamat pengiriman maksimal 100 karakter.");
    alamatEl.focus();
    return;
  }

  localStorage.setItem("crochy_checkout_alamat", JSON.stringify({ nama, alamat }));
  window.location.href = "checkout-bayar.php";
}

// Langkah 3 (checkout-bayar.php): siapkan data tersembunyi sebelum form di-submit ke server.
function siapkanFormBayar(formEl) {
  if (keranjangBelanja.length === 0) {
    alert("Keranjang masih kosong!");
    window.location.href = "keranjang.php";
    return false;
  }

  const savedAlamat = localStorage.getItem("crochy_checkout_alamat");
  if (!savedAlamat) {
    alert("Mohon lengkapi data pengiriman terlebih dahulu.");
    window.location.href = "checkout-alamat.php";
    return false;
  }

  let dataAlamat;
  try {
    dataAlamat = JSON.parse(savedAlamat);
  } catch (e) {
    localStorage.removeItem("crochy_checkout_alamat");
    alert("Data pengiriman tidak valid. Silakan isi ulang.");
    window.location.href = "checkout-alamat.php";
    return false;
  }

  const nama = String(dataAlamat.nama || "").trim();
  const alamat = String(dataAlamat.alamat || "").trim();

  if (!nama || !alamat) {
    alert("Nama dan alamat pengiriman wajib diisi.");
    window.location.href = "checkout-alamat.php";
    return false;
  }
  if (nama.length > 50) {
    alert("Nama penerima maksimal 50 karakter.");
    window.location.href = "checkout-alamat.php";
    return false;
  }
  if (alamat.length > 100) {
    alert("Alamat pengiriman maksimal 100 karakter.");
    window.location.href = "checkout-alamat.php";
    return false;
  }

  const fileInput = document.getElementById("bukti-bayar-input");
  if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
    alert("Mohon unggah bukti transfer/pembayaran terlebih dahulu.");
    return false;
  }

  formEl.querySelector('[name="nama_penerima"]').value = nama;
  formEl.querySelector('[name="alamat_pengiriman"]').value = alamat;
  formEl.querySelector('[name="cart_data"]').value = JSON.stringify(keranjangBelanja);
  formEl.querySelector('[name="kode_promo"]').value = promoAktif ? promoAktif.kode : "";
  return true;
}

// Dipanggil di pesanan-saya.php setelah pesanan berhasil dibuat, supaya keranjang & data checkout dikosongkan.
function bersihkanDataCheckout() {
  localStorage.removeItem("crochy_cart");
  localStorage.removeItem("crochy_promo");
  localStorage.removeItem("crochy_checkout_alamat");
}

// ===== SALIN KODE PROMO (dari section Diskon) =====
function salinKodePromo(el) {
  const kode = el.dataset.kode;
  navigator.clipboard.writeText(kode).then(() => {
    el.classList.add('disalin');
    setTimeout(() => el.classList.remove('disalin'), 1500);
    tampilkanToast(`📋 Kode "${kode}" disalin. Tempel di kolom Kode Promo pada Keranjang!`);
  }).catch(() => {
    alert('Kode promo: ' + kode);
  });
}

// ===== BACK TO TOP =====
window.addEventListener('scroll', () => {
  document.getElementById('back-top').classList.toggle('show', window.scrollY > 400);
});

// ===== SCROLL REVEAL =====
const observer = new IntersectionObserver(entries => entries.forEach(e => {
  if (e.isIntersecting) { e.target.classList.add('visible'); }
}), { threshold: .1 });

document.querySelectorAll('.step').forEach(el => {
  el.classList.add('hidden');
  observer.observe(el);
});

// ===== SCROLL REVEAL UNTUK KARTU PRODUK (dirender PHP) =====
document.querySelectorAll('.product-card').forEach(el => {
  el.classList.add('hidden');
  observer.observe(el);
});

// ===== INISIALISASI =====
loadKeranjang();

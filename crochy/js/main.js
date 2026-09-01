function toggleNav() {
  document.getElementById('navLinks').classList.toggle('open');
}
document.querySelectorAll('.nav-links a').forEach(a =>
  a.addEventListener('click', () => document.getElementById('navLinks').classList.remove('open'))
);

let keranjangBelanja = [];
let promoAktif = null;

function loadKeranjang() {
  const saved = localStorage.getItem('crochy_cart');
  if (saved) {
    try {
      keranjangBelanja = JSON.parse(saved);
    } catch (e) {
      keranjangBelanja = [];
    }
  }
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
    
  tampilkanToast(`🧶 ${produkPilihan.nama} ditambahkan ke keranjang`);
}

function tampilkanToast(pesan) {
  const container = document.getElementById('toast-container');
  if (!container) return;
  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.innerHTML = `<span class="toast-icon">✅</span><span>${pesan}</span>`;
  container.appendChild(toast);
  setTimeout(() => toast.remove(), 2400);
}

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

function hitungTotal() {
  const subtotal = keranjangBelanja.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
  const potongan = promoAktif ? Math.round(subtotal * promoAktif.persen / 100) : 0;
  const total = subtotal - potongan;
  return { subtotal, potongan, total };
}

function tampilkanKeranjang() {
  const totalItems = keranjangBelanja.reduce((sum, item) => sum + item.jumlah, 0);
  const badgeCount = document.getElementById("badgeCount");
  if (badgeCount) badgeCount.textContent = totalItems;

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

  const { subtotal, potongan, total } = hitungTotal();
  const totalBaru = "Rp " + total.toLocaleString("id-ID");
  if (teksTotalHarga.innerText !== totalBaru) {
    teksTotalHarga.innerText = totalBaru;
    teksTotalHarga.classList.remove('updated');
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
  promoAktif = null;=
  saveKeranjang();
  savePromo();
  tampilkanKeranjang();
  tampilkanChipPromo();
  const status = document.getElementById('status-promo');
  if (status) status.textContent = '';
}

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

function bersihkanDataCheckout() {
  localStorage.removeItem("crochy_cart");
  localStorage.removeItem("crochy_promo");
  localStorage.removeItem("crochy_checkout_alamat");
}

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

window.addEventListener('scroll', () => {
  document.getElementById('back-top').classList.toggle('show', window.scrollY > 400);
});

const observer = new IntersectionObserver(entries => entries.forEach(e => {
  if (e.isIntersecting) { e.target.classList.add('visible'); }
}), { threshold: .1 });

document.querySelectorAll('.step').forEach(el => {
  el.classList.add('hidden');
  observer.observe(el);
});

document.querySelectorAll('.product-card').forEach(el => {
  el.classList.add('hidden');
  observer.observe(el);
});

loadKeranjang();

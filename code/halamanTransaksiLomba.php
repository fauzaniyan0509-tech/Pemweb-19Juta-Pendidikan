<?php
include 'penghubung.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Publikasi Lomba - 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght=400;500;600;700;800&display=swap');
    :root {
      --blue: #2f6df6; --blue-dark: #1751d1; --teal: #35c7b6; --green: #2ecc71;
      --dark: #14213d; --muted: #6b7280; --soft-bg: #eef8fb; --card: #ffffff;
      --border: #dbe5ea; --shadow: 0 18px 45px rgba(20, 33, 61, 0.10);
    }
    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
    body { background: linear-gradient(180deg, #eef8fb 0%, #f7fcfb 100%); color: var(--dark); }
    .navbar { background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
    .logo-text { font-weight: 800; color: var(--blue); text-decoration: none; }
    .nav-link { font-size: 14px; color: #1f2937; font-weight: 500; }
    .btn-publish { background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; border-radius: 999px; font-weight: 600; padding: 8px 18px; border: none; }
    .dropdown-menu { border: none; border-radius: 14px; padding: 8px; box-shadow: 0 12px 30px rgba(20,33,61,.12); margin-top: 10px; min-width: 190px; }
    .dropdown-item { border-radius: 10px; padding: 10px 14px; font-size: 14px; font-weight: 600; color: #1f2937; transition: background .15s, color .15s; }
    .dropdown-item:hover, .dropdown-item:focus { background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; }
    .hero-title { font-weight: 800; font-size: 42px; background: linear-gradient(90deg, var(--blue), var(--teal)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .hero-subtitle { color: var(--muted); max-width: 620px; margin: auto; font-size: 15px; }
    .pricing-card, .form-card { background: var(--card); border-radius: 18px; box-shadow: var(--shadow); border: 1px solid #eef2f7; }
    .pricing-card { padding: 24px; cursor: pointer; position: relative; transition: 0.25s ease; border: 2px solid transparent; }
    .pricing-card:hover { transform: translateY(-5px); }
    .pricing-card.selected { border-color: var(--teal); box-shadow: 0 20px 45px rgba(53, 199, 182, 0.18); }
    .package-icon { width: 44px; height: 44px; border-radius: 12px; background: #eaf5ff; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 18px; }
    .package-title { font-size: 18px; font-weight: 700; }
    .price { font-size: 34px; font-weight: 800; color: var(--blue); margin: 0; }
    .duration { color: var(--muted); font-size: 14px; }
    .benefit-list { list-style: none; padding: 0; margin: 18px 0 0; }
    .benefit-list li { font-size: 14px; color: #334155; margin-bottom: 12px; }
    .check { color: var(--green); margin-right: 8px; font-weight: 700; }
    .badge-popular { position: absolute; top: -10px; right: 18px; background: #ffc247; color: white; font-size: 11px; font-weight: 800; padding: 6px 12px; border-radius: 999px; }
    .selected-badge { display: none; background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; border: none; border-radius: 10px; width: 100%; padding: 11px; font-weight: 700; margin-top: 18px; }
    .pricing-card.selected .selected-badge { display: block; }
    .pricing-card.selected .choose-btn { display: none; }
    .choose-btn { width: 100%; border-radius: 10px; padding: 11px; border: none; background: #f1f5f9; color: var(--dark); font-weight: 600; margin-top: 18px; }
    .saving-box { border: 1px dashed var(--teal); background: #f0fffb; border-radius: 10px; font-size: 13px; color: #087f6f; padding: 10px; margin: 15px 0; }

    .form-card { padding: 28px; }
    .form-header { border-bottom: 1px solid var(--border); padding-bottom: 18px; margin-bottom: 20px; display: flex; gap: 14px; align-items: center; }
    .form-icon { width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; display: flex; justify-content: center; align-items: center; font-size: 20px; }
    .section-title { font-weight: 700; margin: 25px 0 14px; font-size: 16px; color: var(--dark); }
    label { font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; }
    .required { color: #ef4444; }
    .form-control, .form-select { border-radius: 10px; font-size: 14px; padding: 11px 13px; border-color: #cbd5e1; }
    .form-control:focus, .form-select:focus { border-color: var(--teal); box-shadow: 0 0 0 4px rgba(53, 199, 182, 0.15); }
    .summary-box { background: #effffb; border: 1px solid var(--teal); border-radius: 14px; padding: 18px; margin-top: 20px; }
    .summary-price { font-size: 24px; font-weight: 800; color: var(--blue); }
    .submit-btn { width: 100%; padding: 14px; border-radius: 12px; border: none; background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; font-weight: 700; margin-top: 18px; transition: 0.25s ease; }
    .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(47, 109, 246, 0.25); }
    .success-modal { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); z-index: 9999; align-items: center; justify-content: center; padding: 20px; }
    .success-box { background: white; border-radius: 22px; padding: 34px; max-width: 430px; text-align: center; box-shadow: var(--shadow); animation: pop 0.25s ease; }
    @keyframes pop { from { transform: scale(0.92); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .success-icon { width: 76px; height: 76px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 42px; margin: 0 auto 18px; }
    .pricing-sticky { position: sticky; top: 90px; }
    @media (max-width: 991px) { .hero-title { font-size: 32px; } .pricing-sticky { position: static !important; } }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg py-3">
    <div class="container">
      <a class="logo-text" href="beranda.php">19JutaPendidikan</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center gap-lg-4">
          <li class="nav-item"><a class="nav-link" href="beranda.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="halamanLomba.php">Lomba</a></li>
          <li class="nav-item"><a class="nav-link" href="halamanBeasiswa.php">Beasiswa</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdownPetaEdukasi" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Peta Edukasi
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownPetaEdukasi">
              <li><a class="dropdown-item" href="halamanTempatEdukatif.php">🔍 Cari Tempat</a></li>
              <li><a class="dropdown-item" href="PengajuanTempat.php">📍 Posting Tempat</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="btn btn-publish" href="halamanTransaksiLomba.php">Publikasi Lomba</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="py-4 text-center">
    <div class="container">
      <h1 class="hero-title">Publikasikan Lomba Anda</h1>
      <p class="hero-subtitle">Jangkau ribuan peserta potensial di seluruh Indonesia melalui platform 19JutaPendidikan.</p>
    </div>
  </section>

  <main class="container pb-5">
    <div class="row g-4 align-items-start">
      <div class="col-lg-4">
        <div class="pricing-sticky">
          <div class="pricing-card mb-4" data-name="Paket Per Lomba" data-price="50000" data-desc="Publikasi 1 lomba selama 30 hari">
            <div class="package-icon">⚡</div>
            <h3 class="package-title">Paket Per Lomba</h3>
            <div class="d-flex align-items-end gap-2">
              <h2 class="price">Rp 50K</h2><span class="duration">/ publikasi</span>
            </div>
            <ul class="benefit-list">
              <li><span class="check">✓</span> Publikasi 1 lomba</li>
              <li><span class="check">✓</span> Tayang selama 30 hari</li>
              <li><span class="check">✓</span> Tampil di halaman utama</li>
            </ul>
            <button type="button" class="choose-btn">Pilih Paket</button>
            <button type="button" class="selected-badge">Paket Terpilih</button>
          </div>

          <div class="pricing-card mb-4 selected" data-name="Paket Langganan Tahunan" data-price="499000" data-desc="Unlimited publikasi selama 12 month">
            <span class="badge-popular">TERPOPULER</span>
            <div class="package-icon">✨</div>
            <h3 class="package-title">Paket Langganan Tahunan</h3>
            <div class="d-flex align-items-end gap-2">
              <h2 class="price">Rp 499K</h2><span class="duration">/ tahun</span>
            </div>
            <div class="saving-box">💰 Hemat hingga Rp 100K+ untuk 10+ lomba!</div>
            <ul class="benefit-list">
              <li><span class="check">✓</span> Publikasi UNLIMITED lomba</li>
              <li><span class="check">✓</span> Berlaku 12 bulan penuh</li>
              <li><span class="check">✓</span> Priority placement</li>
            </ul>
            <button type="button" class="choose-btn">Pilih Paket</button>
            <button type="button" class="selected-badge">Paket Terpilih</button>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="form-card">
          <div class="form-header">
            <div class="form-icon">📂</div>
            <div>
              <h4 class="fw-bold mb-1">Form Pengajuan Publikasi</h4>
              <p class="text-muted mb-0 small">Silakan lengkapi detail informasi pengajuan Anda di bawah ini.</p>
            </div>
          </div>

          <form id="publishForm" action="proses_iklan.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="paket_langganan" id="inputPaket" value="Paket Langganan Tahunan">
            <input type="hidden" name="jumlah" id="inputJumlahBayar" value="499000">

            <h6 class="section-title">Informasi Lomba</h6>
            <div class="mb-3">
              <label for="judul_lomba">Nama Lomba <span class="required">*</span></label>
              <input type="text" name="judul_lomba" id="judul_lomba" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="penyelenggara">Penyelenggara <span class="required">*</span></label>
              <input type="text" name="penyelenggara" id="penyelenggara" class="form-control" required>
            </div>

            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="kategori">Kategori Lomba <span class="required">*</span></label>
                <select name="kategori" id="kategori" class="form-select" required>
                  <option value=""></option>
                  <option value="Akademik">Akademik</option>
                  <option value="Akademik">Non Akademik</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="tingkat_lomba">Tingkat Lomba <span class="required">*</span></label>
                <select name="tingkat_lomba" id="tingkat_lomba" class="form-select" required>
                  <option value=""></option>
                  <option value="Kabupaten / Kota">Kabupaten / Kota</option>
                  <option value="Regional">Provinsi</option>
                  <option value="Nasional">Nasional</option>
                  <option value="Internasional">Internasional</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="deadline">Deadline Pendaftaran <span class="required">*</span></label>
                <input type="date" name="deadline" id="deadline" class="form-control" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="tipe_biaya">Tipe Biaya <span class="required">*</span></label>
                <select name="tipe_biaya" id="tipe_biaya" class="form-select" required>
                  <option value=""></option>
                  <option value="Gratis">Gratis</option>
                  <option value="Berbayar">Berbayar</option>
                </select>
              </div>
              <div class="col-md-8 mb-3">
                <label for="biaya">Nominal Biaya Pendaftaran (Rp) <span class="required">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input type="number" name="biaya" id="biaya" class="form-control" min="0" value="0" required>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="deskripsi">Deskripsi Lomba <span class="required">*</span></label>
              <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" maxlength="200" required></textarea>
            </div>
            
            <div class="mb-3">
              <label for="poster">Upload Poster Lomba <span class="required">*</span></label>
              <input type="file" name="poster" id="poster" class="form-control" accept="image/*" required>
              <small class="text-muted" style="font-size: 11px;">Format yang didukung: JPG, JPEG, PNG.</small>
            </div>

            <div class="mb-3" style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:14px;padding:18px 20px;">
              <label for="kontak_pengaju" style="font-size:13px;font-weight:700;color:#1e3a5f;margin-bottom:6px;display:block;">
                Sumber / Kontak Pengaju <span class="required">*</span>
              </label>
              <input type="text" name="kontak_pengaju" id="kontak_pengaju" class="form-control" required
                placeholder="Contoh: https://instagram.com/namaakun  atau  08123456789  atau  https://web-resmi.com">
              <small class="text-muted" style="font-size:11px;display:block;margin-top:6px;">
                &#9888;&#65039; Wajib diisi untuk verifikasi admin. Masukkan link Instagram, nomor WhatsApp, website resmi, atau media sosial lain yang aktif.
              </small>
            </div>

            <h6 class="section-title">Detail Iklan</h6>
            <div class="mb-3">
              <label for="judul_iklan">Judul Iklan Promosi <span class="required">*</span></label>
              <input type="text" name="judul_iklan" id="judul_iklan" class="form-control" required>
            </div>

            <h6 class="section-title">Metode & Bukti Pembayaran</h6>
            <div class="mb-3">
              <label for="metode_pembayaran">Pilih Metode Pembayaran <span class="required">*</span></label>
              <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                <option value=""></option>
                <option value="Transfer Bank BCA">Transfer Bank BCA (123-456-789 a/n 19JutaPendidikan)</option>
                <option value="Transfer Bank Mandiri">Transfer Bank Mandiri (987-654-321 a/n 19JutaPendidikan)</option>
                <option value="E-Wallet Dana">QRIS / E-Wallet DANA (08123456789)</option>
              </select>
            </div>
            <div class="mb-4">
              <label for="bukti_pembayaran">Upload Bukti Transfer <span class="required">*</span></label>
              <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control" accept="image/*" required>
            </div>

            <div class="summary-box d-flex justify-content-between align-items-center flex-wrap gap-3">
              <div>
                <h6 class="fw-bold mb-1">Ringkasan Pembayaran</h6>
                <p class="mb-0 small fw-semibold" id="summaryPackage">Paket Langganan Tahunan</p>
                <small class="text-muted" id="summaryDesc">Unlimited publikasi selama 12 bulan</small>
              </div>
              <div class="summary-price" id="summaryPrice">Rp 499.000</div>
            </div>

            <button type="submit" class="submit-btn">Kirim Pengajuan</button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <div class="success-modal" id="successModal">
    <div class="success-box">
      <div class="success-icon">✓</div>
      <h3 class="fw-bold">Pengajuan Terkirim!</h3>
      <p class="text-muted">Data sedang disiapkan. Klik selesai untuk memproses penyimpanan sistem dan berpindah halaman.</p>
      <div class="summary-box text-start">
        <small class="text-muted">Total Pembayaran</small>
        <h4 class="fw-bold text-primary mb-0" id="modalPrice">Rp 499.000</h4>
      </div>
      <button class="submit-btn" onclick="closeModal()">Selesai & Lihat Lomba</button>
    </div>
  </div>

  <script>
    const cards = document.querySelectorAll('.pricing-card');
    const summaryPackage = document.getElementById('summaryPackage');
    const summaryDesc = document.getElementById('summaryDesc');
    const summaryPrice = document.getElementById('summaryPrice');
    const modalPrice = document.getElementById('modalPrice');
    const inputPaket = document.getElementById('inputPaket');
    const inputJumlahBayar = document.getElementById('inputJumlahBayar');
    const tipeBiaya = document.getElementById('tipe_biaya');
    const inputBiaya = document.getElementById('biaya');
    const form = document.getElementById('publishForm');
    const successModal = document.getElementById('successModal');

    function formatRupiah(number) {
      return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    cards.forEach(card => {
      card.addEventListener('click', () => {
        cards.forEach(item => item.classList.remove('selected'));
        card.classList.add('selected');
        summaryPackage.textContent = card.dataset.name;
        summaryDesc.textContent = card.dataset.desc;
        summaryPrice.textContent = formatRupiah(card.dataset.price);
        modalPrice.textContent = formatRupiah(card.dataset.price);
        inputPaket.value = card.dataset.name;
        inputJumlahBayar.value = card.dataset.price;
      });
    });

    tipeBiaya.addEventListener('change', function() {
      if (this.value === 'Gratis') {
        inputBiaya.value = 0;
        inputBiaya.readOnly = true;
      } else {
        inputBiaya.readOnly = false;
        if(inputBiaya.value == 0) inputBiaya.value = '';
      }
    });

    form.addEventListener('submit', function(e) {
      e.preventDefault(); 
      successModal.style.display = 'flex';
    });

    function closeModal() {
      successModal.style.display = 'none';
      form.submit(); 
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Publikasi Beasiswa - 19JutaPendidikan</title>
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
      <a class="logo-text" href="#">19JutaPendidikan</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center gap-lg-4">
          <li class="nav-item"><a class="nav-link" href="beranda.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="halamanBeasiswa.php">Beasiswa</a></li>
          <li class="nav-item"><a class="btn btn-publish" href="halamanTransaksiBeasiswa.php">Publikasi Beasiswa</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="py-4 text-center">
    <div class="container">
      <h1 class="hero-title">Publikasikan Beasiswa Anda</h1>
      <p class="hero-subtitle">Jangkau ribuan pelajar dan mahasiswa potensial di seluruh Indonesia melalui platform 19JutaPendidikan.</p>
    </div>
  </section>

  <main class="container pb-5">
    <div class="row g-4 align-items-start">
      <div class="col-lg-4">
        <div class="pricing-sticky">
          <div class="pricing-card mb-4" data-name="Paket Per Beasiswa" data-price="50000" data-desc="Publikasi 1 beasiswa selama 30 hari">
            <div class="package-icon">⚡</div>
            <h3 class="package-title">Paket Per Beasiswa</h3>
            <div class="d-flex align-items-end gap-2">
              <h2 class="price">Rp 50K</h2><span class="duration">/ publikasi</span>
            </div>
            <ul class="benefit-list">
              <li><span class="check">✓</span> Publikasi 1 info beasiswa</li>
              <li><span class="check">✓</span> Tayang selama 30 hari</li>
              <li><span class="check">✓</span> Tampil di halaman utama</li>
            </ul>
            <button type="button" class="choose-btn">Pilih Paket</button>
            <button type="button" class="selected-badge">Paket Terpilih</button>
          </div>

          <div class="pricing-card mb-4 selected" data-name="Paket Langganan Tahunan" data-price="499000" data-desc="Unlimited publikasi selama 12 bulan">
            <span class="badge-popular">TERPOPULER</span>
            <div class="package-icon">✨</div>
            <h3 class="package-title">Paket Langganan Tahunan</h3>
            <div class="d-flex align-items-end gap-2">
              <h2 class="price">Rp 499K</h2><span class="duration">/ tahun</span>
            </div>
            <div class="saving-box">💰 Hemat hingga Rp 100K+ untuk 10+ info beasiswa!</div>
            <ul class="benefit-list">
              <li><span class="check">✓</span> Publikasi UNLIMITED beasiswa</li>
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
            <div class="form-icon">🎓</div>
            <div>
              <h4 class="fw-bold mb-1">Form Pengajuan Publikasi Beasiswa</h4>
              <p class="text-muted mb-0 small">Silakan lengkapi detail informasi program beasiswa di bawah ini.</p>
            </div>
          </div>

          <form id="publishForm" action="proses_iklan.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="paket_langganan" id="inputPaket" value="Paket Langganan Tahunan">
            <input type="hidden" name="jumlah" id="inputJumlahBayar" value="499000">

            <h6 class="section-title">Informasi Beasiswa</h6>
            <div class="mb-3">
              <label for="nama_beasiswa">Nama Beasiswa <span class="required">*</span></label>
              <input type="text" name="nama_beasiswa" id="nama_beasiswa" class="form-control" placeholder="Contoh: Beasiswa Unggulan Bank Indonesia 2026" required>
            </div>
            
            <div class="mb-3">
              <label for="penyelenggara">Penyelenggara <span class="required">*</span></label>
              <input type="text" name="penyelenggara" id="penyelenggara" class="form-control" placeholder="Contoh: Bank Indonesia Kpw Surakarta" required>
            </div>

            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="jenjang">Jenjang Pendidikan <span class="required">*</span></label>
                <select name="jenjang" id="jenjang" class="form-select" required>
                  <option value="" disabled selected>Pilih Jenjang</option>
                  <option value="SMA/SMK">SMA / SMK</option>
                  <option value="D3/D4/S1">D3 / D4 / S1</option>
                  <option value="S2/S3">S2 / S3</option>
                  <option value="Umum">Umum / Semua Jenjang</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="tingkat_beasiswa">Tingkat Beasiswa <span class="required">*</span></label>
                <select name="tingkat_beasiswa" id="tingkat_beasiswa" class="form-select" required>
                  <option value="" disabled selected>Pilih Tingkat</option>
                  <option value="Instansi">Internal Instansi / Kampus</option>
                  <option value="Kota / Kabupaten">Kabupaten / Kota</option>
                  <option value="Provinsi">Provinsi / Regional</option>
                  <option value="Nasional">Nasional</option>
                  <option value="Internasional">Internasional</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="deadline">Deadline Pendaftaran <span class="required">*</span></label>
                <input type="date" name="deadline" id="deadline" class="form-control" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="tipe_pendanaan">Tipe Pendanaan <span class="required">*</span></label>
              <select name="tipe_pendanaan" id="tipe_pendanaan" class="form-select" required>
                <option value="" disabled selected>Pilih Tipe Pendanaan</option>
                <option value="Fully Funded">Fully Funded (Penuh)</option>
                <option value="Partial Funded">Partial Funded (Sebagian)</option>
                <option value="Bantuan Dana">Bantuan Dana / One-Time Stipend</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="deskripsi">Deskripsi & Cakupan Beasiswa <span class="required">*</span></label>
              <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" placeholder="Tuliskan persyaratan utama, fasilitas, atau cakupan biaya yang didapatkan..." required></textarea>
            </div>
            
            <div class="mb-3">
              <label for="poster">Upload Poster Beasiswa <span class="required">*</span></label>
              <input type="file" name="poster" id="poster" class="form-control" accept="image/*" required>
              <small class="text-muted" style="font-size: 11px;">Format yang didukung: JPG, JPEG, PNG.</small>
            </div>

            <h6 class="section-title">Detail Iklan Promosi</h6>
            <div class="mb-3">
              <label for="judul_iklan">Judul Iklan Promosi <span class="required">*</span></label>
              <input type="text" name="judul_iklan" id="judul_iklan" class="form-control" placeholder="Contoh: [KAMPUS] Daftar Segera Beasiswa Prestasi Utama!" required>
            </div>

            <h6 class="section-title">Metode & Bukti Pembayaran</h6>
            <div class="mb-3">
              <label for="metode_pembayaran">Pilih Metode Pembayaran <span class="required">*</span></label>
              <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                <option value="" disabled selected>Pilih Bank/E-Wallet</option>
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
      <button class="submit-btn" onclick="closeModal()">Selesai & Lihat Beasiswa</button>
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
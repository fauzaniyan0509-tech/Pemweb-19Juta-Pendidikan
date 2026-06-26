<?php
// =========================================================
// halamanTransaksiBeasiswa.php
// Form pengajuan beasiswa — TANPA pembayaran (gratis)
// =========================================================
include 'penghubung.php';
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== 'sudah_login') {
    header("Location: halamanLogin.php?pesan=belum_login");
    exit();
}
$nama_user = htmlspecialchars($_SESSION['nama'] ?? 'Pengguna');
$foto_user = !empty($_SESSION['foto_profil']) ? 'uploads/' . $_SESSION['foto_profil'] : '';
$inisial   = strtoupper(mb_substr($_SESSION['nama'] ?? 'U', 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Publikasi Beasiswa - 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    :root {
      --blue: #2f6df6; --teal: #35c7b6; --dark: #14213d;
      --muted: #6b7280; --border: #dbe5ea;
      --shadow: 0 18px 45px rgba(20,33,61,.10);
    }
    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
    body { background: linear-gradient(180deg, #eef8fb 0%, #f7fcfb 100%); color: var(--dark); min-height: 100vh; }

    /* NAVBAR */
    .navbar { background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,.04); }
    .logo-text { font-weight: 800; color: var(--blue); text-decoration: none; font-size: 20px; }
    .nav-link { font-size: 14px; color: #1f2937; font-weight: 500; }
    .nav-link:hover { color: var(--blue); }
    .dropdown-menu { border: none; border-radius: 14px; padding: 8px; box-shadow: 0 12px 30px rgba(20,33,61,.12); }
    .dropdown-item { border-radius: 10px; padding: 10px 14px; font-size: 14px; font-weight: 600; color: #1f2937; }
    .dropdown-item:hover { background: linear-gradient(90deg, var(--blue), var(--teal)); color: #fff; }
    .btn-publish { background: linear-gradient(90deg, var(--blue), var(--teal)); color: #fff; border-radius: 999px; font-weight: 600; padding: 8px 18px; border: none; text-decoration: none; font-size: 14px; }
    .nav-avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid var(--teal); }
    .nav-avatar-init { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--blue), var(--teal)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 15px; }

    /* HERO */
    .hero-section { padding: 48px 0 36px; text-align: center; }
    .hero-title { font-weight: 800; font-size: 38px; background: linear-gradient(90deg, var(--blue), var(--teal)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 10px; }
    .hero-sub { color: var(--muted); font-size: 15px; max-width: 580px; margin: 0 auto 20px; }
    .badge-gratis { display: inline-flex; align-items: center; gap: 8px; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 999px; padding: 10px 22px; font-size: 13px; font-weight: 700; color: #15803d; }

    /* FORM WRAPPER */
    .form-wrap { max-width: 800px; margin: 0 auto; padding: 0 16px 64px; }

    /* STEP CARDS */
    .step-card { background: #fff; border-radius: 20px; box-shadow: var(--shadow); border: 1px solid #eef2f7; padding: 28px 32px; margin-bottom: 20px; }
    .step-header { display: flex; align-items: center; gap: 14px; margin-bottom: 22px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; }
    .step-num { width: 38px; height: 38px; border-radius: 12px; background: linear-gradient(90deg, var(--blue), var(--teal)); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; flex-shrink: 0; }
    .step-title { font-size: 16px; font-weight: 800; color: var(--dark); margin: 0; }
    .step-sub { font-size: 12px; color: var(--muted); margin: 0; }

    /* FORM ELEMENTS */
    .form-label { font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block; }
    .required { color: #ef4444; }
    .form-control, .form-select { border-radius: 10px; font-size: 14px; padding: 11px 14px; border: 1.5px solid #cbd5e1; width: 100%; outline: none; color: var(--dark); }
    .form-control:focus, .form-select:focus { border-color: var(--teal); box-shadow: 0 0 0 4px rgba(53,199,182,.12); }
    .form-text { font-size: 11px; color: var(--muted); margin-top: 5px; }

    /* UPLOAD AREA */
    .upload-area { border: 2px dashed #cbd5e1; border-radius: 14px; padding: 24px; text-align: center; cursor: pointer; transition: .2s; position: relative; }
    .upload-area:hover, .upload-area.dragover { border-color: var(--teal); background: #f0fffb; }
    .upload-area input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
    .upload-icon { font-size: 32px; color: var(--muted); margin-bottom: 8px; }
    .upload-text strong { font-size: 14px; display: block; margin-bottom: 4px; }
    .upload-text small { font-size: 12px; color: var(--muted); }
    .upload-preview { width: 100%; max-height: 180px; object-fit: cover; border-radius: 10px; display: none; margin-top: 10px; }

    /* KONTAK BOX */
    .kontak-box { background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 14px; padding: 20px; }
    .kontak-box .form-label { color: #1e3a5f; }
    .kontak-hint { background: #fff; border-radius: 10px; padding: 12px 14px; margin-top: 12px; }
    .kontak-hint-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #334155; margin-bottom: 6px; font-weight: 500; }
    .kontak-hint-item:last-child { margin-bottom: 0; }
    .kontak-hint-item i { color: var(--blue); font-size: 14px; flex-shrink: 0; }

    /* SUBMIT */
    .submit-wrap { background: #fff; border-radius: 20px; box-shadow: var(--shadow); border: 1px solid #eef2f7; padding: 24px 32px; }
    .submit-btn { width: 100%; padding: 15px; border-radius: 14px; border: none; background: linear-gradient(90deg, var(--blue), var(--teal)); color: #fff; font-weight: 800; font-size: 16px; cursor: pointer; transition: .25s; display: flex; align-items: center; justify-content: center; gap: 10px; }
    .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(47,109,246,.25); }
    .submit-note { font-size: 12px; color: var(--muted); text-align: center; margin-top: 12px; }

    /* SUCCESS MODAL */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.5); z-index: 9999; align-items: center; justify-content: center; padding: 20px; }
    .modal-box { background: #fff; border-radius: 24px; padding: 40px 32px; max-width: 420px; width: 100%; text-align: center; box-shadow: var(--shadow); animation: pop .3s ease; }
    @keyframes pop { from { transform: scale(.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .modal-icon { width: 84px; height: 84px; border-radius: 50%; background: linear-gradient(135deg, #dcfce7, #f0fdf4); display: flex; align-items: center; justify-content: center; font-size: 42px; margin: 0 auto 20px; }
    .modal-box h3 { font-weight: 800; font-size: 22px; margin-bottom: 8px; }
    .modal-box p { color: var(--muted); font-size: 14px; line-height: 1.7; margin-bottom: 24px; }
    .modal-btn { display: block; width: 100%; padding: 14px; border: none; border-radius: 14px; background: linear-gradient(90deg, var(--blue), var(--teal)); color: #fff; font-weight: 700; font-size: 15px; cursor: pointer; text-decoration: none; }

    @media (max-width: 600px) {
      .hero-title { font-size: 28px; }
      .step-card { padding: 20px; }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
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
        <li class="nav-item"><a class="nav-link active" href="halamanBeasiswa.php">Beasiswa</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Peta Edukasi</a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="halamanTempatEdukatif.php">🔍 Cari Tempat</a></li>
            <li><a class="dropdown-item" href="PengajuanTempat.php">📍 Posting Tempat</a></li>
          </ul>
        </li>
        <li class="nav-item ms-2">
          <a href="ProfilPengguna.php">
            <?php if ($foto_user): ?>
              <img src="<?= htmlspecialchars($foto_user) ?>" alt="Profil" class="nav-avatar">
            <?php else: ?>
              <div class="nav-avatar-init"><?= $inisial ?></div>
            <?php endif; ?>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<div class="hero-section">
  <div class="container">
    <h1 class="hero-title">Publikasikan Beasiswa Anda</h1>
    <p class="hero-sub">Bantu ribuan pelajar dan mahasiswa menemukan peluang beasiswa melalui 19JutaPendidikan.</p>
    <div class="badge-gratis">
      <i class="bi bi-patch-check-fill" style="font-size:17px;"></i>
      Pengajuan Beasiswa 100% GRATIS — tidak ada biaya
    </div>
  </div>
</div>

<!-- FORM -->
<div class="form-wrap">
  <form id="formBeasiswa" action="proses_iklan.php" method="POST" enctype="multipart/form-data">

    <!-- Hidden fields — beasiswa tidak perlu pembayaran -->
    <input type="hidden" name="paket_langganan" value="Gratis">
    <input type="hidden" name="jumlah" value="0">
    <input type="hidden" name="metode_pembayaran" value="gratis">
    <!-- judul_iklan diisi otomatis dari nama_beasiswa via JS -->
    <input type="hidden" name="judul_iklan" id="judulIklanHidden">

    <!-- ── STEP 1: INFO BEASISWA ── -->
    <div class="step-card">
      <div class="step-header">
        <div class="step-num">1</div>
        <div>
          <p class="step-title">Informasi Beasiswa</p>
          <p class="step-sub">Lengkapi detail program beasiswa yang ingin dipublikasikan.</p>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label" for="nama_beasiswa">Nama Beasiswa <span class="required">*</span></label>
        <input type="text" name="nama_beasiswa" id="nama_beasiswa" class="form-control"
          placeholder="Contoh: Beasiswa Unggulan Bank Indonesia 2026" required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="penyelenggara">Penyelenggara <span class="required">*</span></label>
        <input type="text" name="penyelenggara" id="penyelenggara" class="form-control"
          placeholder="Contoh: Bank Indonesia KPW Surakarta" required>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <label class="form-label" for="jenjang">Jenjang Pendidikan <span class="required">*</span></label>
          <select name="jenjang" id="jenjang" class="form-select" required>
            <option value="" disabled selected>Pilih Jenjang</option>
            <option value="SMA/SMK">SMA / SMK</option>
            <option value="D3/D4/S1">D3 / D4 / S1</option>
            <option value="S2/S3">S2 / S3</option>
            <option value="Umum">Umum / Semua Jenjang</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="tingkat_beasiswa">Tingkat Beasiswa <span class="required">*</span></label>
          <select name="tingkat_beasiswa" id="tingkat_beasiswa" class="form-select" required>
            <option value="" disabled selected>Pilih Tingkat</option>
            <option value="Instansi">Internal Instansi / Kampus</option>
            <option value="Kota / Kabupaten">Kabupaten / Kota</option>
            <option value="Provinsi">Provinsi / Regional</option>
            <option value="Nasional">Nasional</option>
            <option value="Internasional">Internasional</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="tipe_pendanaan">Tipe Pendanaan <span class="required">*</span></label>
          <select name="tipe_pendanaan" id="tipe_pendanaan" class="form-select" required>
            <option value="" disabled selected>Pilih Tipe</option>
            <option value="Fully Funded">Fully Funded (Penuh)</option>
            <option value="Partial Funded">Partial Funded (Sebagian)</option>
            <option value="Bantuan Dana">Bantuan Dana / One-Time Stipend</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label" for="deadline">Deadline Pendaftaran <span class="required">*</span></label>
        <input type="date" name="deadline" id="deadline" class="form-control" required
          min="<?= date('Y-m-d') ?>">
      </div>

      <div class="mb-0">
        <label class="form-label" for="deskripsi">Deskripsi &amp; Cakupan Beasiswa <span class="required">*</span></label>
        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4"
          placeholder="Tuliskan persyaratan utama, fasilitas, atau cakupan biaya yang didapatkan peserta..." required></textarea>
      </div>
    </div>

    <!-- ── STEP 2: POSTER ── -->
    <div class="step-card">
      <div class="step-header">
        <div class="step-num">2</div>
        <div>
          <p class="step-title">Poster Beasiswa</p>
          <p class="step-sub">Upload poster agar beasiswa lebih menarik perhatian.</p>
        </div>
      </div>

      <label class="form-label">Upload Poster Beasiswa <span class="required">*</span></label>
      <div class="upload-area" id="uploadArea">
        <input type="file" name="poster" id="posterInput" accept="image/*" required
          onchange="previewPoster(this)">
        <div class="upload-icon" id="uploadIcon"><i class="bi bi-image"></i></div>
        <div class="upload-text" id="uploadText">
          <strong>Klik atau seret gambar ke sini</strong>
          <small>JPG, PNG, JPEG — Maks. 5MB</small>
        </div>
        <img id="posterPreview" class="upload-preview" alt="Preview Poster">
      </div>
    </div>

    <!-- ── STEP 3: KONTAK PENGAJU ── -->
    <div class="step-card">
      <div class="step-header">
        <div class="step-num">3</div>
        <div>
          <p class="step-title">Sumber &amp; Kontak Pengaju</p>
          <p class="step-sub">Wajib diisi untuk keperluan verifikasi dan ditampilkan di halaman detail.</p>
        </div>
      </div>

      <div class="kontak-box">
        <label class="form-label" for="kontak_pengaju" style="font-size:13px;font-weight:700;">
          Link atau Nomor Kontak <span class="required">*</span>
        </label>
        <input type="text" name="kontak_pengaju" id="kontak_pengaju" class="form-control" required
          placeholder="Contoh: https://instagram.com/namaakun  atau  08123456789  atau  https://website.com"
          style="border-color:#bfdbfe;">

        <div class="kontak-hint mt-3">
          <p style="font-size:12px;font-weight:700;color:#1e3a5f;margin-bottom:8px;">Format yang diterima:</p>
          <div class="kontak-hint-item"><i class="bi bi-instagram"></i> Link Instagram: https://instagram.com/namaakun</div>
          <div class="kontak-hint-item"><i class="bi bi-whatsapp"></i> Nomor WhatsApp: 08123456789 atau +6281234567</div>
          <div class="kontak-hint-item"><i class="bi bi-globe"></i> Website resmi: https://beasiswa.example.com</div>
          <div class="kontak-hint-item"><i class="bi bi-link-45deg"></i> Media sosial lain: Twitter, TikTok, YouTube, dll.</div>
        </div>

        <p class="form-text mt-2" style="color:#92400e;background:#fffbeb;border-radius:8px;padding:8px 12px;">
          ⚠️ Admin akan menggunakan kontak ini untuk memverifikasi keaslian beasiswa sebelum ditayangkan.
          Kontak juga ditampilkan ke pengguna di halaman detail beasiswa.
        </p>
      </div>
    </div>

    <!-- ── SUBMIT ── -->
    <div class="submit-wrap">
      <button type="submit" class="submit-btn">
        <i class="bi bi-send-fill"></i>
        Kirim Pengajuan Beasiswa
      </button>
      <p class="submit-note">
        Pengajuan akan diverifikasi admin dalam 1–2 hari kerja.
        Pantau status di halaman <a href="ProfilPengguna.php" style="color:var(--blue);font-weight:600;">Profil</a>.
      </p>
    </div>

  </form>
</div>

<!-- SUCCESS MODAL -->
<div class="modal-overlay" id="successModal">
  <div class="modal-box">
    <div class="modal-icon">✅</div>
    <h3>Pengajuan Terkirim!</h3>
    <p>
      Terima kasih, <strong><?= $nama_user ?></strong>!<br>
      Beasiswa kamu sedang dalam antrian verifikasi admin.
      Kamu bisa pantau statusnya di halaman Profil.
    </p>
    <button class="modal-btn" onclick="submitDanRedirect()">
      Lihat Daftar Beasiswa
    </button>
  </div>
</div>

<script>
  const form = document.getElementById('formBeasiswa');

  // Auto-isi judul_iklan dari nama_beasiswa
  document.getElementById('nama_beasiswa').addEventListener('input', function () {
    document.getElementById('judulIklanHidden').value = this.value;
  });

  // Intercept submit — tampilkan modal sukses dulu
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    document.getElementById('judulIklanHidden').value =
      document.getElementById('nama_beasiswa').value;
    document.getElementById('successModal').style.display = 'flex';
  });

  // Submit form sesungguhnya setelah user klik tombol di modal
  function submitDanRedirect() {
    document.getElementById('successModal').style.display = 'none';
    form.submit();
  }

  // Preview poster sebelum upload
  function previewPoster(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const preview = document.getElementById('posterPreview');
        preview.src    = e.target.result;
        preview.style.display = 'block';
        document.getElementById('uploadIcon').style.display = 'none';
        document.getElementById('uploadText').style.display = 'none';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  // Drag & drop visual
  const area = document.getElementById('uploadArea');
  area.addEventListener('dragover',  e => { e.preventDefault(); area.classList.add('dragover'); });
  area.addEventListener('dragleave', () => area.classList.remove('dragover'));
  area.addEventListener('drop',      e => { e.preventDefault(); area.classList.remove('dragover'); });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

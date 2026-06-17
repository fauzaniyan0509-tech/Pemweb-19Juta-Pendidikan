<?php
$host = "localhost"; $user = "root"; $pass = ""; $db = "19juta_pendidikan";
$conn = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($conn, "utf8mb4");

$pesan = ''; $tipe_pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama      = mysqli_real_escape_string($conn, trim($_POST['nama_tempat']));
    $kategori  = mysqli_real_escape_string($conn, $_POST['kategori']);
    $alamat    = mysqli_real_escape_string($conn, trim($_POST['alamat_lengkap']));
    $maps      = mysqli_real_escape_string($conn, trim($_POST['alamat_maps']));
    $jam       = mysqli_real_escape_string($conn, trim($_POST['jam_operasional']));
    $prasarana = mysqli_real_escape_string($conn, trim($_POST['prasarana']));
    $sosmed_ig = mysqli_real_escape_string($conn, trim($_POST['sosmed_instagram'] ?? ''));
    $sosmed_x  = mysqli_real_escape_string($conn, trim($_POST['sosmed_x'] ?? ''));
    $sosmed_yt = mysqli_real_escape_string($conn, trim($_POST['sosmed_youtube'] ?? ''));
    $sosmed_la = mysqli_real_escape_string($conn, trim($_POST['sosmed_lainnya'] ?? ''));
    $wa        = mysqli_real_escape_string($conn, trim($_POST['no_wa_pengaju']));
    $foto_fix  = '';

    // Minimal salah satu sosial media harus diisi
    if ($sosmed_ig === '' && $sosmed_x === '' && $sosmed_yt === '' && $sosmed_la === '') {
        $pesan = 'sosmed_kosong';
    } else {

    if (!empty($_FILES['foto']['name'])) {
        $ext  = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $izin = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $izin) && $_FILES['foto']['size'] <= 3 * 1024 * 1024) {
            $nama_file = 'pengajuan_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $nama_file);
            $foto_fix = $nama_file;
        }
    }

    $q = "INSERT INTO pengajuan_tempat
            (nama_tempat, kategori, alamat_lengkap, alamat_maps, jam_operasional, prasarana, sosmed_instagram, sosmed_x, sosmed_youtube, sosmed_lainnya, foto, nama_pengaju, no_wa_pengaju)
          VALUES ('$nama','$kategori','$alamat','$maps','$jam','$prasarana','$sosmed_ig','$sosmed_x','$sosmed_yt','$sosmed_la','$foto_fix','','$wa')";

    if (mysqli_query($conn, $q)) {
        $pesan = 'sukses';
    } else {
        $pesan = 'gagal';
    }

    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajukan Tempat Edukatif – 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="tempatEdukatif.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
    body { background: linear-gradient(180deg,#eef8fb 0%,#f7fcfb 100%); min-height: 100vh; }

    .hero-ajukan {
      background: linear-gradient(135deg, #2f6df6 0%, #35c7b6 100%);
      padding: 60px 20px 40px; text-align: center; color: white;
    }
    .hero-ajukan h1 { font-size: 36px; font-weight: 800; margin-bottom: 10px; }
    .hero-ajukan p  { font-size: 15px; opacity: .88; max-width: 520px; margin: 0 auto; }

    .wrapper-form { max-width: 720px; margin: -30px auto 60px; padding: 0 20px; }

    .kartu-form {
      background: white; border-radius: 22px; padding: 36px;
      box-shadow: 0 18px 50px rgba(20,33,61,.10);
    }

    .seksi-judul {
      font-size: 15px; font-weight: 800; color: #2f6df6;
      border-left: 4px solid #35c7b6; padding-left: 12px;
      margin: 28px 0 18px;
    }
    .seksi-judul:first-of-type { margin-top: 0; }

    .form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px; }
    .form-control, .form-select {
      border-radius: 12px; padding: 11px 14px; font-size: 14px;
      border: 1.5px solid #e2e8f0; font-family: 'Poppins', sans-serif;
      transition: border .2s;
    }
    .form-control:focus, .form-select:focus {
      border-color: #2f6df6; outline: none;
      box-shadow: 0 0 0 3px rgba(47,109,246,.10);
    }
    textarea.form-control { resize: vertical; min-height: 80px; }
    .input-group-text {
      border-radius: 12px 0 0 12px; border: 1.5px solid #e2e8f0;
      background: #f8fafc; font-size: 16px;
    }
    .input-group .form-control { border-radius: 0 12px 12px 0; }

    .upload-area {
      border: 2px dashed #cbd5e1; border-radius: 14px;
      padding: 24px; text-align: center; cursor: pointer; transition: border .2s;
    }
    .upload-area:hover { border-color: #2f6df6; }
    .upload-area input { display: none; }
    .preview-foto { width: 100%; max-height: 200px; object-fit: cover; border-radius: 12px; margin-top: 12px; display: none; }

    .info-box {
      background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px;
      padding: 14px 18px; font-size: 13px; color: #15803d; margin-bottom: 24px;
    }
    .info-box strong { display: block; margin-bottom: 4px; }

    .btn-kirim {
      background: linear-gradient(90deg, #2f6df6, #35c7b6);
      color: white; border: none; border-radius: 14px;
      padding: 14px; font-size: 15px; font-weight: 800;
      width: 100%; cursor: pointer; transition: opacity .2s;
    }
    .btn-kirim:hover { opacity: .88; }

    /* SUKSES STATE */
    .sukses-wrap { text-align: center; padding: 20px 0; }
    .sukses-ikon { font-size: 64px; margin-bottom: 16px; }
    .sukses-judul { font-size: 24px; font-weight: 800; color: #14213d; margin-bottom: 8px; }
    .sukses-sub { color: #64748b; font-size: 14px; max-width: 400px; margin: 0 auto 24px; line-height: 1.7; }
    .btn-kembali {
      display: inline-block; background: linear-gradient(90deg,#2f6df6,#35c7b6);
      color: white; border-radius: 14px; padding: 12px 28px;
      font-weight: 700; font-size: 14px; text-decoration: none;
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg py-3" style="background:white; box-shadow: 0 2px 16px rgba(20,33,61,.07);">
    <div class="container">
      <a class="logo-teks" href="beranda.php" style="font-weight:800; font-size:20px; color:#2f6df6; text-decoration:none;">19JutaPendidikan</a>
      <div class="collapse navbar-collapse justify-content-end" id="nav">
        <ul class="navbar-nav align-items-center gap-lg-4">
          <li><a class="nav-link" href="beranda.php">Beranda</a></li>
          <li><a class="nav-link" href="halamanTempatEdukatif.php">Tempat Edukatif</a></li>
          <li><a class="nav-link aktif" href="PengajuanTempat.php">Pengajuan Tempat</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <div class="hero-ajukan">
    <h1>📍 Ajukan Tempat Edukatif</h1>
    <p>Daftarkan tempat belajar, kafe edukatif, atau ruang kreatif milikmu agar lebih mudah ditemukan pelajar dan mahasiswa.</p>
  </div>

  <div class="wrapper-form">
    <div class="kartu-form">

      <?php if ($pesan === 'sukses'): ?>
      <!-- SUKSES -->
      <div class="sukses-wrap">
        <div class="sukses-ikon">🎉</div>
        <div class="sukses-judul">Pengajuan Terkirim!</div>
        <p class="sukses-sub">
          Terima kasih! Pengajuan tempat edukatifmu sedang dalam review oleh tim admin.
          Jika disetujui, tempat kamu akan tampil di halaman Peta Akses Pendidikan.
        </p>
        <a href="halamanTempatEdukatif.php" class="btn-kembali">Lihat Peta Edukasi</a>
      </div>

      <?php elseif ($pesan === 'gagal'): ?>
      <div class="alert alert-danger rounded-4">Terjadi kesalahan, coba lagi.</div>

      <?php else: ?>

      <?php if ($pesan === 'sosmed_kosong'): ?>
      <div class="alert alert-danger rounded-4">⚠️ Isi minimal salah satu Sosial Media (Instagram, X, YouTube, atau Lainnya) sebelum mengirim pengajuan.</div>
      <?php endif; ?>

      <!-- INFO -->
      <div class="info-box">
        <strong>ℹ️ Proses Pengajuan</strong>
        Pengajuanmu akan direview oleh admin terlebih dahulu sebelum tampil di halaman publik. Proses review biasanya 1–3 hari kerja. Tidak dipungut biaya apapun.
      </div>

      <form action="" method="POST" enctype="multipart/form-data">

        <!-- INFORMASI TEMPAT -->
        <div class="seksi-judul">Informasi Tempat</div>

        <div class="mb-3">
          <label class="form-label">Nama Tempat *</label>
          <input type="text" name="nama_tempat" class="form-control" placeholder="Contoh: Kopi Djati, Perpustakaan UNS" required>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Kategori *</label>
            <select name="kategori" class="form-select" required>
              <option value="">-- Pilih Kategori --</option>
              <option value="perpustakaan">📚 Perpustakaan</option>
              <option value="kafe-belajar">☕ Kafe Belajar</option>
              <option value="teknologi">💡 Teknologi & Inovasi</option>
              <option value="museum">🏛️ Museum & Sejarah</option>
              <option value="ruang-kreatif">🎨 Ruang Kreatif</option>
              <option value="lainnya">📌 Lainnya</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Jam Operasional</label>
            <input type="text" name="jam_operasional" class="form-control" placeholder="Contoh: Senin–Minggu: 08.00–22.00">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Alamat Lengkap</label>
          <textarea name="alamat_lengkap" class="form-control" placeholder="Tulis alamat lengkap tempat kamu"></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Link Google Maps</label>
          <input type="url" name="alamat_maps" class="form-control" placeholder="https://maps.app.goo.gl/...">
          <small class="text-muted">Buka Google Maps → Share → Copy link</small>
        </div>

        <div class="mb-3">
          <label class="form-label">Fasilitas / Prasarana</label>
          <input type="text" name="prasarana" class="form-control" placeholder="Pisahkan dengan koma. Contoh: WiFi, AC, Meja, Stop Kontak">
        </div>

        <div class="mb-3">
          <label class="form-label">Sosial Media <span class="text-muted" style="font-weight:500;">(isi minimal salah satu)</span></label>
          <div class="row g-2">
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-text">📷</span>
                <input type="text" name="sosmed_instagram" class="form-control sosmed-input" placeholder="Instagram: @namainstagram atau link" value="<?= htmlspecialchars($_POST['sosmed_instagram'] ?? '') ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-text">✖️</span>
                <input type="text" name="sosmed_x" class="form-control sosmed-input" placeholder="X: @username atau link" value="<?= htmlspecialchars($_POST['sosmed_x'] ?? '') ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-text">▶️</span>
                <input type="text" name="sosmed_youtube" class="form-control sosmed-input" placeholder="YouTube: @channel atau link" value="<?= htmlspecialchars($_POST['sosmed_youtube'] ?? '') ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-text">🔗</span>
                <input type="text" name="sosmed_lainnya" class="form-control sosmed-input" placeholder="Lainnya: Linktree, TikTok, Website, dll" value="<?= htmlspecialchars($_POST['sosmed_lainnya'] ?? '') ?>">
              </div>
            </div>
          </div>
          <div id="sosmedPeringatan" class="text-danger mt-2" style="font-size:12px; font-weight:600; display:none;">
            ⚠️ Isi minimal salah satu sosial media di atas.
          </div>
        </div>

        <!-- FOTO -->
        <div class="seksi-judul">Foto Tempat</div>

        <div class="mb-4">
          <div class="upload-area" onclick="document.getElementById('inputFoto').click()">
            <div style="font-size:36px;">📷</div>
            <p style="margin:6px 0 0; font-size:13px; color:#64748b;">Klik untuk upload foto tempat<br><small>Format JPG/PNG/WEBP, maks. 3MB</small></p>
            <input type="file" id="inputFoto" name="foto" accept=".jpg,.jpeg,.png,.webp" onchange="previewFoto(this)">
            <img id="previewGbr" class="preview-foto" alt="preview">
          </div>
        </div>

        <!-- IDENTITAS PENGAJU -->
        <div class="seksi-judul">Identitas Pengaju</div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label">Nomor WhatsApp *</label>
            <input type="text" name="no_wa_pengaju" class="form-control" placeholder="Contoh: 08123456789" required>
          </div>
        </div>

        <button type="submit" class="btn-kirim">Kirim Pengajuan →</button>
      </form>

      <?php endif; ?>
    </div>
  </div>

  <script>
    function previewFoto(input) {
      const preview = document.getElementById('previewGbr');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
      }
    }

    // Validasi: minimal 1 dari 4 field sosial media wajib diisi
    const formPengajuan = document.querySelector('.kartu-form form');
    if (formPengajuan) {
      formPengajuan.addEventListener('submit', function (e) {
        const inputSosmed = formPengajuan.querySelectorAll('.sosmed-input');
        const adaIsi = Array.from(inputSosmed).some(i => i.value.trim() !== '');
        const peringatan = document.getElementById('sosmedPeringatan');

        if (!adaIsi) {
          e.preventDefault();
          peringatan.style.display = 'block';
          inputSosmed[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
          peringatan.style.display = 'none';
        }
      });
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
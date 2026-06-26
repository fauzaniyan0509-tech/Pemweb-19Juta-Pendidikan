<?php
// =========================================================
// detailLomba.php  —  Halaman Detail Lomba (BARU)
// Diakses via: detailLomba.php?id=X
// Menggunakan CSS dari detailLomba.css yang sudah ada
// =========================================================
include 'penghubung.php';

// 1. Validasi parameter id dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: halamanLomba.php");
    exit();
}

$id_lomba = (int) $_GET['id'];

// 2. Ambil data lomba — hanya yang sudah disetujui admin
$stmt = $conn->prepare(
    "SELECT l.* 
     FROM lomba l 
     JOIN iklan_lomba i ON l.id_lomba = i.id_lomba 
     WHERE i.status_verifikasi = 'disetujui' 
       AND l.id_lomba = ?
     LIMIT 1"
);
$stmt->bind_param("i", $id_lomba);
$stmt->execute();
$result = $stmt->get_result();

// 3. Redirect jika tidak ditemukan / belum disetujui
if ($result->num_rows === 0) {
    header("Location: halamanLomba.php");
    exit();
}

$lomba = $result->fetch_assoc();
$stmt->close();

// 4. Variabel tampilan
$file_poster    = !empty($lomba['poster']) ? $lomba['poster'] : 'default.jpg';
$is_gratis      = strtolower($lomba['tipe_biaya']) === 'gratis';
$deadline_lewat = strtotime($lomba['deadline']) < strtotime(date('Y-m-d'));

// 5. Proses kontak pengaju — deteksi jenis & normalisasi link
$kontak_raw   = trim($lomba['kontak_pengaju'] ?? '');
$kontak_url   = '';
$kontak_label = '';
$kontak_icon  = '';

if (!empty($kontak_raw)) {
    // Normalisasi: jika nomor WA (angka/diawali 08/+62), ubah ke wa.me
    $angka_saja = preg_replace('/\D/', '', $kontak_raw);
    if (preg_match('/^(08|628|\+628)/', str_replace(' ', '', $kontak_raw)) || 
        (is_numeric($angka_saja) && strlen($angka_saja) >= 9 && strlen($angka_saja) <= 14)) {
        if (str_starts_with($angka_saja, '0')) {
            $angka_saja = '62' . substr($angka_saja, 1);
        }
        $kontak_url   = 'https://wa.me/' . $angka_saja;
        $kontak_label = 'Hubungi via WhatsApp';
        $kontak_icon  = 'bi-whatsapp';
    } elseif (stripos($kontak_raw, 'instagram.com') !== false || stripos($kontak_raw, 'ig.me') !== false) {
        $kontak_url   = str_starts_with($kontak_raw, 'http') ? $kontak_raw : 'https://' . $kontak_raw;
        $kontak_label = 'Lihat Instagram Pengaju';
        $kontak_icon  = 'bi-instagram';
    } elseif (stripos($kontak_raw, 'twitter.com') !== false || stripos($kontak_raw, 'x.com') !== false) {
        $kontak_url   = str_starts_with($kontak_raw, 'http') ? $kontak_raw : 'https://' . $kontak_raw;
        $kontak_label = 'Lihat Twitter / X Pengaju';
        $kontak_icon  = 'bi-twitter-x';
    } elseif (stripos($kontak_raw, 'tiktok.com') !== false) {
        $kontak_url   = str_starts_with($kontak_raw, 'http') ? $kontak_raw : 'https://' . $kontak_raw;
        $kontak_label = 'Lihat TikTok Pengaju';
        $kontak_icon  = 'bi-tiktok';
    } elseif (stripos($kontak_raw, 'youtube.com') !== false) {
        $kontak_url   = str_starts_with($kontak_raw, 'http') ? $kontak_raw : 'https://' . $kontak_raw;
        $kontak_label = 'Lihat YouTube Pengaju';
        $kontak_icon  = 'bi-youtube';
    } else {
        // Link umum / website
        $kontak_url   = str_starts_with($kontak_raw, 'http') ? $kontak_raw : 'https://' . $kontak_raw;
        $kontak_label = 'Kunjungi Sumber Lomba';
        $kontak_icon  = 'bi-box-arrow-up-right';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($lomba['judul_lomba']) ?> | 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <!-- Pakai CSS detail yang sudah ada di project -->
  <link rel="stylesheet" href="detailLomba.css">
  <style>
    /* Tambahan untuk kontak pengaju — mengikuti palet detailLomba.css */
    .kotak-sumber {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      border-radius: 16px;
      padding: 18px 20px;
      margin-top: 4px;
    }
    .kotak-sumber p {
      font-size: 13.5px;
      color: #1e3a5f;
      margin-bottom: 14px;
      line-height: 1.7;
    }
    .tombol-kontak-pengaju {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(90deg, #2f6df6, #35c7b6);
      color: #fff;
      border-radius: 12px;
      padding: 11px 20px;
      font-size: 14px;
      font-weight: 700;
      text-decoration: none;
      transition: opacity .2s, transform .2s;
    }
    .tombol-kontak-pengaju:hover {
      opacity: .88;
      color: #fff;
      transform: translateY(-2px);
    }
    /* Tombol kontak di sidebar */
    .tombol-kontak-sidebar {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      border: 2px solid #2f6df6;
      color: #2f6df6;
      border-radius: 14px;
      padding: 12px;
      font-size: 14px;
      font-weight: 700;
      text-decoration: none;
      transition: background .2s, color .2s;
      margin-bottom: 10px;
    }
    .tombol-kontak-sidebar:hover {
      background: linear-gradient(90deg, #2f6df6, #35c7b6);
      color: #fff;
      border-color: transparent;
    }
  </style>
</head>
<body>

  <!-- NAVBAR — mengikuti pola navbar project -->
  <nav class="navbar navbar-expand-lg py-3 sticky-top">
    <div class="container">
      <a class="logo-teks" href="beranda.php">19JutaPendidikan</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center gap-lg-4">
          <li class="nav-item"><a class="nav-link" href="beranda.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="halamanLomba.php">Lomba</a></li>
          <li class="nav-item ms-lg-2">
            <a href="ProfilPengguna.php">
              <img src="foto-profil-user.jpg" alt="Profil" class="foto-profil-nav"
                   onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
              <div style="display:none;width:40px;height:40px;border-radius:50%;
                background:linear-gradient(90deg,#2f6df6,#35c7b6);
                align-items:center;justify-content:center;color:white;font-weight:700;font-size:16px;">U</div>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-5">

    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="beranda.php">Beranda</a></li>
        <li class="breadcrumb-item"><a href="halamanLomba.php">Daftar Lomba</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Lomba</li>
      </ol>
    </nav>

    <div class="row g-5">

      <!-- ===================== KOLOM KIRI ===================== -->
      <div class="col-lg-8">

        <!-- Poster -->
        <img src="uploads/<?= htmlspecialchars($file_poster) ?>"
             alt="Poster <?= htmlspecialchars($lomba['judul_lomba']) ?>"
             class="gambar-banner"
             onerror="this.src='uploads/default.jpg'">

        <!-- Judul & subjudul -->
        <h1 class="judul-lomba"><?= htmlspecialchars($lomba['judul_lomba']) ?></h1>
        <p class="subjudul-lomba">
          Diselenggarakan oleh <strong><?= htmlspecialchars($lomba['penyelenggara']) ?></strong>
          &nbsp;·&nbsp; Tingkat: <strong><?= htmlspecialchars($lomba['tingkat_lomba']) ?></strong>
        </p>

        <!-- DESKRIPSI -->
        <h4 class="kepala-seksi">Deskripsi Lomba</h4>
        <p class="teks-deskripsi">
          <?= nl2br(htmlspecialchars($lomba['deskripsi'] ?? 'Informasi deskripsi belum tersedia.')) ?>
        </p>

        <!-- BIAYA -->
        <h4 class="kepala-seksi">Biaya Pendaftaran</h4>
        <div class="teks-hadiah">
          <?php if ($is_gratis): ?>
            ✅ <strong>GRATIS</strong> — Tidak dipungut biaya pendaftaran apapun.
          <?php else: ?>
            💳 <strong>Berbayar</strong> — Biaya: <strong>Rp <?= number_format($lomba['biaya'] ?? 0, 0, ',', '.') ?></strong>
          <?php endif; ?>
        </div>

        <!-- SUMBER / KONTAK PENGAJU -->
        <?php if (!empty($kontak_url)): ?>
        <h4 class="kepala-seksi">Sumber & Kontak Pengaju</h4>
        <div class="kotak-sumber">
          <p>
            Ingin memverifikasi keaslian lomba ini atau bertanya langsung kepada pengaju?
            Klik tombol di bawah untuk menghubungi atau membuka sumber resmi lomba ini.
          </p>
          <a href="<?= htmlspecialchars($kontak_url) ?>"
             target="_blank" rel="noopener noreferrer"
             class="tombol-kontak-pengaju">
            <i class="bi <?= $kontak_icon ?>"></i>
            <?= htmlspecialchars($kontak_label) ?>
          </a>
        </div>
        <?php endif; ?>

      </div>

      <!-- ===================== KOLOM KANAN (SIDEBAR) ===================== -->
      <div class="col-lg-4">
        <div class="kartu-sidebar">
          <h5 class="judul-sidebar">Informasi Penting</h5>

          <!-- Penyelenggara -->
          <p class="label-info">penyelenggara</p>
          <p class="nilai-info"><?= htmlspecialchars($lomba['penyelenggara']) ?></p>

          <!-- Kategori -->
          <p class="label-info">kategori</p>
          <p class="nilai-info">
            <span class="badge-kategori"><?= htmlspecialchars($lomba['kategori']) ?></span>
          </p>

          <!-- Tingkat -->
          <p class="label-info">tingkat lomba</p>
          <p class="nilai-info"><?= htmlspecialchars($lomba['tingkat_lomba']) ?></p>

          <!-- Biaya -->
          <p class="label-info">biaya</p>
          <?php if ($is_gratis): ?>
            <p class="teks-gratis"><i class="bi bi-check-circle-fill me-1"></i> Gratis / Free</p>
          <?php else: ?>
            <p class="nilai-info" style="color:#e11d48;">
              Rp <?= number_format($lomba['biaya'] ?? 0, 0, ',', '.') ?>
            </p>
          <?php endif; ?>

          <!-- Status -->
          <p class="label-info">status pendaftaran</p>
          <p class="nilai-info">
            <?php if ($deadline_lewat): ?>
              <span class="badge-status-buka" style="background:#fee2e2;color:#b91c1c;border-color:#fca5a5;">
                <i class="bi bi-circle-fill me-1" style="font-size:8px;"></i> Pendaftaran Ditutup
              </span>
            <?php else: ?>
              <span class="badge-status-buka">
                <i class="bi bi-circle-fill me-1" style="font-size:8px;"></i> Pendaftaran Dibuka
              </span>
            <?php endif; ?>
          </p>

          <!-- Deadline -->
          <div class="kotak-deadline">
            <p class="label-deadline">deadline pendaftaran</p>
            <h4 class="tanggal-deadline">
              <?= date('d F Y', strtotime($lomba['deadline'])) ?>
            </h4>
          </div>

          <!-- Tombol kontak di sidebar -->
          <?php if (!empty($kontak_url)): ?>
          <p class="label-info" style="margin-bottom:8px;">sumber / kontak pengaju</p>
          <a href="<?= htmlspecialchars($kontak_url) ?>"
             target="_blank" rel="noopener noreferrer"
             class="tombol-kontak-sidebar">
            <i class="bi <?= $kontak_icon ?>"></i>
            <?= htmlspecialchars($kontak_label) ?>
          </a>
          <?php endif; ?>

          <!-- Tombol kembali -->
          <a href="halamanLomba.php" class="tombol-unduh mt-2">
            ← Kembali ke Daftar Lomba
          </a>

          <hr class="pemisah">

          <!-- Bagikan -->
          <p class="teks-bagikan">Bagikan lomba ini:</p>
          <div class="baris-bagikan">
            <a href="https://wa.me/?text=<?= urlencode('Cek lomba ini: ' . $lomba['judul_lomba'] . ' — ' . (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '')) ?>"
               target="_blank" class="ikon-sosmed" title="WhatsApp">
              <i class="bi bi-whatsapp"></i>
            </a>
            <a href="#" class="ikon-sosmed" title="Instagram">
              <i class="bi bi-instagram"></i>
            </a>
            <a href="#" class="ikon-sosmed" title="Salin Link" onclick="salinLink(event)">
              <i class="bi bi-link-45deg"></i>
            </a>
          </div>

        </div>
      </div>

    </div>
  </div>

  <script>
    function salinLink(e) {
      e.preventDefault();
      navigator.clipboard.writeText(window.location.href).then(() => {
        alert('Link berhasil disalin!');
      });
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

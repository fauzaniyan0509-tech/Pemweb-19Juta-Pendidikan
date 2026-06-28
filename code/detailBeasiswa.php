<?php
// =========================================================
// detailBeasiswa.php  —  Halaman Detail Beasiswa
// Diakses via: detailBeasiswa.php?id=X
// =========================================================
include 'penghubung.php';

$foto_user = "foto-profil-user.jpg"; // Default jika tidak ada foto
if (!empty($_SESSION['foto_profil'])) {
    $foto_user = "uploads/" . $_SESSION['foto_profil'];
}

// 1. Validasi parameter id dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: halamanBeasiswa.php");
    exit();
}

$id_beasiswa = (int) $_GET['id'];

// 2. Ambil data beasiswa — hanya yang sudah disetujui admin
$stmt = $conn->prepare(
    "SELECT b.* 
     FROM beasiswa b 
     JOIN iklan_beasiswa i ON b.id_beasiswa = i.id_beasiswa 
     WHERE i.status_verifikasi = 'disetujui' 
       AND b.id_beasiswa = ?
     LIMIT 1"
);
$stmt->bind_param("i", $id_beasiswa);
$stmt->execute();
$result = $stmt->get_result();

// 3. Redirect jika tidak ditemukan / belum disetujui
if ($result->num_rows === 0) {
    header("Location: halamanBeasiswa.php");
    exit();
}

$beasiswa = $result->fetch_assoc();
$stmt->close();

// 4. Variabel tampilan
$file_poster    = !empty($beasiswa['poster']) ? $beasiswa['poster'] : 'default.jpg';
$deadline_lewat = strtotime($beasiswa['deadline']) < strtotime(date('Y-m-d'));

// 5. Normalisasi tipe pendanaan untuk tampilan
$tipe_pendanaan = $beasiswa['tipe_pendanaan'];
$tipe_display = '';
$tipe_icon = '';

if (stripos($tipe_pendanaan, 'fully funded') !== false) {
    $tipe_display = 'Fully Funded (Penuh)';
    $tipe_icon = '✅';
} elseif (stripos($tipe_pendanaan, 'partial') !== false) {
    $tipe_display = 'Partial Funded (Sebagian)';
    $tipe_icon = '💰';
} else {
    $tipe_display = $tipe_pendanaan;
    $tipe_icon = '🎁';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($beasiswa['nama_beasiswa']) ?> | 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="detailLomba.css">
  <style>
    /* Tambahan khusus untuk beasiswa */
    .kotak-cakupan {
      background: #f0fdf4;
      border: 1px solid #86efac;
      border-radius: 16px;
      padding: 20px 22px;
      margin-top: 4px;
    }
    .kotak-cakupan h5 {
      font-size: 15px;
      font-weight: 700;
      color: #166534;
      margin-bottom: 12px;
    }
    .kotak-cakupan ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .kotak-cakupan li {
      font-size: 14px;
      color: #14532d;
      padding: 8px 0;
      border-bottom: 1px solid #dcfce7;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .kotak-cakupan li:last-child {
      border-bottom: none;
    }
    .kotak-cakupan li i {
      color: #22c55e;
      font-size: 16px;
    }
    
    .badge-jenjang {
      display: inline-block;
      background: linear-gradient(90deg, #dbeafe, #bfdbfe);
      color: #1e40af;
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
    }
    
    .badge-tingkat {
      display: inline-block;
      background: linear-gradient(90deg, #fef3c7, #fde68a);
      color: #92400e;
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
    }
    
    .badge-pendanaan {
      display: inline-block;
      background: linear-gradient(90deg, #d1fae5, #a7f3d0);
      color: #065f46;
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
    }
    
    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin: 20px 0;
    }
    
    .info-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 16px 18px;
    }
    
    .info-box-label {
      font-size: 11px;
      font-weight: 700;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 6px;
    }
    
    .info-box-value {
      font-size: 15px;
      font-weight: 700;
      color: #0f2942;
    }
    /* Poster Detail Beasiswa - Portrait Style */
.poster-detail-beasiswa {
  background: #f1f5f9; /* Background abu-abu muda */
  border-radius: 20px;
  padding: 24px; /* Jarak di sekeliling poster */
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 450px; /* Tinggi area portrait */
  margin-bottom: 24px;
  overflow: hidden;
}

.poster-detail-beasiswa img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain; /* KUNCI: Agar gambar utuh tidak terpotong */
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08); /* Bayangan halus */
}

/* Responsive untuk mobile */
@media (max-width: 768px) {
  .poster-detail-beasiswa {
    min-height: 350px;
    padding: 16px;
  }
}
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg py-3 sticky-top">
    <div class="container">
      <a class="logo-teks" href="beranda.php">19JutaPendidikan</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center gap-lg-4">
          <li class="nav-item"><a class="nav-link" href="beranda.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="halamanBeasiswa.php">Beasiswa</a></li>
          <li class="nav-item ms-lg-2">
            <a href="ProfilPengguna.php">
              <img src="<?= $foto_user ?>" alt="Profil" 
                   style="width:40px;height:40px;object-fit:cover;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.15);"
                   onerror="this.src='foto-profil-user.jpg'">
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
        <li class="breadcrumb-item"><a href="halamanBeasiswa.php">Daftar Beasiswa</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Beasiswa</li>
      </ol>
    </nav>

    <div class="row g-5">

      <!-- ===================== KOLOM KIRI ===================== -->
      <div class="col-lg-8">

        <!-- Poster - Portrait Style -->
        <div class="poster-detail-beasiswa">
        <img src="uploads/<?= htmlspecialchars($file_poster) ?>"
            alt="Poster <?= htmlspecialchars($beasiswa['nama_beasiswa']) ?>"
            onerror="this.src='uploads/default.jpg'">
        </div>
        <!-- Judul & subjudul -->
        <h1 class="judul-lomba"><?= htmlspecialchars($beasiswa['nama_beasiswa']) ?></h1>
        <p class="subjudul-lomba">
          Diselenggarakan oleh <strong><?= htmlspecialchars($beasiswa['penyelenggara']) ?></strong>
          &nbsp;·&nbsp; Tingkat: <strong><?= htmlspecialchars($beasiswa['tingkat_beasiswa']) ?></strong>
        </p>

        <!-- Info Grid -->
        <div class="info-grid">
          <div class="info-box">
            <div class="info-box-label">Jenjang Pendidikan</div>
            <div class="info-box-value">
              <span class="badge-jenjang"><?= htmlspecialchars($beasiswa['jenjang']) ?></span>
            </div>
          </div>
          <div class="info-box">
            <div class="info-box-label">Tingkat Beasiswa</div>
            <div class="info-box-value">
              <span class="badge-tingkat"><?= htmlspecialchars($beasiswa['tingkat_beasiswa']) ?></span>
            </div>
          </div>
          <div class="info-box">
            <div class="info-box-label">Tipe Pendanaan</div>
            <div class="info-box-value">
              <span class="badge-pendanaan"><?= $tipe_icon ?> <?= htmlspecialchars($tipe_display) ?></span>
            </div>
          </div>
        </div>

        <!-- DESKRIPSI -->
        <h4 class="kepala-seksi">Deskripsi Beasiswa</h4>
        <p class="teks-deskripsi">
          <?= nl2br(htmlspecialchars($beasiswa['deskripsi'] ?? 'Informasi deskripsi belum tersedia.')) ?>
        </p>

        <!-- CAKUPAN / FASILITAS (Jika ada di deskripsi) -->
        <?php if (!empty($beasiswa['deskripsi'])): ?>
        <h4 class="kepala-seksi">Cakupan & Fasilitas</h4>
        <div class="kotak-cakupan">
          <h5>💡 Apa yang akan kamu dapatkan?</h5>
          <ul>
            <?php
            // Coba ekstrak poin-poin dari deskripsi (jika ada format list)
            $deskripsi = $beasiswa['deskripsi'];
            
            // Deteksi jika ada format list (dengan bullet, angka, atau dash)
            if (preg_match('/[•\-\*]\s+/', $deskripsi) || preg_match('/\d+\.\s+/', $deskripsi)) {
                // Pisahkan berdasarkan baris
                $lines = preg_split('/\r\n|\r|\n/', $deskripsi);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        // Hapus bullet/angka di awal
                        $line = preg_replace('/^[•\-\*\d\.]+\s+/', '', $line);
                        if (!empty($line)) {
                            echo '<li><i class="bi bi-check-circle-fill"></i> ' . htmlspecialchars($line) . '</li>';
                        }
                    }
                }
            } else {
                // Jika tidak ada format list, tampilkan deskripsi penuh
                echo '<li><i class="bi bi-info-circle-fill"></i> ' . htmlspecialchars($deskripsi) . '</li>';
            }
            ?>
          </ul>
        </div>
        <?php endif; ?>

      </div>

      <!-- ===================== KOLOM KANAN (SIDEBAR) ===================== -->
      <div class="col-lg-4">
        <div class="kartu-sidebar">
          <h5 class="judul-sidebar">Informasi Penting</h5>

          <!-- Penyelenggara -->
          <p class="label-info">penyelenggara</p>
          <p class="nilai-info"><?= htmlspecialchars($beasiswa['penyelenggara']) ?></p>

          <!-- Jenjang -->
          <p class="label-info">jenjang pendidikan</p>
          <p class="nilai-info">
            <span class="badge-kategori"><?= htmlspecialchars($beasiswa['jenjang']) ?></span>
          </p>

          <!-- Tingkat -->
          <p class="label-info">tingkat beasiswa</p>
          <p class="nilai-info"><?= htmlspecialchars($beasiswa['tingkat_beasiswa']) ?></p>

          <!-- Tipe Pendanaan -->
          <p class="label-info">tipe pendanaan</p>
          <p class="nilai-info">
            <span class="badge-kategori" style="background:#d1fae5;color:#065f46;">
              <?= $tipe_icon ?> <?= htmlspecialchars($tipe_display) ?>
            </span>
          </p>

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
              <?= ($beasiswa['deadline'] && $beasiswa['deadline'] != '0000-00-00') ? date('d F Y', strtotime($beasiswa['deadline'])) : 'Tanpa Deadline' ?>
            </h4>
          </div>

          <!-- Tombol kembali -->
          <a href="halamanBeasiswa.php" class="tombol-unduh mt-2">
            ← Kembali ke Daftar Beasiswa
          </a>

          <hr class="pemisah">

          <!-- Bagikan -->
          <p class="teks-bagikan">Bagikan beasiswa ini:</p>
          <div class="baris-bagikan">
            <a href="https://wa.me/?text=<?= urlencode('Cek beasiswa ini: ' . $beasiswa['nama_beasiswa'] . ' — ' . (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '')) ?>"
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
        alert('Link beasiswa berhasil disalin!');
      });
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
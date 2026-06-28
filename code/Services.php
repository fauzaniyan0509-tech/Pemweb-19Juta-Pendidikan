<?php
include 'penghubung.php';
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== "sudah_login") {
    header("Location: halamanLogin.php"); exit();
}
$foto_user = "foto-profil-user.jpg";
if (!empty($_SESSION['foto_profil'])) $foto_user = "uploads/" . $_SESSION['foto_profil'];

$jmlTempat   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM tempat_edukatif"))[0] ?? 0;
$jmlBeasiswa = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM beasiswa"))[0] ?? 0;
$jmlLomba    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM lomba"))[0] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Services – 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="landingpage.css">
  <style>
    :root { --biru: #148fcd; --teal: #35c7b6; --gelap: #0f2942; }
    body { background: #eef5f9; font-family: 'Poppins', sans-serif; }

    .navbar { background: #fff !important; box-shadow: 0 2px 16px rgba(20,143,205,.08); }
    .logo-text { color: var(--biru) !important; font-weight: 800; font-size: 1.4rem; text-decoration: none; }
    .nav-link { font-weight: 500; color: #374151 !important; font-size: 14px; }
    .nav-link.active, .nav-link:hover { color: var(--biru) !important; }

    /* HERO */
    .hero-services {
      background: linear-gradient(135deg, #0f2942 0%, #148fcd 55%, #35c7b6 100%);
      padding: 80px 0 100px; text-align: center; position: relative; overflow: hidden;
    }
    .hero-services::after {
      content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 70px;
      background: #eef5f9; clip-path: ellipse(55% 100% at 50% 100%);
    }
    .hero-services h1 { font-size: 2.6rem; font-weight: 900; color: white; margin-bottom: 14px; }
    .hero-services p  { font-size: 1rem; color: rgba(255,255,255,.88); max-width: 560px; margin: 0 auto; line-height: 1.8; }
    .hero-badge {
      display: inline-block; background: rgba(255,255,255,.15); color: white;
      border: 1px solid rgba(255,255,255,.3); border-radius: 999px;
      font-size: 12px; font-weight: 600; padding: 5px 16px; margin-bottom: 18px;
    }

    .label-biru {
      display: inline-block; background: #e0f2fe; color: var(--biru);
      font-size: 12px; font-weight: 700; padding: 4px 14px;
      border-radius: 999px; margin-bottom: 10px;
    }
    .s-judul { font-size: 1.65rem; font-weight: 800; color: var(--gelap); margin-bottom: 8px; }
    .s-sub   { color: #64748b; font-size: 14.5px; line-height: 1.8; }

    /* SERVICE CARDS */
    .svc-card {
      background: white; border-radius: 22px;
      box-shadow: 0 6px 28px rgba(20,143,205,.09);
      overflow: hidden; height: 100%;
      transition: transform .25s, box-shadow .25s;
    }
    .svc-card:hover { transform: translateY(-6px); box-shadow: 0 18px 44px rgba(20,143,205,.16); }
    .svc-card-body { padding: 32px 28px 24px; }
    .svc-icon-wrap {
      width: 64px; height: 64px; border-radius: 18px;
      display: flex; align-items: center; justify-content: center;
      font-size: 2rem; margin-bottom: 18px;
    }
    .svc-card h4 { font-size: 17px; font-weight: 800; color: var(--gelap); margin-bottom: 10px; }
    .svc-card p  { font-size: 13.5px; color: #64748b; line-height: 1.75; margin: 0; }
    .svc-footer {
      padding: 18px 28px; border-top: 1px solid #f1f5f9;
      display: flex; align-items: center; justify-content: space-between;
    }
    .svc-stat { font-size: 13px; color: #94a3b8; font-weight: 500; }
    .svc-stat strong { color: var(--biru); font-size: 15px; }
    .svc-btn {
      font-size: 13px; font-weight: 700; padding: 8px 18px;
      border-radius: 999px; text-decoration: none; display: inline-block;
      transition: opacity .2s, transform .2s;
    }
    .svc-btn:hover { opacity: .88; transform: translateX(3px); }

    /* FITUR PENDUKUNG */
    .detail-row {
      display: flex; gap: 20px; align-items: flex-start;
      padding: 22px 0; border-bottom: 1px solid #f1f5f9;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-icon {
      width: 48px; height: 48px; border-radius: 14px; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
    }
    .detail-body h6 { font-weight: 700; color: var(--gelap); font-size: 14.5px; margin-bottom: 4px; }
    .detail-body p  { font-size: 13px; color: #64748b; line-height: 1.7; margin: 0; }

    /* STEP */
    .step-card {
      background: white; border-radius: 20px; padding: 32px 20px;
      box-shadow: 0 4px 20px rgba(20,143,205,.08); height: 100%; text-align: center;
    }
    .step-num {
      width: 52px; height: 52px; border-radius: 50%;
      background: linear-gradient(135deg, var(--biru), var(--teal));
      color: white; font-weight: 900; font-size: 1.2rem;
      display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;
    }
    .step-card h6 { font-weight: 800; color: var(--gelap); margin-bottom: 8px; }
    .step-card p  { font-size: 13px; color: #64748b; line-height: 1.7; margin: 0; }

    /* FAQ */
    .faq-item {
      background: white; border-radius: 16px; padding: 20px 22px;
      box-shadow: 0 4px 16px rgba(20,143,205,.07); margin-bottom: 12px; cursor: pointer;
    }
    .faq-q { font-weight: 700; font-size: 14.5px; color: var(--gelap); display: flex; justify-content: space-between; align-items: center; }
    .faq-a { font-size: 13.5px; color: #64748b; line-height: 1.75; margin-top: 10px; display: none; }
    .faq-item.open .faq-a { display: block; }
    .faq-item.open .faq-chevron { transform: rotate(180deg); }
    .faq-chevron { transition: transform .2s; font-size: 12px; color: var(--biru); }

    /* CTA */
    .cta-box {
      background: linear-gradient(135deg, var(--gelap), #148fcd);
      border-radius: 24px; padding: 50px 40px; text-align: center; color: white;
    }
    .cta-box h2 { font-size: 1.7rem; font-weight: 800; margin-bottom: 12px; }
    .cta-box p  { opacity: .85; font-size: 14.5px; max-width: 480px; margin: 0 auto 28px; line-height: 1.8; }
    .cta-lnk {
      display: inline-block; background: white; color: var(--biru);
      font-weight: 700; font-size: 14px; padding: 12px 28px;
      border-radius: 999px; text-decoration: none; margin: 6px; transition: transform .2s;
    }
    .cta-lnk:hover { transform: translateY(-2px); color: var(--biru); }
    .cta-lnk.outline { background: transparent; color: white; border: 2px solid rgba(255,255,255,.5); }
    .cta-lnk.outline:hover { background: rgba(255,255,255,.1); color: white; }

    footer { background: var(--gelap); color: white; padding: 24px 0; text-align: center; }
    footer p { margin: 0; font-size: 13px; opacity: .6; }
    /* MODAL STYLES */
.modal-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(15, 41, 66, 0.75);
  backdrop-filter: blur(6px);
  z-index: 9999;
  align-items: center;
  justify-content: center;
  padding: 20px;
  animation: fadeIn 0.25s ease;
}

.modal-overlay.active {
  display: flex;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { 
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to { 
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-content {
  background: white;
  border-radius: 28px;
  padding: 40px 36px;
  max-width: 720px;
  width: 100%;
  position: relative;
  box-shadow: 0 25px 80px rgba(20, 143, 205, 0.25);
  animation: slideUp 0.35s ease;
}

.modal-close {
  position: absolute;
  top: 20px;
  right: 24px;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  border: none;
  background: #f1f5f9;
  color: #64748b;
  font-size: 28px;
  line-height: 1;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  background: #e2e8f0;
  color: var(--gelap);
  transform: rotate(90deg);
}

.modal-header {
  text-align: center;
  margin-bottom: 32px;
}

.modal-header h3 {
  font-size: 1.8rem;
  font-weight: 800;
  color: var(--gelap);
  margin-bottom: 8px;
}

.modal-header p {
  color: #64748b;
  font-size: 14.5px;
  margin: 0;
}

.modal-features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.modal-feature-card {
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 20px;
  padding: 28px 22px;
  text-align: center;
  text-decoration: none;
  transition: all 0.25s;
  cursor: pointer;
}

.modal-feature-card:hover {
  border-color: var(--biru);
  transform: translateY(-6px);
  box-shadow: 0 12px 32px rgba(20, 143, 205, 0.15);
}

.feature-icon {
  width: 72px;
  height: 72px;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.2rem;
  margin: 0 auto 16px;
}

.modal-feature-card h4 {
  font-size: 16px;
  font-weight: 800;
  color: var(--gelap);
  margin-bottom: 8px;
}

.modal-feature-card p {
  font-size: 13px;
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 14px;
}

.feature-link {
  display: inline-block;
  font-size: 13px;
  font-weight: 700;
  color: var(--biru);
  transition: gap 0.2s;
}

.modal-feature-card:hover .feature-link {
  gap: 6px;
}

@media (max-width: 768px) {
  .modal-content {
    padding: 32px 24px;
  }
  .modal-header h3 {
    font-size: 1.5rem;
  }
  .modal-features {
    grid-template-columns: 1fr;
  }
}
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg py-3 sticky-top">
  <div class="container">
    <a class="navbar-brand logo-text" href="beranda.php">19JutaPendidikan</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center gap-lg-4">
        <li class="nav-item"><a class="nav-link" href="beranda.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="tentangKami.php">About us</a></li>
        <li class="nav-item"><a class="nav-link active" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item mt-2 mt-lg-0">
          <a href="penghubung.php?aksi=logout" class="btn btn-danger px-3 py-2" style="border-radius:10px;">Logout</a>
        </li>
        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
          <a href="ProfilPengguna.php">
            <img src="<?= $foto_user ?>" alt="Profil" style="width:40px;height:40px;object-fit:cover;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.15);">
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero-services">
  <div class="container">
    <span class="hero-badge">⚙️ Layanan Platform</span>
    <h1>Semua yang kamu butuhkan<br>ada di sini</h1>
    <p>Dari mencari tempat belajar nyaman, beasiswa impian, hingga lomba bergengsi — 19JutaPendidikan menyediakan semuanya dalam satu platform.</p>
  </div>
</section>

<!-- 3 SERVICE CARDS UTAMA -->
<section class="py-5 mt-3">
  <div class="container">
    <div class="text-center mb-5">
      <span class="label-biru">🚀 Layanan Utama</span>
      <h2 class="s-judul">3 Fitur Inti Platform</h2>
      <p class="s-sub">Setiap fitur dirancang khusus untuk kebutuhan pelajar dan mahasiswa Indonesia.</p>
    </div>
    <div class="row g-4">

      <div class="col-md-4">
        <div class="svc-card">
          <div class="svc-card-body">
            <div class="svc-icon-wrap" style="background:#dbeafe;">🗺️</div>
            <h4>Peta Tempat Edukatif</h4>
            <p>Temukan tempat belajar terbaik di sekitarmu — kafe wifi, perpustakaan, ruang belajar, museum, dan lainnya — lengkap dengan info fasilitas, jam buka, rating, dan link Google Maps.</p>
          </div>
          <div class="svc-footer">
            <span class="svc-stat"><strong><?= $jmlTempat ?>+</strong> tempat terdaftar</span>
            <a href="halamanTempatEdukatif.php" class="svc-btn" style="background:#dbeafe; color:#1d4ed8;">Jelajahi →</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="svc-card">
          <div class="svc-card-body">
            <div class="svc-icon-wrap" style="background:#d1fae5;">🎓</div>
            <h4>Info Beasiswa</h4>
            <p>Direktori beasiswa lengkap dari skala lokal hingga internasional, gratis maupun berbayar. Diurutkan berdasarkan deadline supaya kamu tidak pernah kelewatan satu pun peluang.</p>
          </div>
          <div class="svc-footer">
            <span class="svc-stat"><strong><?= $jmlBeasiswa ?>+</strong> beasiswa tersedia</span>
            <a href="halamanBeasiswa.php" class="svc-btn" style="background:#d1fae5; color:#065f46;">Cari →</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="svc-card">
          <div class="svc-card-body">
            <div class="svc-icon-wrap" style="background:#fef3c7;">🏆</div>
            <h4>Info Lomba</h4>
            <p>Kumpulan info lomba dari berbagai kategori — sains, seni, esai, inovasi, dan olahraga. Tersedia untuk semua jenjang: SD, SMP, SMA, Mahasiswa, hingga Umum.</p>
          </div>
          <div class="svc-footer">
            <span class="svc-stat"><strong><?= $jmlLomba ?>+</strong> lomba terdaftar</span>
            <a href="halamanLomba.php" class="svc-btn" style="background:#fef3c7; color:#92400e;">Cari →</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- FITUR PENDUKUNG -->
<section class="py-5" style="background: white;">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-5">
        <span class="label-biru">✨ Fitur Pendukung</span>
        <h2 class="s-judul">Lebih dari sekadar direktori</h2>
        <p class="s-sub">Platform ini dilengkapi berbagai fitur pendukung yang membuat pengalaman mencari informasi pendidikan jadi lebih efisien dan menyenangkan.</p>
        <a href="PengajuanTempat.php" class="svc-btn mt-3 d-inline-block" style="background:linear-gradient(90deg,#148fcd,#35c7b6); color:white;">📍 Ajukan Tempat Sekarang</a>
      </div>
      <div class="col-lg-7">
        <div class="detail-row">
          <div class="detail-icon" style="background:#ede9fe;">📍</div>
          <div class="detail-body">
            <h6>Pengajuan Tempat oleh Pengguna</h6>
            <p>Kamu bisa berkontribusi dengan mendaftarkan tempat belajar yang belum ada. Setelah diverifikasi admin (1–3 hari kerja), tempat langsung tampil untuk semua pengguna.</p>
          </div>
        </div>
        <div class="detail-row">
          <div class="detail-icon" style="background:#fce7f3;">⭐</div>
          <div class="detail-body">
            <h6>Sistem Rating Tempat Edukatif</h6>
            <p>Setiap tempat punya rating yang dikurasi admin, membantu kamu memilih tempat belajar terbaik sesuai kebutuhan akademik tanpa perlu trial and error.</p>
          </div>
        </div>
        <div class="detail-row">
          <div class="detail-icon" style="background:#dcfce7;">🔍</div>
          <div class="detail-body">
            <h6>Filter & Pencarian Cerdas</h6>
            <p>Setiap halaman dilengkapi fitur pencarian dan filter — berdasarkan kategori, rating, jenjang, deadline — sehingga info yang kamu butuhkan ditemukan dalam hitungan detik.</p>
          </div>
        </div>
        <div class="detail-row">
          <div class="detail-icon" style="background:#fef9c3;">🔗</div>
          <div class="detail-body">
            <h6>Integrasi Google Maps</h6>
            <p>Setiap tempat edukatif dilengkapi tombol "Lihat di Google Maps" sehingga kamu langsung bisa mendapatkan rute ke sana tanpa perlu mencari manual.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <span class="label-biru">💡 Cara Kerja</span>
      <h2 class="s-judul">Mulai dalam 4 langkah mudah</h2>
    </div>
    <div class="row g-4">
      <div class="col-6 col-md-3">
        <div class="step-card">
          <div class="step-num">1</div>
          <h6>Daftar & Login</h6>
          <p>Buat akun gratis dan langsung akses semua fitur platform tanpa biaya apapun.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="step-card">
          <div class="step-num">2</div>
          <h6>Pilih Fitur</h6>
          <p>Cari tempat belajar, cek info beasiswa, atau browse lomba yang sesuai kebutuhanmu.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="step-card">
          <div class="step-num">3</div>
          <h6>Filter & Temukan</h6>
          <p>Gunakan pencarian dan filter untuk menemukan informasi paling relevan untukmu dengan cepat.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="step-card">
          <div class="step-num">4</div>
          <h6>Kontribusi</h6>
          <p>Bantu sesama dengan mengajukan tempat belajar baru yang belum terdaftar di platform.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="py-5" style="background: white;">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        <span class="label-biru">❓ FAQ</span>
        <h2 class="s-judul">Pertanyaan yang sering ditanya</h2>
        <p class="s-sub">Tidak menemukan jawaban yang kamu cari? Hubungi kami melalui halaman About Us.</p>
        <a href="tentangKami.php" class="svc-btn mt-3 d-inline-block" style="background:#e0f2fe; color:#148fcd;">Tentang Kami →</a>
      </div>
      <div class="col-lg-8">
        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-q">Apakah platform ini gratis? <span class="faq-chevron">▾</span></div>
          <div class="faq-a">Ya, sepenuhnya gratis! Semua fitur — mulai dari mencari tempat belajar, info beasiswa, info lomba, hingga pengajuan tempat baru — tidak dipungut biaya apapun.</div>
        </div>
        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-q">Bagaimana cara mengajukan tempat belajar baru? <span class="faq-chevron">▾</span></div>
          <div class="faq-a">Klik menu "Pengajuan Tempat" di halaman Tempat Edukatif, isi formulir dengan detail tempat (nama, kategori, fasilitas, sosial media, foto, dan link Google Maps), lalu kirim. Admin akan mereview dalam 1–3 hari kerja.</div>
        </div>
        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-q">Apakah info lomba dan beasiswa diperbarui secara rutin? <span class="faq-chevron">▾</span></div>
          <div class="faq-a">Ya, admin platform secara berkala menambahkan dan memperbarui info lomba dan beasiswa. Gunakan filter deadline untuk melihat yang masih aktif.</div>
        </div>
        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-q">Bagaimana rating tempat edukatif ditentukan? <span class="faq-chevron">▾</span></div>
          <div class="faq-a">Rating diberikan oleh admin platform berdasarkan verifikasi kondisi tempat — meliputi fasilitas yang tersedia, kenyamanan, dan kesesuaian untuk kegiatan belajar akademik.</div>
        </div>
        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-q">Apakah pengajuan tempat saya pasti diterima? <span class="faq-chevron">▾</span></div>
          <div class="faq-a">Tidak selalu. Admin akan memverifikasi apakah tempat yang diajukan sesuai kriteria platform. Pengajuan yang tidak memenuhi syarat akan ditolak dengan keterangan.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-5">
  <div class="container">
    <div class="cta-box">
      <h2>Siap mulai eksplorasi?</h2>
      <p>Temukan tempat belajar, beasiswa, dan lomba terbaik untukmu — semua gratis, semua di satu tempat.</p>
      <button onclick="openFiturModal()" class="cta-lnk">🚀 Jelajahi Semua Fitur</button>
      <a href="beranda.php" class="cta-lnk outline">🏠 Kembali ke Beranda</a>
    </div>
  </div>
</section>

<!-- MODAL FITUR UTAMA -->
<div id="fiturModal" class="modal-overlay" onclick="closeFiturModalOutside(event)">
  <div class="modal-content" onclick="event.stopPropagation()">
    <button class="modal-close" onclick="closeFiturModal()">×</button>
    
    <div class="modal-header">
      <h3>Pilih Fitur yang Ingin Dijelajahi</h3>
      <p>Temukan apa yang kamu butuhkan untuk meningkatkan kualitas belajarmu</p>
    </div>
    
    <div class="modal-features">
      <a href="halamanTempatEdukatif.php" class="modal-feature-card">
        <div class="feature-icon" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe);">🗺️</div>
        <h4>Peta Tempat Edukatif</h4>
        <p>Temukan tempat belajar nyaman dengan fasilitas lengkap di sekitarmu</p>
        <span class="feature-link">Jelajahi →</span>
      </a>
      
      <a href="halamanBeasiswa.php" class="modal-feature-card">
        <div class="feature-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0);">🎓</div>
        <h4>Info Beasiswa</h4>
        <p>Raih pendidikan lebih tinggi dengan beasiswa dari berbagai sumber</p>
        <span class="feature-link">Cari Beasiswa →</span>
      </a>
      
      <a href="halamanLomba.php" class="modal-feature-card">
        <div class="feature-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">🏆</div>
        <h4>Info Lomba</h4>
        <p>Tunjukkan prestasimu di berbagai kompetisi dan lomba bergengsi</p>
        <span class="feature-link">Lihat Lomba →</span>
      </a>
    </div>
  </div>
</div>

<footer>
  <p>© 2025 19JutaPendidikan — Kelompok 3 · Dibuat dengan ❤️ untuk pendidikan Indonesia.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Fungsi FAQ (yang lama - JANGAN DIHAPUS)
  function toggleFaq(el) {
    const isOpen = el.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(f => f.classList.remove('open'));
    if (!isOpen) el.classList.add('open');
  }
  
  // Fungsi Modal Fitur (yang baru)
  function openFiturModal() {
    document.getElementById('fiturModal').classList.add('active');
    document.body.style.overflow = 'hidden';
  }
  
  function closeFiturModal() {
    document.getElementById('fiturModal').classList.remove('active');
    document.body.style.overflow = '';
  }
  
  function closeFiturModalOutside(event) {
    if (event.target === document.getElementById('fiturModal')) {
      closeFiturModal();
    }
  }
  
  // Tutup modal dengan tombol ESC
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeFiturModal();
    }
  });
</script>
</body>
</html>
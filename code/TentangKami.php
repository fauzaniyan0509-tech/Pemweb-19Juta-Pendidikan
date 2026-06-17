<?php
include 'penghubung.php';
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== "sudah_login") {
    header("Location: halamanLogin.php"); exit();
}
$foto_user = "foto-profil-user.jpg";
if (!empty($_SESSION['foto_profil'])) $foto_user = "uploads/" . $_SESSION['foto_profil'];

// Statistik live dari database
$jmlTempat   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM tempat_edukatif"))[0] ?? 0;
$jmlBeasiswa = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM beasiswa"))[0] ?? 0;
$jmlLomba    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM lomba"))[0] ?? 0;
$jmlUser     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM user"))[0] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tentang Kami – 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="landingpage.css">
  <style>
    :root { --biru: #148fcd; --teal: #35c7b6; --gelap: #14213d; }
    body { background: #eef5f9; font-family: 'Poppins', sans-serif; }

    /* ── HERO ── */
    .hero-about {
      background: linear-gradient(135deg, #148fcd 0%, #35c7b6 100%);
      color: white; padding: 80px 0 60px; text-align: center;
    }
    .hero-about h1 { font-size: 2.6rem; font-weight: 800; margin-bottom: 14px; }
    .hero-about p  { font-size: 1.05rem; max-width: 620px; margin: 0 auto; opacity: .9; line-height: 1.8; }

    /* ── SECTION ── */
    .seksi { padding: 60px 0; }
    .seksi-judul {
      font-size: 1.6rem; font-weight: 800; color: var(--gelap);
      margin-bottom: 10px;
    }
    .seksi-sub { color: #64748b; font-size: 15px; line-height: 1.8; }
    .label-biru {
      display: inline-block; background: #e0f2fe; color: var(--biru);
      font-size: 12px; font-weight: 700; padding: 4px 12px;
      border-radius: 999px; margin-bottom: 12px; letter-spacing: .4px;
    }

    /* ── KENAPA ── */
    .kenapa-card {
      background: white; border-radius: 18px; padding: 28px 24px;
      box-shadow: 0 4px 20px rgba(20,143,205,.08);
      height: 100%; border-top: 4px solid var(--biru);
    }
    .kenapa-card .angka { font-size: 2.4rem; font-weight: 900; color: var(--biru); line-height: 1; margin-bottom: 8px; }
    .kenapa-card h5 { font-weight: 700; color: var(--gelap); font-size: 15px; }
    .kenapa-card p  { color: #64748b; font-size: 13.5px; line-height: 1.7; margin: 0; }

    /* ── STATISTIK ── */
    .stat-section { background: linear-gradient(135deg, var(--gelap) 0%, #1e3a5f 100%); color: white; padding: 60px 0; }
    .stat-card { text-align: center; }
    .stat-card .angka { font-size: 2.8rem; font-weight: 900; color: #35c7b6; }
    .stat-card .label { font-size: 14px; opacity: .8; margin-top: 4px; }

    /* ── KEUNGGULAN ── */
    .unggulan-card {
      background: white; border-radius: 18px; padding: 28px 24px;
      box-shadow: 0 4px 20px rgba(20,143,205,.08); height: 100%;
      transition: transform .2s;
    }
    .unggulan-card:hover { transform: translateY(-4px); }
    .unggulan-icon { font-size: 2.2rem; margin-bottom: 14px; }
    .unggulan-card h5 { font-weight: 700; color: var(--gelap); font-size: 15px; margin-bottom: 8px; }
    .unggulan-card p  { color: #64748b; font-size: 13.5px; line-height: 1.7; margin: 0; }

    /* ── SDGs ── */
    .sdg-card {
      background: white; border-radius: 18px; padding: 24px;
      box-shadow: 0 4px 20px rgba(20,143,205,.08); height: 100%;
      border-left: 5px solid;
    }
    .sdg-card.sdg4 { border-color: #e5243b; }
    .sdg-card.sdg9 { border-color: #fd6925; }
    .sdg-card .sdg-badge {
      display: inline-block; font-size: 12px; font-weight: 800;
      padding: 3px 10px; border-radius: 6px; margin-bottom: 10px;
      color: white;
    }
    .sdg-card.sdg4 .sdg-badge { background: #e5243b; }
    .sdg-card.sdg9 .sdg-badge { background: #fd6925; }
    .sdg-card h5 { font-weight: 700; color: var(--gelap); font-size: 15px; margin-bottom: 8px; }
    .sdg-card p  { color: #64748b; font-size: 13.5px; line-height: 1.7; margin: 0; }

    /* ── TIM ── */
    .tim-card {
      background: white; border-radius: 20px; padding: 30px 20px;
      text-align: center; box-shadow: 0 4px 20px rgba(20,143,205,.08);
      transition: transform .2s;
    }
    .tim-card:hover { transform: translateY(-5px); }
    .tim-avatar {
      width: 80px; height: 80px; border-radius: 50%;
      background: linear-gradient(135deg, #148fcd, #35c7b6);
      display: flex; align-items: center; justify-content: center;
      font-size: 2rem; margin: 0 auto 16px; color: white;
    }
    .tim-nama  { font-weight: 800; color: var(--gelap); font-size: 15px; margin-bottom: 4px; }
    .tim-nim   { font-size: 12px; color: #94a3b8; font-weight: 600; }
    .tim-role  { font-size: 12.5px; color: var(--biru); font-weight: 600; margin-top: 8px;
                 background: #e0f2fe; border-radius: 999px; padding: 3px 12px; display: inline-block; }
  </style>
</head>
<body>

<!-- NAVBAR (sama dengan beranda.php) -->
<nav class="navbar navbar-expand-lg py-3 sticky-top shadow-sm" style="background-color: #fff;">
  <div class="container">
    <a class="navbar-brand logo-text" href="beranda.php">19JutaPendidikan</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center gap-lg-4">
        <li class="nav-item"><a class="nav-link" href="beranda.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="tentangKami.php">About us</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item mt-3 mt-lg-0">
          <a href="penghubung.php?aksi=logout" class="btn btn-danger px-3 py-2">Logout</a>
        </li>
        <li class="nav-item ms-lg-2 mt-3 mt-lg-0">
          <a href="ProfilPengguna.php" class="profile-nav-link d-inline-block shadow-sm">
            <img src="<?= $foto_user ?>" alt="Profile" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero-about">
  <div class="container">
    <h1>Tentang 19JutaPendidikan</h1>
    <p>Platform digital yang memudahkan pelajar dan mahasiswa mengakses informasi pendidikan — lomba, beasiswa, dan tempat belajar — dalam satu tempat.</p>
  </div>
</section>

<!-- APA ITU 19 JUTA -->
<section class="seksi">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="label-biru">🎓 Tentang Platform</span>
        <h2 class="seksi-judul">Apa itu<br>19JutaPendidikan?</h2>
        <p class="seksi-sub">
          <strong>19JutaPendidikan</strong> adalah website yang membantu masyarakat mengakses informasi yang berkaitan dengan dunia pendidikan. Fitur utama yang disediakan adalah informasi beasiswa, informasi lomba, dan rekomendasi tempat edukatif.
        </p>
        <p class="seksi-sub mt-3">
          Dengan adanya website ini, diharapkan akses terhadap informasi pendidikan menjadi <strong>lebih mudah dan merata</strong> bagi seluruh pelajar di Indonesia.
        </p>
      </div>
      <div class="col-lg-6">
        <div class="row g-3">
          <div class="col-6">
            <div class="kenapa-card">
              <div class="angka">19</div>
              <h5>Tanggal Spesial</h5>
              <p>Angka 19 dipilih karena setiap tanggal 19 diadakan perlombaan pendidikan.</p>
            </div>
          </div>
          <div class="col-6">
            <div class="kenapa-card">
              <div class="angka">Juta</div>
              <h5>Jutaan Manfaat</h5>
              <p>Menggambarkan harapan website ini dapat memberikan jutaan manfaat bagi masyarakat.</p>
            </div>
          </div>
          <div class="col-12">
            <div class="kenapa-card" style="border-top-color: var(--teal);">
              <div class="angka" style="color:var(--teal);">Pendidikan</div>
              <h5>Fokus Utama</h5>
              <p>Kata "Pendidikan" menunjukkan fokus utama website ini: informasi pada bidang pendidikan yang terstruktur dan mudah diakses.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- STATISTIK LIVE -->
<section class="stat-section">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="angka"><?= $jmlTempat ?>+</div>
          <div class="label">Tempat Edukatif</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="angka"><?= $jmlBeasiswa ?>+</div>
          <div class="label">Info Beasiswa</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="angka"><?= $jmlLomba ?>+</div>
          <div class="label">Info Lomba</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="angka"><?= $jmlUser ?>+</div>
          <div class="label">Pengguna Terdaftar</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- KEUNGGULAN -->
<section class="seksi" style="background: white;">
  <div class="container">
    <div class="text-center mb-5">
      <span class="label-biru">✨ Keunggulan</span>
      <h2 class="seksi-judul">Kenapa pilih 19JutaPendidikan?</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-3">
        <div class="unggulan-card">
          <div class="unggulan-icon">⚡</div>
          <h5>Efisien</h5>
          <p>Mengintegrasikan tempat belajar, beasiswa, dan lomba dalam satu platform — tidak perlu buka banyak website.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="unggulan-card">
          <div class="unggulan-icon">🎯</div>
          <h5>Spesifik</h5>
          <p>Peta interaktif khusus tempat belajar, lengkap dengan info fasilitas seperti WiFi, colokan listrik, dan jam operasional.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="unggulan-card">
          <div class="unggulan-icon">⭐</div>
          <h5>Sistem Rating</h5>
          <p>Rating yang berfokus pada kebutuhan akademik mahasiswa — membantu memilih tempat belajar terbaik lebih cepat.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="unggulan-card">
          <div class="unggulan-icon">🧭</div>
          <h5>Terarah</h5>
          <p>Info beasiswa dan lomba diurutkan berdasarkan deadline dan relevan untuk mahasiswa di wilayah tertentu.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SDGs -->
<section class="seksi">
  <div class="container">
    <div class="text-center mb-5">
      <span class="label-biru">🌍 SDGs</span>
      <h2 class="seksi-judul">Keterkaitan dengan SDGs</h2>
      <p class="seksi-sub mx-auto" style="max-width:540px;">Website 19JutaPendidikan mendukung beberapa tujuan dalam <em>Sustainable Development Goals</em> (SDGs) PBB.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <div class="col-md-5">
        <div class="sdg-card sdg4">
          <span class="sdg-badge">SDG 4</span>
          <h5>Quality Education</h5>
          <p>Selaras dengan tujuan utama website: membantu menyediakan akses informasi pendidikan seperti lomba, beasiswa, dan tempat belajar yang kondusif bagi semua kalangan.</p>
        </div>
      </div>
      <div class="col-md-5">
        <div class="sdg-card sdg9">
          <span class="sdg-badge">SDG 9</span>
          <h5>Industry, Innovation & Infrastructure</h5>
          <p>Memanfaatkan teknologi digital melalui website untuk mempermudah masyarakat dalam mengakses informasi pendidikan secara merata dan modern.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TIM -->
<section class="seksi" style="background: white;">
  <div class="container">
    <div class="text-center mb-5">
      <span class="label-biru">👥 Meet the Team</span>
      <h2 class="seksi-judul">Tim Pengembang</h2>
      <p class="seksi-sub">Kelompok 3 — Pemrograman Web</p>
    </div>
    <div class="row g-4 justify-content-center">

      <div class="col-6 col-md-3">
        <div class="tim-card">
          <div class="tim-avatar">🌸</div>
          <div class="tim-nama">Risti Maya Fajri</div>
          <div class="tim-nim">V3425081</div>
          <div class="tim-role">Ketua Tim</div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="tim-card">
          <div class="tim-avatar">💡</div>
          <div class="tim-nama">Prima Hadi R.</div>
          <div class="tim-nim">V3425036</div>
          <div class="tim-role">Backend Dev</div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="tim-card">
          <div class="tim-avatar">🎨</div>
          <div class="tim-nama">Nisa Ayu Khoiri A.</div>
          <div class="tim-nim">V3425078</div>
          <div class="tim-role">UI / Frontend</div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="tim-card">
          <div class="tim-avatar">🛠️</div>
          <div class="tim-nama">Mukhammad Fauzan</div>
          <div class="tim-nim">V3425074</div>
          <div class="tim-role">Full Stack</div>
        </div>
      </div>

    </div>

    <div class="text-center mt-4">
      <p class="seksi-sub" style="font-size:13px;">
        📚 Mata Kuliah Pemrograman Web &nbsp;·&nbsp; Universitas Sebelas Maret &nbsp;·&nbsp; 2025
      </p>
    </div>
  </div>
</section>

<!-- FOOTER SEDERHANA -->
<footer style="background: var(--gelap); color: white; padding: 24px 0; text-align:center;">
  <p style="margin:0; font-size:13px; opacity:.7;">© 2025 19JutaPendidikan — Kelompok 3. Dibuat dengan ❤️ untuk dunia pendidikan Indonesia.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
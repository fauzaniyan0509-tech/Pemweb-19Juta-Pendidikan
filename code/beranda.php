<?php
include 'penghubung.php';
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== "sudah_login") {
    header("Location: halamanLogin.php"); exit();
}
$foto_user = "foto-profil-user.jpg";
if (!empty($_SESSION['foto_profil'])) $foto_user = "uploads/" . $_SESSION['foto_profil'];
$nama_user = htmlspecialchars($_SESSION['nama'] ?? 'Pengguna');

// Ambil fitur dari database
$qFitur = mysqli_query($conn, "SELECT * FROM fitur_beranda WHERE aktif = 1 ORDER BY urutan ASC");

// Statistik live
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
  <title>Beranda – 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="landingpage.css">
  <style>
    :root { --biru: #148fcd; --teal: #35c7b6; --gelap: #0f2942; }
    body { background: #f0f7fb; font-family: 'Poppins', sans-serif; }

    /* ── NAVBAR ── */
    .navbar { background: #fff !important; box-shadow: 0 2px 16px rgba(20,143,205,.08); }
    .logo-text { color: var(--biru) !important; font-weight: 800; font-size: 1.4rem; text-decoration: none; }
    .nav-link { font-weight: 500; color: #374151 !important; font-size: 14px; }
    .nav-link.active, .nav-link:hover { color: var(--biru) !important; }

    /* ── HERO ── */
    .hero {
      background: linear-gradient(135deg, #0f2942 0%, #148fcd 60%, #35c7b6 100%);
      padding: 90px 0 120px; position: relative; overflow: hidden;
    }
    .hero::after {
      content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 80px;
      background: #f0f7fb;
      clip-path: ellipse(55% 100% at 50% 100%);
    }
    .hero-badge {
      display: inline-block; background: rgba(255,255,255,.15); color: white;
      border: 1px solid rgba(255,255,255,.3); border-radius: 999px;
      font-size: 12px; font-weight: 600; padding: 5px 16px; margin-bottom: 20px;
      backdrop-filter: blur(4px);
    }
    .hero h1 {
      font-size: clamp(1.8rem, 4vw, 3rem); font-weight: 900; color: white;
      line-height: 1.2; margin-bottom: 16px;
    }
    .hero h1 span { color: #7dd3fc; }
    .hero p { color: rgba(255,255,255,.85); font-size: 1rem; line-height: 1.8; max-width: 520px; }
    .hero-orb {
      position: absolute; border-radius: 50%;
      background: rgba(255,255,255,.05); pointer-events: none;
    }
    .orb1 { width: 300px; height: 300px; top: -80px; right: 5%; }
    .orb2 { width: 180px; height: 180px; bottom: 80px; right: 20%; }
    .hero-emoji-grid {
      display: grid; grid-template-columns: repeat(3,1fr); gap: 14px;
      max-width: 280px; margin: 0 auto;
    }
    .hero-emoji-card {
      background: rgba(255,255,255,.12); backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,.2); border-radius: 16px;
      padding: 18px 10px; text-align: center;
      transition: transform .2s;
    }
    .hero-emoji-card:hover { transform: translateY(-4px); }
    .hero-emoji-card .emo { font-size: 1.8rem; display: block; margin-bottom: 6px; }
    .hero-emoji-card .lbl { color: rgba(255,255,255,.85); font-size: 10.5px; font-weight: 600; }

    /* ── STAT STRIP ── */
    .stat-strip {
      background: white; border-radius: 20px; padding: 28px 32px;
      box-shadow: 0 8px 40px rgba(20,143,205,.12);
      margin-top: -40px; position: relative; z-index: 10;
    }
    .stat-item { text-align: center; }
    .stat-item .num { font-size: 2rem; font-weight: 900; color: var(--biru); line-height: 1; }
    .stat-item .lbl { font-size: 12px; color: #64748b; margin-top: 4px; font-weight: 500; }
    .stat-divider { width: 1px; background: #e2e8f0; align-self: stretch; }

    /* ── SECTION TITLE ── */
    .s-tag {
      display: inline-block; background: #dbeafe; color: var(--biru);
      font-size: 12px; font-weight: 700; padding: 4px 14px;
      border-radius: 999px; margin-bottom: 10px;
    }
    .s-judul { font-size: 1.7rem; font-weight: 800; color: var(--gelap); margin-bottom: 8px; }
    .s-sub { color: #64748b; font-size: 14.5px; }

    /* ── FITUR CARDS ── */
    .fitur-card {
      background: white; border-radius: 20px;
      box-shadow: 0 4px 24px rgba(20,143,205,.08);
      overflow: hidden; transition: transform .25s, box-shadow .25s;
      text-decoration: none; color: inherit; display: block; height: 100%;
    }
    .fitur-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(20,143,205,.16); }
    .fitur-card-top {
      height: 130px; display: flex; align-items: center; justify-content: center;
      font-size: 3.5rem; position: relative;
    }
    .fitur-card-body { padding: 22px 22px 24px; }
    .fitur-card h4 { font-size: 16px; font-weight: 800; color: var(--gelap); margin-bottom: 8px; }
    .fitur-card p  { font-size: 13.5px; color: #64748b; line-height: 1.7; margin: 0; }
    .fitur-arrow {
      display: inline-flex; align-items: center; gap: 6px;
      font-size: 13px; font-weight: 700; margin-top: 14px;
      transition: gap .2s;
    }
    .fitur-card:hover .fitur-arrow { gap: 10px; }

    /* ── CARA PAKAI ── */
    .cara-step { display: flex; gap: 18px; align-items: flex-start; padding: 20px 0; }
    .cara-step + .cara-step { border-top: 1px solid #e2e8f0; }
    .step-num {
      flex-shrink: 0; width: 42px; height: 42px; border-radius: 50%;
      background: linear-gradient(135deg, var(--biru), var(--teal));
      color: white; font-weight: 800; font-size: 16px;
      display: flex; align-items: center; justify-content: center;
    }
    .step-body h6 { font-weight: 700; color: var(--gelap); margin-bottom: 4px; font-size: 14.5px; }
    .step-body p  { font-size: 13px; color: #64748b; margin: 0; line-height: 1.7; }

    /* ── CTA BANNER ── */
    .cta-banner {
      background: linear-gradient(135deg, var(--gelap) 0%, #1a4a7a 50%, #148fcd 100%);
      border-radius: 24px; padding: 50px 40px; text-align: center; color: white;
      position: relative; overflow: hidden;
    }
    .cta-banner::before {
      content: '🎓'; position: absolute; font-size: 120px; opacity: .06;
      top: -20px; left: -20px; transform: rotate(-15deg);
    }
    .cta-banner::after {
      content: '🏆'; position: absolute; font-size: 120px; opacity: .06;
      bottom: -20px; right: -20px; transform: rotate(15deg);
    }
    .cta-banner h2 { font-size: 1.7rem; font-weight: 800; margin-bottom: 12px; }
    .cta-banner p  { opacity: .85; font-size: 14.5px; max-width: 500px; margin: 0 auto 28px; line-height: 1.8; }
    .cta-btn {
      display: inline-block; background: white; color: var(--biru);
      font-weight: 700; font-size: 14px; padding: 12px 28px;
      border-radius: 999px; text-decoration: none; transition: transform .2s, box-shadow .2s;
      margin: 6px;
    }
    .cta-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.2); }
    .cta-btn.outline {
      background: transparent; color: white;
      border: 2px solid rgba(255,255,255,.5);
    }
    .cta-btn.outline:hover { background: rgba(255,255,255,.1); }

    /* ── FOOTER ── */
    footer { background: var(--gelap); color: white; padding: 24px 0; text-align: center; }
    footer p { margin: 0; font-size: 13px; opacity: .6; }
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
        <li class="nav-item"><a class="nav-link active" href="beranda.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="tentangKami.php">About us</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
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
<section class="hero">
  <div class="hero-orb orb1"></div>
  <div class="hero-orb orb2"></div>
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="hero-badge">✨ Platform Pendidikan Digital</span>
        <h1>Halo, <span><?= $nama_user ?>!</span><br>Temukan peluang<br>terbaik untuk belajar.</h1>
        <p>Cari tempat belajar nyaman, info beasiswa, hingga lomba bergengsi — semuanya ada di satu platform.</p>
        <div class="mt-4 d-flex gap-3 flex-wrap">
          <a href="halamanTempatEdukatif.php" class="cta-btn" style="color:var(--biru);">🗺️ Jelajahi Tempat</a>
          <a href="tentangKami.php" class="cta-btn outline">Tentang Kami →</a>
        </div>
      </div>
      <div class="col-lg-6 d-none d-lg-block">
        <div class="hero-emoji-grid">
          <div class="hero-emoji-card"><span class="emo">🗺️</span><span class="lbl">Peta Edu</span></div>
          <div class="hero-emoji-card"><span class="emo">🎓</span><span class="lbl">Beasiswa</span></div>
          <div class="hero-emoji-card"><span class="emo">🏆</span><span class="lbl">Lomba</span></div>
          <div class="hero-emoji-card"><span class="emo">⭐</span><span class="lbl">Rating</span></div>
          <div class="hero-emoji-card"><span class="emo">📍</span><span class="lbl">Lokasi</span></div>
          <div class="hero-emoji-card"><span class="emo">🔍</span><span class="lbl">Cari Info</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- STAT STRIP -->
<section class="py-0">
  <div class="container">
    <div class="stat-strip d-flex justify-content-around align-items-center flex-wrap gap-3">
      <div class="stat-item">
        <div class="num"><?= $jmlTempat ?>+</div>
        <div class="lbl">🗺️ Tempat Edukatif</div>
      </div>
      <div class="stat-divider d-none d-md-block"></div>
      <div class="stat-item">
        <div class="num"><?= $jmlBeasiswa ?>+</div>
        <div class="lbl">🎓 Info Beasiswa</div>
      </div>
      <div class="stat-divider d-none d-md-block"></div>
      <div class="stat-item">
        <div class="num"><?= $jmlLomba ?>+</div>
        <div class="lbl">🏆 Info Lomba</div>
      </div>
      <div class="stat-divider d-none d-md-block"></div>
      <div class="stat-item">
        <div class="num"><?= $jmlUser ?>+</div>
        <div class="lbl">👥 Pengguna</div>
      </div>
    </div>
  </div>
</section>

<!-- FITUR DARI DATABASE -->
<section class="py-5 mt-4">
  <div class="container">
    <div class="text-center mb-5">
      <span class="s-tag">🚀 Fitur Utama</span>
      <h2 class="s-judul">Pilih fitur yang kamu butuhkan</h2>
      <p class="s-sub">Platform kami terus berkembang — fitur baru bisa ditambahkan kapan saja.</p>
    </div>

    <div class="row g-4">
      <?php
      $warna_gelap = ['#0f2942','#0d5c4e','#78350f','#4c1d95','#7f1d1d','#1e3a5f'];
      $i = 0;
      while ($f = mysqli_fetch_assoc($qFitur)):
        $bg = $warna_gelap[$i % count($warna_gelap)];
      ?>
      <div class="col-md-6 col-lg-4">
        <a href="<?= htmlspecialchars($f['link_url']) ?>" class="fitur-card">
          <div class="fitur-card-top" style="background: linear-gradient(135deg, <?= $bg ?>, <?= htmlspecialchars($f['warna_aksen']) ?>);">
            <?= htmlspecialchars($f['ikon_emoji']) ?>
          </div>
          <div class="fitur-card-body">
            <h4><?= htmlspecialchars($f['judul']) ?></h4>
            <p><?= htmlspecialchars($f['deskripsi']) ?></p>
            <div class="fitur-arrow" style="color:<?= htmlspecialchars($f['warna_aksen']) ?>">
              Buka fitur <span>→</span>
            </div>
          </div>
        </a>
      </div>
      <?php $i++; endwhile; ?>

      <?php if (mysqli_num_rows(mysqli_query($conn, "SELECT id_fitur FROM fitur_beranda WHERE aktif=1")) == 0): ?>
      <div class="col-12 text-center py-5">
        <p class="text-muted">Belum ada fitur yang ditambahkan. Admin dapat menambahkan fitur baru melalui dashboard.</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- CARA PAKAI -->
<section class="py-5" style="background: white;">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-5">
        <span class="s-tag">💡 Cara Pakai</span>
        <h2 class="s-judul">Mulai dalam 3 langkah mudah</h2>
        <p class="s-sub">Tidak perlu bingung, platform ini dirancang sesederhana mungkin untuk semua kalangan pelajar.</p>
      </div>
      <div class="col-lg-7">
        <div class="cara-step">
          <div class="step-num">1</div>
          <div class="step-body">
            <h6>Pilih fitur yang kamu butuhkan</h6>
            <p>Kamu mau cari tempat belajar, beasiswa, atau lomba? Pilih kartunya di halaman utama ini dan kamu langsung diarahkan ke halaman yang tepat.</p>
          </div>
        </div>
        <div class="cara-step">
          <div class="step-num">2</div>
          <div class="step-body">
            <h6>Gunakan filter & pencarian</h6>
            <p>Di setiap halaman ada fitur pencarian dan filter — berdasarkan kategori, rating, deadline, dan lain-lain — supaya kamu cepat menemukan yang paling relevan.</p>
          </div>
        </div>
        <div class="cara-step">
          <div class="step-num">3</div>
          <div class="step-body">
            <h6>Kontribusi & bagikan</h6>
            <p>Tahu tempat belajar yang oke tapi belum ada di platform? Ajukan lewat menu Pengajuan Tempat. Setelah diverifikasi admin, langsung tampil untuk semua.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA BANNER -->
<section class="py-5">
  <div class="container">
    <div class="cta-banner">
      <h2>Punya info tempat belajar yang belum terdaftar?</h2>
      <p>Bantu sesama pelajar dengan mengajukan tempat edukatif favoritmu. Proses review cepat — 1–3 hari kerja!</p>
      <a href="halamanTempatEdukatif.php" class="cta-btn">🗺️ Lihat Peta Edukatif</a>
      <a href="PengajuanTempat.php" class="cta-btn outline">📍 Ajukan Tempat</a>
    </div>
  </div>
</section>

<footer>
  <p>© 2025 19JutaPendidikan — Kelompok 3 · Dibuat dengan ❤️ untuk pendidikan Indonesia.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
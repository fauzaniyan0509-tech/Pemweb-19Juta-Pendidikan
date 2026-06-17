<?php
include 'penghubung.php';
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== "sudah_login") {
    header("Location: halamanLogin.php"); exit();
}
$foto_user = "foto-profil-user.jpg";
if (!empty($_SESSION['foto_profil'])) $foto_user = "uploads/" . $_SESSION['foto_profil'];

// Filter kategori
$kategoriAktif = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, trim($_GET['cari'])) : '';

$where = "WHERE aktif = 1";
if ($kategoriAktif) $where .= " AND kategori = '$kategoriAktif'";
if ($cari) $where .= " AND (judul LIKE '%$cari%' OR ringkasan LIKE '%$cari%')";

$qBlog    = mysqli_query($conn, "SELECT * FROM blog $where ORDER BY dibuat_pada DESC");
$qFeatured = mysqli_query($conn, "SELECT * FROM blog WHERE aktif = 1 ORDER BY dibuat_pada DESC LIMIT 1");
$featured  = mysqli_fetch_assoc($qFeatured);

$qKategori = mysqli_query($conn, "SELECT DISTINCT kategori FROM blog WHERE aktif = 1 ORDER BY kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blog – 19JutaPendidikan</title>
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
    .hero-blog {
      background: linear-gradient(135deg, #0f2942 0%, #148fcd 55%, #35c7b6 100%);
      padding: 60px 0 90px; position: relative; overflow: hidden;
    }
    .hero-blog::after {
      content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 70px;
      background: #eef5f9; clip-path: ellipse(55% 100% at 50% 100%);
    }
    .hero-badge {
      display: inline-block; background: rgba(255,255,255,.15); color: white;
      border: 1px solid rgba(255,255,255,.3); border-radius: 999px;
      font-size: 12px; font-weight: 600; padding: 5px 16px; margin-bottom: 16px;
    }
    .hero-blog h1 { font-size: 2.4rem; font-weight: 900; color: white; margin-bottom: 10px; }
    .hero-blog p  { color: rgba(255,255,255,.85); font-size: 15px; max-width: 500px; line-height: 1.8; }

    /* FEATURED */
    .featured-card {
      background: white; border-radius: 22px;
      box-shadow: 0 8px 36px rgba(20,143,205,.12);
      overflow: hidden; text-decoration: none; color: inherit; display: block;
      transition: transform .25s, box-shadow .25s;
    }
    .featured-card:hover { transform: translateY(-4px); box-shadow: 0 18px 48px rgba(20,143,205,.18); }
    .featured-header {
      background: linear-gradient(135deg, var(--gelap), var(--biru));
      padding: 40px 36px; color: white;
    }
    .featured-badge {
      background: rgba(255,255,255,.2); color: white; border-radius: 999px;
      font-size: 11px; font-weight: 700; padding: 4px 12px; margin-bottom: 16px; display: inline-block;
    }
    .featured-header h2 { font-size: 1.5rem; font-weight: 800; margin-bottom: 10px; line-height: 1.4; }
    .featured-header p  { font-size: 14px; opacity: .85; line-height: 1.75; margin: 0; }
    .featured-footer {
      padding: 20px 36px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
    }
    .featured-meta { font-size: 13px; color: #94a3b8; }
    .featured-meta strong { color: var(--biru); }
    .read-btn {
      background: linear-gradient(90deg, var(--biru), var(--teal));
      color: white; border-radius: 999px; font-size: 13px; font-weight: 700;
      padding: 9px 22px; text-decoration: none; transition: opacity .2s;
    }
    .read-btn:hover { opacity: .88; color: white; }

    /* FILTER */
    .filter-bar { background: white; border-radius: 18px; padding: 20px 24px; box-shadow: 0 4px 20px rgba(20,143,205,.07); margin-bottom: 24px; }
    .search-input {
      border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px 16px;
      font-size: 14px; width: 100%; font-family: 'Poppins', sans-serif;
    }
    .search-input:focus { outline: none; border-color: var(--biru); box-shadow: 0 0 0 3px rgba(20,143,205,.1); }
    .kat-btn {
      border: 1.5px solid #e2e8f0; background: white; border-radius: 999px;
      font-size: 12.5px; font-weight: 600; padding: 7px 16px; cursor: pointer;
      text-decoration: none; color: #374151; transition: all .2s;
    }
    .kat-btn:hover, .kat-btn.aktif { background: var(--biru); color: white; border-color: var(--biru); }

    /* BLOG CARDS */
    .blog-card {
      background: white; border-radius: 20px;
      box-shadow: 0 4px 20px rgba(20,143,205,.08); overflow: hidden;
      text-decoration: none; color: inherit; display: block; height: 100%;
      transition: transform .22s, box-shadow .22s;
    }
    .blog-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(20,143,205,.15); }
    .blog-card-top {
      height: 110px; display: flex; align-items: center; justify-content: center;
      font-size: 3rem;
    }
    .blog-card-body { padding: 20px 22px 22px; }
    .blog-kat {
      font-size: 11px; font-weight: 700; border-radius: 999px; padding: 3px 12px;
      background: #e0f2fe; color: var(--biru); margin-bottom: 10px; display: inline-block;
    }
    .blog-card h5 { font-size: 15px; font-weight: 800; color: var(--gelap); margin-bottom: 8px; line-height: 1.5; }
    .blog-card p  { font-size: 13px; color: #64748b; line-height: 1.7; margin: 0; }
    .blog-meta  { font-size: 12px; color: #94a3b8; margin-top: 14px; display: flex; align-items: center; gap: 6px; }

    /* EMPTY */
    .empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 20px; }
    .empty-state .emo { font-size: 56px; margin-bottom: 14px; }

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
        <li class="nav-item"><a class="nav-link" href="beranda.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="tentangKami.php">About us</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link active" href="blog.php">Blog</a></li>
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
<section class="hero-blog">
  <div class="container">
    <span class="hero-badge">📝 Blog & Artikel</span>
    <h1>Tips, Panduan &<br>Update Platform</h1>
    <p>Artikel seputar tips beasiswa, panduan lomba, dan update fitur terbaru 19JutaPendidikan.</p>
  </div>
</section>

<section class="py-5">
  <div class="container">

    <!-- FEATURED ARTIKEL -->
    <?php if ($featured && !$cari && !$kategoriAktif): ?>
    <div class="mb-5">
      <div class="d-flex align-items-center gap-2 mb-3">
        <span style="font-size:13px; font-weight:700; color:var(--biru);">⭐ Artikel Terbaru</span>
      </div>
      <a href="detailBlog.php?slug=<?= urlencode($featured['slug']) ?>" class="featured-card">
        <div class="featured-header">
          <span class="featured-badge"><?= $featured['ikon_emoji'] ?> <?= htmlspecialchars($featured['kategori']) ?></span>
          <h2><?= htmlspecialchars($featured['judul']) ?></h2>
          <p><?= htmlspecialchars($featured['ringkasan'] ?: mb_strimwidth($featured['isi'], 0, 180, '...')) ?></p>
        </div>
        <div class="featured-footer">
          <span class="featured-meta">✍️ <strong><?= htmlspecialchars($featured['penulis']) ?></strong> &nbsp;·&nbsp; <?= date('d M Y', strtotime($featured['dibuat_pada'])) ?></span>
          <span class="read-btn">Baca Selengkapnya →</span>
        </div>
      </a>
    </div>
    <?php endif; ?>

    <div class="row g-4">
      <!-- KONTEN KIRI -->
      <div class="col-lg-8">

        <!-- FILTER -->
        <div class="filter-bar d-flex flex-wrap gap-2 align-items-center justify-content-between">
          <form method="GET" action="" class="d-flex gap-2 flex-grow-1" style="max-width:340px;">
            <?php if ($kategoriAktif): ?><input type="hidden" name="kategori" value="<?= htmlspecialchars($kategoriAktif) ?>"><?php endif; ?>
            <input type="text" name="cari" class="search-input" placeholder="🔍 Cari artikel..." value="<?= htmlspecialchars($cari) ?>">
            <button type="submit" class="read-btn" style="white-space:nowrap;">Cari</button>
          </form>
          <?php if ($cari || $kategoriAktif): ?>
          <a href="blog.php" class="kat-btn">✕ Reset</a>
          <?php endif; ?>
        </div>

        <!-- DAFTAR ARTIKEL -->
        <?php
        $ada = false;
        $warnaBg = ['#dbeafe','#d1fae5','#fef3c7','#ede9fe','#fce7f3','#dcfce7'];
        $i = 0;
        $rows = [];
        while ($b = mysqli_fetch_assoc($qBlog)) $rows[] = $b;

        // Kalau ada featured dan tidak filter, skip artikel pertama (sudah ditampilkan di featured)
        if ($featured && !$cari && !$kategoriAktif && count($rows) > 0) {
            if ($rows[0]['id_blog'] == $featured['id_blog']) array_shift($rows);
        }

        if (count($rows) === 0):
        ?>
        <div class="empty-state">
          <div class="emo">📭</div>
          <h5>Artikel tidak ditemukan</h5>
          <p style="color:#64748b; font-size:14px;">Coba kata kunci lain atau reset filter.</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
          <?php foreach ($rows as $b): $ada = true; ?>
          <div class="col-md-6">
            <a href="detailBlog.php?slug=<?= urlencode($b['slug']) ?>" class="blog-card">
              <div class="blog-card-top" style="background:<?= $warnaBg[$i % count($warnaBg)] ?>;">
                <?= htmlspecialchars($b['ikon_emoji']) ?>
              </div>
              <div class="blog-card-body">
                <span class="blog-kat"><?= htmlspecialchars($b['kategori']) ?></span>
                <h5><?= htmlspecialchars($b['judul']) ?></h5>
                <p><?= htmlspecialchars(mb_strimwidth($b['ringkasan'] ?: $b['isi'], 0, 100, '...')) ?></p>
                <div class="blog-meta">
                  ✍️ <?= htmlspecialchars($b['penulis']) ?> &nbsp;·&nbsp; <?= date('d M Y', strtotime($b['dibuat_pada'])) ?>
                </div>
              </div>
            </a>
          </div>
          <?php $i++; endforeach; ?>
        </div>
        <?php endif; ?>

      </div>

      <!-- SIDEBAR KANAN -->
      <div class="col-lg-4">

        <!-- KATEGORI -->
        <div style="background:white; border-radius:18px; padding:22px; box-shadow:0 4px 20px rgba(20,143,205,.07); margin-bottom:20px;">
          <h6 style="font-weight:800; color:var(--gelap); margin-bottom:16px;">📂 Kategori</h6>
          <div class="d-flex flex-wrap gap-2">
            <a href="blog.php" class="kat-btn <?= !$kategoriAktif ? 'aktif' : '' ?>">Semua</a>
            <?php
            mysqli_data_seek($qKategori, 0);
            while ($k = mysqli_fetch_assoc($qKategori)):
            ?>
            <a href="?kategori=<?= urlencode($k['kategori']) ?>" class="kat-btn <?= $kategoriAktif === $k['kategori'] ? 'aktif' : '' ?>">
              <?= htmlspecialchars($k['kategori']) ?>
            </a>
            <?php endwhile; ?>
          </div>
        </div>

        <!-- TENTANG BLOG -->
        <div style="background:linear-gradient(135deg,var(--gelap),var(--biru)); border-radius:18px; padding:24px; color:white;">
          <div style="font-size:2rem; margin-bottom:12px;">📝</div>
          <h6 style="font-weight:800; margin-bottom:8px;">Tentang Blog Ini</h6>
          <p style="font-size:13px; opacity:.85; line-height:1.75; margin:0 0 16px;">Blog ini berisi tips & panduan seputar beasiswa, lomba, belajar efektif, serta update fitur terbaru platform 19JutaPendidikan.</p>
          <a href="beranda.php" style="background:rgba(255,255,255,.2); color:white; border-radius:999px; font-size:12.5px; font-weight:700; padding:8px 16px; text-decoration:none;">🏠 Ke Beranda</a>
        </div>

      </div>
    </div>
  </div>
</section>

<footer>
  <p>© 2025 19JutaPendidikan — Kelompok 3 · Dibuat dengan ❤️ untuk pendidikan Indonesia.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
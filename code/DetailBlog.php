<?php
include 'penghubung.php';
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== "sudah_login") {
    header("Location: halamanLogin.php"); exit();
}
$foto_user = "foto-profil-user.jpg";
if (!empty($_SESSION['foto_profil'])) $foto_user = "uploads/" . $_SESSION['foto_profil'];

$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';
$qArtikel = mysqli_query($conn, "SELECT * FROM blog WHERE slug = '$slug' AND aktif = 1 LIMIT 1");
$artikel  = mysqli_fetch_assoc($qArtikel);
if (!$artikel) { header("Location: blog.php"); exit(); }

// Artikel lain (rekomendasi)
$qLain = mysqli_query($conn, "SELECT id_blog, judul, slug, kategori, ikon_emoji, penulis, dibuat_pada, ringkasan
    FROM blog WHERE aktif = 1 AND slug != '$slug' ORDER BY dibuat_pada DESC LIMIT 3");

// Render isi artikel: **bold**, baris kosong = <br>, numbered list
function renderIsi($teks) {
    $teks = htmlspecialchars($teks);
    $teks = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $teks);
    $baris = explode("\n", $teks);
    $html = '';
    foreach ($baris as $b) {
        $b = trim($b);
        if ($b === '') { $html .= '<br>'; continue; }
        if (preg_match('/^\d+\.\s/', $b)) {
            $html .= '<p style="margin:0 0 6px; padding-left:4px;">🔹 ' . preg_replace('/^\d+\.\s/', '', $b) . '</p>';
        } else {
            $html .= '<p style="margin:0 0 8px;">' . $b . '</p>';
        }
    }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($artikel['judul']) ?> – Blog 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="landingpage.css">
  <style>
    :root { --biru: #148fcd; --teal: #35c7b6; --gelap: #0f2942; }
    body { background: #eef5f9; font-family: 'Poppins', sans-serif; }
    .navbar { background: #fff !important; box-shadow: 0 2px 16px rgba(20,143,205,.08); }
    .logo-text { color: var(--biru) !important; font-weight: 800; font-size: 1.4rem; text-decoration: none; }
    .nav-link { font-weight: 500; color: #374151 !important; font-size: 14px; }
    .nav-link.active, .nav-link:hover { color: var(--biru) !important; }

    .artikel-hero {
      background: linear-gradient(135deg, #0f2942 0%, #148fcd 60%, #35c7b6 100%);
      padding: 60px 0 80px; position: relative; overflow: hidden;
    }
    .artikel-hero::after {
      content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 60px;
      background: #eef5f9; clip-path: ellipse(55% 100% at 50% 100%);
    }
    .kat-badge {
      display: inline-block; background: rgba(255,255,255,.2); color: white;
      border-radius: 999px; font-size: 12px; font-weight: 700;
      padding: 5px 14px; margin-bottom: 14px;
    }
    .artikel-hero h1 { font-size: clamp(1.5rem, 4vw, 2.2rem); font-weight: 900; color: white; line-height: 1.3; margin-bottom: 14px; }
    .artikel-meta { font-size: 13px; color: rgba(255,255,255,.8); }
    .artikel-meta strong { color: white; }

    .artikel-body {
      background: white; border-radius: 22px;
      box-shadow: 0 6px 30px rgba(20,143,205,.09);
      padding: 36px 40px; font-size: 15px; line-height: 1.9; color: #374151;
    }
    @media(max-width: 576px) { .artikel-body { padding: 24px 20px; } }

    .rec-card {
      background: white; border-radius: 18px;
      box-shadow: 0 4px 18px rgba(20,143,205,.08);
      padding: 18px; text-decoration: none; color: inherit; display: block;
      transition: transform .2s; margin-bottom: 14px;
    }
    .rec-card:hover { transform: translateX(4px); }
    .rec-ikon { font-size: 1.6rem; margin-bottom: 8px; display: block; }
    .rec-kat  { font-size: 11px; font-weight: 700; color: var(--biru); background: #e0f2fe; border-radius: 999px; padding: 2px 10px; }
    .rec-card h6 { font-weight: 700; font-size: 13.5px; color: var(--gelap); margin: 8px 0 4px; line-height: 1.4; }
    .rec-card small { font-size: 11.5px; color: #94a3b8; }

    .back-btn {
      display: inline-flex; align-items: center; gap: 6px;
      background: #e0f2fe; color: var(--biru); border-radius: 999px;
      font-size: 13px; font-weight: 700; padding: 8px 18px;
      text-decoration: none; transition: background .2s;
    }
    .back-btn:hover { background: #bae6fd; color: var(--biru); }

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
            <img src="<?= $foto_user ?>" alt="Profil" style="width:40px;height:40px;object-fit:cover;border-radius:50%;">
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO ARTIKEL -->
<section class="artikel-hero">
  <div class="container" style="max-width:860px;">
    <a href="blog.php" class="back-btn mb-3 d-inline-flex">← Kembali ke Blog</a>
    <div>
      <span class="kat-badge"><?= $artikel['ikon_emoji'] ?> <?= htmlspecialchars($artikel['kategori']) ?></span>
      <h1><?= htmlspecialchars($artikel['judul']) ?></h1>
      <p class="artikel-meta">
        ✍️ <strong><?= htmlspecialchars($artikel['penulis']) ?></strong>
        &nbsp;·&nbsp;
        <?= date('d F Y', strtotime($artikel['dibuat_pada'])) ?>
      </p>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="row g-4">

      <!-- ISI ARTIKEL -->
      <div class="col-lg-8">
        <div class="artikel-body">
          <?= renderIsi($artikel['isi']) ?>
        </div>

        <div class="mt-4 d-flex gap-3 flex-wrap">
          <a href="blog.php" class="back-btn">← Semua Artikel</a>
          <a href="blog.php?kategori=<?= urlencode($artikel['kategori']) ?>" class="back-btn" style="background:#d1fae5; color:#065f46;">
            📂 <?= htmlspecialchars($artikel['kategori']) ?>
          </a>
        </div>
      </div>

      <!-- SIDEBAR: ARTIKEL LAIN -->
      <div class="col-lg-4">
        <div style="background:white; border-radius:18px; padding:22px; box-shadow:0 4px 20px rgba(20,143,205,.07);">
          <h6 style="font-weight:800; color:var(--gelap); margin-bottom:16px;">📚 Artikel Lainnya</h6>
          <?php while ($r = mysqli_fetch_assoc($qLain)): ?>
          <a href="detailBlog.php?slug=<?= urlencode($r['slug']) ?>" class="rec-card">
            <span class="rec-ikon"><?= $r['ikon_emoji'] ?></span>
            <span class="rec-kat"><?= htmlspecialchars($r['kategori']) ?></span>
            <h6><?= htmlspecialchars($r['judul']) ?></h6>
            <small>✍️ <?= htmlspecialchars($r['penulis']) ?> · <?= date('d M Y', strtotime($r['dibuat_pada'])) ?></small>
          </a>
          <?php endwhile; ?>
          <a href="blog.php" style="font-size:13px; color:var(--biru); font-weight:700; text-decoration:none;">Lihat semua artikel →</a>
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
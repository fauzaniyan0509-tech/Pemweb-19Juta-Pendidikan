<?php
session_start();
$host = "localhost"; $user = "root"; $pass = ""; $db = "19juta_pendidikan";
$conn = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($conn, "utf8mb4");
require_once 'helperSosmed.php';

// ─── PROSES EDIT RATING (untuk tempat yang sudah disetujui & live) ──────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'edit_rating') {
    $namaTempat = mysqli_real_escape_string($conn, trim($_POST['nama_tempat']));
    $rating     = trim($_POST['rating']);
    $ratingSql  = ($rating === '') ? "NULL" : "'" . mysqli_real_escape_string($conn, $rating) . "'";
    $statusKembali = mysqli_real_escape_string($conn, $_POST['status_kembali'] ?? 'disetujui');

    mysqli_query($conn, "UPDATE tempat_edukatif SET rating = $ratingSql WHERE nama_tempat_edukatif = '$namaTempat'");
    header("Location: HalamanVerifikasiTempat.php?status=$statusKembali&pesan=rating_diperbarui"); exit();
}

// ─── PROSES SETUJUI + EDIT (termasuk tambah rating) ─────────────────────────
// Admin bisa mengoreksi data pengajuan & menambahkan rating sebelum dipublikasikan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'setujui_edit') {
    $id   = mysqli_real_escape_string($conn, $_POST['id_pengajuan']);
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_tempat']));
    $kat  = mysqli_real_escape_string($conn, $_POST['kategori']);
    $maps = mysqli_real_escape_string($conn, trim($_POST['alamat_maps']));
    $jam  = mysqli_real_escape_string($conn, trim($_POST['jam_operasional']));
    $pras = mysqli_real_escape_string($conn, trim($_POST['prasarana']));
    $ig   = mysqli_real_escape_string($conn, trim($_POST['sosmed_instagram']));
    $x    = mysqli_real_escape_string($conn, trim($_POST['sosmed_x']));
    $yt   = mysqli_real_escape_string($conn, trim($_POST['sosmed_youtube']));
    $lain = mysqli_real_escape_string($conn, trim($_POST['sosmed_lainnya']));
    $rating = trim($_POST['rating']);
    $ratingSql = ($rating === '') ? "NULL" : "'" . mysqli_real_escape_string($conn, $rating) . "'";

    $qFoto = mysqli_query($conn, "SELECT foto FROM pengajuan_tempat WHERE id_pengajuan = '$id'");
    $foto  = ($r = mysqli_fetch_assoc($qFoto)) ? mysqli_real_escape_string($conn, $r['foto']) : '';

    mysqli_query($conn,
        "INSERT INTO tempat_edukatif
            (nama_tempat_edukatif, kategori, alamat_maps, jam_operasional, prasarana, sosmed_instagram, sosmed_x, sosmed_youtube, sosmed_lainnya, foto, rating)
         VALUES ('$nama','$kat','$maps','$jam','$pras','$ig','$x','$yt','$lain','$foto', $ratingSql)"
    );
    mysqli_query($conn, "UPDATE pengajuan_tempat SET status='disetujui' WHERE id_pengajuan='$id'");
    header("Location: HalamanVerifikasiTempat.php?pesan=disetujui"); exit();
}

// ─── PROSES SETUJUI (tanpa edit, dipertahankan untuk kompatibilitas) ────────
// Saat disetujui, data otomatis masuk ke tabel tempat_edukatif
if (isset($_GET['setujui'])) {
    $id = mysqli_real_escape_string($conn, $_GET['setujui']);
    $q  = mysqli_query($conn, "SELECT * FROM pengajuan_tempat WHERE id_pengajuan = '$id'");
    $d  = mysqli_fetch_assoc($q);
    if ($d) {
        $nama     = mysqli_real_escape_string($conn, $d['nama_tempat']);
        $kat      = mysqli_real_escape_string($conn, $d['kategori']);
        $maps     = mysqli_real_escape_string($conn, $d['alamat_maps']);
        $jam      = mysqli_real_escape_string($conn, $d['jam_operasional']);
        $pras     = mysqli_real_escape_string($conn, $d['prasarana']);
        $ig       = mysqli_real_escape_string($conn, $d['sosmed_instagram'] ?? '');
        $x        = mysqli_real_escape_string($conn, $d['sosmed_x'] ?? '');
        $yt       = mysqli_real_escape_string($conn, $d['sosmed_youtube'] ?? '');
        $lain     = mysqli_real_escape_string($conn, $d['sosmed_lainnya'] ?? '');
        $foto     = mysqli_real_escape_string($conn, $d['foto']);
        mysqli_query($conn,
            "INSERT INTO tempat_edukatif (nama_tempat_edukatif, kategori, alamat_maps, jam_operasional, prasarana, sosmed_instagram, sosmed_x, sosmed_youtube, sosmed_lainnya, foto)
             VALUES ('$nama','$kat','$maps','$jam','$pras','$ig','$x','$yt','$lain','$foto')"
        );
        mysqli_query($conn, "UPDATE pengajuan_tempat SET status='disetujui' WHERE id_pengajuan='$id'");
    }
    header("Location: HalamanVerifikasiTempat.php?pesan=disetujui"); exit();
}

// ─── PROSES TOLAK ───────────────────────────────────────────────────────────
if (isset($_GET['tolak'])) {
    $id = mysqli_real_escape_string($conn, $_GET['tolak']);
    mysqli_query($conn, "UPDATE pengajuan_tempat SET status='ditolak' WHERE id_pengajuan='$id'");
    header("Location: HalamanVerifikasiTempat.php?pesan=ditolak"); exit();
}

// ─── FILTER STATUS ──────────────────────────────────────────────────────────
$filter  = isset($_GET['status']) ? $_GET['status'] : 'menunggu';
$where   = in_array($filter, ['menunggu','disetujui','ditolak']) ? "WHERE status='$filter'" : "";
$data    = mysqli_query($conn, "SELECT * FROM pengajuan_tempat $where ORDER BY tgl_pengajuan DESC");

// Hitung per status
$jml = [];
foreach (['menunggu','disetujui','ditolak'] as $s) {
    $r = mysqli_query($conn, "SELECT COUNT(*) as n FROM pengajuan_tempat WHERE status='$s'");
    $jml[$s] = mysqli_fetch_assoc($r)['n'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifikasi Tempat Edukatif – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    :root { --blue:#2f6df6; --teal:#35c7b6; --dark:#14213d; --muted:#6b7280; --border:#e2e8f0; --shadow:0 18px 45px rgba(20,33,61,.10); }
    * { font-family:'Poppins',sans-serif; box-sizing:border-box; }
    body { background:linear-gradient(180deg,#eef8fb,#f7fcfb); color:var(--dark); min-height:100vh; }
    .admin-wrapper { display:flex; min-height:100vh; }

    /* SIDEBAR */
    .sidebar { width:270px; background:#fff; border-right:1px solid var(--border); padding:26px 20px; position:fixed; height:100vh; overflow-y:auto; }
    .logo-text { font-weight:800; font-size:22px; color:var(--blue); margin-bottom:36px; display:block; }
    .menu-item { display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:14px; color:#334155; text-decoration:none; font-size:14px; font-weight:600; margin-bottom:8px; transition:.25s; }
    .menu-item:hover, .menu-item.active { background:linear-gradient(90deg,var(--blue),var(--teal)); color:white; }
    .menu-toggle { display: flex; align-items: center; justify-content: space-between; }
    .menu-toggle .chevron { font-size: 11px; transition: transform .2s ease; }
    .menu-toggle[aria-expanded="true"] .chevron { transform: rotate(180deg); }
    .submenu { display: flex; flex-direction: column; padding-left: 16px; margin-bottom: 4px; }
    .submenu-item { font-size: 12.5px; padding: 9px 14px; }
    .logout { background:#fee2e2; color:#b91c1c; text-align:center; margin-top:16px; }

    /* MAIN */
    .main-content { margin-left:270px; width:calc(100% - 270px); padding:34px; }
    .topbar { background:white; border-radius:22px; padding:22px 26px; box-shadow:var(--shadow); margin-bottom:28px; }
    .page-title { font-size:26px; font-weight:800; background:linear-gradient(90deg,var(--blue),var(--teal)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin:0; }
    .page-desc { color:var(--muted); font-size:14px; margin:4px 0 0; }

    /* STAT TABS */
    .stat-tabs { display:flex; gap:14px; margin-bottom:24px; flex-wrap:wrap; }
    .stat-tab { background:white; border-radius:16px; padding:16px 22px; border:2px solid transparent; box-shadow:var(--shadow); cursor:pointer; text-decoration:none; transition:.2s; min-width:150px; }
    .stat-tab:hover { border-color:var(--blue); }
    .stat-tab.aktif-menunggu { border-color:#f59e0b; background:#fffbeb; }
    .stat-tab.aktif-disetujui { border-color:#10b981; background:#f0fdf4; }
    .stat-tab.aktif-ditolak { border-color:#ef4444; background:#fef2f2; }
    .stat-angka { font-size:28px; font-weight:800; }
    .stat-label { font-size:12px; font-weight:600; color:var(--muted); }
    .warna-menunggu { color:#d97706; }
    .warna-disetujui { color:#059669; }
    .warna-ditolak   { color:#dc2626; }

    /* KARTU PENGAJUAN */
    .kartu-pengajuan { background:white; border-radius:18px; border:1px solid var(--border); box-shadow:0 4px 20px rgba(20,33,61,.07); overflow:hidden; margin-bottom:16px; }
    .kartu-head { display:flex; gap:16px; padding:18px 20px; align-items:flex-start; }
    .foto-pengajuan { width:90px; height:72px; object-fit:cover; border-radius:12px; flex-shrink:0; background:#f1f5f9; }
    .foto-placeholder { width:90px; height:72px; border-radius:12px; background:linear-gradient(135deg,#dbeafe,#ccfbf1); display:flex; align-items:center; justify-content:center; font-size:28px; flex-shrink:0; }
    .info-pengajuan { flex:1; }
    .nama-pengajuan { font-size:16px; font-weight:800; margin-bottom:2px; }
    .meta-pengajuan { font-size:12px; color:var(--muted); display:flex; flex-wrap:wrap; gap:10px; margin-top:6px; }
    .meta-item { display:flex; align-items:center; gap:4px; }
    .badge-status { border-radius:999px; padding:3px 12px; font-size:11px; font-weight:700; }
    .badge-menunggu { background:#fef3c7; color:#b45309; }
    .badge-disetujui { background:#d1fae5; color:#065f46; }
    .badge-ditolak   { background:#fee2e2; color:#b91c1c; }
    .badge-kategori  { background:#e0f2fe; color:#0369a1; border-radius:999px; padding:3px 10px; font-size:11px; font-weight:700; }

    .kartu-body { padding:0 20px 18px; border-top:1px solid #f1f5f9; display:none; }
    .kartu-body.terbuka { display:block; }
    .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin:14px 0; }
    .detail-item label { font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; }
    .detail-item span  { font-size:13px; color:#334155; font-weight:500; display:block; }
    .link-maps { color:var(--teal); font-size:13px; font-weight:600; text-decoration:none; }

    .aksi-baris { display:flex; gap:10px; margin-top:14px; flex-wrap:wrap; }
    .btn-setujui { background:#d1fae5; color:#065f46; border:none; border-radius:10px; padding:9px 18px; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; }
    .btn-tolak   { background:#fee2e2; color:#b91c1c; border:none; border-radius:10px; padding:9px 18px; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; }
    .btn-setujui:hover { background:#a7f3d0; }
    .btn-tolak:hover   { background:#fecaca; }
    .btn-toggle { background:#f1f5f9; color:#475569; border:none; border-radius:10px; padding:9px 14px; font-size:12px; font-weight:600; cursor:pointer; margin-left:auto; }

    .kosong-state { text-align:center; padding:60px; color:var(--muted); background:white; border-radius:18px; }
    .kosong-state .ikon { font-size:52px; margin-bottom:14px; }

    @media(max-width:991px) {
      .sidebar { position:static; width:100%; height:auto; }
      .admin-wrapper { flex-direction:column; }
      .main-content { margin-left:0; width:100%; padding:20px; }
      .detail-grid { grid-template-columns:1fr; }
    }
  </style>
</head>
<body>
<div class="admin-wrapper">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <span class="logo-text">19JutaAdmin</span>
    <a class="menu-item" href="adminDashboard.php">📊 Dashboard</a>
    <a class="menu-item" href="halamanKelolaLomba.php">🏆 Kelola Lomba</a>
    <a class="menu-item" href="halamanKelolaBeasiswa.php">🎓 Kelola Beasiswa</a>
    <a href="#" class="menu-item menu-toggle active" data-bs-toggle="collapse" data-bs-target="#submenuTempat" role="button" aria-expanded="true">
      <span>📍 Kelola Tempat / Peta</span>
      <span class="chevron">▾</span>
    </a>
    <div class="collapse show submenu" id="submenuTempat">
      <a href="halamanKelolaTempat.php" class="menu-item submenu-item">📋 Daftar Tempat</a>
      <a href="HalamanVerifikasiTempat.php" class="menu-item submenu-item active">✅ Verifikasi Pengajuan</a>
    </div>
    <a class="menu-item" href="halamanVerifikasi.php">📋 Verifikasi Iklan</a>
    <a href="logout.php" class="menu-item logout">🚪 Logout</a>
  </aside>

  <main class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
      <h1 class="page-title">Verifikasi Pengajuan Tempat</h1>
      <p class="page-desc">Review dan setujui tempat edukatif yang diajukan oleh pengguna.</p>
    </div>

    <!-- ALERT -->
    <?php if (isset($_GET['pesan'])): ?>
    <?php
      if ($_GET['pesan'] === 'disetujui') {
        $info = ['✅ Pengajuan disetujui dan tempat berhasil ditambahkan ke halaman publik!', 'success'];
      } elseif ($_GET['pesan'] === 'rating_diperbarui') {
        $info = ['⭐ Rating tempat berhasil diperbarui!', 'success'];
      } else {
        $info = ['❌ Pengajuan berhasil ditolak.', 'warning'];
      }
    ?>
    <div class="alert alert-<?= $info[1] ?> alert-dismissible fade show rounded-4 mb-4">
      <?= $info[0] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- STAT TABS -->
    <div class="stat-tabs">
      <a href="?status=menunggu" class="stat-tab <?= $filter==='menunggu' ? 'aktif-menunggu' : '' ?>">
        <div class="stat-angka warna-menunggu"><?= $jml['menunggu'] ?></div>
        <div class="stat-label">⏳ Menunggu Review</div>
      </a>
      <a href="?status=disetujui" class="stat-tab <?= $filter==='disetujui' ? 'aktif-disetujui' : '' ?>">
        <div class="stat-angka warna-disetujui"><?= $jml['disetujui'] ?></div>
        <div class="stat-label">✅ Disetujui</div>
      </a>
      <a href="?status=ditolak" class="stat-tab <?= $filter==='ditolak' ? 'aktif-ditolak' : '' ?>">
        <div class="stat-angka warna-ditolak"><?= $jml['ditolak'] ?></div>
        <div class="stat-label">❌ Ditolak</div>
      </a>
    </div>

    <!-- LIST PENGAJUAN -->
    <?php if (mysqli_num_rows($data) == 0): ?>
    <div class="kosong-state">
      <div class="ikon">📭</div>
      <h5>Tidak ada pengajuan <?= $filter ?></h5>
      <p>Pengajuan dengan status "<?= $filter ?>" belum ada saat ini.</p>
    </div>
    <?php endif; ?>

    <?php while ($p = mysqli_fetch_assoc($data)):
      $tgl = date('d M Y', strtotime($p['tgl_pengajuan']));
    ?>
    <div class="kartu-pengajuan">
      <div class="kartu-head">

        <!-- FOTO -->
        <?php if ($p['foto']): ?>
          <img src="uploads/<?= htmlspecialchars($p['foto']) ?>" class="foto-pengajuan" alt="foto">
        <?php else: ?>
          <div class="foto-placeholder">📍</div>
        <?php endif; ?>

        <!-- INFO SINGKAT -->
        <div class="info-pengajuan">
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="nama-pengajuan"><?= htmlspecialchars($p['nama_tempat']) ?></span>
            <span class="badge-kategori"><?= htmlspecialchars($p['kategori']) ?></span>
            <span class="badge-status badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span>
          </div>
          <div class="meta-pengajuan">
            <?php if (!empty($p['nama_pengaju'])): ?>
            <span class="meta-item">👤 <?= htmlspecialchars($p['nama_pengaju']) ?></span>
            <?php endif; ?>
            <span class="meta-item">📱 <?= htmlspecialchars($p['no_wa_pengaju']) ?></span>
            <span class="meta-item">📅 <?= $tgl ?></span>
          </div>
        </div>

        <button class="btn-toggle" onclick="toggleDetail(this)">Lihat Detail ▾</button>
      </div>

      <!-- DETAIL (collapsed by default) -->
      <div class="kartu-body">
        <div class="detail-grid">
          <div class="detail-item">
            <label>Jam Operasional</label>
            <span><?= htmlspecialchars($p['jam_operasional'] ?: '–') ?></span>
          </div>
          <div class="detail-item">
            <label>Sosial Media</label>
            <span>
              <?php $listSosmed = daftarSosmed($p); ?>
              <?php if (!empty($listSosmed)): ?>
                <?php foreach ($listSosmed as $sm): ?>
                  <?php if ($sm['url']): ?>
                    <a href="<?= htmlspecialchars($sm['url']) ?>" target="_blank" style="color:var(--blue); text-decoration:none; margin-right:10px;"><?= $sm['icon'] ?> <?= htmlspecialchars($sm['label']) ?></a>
                  <?php else: ?>
                    <span style="margin-right:10px;"><?= $sm['icon'] ?> <?= htmlspecialchars($sm['label']) ?></span>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php else: ?>
                –
              <?php endif; ?>
            </span>
          </div>
          <div class="detail-item">
            <label>Fasilitas</label>
            <span><?= htmlspecialchars($p['prasarana'] ?: '–') ?></span>
          </div>
          <div class="detail-item">
            <label>Alamat Lengkap</label>
            <span><?= htmlspecialchars($p['alamat_lengkap'] ?: '–') ?></span>
          </div>
        </div>

        <?php if ($p['alamat_maps']): ?>
        <a href="<?= htmlspecialchars($p['alamat_maps']) ?>" target="_blank" class="link-maps">🗺️ Buka di Google Maps</a>
        <?php endif; ?>

        <?php if ($p['status'] === 'menunggu'): ?>
        <div class="aksi-baris">
          <button type="button" class="btn-setujui" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $p['id_pengajuan'] ?>">
            ✏️ Edit & Setujui
          </button>
          <a href="?tolak=<?= $p['id_pengajuan'] ?>" class="btn-tolak"
            onclick="return confirm('Tolak pengajuan ini?')">
            ❌ Tolak
          </a>
        </div>
        <?php endif; ?>

        <?php if ($p['status'] === 'disetujui'):
          $namaEsc = mysqli_real_escape_string($conn, $p['nama_tempat']);
          $qMatch  = mysqli_query($conn, "SELECT id_tempat, rating FROM tempat_edukatif WHERE nama_tempat_edukatif = '$namaEsc' LIMIT 1");
          $match   = mysqli_fetch_assoc($qMatch);
        ?>
          <?php if ($match): ?>
          <div class="aksi-baris">
            <span style="font-size:13px; color:var(--muted); align-self:center;">
              ⭐ Rating saat ini: <strong><?= $match['rating'] !== null && $match['rating'] !== '' ? htmlspecialchars($match['rating']) : '–' ?></strong>
            </span>
            <button type="button" class="btn-setujui" data-bs-toggle="modal" data-bs-target="#modalRating<?= $p['id_pengajuan'] ?>">
              ⭐ Edit Rating
            </button>
          </div>
          <?php else: ?>
          <div class="aksi-baris">
            <span style="font-size:12px; color:var(--muted);">ℹ️ Data tempat ini tidak ditemukan di menu <strong>Daftar Tempat</strong> (mungkin nama sudah diubah). Edit rating lewat menu Daftar Tempat.</span>
          </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- MODAL EDIT RATING (untuk tempat yang sudah disetujui) -->
    <?php if ($p['status'] === 'disetujui' && $match): ?>
    <div class="modal fade" id="modalRating<?= $p['id_pengajuan'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px; border:none;">
          <form method="POST" action="">
            <input type="hidden" name="aksi" value="edit_rating">
            <input type="hidden" name="nama_tempat" value="<?= htmlspecialchars($p['nama_tempat']) ?>">
            <input type="hidden" name="status_kembali" value="<?= htmlspecialchars($filter) ?>">
            <div class="modal-header">
              <h5 class="modal-title" style="font-weight:800;">⭐ Edit Rating</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p class="text-muted" style="font-size:13px;">
                Ubah rating untuk <strong><?= htmlspecialchars($p['nama_tempat']) ?></strong> yang sudah tampil di halaman Peta Edukasi.
              </p>
              <label class="form-label fw-bold" style="font-size:12px;">Rating (0–5)</label>
              <input type="number" name="rating" class="form-control" step="0.1" min="0" max="5"
                value="<?= htmlspecialchars($match['rating'] ?? '') ?>" placeholder="Contoh: 4.5" style="max-width:160px;">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn-setujui" style="border-radius:10px; padding:9px 18px;">💾 Simpan Rating</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php if ($p['status'] === 'menunggu'):
      $igVal = !empty($p['sosmed_instagram']) ? $p['sosmed_instagram'] : ($p['sosial_media'] ?? '');
      $kategoriList = [
        'perpustakaan'  => '📚 Perpustakaan',
        'kafe-belajar'  => '☕ Kafe Belajar',
        'teknologi'     => '💡 Teknologi & Inovasi',
        'museum'        => '🏛️ Museum & Sejarah',
        'ruang-kreatif' => '🎨 Ruang Kreatif',
        'lainnya'       => '📌 Lainnya',
      ];
    ?>
    <div class="modal fade" id="modalEdit<?= $p['id_pengajuan'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:18px; border:none;">
          <form method="POST" action="">
            <input type="hidden" name="aksi" value="setujui_edit">
            <input type="hidden" name="id_pengajuan" value="<?= $p['id_pengajuan'] ?>">
            <div class="modal-header">
              <h5 class="modal-title" style="font-weight:800;">✏️ Edit & Setujui Tempat</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p class="text-muted" style="font-size:13px;">Periksa dan koreksi data sebelum dipublikasikan ke halaman umum. Tambahkan rating jika diperlukan.</p>

              <div class="mb-3">
                <label class="form-label fw-bold" style="font-size:12px;">Nama Tempat</label>
                <input type="text" name="nama_tempat" class="form-control" value="<?= htmlspecialchars($p['nama_tempat']) ?>" required>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold" style="font-size:12px;">Kategori</label>
                  <select name="kategori" class="form-select">
                    <?php foreach ($kategoriList as $val => $label): ?>
                      <option value="<?= $val ?>" <?= ($p['kategori'] === $val) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold" style="font-size:12px;">Jam Operasional</label>
                  <input type="text" name="jam_operasional" class="form-control" value="<?= htmlspecialchars($p['jam_operasional']) ?>">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold" style="font-size:12px;">Link Google Maps</label>
                <input type="url" name="alamat_maps" class="form-control" value="<?= htmlspecialchars($p['alamat_maps']) ?>">
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold" style="font-size:12px;">Fasilitas / Prasarana</label>
                <input type="text" name="prasarana" class="form-control" value="<?= htmlspecialchars($p['prasarana']) ?>">
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold" style="font-size:12px;">Sosial Media</label>
                <div class="row g-2">
                  <div class="col-md-6">
                    <div class="input-group">
                      <span class="input-group-text">📷</span>
                      <input type="text" name="sosmed_instagram" class="form-control" placeholder="Instagram" value="<?= htmlspecialchars($igVal) ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group">
                      <span class="input-group-text">✖️</span>
                      <input type="text" name="sosmed_x" class="form-control" placeholder="X" value="<?= htmlspecialchars($p['sosmed_x'] ?? '') ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group">
                      <span class="input-group-text">▶️</span>
                      <input type="text" name="sosmed_youtube" class="form-control" placeholder="YouTube" value="<?= htmlspecialchars($p['sosmed_youtube'] ?? '') ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group">
                      <span class="input-group-text">🔗</span>
                      <input type="text" name="sosmed_lainnya" class="form-control" placeholder="Lainnya" value="<?= htmlspecialchars($p['sosmed_lainnya'] ?? '') ?>">
                    </div>
                  </div>
                </div>
              </div>

              <div class="mb-2">
                <label class="form-label fw-bold" style="font-size:12px;">⭐ Rating (0–5)</label>
                <input type="number" name="rating" class="form-control" step="0.1" min="0" max="5" placeholder="Contoh: 4.5" style="max-width:160px;">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn-setujui" style="border-radius:10px; padding:9px 18px;"
                onclick="return confirm('Simpan perubahan dan publikasikan tempat ini?')">
                ✅ Simpan & Publikasikan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php endwhile; ?>

  </main>
</div>

<script>
  function toggleDetail(btn) {
    const body = btn.closest('.kartu-pengajuan').querySelector('.kartu-body');
    body.classList.toggle('terbuka');
    btn.textContent = body.classList.contains('terbuka') ? 'Tutup ▴' : 'Lihat Detail ▾';
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
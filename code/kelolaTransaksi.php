<?php
// =========================================================
// kelolaTransaksi.php — Halaman Kelola Transaksi (Admin Only)
// STATUS OTOMATIS MENGIKUTI VERIFIKASI IKLAN
// =========================================================
include 'penghubung.php';

// 🔒 Proteksi: Hanya admin yang bisa akses
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: halamanLoginAdmin.php");
    exit();
}

$nama_admin = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin');

// ── 1. PROSES UPDATE STATUS PEMBAYARAN (OVERRIDE MANUAL) ──
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id_pembayaran = (int) $_POST['id_pembayaran'];
    $status_baru   = $_POST['status_baru'];
    $id_admin_aktif = $_SESSION['id_admin'] ?? 1;
    
    $status_valid = ['pending', 'sukses', 'gagal'];
    if (!in_array($status_baru, $status_valid)) {
        $pesan = "<div class='alert alert-danger'>Status tidak valid!</div>";
    } else {
        $stmt = $koneksi->prepare("UPDATE pembayaran SET status_pembayaran = ?, id_admin = ? WHERE id_pembayaran = ?");
        $stmt->bind_param("sii", $status_baru, $id_admin_aktif, $id_pembayaran);
        if ($stmt->execute()) {
            $pesan = "<div class='alert alert-success'>✅ Status pembayaran #" . $id_pembayaran . " berhasil di-override menjadi <strong>" . ucfirst($status_baru) . "</strong></div>";
        } else {
            $pesan = "<div class='alert alert-danger'>❌ Gagal update: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// ── 2. FILTER & PENCARIAN ─────────────────────────────────
$filter_status  = $_GET['status'] ?? 'semua';
$search_keyword = trim($_GET['q'] ?? '');

// ── 3. QUERY TRANSAKSI DENGAN STATUS OTOMATIS ────────────
// STATUS DITENTUKAN OLEH STATUS VERIFIKASI IKLAN
$where = [];
if (!empty($search_keyword)) {
    $keyword = mysqli_real_escape_string($koneksi, $search_keyword);
    $where[] = "(u.nama LIKE '%$keyword%' OR u.email LIKE '%$keyword%' OR p.id_pembayaran LIKE '%$keyword%' OR p.metode_pembayaran LIKE '%$keyword%')";
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Query utama: gabungkan pembayaran dengan iklan untuk mendapatkan status verifikasi
$query_transaksi = "
    SELECT 
        p.id_pembayaran, p.id_user, p.jumlah, p.metode_pembayaran, 
        p.bukti_pembayaran, p.status_pembayaran AS status_asli, p.tanggal_bayar, p.id_admin,
        u.nama AS nama_user, u.email AS email_user,
        
        -- Status verifikasi dari iklan lomba (jika ada)
        il.status_verifikasi AS status_verif_lomba,
        il.judul_iklan AS judul_iklan_lomba,
        il.paket_langganan AS paket_lomba,
        
        -- Status verifikasi dari iklan beasiswa (jika ada)
        ib.status_verifikasi AS status_verif_beasiswa,
        ib.judul_iklan AS judul_iklan_beasiswa,
        ib.paket_langganan AS paket_beasiswa,
        
        -- STATUS OTOMATIS: ditentukan oleh status verifikasi iklan
        CASE 
            WHEN il.status_verifikasi = 'disetujui' OR ib.status_verifikasi = 'disetujui' THEN 'sukses'
            WHEN il.status_verifikasi = 'ditolak' OR ib.status_verifikasi = 'ditolak' THEN 'gagal'
            WHEN il.status_verifikasi = 'menunggu' OR ib.status_verifikasi = 'menunggu' THEN 'pending'
            ELSE p.status_pembayaran
        END AS status_efektif,
        
        -- Sumber status (untuk indikator visual)
        CASE 
            WHEN il.status_verifikasi IS NOT NULL THEN 'iklan_lomba'
            WHEN ib.status_verifikasi IS NOT NULL THEN 'iklan_beasiswa'
            ELSE 'manual'
        END AS sumber_status
        
    FROM pembayaran p
    LEFT JOIN user u ON p.id_user = u.id_user
    LEFT JOIN iklan_lomba il ON p.id_pembayaran = il.id_pembayaran
    LEFT JOIN iklan_beasiswa ib ON p.id_pembayaran = ib.id_pembayaran
    $where_sql
    ORDER BY p.tanggal_bayar DESC
";

$list_transaksi = mysqli_query($koneksi, $query_transaksi);

// ── 4. STATISTIK TRANSAKSI (BERDASARKAN STATUS EFEKTIF) ──
// Hitung statistik berdasarkan status efektif (otomatis dari verifikasi)
$stat_query = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE 
            WHEN il.status_verifikasi = 'disetujui' OR ib.status_verifikasi = 'disetujui' THEN 1 
            WHEN il.status_verifikasi = 'ditolak' OR ib.status_verifikasi = 'ditolak' THEN 0
            WHEN il.status_verifikasi = 'menunggu' OR ib.status_verifikasi = 'menunggu' THEN 0
            WHEN p.status_pembayaran = 'sukses' THEN 1
            ELSE 0
        END) as sukses,
        SUM(CASE 
            WHEN il.status_verifikasi = 'disetujui' OR ib.status_verifikasi = 'disetujui' THEN 0
            WHEN il.status_verifikasi = 'ditolak' OR ib.status_verifikasi = 'ditolak' THEN 1
            WHEN il.status_verifikasi = 'menunggu' OR ib.status_verifikasi = 'menunggu' THEN 0
            WHEN p.status_pembayaran = 'gagal' THEN 1
            ELSE 0
        END) as gagal,
        SUM(CASE 
            WHEN il.status_verifikasi = 'disetujui' OR ib.status_verifikasi = 'disetujui' THEN 0
            WHEN il.status_verifikasi = 'ditolak' OR ib.status_verifikasi = 'ditolak' THEN 0
            WHEN il.status_verifikasi = 'menunggu' OR ib.status_verifikasi = 'menunggu' THEN 1
            WHEN p.status_pembayaran = 'pending' THEN 1
            ELSE 0
        END) as pending,
        SUM(CASE 
            WHEN (il.status_verifikasi = 'disetujui' OR ib.status_verifikasi = 'disetujui' OR p.status_pembayaran = 'sukses') 
            THEN p.jumlah ELSE 0 
        END) as pendapatan
    FROM pembayaran p
    LEFT JOIN iklan_lomba il ON p.id_pembayaran = il.id_pembayaran
    LEFT JOIN iklan_beasiswa ib ON p.id_pembayaran = ib.id_pembayaran
";
$stat = mysqli_fetch_assoc(mysqli_query($koneksi, $stat_query));
$stat_total      = $stat['total'] ?? 0;
$stat_sukses     = $stat['sukses'] ?? 0;
$stat_gagal      = $stat['gagal'] ?? 0;
$stat_pending    = $stat['pending'] ?? 0;
$stat_pendapatan = $stat['pendapatan'] ?? 0;

// Filter berdasarkan status efektif
if ($filter_status !== 'semua') {
    // Filter dilakukan di PHP setelah data diambil
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Transaksi - 19JutaPendidikan Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    :root {
      --blue: #2f6df6; --teal: #35c7b6; --dark: #14213d;
      --muted: #6b7280; --card: #ffffff; --border: #dbe5ea;
      --shadow: 0 18px 45px rgba(20,33,61,.10);
    }
    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
    body { background: linear-gradient(180deg,#eef8fb 0%,#f7fcfb 100%); color: var(--dark); min-height: 100vh; }

    /* SIDEBAR */
    .sidebar { width: 260px; height: 100vh; position: fixed; background: #fff; padding: 25px 20px; box-shadow: 2px 0 15px rgba(0,0,0,0.05); z-index: 100; }
    .sidebar h4 { font-weight: 800; color: var(--blue); margin-bottom: 24px; }
    .sidebar .nav-link { color: #6b7280; padding: 12px 15px; border-radius: 10px; display: block; text-decoration: none; margin-bottom: 6px; font-weight: 500; transition: .2s; }
    .sidebar .nav-link:hover { background: #f1f5f9; color: var(--dark); }
    .sidebar .nav-link.active { background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; }
    .sidebar .nav-link.logout { background: #fee2e2; color: #b91c1c; margin-top: 20px; }
    .sidebar .nav-link.logout:hover { background: #fca5a5; }

    /* MAIN */
    .main-content { margin-left: 280px; padding: 32px; }
    .page-title { font-weight: 800; font-size: 28px; background: linear-gradient(90deg, var(--blue), var(--teal)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 4px; }
    .page-subtitle { color: var(--muted); font-size: 14px; margin-bottom: 24px; }

    /* INFO BOX */
    .info-box { background: linear-gradient(135deg, #eff6ff, #f0fdf4); border: 1px solid #bfdbfe; border-radius: 16px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 14px; }
    .info-box i { font-size: 28px; color: var(--blue); }
    .info-box-text h6 { font-weight: 700; margin: 0 0 2px; font-size: 14px; color: var(--dark); }
    .info-box-text p { margin: 0; font-size: 12px; color: var(--muted); line-height: 1.6; }

    /* STAT CARD */
    .stat-card { background: var(--card); border-radius: 18px; padding: 22px; box-shadow: var(--shadow); border: 1px solid #eef2f7; transition: .25s; height: 100%; }
    .stat-card:hover { transform: translateY(-4px); }
    .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 12px; }
    .stat-icon.blue { background: #dbeafe; color: #1d4ed8; }
    .stat-icon.yellow { background: #fef3c7; color: #b45309; }
    .stat-icon.green { background: #dcfce7; color: #15803d; }
    .stat-icon.red { background: #fee2e2; color: #b91c1c; }
    .stat-icon.purple { background: #ede9fe; color: #6d28d9; }
    .stat-number { font-size: 28px; font-weight: 800; color: var(--dark); line-height: 1; margin-bottom: 4px; }
    .stat-label { color: var(--muted); font-size: 12px; font-weight: 500; margin: 0; }

    /* FILTER BAR */
    .filter-bar { background: var(--card); border-radius: 18px; padding: 20px; box-shadow: var(--shadow); border: 1px solid #eef2f7; margin-bottom: 24px; }
    .filter-bar .form-control, .filter-bar .form-select { border-radius: 12px; padding: 10px 14px; font-size: 14px; border-color: #cbd5e1; }
    .filter-bar .form-control:focus, .filter-bar .form-select:focus { border-color: var(--teal); box-shadow: 0 0 0 4px rgba(53,199,182,.15); }
    .filter-bar label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }

    /* TABLE */
    .table-card { background: var(--card); border-radius: 18px; padding: 24px; box-shadow: var(--shadow); border: 1px solid #eef2f7; }
    .table thead th { background: #f8fafc; color: #475569; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: .5px; border-bottom: none; padding: 14px; }
    .table tbody td { padding: 16px 14px; vertical-align: middle; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
    .table tbody tr:hover { background: #f8fafc; }
    .user-info { display: flex; align-items: center; gap: 10px; }
    .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--blue), var(--teal)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px; flex-shrink: 0; }
    .user-name { font-weight: 700; color: var(--dark); margin: 0; font-size: 14px; }
    .user-email { color: var(--muted); font-size: 11px; margin: 0; }

    /* STATUS BADGE */
    .sbadge { font-size: 11px; font-weight: 700; border-radius: 999px; padding: 6px 12px; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px; }
    .sbadge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
    .sbadge-pending { background: #fef3c7; color: #b45309; }
    .sbadge-pending::before { background: #b45309; }
    .sbadge-sukses { background: #dcfce7; color: #15803d; }
    .sbadge-sukses::before { background: #15803d; }
    .sbadge-gagal { background: #fee2e2; color: #b91c1c; }
    .sbadge-gagal::before { background: #b91c1c; }

    /* AUTO BADGE INDICATOR */
    .auto-badge { display: inline-block; font-size: 9px; font-weight: 700; background: #dbeafe; color: #1d4ed8; padding: 2px 7px; border-radius: 6px; margin-left: 6px; text-transform: uppercase; letter-spacing: .3px; }

    /* AMOUNT */
    .amount { font-weight: 700; color: var(--dark); font-size: 14px; }

    /* ACTION BUTTON */
    .btn-action { border: none; border-radius: 10px; padding: 7px 12px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; transition: .2s; cursor: pointer; }
    .btn-detail { background: #dbeafe; color: #1d4ed8; }
    .btn-detail:hover { background: #bfdbfe; color: #1e40af; }
    .btn-edit { background: #fef3c7; color: #b45309; }
    .btn-edit:hover { background: #fde68a; }

    /* MODAL */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px); }
    .modal-overlay.active { display: flex; }
    .modal-box { background: #fff; border-radius: 22px; padding: 30px; max-width: 600px; width: 100%; box-shadow: var(--shadow); animation: pop .25s ease; max-height: 90vh; overflow-y: auto; }
    @keyframes pop { from { transform: scale(.93); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #eef2f7; }
    .modal-title { font-weight: 800; font-size: 20px; margin: 0; }
    .modal-close { background: #f1f5f9; border: none; width: 36px; height: 36px; border-radius: 50%; font-size: 20px; cursor: pointer; color: var(--muted); transition: .2s; }
    .modal-close:hover { background: #e2e8f0; color: var(--dark); }
    .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { font-size: 12px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
    .detail-value { font-weight: 700; color: var(--dark); font-size: 14px; text-align: right; max-width: 60%; }
    .bukti-preview { width: 100%; max-height: 300px; object-fit: contain; border-radius: 12px; border: 2px dashed #cbd5e1; background: #f8fafc; margin-top: 10px; }

    .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
    .empty-state i { font-size: 56px; opacity: .3; margin-bottom: 16px; display: block; }
    .empty-state h5 { font-weight: 700; color: var(--dark); margin-bottom: 6px; }

    @media (max-width: 991px) {
      .sidebar { position: static; width: 100%; height: auto; }
      .main-content { margin-left: 0; padding: 20px; }
    }
  </style>
</head>
<body>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <h4>🏛️ 19JutaAdmin</h4>
    <a href="adminDashboard.php" class="nav-link"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
    <a href="halamanVerifikasi.php" class="nav-link"><i class="bi bi-check2-circle me-2"></i> Verifikasi Iklan</a>
    <a href="kelolaTransaksi.php" class="nav-link active"><i class="bi bi-credit-card-2-front-fill me-2"></i> Kelola Transaksi</a>
    <a href="halamankelolaLomba.php" class="nav-link"><i class="bi bi-trophy-fill me-2"></i> Kelola Lomba</a>
    <a href="halamankelolaBeasiswa.php" class="nav-link"><i class="bi bi-mortarboard-fill me-2"></i> Kelola Beasiswa</a>
    <a href="halamanKelolaTempat.php" class="nav-link"><i class="bi bi-geo-alt-fill me-2"></i> Kelola Tempat</a>
    <a href="penghubung.php?aksi=logout_admin" class="nav-link logout"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
  </aside>

  <!-- MAIN -->
  <main class="main-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-2">
      <div>
        <h1 class="page-title">💳 Kelola Transaksi</h1>
        <p class="page-subtitle">Pantau dan kelola semua riwayat pembayaran dari pengguna platform.</p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--teal));display:flex;align-items:center;justify-content:center;color:white;font-weight:800;">
          <?= strtoupper(mb_substr($nama_admin, 0, 1)) ?>
        </div>
        <div>
          <div style="font-weight:700;font-size:14px;"><?= $nama_admin ?></div>
          <div style="font-size:11px;color:var(--muted);">Administrator</div>
        </div>
      </div>
    </div>

    <?= $pesan ?>

    <!-- INFO BOX: Penjelasan Status Otomatis -->
    <div class="info-box">
      <i class="bi bi-info-circle-fill"></i>
      <div class="info-box-text">
        <h6><i class="bi bi-lightning-charge-fill text-warning"></i> Status Pembayaran Otomatis</h6>
        <p>Status transaksi di halaman ini <strong>otomatis mengikuti status verifikasi iklan</strong> di halaman Verifikasi Iklan. 
        Jika iklan <strong class="text-success">disetujui</strong> → pembayaran <strong class="text-success">sukses</strong>. 
        Jika iklan <strong class="text-danger">ditolak</strong> → pembayaran <strong class="text-danger">gagal</strong>. 
        Badge <span class="auto-badge">AUTO</span> menandakan status dihitung otomatis.</p>
      </div>
    </div>

    <!-- STATISTIK -->
    <div class="row g-3 mb-4">
      <div class="col-md-6 col-xl">
        <div class="stat-card">
          <div class="stat-icon blue"><i class="bi bi-receipt"></i></div>
          <div class="stat-number"><?= $stat_total ?></div>
          <p class="stat-label">Total Transaksi</p>
        </div>
      </div>
      <div class="col-md-6 col-xl">
        <div class="stat-card">
          <div class="stat-icon yellow"><i class="bi bi-hourglass-split"></i></div>
          <div class="stat-number"><?= $stat_pending ?></div>
          <p class="stat-label">Menunggu Verifikasi</p>
        </div>
      </div>
      <div class="col-md-6 col-xl">
        <div class="stat-card">
          <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
          <div class="stat-number"><?= $stat_sukses ?></div>
          <p class="stat-label">Transaksi Sukses</p>
        </div>
      </div>
      <div class="col-md-6 col-xl">
        <div class="stat-card">
          <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
          <div class="stat-number"><?= $stat_gagal ?></div>
          <p class="stat-label">Transaksi Gagal</p>
        </div>
      </div>
      <div class="col-md-6 col-xl">
        <div class="stat-card">
          <div class="stat-icon purple"><i class="bi bi-cash-stack"></i></div>
          <div class="stat-number" style="font-size:22px;">Rp <?= number_format($stat_pendapatan, 0, ',', '.') ?></div>
          <p class="stat-label">Total Pendapatan</p>
        </div>
      </div>
    </div>

    <!-- FILTER BAR -->
    <form method="GET" class="filter-bar">
      <div class="row g-3 align-items-end">
        <div class="col-md-5">
          <label><i class="bi bi-search me-1"></i> Pencarian</label>
          <input type="text" name="q" class="form-control" placeholder="Cari nama user, email, atau ID transaksi..." value="<?= htmlspecialchars($search_keyword) ?>">
        </div>
        <div class="col-md-3">
          <label><i class="bi bi-funnel me-1"></i> Filter Status</label>
          <select name="status" class="form-select">
            <option value="semua" <?= $filter_status === 'semua' ? 'selected' : '' ?>>Semua Status</option>
            <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>⏳ Menunggu</option>
            <option value="sukses" <?= $filter_status === 'sukses' ? 'selected' : '' ?>>✅ Sukses</option>
            <option value="gagal" <?= $filter_status === 'gagal' ? 'selected' : '' ?>>❌ Gagal</option>
          </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
          <button type="submit" class="btn w-100" style="background:linear-gradient(90deg,var(--blue),var(--teal));color:white;font-weight:700;border-radius:12px;padding:10px;">
            <i class="bi bi-funnel-fill me-1"></i> Terapkan Filter
          </button>
          <a href="kelolaTransaksi.php" class="btn btn-light" style="border-radius:12px;padding:10px 16px;font-weight:600;">
            <i class="bi bi-arrow-counterclockwise"></i>
          </a>
        </div>
      </div>
    </form>

    <!-- TABEL TRANSAKSI -->
    <div class="table-card">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 style="font-weight:800;margin:0;">📋 Daftar Transaksi</h5>
        <small class="text-muted">Menampilkan data transaksi</small>
      </div>

      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Pengguna</th>
              <th>Tanggal</th>
              <th>Item / Paket</th>
              <th>Jumlah</th>
              <th>Status</th>
              <th style="text-align:right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $displayed = 0;
            if ($list_transaksi && mysqli_num_rows($list_transaksi) > 0): 
              while ($t = mysqli_fetch_assoc($list_transaksi)): 
                // Filter berdasarkan status efektif
                if ($filter_status !== 'semua' && $t['status_efektif'] !== $filter_status) continue;
                $displayed++;
                
                $inisial = strtoupper(mb_substr($t['nama_user'] ?? 'U', 0, 1));
                $status_efektif = $t['status_efektif'];
                $badge_class = 'sbadge-' . $status_efektif;
                
                // Tentukan item yang dibeli
                $item_nama = '';
                $item_paket = '';
                if (!empty($t['judul_iklan_lomba'])) {
                    $item_nama = $t['judul_iklan_lomba'];
                    $item_paket = $t['paket_lomba'];
                } elseif (!empty($t['judul_iklan_beasiswa'])) {
                    $item_nama = $t['judul_iklan_beasiswa'];
                    $item_paket = $t['paket_beasiswa'];
                } else {
                    $item_nama = 'Transaksi #' . $t['id_pembayaran'];
                    $item_paket = '-';
                }
                
                // Apakah status ini auto atau manual?
                $is_auto = ($t['sumber_status'] !== 'manual');
            ?>
              <tr>
                <td><strong>#<?= $t['id_pembayaran'] ?></strong></td>
                <td>
                  <div class="user-info">
                    <div class="user-avatar"><?= $inisial ?></div>
                    <div>
                      <p class="user-name"><?= htmlspecialchars($t['nama_user'] ?? 'User Tidak Diketahui') ?></p>
                      <p class="user-email"><?= htmlspecialchars($t['email_user'] ?? '-') ?></p>
                    </div>
                  </div>
                </td>
                <td>
                  <div style="font-weight:600;font-size:13px;"><?= date('d M Y', strtotime($t['tanggal_bayar'])) ?></div>
                  <div style="font-size:11px;color:var(--muted);"><?= date('H:i', strtotime($t['tanggal_bayar'])) ?> WIB</div>
                </td>
                <td>
                  <div style="font-weight:600;font-size:13px;"><?= htmlspecialchars($item_nama) ?></div>
                  <small class="text-muted"><?= htmlspecialchars($item_paket) ?></small>
                </td>
                <td><span class="amount">Rp <?= number_format($t['jumlah'], 0, ',', '.') ?></span></td>
                <td>
                  <span class="sbadge <?= $badge_class ?>"><?= ucfirst($status_efektif) ?></span>
                  <?php if ($is_auto): ?>
                    <span class="auto-badge" title="Status otomatis dari verifikasi iklan">AUTO</span>
                  <?php endif; ?>
                </td>
                <td style="text-align:right;">
                  <button class="btn-action btn-detail" onclick='bukaDetail(<?= json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                    <i class="bi bi-eye-fill"></i> Detail
                  </button>
                  <button class="btn-action btn-edit" onclick="bukaEditStatus(<?= $t['id_pembayaran'] ?>, '<?= $status_efektif ?>')" title="Override manual">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php endif; ?>
            
            <?php if ($displayed === 0): ?>
              <tr>
                <td colspan="7">
                  <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>Tidak ada transaksi ditemukan</h5>
                    <p class="mb-0">Coba ubah filter atau kata kunci pencarian Anda.</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- MODAL DETAIL -->
  <div class="modal-overlay" id="modalDetail">
    <div class="modal-box">
      <div class="modal-header">
        <h5 class="modal-title">📄 Detail Transaksi</h5>
        <button class="modal-close" onclick="tutupModal('modalDetail')">✕</button>
      </div>
      <div id="detailContent"></div>
    </div>
  </div>

  <!-- MODAL EDIT STATUS (OVERRIDE MANUAL) -->
  <div class="modal-overlay" id="modalEdit">
    <div class="modal-box" style="max-width:450px;">
      <div class="modal-header">
        <h5 class="modal-title">✏️ Override Status Manual</h5>
        <button class="modal-close" onclick="tutupModal('modalEdit')">✕</button>
      </div>
      <form method="POST">
        <input type="hidden" name="update_status" value="1">
        <input type="hidden" name="id_pembayaran" id="editIdPembayaran">
        
        <div class="alert alert-warning" style="border-radius:12px;font-size:13px;border:none;background:#fffbeb;color:#92400e;">
          <i class="bi bi-exclamation-triangle-fill me-1"></i>
          <strong>Perhatian:</strong> Status transaksi biasanya otomatis mengikuti verifikasi iklan. 
          Gunakan override ini hanya jika ada kondisi khusus yang membutuhkan penyesuaian manual.
        </div>
        
        <div class="mb-3">
          <label style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:8px;display:block;">Status Baru (Override)</label>
          <select name="status_baru" class="form-select" id="editStatus" style="border-radius:12px;padding:12px;font-weight:600;">
            <option value="pending">⏳ Menunggu Verifikasi</option>
            <option value="sukses">✅ Sukses / Dibayar</option>
            <option value="gagal">❌ Gagal / Ditolak</option>
          </select>
        </div>

        <div class="d-flex gap-2">
          <button type="button" class="btn btn-light flex-fill" onclick="tutupModal('modalEdit')" style="border-radius:12px;font-weight:600;">Batal</button>
          <button type="submit" class="btn flex-fill" style="background:linear-gradient(90deg,var(--blue),var(--teal));color:white;border-radius:12px;font-weight:700;">💾 Override Status</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function bukaDetail(data) {
      const statusClass = 'sbadge-' + data.status_efektif;
      const buktiUrl = data.bukti_pembayaran ? 'uploads/' + data.bukti_pembayaran : '';
      const isAuto = data.sumber_status !== 'manual';
      
      // Tentukan item
      let itemNama = 'Transaksi #' + data.id_pembayaran;
      let itemPaket = '-';
      if (data.judul_iklan_lomba) {
        itemNama = data.judul_iklan_lomba;
        itemPaket = data.paket_lomba;
      } else if (data.judul_iklan_beasiswa) {
        itemNama = data.judul_iklan_beasiswa;
        itemPaket = data.paket_beasiswa;
      }
      
      let html = `
        <div class="detail-row">
          <span class="detail-label">ID Transaksi</span>
          <span class="detail-value">#${data.id_pembayaran}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Pengguna</span>
          <span class="detail-value">${escapeHtml(data.nama_user || '-')}<br><small style="color:#6b7280;font-weight:400;">${escapeHtml(data.email_user || '-')}</small></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Tanggal Transaksi</span>
          <span class="detail-value">${formatTanggal(data.tanggal_bayar)}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Item / Paket</span>
          <span class="detail-value">${escapeHtml(itemNama)}<br><small style="color:#6b7280;font-weight:400;">${escapeHtml(itemPaket)}</small></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Metode Pembayaran</span>
          <span class="detail-value">${escapeHtml(data.metode_pembayaran || '-')}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Jumlah Dibayar</span>
          <span class="detail-value" style="color:#2f6df6;font-size:16px;">Rp ${formatRupiah(data.jumlah)}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Status</span>
          <span class="detail-value">
            <span class="sbadge ${statusClass}">${capitalize(data.status_efektif)}</span>
            ${isAuto ? '<span class="auto-badge">AUTO</span>' : ''}
          </span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Status Verifikasi Iklan</span>
          <span class="detail-value">
            ${data.status_verif_lomba ? 'Lomba: <strong>' + capitalize(data.status_verif_lomba) + '</strong>' : ''}
            ${data.status_verif_beasiswa ? 'Beasiswa: <strong>' + capitalize(data.status_verif_beasiswa) + '</strong>' : ''}
            ${!data.status_verif_lomba && !data.status_verif_beasiswa ? '<em class="text-muted">-</em>' : ''}
          </span>
        </div>
      `;
      
      if (buktiUrl) {
        html += `
          <div style="margin-top:20px;">
            <div class="detail-label mb-2">BUKTI PEMBAYARAN</div>
            <img src="${buktiUrl}" class="bukti-preview" alt="Bukti Pembayaran" onerror="this.src='';this.alt='Gambar tidak ditemukan';this.style.padding='40px';">
            <div class="text-center mt-2">
              <a href="${buktiUrl}" target="_blank" class="btn btn-sm" style="background:#dbeafe;color:#1d4ed8;border-radius:10px;font-weight:600;font-size:12px;">
                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Gambar Penuh
              </a>
            </div>
          </div>
        `;
      }
      
      document.getElementById('detailContent').innerHTML = html;
      document.getElementById('modalDetail').classList.add('active');
    }
    
    function bukaEditStatus(id, status) {
      document.getElementById('editIdPembayaran').value = id;
      document.getElementById('editStatus').value = status;
      document.getElementById('modalEdit').classList.add('active');
    }
    
    function tutupModal(id) {
      document.getElementById(id).classList.remove('active');
    }
    
    document.querySelectorAll('.modal-overlay').forEach(modal => {
      modal.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
      });
    });
    
    function formatRupiah(num) {
      return Number(num).toLocaleString('id-ID');
    }
    
    function formatTanggal(dateStr) {
      if (!dateStr) return '-';
      const d = new Date(dateStr);
      const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
      return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}, ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')} WIB`;
    }
    
    function capitalize(str) {
      return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    }
    
    function escapeHtml(str) {
      if (!str) return '';
      const div = document.createElement('div');
      div.textContent = str;
      return div.innerHTML;
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
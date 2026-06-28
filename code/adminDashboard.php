<?php
include 'penghubung.php';

// Proteksi admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: halamanLoginAdmin.php");
    exit();
}

// Ambil data admin dari database jika session email belum ada
if (!isset($_SESSION['admin_email'])) {
    $id_admin = $_SESSION['id_admin'] ?? 1;
    $q_admin = mysqli_query($conn, "SELECT email FROM admin WHERE id_admin = $id_admin LIMIT 1");
    if ($data_admin = mysqli_fetch_assoc($q_admin)) {
        $_SESSION['admin_email'] = $data_admin['email'];
    }
}

// PROSES UPDATE VERIFIKASI IKLAN
if (isset($_GET['action']) && isset($_GET['id_verif']) && isset($_GET['tipe'])) {
    $id_iklan = (int)$_GET['id_verif'];
    $action = $_GET['action'];
    $tipe = $_GET['tipe'];
    $status_baru = ($action == 'setujui') ? 'disetujui' : 'ditolak';
    $tabel_target = ($tipe == 'beasiswa') ? 'iklan_beasiswa' : 'iklan_lomba';
    
    $stmt = $conn->prepare("UPDATE $tabel_target SET status_verifikasi = ? WHERE id_iklan = ?");
    $stmt->bind_param("si", $status_baru, $id_iklan);
    $stmt->execute();
    $stmt->close();
    
    header("Location: adminDashboard.php?pesan=sukses_verif");
    exit();
}

// STATISTIK
function getCount($conn, $table, $where = "") {
    $sql = "SELECT COUNT(*) as total FROM $table";
    if ($where != "") $sql .= " WHERE $where";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    return $data['total'] ?? 0;
}

$total_lomba = getCount($conn, 'lomba');
$total_beasiswa = getCount($conn, 'beasiswa');
$total_tempat = getCount($conn, 'tempat_edukatif');
$total_user = getCount($conn, 'user');

$menunggu_lomba = getCount($conn, "iklan_lomba", "status_verifikasi = 'menunggu'");
$menunggu_beasiswa = getCount($conn, "iklan_beasiswa", "status_verifikasi = 'menunggu'");
$total_menunggu = $menunggu_lomba + $menunggu_beasiswa;

// Query Verifikasi Iklan Gabungan
$query_verif = "
    (SELECT i.id_iklan, i.judul_iklan, i.paket_langganan, i.status_verifikasi, 
            p.jumlah AS jumlah_bayar, 'lomba' AS tipe
     FROM iklan_lomba i 
     JOIN pembayaran p ON i.id_pembayaran = p.id_pembayaran 
     WHERE i.status_verifikasi = 'menunggu')
    UNION
    (SELECT i.id_iklan, i.judul_iklan, i.paket_langganan, i.status_verifikasi, 
            p.jumlah AS jumlah_bayar, 'beasiswa' AS tipe
     FROM iklan_beasiswa i 
     JOIN pembayaran p ON i.id_pembayaran = p.id_pembayaran 
     WHERE i.status_verifikasi = 'menunggu')
    ORDER BY id_iklan DESC
    LIMIT 10";
$list_verif = mysqli_query($conn, $query_verif);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    :root {
      --blue: #2f6df6; --teal: #35c7b6; --dark: #14213d; --muted: #6b7280;
      --soft-bg: #eef8fb; --card: #ffffff; --shadow: 0 18px 45px rgba(20, 33, 61, 0.10); --border: #e2e8f0;
    }
    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
    body { background: linear-gradient(180deg, #eef8fb 0%, #f7fcfb 100%); color: var(--dark); min-height: 100vh; }
    
    .admin-wrapper { display: flex; min-height: 100vh; }
    
    /* SIDEBAR */
    .sidebar { 
      width: 270px; 
      background: #ffffff; 
      border-right: 1px solid var(--border); 
      padding: 26px 20px; 
      position: fixed; 
      height: 100vh; 
      box-shadow: 8px 0 30px rgba(20, 33, 61, 0.05); 
      display: flex;
      flex-direction: column;
      overflow-y: auto;
    }
    .logo-text { font-weight: 800; font-size: 22px; color: var(--blue); margin-bottom: 24px; }
    .menu-label { font-size: 11px; color: var(--muted); font-weight: 700; margin-top: 16px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .menu-item { 
      display: flex; 
      align-items: center; 
      gap: 12px; 
      padding: 11px 14px; 
      border-radius: 12px; 
      color: #334155; 
      text-decoration: none; 
      font-size: 13.5px; 
      font-weight: 600; 
      margin-bottom: 4px; 
      transition: 0.25s ease; 
      cursor: pointer; 
    }
    .menu-item:hover, .menu-item.active { 
      background: linear-gradient(90deg, var(--blue), var(--teal)); 
      color: white; 
      transform: translateX(4px); 
    }
    .menu-toggle { display: flex; align-items: center; justify-content: space-between; }
    .menu-toggle .chevron { font-size: 11px; transition: transform .2s ease; }
    .menu-toggle[aria-expanded="true"] .chevron { transform: rotate(180deg); }
    .submenu { display: flex; flex-direction: column; padding-left: 16px; margin-bottom: 4px; }
    .submenu-item { font-size: 12.5px; padding: 9px 14px; }
    .logout { 
      background: #fee2e2; 
      color: #b91c1c; 
      text-align: center; 
      justify-content: center; 
      margin-top: auto; 
      margin-bottom: 10px; 
    }
    .logout:hover { 
      background: #fca5a5; 
      color: #b91c1c; 
      transform: none; 
    }
    
    /* MAIN CONTENT */
    .main-content { margin-left: 270px; width: calc(100% - 270px); padding: 34px; }
    .topbar { 
      background: white; 
      border-radius: 22px; 
      padding: 22px 26px; 
      box-shadow: var(--shadow); 
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
      margin-bottom: 28px; 
      gap: 20px; 
      flex-wrap: wrap; 
    }
    .page-title { 
      font-size: 30px; 
      font-weight: 800; 
      margin: 0; 
      background: linear-gradient(90deg, var(--blue), var(--teal)); 
      -webkit-background-clip: text; 
      -webkit-text-fill-color: transparent; 
    }
    .page-desc { color: var(--muted); font-size: 14px; margin: 4px 0 0; }
    .admin-profile { 
      display: flex; 
      align-items: center; 
      gap: 12px; 
      background: #f8fafc; 
      border-radius: 999px; 
      padding: 8px 14px; 
    }
    .avatar { 
      width: 38px; 
      height: 38px; 
      border-radius: 50%; 
      background: linear-gradient(90deg, var(--blue), var(--teal)); 
      color: white; 
      display: flex; 
      align-items: center; 
      justify-content: center; 
      font-weight: 800; 
    }
    
    /* STAT CARDS */
    .stat-card { 
      background: white; 
      border-radius: 20px; 
      border: 1px solid #eef2f7; 
      box-shadow: var(--shadow); 
      padding: 24px; 
      transition: 0.25s ease; 
      height: 100%; 
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-icon { 
      width: 48px; 
      height: 48px; 
      border-radius: 16px; 
      background: linear-gradient(135deg, #dbeafe, #ccfbf1); 
      display: flex; 
      align-items: center; 
      justify-content: center; 
      font-size: 24px; 
      margin-bottom: 14px; 
    }
    .stat-number { font-size: 30px; font-weight: 800; color: var(--blue); }
    .stat-label { color: var(--muted); font-size: 13px; margin: 0; }
    
    /* CONTENT CARD */
    .content-card { 
      background: white; 
      border-radius: 20px; 
      border: 1px solid #eef2f7; 
      box-shadow: var(--shadow); 
      padding: 26px; 
      margin-top: 28px; 
    }
    .section-header { 
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
      gap: 18px; 
      flex-wrap: wrap; 
      margin-bottom: 20px; 
    }
    .section-title { font-size: 20px; font-weight: 800; margin: 0; }
    
    /* TABLE */
    .table { vertical-align: middle; font-size: 14px; }
    .table thead th { 
      background: #f8fafc; 
      color: #475569; 
      font-size: 12px; 
      text-transform: uppercase; 
      border-bottom: none; 
      padding: 14px; 
    }
    .table tbody td { padding: 16px 14px; color: #334155; }
    
    /* BADGES */
    .badge-status { padding: 7px 11px; border-radius: 999px; font-size: 11px; font-weight: 800; }
    .status-review { background: #fef3c7; color: #b45309; }
    
    /* ACTION BUTTONS */
    .action-btn { 
      border: none; 
      border-radius: 10px; 
      padding: 7px 10px; 
      font-size: 12px; 
      font-weight: 700; 
      margin-right: 6px; 
      text-decoration: none; 
      display: inline-block;
    }
    .approve-btn { background: #dcfce7; color: #15803d; }
    .approve-btn:hover { background: #bbf7d0; color: #15803d; }
    .delete-btn { background: #fee2e2; color: #b91c1c; }
    .delete-btn:hover { background: #fecaca; color: #b91c1c; }
    
    /* QUICK NOTE */
    .quick-note { 
      background: #effffb; 
      border: 1px solid var(--teal); 
      border-radius: 18px; 
      padding: 18px; 
      color: #087f6f; 
      font-size: 14px; 
      font-weight: 600; 
      margin-top: 24px; 
    }
    
    @media (max-width: 991px) { 
      .sidebar { position: static; width: 100%; height: auto; } 
      .logout { margin-top: 20px; } 
      .admin-wrapper { flex-direction: column; } 
      .main-content { margin-left: 0; width: 100%; padding: 20px; } 
    }
  </style>
</head>
<body>

  <div class="admin-wrapper">
    <aside class="sidebar">
      <div class="logo-text">19JutaAdmin</div>
      
      <div class="menu-label">Navigasi Utama</div>
      <a href="adminDashboard.php" class="menu-item active">📊 Dashboard</a>
      
      <div class="menu-label">Manajemen Data</div>
      <a href="halamanKelolaLomba.php" class="menu-item">🏆 Kelola Lomba</a>
      <a href="halamanKelolaBeasiswa.php" class="menu-item">🎓 Kelola Beasiswa</a>
      <a href="#" class="menu-item menu-toggle" data-bs-toggle="collapse" data-bs-target="#submenuTempat" role="button" aria-expanded="false">
        <span>📍 Kelola Tempat / Peta</span>
        <span class="chevron">▾</span>
      </a>
      <div class="collapse submenu" id="submenuTempat">
        <a href="halamanKelolaTempat.php" class="menu-item submenu-item">📋 Daftar Tempat</a>
        <a href="HalamanVerifikasiTempat.php" class="menu-item submenu-item">✅ Verifikasi Pengajuan</a>
      </div>
      <a href="halamanKelolaFiturBeranda.php" class="menu-item">🏠 Kelola Fitur Beranda</a>
      <a href="halamanKelolaBlog.php" class="menu-item">📝 Kelola Blog</a>
      
      <a href="kelolaTransaksi.php" class="menu-item">💳 Kelola Transaksi</a>
      
      <div class="menu-label">Sistem Validasi</div>
      <a href="halamanVerifikasi.php" class="menu-item" id="menu-verif-sidebar">
        ✅ Verifikasi Iklan 
        <span id="badge-notif" class="badge bg-danger ms-auto" style="display: none; font-size: 11px; border-radius: 50%;">0</span>
      </a>
      
      <a href="penghubung.php?aksi=logout_admin" class="menu-item logout">🚪 Logout</a>
    </aside>

    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 class="page-title">Admin Dashboard</h1>
          <p class="page-desc">Selamat datang! Pantau aktivitas platform dari sini.</p>
        </div>
        <div class="admin-profile">
          <div class="avatar">
            <?= strtoupper(mb_substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
          </div>
          <div>
            <div class="fw-bold"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
            <small class="text-muted"><?= htmlspecialchars($_SESSION['admin_email'] ?? 'admin@19jutapendidikan.id') ?></small>
          </div>
        </div>
      </div>

      <?php if(isset($_GET['pesan'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            ⚡ Tindakan berhasil diproses!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- STATISTIK -->
      <div class="row g-4">
        <div class="col-md-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon">🏆</div>
            <div class="stat-number"><?= $total_lomba ?></div>
            <p class="stat-label">Total Lomba</p>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon">🎓</div>
            <div class="stat-number"><?= $total_beasiswa ?></div>
            <p class="stat-label">Total Beasiswa</p>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon">📍</div>
            <div class="stat-number"><?= $total_tempat ?></div>
            <p class="stat-label">Tempat Edukatif</p>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-number text-warning"><?= $total_menunggu ?></div>
            <p class="stat-label">Menunggu Verifikasi</p>
          </div>
        </div>
      </div>

      <div class="quick-note">
        <i class="fas fa-info-circle me-2"></i>
        Sistem berjalan normal. Saat ini terdapat <strong><?= $total_menunggu ?></strong> pengajuan iklan baru (Lomba & Beasiswa) yang membutuhkan verifikasi.
      </div>

      <!-- VERIFIKASI CEPAT -->
      <div class="content-card">
        <div class="section-header">
          <h2 class="section-title">⚡ Verifikasi Cepat Antrean Promosi</h2>
          <a href="halamanVerifikasi.php" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua →</a>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Judul Iklan</th>
                <th>Paket</th>
                <th>Pembayaran</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($list_verif) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($list_verif)): ?>
                <tr>
                  <td>
                    <?= htmlspecialchars($row['judul_iklan']); ?>
                    <span class="badge <?= $row['tipe'] == 'lomba' ? 'bg-primary' : 'bg-info text-dark' ?> ms-1" style="font-size: 10px;">
                      <?= ucfirst($row['tipe']) ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($row['paket_langganan']); ?></td>
                  <td>Rp<?= number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                  <td><span class="badge-status status-review">Menunggu</span></td>
                  <td>
                    <a href="?action=setujui&id_verif=<?= $row['id_iklan']; ?>&tipe=<?= $row['tipe']; ?>" 
                       class="action-btn approve-btn"
                       onclick="return confirm('Setujui iklan ini?')">✓ Setujui</a>
                    <a href="?action=tolak&id_verif=<?= $row['id_iklan']; ?>&tipe=<?= $row['tipe']; ?>" 
                       class="action-btn delete-btn"
                       onclick="return confirm('Tolak iklan ini?')">✗ Tolak</a>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    🎉 Tidak ada iklan yang menunggu verifikasi.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let lastPendingCount = <?= $total_menunggu ?>; 

    function periksaNotifikasi() {
        fetch('penghubung.php?aksi=cek_notif')
            .then(response => response.json())
            .then(data => {
                let currentPending = data.total_pending;
                let badge = document.getElementById('badge-notif');

                if (currentPending > 0) {
                    badge.textContent = currentPending;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
                lastPendingCount = currentPending;
            })
            .catch(error => console.error('Gagal mengambil notifikasi:', error));
    }

    periksaNotifikasi();          
    setInterval(periksaNotifikasi, 5000); 
  </script>
</body>
</html>
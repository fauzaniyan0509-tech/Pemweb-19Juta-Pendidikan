<?php
session_start();

// 1. KONEKSI KE DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "19juta_pendidikan"; 

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. PROSES UPDATE VERIFIKASI IKLAN (SETUJUI / TOLAK)
if (isset($_GET['action']) && isset($_GET['id_verif'])) {
    $id_iklan = $_GET['id_verif'];
    $action = $_GET['action'];
    $status_baru = ($action == 'setujui') ? 'disetujui' : 'ditolak';

    $stmt = $conn->prepare("UPDATE iklan_lomba SET status_verifikasi = ? WHERE id_iklan = ?");
    $stmt->bind_param("si", $status_baru, $id_iklan);
    $stmt->execute();
    
    header("Location: adminDashboard.php?pesan=sukses_verif");
    exit();
}

// 3. PROSES TAMBAH DATA (LOMBA / BEASISWA / TEMPAT) VIA MODAL
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_tipe'])) {
    // KODE YANG BENAR:
$tipe = $_POST['tambah_tipe'];
$nama = mysqli_real_escape_string($conn, $_POST['nama']);
$kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
$deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    if ($tipe == 'lomba') {
        // Sesuaikan kolom tabel lomba Anda (contoh default minimalis)
        $query = "INSERT INTO lomba (judul_lomba, kategori, deskripsi, deadline) VALUES ('$nama', '$kategori', '$deskripsi', NOW())";
    } elseif ($tipe == 'beasiswa') {
        $query = "INSERT INTO beasiswa (nama_beasiswa, jenjang, deskripsi) VALUES ('$nama', '$kategori', '$deskripsi')";
    } elseif ($tipe == 'tempat') {
        $query = "INSERT INTO tempat_edukatif (nama_tempat, kategori, deskripsi) VALUES ('$nama', '$kategori', '$deskripsi')";
    }
    
    if (mysqli_query($conn, $query)) {
        header("Location: adminDashboard.php?pesan=sukses_tambah");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit();
}

// 4. LOGIKA PENGAMBILAN DATA STATISTIK
function getCount($conn, $table, $where = "") {
    $sql = "SELECT COUNT(*) as total FROM $table";
    if ($where != "") { $sql .= " WHERE $where"; }
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    return $data['total'];
}

$total_lomba = getCount($conn, 'lomba');
$total_beasiswa = getCount($conn, 'beasiswa');
$total_tempat = getCount($conn, 'tempat_edukatif');
$total_menunggu = getCount($conn, "iklan_lomba", "status_verifikasi = 'menunggu'");

// 5. QUERY LIST DATA UNTUK TABEL-TABEL
$list_lomba = mysqli_query($conn, "SELECT * FROM lomba ORDER BY id_lomba DESC");
$list_beasiswa = mysqli_query($conn, "SELECT * FROM beasiswa ORDER BY id_beasiswa DESC"); 
$list_tempat = mysqli_query($conn, "SELECT * FROM tempat_edukatif ORDER BY id_tempat DESC");

// Query Verifikasi Iklan
$query_verif = "SELECT i.id_iklan, i.judul_iklan, i.paket_langganan, i.status_verifikasi, l.judul_lomba, p.jumlah AS jumlah_bayar
                FROM iklan_lomba i 
                JOIN lomba l ON i.id_lomba = l.id_lomba 
                JOIN pembayaran p ON i.id_pembayaran = p.id_pembayaran 
                WHERE i.status_verifikasi = 'menunggu' ORDER BY i.id_iklan DESC";
$list_verif = mysqli_query($conn, $query_verif);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    :root {
      --blue: #2f6df6; --teal: #35c7b6; --dark: #14213d; --muted: #6b7280;
      --soft-bg: #eef8fb; --card: #ffffff; --shadow: 0 18px 45px rgba(20, 33, 61, 0.10); --border: #e2e8f0;
    }
    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
    body { background: linear-gradient(180deg, #eef8fb 0%, #f7fcfb 100%); color: var(--dark); min-height: 100vh; }
    .admin-wrapper { display: flex; min-height: 100vh; }
    .sidebar { width: 270px; background: #ffffff; border-right: 1px solid var(--border); padding: 26px 20px; position: fixed; height: 100vh; box-shadow: 8px 0 30px rgba(20, 33, 61, 0.05); }
    .logo-text { font-weight: 800; font-size: 22px; color: var(--blue); margin-bottom: 36px; }
    .menu-label { font-size: 12px; color: var(--muted); font-weight: 700; margin-bottom: 12px; text-transform: uppercase; }
    .menu-item { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border-radius: 14px; color: #334155; text-decoration: none; font-size: 14px; font-weight: 600; margin-bottom: 8px; transition: 0.25s ease; cursor: pointer; }
    .menu-item:hover, .menu-item.active { background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; transform: translateX(4px); }
    .logout { position: absolute; bottom: 24px; left: 20px; right: 20px; background: #fee2e2; color: #b91c1c; text-align: center; }
    .main-content { margin-left: 270px; width: calc(100% - 270px); padding: 34px; }
    .topbar { background: white; border-radius: 22px; padding: 22px 26px; box-shadow: var(--shadow); display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; gap: 20px; flex-wrap: wrap; }
    .page-title { font-size: 30px; font-weight: 800; margin: 0; background: linear-gradient(90deg, var(--blue), var(--teal)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .page-desc { color: var(--muted); font-size: 14px; margin: 4px 0 0; }
    .admin-profile { display: flex; align-items: center; gap: 12px; background: #f8fafc; border-radius: 999px; padding: 8px 14px; }
    .avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; }
    .stat-card, .content-card { background: white; border-radius: 20px; border: 1px solid #eef2f7; box-shadow: var(--shadow); }
    .stat-card { padding: 24px; transition: 0.25s ease; height: 100%; }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-icon { width: 48px; height: 48px; border-radius: 16px; background: linear-gradient(135deg, #dbeafe, #ccfbf1); display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 14px; }
    .stat-number { font-size: 30px; font-weight: 800; color: var(--blue); }
    .stat-label { color: var(--muted); font-size: 13px; margin: 0; }
    .content-card { padding: 26px; margin-top: 28px; }
    .section-header { display: flex; justify-content: space-between; align-items: center; gap: 18px; flex-wrap: wrap; margin-bottom: 20px; }
    .section-title { font-size: 20px; font-weight: 800; margin: 0; }
    .btn-gradient { background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; border: none; border-radius: 12px; padding: 10px 16px; font-weight: 700; font-size: 14px; }
    .table { vertical-align: middle; font-size: 14px; }
    .table thead th { background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase; border-bottom: none; padding: 14px; }
    .table tbody td { padding: 16px 14px; color: #334155; }
    .badge-status { padding: 7px 11px; border-radius: 999px; font-size: 11px; font-weight: 800; }
    .status-active { background: #dcfce7; color: #15803d; }
    .status-review { background: #fef3c7; color: #b45309; }
    .status-rejected { background: #fee2e2; color: #b91c1c; }
    .action-btn { border: none; border-radius: 10px; padding: 7px 10px; font-size: 12px; font-weight: 700; margin-right: 6px; text-decoration: none; }
    .edit-btn { background: #dbeafe; color: #1d4ed8; }
    .delete-btn { background: #fee2e2; color: #b91c1c; }
    .approve-btn { background: #dcfce7; color: #15803d; }
    .tab-content-section { display: none; }
    .tab-content-section.active { display: block; }
    .quick-note { background: #effffb; border: 1px solid var(--teal); border-radius: 18px; padding: 18px; color: #087f6f; font-size: 14px; font-weight: 600; margin-top: 24px; }
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); z-index: 9999; align-items: center; justify-content: center; padding: 20px; }
    .modal-box { background: white; border-radius: 22px; padding: 28px; width: 100%; max-width: 560px; box-shadow: var(--shadow); animation: pop 0.25s ease; }
    @keyframes pop { from { transform: scale(0.94); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .form-control, .form-textarea { border-radius: 12px; padding: 11px 13px; font-size: 14px; width: 100%; border: 1px solid var(--border); margin-bottom: 15px; }
    @media (max-width: 991px) { .sidebar { position: static; width: 100%; height: auto; } .logout { position: static; margin-top: 20px; } .admin-wrapper { flex-direction: column; } .main-content { margin-left: 0; width: 100%; padding: 20px; } }
  </style>
</head>
<body>

  <div class="admin-wrapper">
    <aside class="sidebar">
      <div class="logo-text">19JutaAdmin</div>
      <div class="menu-label">Menu Admin</div>
      <a class="menu-item active" onclick="showSection('dashboard', this)">📊 Dashboard</a>
      <a class="menu-item" onclick="showSection('lomba', this)" href="halamankelolaLomba.php">🏆 Kelola Lomba</a>
      <a class="menu-item" onclick="showSection('beasiswa', this)">🎓 Kelola Beasiswa</a>
      <a class="menu-item" onclick="showSection('tempat', this)">📍 Kelola Tempat</a>
      <a class="menu-item" onclick="showSection('verifikasi', this)" href="halamanVerifikasi.php">✅ Verifikasi Iklan</a>
      <a href="logout.php" class="menu-item logout">🚪 Logout</a>
    </aside>

    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 class="page-title">Admin Dashboard</h1>
          <p class="page-desc">Kelola data lomba, beasiswa, tempat edukatif, dan verifikasi publikasi.</p>
        </div>
        <div class="admin-profile">
          <div class="avatar">A</div>
          <div>
            <div class="fw-bold">Admin</div>
            <small class="text-muted">admin@19jutapendidikan.id</small>
          </div>
        </div>
      </div>

      <?php if(isset($_GET['pesan'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            ⚡ Tindakan berhasil diproses ke database!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <section id="dashboard" class="tab-content-section active">
        <div class="row g-4">
          <div class="col-md-6 col-xl-3"><div class="stat-card"><div class="stat-icon">🏆</div><div class="stat-number"><?= $total_lomba ?></div><p class="stat-label">Total Lomba</p></div></div>
          <div class="col-md-6 col-xl-3"><div class="stat-card"><div class="stat-icon">🎓</div><div class="stat-number"><?= $total_beasiswa ?></div><p class="stat-label">Total Beasiswa</p></div></div>
          <div class="col-md-6 col-xl-3"><div class="stat-card"><div class="stat-icon">📍</div><div class="stat-number"><?= $total_tempat ?></div><p class="stat-label">Tempat Edukatif</p></div></div>
          <div class="col-md-6 col-xl-3"><div class="stat-card"><div class="stat-icon">⏳</div><div class="stat-number text-warning"><?= $total_menunggu ?></div><p class="stat-label">Menunggu Verifikasi</p></div></div>
        </div>
        <div class="quick-note">
          Sistem berjalan normal. Hari ini terdapat <?= $total_menunggu ?> pengajuan iklan baru yang membutuhkan konfirmasi verifikasi.
        </div>
      </section>

      <section id="lomba" class="tab-content-section">
        <div class="content-card">
          <div class="section-header">
            <h2 class="section-title">Kelola Data Lomba</h2>
            <button class="btn-gradient" onclick="openModal('Tambah Lomba', 'lomba')">+ Tambah Lomba</button>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead><tr><th>Judul Lomba</th><th>Kategori</th><th>Deskripsi</th></tr></thead>
              <tbody>
                <?php while($l = mysqli_fetch_assoc($list_lomba)): ?>
                <tr>
                  <td><?= htmlspecialchars($l['judul_lomba']) ?></td>
                  <td><span class="badge bg-primary rounded-pill"><?= htmlspecialchars($l['kategori'] ?? 'Umum') ?></span></td>
                  <td><?= substr(htmlspecialchars($l['deskripsi'] ?? '-'), 0, 60) ?>...</td>
                </tr>
                <?php endwhile; if(mysqli_num_rows($list_lomba) == 0) echo "<tr><td colspan='3' class='text-center text-muted'>Belum ada data lomba.</td></tr>"; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <section id="beasiswa" class="tab-content-section">
        <div class="content-card">
          <div class="section-header">
            <h2 class="section-title">Kelola Data Beasiswa</h2>
            <button class="btn-gradient" onclick="openModal('Tambah Beasiswa', 'beasiswa')">+ Tambah Beasiswa</button>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead><tr><th>Nama Beasiswa</th><th>Jenjang</th><th>Deskripsi</th></tr></thead>
              <tbody>
                <?php while($b = mysqli_fetch_assoc($list_beasiswa)): ?>
                <tr>
                  <td><?= htmlspecialchars($b['nama_beasiswa'] ?? $b['judul_beasiswa']) ?></td>
                  <td><?= htmlspecialchars($b['jenjang'] ?? '-') ?></td>
                  <td><?= substr(htmlspecialchars($b['deskripsi'] ?? '-'), 0, 60) ?>...</td>
                </tr>
                <?php endwhile; if(mysqli_num_rows($list_beasiswa) == 0) echo "<tr><td colspan='3' class='text-center text-muted'>Belum ada data beasiswa.</td></tr>"; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <section id="tempat" class="tab-content-section">
        <div class="content-card">
          <div class="section-header">
            <h2 class="section-title">Kelola Tempat Edukatif</h2>
            <button class="btn-gradient" onclick="openModal('Tambah Tempat Edukatif', 'tempat')">+ Tambah Tempat</button>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead><tr><th>Nama Tempat</th><th>Kategori</th><th>Deskripsi</th></tr></thead>
              <tbody>
                <?php while($t = mysqli_fetch_assoc($list_tempat)): ?>
                <tr>
                  <td><?= htmlspecialchars($t['nama_tempat']) ?></td>
                  <td><?= htmlspecialchars($t['kategori'] ?? '-') ?></td>
                  <td><?= substr(htmlspecialchars($t['deskripsi'] ?? '-'), 0, 60) ?>...</td>
                </tr>
                <?php endwhile; if(mysqli_num_rows($list_tempat) == 0) echo "<tr><td colspan='3' class='text-center text-muted'>Belum ada data tempat.</td></tr>"; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <section id="verifikasi" class="tab-content-section">
        <div class="content-card">
          <div class="section-header"><h2 class="section-title">Verifikasi Iklan Lomba</h2></div>
          <div class="table-responsive">
            <table class="table">
              <thead><tr><th>Judul Iklan</th><th>Paket</th><th>Pembayaran</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <?php while ($row = mysqli_fetch_assoc($list_verif)): ?>
                <tr>
                  <td><?= htmlspecialchars($row['judul_iklan']); ?></td>
                  <td><?= htmlspecialchars($row['paket_langganan']); ?></td>
                  <td>Rp<?= number_format($row['jumlah_bayar']); ?></td>
                  <td><span class="badge-status status-review">Menunggu</span></td>
                  <td>
                    <a href="?action=setujui&id_verif=<?= $row['id_iklan']; ?>" class="action-btn approve-btn">Setujui</a>
                    <a href="?action=tolak&id_verif=<?= $row['id_iklan']; ?>" class="action-btn delete-btn">Tolak</a>
                  </td>
                </tr>
                <?php endwhile; if (mysqli_num_rows($list_verif) == 0): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada iklan yang menunggu verifikasi.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>

  <div class="modal-overlay" id="adminModal">
    <div class="modal-box">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0" id="modalTitle">Tambah Data</h4>
        <button class="btn btn-light rounded-circle" onclick="closeModal()">×</button>
      </div>
      <form action="" method="POST">
        <input type="hidden" name="tambah_tipe" id="tambahTipe">
        
        <div class="mb-2">
          <label class="form-label font-weight-600">Nama / Judul Data</label>
          <input type="text" name="nama" class="form-control" placeholder="Masukkan nama atau judul" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Kategori / Jenjang</label>
          <input type="text" name="kategori" class="form-control" placeholder="Contoh: Essay, S1, Perpus" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Deskripsi Lengkap</label>
          <textarea name="deskripsi" class="form-textarea" rows="4" placeholder="Tulis deskripsi penjelasan di sini..." required></textarea>
        </div>
        <button type="submit" class="btn-gradient w-100">Simpan Ke Database</button>
      </form>
    </div>
  </div>

  <script>
    function showSection(sectionId, element) {
      const sections = document.querySelectorAll('.tab-content-section');
      const menus = document.querySelectorAll('.menu-item');

      sections.forEach(section => section.classList.remove('active'));
      menus.forEach(menu => menu.classList.remove('active'));

      document.getElementById(sectionId).classList.add('active');
      element.classList.add('active');
    }

    function openModal(title, tipe) {
      document.getElementById('modalTitle').textContent = title;
      document.getElementById('tambahTipe').value = tipe;
      document.getElementById('adminModal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('adminModal').style.display = 'none';
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
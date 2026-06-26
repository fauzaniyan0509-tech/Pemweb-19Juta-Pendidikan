<?php
// =========================================================
// ProfilPengguna.php
// Halaman profil user — data diambil dari SESSION & DATABASE
// =========================================================
include 'penghubung.php';

// Proteksi: wajib login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== 'sudah_login') {
    header("Location: halamanLogin.php?pesan=belum_login");
    exit();
}

$id_user = (int) $_SESSION['id_user'];

// ── 1. PROSES SIMPAN EDIT PROFIL ──────────────────────────
$pesan_sukses = '';
$pesan_error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_profil'])) {
    $nama_baru  = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email_baru = mysqli_real_escape_string($conn, trim($_POST['email']));

    if (empty($nama_baru) || empty($email_baru)) {
        $pesan_error = 'Nama dan Email tidak boleh kosong.';
    } else {
        // Cek email duplikat (selain user ini sendiri)
        $cek = mysqli_query($conn, "SELECT id_user FROM user WHERE email = '$email_baru' AND id_user != $id_user");
        if (mysqli_num_rows($cek) > 0) {
            $pesan_error = 'Email sudah dipakai akun lain.';
        } else {
            // Proses upload foto baru jika ada
            $foto_baru = $_SESSION['foto_profil'] ?? ''; // default: foto lama
            if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
                $ext_foto     = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
                $allowed_ext  = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($ext_foto, $allowed_ext)) {
                    $nama_file_baru = 'profil_' . $id_user . '_' . time() . '.' . $ext_foto;
                    if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], 'uploads/' . $nama_file_baru)) {
                        $foto_baru = $nama_file_baru;
                    }
                }
            }

            $foto_esc = mysqli_real_escape_string($conn, $foto_baru);
            mysqli_query($conn, "UPDATE user SET nama='$nama_baru', email='$email_baru', foto_profil='$foto_esc' WHERE id_user=$id_user");

            // Update session agar navbar langsung berubah
            $_SESSION['nama']        = $nama_baru;
            $_SESSION['email']       = $email_baru;
            $_SESSION['foto_profil'] = $foto_baru;

            $pesan_sukses = 'Profil berhasil diperbarui!';
        }
    }
}

// ── 2. AMBIL DATA USER TERBARU DARI DB ────────────────────
$q_user = mysqli_query($conn, "SELECT * FROM user WHERE id_user = $id_user LIMIT 1");
$user   = mysqli_fetch_assoc($q_user);

$nama_tampil  = htmlspecialchars($user['nama'] ?? $_SESSION['nama'] ?? 'Pengguna');
$email_tampil = htmlspecialchars($user['email'] ?? $_SESSION['email'] ?? '-');
$foto_file    = !empty($user['foto_profil']) ? 'uploads/' . $user['foto_profil'] : '';

// ── 3. RIWAYAT PENGAJUAN LOMBA USER ──────────────────────
$q_lomba = mysqli_query($conn,
    "SELECT l.judul_lomba, il.judul_iklan, il.paket_langganan, il.status_verifikasi, p.jumlah, p.tanggal_bayar
     FROM iklan_lomba il
     JOIN lomba l ON il.id_lomba = l.id_lomba
     JOIN pembayaran p ON il.id_pembayaran = p.id_pembayaran
     WHERE il.id_user = $id_user
     ORDER BY p.tanggal_bayar DESC
     LIMIT 5"
);

// ── 4. RIWAYAT PENGAJUAN BEASISWA USER ───────────────────
$q_beasiswa = mysqli_query($conn,
    "SELECT b.nama_beasiswa, ib.judul_iklan, ib.paket_langganan, ib.status_verifikasi, p.jumlah, p.tanggal_bayar
     FROM iklan_beasiswa ib
     JOIN beasiswa b ON ib.id_beasiswa = b.id_beasiswa
     JOIN pembayaran p ON ib.id_pembayaran = p.id_pembayaran
     WHERE ib.id_user = $id_user
     ORDER BY p.tanggal_bayar DESC
     LIMIT 5"
);

// ── 5. HITUNG TOTAL PENGAJUAN ─────────────────────────────
$total_lomba    = mysqli_num_rows($q_lomba);
$total_beasiswa = $q_beasiswa ? mysqli_num_rows($q_beasiswa) : 0;

// Reset pointer agar bisa di-loop lagi
mysqli_data_seek($q_lomba, 0);
if ($q_beasiswa) mysqli_data_seek($q_beasiswa, 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Pengguna - 19JutaPendidikan</title>
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

    /* NAVBAR */
    .navbar { background:#fff; box-shadow:0 2px 12px rgba(0,0,0,.04); }
    .logo-text { font-weight:800; color:var(--blue); text-decoration:none; font-size:20px; }
    .nav-link { font-size:14px; color:#1f2937; font-weight:500; }
    .nav-link:hover { color:var(--blue); }
    .dropdown-menu { border:none; border-radius:14px; padding:8px; box-shadow:0 12px 30px rgba(20,33,61,.12); min-width:190px; }
    .dropdown-item { border-radius:10px; padding:10px 14px; font-size:14px; font-weight:600; }
    .dropdown-item:hover { background:linear-gradient(90deg,var(--blue),var(--teal)); color:white; }
    .btn-gradient { background:linear-gradient(90deg,var(--blue),var(--teal)); color:white; border:none; border-radius:999px; font-weight:600; padding:9px 20px; text-decoration:none; }
    .foto-profil-nav { width:40px; height:40px; border-radius:50%; object-fit:cover; }

    /* PAGE */
    .page-title { font-weight:800; font-size:34px; background:linear-gradient(90deg,var(--blue),var(--teal)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .page-subtitle { color:var(--muted); font-size:15px; }

    /* CARD */
    .card-custom { background:var(--card); border-radius:20px; box-shadow:var(--shadow); border:1px solid #eef2f7; }

    /* PROFILE CARD */
    .profile-card { padding:0 0 28px; overflow:hidden; }
    .profile-cover { height:88px; background:linear-gradient(90deg,var(--blue),var(--teal)); border-radius:20px 20px 0 0; }
    .avatar-wrap { padding:0 28px; margin-top:-52px; display:flex; align-items:flex-end; gap:16px; }
    .avatar {
      width:100px; height:100px; border-radius:50%; border:4px solid #fff;
      box-shadow:0 8px 22px rgba(0,0,0,.14); object-fit:cover; flex-shrink:0;
      background:linear-gradient(135deg,var(--blue),var(--teal));
      display:flex; align-items:center; justify-content:center;
      font-size:38px; color:white; font-weight:800;
    }
    .avatar img { width:100%; height:100%; border-radius:50%; object-fit:cover; }
    .profile-meta { padding-bottom:6px; }
    .profile-name { font-size:20px; font-weight:800; margin:0; }
    .profile-email { color:var(--muted); font-size:13px; margin:0; }
    .profile-body { padding:20px 28px 0; }
    .badge-role { display:inline-block; background:#effffb; color:#087f6f; border:1px solid var(--teal); padding:5px 14px; border-radius:999px; font-size:12px; font-weight:700; }
    .info-item { border-bottom:1px solid #eef2f7; padding:12px 0; }
    .info-item:last-child { border-bottom:none; }
    .info-label { font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; font-weight:700; margin-bottom:3px; }
    .info-value { font-weight:600; color:var(--dark); font-size:14px; }
    .btn-edit-profil { display:block; width:100%; margin-top:18px; padding:12px; border:none; border-radius:14px; background:linear-gradient(90deg,var(--blue),var(--teal)); color:white; font-weight:700; font-size:14px; cursor:pointer; transition:.2s; }
    .btn-edit-profil:hover { opacity:.88; transform:translateY(-1px); }
    .btn-logout { display:block; width:100%; margin-top:10px; padding:11px; border:none; border-radius:14px; background:#fee2e2; color:#b91c1c; font-weight:700; font-size:14px; cursor:pointer; text-decoration:none; text-align:center; transition:.2s; }
    .btn-logout:hover { background:#fca5a5; color:#7f1d1d; }

    /* STAT */
    .stat-card { padding:20px 22px; transition:.25s; }
    .stat-card:hover { transform:translateY(-4px); }
    .stat-icon { width:44px; height:44px; border-radius:14px; background:#eaf5ff; display:flex; align-items:center; justify-content:center; font-size:20px; margin-bottom:10px; }
    .stat-number { font-size:30px; font-weight:800; color:var(--blue); line-height:1; }
    .stat-label { color:var(--muted); font-size:12px; margin-top:4px; font-weight:500; }

    /* SECTION */
    .section-title { font-size:17px; font-weight:800; margin-bottom:16px; }

    /* ACTIVITY / RIWAYAT */
    .activity-item { display:flex; align-items:flex-start; gap:14px; padding:14px 0; border-bottom:1px solid #eef2f7; }
    .activity-item:last-child { border-bottom:none; }
    .activity-icon { width:40px; height:40px; border-radius:12px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:18px; }
    .activity-title { font-weight:700; font-size:14px; margin-bottom:2px; }
    .activity-desc { color:var(--muted); font-size:12px; margin:0; }
    .activity-right { margin-left:auto; flex-shrink:0; }

    /* STATUS BADGE */
    .sbadge { font-size:11px; font-weight:700; border-radius:999px; padding:5px 12px; white-space:nowrap; }
    .sbadge-disetujui { background:#dcfce7; color:#15803d; }
    .sbadge-menunggu  { background:#fef3c7; color:#b45309; }
    .sbadge-ditolak   { background:#fee2e2; color:#b91c1c; }

    /* MODAL EDIT */
    .edit-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:9999; align-items:center; justify-content:center; padding:20px; }
    .edit-box { background:#fff; border-radius:22px; padding:30px; max-width:500px; width:100%; box-shadow:var(--shadow); animation:pop .25s ease; }
    @keyframes pop { from{transform:scale(.93);opacity:0} to{transform:scale(1);opacity:1} }
    .edit-box label { font-size:12px; font-weight:700; color:#334155; margin-bottom:5px; display:block; }
    .edit-box .form-control { border-radius:12px; padding:11px 13px; font-size:14px; border-color:#cbd5e1; }
    .edit-box .form-control:focus { border-color:var(--teal); box-shadow:0 0 0 4px rgba(53,199,182,.15); }
    .btn-simpan { width:100%; padding:13px; border:none; border-radius:14px; background:linear-gradient(90deg,var(--blue),var(--teal)); color:#fff; font-weight:700; font-size:15px; cursor:pointer; margin-top:6px; transition:.2s; }
    .btn-simpan:hover { opacity:.88; }

    .kosong-box { text-align:center; padding:30px 20px; color:var(--muted); font-size:14px; }
    .kosong-box i { font-size:36px; display:block; margin-bottom:10px; opacity:.4; }

    @media(max-width:768px) { .page-title{font-size:26px;} .avatar-wrap{flex-direction:column;align-items:flex-start;} }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg py-3">
    <div class="container">
      <a class="logo-text" href="beranda.php">19JutaPendidikan</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center gap-lg-4">
          <li class="nav-item"><a class="nav-link" href="beranda.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="halamanLomba.php">Lomba</a></li>
          <li class="nav-item"><a class="nav-link" href="halamanBeasiswa.php">Beasiswa</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Peta Edukasi</a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="halamanTempatEdukatif.php">🔍 Cari Tempat</a></li>
              <li><a class="dropdown-item" href="PengajuanTempat.php">📍 Posting Tempat</a></li>
            </ul>
          </li>
          <li class="nav-item ms-lg-2">
            <?php if (!empty($foto_file)): ?>
              <img src="<?= htmlspecialchars($foto_file) ?>" alt="Profil" class="foto-profil-nav">
            <?php else: ?>
              <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(90deg,var(--blue),var(--teal));display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:16px;">
                <?= strtoupper(mb_substr($user['nama'] ?? 'U', 0, 1)) ?>
              </div>
            <?php endif; ?>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container py-5">

    <div class="text-center mb-5">
      <h1 class="page-title">Profil Pengguna</h1>
      <p class="page-subtitle">Kelola data akun dan lihat riwayat pengajuanmu.</p>
    </div>

    <!-- ALERT -->
    <?php if ($pesan_sukses): ?>
      <div class="alert alert-success rounded-4 mb-4">✅ <?= htmlspecialchars($pesan_sukses) ?></div>
    <?php endif; ?>
    <?php if ($pesan_error): ?>
      <div class="alert alert-danger rounded-4 mb-4">❌ <?= htmlspecialchars($pesan_error) ?></div>
    <?php endif; ?>

    <div class="row g-4">

      <!-- ═══ KOLOM KIRI: KARTU PROFIL ═══ -->
      <div class="col-lg-4">
        <div class="card-custom profile-card">
          <div class="profile-cover"></div>

          <div class="avatar-wrap">
            <div class="avatar">
              <?php if (!empty($foto_file)): ?>
                <img src="<?= htmlspecialchars($foto_file) ?>" alt="Foto Profil">
              <?php else: ?>
                <?= strtoupper(mb_substr($user['nama'] ?? 'U', 0, 1)) ?>
              <?php endif; ?>
            </div>
            <div class="profile-meta">
              <p class="profile-name"><?= $nama_tampil ?></p>
              <p class="profile-email"><?= $email_tampil ?></p>
            </div>
          </div>

          <div class="profile-body">
            <span class="badge-role">Pelajar / Mahasiswa</span>

            <div class="mt-4">
              <div class="info-item">
                <div class="info-label">Nama Lengkap</div>
                <div class="info-value"><?= $nama_tampil ?></div>
              </div>
              <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value"><?= $email_tampil ?></div>
              </div>
              <div class="info-item">
                <div class="info-label">Status Akun</div>
                <div class="info-value" style="color:#15803d;">✓ Aktif</div>
              </div>
            </div>

            <button class="btn-edit-profil" onclick="bukaModal()">✏️ Edit Profil</button>
            <a href="penghubung.php?aksi=logout" class="btn-logout">🚪 Keluar</a>
          </div>
        </div>
      </div>

      <!-- ═══ KOLOM KANAN: STATISTIK + RIWAYAT ═══ -->
      <div class="col-lg-8">

        <!-- STATISTIK -->
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <div class="card-custom stat-card">
              <div class="stat-icon">🏆</div>
              <div class="stat-number"><?= $total_lomba ?></div>
              <p class="stat-label">Lomba Diajukan</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card-custom stat-card">
              <div class="stat-icon">🎓</div>
              <div class="stat-number"><?= $total_beasiswa ?></div>
              <p class="stat-label">Beasiswa Diajukan</p>
            </div>
          </div>
        </div>

        <!-- RIWAYAT PENGAJUAN LOMBA -->
        <div class="card-custom p-4 mb-4">
          <h4 class="section-title">Riwayat Pengajuan Lomba</h4>

          <?php if ($total_lomba > 0): ?>
            <?php while ($r = mysqli_fetch_assoc($q_lomba)):
              $status = $r['status_verifikasi'];
              $badge_class = match($status) {
                'disetujui' => 'sbadge-disetujui',
                'ditolak'   => 'sbadge-ditolak',
                default     => 'sbadge-menunggu',
              };
              $label = match($status) {
                'disetujui' => 'Disetujui',
                'ditolak'   => 'Ditolak',
                default     => 'Menunggu',
              };
            ?>
            <div class="activity-item">
              <div class="activity-icon">🏆</div>
              <div style="flex:1; min-width:0;">
                <div class="activity-title"><?= htmlspecialchars($r['judul_lomba']) ?></div>
                <p class="activity-desc">
                  <?= htmlspecialchars($r['paket_langganan']) ?> &bull;
                  Rp <?= number_format($r['jumlah'], 0, ',', '.') ?> &bull;
                  <?= date('d M Y', strtotime($r['tanggal_bayar'])) ?>
                </p>
              </div>
              <div class="activity-right">
                <span class="sbadge <?= $badge_class ?>"><?= $label ?></span>
              </div>
            </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="kosong-box">
              <i class="bi bi-trophy"></i>
              Belum ada pengajuan lomba. <a href="halamanTransaksiLomba.php" style="color:var(--blue);font-weight:600;">Ajukan sekarang →</a>
            </div>
          <?php endif; ?>
        </div>

        <!-- RIWAYAT PENGAJUAN BEASISWA -->
        <div class="card-custom p-4">
          <h4 class="section-title">Riwayat Pengajuan Beasiswa</h4>

          <?php if ($total_beasiswa > 0): ?>
            <?php while ($r = mysqli_fetch_assoc($q_beasiswa)):
              $status = $r['status_verifikasi'];
              $badge_class = match($status) {
                'disetujui' => 'sbadge-disetujui',
                'ditolak'   => 'sbadge-ditolak',
                default     => 'sbadge-menunggu',
              };
              $label = match($status) {
                'disetujui' => 'Disetujui',
                'ditolak'   => 'Ditolak',
                default     => 'Menunggu',
              };
            ?>
            <div class="activity-item">
              <div class="activity-icon">🎓</div>
              <div style="flex:1; min-width:0;">
                <div class="activity-title"><?= htmlspecialchars($r['nama_beasiswa']) ?></div>
                <p class="activity-desc">
                  <?= htmlspecialchars($r['paket_langganan']) ?> &bull;
                  Rp <?= number_format($r['jumlah'], 0, ',', '.') ?> &bull;
                  <?= date('d M Y', strtotime($r['tanggal_bayar'])) ?>
                </p>
              </div>
              <div class="activity-right">
                <span class="sbadge <?= $badge_class ?>"><?= $label ?></span>
              </div>
            </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="kosong-box">
              <i class="bi bi-mortarboard"></i>
              Belum ada pengajuan beasiswa. <a href="halamanTransaksiBeasiswa.php" style="color:var(--blue);font-weight:600;">Ajukan sekarang →</a>
            </div>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </main>

  <!-- MODAL EDIT PROFIL -->
  <div class="edit-overlay" id="editOverlay">
    <div class="edit-box">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Profil</h4>
        <button type="button" onclick="tutupModal()" style="background:none;border:none;font-size:22px;cursor:pointer;color:var(--muted);">✕</button>
      </div>

      <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="simpan_profil" value="1">

        <div class="mb-3">
          <label>Foto Profil <span style="font-weight:400;color:var(--muted);">(opsional)</span></label>
          <input type="file" name="foto_profil" class="form-control" accept="image/jpg,image/jpeg,image/png,image/webp">
          <small class="text-muted" style="font-size:11px;">Format: JPG, PNG, WEBP. Biarkan kosong jika tidak ingin mengubah foto.</small>
        </div>

        <div class="mb-3">
          <label>Nama Lengkap <span style="color:#ef4444;">*</span></label>
          <input type="text" name="nama" class="form-control" value="<?= $nama_tampil ?>" required>
        </div>

        <div class="mb-4">
          <label>Email <span style="color:#ef4444;">*</span></label>
          <input type="email" name="email" class="form-control" value="<?= $email_tampil ?>" required>
        </div>

        <button type="submit" class="btn-simpan">💾 Simpan Perubahan</button>
      </form>
    </div>
  </div>

  <script>
    function bukaModal() { document.getElementById('editOverlay').style.display = 'flex'; }
    function tutupModal() { document.getElementById('editOverlay').style.display = 'none'; }

    // Otomatis buka modal jika ada error validasi dari server
    <?php if ($pesan_error): ?>
    window.addEventListener('DOMContentLoaded', () => bukaModal());
    <?php endif; ?>
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

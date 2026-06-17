<?php
session_start();
$host = "localhost"; $user = "root"; $pass = ""; $db = "19juta_pendidikan";
$conn = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($conn, "utf8mb4");

// ─── TAMBAH FITUR ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
    $judul   = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $desk    = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $url     = mysqli_real_escape_string($conn, trim($_POST['link_url']));
    $ikon    = mysqli_real_escape_string($conn, trim($_POST['ikon_emoji']));
    $warna   = mysqli_real_escape_string($conn, trim($_POST['warna_aksen']));
    $urutan  = (int) $_POST['urutan'];
    mysqli_query($conn,
        "INSERT INTO fitur_beranda (judul, deskripsi, link_url, ikon_emoji, warna_aksen, urutan)
         VALUES ('$judul','$desk','$url','$ikon','$warna','$urutan')"
    );
    header("Location: halamanKelolaFiturBeranda.php?pesan=tambah"); exit();
}

// ─── EDIT FITUR ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'edit') {
    $id      = (int) $_POST['id_fitur'];
    $judul   = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $desk    = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $url     = mysqli_real_escape_string($conn, trim($_POST['link_url']));
    $ikon    = mysqli_real_escape_string($conn, trim($_POST['ikon_emoji']));
    $warna   = mysqli_real_escape_string($conn, trim($_POST['warna_aksen']));
    $urutan  = (int) $_POST['urutan'];
    $aktif   = isset($_POST['aktif']) ? 1 : 0;
    mysqli_query($conn,
        "UPDATE fitur_beranda SET judul='$judul', deskripsi='$desk', link_url='$url',
         ikon_emoji='$ikon', warna_aksen='$warna', urutan='$urutan', aktif='$aktif'
         WHERE id_fitur='$id'"
    );
    header("Location: halamanKelolaFiturBeranda.php?pesan=edit"); exit();
}

// ─── HAPUS FITUR ──────────────────────────────────────────────────────────
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM fitur_beranda WHERE id_fitur = '$id'");
    header("Location: halamanKelolaFiturBeranda.php?pesan=hapus"); exit();
}

// ─── TOGGLE AKTIF ──────────────────────────────────────────────────────────
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    mysqli_query($conn, "UPDATE fitur_beranda SET aktif = NOT aktif WHERE id_fitur = '$id'");
    header("Location: halamanKelolaFiturBeranda.php"); exit();
}

// Ambil semua fitur
$daftarFitur = mysqli_query($conn, "SELECT * FROM fitur_beranda ORDER BY urutan ASC, id_fitur ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Fitur Beranda – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    :root { --blue:#2f6df6; --teal:#35c7b6; --dark:#14213d; --muted:#6b7280; --border:#e2e8f0; --shadow:0 18px 45px rgba(20,33,61,.10); }
    * { font-family:'Poppins',sans-serif; box-sizing:border-box; }
    body { background:linear-gradient(180deg,#eef8fb,#f7fcfb); color:var(--dark); min-height:100vh; }
    .admin-wrapper { display:flex; min-height:100vh; }

    /* SIDEBAR */
    .sidebar { width:270px; background:#fff; border-right:1px solid var(--border); padding:26px 20px; position:fixed; height:100vh; overflow-y:auto; box-shadow:8px 0 30px rgba(20,33,61,.05); }
    .logo-text { font-weight:800; font-size:22px; color:var(--blue); margin-bottom:36px; display:block; text-decoration:none; }
    .menu-label { font-size:12px; color:var(--muted); font-weight:700; margin-bottom:12px; text-transform:uppercase; letter-spacing:.5px; }
    .menu-item { display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:14px; color:#334155; text-decoration:none; font-size:14px; font-weight:600; margin-bottom:8px; transition:.25s ease; }
    .menu-item:hover, .menu-item.active { background:linear-gradient(90deg,var(--blue),var(--teal)); color:white; transform:translateX(4px); }
    .menu-toggle { display:flex; align-items:center; justify-content:space-between; }
    .menu-toggle .chevron { font-size:11px; transition:transform .2s; }
    .menu-toggle[aria-expanded="true"] .chevron { transform:rotate(180deg); }
    .submenu { display:flex; flex-direction:column; padding-left:16px; }
    .submenu-item { font-size:12.5px; padding:9px 14px; }
    .logout { background:#fee2e2; color:#b91c1c !important; margin-top:20px; }
    .logout:hover { background:#fca5a5; color:#b91c1c !important; transform:none !important; }

    /* MAIN */
    .main-content { margin-left:270px; width:calc(100% - 270px); padding:34px; }
    .topbar { background:white; border-radius:22px; padding:22px 26px; box-shadow:var(--shadow); display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px; }
    .page-title { font-size:26px; font-weight:800; margin:0; background:linear-gradient(90deg,var(--blue),var(--teal)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .page-desc { color:var(--muted); font-size:14px; margin:4px 0 0; }

    /* ALERT */
    .alert-custom { border-radius:14px; padding:14px 18px; font-size:14px; font-weight:600; margin-bottom:20px; border:none; }

    /* FITUR CARDS */
    .fitur-row { background:white; border-radius:18px; padding:18px 22px; box-shadow:0 4px 20px rgba(20,33,61,.06); display:flex; align-items:center; gap:18px; margin-bottom:12px; }
    .fitur-ikon { width:56px; height:56px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.8rem; flex-shrink:0; }
    .fitur-info { flex:1; }
    .fitur-info h6 { font-weight:700; margin:0 0 4px; font-size:15px; color:var(--dark); }
    .fitur-info p  { margin:0; font-size:13px; color:var(--muted); }
    .fitur-info small { font-size:11.5px; color:#94a3b8; }
    .fitur-aksi { display:flex; gap:8px; flex-shrink:0; }
    .btn-sm-aksi { border:none; border-radius:10px; padding:7px 14px; font-size:12.5px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn-edit { background:#dbeafe; color:#1d4ed8; }
    .btn-edit:hover { background:#bfdbfe; color:#1d4ed8; }
    .btn-hapus { background:#fee2e2; color:#b91c1c; }
    .btn-hapus:hover { background:#fecaca; color:#b91c1c; }
    .badge-aktif   { background:#d1fae5; color:#065f46; border-radius:999px; font-size:11px; font-weight:700; padding:3px 10px; }
    .badge-nonaktif{ background:#f1f5f9; color:#64748b; border-radius:999px; font-size:11px; font-weight:700; padding:3px 10px; }

    /* MODAL */
    .modal-content { border:none; border-radius:20px; }
    .modal-header { border-bottom:1px solid var(--border); padding:20px 24px; }
    .modal-body { padding:20px 24px; }
    .modal-footer { border-top:1px solid var(--border); padding:16px 24px; }
    .form-label { font-size:13px; font-weight:700; color:var(--dark); }
    .form-control, .form-select { border-radius:12px; border:1.5px solid var(--border); font-size:14px; padding:10px 13px; }
    .form-control:focus, .form-select:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(47,109,246,.1); }
    .btn-simpan { background:linear-gradient(90deg,var(--blue),var(--teal)); color:white; border:none; border-radius:12px; padding:10px 24px; font-weight:700; font-size:14px; }

    /* EMPTY STATE */
    .empty-state { text-align:center; padding:60px 20px; color:var(--muted); background:white; border-radius:18px; }
    .empty-state .emo { font-size:56px; margin-bottom:12px; }

    @media(max-width:991px) {
      .sidebar { position:static; width:100%; height:auto; }
      .admin-wrapper { flex-direction:column; }
      .main-content { margin-left:0; width:100%; padding:20px; }
    }
  </style>
</head>
<body>
<div class="admin-wrapper">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <a href="adminDashboard.php" class="logo-text">19JutaAdmin</a>

    <div class="menu-label">Navigasi Utama</div>
    <a href="adminDashboard.php" class="menu-item">📊 Dashboard</a>

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
    <a href="halamanKelolaFiturBeranda.php" class="menu-item active">🏠 Kelola Fitur Beranda</a>
      <a href="halamanKelolaBlog.php" class="menu-item">📝 Kelola Blog</a>
    <a href="halamanKelolaTransaksi.php" class="menu-item">💳 Kelola Transaksi</a>

    <div class="menu-label">Sistem Validasi</div>
    <a href="halamanVerifikasi.php" class="menu-item">✅ Verifikasi Iklan</a>

    <div class="menu-label">Pengaturan</div>
    <a href="halamanKelolaUser.php" class="menu-item">👤 Kelola Pengguna</a>
    <a href="halamanLoginAdmin.php" class="menu-item logout">🚪 Logout</a>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="main-content">

    <div class="topbar">
      <div>
        <h1 class="page-title">🏠 Kelola Fitur Beranda</h1>
        <p class="page-desc">Tambah, ubah, atau hapus kartu fitur yang ditampilkan di halaman Home pengguna.</p>
      </div>
      <button class="btn-simpan" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Fitur</button>
    </div>

    <!-- ALERT -->
    <?php if (isset($_GET['pesan'])): ?>
      <?php
        $alertMap = [
          'tambah' => ['✅ Fitur baru berhasil ditambahkan ke beranda!', 'success'],
          'edit'   => ['✏️ Fitur berhasil diperbarui.', 'info'],
          'hapus'  => ['🗑️ Fitur berhasil dihapus.', 'warning'],
        ];
        [$msg, $type] = $alertMap[$_GET['pesan']] ?? ['Selesai.', 'secondary'];
      ?>
      <div class="alert-custom alert-<?= $type ?>"><?= $msg ?></div>
    <?php endif; ?>

    <!-- DAFTAR FITUR -->
    <?php
    mysqli_data_seek($daftarFitur, 0);
    $ada = false;
    while ($f = mysqli_fetch_assoc($daftarFitur)):
      $ada = true;
    ?>
    <div class="fitur-row">
      <div class="fitur-ikon" style="background: linear-gradient(135deg, <?= htmlspecialchars($f['warna_aksen']) ?>22, <?= htmlspecialchars($f['warna_aksen']) ?>44);">
        <?= htmlspecialchars($f['ikon_emoji']) ?>
      </div>
      <div class="fitur-info">
        <h6><?= htmlspecialchars($f['judul']) ?>
          <?php if ($f['aktif']): ?>
            <span class="badge-aktif ms-2">Aktif</span>
          <?php else: ?>
            <span class="badge-nonaktif ms-2">Nonaktif</span>
          <?php endif; ?>
        </h6>
        <p><?= htmlspecialchars(mb_strimwidth($f['deskripsi'], 0, 90, '...')) ?></p>
        <small>🔗 <?= htmlspecialchars($f['link_url']) ?> &nbsp;·&nbsp; Urutan: <?= $f['urutan'] ?> &nbsp;·&nbsp; Warna: <?= $f['warna_aksen'] ?></small>
      </div>
      <div class="fitur-aksi">
        <a href="?toggle=<?= $f['id_fitur'] ?>" class="btn-sm-aksi" style="background:#f1f5f9; color:#334155;"
          title="<?= $f['aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
          <?= $f['aktif'] ? '👁 Sembunyikan' : '👁 Tampilkan' ?>
        </a>
        <button class="btn-sm-aksi btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $f['id_fitur'] ?>">✏️ Edit</button>
        <a href="?hapus=<?= $f['id_fitur'] ?>" class="btn-sm-aksi btn-hapus"
          onclick="return confirm('Hapus fitur <?= htmlspecialchars($f['judul']) ?>?')">🗑 Hapus</a>
      </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEdit<?= $f['id_fitur'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <form method="POST">
            <input type="hidden" name="aksi" value="edit">
            <input type="hidden" name="id_fitur" value="<?= $f['id_fitur'] ?>">
            <div class="modal-header">
              <h5 class="modal-title fw-bold">✏️ Edit Fitur: <?= htmlspecialchars($f['judul']) ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Judul Fitur *</label>
                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($f['judul']) ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Deskripsi *</label>
                <textarea name="deskripsi" class="form-control" rows="3" required><?= htmlspecialchars($f['deskripsi']) ?></textarea>
              </div>
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label">Link URL *</label>
                  <input type="text" name="link_url" class="form-control" placeholder="halamanLomba.php" value="<?= htmlspecialchars($f['link_url']) ?>" required>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Ikon Emoji</label>
                  <input type="text" name="ikon_emoji" class="form-control text-center" style="font-size:20px;" maxlength="4" value="<?= htmlspecialchars($f['ikon_emoji']) ?>">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Warna Aksen</label>
                  <input type="color" name="warna_aksen" class="form-control form-control-color" value="<?= htmlspecialchars($f['warna_aksen']) ?>" style="height:44px;">
                </div>
              </div>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Urutan Tampil</label>
                  <input type="number" name="urutan" class="form-control" min="0" value="<?= $f['urutan'] ?>">
                </div>
                <div class="col-md-8 d-flex align-items-end pb-1">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="aktif" id="aktif<?= $f['id_fitur'] ?>" <?= $f['aktif'] ? 'checked' : '' ?> style="width:44px; height:24px;">
                    <label class="form-check-label ms-2 fw-bold" for="aktif<?= $f['id_fitur'] ?>">Tampilkan di beranda</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn-simpan">💾 Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php endwhile; ?>

    <?php if (!$ada): ?>
    <div class="empty-state">
      <div class="emo">🏠</div>
      <h5>Belum ada fitur</h5>
      <p>Klik "Tambah Fitur" untuk menambahkan kartu fitur di beranda.</p>
    </div>
    <?php endif; ?>

  </div><!-- /main-content -->
</div><!-- /admin-wrapper -->

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="aksi" value="tambah">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">➕ Tambah Fitur Beranda Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted" style="font-size:13px;">Fitur baru akan langsung muncul di halaman Home setelah ditambahkan.</p>
          <div class="mb-3">
            <label class="form-label">Judul Fitur *</label>
            <input type="text" name="judul" class="form-control" placeholder="Contoh: Info Magang, Galeri Kampus..." required>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi *</label>
            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Penjelasan singkat tentang fitur ini..." required></textarea>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Link URL * <small class="text-muted fw-normal">(file PHP tujuan)</small></label>
              <input type="text" name="link_url" class="form-control" placeholder="halamanLomba.php" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Ikon Emoji</label>
              <input type="text" name="ikon_emoji" class="form-control text-center" style="font-size:20px;" maxlength="4" value="📌" placeholder="📌">
            </div>
            <div class="col-md-3">
              <label class="form-label">Warna Aksen</label>
              <input type="color" name="warna_aksen" class="form-control form-control-color" value="#148fcd" style="height:44px;">
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Urutan Tampil <small class="text-muted fw-normal">(angka kecil = tampil duluan)</small></label>
            <input type="number" name="urutan" class="form-control" min="0" value="10">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn-simpan">✅ Tambahkan Fitur</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
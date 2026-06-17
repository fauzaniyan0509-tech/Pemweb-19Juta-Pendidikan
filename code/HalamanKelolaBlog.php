<?php
session_start();
$host = "localhost"; $user = "root"; $pass = ""; $db = "19juta_pendidikan";
$conn = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($conn, "utf8mb4");

// Helper: buat slug dari judul
function buatSlug($teks) {
    $teks = strtolower(trim($teks));
    $teks = preg_replace('/[^a-z0-9\s-]/', '', $teks);
    $teks = preg_replace('/[\s-]+/', '-', $teks);
    return trim($teks, '-') . '-' . time();
}

// TAMBAH
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'tambah') {
    $judul    = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $isi      = mysqli_real_escape_string($conn, trim($_POST['isi']));
    $ringkasan= mysqli_real_escape_string($conn, trim($_POST['ringkasan']));
    $kategori = mysqli_real_escape_string($conn, trim($_POST['kategori']));
    $ikon     = mysqli_real_escape_string($conn, trim($_POST['ikon_emoji']));
    $penulis  = mysqli_real_escape_string($conn, trim($_POST['penulis']) ?: '19JutaPendidikan');
    $slug     = buatSlug($_POST['judul']);
    mysqli_query($conn,
        "INSERT INTO blog (judul, slug, isi, ringkasan, kategori, ikon_emoji, penulis)
         VALUES ('$judul','$slug','$isi','$ringkasan','$kategori','$ikon','$penulis')"
    );
    header("Location: halamanKelolaBlog.php?pesan=tambah"); exit();
}

// EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'edit') {
    $id       = (int) $_POST['id_blog'];
    $judul    = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $isi      = mysqli_real_escape_string($conn, trim($_POST['isi']));
    $ringkasan= mysqli_real_escape_string($conn, trim($_POST['ringkasan']));
    $kategori = mysqli_real_escape_string($conn, trim($_POST['kategori']));
    $ikon     = mysqli_real_escape_string($conn, trim($_POST['ikon_emoji']));
    $penulis  = mysqli_real_escape_string($conn, trim($_POST['penulis']));
    $aktif    = isset($_POST['aktif']) ? 1 : 0;
    mysqli_query($conn,
        "UPDATE blog SET judul='$judul', isi='$isi', ringkasan='$ringkasan',
         kategori='$kategori', ikon_emoji='$ikon', penulis='$penulis', aktif='$aktif'
         WHERE id_blog='$id'"
    );
    header("Location: halamanKelolaBlog.php?pesan=edit"); exit();
}

// HAPUS
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM blog WHERE id_blog = '$id'");
    header("Location: halamanKelolaBlog.php?pesan=hapus"); exit();
}

// TOGGLE
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    mysqli_query($conn, "UPDATE blog SET aktif = NOT aktif WHERE id_blog = '$id'");
    header("Location: halamanKelolaBlog.php"); exit();
}

$daftarBlog = mysqli_query($conn, "SELECT * FROM blog ORDER BY dibuat_pada DESC");
$kategoriList = ['Tips Beasiswa','Tips Lomba','Update Platform','Tips Belajar','Inspirasi','Lainnya'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Blog – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    :root { --blue:#2f6df6; --teal:#35c7b6; --dark:#14213d; --muted:#6b7280; --border:#e2e8f0; --shadow:0 18px 45px rgba(20,33,61,.10); }
    * { font-family:'Poppins',sans-serif; box-sizing:border-box; }
    body { background:linear-gradient(180deg,#eef8fb,#f7fcfb); color:var(--dark); min-height:100vh; }
    .admin-wrapper { display:flex; min-height:100vh; }
    .sidebar { width:270px; background:#fff; border-right:1px solid var(--border); padding:26px 20px; position:fixed; height:100vh; overflow-y:auto; }
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
    .main-content { margin-left:270px; width:calc(100% - 270px); padding:34px; }
    .topbar { background:white; border-radius:22px; padding:22px 26px; box-shadow:var(--shadow); display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px; }
    .page-title { font-size:26px; font-weight:800; margin:0; background:linear-gradient(90deg,var(--blue),var(--teal)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .page-desc { color:var(--muted); font-size:14px; margin:4px 0 0; }
    .btn-tambah { background:linear-gradient(90deg,var(--blue),var(--teal)); color:white; border:none; border-radius:12px; padding:10px 22px; font-weight:700; font-size:14px; cursor:pointer; }
    .alert-custom { border-radius:14px; padding:14px 18px; font-size:14px; font-weight:600; margin-bottom:20px; border:none; }
    .blog-row { background:white; border-radius:18px; padding:18px 22px; box-shadow:0 4px 20px rgba(20,33,61,.06); display:flex; align-items:flex-start; gap:18px; margin-bottom:12px; }
    .blog-ikon { width:52px; height:52px; border-radius:14px; background:#e0f2fe; display:flex; align-items:center; justify-content:center; font-size:1.6rem; flex-shrink:0; }
    .blog-info { flex:1; }
    .blog-info h6 { font-weight:700; margin:0 0 4px; font-size:15px; }
    .blog-info p  { margin:0 0 6px; font-size:13px; color:var(--muted); }
    .blog-info small { font-size:11.5px; color:#94a3b8; }
    .blog-aksi { display:flex; gap:8px; flex-shrink:0; flex-wrap:wrap; }
    .btn-sm-aksi { border:none; border-radius:10px; padding:7px 14px; font-size:12.5px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn-edit  { background:#dbeafe; color:#1d4ed8; }
    .btn-hapus { background:#fee2e2; color:#b91c1c; }
    .badge-aktif    { background:#d1fae5; color:#065f46; border-radius:999px; font-size:11px; font-weight:700; padding:3px 10px; }
    .badge-nonaktif { background:#f1f5f9; color:#64748b; border-radius:999px; font-size:11px; font-weight:700; padding:3px 10px; }
    .modal-content { border:none; border-radius:20px; }
    .form-label { font-size:13px; font-weight:700; }
    .form-control, .form-select { border-radius:12px; border:1.5px solid var(--border); font-size:14px; padding:10px 13px; }
    .form-control:focus, .form-select:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(47,109,246,.1); }
    .empty-state { text-align:center; padding:60px; color:var(--muted); background:white; border-radius:18px; }
    @media(max-width:991px) { .sidebar { position:static; width:100%; height:auto; } .admin-wrapper { flex-direction:column; } .main-content { margin-left:0; width:100%; padding:20px; } }
  </style>
</head>
<body>
<div class="admin-wrapper">

  <aside class="sidebar">
    <a href="adminDashboard.php" class="logo-text">19JutaAdmin</a>
    <div class="menu-label">Navigasi Utama</div>
    <a href="adminDashboard.php" class="menu-item">📊 Dashboard</a>
    <div class="menu-label">Manajemen Data</div>
    <a href="halamanKelolaLomba.php" class="menu-item">🏆 Kelola Lomba</a>
    <a href="halamanKelolaBeasiswa.php" class="menu-item">🎓 Kelola Beasiswa</a>
    <a href="#" class="menu-item menu-toggle" data-bs-toggle="collapse" data-bs-target="#submenuTempat" role="button" aria-expanded="false">
      <span>📍 Kelola Tempat / Peta</span><span class="chevron">▾</span>
    </a>
    <div class="collapse submenu" id="submenuTempat">
      <a href="halamanKelolaTempat.php" class="menu-item submenu-item">📋 Daftar Tempat</a>
      <a href="HalamanVerifikasiTempat.php" class="menu-item submenu-item">✅ Verifikasi Pengajuan</a>
    </div>
    <a href="halamanKelolaFiturBeranda.php" class="menu-item">🏠 Kelola Fitur Beranda</a>
    <a href="halamanKelolaBlog.php" class="menu-item active">📝 Kelola Blog</a>
    <div class="menu-label">Sistem Validasi</div>
    <a href="halamanVerifikasi.php" class="menu-item">✅ Verifikasi Iklan</a>
    <div class="menu-label">Pengaturan</div>
    <a href="halamanKelolaUser.php" class="menu-item">👤 Kelola Pengguna</a>
    <a href="halamanLoginAdmin.php" class="menu-item logout">🚪 Logout</a>
  </aside>

  <div class="main-content">
    <div class="topbar">
      <div>
        <h1 class="page-title">📝 Kelola Blog</h1>
        <p class="page-desc">Tulis, edit, dan kelola artikel blog yang tampil di halaman Blog pengguna.</p>
      </div>
      <button class="btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tulis Artikel</button>
    </div>

    <?php if (isset($_GET['pesan'])):
      $alertMap = ['tambah'=>['✅ Artikel berhasil ditambahkan!','success'], 'edit'=>['✏️ Artikel berhasil diperbarui.','info'], 'hapus'=>['🗑️ Artikel berhasil dihapus.','warning']];
      [$msg,$type] = $alertMap[$_GET['pesan']] ?? ['Selesai.','secondary'];
    ?>
    <div class="alert-custom alert-<?= $type ?>"><?= $msg ?></div>
    <?php endif; ?>

    <!-- DAFTAR ARTIKEL -->
    <?php $ada = false; while ($b = mysqli_fetch_assoc($daftarBlog)): $ada = true; ?>
    <div class="blog-row">
      <div class="blog-ikon"><?= htmlspecialchars($b['ikon_emoji']) ?></div>
      <div class="blog-info">
        <h6><?= htmlspecialchars($b['judul']) ?>
          <?php if ($b['aktif']): ?><span class="badge-aktif ms-2">Aktif</span><?php else: ?><span class="badge-nonaktif ms-2">Draf</span><?php endif; ?>
        </h6>
        <p><?= htmlspecialchars(mb_strimwidth($b['ringkasan'] ?: $b['isi'], 0, 100, '...')) ?></p>
        <small>📂 <?= htmlspecialchars($b['kategori']) ?> &nbsp;·&nbsp; ✍️ <?= htmlspecialchars($b['penulis']) ?> &nbsp;·&nbsp; <?= date('d M Y', strtotime($b['dibuat_pada'])) ?></small>
      </div>
      <div class="blog-aksi">
        <a href="detailBlog.php?slug=<?= urlencode($b['slug']) ?>" target="_blank" class="btn-sm-aksi" style="background:#f1f5f9;color:#334155;">👁 Preview</a>
        <a href="?toggle=<?= $b['id_blog'] ?>" class="btn-sm-aksi" style="background:#f1f5f9;color:#334155;"><?= $b['aktif'] ? 'Sembunyikan' : 'Tampilkan' ?></a>
        <button class="btn-sm-aksi btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $b['id_blog'] ?>">✏️ Edit</button>
        <a href="?hapus=<?= $b['id_blog'] ?>" class="btn-sm-aksi btn-hapus" onclick="return confirm('Hapus artikel ini secara permanen?')">🗑 Hapus</a>
      </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEdit<?= $b['id_blog'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
          <form method="POST">
            <input type="hidden" name="aksi" value="edit">
            <input type="hidden" name="id_blog" value="<?= $b['id_blog'] ?>">
            <div class="modal-header"><h5 class="modal-title fw-bold">✏️ Edit Artikel</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">
              <div class="col-12">
                <label class="form-label">Judul *</label>
                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($b['judul']) ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select">
                  <?php foreach ($kategoriList as $k): ?>
                  <option value="<?= $k ?>" <?= $b['kategori']===$k?'selected':'' ?>><?= $k ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Ikon Emoji</label>
                <input type="text" name="ikon_emoji" class="form-control text-center" style="font-size:20px;" maxlength="4" value="<?= htmlspecialchars($b['ikon_emoji']) ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label">Penulis</label>
                <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($b['penulis']) ?>">
              </div>
              <div class="col-md-2 d-flex align-items-end pb-1">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="aktif" id="aktif<?= $b['id_blog'] ?>" <?= $b['aktif']?'checked':'' ?> style="width:44px;height:24px;">
                  <label class="form-check-label ms-2 fw-bold" for="aktif<?= $b['id_blog'] ?>">Aktif</label>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Ringkasan <small class="text-muted fw-normal">(tampil di kartu blog)</small></label>
                <input type="text" name="ringkasan" class="form-control" value="<?= htmlspecialchars($b['ringkasan']) ?>" maxlength="300">
              </div>
              <div class="col-12">
                <label class="form-label">Isi Artikel * <small class="text-muted fw-normal">(gunakan **teks** untuk tebal, baris kosong untuk paragraf baru)</small></label>
                <textarea name="isi" class="form-control" rows="12" required><?= htmlspecialchars($b['isi']) ?></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn-tambah">💾 Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endwhile; ?>

    <?php if (!$ada): ?>
    <div class="empty-state">
      <div style="font-size:56px; margin-bottom:14px;">📭</div>
      <h5>Belum ada artikel</h5>
      <p>Klik "Tulis Artikel" untuk mulai menulis konten blog pertamamu.</p>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="aksi" value="tambah">
        <div class="modal-header"><h5 class="modal-title fw-bold">✍️ Tulis Artikel Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body row g-3">
          <div class="col-12">
            <label class="form-label">Judul *</label>
            <input type="text" name="judul" class="form-control" placeholder="Contoh: 5 Tips Lolos Beasiswa Impian" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-select">
              <?php foreach ($kategoriList as $k): ?>
              <option value="<?= $k ?>"><?= $k ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Ikon Emoji</label>
            <input type="text" name="ikon_emoji" class="form-control text-center" style="font-size:20px;" maxlength="4" value="📝">
          </div>
          <div class="col-md-6">
            <label class="form-label">Penulis</label>
            <input type="text" name="penulis" class="form-control" value="19JutaPendidikan">
          </div>
          <div class="col-12">
            <label class="form-label">Ringkasan <small class="text-muted fw-normal">(tampil di kartu blog, maks 300 karakter)</small></label>
            <input type="text" name="ringkasan" class="form-control" placeholder="Deskripsi singkat artikel ini..." maxlength="300">
          </div>
          <div class="col-12">
            <label class="form-label">Isi Artikel * <small class="text-muted fw-normal">(gunakan **teks** untuk tebal, baris kosong untuk paragraf baru, angka+titik untuk list)</small></label>
            <textarea name="isi" class="form-control" rows="14" placeholder="Tulis isi artikel di sini..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn-tambah">✅ Terbitkan Artikel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
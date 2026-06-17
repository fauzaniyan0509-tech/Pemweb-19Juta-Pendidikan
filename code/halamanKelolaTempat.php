<?php
include 'penghubung.php';

// KONEKSI DATABASE
$host = "localhost"; $user = "root"; $pass = ""; $db = "19juta_pendidikan";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());
mysqli_set_charset($conn, "utf8mb4");

// ─── PROSES HAPUS ───────────────────────────────────────────────────────────
if (isset($_GET['hapus_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus_id']);
    $q  = mysqli_query($conn, "SELECT foto FROM tempat_edukatif WHERE id_tempat = '$id'");
    $d  = mysqli_fetch_assoc($q);
    mysqli_query($conn, "DELETE FROM tempat_edukatif WHERE id_tempat = '$id'");
    if ($d['foto'] && file_exists("uploads/" . $d['foto'])) unlink("uploads/" . $d['foto']);
    header("Location: halamanKelolaTempat.php?pesan=hapus_sukses"); exit();
}

// ─── PROSES TAMBAH ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    $nama      = mysqli_real_escape_string($conn, $_POST['nama_tempat_edukatif']);
    $alamat    = mysqli_real_escape_string($conn, $_POST['alamat_maps']);
    $kategori  = mysqli_real_escape_string($conn, $_POST['kategori']);
    $prasarana = mysqli_real_escape_string($conn, $_POST['prasarana']);
    $rating    = mysqli_real_escape_string($conn, $_POST['rating']);
    $sosmed    = mysqli_real_escape_string($conn, $_POST['sosial_media']);
    $jam       = mysqli_real_escape_string($conn, $_POST['jam_operasional']);
    $foto_fix  = '';

    if (!empty($_FILES['foto']['name'])) {
        $ext       = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $izin      = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $izin)) {
            $nama_file = time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $nama_file)) {
                $foto_fix = $nama_file;
            }
        }
    }

    $q = "INSERT INTO tempat_edukatif 
            (nama_tempat_edukatif, alamat_maps, kategori, prasarana, rating, sosial_media, jam_operasional, foto)
          VALUES ('$nama','$alamat','$kategori','$prasarana','$rating','$sosmed','$jam','$foto_fix')";
    mysqli_query($conn, $q);
    header("Location: halamanKelolaTempat.php?pesan=tambah_sukses"); exit();
}

// ─── PROSES EDIT ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi']) && $_POST['aksi'] == 'edit') {
    $id        = mysqli_real_escape_string($conn, $_POST['id_tempat']);
    $nama      = mysqli_real_escape_string($conn, $_POST['nama_tempat_edukatif']);
    $alamat    = mysqli_real_escape_string($conn, $_POST['alamat_maps']);
    $kategori  = mysqli_real_escape_string($conn, $_POST['kategori']);
    $prasarana = mysqli_real_escape_string($conn, $_POST['prasarana']);
    $rating    = mysqli_real_escape_string($conn, $_POST['rating']);
    $sosmed    = mysqli_real_escape_string($conn, $_POST['sosial_media']);
    $jam       = mysqli_real_escape_string($conn, $_POST['jam_operasional']);

    // Ambil foto lama
    $q_old   = mysqli_query($conn, "SELECT foto FROM tempat_edukatif WHERE id_tempat = '$id'");
    $d_old   = mysqli_fetch_assoc($q_old);
    $foto_fix = $d_old['foto'];

    if (!empty($_FILES['foto']['name'])) {
        $ext  = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $izin = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $izin)) {
            $nama_file = time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $nama_file)) {
                if ($foto_fix && file_exists("uploads/" . $foto_fix)) unlink("uploads/" . $foto_fix);
                $foto_fix = $nama_file;
            }
        }
    }

    $q = "UPDATE tempat_edukatif SET
            nama_tempat_edukatif='$nama', alamat_maps='$alamat', kategori='$kategori',
            prasarana='$prasarana', rating='$rating', sosial_media='$sosmed',
            jam_operasional='$jam', foto='$foto_fix'
          WHERE id_tempat='$id'";
    mysqli_query($conn, $q);
    header("Location: halamanKelolaTempat.php?pesan=edit_sukses"); exit();
}

// ─── AMBIL DATA ─────────────────────────────────────────────────────────────
$list_tempat = mysqli_query($conn, "SELECT * FROM tempat_edukatif ORDER BY id_tempat DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Tempat Edukatif – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    :root {
      --blue: #2f6df6; --teal: #35c7b6; --dark: #14213d; --muted: #6b7280;
      --border: #e2e8f0; --shadow: 0 18px 45px rgba(20,33,61,.10);
    }
    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
    body { background: linear-gradient(180deg,#eef8fb 0%,#f7fcfb 100%); color: var(--dark); min-height: 100vh; }
    .admin-wrapper { display: flex; min-height: 100vh; }

    /* SIDEBAR */
    .sidebar { width: 270px; background: #fff; border-right: 1px solid var(--border); padding: 26px 20px; position: fixed; height: 100vh; box-shadow: 8px 0 30px rgba(20,33,61,.05); overflow-y: auto; }
    .logo-text { font-weight: 800; font-size: 22px; color: var(--blue); margin-bottom: 36px; display: block; }
    .menu-label { font-size: 12px; color: var(--muted); font-weight: 700; margin-bottom: 12px; text-transform: uppercase; }
    .menu-item { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border-radius: 14px; color: #334155; text-decoration: none; font-size: 14px; font-weight: 600; margin-bottom: 8px; transition: .25s ease; }
    .menu-item:hover, .menu-item.active { background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; transform: translateX(4px); }
    .menu-toggle { display: flex; align-items: center; justify-content: space-between; }
    .menu-toggle .chevron { font-size: 11px; transition: transform .2s ease; }
    .menu-toggle[aria-expanded="true"] .chevron { transform: rotate(180deg); }
    .submenu { display: flex; flex-direction: column; padding-left: 16px; margin-bottom: 4px; }
    .submenu-item { font-size: 12.5px; padding: 9px 14px; }
    .logout { position: absolute; bottom: 24px; left: 20px; right: 20px; background: #fee2e2; color: #b91c1c; text-align: center; }

    /* MAIN */
    .main-content { margin-left: 270px; width: calc(100% - 270px); padding: 34px; }
    .topbar { background: white; border-radius: 22px; padding: 22px 26px; box-shadow: var(--shadow); display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; }
    .page-title { font-size: 28px; font-weight: 800; margin: 0; background: linear-gradient(90deg, var(--blue), var(--teal)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .page-desc { color: var(--muted); font-size: 14px; margin: 4px 0 0; }

    .content-card { background: white; border-radius: 20px; border: 1px solid #eef2f7; box-shadow: var(--shadow); padding: 26px; }
    .section-header { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
    .section-title { font-size: 20px; font-weight: 800; margin: 0; }
    .btn-gradient { background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; border: none; border-radius: 12px; padding: 10px 18px; font-weight: 700; font-size: 14px; cursor: pointer; }
    .btn-gradient:hover { opacity: .88; color: white; }

    /* TABLE */
    .table { vertical-align: middle; font-size: 14px; }
    .table thead th { background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase; border-bottom: none; padding: 14px; }
    .table tbody td { padding: 14px; color: #334155; }
    .foto-mini { width: 60px; height: 48px; object-fit: cover; border-radius: 10px; background: #f1f5f9; }
    .foto-placeholder { width: 60px; height: 48px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 20px; }
    .action-btn { border: none; border-radius: 10px; padding: 7px 11px; font-size: 12px; font-weight: 700; margin-right: 5px; text-decoration: none; cursor: pointer; display: inline-block; }
    .edit-btn { background: #dbeafe; color: #1d4ed8; }
    .delete-btn { background: #fee2e2; color: #b91c1c; }
    .badge-kategori { background: #e0f2fe; color: #0369a1; border-radius: 999px; padding: 4px 10px; font-size: 11px; font-weight: 700; }
    .link-maps { color: var(--teal); font-size: 12px; font-weight: 600; text-decoration: none; }
    .link-maps:hover { text-decoration: underline; }

    /* MODAL */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.45); z-index: 9999; align-items: flex-start; justify-content: center; padding: 30px 20px; overflow-y: auto; }
    .modal-overlay.show { display: flex; }
    .modal-box { background: white; border-radius: 22px; padding: 30px; width: 100%; max-width: 640px; box-shadow: var(--shadow); animation: pop .25s ease; margin: auto; }
    @keyframes pop { from { transform: scale(.94); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
    .modal-title { font-size: 20px; font-weight: 800; }
    .btn-tutup { background: #f1f5f9; border: none; border-radius: 50%; width: 34px; height: 34px; font-size: 18px; cursor: pointer; color: #64748b; }
    .form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px; display: block; }
    .form-control, .form-select, .form-textarea { border-radius: 12px; padding: 10px 13px; font-size: 14px; width: 100%; border: 1px solid var(--border); font-family: 'Poppins', sans-serif; transition: border .2s; }
    .form-control:focus, .form-select:focus, .form-textarea:focus { border-color: var(--blue); outline: none; box-shadow: 0 0 0 3px rgba(47,109,246,.1); }
    .form-textarea { resize: vertical; min-height: 80px; }
    .upload-area { border: 2px dashed var(--border); border-radius: 14px; padding: 18px; text-align: center; cursor: pointer; transition: border .2s; }
    .upload-area:hover { border-color: var(--blue); }
    .upload-area input { display: none; }
    .preview-foto { width: 100%; max-height: 180px; object-fit: cover; border-radius: 10px; margin-top: 10px; display: none; }
    .rating-wrapper { display: flex; gap: 8px; align-items: center; }
    .rating-wrapper input { width: 80px; }
    .row-form { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media (max-width: 600px) { .row-form { grid-template-columns: 1fr; } }

    @media (max-width: 991px) {
      .sidebar { position: static; width: 100%; height: auto; }
      .logout { position: static; margin-top: 20px; }
      .admin-wrapper { flex-direction: column; }
      .main-content { margin-left: 0; width: 100%; padding: 20px; }
    }
  </style>
</head>
<body>

<div class="admin-wrapper">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <span class="logo-text">19JutaAdmin</span>
    <div class="menu-label">Menu Admin</div>
    <a class="menu-item" href="adminDashboard.php">📊 Dashboard</a>
    <a class="menu-item" href="halamanKelolaLomba.php">🏆 Kelola Lomba</a>
    <a class="menu-item" href="halamanKelolaBeasiswa.php">🎓 Kelola Beasiswa</a>
    <a href="#" class="menu-item menu-toggle active" data-bs-toggle="collapse" data-bs-target="#submenuTempat" role="button" aria-expanded="true">
      <span>📍 Kelola Tempat / Peta</span>
      <span class="chevron">▾</span>
    </a>
    <div class="collapse show submenu" id="submenuTempat">
      <a href="halamanKelolaTempat.php" class="menu-item submenu-item active">📋 Daftar Tempat</a>
      <a href="HalamanVerifikasiTempat.php" class="menu-item submenu-item">✅ Verifikasi Pengajuan</a>
      </div>
      <a href="halamanKelolaFiturBeranda.php" class="menu-item">🏠 Kelola Fitur Beranda</a>
      <a href="halamanKelolaBlog.php" class="menu-item">📝 Kelola Blog</a>
      
    <a class="menu-item" href="halamanVerifikasi.php">✅ Verifikasi Iklan</a>
    <a href="logout.php" class="menu-item logout">🚪 Logout</a>
  </aside>

  <main class="main-content">
    <!-- TOPBAR -->
    <div class="topbar">
      <div>
        <h1 class="page-title">Kelola Tempat Edukatif</h1>
        <p class="page-desc">Tambah, edit, dan hapus data tempat edukatif yang tampil di Peta Akses Pendidikan.</p>
      </div>
      <button class="btn-gradient" onclick="bukaModalTambah()">+ Tambah Tempat</button>
    </div>

    <!-- ALERT PESAN -->
    <?php if (isset($_GET['pesan'])): ?>
    <?php
      $pesanMap = [
        'tambah_sukses' => ['✅ Tempat edukatif berhasil ditambahkan!', 'success'],
        'edit_sukses'   => ['✏️ Data tempat berhasil diperbarui!', 'success'],
        'hapus_sukses'  => ['🗑️ Tempat edukatif berhasil dihapus.', 'warning'],
      ];
      $info = $pesanMap[$_GET['pesan']] ?? ['⚡ Tindakan berhasil diproses.', 'info'];
    ?>
    <div class="alert alert-<?= $info[1] ?> alert-dismissible fade show rounded-4 mb-4" role="alert">
      <?= $info[0] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- TABEL DATA -->
    <div class="content-card">
      <div class="section-header">
        <h2 class="section-title">Daftar Tempat Edukatif</h2>
        <span class="text-muted" style="font-size:13px;">
          <?= mysqli_num_rows($list_tempat) ?> tempat terdaftar
        </span>
      </div>

      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Foto</th>
              <th>Nama Tempat</th>
              <th>Kategori</th>
              <th>Jam Operasional</th>
              <th>Rating</th>
              <th>Link Maps</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            mysqli_data_seek($list_tempat, 0);
            while ($t = mysqli_fetch_assoc($list_tempat)):
            ?>
            <tr>
              <td>
                <?php if ($t['foto']): ?>
                  <img src="uploads/<?= htmlspecialchars($t['foto']) ?>" class="foto-mini" alt="foto">
                <?php else: ?>
                  <div class="foto-placeholder">📷</div>
                <?php endif; ?>
              </td>
              <td>
                <strong><?= htmlspecialchars($t['nama_tempat_edukatif']) ?></strong>
                <?php if ($t['prasarana']): ?>
                  <br><small class="text-muted"><?= htmlspecialchars(substr($t['prasarana'], 0, 45)) ?>...</small>
                <?php endif; ?>
              </td>
              <td><span class="badge-kategori"><?= htmlspecialchars($t['kategori'] ?? '-') ?></span></td>
              <td style="font-size:12px;"><?= htmlspecialchars($t['jam_operasional'] ?? '-') ?></td>
              <td>
                <?php if ($t['rating']): ?>
                  ⭐ <?= htmlspecialchars($t['rating']) ?>
                <?php else: ?>
                  <span class="text-muted">–</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($t['alamat_maps']): ?>
                  <a href="<?= htmlspecialchars($t['alamat_maps']) ?>" target="_blank" class="link-maps">🗺️ Buka Maps</a>
                <?php else: ?>
                  <span class="text-muted" style="font-size:12px;">–</span>
                <?php endif; ?>
              </td>
              <td>
                <button class="action-btn edit-btn"
                  onclick="bukaModalEdit(
                    '<?= $t['id_tempat'] ?>',
                    '<?= addslashes(htmlspecialchars($t['nama_tempat_edukatif'])) ?>',
                    '<?= addslashes(htmlspecialchars($t['alamat_maps'] ?? '')) ?>',
                    '<?= addslashes(htmlspecialchars($t['kategori'] ?? '')) ?>',
                    '<?= addslashes(htmlspecialchars($t['prasarana'] ?? '')) ?>',
                    '<?= addslashes(htmlspecialchars($t['rating'] ?? '')) ?>',
                    '<?= addslashes(htmlspecialchars($t['sosial_media'] ?? '')) ?>',
                    '<?= addslashes(htmlspecialchars($t['jam_operasional'] ?? '')) ?>',
                    '<?= $t['foto'] ?? '' ?>'
                  )">✏️ Edit</button>
                <a href="?hapus_id=<?= $t['id_tempat'] ?>" class="action-btn delete-btn"
                  onclick="return confirm('Hapus tempat ini? Foto juga akan dihapus.')">🗑️ Hapus</a>
              </td>
            </tr>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($list_tempat) == 0): ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-4">
                Belum ada tempat edukatif. Klik "+ Tambah Tempat" untuk memulai.
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<!-- ═══ MODAL TAMBAH ═══════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalTambah">
  <div class="modal-box">
    <div class="modal-header">
      <span class="modal-title">📍 Tambah Tempat Edukatif</span>
      <button class="btn-tutup" onclick="tutupModal('modalTambah')">×</button>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="aksi" value="tambah">

      <div class="mb-3">
        <label class="form-label">Nama Tempat Edukatif *</label>
        <input type="text" name="nama_tempat_edukatif" class="form-control" placeholder="Contoh: Perpustakaan Pusat UNS" required>
      </div>

      <div class="row-form mb-3">
        <div>
          <label class="form-label">Kategori *</label>
          <select name="kategori" class="form-select" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="perpustakaan">📚 Perpustakaan</option>
            <option value="kafe-belajar">☕ Kafe Belajar</option>
            <option value="teknologi">💡 Teknologi & Inovasi</option>
            <option value="museum">🏛️ Museum & Sejarah</option>
            <option value="ruang-kreatif">🎨 Ruang Kreatif</option>
            <option value="tempat-makan">🍽️ Tempat Makan</option>
            <option value="ruang-terbuka">🌳 Ruang Terbuka</option>
            <option value="lainnya">📌 Lainnya</option>
          </select>
        </div>
        <div>
          <label class="form-label">Rating (contoh: 4.5)</label>
          <input type="text" name="rating" class="form-control" placeholder="4.5">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Link Google Maps *</label>
        <input type="url" name="alamat_maps" class="form-control" placeholder="https://maps.google.com/..." required>
        <small class="text-muted">Paste link share dari Google Maps</small>
      </div>

      <div class="mb-3">
        <label class="form-label">Jam Operasional</label>
        <input type="text" name="jam_operasional" class="form-control" placeholder="Senin–Jumat: 08.00–21.00 | Sabtu: 08.00–15.00">
      </div>

      <div class="mb-3">
        <label class="form-label">Prasarana / Fasilitas</label>
        <textarea name="prasarana" class="form-textarea" placeholder="Contoh: WiFi, Ruang Baca, AC, Coworking Space, Outlet Listrik"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Sosial Media</label>
        <input type="text" name="sosial_media" class="form-control" placeholder="@namainstagram atau https://instagram.com/...">
      </div>

      <div class="mb-3">
        <label class="form-label">Foto Tempat</label>
        <div class="upload-area" onclick="document.getElementById('fotoTambah').click()">
          <div style="font-size:32px;">📷</div>
          <p style="margin:6px 0 0; font-size:13px; color:#64748b;">Klik untuk upload foto (JPG/PNG/WEBP)</p>
          <input type="file" id="fotoTambah" name="foto" accept=".jpg,.jpeg,.png,.webp" onchange="previewGambar(this,'previewTambah')">
          <img id="previewTambah" class="preview-foto" alt="preview">
        </div>
      </div>

      <button type="submit" class="btn-gradient w-100" style="padding:12px;">Simpan ke Database</button>
    </form>
  </div>
</div>

<!-- ═══ MODAL EDIT ═════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="modalEdit">
  <div class="modal-box">
    <div class="modal-header">
      <span class="modal-title">✏️ Edit Tempat Edukatif</span>
      <button class="btn-tutup" onclick="tutupModal('modalEdit')">×</button>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="aksi" value="edit">
      <input type="hidden" name="id_tempat" id="edit_id">

      <div class="mb-3">
        <label class="form-label">Nama Tempat Edukatif *</label>
        <input type="text" name="nama_tempat_edukatif" id="edit_nama" class="form-control" required>
      </div>

      <div class="row-form mb-3">
        <div>
          <label class="form-label">Kategori *</label>
          <select name="kategori" id="edit_kategori" class="form-select" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="perpustakaan">📚 Perpustakaan</option>
            <option value="kafe-belajar">☕ Kafe Belajar</option>
            <option value="teknologi">💡 Teknologi & Inovasi</option>
            <option value="museum">🏛️ Museum & Sejarah</option>
            <option value="ruang-kreatif">🎨 Ruang Kreatif</option>
            <option value="tempat-makan">🍽️ Tempat Makan</option>
            <option value="ruang-terbuka">🌳 Ruang Terbuka</option>
            <option value="lainnya">📌 Lainnya</option>
          </select>
        </div>
        <div>
          <label class="form-label">Rating</label>
          <input type="text" name="rating" id="edit_rating" class="form-control" placeholder="4.5">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Link Google Maps *</label>
        <input type="url" name="alamat_maps" id="edit_alamat" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Jam Operasional</label>
        <input type="text" name="jam_operasional" id="edit_jam" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Prasarana / Fasilitas</label>
        <textarea name="prasarana" id="edit_prasarana" class="form-textarea"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Sosial Media</label>
        <input type="text" name="sosial_media" id="edit_sosmed" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Ganti Foto (kosongkan jika tidak ingin mengganti)</label>
        <div id="fotoLamaWrapper" style="margin-bottom:8px;display:none;">
          <img id="fotoLamaSrc" src="" style="height:70px;border-radius:10px;object-fit:cover;" alt="foto lama">
          <small class="text-muted d-block mt-1">Foto saat ini</small>
        </div>
        <div class="upload-area" onclick="document.getElementById('fotoEdit').click()">
          <div style="font-size:28px;">🔄</div>
          <p style="margin:4px 0 0; font-size:13px; color:#64748b;">Klik untuk upload foto baru</p>
          <input type="file" id="fotoEdit" name="foto" accept=".jpg,.jpeg,.png,.webp" onchange="previewGambar(this,'previewEdit')">
          <img id="previewEdit" class="preview-foto" alt="preview baru">
        </div>
      </div>

      <button type="submit" class="btn-gradient w-100" style="padding:12px;">Simpan Perubahan</button>
    </form>
  </div>
</div>

<script>
  function bukaModalTambah() {
    document.getElementById('modalTambah').classList.add('show');
  }

  function bukaModalEdit(id, nama, alamat, kategori, prasarana, rating, sosmed, jam, foto) {
    document.getElementById('edit_id').value       = id;
    document.getElementById('edit_nama').value     = nama;
    document.getElementById('edit_alamat').value   = alamat;
    document.getElementById('edit_kategori').value = kategori;
    document.getElementById('edit_prasarana').value= prasarana;
    document.getElementById('edit_rating').value   = rating;
    document.getElementById('edit_sosmed').value   = sosmed;
    document.getElementById('edit_jam').value      = jam;

    const fotoWrapper = document.getElementById('fotoLamaWrapper');
    if (foto) {
      document.getElementById('fotoLamaSrc').src = 'uploads/' + foto;
      fotoWrapper.style.display = 'block';
    } else {
      fotoWrapper.style.display = 'none';
    }

    // Reset preview baru
    const prev = document.getElementById('previewEdit');
    prev.style.display = 'none';
    document.getElementById('fotoEdit').value = '';

    document.getElementById('modalEdit').classList.add('show');
  }

  function tutupModal(id) {
    document.getElementById(id).classList.remove('show');
  }

  // Tutup modal klik di luar box
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) overlay.classList.remove('show');
    });
  });

  function previewGambar(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => {
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
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

// 2. PROSES HAPUS DATA LOMBA (SAMA SEPERTI SEBELUMNYA)
if (isset($_GET['hapus_id'])) {
    $id_lomba = mysqli_real_escape_string($conn, $_GET['hapus_id']);
    
    // Ambil nama poster lama untuk dihapus filenya
    $query_file = mysqli_query($conn, "SELECT poster FROM lomba WHERE id_lomba = '$id_lomba'");
    $data_file = mysqli_fetch_assoc($query_file);
    $poster_lama = $data_file['poster'];
    
    $query_hapus = "DELETE FROM lomba WHERE id_lomba = '$id_lomba'";
    
    if (mysqli_query($conn, $query_hapus)) {
        // Hapus file fisik poster jika ada dan bukan poster default
        if ($poster_lama && file_exists("uploads/" . $poster_lama) && $poster_lama != 'default.jpg') {
            unlink("uploads/" . $poster_lama);
        }
        header("Location: halamanKelolaLomba.php?pesan=hapus_sukses");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit();
}

// 3. PROSES UPDATE DATA LOMBA + POSTER VIA MODAL
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_lomba'])) {
    $id_lomba = mysqli_real_escape_string($conn, $_POST['id_lomba']);
    
    // Menangkap data teks dari form modal
    $judul         = mysqli_real_escape_string($conn, $_POST['judul_lomba']);
    $penyelenggara = mysqli_real_escape_string($conn, $_POST['penyelenggara']);
    $kategori      = mysqli_real_escape_string($conn, $_POST['kategori']);
    $tingkat       = mysqli_real_escape_string($conn, $_POST['tingkat_lomba']);
    $deadline      = mysqli_real_escape_string($conn, $_POST['deadline']);
    $tipe_biaya    = mysqli_real_escape_string($conn, $_POST['tipe_biaya']);
    $biaya         = mysqli_real_escape_string($conn, $_POST['biaya']);
    $deskripsi     = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    // Logika Pemrosesan Gambar (POSTER BARU)
    // Ambil data poster lama untuk backup dan penghapusan jika diganti
    $query_old = mysqli_query($conn, "SELECT poster FROM lomba WHERE id_lomba = '$id_lomba'");
    $data_old = mysqli_fetch_assoc($query_old);
    $poster_fix = $data_old['poster']; // Default: pakai yang lama
    
    // Cek apakah admin mengupload file baru di input 'poster'
    if ($_FILES['poster']['name'] != "") {
        $target_dir = "uploads/";
        $nama_file_lama = $_FILES['poster']['name'];
        $file_extension = pathinfo($nama_file_lama, PATHINFO_EXTENSION);
        
        // Buat nama unik baru untuk poster agar tidak bentrok
        $nama_poster_baru = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $nama_poster_baru;
        
        // Pindahkan file baru
        if (move_uploaded_file($_FILES["poster"]["tmp_name"], $target_file)) {
            $poster_fix = $nama_poster_baru; // Set nama file baru untuk disimpan ke DB
            
            // Hapus file poster lama (biar server gak penuh), kecuali default.jpg
            if ($data_old['poster'] && file_exists("uploads/" . $data_old['poster']) && $data_old['poster'] != 'default.jpg') {
                unlink("uploads/" . $data_old['poster']);
            }
        }
    }

    // Mulai Update Database (Sekarang mencakup kolom 'poster')
    $query_update = "UPDATE lomba SET 
                        judul_lomba = '$judul', 
                        penyelenggara = '$penyelenggara',
                        kategori = '$kategori', 
                        tingkat_lomba = '$tingkat',
                        deadline = '$deadline',
                        tipe_biaya = '$tipe_biaya',
                        biaya = '$biaya',
                        deskripsi = '$deskripsi',
                        poster = '$poster_fix' 
                    WHERE id_lomba = '$id_lomba'";
    
    if (mysqli_query($conn, $query_update)) {
        header("Location: halamanKelolaLomba.php?pesan=update_sukses");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit();
}

// 4. AMBIL DATA LOMBA UNTUK DITAMPILKAN DI TABEL
$list_lomba = mysqli_query($conn, "SELECT * FROM lomba ORDER BY id_lomba DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Lomba - 19JutaAdmin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght=400;500;600;700;800&display=swap');
    :root {
      --blue: #2f6df6; --teal: #35c7b6; --dark: #14213d; --muted: #6b7280;
      --soft-bg: #eef8fb; --card: #ffffff; --shadow: 0 18px 45px rgba(20, 33, 61, 0.10); --border: #e2e8f0;
    }
    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
    body { background: linear-gradient(180deg, #eef8fb 0%, #f7fcfb 100%); color: var(--dark); min-height: 100vh; }
    .admin-wrapper { display: flex; min-height: 100vh; }
    .sidebar { width: 270px; background: #ffffff; border-right: 1px solid var(--border); padding: 26px 20px; position: fixed; height: 100vh; box-shadow: 8px 0 30px rgba(20, 33, 61, 0.05); overflow-y: auto;}
    .logo-text { font-weight: 800; font-size: 22px; color: var(--blue); margin-bottom: 36px; }
    .menu-item { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border-radius: 14px; color: #334155; text-decoration: none; font-size: 14px; font-weight: 600; margin-bottom: 8px; transition: 0.25s ease; cursor: pointer; }
    .menu-item:hover, .menu-item.active { background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; transform: translateX(4px); }
    .main-content { margin-left: 270px; width: calc(100% - 270px); padding: 34px; }
    .topbar { background: white; border-radius: 22px; padding: 22px 26px; box-shadow: var(--shadow); display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
    .page-title { font-size: 30px; font-weight: 800; margin: 0; background: linear-gradient(90deg, var(--blue), var(--teal)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .content-card { background: white; border-radius: 20px; border: 1px solid #eef2f7; box-shadow: var(--shadow); padding: 26px; }
    .table { vertical-align: middle; font-size: 13px; }
    .table thead th { background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase; padding: 14px; }
    .table tbody td { padding: 12px 14px; }
    .table-poster { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;}
    .action-btn { border: none; border-radius: 8px; padding: 6px 10px; font-size: 11px; font-weight: 700; text-decoration: none; display: inline-block; }
    .edit-btn { background: #dbeafe; color: #1d4ed8; }
    .delete-btn { background: #fee2e2; color: #b91c1c; }
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); z-index: 9999; align-items: center; justify-content: center; padding: 20px; overflow-y: auto;}
    .modal-box { background: white; border-radius: 22px; padding: 28px; width: 100%; max-width: 800px; box-shadow: var(--shadow); animation: pop 0.25s ease; margin: auto; }
    @keyframes pop { from { transform: scale(0.94); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .modal-box label { font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 5px; }
    .form-control, .form-select { border-radius: 10px; font-size: 13px; border: 1px solid #cbd5e1; }
    .btn-gradient { background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; border: none; border-radius: 12px; padding: 12px 16px; font-weight: 700; width: 100%; margin-top: 15px; }
    @media (max-width: 991px) { .sidebar { position: static; width: 100%; height: auto; } .main-content { margin-left: 0; width: 100%; padding: 20px; } }
  </style>
</head>
<body>

  <div class="admin-wrapper">
    <aside class="sidebar">
      <div class="logo-text">19JutaAdmin</div>
      <a href="adminDashboard.php" class="menu-item">📊 Dashboard</a>
      <a href="halamanKelolaLomba.php" class="menu-item active">🏆 Kelola Lomba</a>
      <a href="halamanVerifikasi.php" class="menu-item">✅ Verifikasi Iklan</a>
    </aside>

    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 class="page-title">Kelola Data Lomba</h1>
          <p class="text-muted small mb-0">Halaman admin untuk mengubah informasi detail lomba termasuk poster.</p>
        </div>
      </div>

      <?php if(isset($_GET['pesan'])): ?>
        <?php if($_GET['pesan'] == 'update_sukses'): ?>
            <div class="alert alert-success rounded-4 mb-4">✨ Data & Poster berhasil diupdate!</div>
        <?php elseif($_GET['pesan'] == 'hapus_sukses'): ?>
            <div class="alert alert-danger rounded-4 mb-4">🗑️ Data lomba berhasil dihapus!</div>
        <?php endif; ?>
      <?php endif; ?>

      <div class="content-card">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Poster</th> <th>Judul & Penyelenggara</th>
                <th>Kategori / Tingkat</th>
                <th>Deadline</th>
                <th>Biaya</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php while($l = mysqli_fetch_assoc($list_lomba)): 
                $file_poster = (!empty($l['poster']) && file_exists("uploads/".$l['poster'])) ? $l['poster'] : 'default.jpg';
              ?>
              <tr>
                <td><img src="uploads/<?= htmlspecialchars($file_poster) ?>" alt="Poster" class="table-poster"></td>
                <td>
                  <div class="fw-semibold text-dark"><?= htmlspecialchars($l['judul_lomba']) ?></div>
                  <div class="text-muted small"><?= htmlspecialchars($l['penyelenggara'] ?? '-') ?></div>
                </td>
                <td>
                  <span class="badge bg-primary"><?= htmlspecialchars($l['kategori'] ?? '-') ?></span>
                  <div class="mt-1 small text-muted"><?= htmlspecialchars($l['tingkat_lomba'] ?? '-') ?></div>
                </td>
                <td class="fw-semibold text-danger"><?= htmlspecialchars($l['deadline'] ?? '-') ?></td>
                <td>
                  <div class="fw-semibold"><?= htmlspecialchars($l['tipe_biaya'] ?? '-') ?></div>
                  <small class="text-muted">Rp <?= number_format($l['biaya'] ?? 0, 0, ',', '.') ?></small>
                </td>
                <td class="text-center" style="white-space: nowrap;">
                  <button class="action-btn edit-btn" onclick="openEditModal(
                    '<?= $l['id_lomba'] ?>', 
                    '<?= addslashes($l['judul_lomba']) ?>',
                    '<?= addslashes($l['penyelenggara'] ?? '') ?>',
                    '<?= addslashes($l['kategori'] ?? '') ?>',
                    '<?= addslashes($l['tingkat_lomba'] ?? '') ?>',
                    '<?= addslashes($l['deadline'] ?? '') ?>',
                    '<?= addslashes($l['tipe_biaya'] ?? '') ?>',
                    '<?= $l['biaya'] ?? 0 ?>',
                    '<?= addslashes(str_replace(["\r", "\n"], ' ', $l['deskripsi'] ?? '')) ?>',
                    '<?= htmlspecialchars($file_poster) ?>'
                  )">✏️ Edit</button>
                  <a href="?hapus_id=<?= $l['id_lomba'] ?>" class="action-btn delete-btn" onclick="return confirm('Hapus lomba ini?')">❌ Hapus</a>
                </td>
              </tr>
              <?php endwhile; if(mysqli_num_rows($list_lomba) == 0) echo "<tr><td colspan='6' class='text-center py-4'>Belum ada data lomba.</td></tr>"; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <div class="modal-overlay" id="editModal">
    <div class="modal-box">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Update Data Lomba & Poster</h5>
        <button type="button" class="btn-close" onclick="closeModal()"></button>
      </div>

      <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_lomba" value="1">
        <input type="hidden" name="id_lomba" id="modalId">
        
        <div class="row mb-3 align-items-center">
            <div class="col-md-3 text-center">
                <img src="" id="modalPreviewPoster" alt="Poster Lomba" class="img-thumbnail" style="height: 100px; width: 100px; object-fit: cover;">
            </div>
            <div class="col-md-9">
                <label>Update Poster Lomba <span class="text-muted">(Kosongkan jika tidak ingin mengubah)</span></label>
                <input type="file" name="poster" id="modalInputPoster" class="form-control" accept="image/jpeg,image/png">
                <small class="text-muted mt-1 d-block" style="font-size: 11px;">Hanya menerima format JPG, JPEG, PNG.</small>
            </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3"><label>Nama Lomba</label><input type="text" name="judul_lomba" id="modalJudul" class="form-control" required></div>
          <div class="col-md-6 mb-3"><label>Penyelenggara</label><input type="text" name="penyelenggara" id="modalPenyelenggara" class="form-control" required></div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label>Kategori</label>
            <select name="kategori" id="modalKategori" class="form-select" required>
              <option value="Akademik">Akademik</option><option value="Desain">Desain</option><option value="Teknologi">Teknologi</option><option value="Esai">Esai</option><option value="Bisnis">Bisnis</option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label>Tingkat Lomba</label>
            <select name="tingkat_lomba" id="modalTingkat" class="form-select" required>
              <option value="Sekolah">Sekolah / Instansi</option><option value="Regional">Regional / Provinsi</option><option value="Nasional">Nasional</option><option value="Internasional">Internasional</option>
            </select>
          </div>
          <div class="col-md-4 mb-3"><label>Deadline</label><input type="date" name="deadline" id="modalDeadline" class="form-control" required></div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label>Tipe Biaya</label>
            <select name="tipe_biaya" id="modalTipeBiaya" class="form-select" required onchange="cekTipeBiaya()"><option value="Gratis">Gratis</option><option value="Berbayar">Berbayar</option></select>
          </div>
          <div class="col-md-8 mb-3"><label>Nominal Biaya (Rp)</label><input type="number" name="biaya" id="modalBiaya" class="form-control" min="0" required></div>
        </div>

        <div class="mb-3"><label>Deskripsi Lomba</label><textarea name="deskripsi" id="modalDeskripsi" class="form-control" rows="4" required></textarea></div>
        
        <button type="submit" class="btn-gradient">Simpan Semua Perubahan Data</button>
      </form>
    </div>
  </div>

  <script>
    function openEditModal(id, judul, penyelenggara, kategori, tingkat, deadline, tipe_biaya, biaya, deskripsi, poster_path) {
      document.getElementById('modalId').value = id;
      document.getElementById('modalJudul').value = judul;
      document.getElementById('modalPenyelenggara').value = penyelenggara;
      document.getElementById('modalKategori').value = kategori;
      document.getElementById('modalTingkat').value = tingkat;
      document.getElementById('modalDeadline').value = deadline;
      document.getElementById('modalTipeBiaya').value = tipe_biaya;
      document.getElementById('modalBiaya').value = biaya;
      document.getElementById('modalDeskripsi').value = deskripsi;
      
      // Update Preview Poster Lama di Modal
      document.getElementById('modalPreviewPoster').src = 'uploads/' + poster_path;
      document.getElementById('modalInputPoster').value = ''; // Reset input file jika terbuka lagi

      cekTipeBiaya();
      document.getElementById('editModal').style.display = 'flex';
    }

    function closeModal() { document.getElementById('editModal').style.display = 'none'; }

    function cekTipeBiaya() {
      let tipe = document.getElementById('modalTipeBiaya').value;
      let inputBiaya = document.getElementById('modalBiaya');
      if(tipe === 'Gratis') { inputBiaya.value = 0; inputBiaya.readOnly = true; } else { inputBiaya.readOnly = false; }
    }
  </script>
</body>
</html>
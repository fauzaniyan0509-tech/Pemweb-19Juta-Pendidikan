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

// 2. PROSES HAPUS DATA BEASISWA
if (isset($_GET['hapus_id'])) {
    $id_beasiswa = mysqli_real_escape_string($conn, $_GET['hapus_id']);
    
    // Ambil nama poster lama untuk dihapus filenya
    $query_file = mysqli_query($conn, "SELECT poster FROM beasiswa WHERE id_beasiswa = '$id_beasiswa'");
    $data_file = mysqli_fetch_assoc($query_file);
    $poster_lama = $data_file['poster'];
    
    $query_hapus = "DELETE FROM beasiswa WHERE id_beasiswa = '$id_beasiswa'";
    
    if (mysqli_query($conn, $query_hapus)) {
        // Hapus file fisik poster jika ada dan bukan poster default
        if ($poster_lama && file_exists("uploads/" . $poster_lama) && $poster_lama != 'default.jpg') {
            unlink("uploads/" . $poster_lama);
        }
        header("Location: halamanKelolaBeasiswa.php?pesan=hapus_sukses");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit();
}

// 3. PROSES UPDATE DATA BEASISWA + POSTER VIA MODAL
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_beasiswa'])) {
    $id_beasiswa = mysqli_real_escape_string($conn, $_POST['id_beasiswa']);
    
    // Menangkap data teks dari form modal sesuai struktur kolom tabel beasiswa
    $nama_beasiswa   = mysqli_real_escape_string($conn, $_POST['nama_beasiswa']);
    $penyelenggara   = mysqli_real_escape_string($conn, $_POST['penyelenggara']);
    $jenjang         = mysqli_real_escape_string($conn, $_POST['jenjang']);
    $tingkat_beasiswa = mysqli_real_escape_string($conn, $_POST['tingkat_beasiswa']);
    $deadline        = mysqli_real_escape_string($conn, $_POST['deadline']);
    $tipe_pendanaan  = mysqli_real_escape_string($conn, $_POST['tipe_pendanaan']);
    $deskripsi       = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    // Logika Pemrosesan Gambar (POSTER BARU)
    $query_old = mysqli_query($conn, "SELECT poster FROM beasiswa WHERE id_beasiswa = '$id_beasiswa'");
    $data_old = mysqli_fetch_assoc($query_old);
    $poster_fix = $data_old['poster']; // Default: pakai yang lama
    
    if ($_FILES['poster']['name'] != "") {
        $target_dir = "uploads/";
        $nama_file_lama = $_FILES['poster']['name'];
        $file_extension = pathinfo($nama_file_lama, PATHINFO_EXTENSION);
        
        // Buat nama unik baru untuk poster
        $nama_poster_baru = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $nama_poster_baru;
        
        if (move_uploaded_file($_FILES["poster"]["tmp_name"], $target_file)) {
            $poster_fix = $nama_poster_baru;
            
            if ($data_old['poster'] && file_exists("uploads/" . $data_old['poster']) && $data_old['poster'] != 'default.jpg') {
                unlink("uploads/" . $data_old['poster']);
            }
        }
    }

    // Mulai Update Database tabel beasiswa
    $query_update = "UPDATE beasiswa SET 
                        nama_beasiswa = '$nama_beasiswa', 
                        penyelenggara = '$penyelenggara',
                        jenjang = '$jenjang', 
                        tingkat_beasiswa = '$tingkat_beasiswa',
                        deadline = '$deadline',
                        tipe_pendanaan = '$tipe_pendanaan',
                        deskripsi = '$deskripsi',
                        poster = '$poster_fix' 
                    WHERE id_beasiswa = '$id_beasiswa'";
    
    if (mysqli_query($conn, $query_update)) {
        header("Location: halamanKelolaBeasiswa.php?pesan=update_sukses");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit();
}

// 4. AMBIL DATA BEASISWA & HITUNG ANTRIAN NOTIFIKASI IKLAN BEASISWA
$list_beasiswa = mysqli_query($conn, "SELECT * FROM beasiswa ORDER BY id_beasiswa DESC");

// Query menghitung iklan beasiswa yang butuh verifikasi (status 'menunggu')
$query_menunggu = mysqli_query($conn, "SELECT COUNT(*) as total FROM iklan_beasiswa WHERE status_verifikasi = 'menunggu'");
$data_menunggu = mysqli_fetch_assoc($query_menunggu);
$total_menunggu = $data_menunggu['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Beasiswa - 19JutaAdmin</title>
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
    
    /* Sidebar Styling */
    .sidebar { width: 270px; background: #ffffff; border-right: 1px solid var(--border); padding: 26px 20px; position: fixed; height: 100vh; box-shadow: 8px 0 30px rgba(20, 33, 61, 0.05); overflow-y: auto; display: flex; flex-direction: column; }
    .logo-text { font-weight: 800; font-size: 22px; color: var(--blue); margin-bottom: 24px; }
    .menu-label { font-size: 11px; color: var(--muted); font-weight: 700; margin-top: 16px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .menu-item { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 12px; color: #334155; text-decoration: none; font-size: 13.5px; font-weight: 600; margin-bottom: 4px; transition: 0.25s ease; cursor: pointer; }
    .menu-item:hover, .menu-item.active { background: linear-gradient(90deg, var(--blue), var(--teal)); color: white; transform: translateX(4px); }
    .logout { background: #fee2e2; color: #b91c1c; text-align: center; justify-content: center; margin-top: auto; margin-bottom: 10px; }
    .logout:hover { background: #fca5a5; color: #b91c1c; transform: none; }
    
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
    @media (max-width: 991px) { .sidebar { position: static; width: 100%; height: auto; } .logout { margin-top: 20px; } .admin-wrapper { flex-direction: column; } .main-content { margin-left: 0; width: 100%; padding: 20px; } }
  </style>
</head>
<body>

  <div class="admin-wrapper">
    <aside class="sidebar">
      <div class="logo-text">19JutaAdmin</div>
      
      <div class="menu-label">Navigasi Utama</div>
      <a href="adminDashboard.php" class="menu-item">📊 Dashboard</a>
      
      <div class="menu-label">Manajemen Data</div>
      <a href="halamanKelolaLomba.php" class="menu-item">🏆 Kelola Lomba</a>
      <a href="halamanKelolaBeasiswa.php" class="menu-item active">🎓 Kelola Beasiswa</a>
      <a href="halamanKelolaTempat.php" class="menu-item">📍 Kelola Tempat / Peta</a>
      <a href="halamanKelolaTransaksi.php" class="menu-item">💳 Kelola Transaksi</a>
      
      <div class="menu-label">Sistem Validasi</div>
      <a href="halamanVerifikasi.php" class="menu-item" id="menu-verif-sidebar">
        ✅ Verifikasi Iklan 
        <span id="badge-notif" class="badge bg-danger ms-auto" style="display: none; font-size: 11px; border-radius: 50%;">0</span>
      </a>

      <div class="menu-label">Pengaturan</div>
      <a href="halamanKelolaUser.php" class="menu-item">👥 Kelola Pengguna</a>
      <a href="logout.php" class="menu-item logout">🚪 Logout</a>
    </aside>

    <main class="main-content">
      <div class="topbar">
        <div>
          <h1 class="page-title">Kelola Data Beasiswa</h1>
          <p class="text-muted small mb-0">Halaman admin untuk mengubah informasi detail program beasiswa dan poster.</p>
        </div>
      </div>

      <?php if(isset($_GET['pesan'])): ?>
        <?php if($_GET['pesan'] == 'update_sukses'): ?>
            <div class="alert alert-success rounded-4 mb-4">✨ Data & Poster Beasiswa berhasil diupdate!</div>
        <?php elseif($_GET['pesan'] == 'hapus_sukses'): ?>
            <div class="alert alert-danger rounded-4 mb-4">🗑️ Data beasiswa berhasil dihapus!</div>
        <?php endif; ?>
      <?php endif; ?>

      <div class="content-card">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Poster</th> 
                <th>Nama Beasiswa & Penyelenggara</th>
                <th>Jenjang / Tingkat</th>
                <th>Deadline</th>
                <th>Pendanaan</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php while($b = mysqli_fetch_assoc($list_beasiswa)): 
                $file_poster = (!empty($b['poster']) && file_exists("uploads/".$b['poster'])) ? $b['poster'] : 'default.jpg';
              ?>
              <tr>
                <td><img src="uploads/<?= htmlspecialchars($file_poster) ?>" alt="Poster" class="table-poster"></td>
                <td>
                  <div class="fw-semibold text-dark"><?= htmlspecialchars($b['nama_beasiswa']) ?></div>
                  <div class="text-muted small"><?= htmlspecialchars($b['penyelenggara'] ?? '-') ?></div>
                </td>
                <td>
                  <span class="badge bg-success"><?= htmlspecialchars($b['jenjang'] ?? '-') ?></span>
                  <div class="mt-1 small text-muted"><?= htmlspecialchars($b['tingkat_beasiswa'] ?? '-') ?></div>
                </td>
                <td class="fw-semibold text-danger">
                  <?= htmlspecialchars($b['deadline'] ?? '-') ?>
                  <?php if($b['deadline'] && strtotime($b['deadline']) < strtotime(date('Y-m-d'))): ?>
                    <div class="mt-1"><span class="badge bg-secondary" style="font-size: 10px;">Berakhir</span></div>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="fw-semibold text-primary"><?= htmlspecialchars($b['tipe_pendanaan'] ?? '-') ?></div>
                </td>
                <td class="text-center" style="white-space: nowrap;">
                  <button class="action-btn edit-btn" onclick="openEditModal(
                    '<?= $b['id_beasiswa'] ?>', 
                    '<?= addslashes($b['nama_beasiswa']) ?>',
                    '<?= addslashes($b['penyelenggara'] ?? '') ?>',
                    '<?= addslashes($b['jenjang'] ?? '') ?>',
                    '<?= addslashes($b['tingkat_beasiswa'] ?? '') ?>',
                    '<?= addslashes($b['deadline'] ?? '') ?>',
                    '<?= addslashes($b['tipe_pendanaan'] ?? '') ?>',
                    '<?= addslashes(str_replace(["\r", "\n"], ' ', $b['deskripsi'] ?? '')) ?>',
                    '<?= htmlspecialchars($file_poster) ?>'
                  )">✏️ Edit</button>
                  <a href="?hapus_id=<?= $b['id_beasiswa'] ?>" class="action-btn delete-btn" onclick="return confirm('Hapus program beasiswa ini?')">❌ Hapus</a>
                </td>
              </tr>
              <?php endwhile; if(mysqli_num_rows($list_beasiswa) == 0) echo "<tr><td colspan='6' class='text-center py-4'>Belum ada data beasiswa.</td></tr>"; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <div class="modal-overlay" id="editModal">
    <div class="modal-box">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Update Data Beasiswa & Poster</h5>
        <button type="button" class="btn-close" onclick="closeModal()"></button>
      </div>

      <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_beasiswa" value="1">
        <input type="hidden" name="id_beasiswa" id="modalId">
        
        <div class="row mb-3 align-items-center">
            <div class="col-md-3 text-center">
                <img src="" id="modalPreviewPoster" alt="Poster Beasiswa" class="img-thumbnail" style="height: 100px; width: 100px; object-fit: cover;">
            </div>
            <div class="col-md-9">
                <label>Update Poster Beasiswa <span class="text-muted">(Kosongkan jika tidak ingin mengubah)</span></label>
                <input type="file" name="poster" id="modalInputPoster" class="form-control" accept="image/jpeg,image/png">
                <small class="text-muted mt-1 d-block" style="font-size: 11px;">Hanya menerima format JPG, JPEG, PNG.</small>
            </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3"><label>Nama Beasiswa</label><input type="text" name="nama_beasiswa" id="modalNama" class="form-control" required></div>
          <div class="col-md-6 mb-3"><label>Penyelenggara</label><input type="text" name="penyelenggara" id="modalPenyelenggara" class="form-control" required></div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label>Jenjang Pendidikan</label>
            <select name="jenjang" id="modalJenjang" class="form-select" required>
              <option value="SMA/SMK">SMA/SMK</option>
              <option value="D3/D4/S1">D3/D4/S1</option>
              <option value="S2">Pascasarjana (S2)</option>
              <option value="Umum">Umum</option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label>Tingkat Wilayah</label>
            <select name="tingkat_beasiswa" id="modalTingkat" class="form-select" required>
              <option value="Instansi">Instansi / Internal</option>
              <option value="Nasional">Nasional</option>
              <option value="Internasional">Internasional</option>
            </select>
          </div>
          <div class="col-md-4 mb-3"><label>Deadline</label><input type="date" name="deadline" id="modalDeadline" class="form-control" required></div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-3">
            <label>Tipe Pendanaan</label>
            <select name="tipe_pendanaan" id="modalTipePendanaan" class="form-select" required>
                <option value="Fully Funded">Fully Funded (Penuh)</option>
                <option value="Partial Funded">Partial Funded (Sebagian)</option>
            </select>
          </div>
        </div>

        <div class="mb-3"><label>Deskripsi Beasiswa</label><textarea name="deskripsi" id="modalDeskripsi" class="form-control" rows="4" required></textarea></div>
        
        <button type="submit" class="btn-gradient">Simpan Semua Perubahan Data</button>
      </form>
    </div>
  </div>

  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 10000;">
    <div id="notificationToast" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          🔔 <strong>Pemberitahuan Baru!</strong> Ada iklan beasiswa baru yang menunggu verifikasi kamu.
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-footer bg-light p-2 text-end rounded-bottom">
          <a href="halamanVerifikasiIklan.php" class="btn btn-sm btn-outline-primary" style="font-size: 11px; font-weight: bold;">Cek Sekarang</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function openEditModal(id, nama, penyelenggara, jenjang, tingkat, deadline, tipe_pendanaan, deskripsi, poster_path) {
      document.getElementById('modalId').value = id;
      document.getElementById('modalNama').value = nama;
      document.getElementById('modalPenyelenggara').value = penyelenggara;
      document.getElementById('modalJenjang').value = jenjang;
      document.getElementById('modalTingkat').value = tingkat;
      document.getElementById('modalDeadline').value = deadline;
      document.getElementById('modalTipePendanaan').value = tipe_pendanaan;
      document.getElementById('modalDeskripsi').value = deskripsi;
      
      document.getElementById('modalPreviewPoster').src = 'uploads/' + poster_path;
      document.getElementById('modalInputPoster').value = ''; 

      document.getElementById('editModal').style.display = 'flex';
    }

    function closeModal() { document.getElementById('editModal').style.display = 'none'; }

    // REAL-TIME NOTIFICATION SYSTEM (CEK KONDISI ANTREAN IKLAN BEASISWA)
    let lastPendingCount = <?= $total_menunggu ?>; 

    function periksaBeasiswaBaru() {
        fetch('penghubung.php?aksi=cek_notif_beasiswa') // Ganti endpoint atau parameter sesuai router sistemmu jika digabung
            .then(response => response.json())
            .then(data => {
                let currentPending = data.total_pending;
                let badge = document.getElementById('badge-notif');

                // A. Update Badge Angka Notifikasi di Sidebar
                if (currentPending > 0) {
                    badge.textContent = currentPending;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }

                // B. Munculkan Toast Pop-up jika ada pengajuan verifikasi baru masuk
                if (currentPending > lastPendingCount) {
                    let toastEl = document.getElementById('notificationToast');
                    let toast = new bootstrap.Toast(toastEl);
                    toast.show();
                }

                lastPendingCount = currentPending;
            })
            .catch(error => console.error('Gagal mengambil data notifikasi:', error));
    }

    // Interval hit realtime tiap 5 detik sekali
    periksaBeasiswaBaru();          
    setInterval(periksaBeasiswaBaru, 5000); 
  </script>
</body>
</html>
<?php
// 1. KONEKSI DATABASE & PROSES AKSI ADMIN
$conn = mysqli_connect("localhost", "root", "", "19juta_pendidikan"); 

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$pesan = "";

// Proses jika tombol Setujui atau Tolak diklik
if (isset($_POST['aksi_verifikasi'])) {
    $id_iklan = mysqli_real_escape_string($conn, $_POST['id_iklan']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status']);
    $tipe_iklan = mysqli_real_escape_string($conn, $_POST['tipe_iklan']); // Ambil info tipe (lomba/beasiswa)
    
    // Tentukan tabel target berdasarkan tipe iklan
    $tabel_target = ($tipe_iklan === 'beasiswa') ? 'iklan_beasiswa' : 'iklan_lomba';
    
    $query_update = "UPDATE $tabel_target SET status_verifikasi = '$status_baru' WHERE id_iklan = '$id_iklan'";
    if (mysqli_query($conn, $query_update)) {
        $pesan = "<div class='alert alert-success'>Iklan $tipe_iklan berhasil di-$status_baru!</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal memperbarui status: " . mysqli_error($conn) . "</div>";
    }
}

// 2. AMBIL DAFTAR IKLAN YANG MENUNGGU VERIFIKASI (GABUNGAN LOMBA & BEASISWA)
$query_tunggu = "
    SELECT id_iklan, judul_iklan, paket_langganan, 'lomba' AS tipe_iklan FROM iklan_lomba WHERE status_verifikasi = 'menunggu'
    UNION ALL
    SELECT id_iklan, judul_iklan, paket_langganan, 'beasiswa' AS tipe_iklan FROM iklan_beasiswa WHERE status_verifikasi = 'menunggu'
    ORDER BY id_iklan DESC";
    
$list_tunggu = mysqli_query($conn, $query_tunggu);

// 3. AMBIL DETAIL IKLAN BERDASARKAN TIPE YANG DIPILIH
$detail = null;
if (isset($_GET['periksa_id']) && isset($_GET['tipe'])) {
    $id_periksa = mysqli_real_escape_string($conn, $_GET['periksa_id']);
    $tipe_periksa = mysqli_real_escape_string($conn, $_GET['tipe']);
    
    if ($tipe_periksa === 'beasiswa') {
        // Query Join untuk Beasiswa (Silakan sesuaikan nama kolom tabel beasiswa jika berbeda)
        $query_detail = "SELECT ib.*, b.*, p.*, 'beasiswa' AS tipe_iklan FROM iklan_beasiswa ib
                         INNER JOIN beasiswa b ON ib.id_beasiswa = b.id_beasiswa
                         INNER JOIN pembayaran p ON ib.id_pembayaran = p.id_pembayaran
                         WHERE ib.id_iklan = '$id_periksa'";
    } else {
        // Query Join asal untuk Lomba
        $query_detail = "SELECT il.*, l.*, p.*, 'lomba' AS tipe_iklan FROM iklan_lomba il
                         INNER JOIN lomba l ON il.id_lomba = l.id_lomba
                         INNER JOIN pembayaran p ON il.id_pembayaran = p.id_pembayaran
                         WHERE il.id_iklan = '$id_periksa'";
    }
                     
    $hasil_detail = mysqli_query($conn, $query_detail);
    if ($hasil_detail) {
        $detail = mysqli_fetch_assoc($hasil_detail);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifikasi Iklan - 19JutaPendidikan Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; color: #14213d; }
    .sidebar { width: 260px; height: 100vh; position: fixed; background: #fff; padding: 25px 20px; box-shadow: 2px 0 15px rgba(0,0,0,0.05); }
    .sidebar .nav-link { color: #6b7280; padding: 12px 15px; border-radius: 8px; display: block; text-decoration: none; margin-bottom: 8px; font-weight: 500; }
    .sidebar .nav-link.active { background: #2f6df6; color: white; }
    .main-content { margin-left: 260px; padding: 40px; }
    .card-custom { background: #fff; border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 25px; }
    .bukti-img { max-width: 100%; max-height: 400px; object-fit: contain; border-radius: 10px; border: 2px dashed #cbd5e1; }
    .table-data td { padding: 8px 4px; font-size: 14px; }
    .table-data td.label-view { font-weight: 600; color: #6b7280; width: 35%; }
  </style>
</head>
<body>

  <aside class="sidebar">
    <h4 class="fw-bold text-primary mb-4">19JutaAdmin</h4>
    <a href="adminDashboard.php" class="nav-link">📊 Dashboard</a>
    <a href="halamanVerifikasi.php" class="nav-link active">✅ Verifikasi Iklan</a>
  </aside>

  <main class="main-content">
    <div class="container-fluid">
      <h3 class="fw-bold mb-4">📋 Sistem Verifikasi Iklan Publikasi</h3>
      <?= $pesan ?>

      <div class="row g-4">
        
        <div class="col-lg-5">
          <div class="card-custom">
            <h5 class="fw-bold mb-3 text-warning">⏳ Antrean Menunggu Kelayakan</h5>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Judul Promosi</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (mysqli_num_rows($list_tunggu) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($list_tunggu)): ?>
                      <tr class="<?= (isset($id_periksa) && $id_periksa == $row['id_iklan'] && $_GET['tipe'] == $row['tipe_iklan']) ? 'table-primary' : '' ?>">
                        <td>
                          <span class="fw-bold d-block" style="font-size: 14px;"><?= htmlspecialchars($row['judul_iklan']) ?></span>
                          <div class="d-flex gap-1 align-items-center mt-1">
                            <small class="text-muted"><?= htmlspecialchars($row['paket_langganan']) ?></small>
                            <?php if($row['tipe_iklan'] === 'lomba'): ?>
                              <span class="badge bg-primary" style="font-size: 10px;">Lomba</span>
                            <?php else: ?>
                              <span class="badge bg-success" style="font-size: 10px;">Beasiswa</span>
                            <?php endif; ?>
                          </div>
                        </td>
                        <td>
                          <a href="halamanVerifikasi.php?periksa_id=<?= $row['id_iklan'] ?>&tipe=<?= $row['tipe_iklan'] ?>" class="btn btn-primary btn-sm px-3 rounded-pill">Periksa</a>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="2" class="text-center text-muted py-4">Tidak ada antrean iklan baru 🎉</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card-custom">
            <h5 class="fw-bold mb-4 text-primary">🔍 Lembar Detail Isian Transaksi</h5>

            <?php if ($detail): ?>
              <div class="row">
                <div class="col-md-12">
                  
                  <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">📦 Ringkasan Paket & Iklan</h6>
                  <table class="table table-borderless table-data mb-4">
                    <tr><td class="label-view">Kategori Data</td><td>: <span class="badge bg-dark text-white text-uppercase"><?= $detail['tipe_iklan'] ?></span></td></tr>
                    <tr><td class="label-view">Jenis Paket</td><td>: <span class="badge bg-info text-dark"><?= htmlspecialchars($detail['paket_langganan']) ?></span></td></tr>
                    <tr><td class="label-view">Judul Promosi</td><td>: <strong><?= htmlspecialchars($detail['judul_iklan']) ?></strong></td></tr>
                    <tr><td class="label-view">Metode Pembayaran</td><td>: <?= htmlspecialchars($detail['metode_pembayaran']) ?></td></tr>
                    <tr><td class="label-view">Total Dibayar</td><td>: <span class="text-primary fw-bold">Rp <?= number_format($detail['jumlah'], 0, ',', '.') ?></span></td></tr>
                  </table>

                  <?php if ($detail['tipe_iklan'] === 'lomba'): ?>
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">🏆 Atribut Informasi Lomba</h6>
                    <table class="table table-borderless table-data mb-4">
                      <tr><td class="label-view">Nama Lomba</td><td>: <?= htmlspecialchars($detail['judul_lomba']) ?></td></tr>
                      <tr><td class="label-view">Penyelenggara</td><td>: <?= htmlspecialchars($detail['penyelenggara']) ?></td></tr>
                      <tr><td class="label-view">Kategori & Tingkat</td><td>: <?= htmlspecialchars($detail['kategori']) ?> / <?= htmlspecialchars($detail['tingkat_lomba']) ?></td></tr>
                      <tr><td class="label-view">Deadline</td><td>: <span class="text-danger fw-bold"><?= date('d M Y', strtotime($detail['deadline'])) ?></span></td></tr>
                      <tr><td class="label-view">Biaya Pendaftaran</td><td>: <?= ($detail['tipe_biaya'] == 'Gratis') ? '<span class="badge bg-success">Gratis</span>' : 'Rp ' . number_format($detail['biaya'], 0, ',', '.') ?></td></tr>
                      <tr><td class="label-view">Deskripsi Mekanisme</td><td>: <p class="text-muted d-inline"><?= htmlspecialchars($detail['deskripsi']) ?></p></td></tr>
                    </table>

                  <?php else: ?>
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">🎓 Atribut Informasi Beasiswa</h6>
                    <table class="table table-borderless table-data mb-4">
                      <tr><td class="label-view">Nama Beasiswa</td><td>: <?= htmlspecialchars($detail['nama_beasiswa'] ?? $detail['judul_beasiswa'] ?? 'Tidak Terbaca') ?></td></tr>
                      <tr><td class="label-view">Penyedia / Instansi</td><td>: <?= htmlspecialchars($detail['penyedia'] ?? $detail['penyelenggara'] ?? '-') ?></td></tr>
                      <tr><td class="label-view">Tipe Cakupan</td><td>: <?= htmlspecialchars($detail['cakupan_biaya'] ?? $detail['kategori'] ?? '-') ?></td></tr>
                      <tr><td class="label-view">Deadline Pendaftaran</td><td>: <span class="text-danger fw-bold"><?= date('d M Y', strtotime($detail['deadline'])) ?></span></td></tr>
                      <tr><td class="label-view">Deskripsi Persyaratan</td><td>: <p class="text-muted d-inline"><?= htmlspecialchars($detail['deskripsi']) ?></p></td></tr>
                    </table>
                  <?php endif; ?>

                  <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">🖼️ Poster Resmi / Banner Informasi</h6>
                  <div class="text-center mb-4">
                    <?php if (!empty($detail['poster'])): ?>
                      <img src="uploads/<?= htmlspecialchars($detail['poster']) ?>" alt="Poster Promosi" class="bukti-img mb-2">
                    <?php else: ?>
                      <div class="p-4 bg-light rounded-3 text-muted small">User tidak mengunggah berkas poster.</div>
                    <?php endif; ?>
                  </div>

                  <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">💳 Dokumen Bukti Transfer</h6>
                  <div class="text-center mb-4">
                    <img src="uploads/<?= htmlspecialchars($detail['bukti_pembayaran']) ?>" alt="Bukti Pembayaran" class="bukti-img mb-2">
                  </div>

                  <div class="p-3 bg-light rounded-3 d-flex justify-content-end gap-2">
                    <form action="halamanVerifikasi.php" method="POST" class="d-inline">
                      <input type="hidden" name="id_iklan" value="<?= $detail['id_iklan'] ?>">
                      <input type="hidden" name="tipe_iklan" value="<?= $detail['tipe_iklan'] ?>">
                      <input type="hidden" name="status" value="ditolak">
                      <button type="submit" name="aksi_verifikasi" class="btn btn-danger px-4 rounded-3 fw-semibold">❌ Tolak Transaksi</button>
                    </form>
                    
                    <form action="halamanVerifikasi.php" method="POST" class="d-inline">
                      <input type="hidden" name="id_iklan" value="<?= $detail['id_iklan'] ?>">
                      <input type="hidden" name="tipe_iklan" value="<?= $detail['tipe_iklan'] ?>">
                      <input type="hidden" name="status" value="disetujui">
                      <button type="submit" name="aksi_verifikasi" class="btn btn-success px-4 rounded-3 fw-semibold">✅ Setujui & Publish</button>
                    </form>
                  </div>

                </div>
              </div>

            <?php else: ?>
              <div class="text-center py-5">
                <span style="font-size: 48px;">👈</span>
                <p class="text-muted mt-2">Silakan pilih salah satu antrean iklan di sebelah kiri untuk meninjau data inputan user secara lengkap.</p>
              </div>
            <?php endif; ?>

          </div>
        </div>

      </div>
    </div>
  </main>

</body>
</html>
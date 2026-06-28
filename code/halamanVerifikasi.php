<?php
// Set memory limit dan timeout
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 60);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'penghubung.php';

$pesan = "";

// Proses verifikasi
if (isset($_POST['aksi_verifikasi'])) {
    $id_iklan = (int)$_POST['id_iklan'];
    $status_baru = $_POST['status'];
    $tipe_iklan = $_POST['tipe_iklan'];
    
    $tabel = ($tipe_iklan === 'beasiswa') ? 'iklan_beasiswa' : 'iklan_lomba';
    
    $stmt = $koneksi->prepare("UPDATE $tabel SET status_verifikasi = ? WHERE id_iklan = ?");
    $stmt->bind_param("si", $status_baru, $id_iklan);
    
    if ($stmt->execute()) {
        $pesan = "<div class='alert alert-success'>Iklan $tipe_iklan berhasil di-$status_baru!</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Ambil antrean - SIMPLE QUERY
$query_tunggu = "
    (SELECT id_iklan, judul_iklan, paket_langganan, 'lomba' AS tipe_iklan 
     FROM iklan_lomba 
     WHERE status_verifikasi = 'menunggu' 
     ORDER BY id_iklan DESC 
     LIMIT 50)
    UNION ALL
    (SELECT id_iklan, judul_iklan, paket_langganan, 'beasiswa' AS tipe_iklan 
     FROM iklan_beasiswa 
     WHERE status_verifikasi = 'menunggu' 
     ORDER BY id_iklan DESC 
     LIMIT 50)
    ORDER BY id_iklan DESC
    LIMIT 100";

$list_tunggu = $koneksi->query($query_tunggu);

// Ambil detail
$detail = null;
$id_periksa = null;
$tipe_periksa = null;

if (isset($_GET['periksa_id']) && isset($_GET['tipe'])) {
    $id_periksa = (int)$_GET['periksa_id'];
    $tipe_periksa = $_GET['tipe'];
    
    if ($tipe_periksa === 'beasiswa') {
        // Gunakan LEFT JOIN karena pembayaran bisa NULL (beasiswa gratis)
        $query_detail = "
            SELECT 
                ib.id_iklan, ib.id_beasiswa, ib.id_pembayaran, ib.id_user, 
                ib.judul_iklan, ib.status_verifikasi, ib.paket_langganan,
                b.nama_beasiswa, b.penyelenggara, b.jenjang, b.tingkat_beasiswa, 
                b.deskripsi, b.poster, b.deadline, b.tipe_pendanaan, b.kontak_pengaju,
                p.jumlah, p.metode_pembayaran, p.bukti_pembayaran,
                'beasiswa' AS tipe_iklan
            FROM iklan_beasiswa ib
            LEFT JOIN beasiswa b ON ib.id_beasiswa = b.id_beasiswa
            LEFT JOIN pembayaran p ON ib.id_pembayaran = p.id_pembayaran
            WHERE ib.id_iklan = ?
            LIMIT 1
        ";
        
        $stmt = $koneksi->prepare($query_detail);
        $stmt->bind_param("i", $id_periksa);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $detail = $result->fetch_assoc();
        }
        $stmt->close();
        
    } else {
        // Untuk lomba, pembayaran WAJIB ada
        $query_detail = "
            SELECT 
                il.id_iklan, il.id_lomba, il.id_pembayaran, il.id_user,
                il.judul_iklan, il.status_verifikasi, il.paket_langganan,
                l.judul_lomba, l.penyelenggara, l.kategori, l.tingkat_lomba,
                l.deskripsi, l.poster, l.deadline, l.tipe_biaya, l.biaya, l.kontak_pengaju,
                p.jumlah, p.metode_pembayaran, p.bukti_pembayaran,
                'lomba' AS tipe_iklan
            FROM iklan_lomba il
            INNER JOIN lomba l ON il.id_lomba = l.id_lomba
            INNER JOIN pembayaran p ON il.id_pembayaran = p.id_pembayaran
            WHERE il.id_iklan = ?
            LIMIT 1
        ";
        
        $stmt = $koneksi->prepare($query_detail);
        $stmt->bind_param("i", $id_periksa);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $detail = $result->fetch_assoc();
        }
        $stmt->close();
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
    .sidebar { width: 260px; height: 100vh; position: fixed; background: #fff; padding: 25px 20px; box-shadow: 2px 0 15px rgba(0,0,0,0.05); z-index: 100; }
    .sidebar .nav-link { color: #6b7280; padding: 12px 15px; border-radius: 8px; display: block; text-decoration: none; margin-bottom: 8px; font-weight: 500; }
    .sidebar .nav-link.active { background: #2f6df6; color: white; }
    .main-content { margin-left: 280px; padding: 40px; position: relative; z-index: 1; }
    .card-custom { background: #fff; border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 25px; }
    .bukti-img { max-width: 100%; max-height: 400px; object-fit: contain; border-radius: 10px; border: 2px dashed #cbd5e1; }
    .table-data td { padding: 8px 4px; font-size: 14px; vertical-align: top; }
    .table-data td.label-view { font-weight: 600; color: #6b7280; width: 35%; }
    .btn-periksa { 
      pointer-events: auto !important; 
      cursor: pointer !important; 
      z-index: 10 !important;
      position: relative !important;
    }
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
        
        <!-- KOLOM KIRI: ANTREAN -->
        <div class="col-lg-5">
          <div class="card-custom">
            <h5 class="fw-bold mb-3 text-warning">⏳ Antrean Menunggu Kelayakan</h5>
            
            <?php if ($list_tunggu && mysqli_num_rows($list_tunggu) > 0): ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Judul Promosi</th>
                      <th style="width: 120px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while($row = mysqli_fetch_assoc($list_tunggu)): ?>
                      <?php 
                        $isActive = ($id_periksa == $row['id_iklan'] && $tipe_periksa == $row['tipe_iklan']);
                        $url_periksa = "halamanVerifikasi.php?periksa_id=" . $row['id_iklan'] . "&tipe=" . $row['tipe_iklan'];
                      ?>
                      <tr class="<?= $isActive ? 'table-primary' : '' ?>">
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
                          <a href="<?= $url_periksa ?>" class="btn btn-primary btn-sm px-3 rounded-pill btn-periksa">
                            Periksa
                          </a>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="text-center py-4">
                <p class="text-muted mb-0">Tidak ada antrean iklan baru 🎉</p>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- KOLOM KANAN: DETAIL -->
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
                    <tr><td class="label-view">Metode Pembayaran</td><td>: <?= htmlspecialchars($detail['metode_pembayaran'] ?? 'Gratis (Tanpa Pembayaran)') ?></td></tr>
                    <tr><td class="label-view">Total Dibayar</td><td>: <span class="text-primary fw-bold">Rp <?= number_format($detail['jumlah'] ?? 0, 0, ',', '.') ?></span></td></tr>
                  </table>

                  <?php if ($detail['tipe_iklan'] === 'lomba'): ?>
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">🏆 Atribut Informasi Lomba</h6>
                    <table class="table table-borderless table-data mb-4">
                      <tr><td class="label-view">Nama Lomba</td><td>: <?= htmlspecialchars($detail['judul_lomba']) ?></td></tr>
                      <tr><td class="label-view">Penyelenggara</td><td>: <?= htmlspecialchars($detail['penyelenggara']) ?></td></tr>
                      <tr><td class="label-view">Kategori & Tingkat</td><td>: <?= htmlspecialchars($detail['kategori']) ?> / <?= htmlspecialchars($detail['tingkat_lomba']) ?></td></tr>
                      <tr><td class="label-view">Deadline</td><td>: <span class="text-danger fw-bold"><?= ($detail['deadline'] && $detail['deadline'] != '0000-00-00') ? date('d M Y', strtotime($detail['deadline'])) : 'Tidak ada deadline' ?></span></td></tr>
                      <tr><td class="label-view">Biaya Pendaftaran</td><td>: <?= ($detail['tipe_biaya'] == 'Gratis') ? '<span class="badge bg-success">Gratis</span>' : 'Rp ' . number_format($detail['biaya'], 0, ',', '.') ?></td></tr>
                      <tr><td class="label-view">Deskripsi</td><td>: <p class="text-muted d-inline"><?= nl2br(htmlspecialchars($detail['deskripsi'])) ?></p></td></tr>
                      <tr><td class="label-view">Kontak Pengaju</td><td>: <a href="<?= htmlspecialchars($detail['kontak_pengaju'] ?? '#') ?>" target="_blank" class="text-primary fw-semibold"><?= htmlspecialchars($detail['kontak_pengaju'] ?? 'Tidak tersedia') ?></a></td></tr>
                    </table>

                  <?php else: ?>
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">🎓 Atribut Informasi Beasiswa</h6>
                    <table class="table table-borderless table-data mb-4">
                      <tr><td class="label-view">Nama Beasiswa</td><td>: <?= htmlspecialchars($detail['nama_beasiswa'] ?? 'Tidak Terbaca') ?></td></tr>
                      <tr><td class="label-view">Penyelenggara</td><td>: <?= htmlspecialchars($detail['penyelenggara'] ?? '-') ?></td></tr>
                      <tr><td class="label-view">Jenjang</td><td>: <?= htmlspecialchars($detail['jenjang'] ?? '-') ?></td></tr>
                      <tr><td class="label-view">Tingkat</td><td>: <?= htmlspecialchars($detail['tingkat_beasiswa'] ?? '-') ?></td></tr>
                      <tr><td class="label-view">Tipe Pendanaan</td><td>: <?= htmlspecialchars($detail['tipe_pendanaan'] ?? '-') ?></td></tr>
                      <tr><td class="label-view">Deadline</td><td>: <span class="text-danger fw-bold"><?= ($detail['deadline'] && $detail['deadline'] != '0000-00-00') ? date('d M Y', strtotime($detail['deadline'])) : 'Tidak ada deadline' ?></span></td></tr>
                      <tr><td class="label-view">Deskripsi</td><td>: <p class="text-muted d-inline"><?= nl2br(htmlspecialchars($detail['deskripsi'] ?? '-')) ?></p></td></tr>
                      <tr><td class="label-view">Kontak Pengaju</td><td>: <a href="<?= htmlspecialchars($detail['kontak_pengaju'] ?? '#') ?>" target="_blank" class="text-primary fw-semibold"><?= htmlspecialchars($detail['kontak_pengaju'] ?? 'Tidak tersedia') ?></a></td></tr>
                    </table>
                  <?php endif; ?>

                  <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">🖼️ Poster</h6>
                  <div class="text-center mb-4">
                    <?php if (!empty($detail['poster'])): ?>
                      <img src="uploads/<?= htmlspecialchars($detail['poster']) ?>" alt="Poster" class="bukti-img mb-2">
                    <?php else: ?>
                      <div class="p-4 bg-light rounded-3 text-muted small">Tidak ada poster.</div>
                    <?php endif; ?>
                  </div>

                  <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">💳 Bukti Transfer</h6>
                  <div class="text-center mb-4">
                    <?php if (!empty($detail['bukti_pembayaran'])): ?>
                      <img src="uploads/<?= htmlspecialchars($detail['bukti_pembayaran']) ?>" alt="Bukti" class="bukti-img mb-2">
                    <?php else: ?>
                      <div class="p-4 bg-light rounded-3 text-muted small">Tidak ada bukti pembayaran (pengajuan gratis).</div>
                    <?php endif; ?>
                  </div>

                  <div class="p-3 bg-light rounded-3 d-flex justify-content-end gap-2">
                    <form action="halamanVerifikasi.php" method="POST" class="d-inline">
                      <input type="hidden" name="id_iklan" value="<?= $detail['id_iklan'] ?>">
                      <input type="hidden" name="tipe_iklan" value="<?= $detail['tipe_iklan'] ?>">
                      <input type="hidden" name="status" value="ditolak">
                      <button type="submit" name="aksi_verifikasi" class="btn btn-danger px-4 rounded-3 fw-semibold" onclick="return confirm('Yakin tolak?')">❌ Tolak</button>
                    </form>
                    
                    <form action="halamanVerifikasi.php" method="POST" class="d-inline">
                      <input type="hidden" name="id_iklan" value="<?= $detail['id_iklan'] ?>">
                      <input type="hidden" name="tipe_iklan" value="<?= $detail['tipe_iklan'] ?>">
                      <input type="hidden" name="status" value="disetujui">
                      <button type="submit" name="aksi_verifikasi" class="btn btn-success px-4 rounded-3 fw-semibold" onclick="return confirm('Yakin setujui?')">✅ Setujui</button>
                    </form>
                  </div>

                </div>
              </div>

            <?php else: ?>
              <div class="text-center py-5">
                <span style="font-size: 48px;">👈</span>
                <p class="text-muted mt-2">Silakan pilih antrean di sebelah kiri untuk melihat detail.</p>
              </div>
            <?php endif; ?>

          </div>
        </div>

      </div>
    </div>
  </main>

</body>
</html>
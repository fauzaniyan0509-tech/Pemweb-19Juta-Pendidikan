<?php
require_once 'koneksi.php'; // Pastikan file koneksi ada
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Lomba - 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="lomba.css">
</head>
<body>

  <nav class="navbar navbar-expand-lg py-3">
    <div class="container">
      <a class="logo-text" href="#">19JutaPendidikan</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center gap-lg-4">
          <li class="nav-item"><a class="nav-link" href="#">Beranda</a></li>
          <li class="nav-item"><a class="nav-link active" href="#">Lomba</a></li>
          <li class="nav-item"><a class="btn-gradient" href="halamanTransaksi.php">Publikasi Lomba</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="py-5 text-center">
    <div class="container">
      <h1 class="hero-title">Temukan Lomba Sesuai Bakatmu</h1>
      <div class="search-hero">
        <input type="text" id="heroSearch" placeholder="Cari nama lomba, penyelenggara, atau kategori...">
        <button onclick="filterLomba()">Cari Lomba</button>
      </div>
    </div>
  </section>

  <main class="container pb-5">
    <div class="row g-4">

      <div class="col-lg-3">
        <div class="card-custom filter-card">
          <h3 class="filter-title">Filter Lomba</h3>
          <div class="mb-4">
            <label>Cari Nama Lomba</label>
            <input type="text" class="form-control" id="sideSearch" placeholder="Contoh: Olimpiade">
          </div>
          <button class="filter-btn" onclick="filterLomba()">Terapkan Filter</button>
          <button class="reset-btn" onclick="resetFilter()">Reset Filter</button>
        </div>
      </div>

      <div class="col-lg-9">
        <div class="section-heading">
          <h2>Daftar Lomba</h2>
          <p class="result-count mb-0" id="resultText">Memuat data...</p>
        </div>

        <div class="row g-4" id="lombaGrid">
          <?php
          // Query mengambil data lomba yang sudah disetujui admin
          $query = "SELECT l.* FROM lomba l 
                    JOIN iklan_lomba i ON l.id_lomba = i.id_lomba 
                    WHERE i.status_verifikasi = 'disetujui'";
          $result = mysqli_query($conn, $query);

          if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                  // PRO MODE: Cek apakah data poster ada di database. 
                  // Jika ada dan filenya fisik tersedia, pakai itu. Jika tidak, pakai default.jpg
                  $file_poster = !empty($row['poster']) ? $row['poster'] : 'default.jpg';
          ?>
              <div class="col-md-6 lomba-item"
                data-title="<?= htmlspecialchars(strtolower($row['judul_lomba'])) ?>"
                data-kategori="<?= htmlspecialchars($row['kategori']) ?>"
                data-tingkat="<?= htmlspecialchars($row['tingkat_lomba']) ?>"
                data-biaya="<?= htmlspecialchars(strtolower($row['tipe_biaya'])) ?>">
                
                <div class="card-custom lomba-card">
                  <div class="lomba-img <?= htmlspecialchars($row['kategori']) ?>">
                    <img src="uploads/<?= htmlspecialchars($file_poster) ?>" 
                        alt="Poster <?= htmlspecialchars($row['judul_lomba']) ?>" 
                        class="poster-lomba"
                        style="width: 100%; aspect-ratio: 16/9; object-fit: contain; background-color: #f4f7f6; border-radius: 12px 12px 0 0;">
                         
                    <span class="badge-kategori"><?= htmlspecialchars($row['kategori']) ?></span>
                    <span class="badge-tingkat <?= htmlspecialchars($row['tingkat_lomba']) ?>"><?= htmlspecialchars($row['tingkat_lomba']) ?></span>
                  </div>
                  <div class="lomba-body">
                    <h4 class="lomba-title"><?= htmlspecialchars($row['judul_lomba']) ?></h4>
                    <p class="organizer"><?= htmlspecialchars($row['penyelenggara']) ?></p>
                    <div class="meta-row">
                      <span class="meta-pill"><?= htmlspecialchars($row['tipe_biaya']) ?></span>
                    </div>
                    <div class="deadline-box">⏰ Deadline: <?= date('d F Y', strtotime($row['deadline'])) ?></div>
                    <div class="hadiah-box">🏆 <?= htmlspecialchars($row['deskripsi']) ?></div>
                    <a href="#" class="detail-btn">Lihat Detail & Daftar</a>
                  </div>
                </div>
              </div>
          <?php
              }
          } else {
              echo "<div class='col-12 text-center py-5'><h5 class='text-muted'>Belum ada lomba yang dipublikasikan.</h5></div>";
          }
          ?>
        </div>
      </div>
    </div>
  </main>

  <script>
    // FUNGSI FILTER 
    function filterLomba() {
      const heroSearch = document.getElementById('heroSearch').value.toLowerCase();
      const sideSearch = document.getElementById('sideSearch').value.toLowerCase();
      
      // Mengatasi error jika elemen filter belum ada di HTML-mu
      const kategoriEl = document.getElementById('kategoriFilter');
      const tingkatEl  = document.querySelector('input[name="tingkat"]:checked');
      const biayaEl    = document.querySelector('input[name="biaya"]:checked');

      const kategori = kategoriEl ? kategoriEl.value : 'all';
      const tingkat  = tingkatEl ? tingkatEl.value : 'all';
      const biaya    = biayaEl ? biayaEl.value : 'all';

      const items = document.querySelectorAll('.lomba-item');
      const searchText = heroSearch || sideSearch;
      let visibleCount = 0;

      items.forEach(item => {
        const title       = item.dataset.title;
        const itemKat     = item.dataset.kategori;
        const itemTingkat = item.dataset.tingkat;
        const itemBiaya   = item.dataset.biaya;

        const matchSearch  = searchText === '' || title.includes(searchText);
        const matchKat     = kategori === 'all' || itemKat === kategori;
        const matchTingkat = tingkat === 'all'  || itemTingkat === tingkat;
        const matchBiaya   = biaya === 'all'    || itemBiaya === biaya;

        if (matchSearch && matchKat && matchTingkat && matchBiaya) {
          item.style.display = 'block';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      });
      document.getElementById('resultText').textContent = `Menampilkan ${visibleCount} lomba`;
    }

    // Fungsi tambahan agar tombol reset berjalan (karena di HTML kamu ada pemanggilannya)
    function resetFilter() {
      document.getElementById('heroSearch').value = '';
      document.getElementById('sideSearch').value = '';
      // Reset radio/select ke default jika ada
      filterLomba(); 
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
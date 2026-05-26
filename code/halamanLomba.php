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
          <li class="nav-item"><a class="nav-link" href="beranda.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link active" href="halamanLomba">Lomba</a></li>
          <li class="nav-item"><a class="btn-gradient" href="halamanTransaksiLomba.php">Publikasi Lomba</a></li>
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
        <div class="card-custom filter-card p-4 bg-white rounded-4 shadow-sm">
          <h3 class="filter-title mb-4" style="font-weight: 700; font-size: 18px;">Filter Lomba</h3>
          
          <div class="mb-4">
            <label class="form-label fw-semibold text-secondary small">CARI NAMA LOMBA</label>
            <input type="text" class="form-control" id="sideSearch" placeholder="Contoh: Olimpiade" onkeyup="filterLomba()">
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold text-secondary small">KATEGORI</label>
            <div class="form-check mb-1">
              <input class="form-check-input filter-kategori" type="checkbox" value="akademik" id="katAkademik" onchange="filterLomba()">
              <label class="form-check-label" for="katAkademik">Akademik</label>
            </div>
            <div class="form-check">
              <input class="form-check-input filter-kategori" type="checkbox" value="non akademik" id="katNonAkademik" onchange="filterLomba()">
              <label class="form-check-label" for="katNonAkademik">Non Akademik</label>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold text-secondary small">TINGKAT LOMBA</label>
            <div class="form-check mb-1">
              <input class="form-check-input filter-tingkat" type="checkbox" value="kabupaten / kota" id="tingkatKab" onchange="filterLomba()">
              <label class="form-check-label" for="tingkatKab">Kabupaten / Kota</label>
            </div>
            <div class="form-check mb-1">
              <input class="form-check-input filter-tingkat" type="checkbox" value="provinsi" id="tingkatProv" onchange="filterLomba()">
              <label class="form-check-label" for="tingkatProv">Provinsi</label>
            </div>
            <div class="form-check mb-1">
              <input class="form-check-input filter-tingkat" type="checkbox" value="nasional" id="tingkatNas" onchange="filterLomba()">
              <label class="form-check-label" for="tingkatNas">Nasional</label>
            </div>
            <div class="form-check">
              <input class="form-check-input filter-tingkat" type="checkbox" value="internasional" id="tingkatInter" onchange="filterLomba()">
              <label class="form-check-label" for="tingkatInter">Internasional</label>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold text-secondary small">BIAYA PENDAFTARAN</label>
            <div class="form-check mb-1">
              <input class="form-check-input filter-biaya" type="checkbox" value="gratis" id="biayaGratis" onchange="filterLomba()">
              <label class="form-check-label" for="biayaGratis">Gratis</label>
            </div>
            <div class="form-check">
              <input class="form-check-input filter-biaya" type="checkbox" value="berbayar" id="biayaBerbayar" onchange="filterLomba()">
              <label class="form-check-label" for="biayaBerbayar">Berbayar</label>
            </div>
          </div>

          <button class="filter-btn w-100 mb-2" onclick="filterLomba()">Terapkan Filter</button>
          <button class="reset-btn w-100 btn btn-light btn-sm text-secondary" onclick="resetFilter()">Reset Filter</button>
        </div>
      </div>

      <div class="col-lg-9">
        <div class="section-heading d-flex justify-content-between align-items-center mb-4">
          <h2 style="font-weight: 700;">Daftar Lomba</h2>
          <p class="result-count mb-0 text-muted" id="resultText">Memuat data...</p>
        </div>

        <div class="row g-4" id="lombaGrid">
          <?php
          // 1. String SQL Query mengambil data lomba yang disetujui & belum lewat deadline
          $query_string = "SELECT l.* FROM lomba l 
                           JOIN iklan_lomba i ON l.id_lomba = i.id_lomba 
                           WHERE i.status_verifikasi = 'disetujui' AND l.deadline >= CURDATE()";
          
          // 2. Eksekusi query ke database menggunakan $conn dari koneksi.php
          $list_lomba = mysqli_query($conn, $query_string);

          // 3. Cek apakah ada data lomba yang ditemukan
          if ($list_lomba && mysqli_num_rows($list_lomba) > 0) {
              
              // 4. Looping data secara sinkron menggunakan $list_lomba
              while ($row = mysqli_fetch_assoc($list_lomba)) {
                  
                  // Cek ketersediaan file poster, jika kosong gunakan default.jpg
                  $file_poster = !empty($row['poster']) ? $row['poster'] : 'default.jpg';
          ?>
              <div class="col-md-6 lomba-item"
                data-title="<?= htmlspecialchars(strtolower($row['judul_lomba'])) ?>"
                data-kategori="<?= htmlspecialchars($row['kategori'] ?? 'Umum') ?>"
                data-tingkat="<?= htmlspecialchars($row['tingkat_lomba'] ?? 'Nasional') ?>"
                data-biaya="<?= htmlspecialchars(strtolower($row['tipe_biaya'] ?? 'Gratis')) ?>">
                
                <div class="card-custom lomba-card">
                  <div class="lomba-img <?= htmlspecialchars($row['kategori'] ?? 'Umum') ?>">
                    <img src="uploads/<?= htmlspecialchars($file_poster) ?>" 
                        alt="Poster <?= htmlspecialchars($row['judul_lomba']) ?>" 
                        class="poster-lomba"
                        style="width: 100%; aspect-ratio: 16/9; object-fit: contain; background-color: #f4f7f6; border-radius: 12px 12px 0 0;">
                         
                    <span class="badge-kategori"><?= htmlspecialchars($row['kategori'] ?? 'Umum') ?></span>
                    <span class="badge-tingkat"><?= htmlspecialchars($row['tingkat_lomba'] ?? 'Nasional') ?></span>
                  </div>
                  <div class="lomba-body">
                    <h4 class="lomba-title"><?= htmlspecialchars($row['judul_lomba']) ?></h4>
                    <p class="organizer"><?= htmlspecialchars($row['penyelenggara'] ?? 'Penyelenggara') ?></p>
                    <div class="meta-row">
                      <span class="meta-pill"><?= htmlspecialchars($row['tipe_biaya'] ?? 'Gratis') ?></span>
                    </div>
                    <div class="deadline-box">⏰ Deadline: <?= date('d F Y', strtotime($row['deadline'])) ?></div>
                    <div class="hadiah-box">🏆 <?= htmlspecialchars($row['deskripsi']) ?></div>
                    <a href="#" class="detail-btn">Lihat Detail & Daftar</a>
                  </div>
                </div>
              </div>
          <?php
              } // Akhir perulangan while
          } else {
              echo "<div class='col-12 text-center py-5'><h5 class='text-muted'>Belum ada lomba yang dipublikasikan.</h5></div>";
          }
          ?>
        </div>
      </div>

    </div>
  </main>

  <script>
    function filterLomba() {
      // 1. Ambil kata kunci dari pencarian
      const heroSearch = document.getElementById('heroSearch').value.toLowerCase();
      const sideSearch = document.getElementById('sideSearch').value.toLowerCase();
      const searchText = heroSearch || sideSearch;

      // 2. Ambil nilai checkbox mana saja yang sedang dicentang (Ubah ke array lowercase)
      const checkedKategori = Array.from(document.querySelectorAll('.filter-kategori:checked')).map(cb => cb.value.toLowerCase());
      const checkedTingkat  = Array.from(document.querySelectorAll('.filter-tingkat:checked')).map(cb => cb.value.toLowerCase());
      const checkedBiaya    = Array.from(document.querySelectorAll('.filter-biaya:checked')).map(cb => cb.value.toLowerCase());

      const items = document.querySelectorAll('.lomba-item');
      let visibleCount = 0;

      items.forEach(item => {
        // Ambil data atribut HTML dari element PHP
        const title       = item.dataset.title.toLowerCase();
        const itemKat     = item.dataset.kategori.toLowerCase();
        const itemTingkat = item.dataset.tingkat.toLowerCase();
        const itemBiaya   = item.dataset.biaya.toLowerCase();

        // 3. Evaluasi Kecocokan Kriteria
        const matchSearch  = searchText === '' || title.includes(searchText);
        
        // Jika tidak ada checkbox dicentang, otomatis dianggap true (Lolos filter)
        const matchKat     = checkedKategori.length === 0 || checkedKategori.includes(itemKat);
        const matchTingkat = checkedTingkat.length === 0  || checkedTingkat.includes(itemTingkat);
        const matchBiaya   = checkedBiaya.length === 0    || checkedBiaya.includes(itemBiaya);

        // 4. Aksi Tampilan Elemen Grid
        if (matchSearch && matchKat && matchTingkat && matchBiaya) {
          item.style.display = 'block';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      });
      
      // Tampilkan jumlah hasil aktual di UI
      document.getElementById('resultText').textContent = `Menampilkan ${visibleCount} lomba`;
    }

    function resetFilter() {
      // Kosongkan kolom pencarian
      document.getElementById('heroSearch').value = '';
      document.getElementById('sideSearch').value = '';
      
      // Uncheck semua checkbox filter di sidebar
      const semuaCheckbox = document.querySelectorAll('.filter-card input[type="checkbox"]');
      semuaCheckbox.forEach(cb => cb.checked = false);
      
      // Jalankan ulang fungsi filter agar menampilkan semua data kembali
      filterLomba(); 
    }

    // Jalankan kalkulasi jumlah lomba pertama kali saat web selesai dimuat
    window.addEventListener('DOMContentLoaded', () => {
      const totalItems = document.querySelectorAll('.lomba-item').length;
      document.getElementById('resultText').textContent = `Menampilkan ${totalItems} lomba`;
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
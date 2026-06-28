<?php
include 'penghubung.php';
// Memastikan file koneksi database terhubung 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Beasiswa - 19JutaPendidikan</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

    :root {
      --blue: #2f6df6;
      --teal: #35c7b6;
      --dark: #14213d;
      --muted: #6b7280;
      --soft-bg: #eef8fb;
      --card: #ffffff;
      --border: #dbe5ea;
      --shadow: 0 18px 45px rgba(20, 33, 61, 0.10);
    }

    * {
      font-family: 'Poppins', sans-serif;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(180deg, #eef8fb 0%, #f7fcfb 100%);
      color: var(--dark);
      min-height: 100vh;
    }

    .navbar {
      background: #fff;
      box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }

    .logo-text {
      font-weight: 800;
      color: var(--blue);
      text-decoration: none;
      font-size: 22px;
    }

    .nav-link {
      font-size: 14px;
      color: #1f2937;
      font-weight: 500;
    }

    .nav-link.active {
      color: var(--blue);
      font-weight: 700;
    }

    .btn-gradient {
      background: linear-gradient(90deg, var(--blue), var(--teal));
      color: white;
      border: none;
      border-radius: 999px;
      font-weight: 600;
      padding: 9px 20px;
      text-decoration: none;
    }

    .hero-title {
      font-weight: 800;
      font-size: 42px;
      background: linear-gradient(90deg, var(--blue), var(--teal));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .hero-subtitle {
      color: var(--muted);
      max-width: 650px;
      margin: auto;
      font-size: 15px;
    }

    .search-hero {
      max-width: 720px;
      margin: 28px auto 0;
      background: white;
      padding: 10px;
      border-radius: 18px;
      box-shadow: var(--shadow);
      display: flex;
      gap: 10px;
    }

    .search-hero input {
      border: none;
      flex: 1;
      padding: 12px 16px;
      outline: none;
      font-size: 14px;
    }

    .search-hero button {
      border: none;
      border-radius: 12px;
      background: linear-gradient(90deg, var(--blue), var(--teal));
      color: white;
      font-weight: 700;
      padding: 0 22px;
    }

    .card-custom {
      background: var(--card);
      border-radius: 20px;
      box-shadow: var(--shadow);
      border: 1px solid #eef2f7;
    }

    .filter-card {
      padding: 24px;
      position: sticky;
      top: 95px;
    }

    .filter-title {
      font-weight: 800;
      font-size: 20px;
      margin-bottom: 22px;
    }

    label {
      font-size: 13px;
      font-weight: 700;
      color: #334155;
      margin-bottom: 8px;
    }

    .form-control,
    .form-select {
      border-radius: 12px;
      padding: 11px 13px;
      font-size: 14px;
      border-color: #cbd5e1;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--teal);
      box-shadow: 0 0 0 4px rgba(53, 199, 182, 0.15);
    }

    .radio-item {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 12px;
      font-size: 14px;
      color: #334155;
    }

    .filter-btn {
      width: 100%;
      border: none;
      border-radius: 12px;
      padding: 12px;
      background: linear-gradient(90deg, var(--blue), var(--teal));
      color: white;
      font-weight: 700;
      margin-top: 14px;
    }

    .reset-btn {
      border: none;
      background: transparent;
      color: var(--muted);
      font-weight: 600;
      margin-top: 14px;
      width: 100%;
    }

    .section-heading {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 22px;
      gap: 16px;
      flex-wrap: wrap;
    }

    .section-heading h2 {
      font-size: 28px;
      font-weight: 800;
      margin: 0;
    }

    .result-count {
      color: var(--muted);
      font-size: 14px;
    }

    .scholarship-card {
      overflow: hidden;
      transition: 0.25s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .scholarship-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 22px 48px rgba(20, 33, 61, 0.15);
    }

/* Ganti seluruh blok .scholarship-img dan .poster-beasiswa dengan ini */
.scholarship-img {
  height: 340px; /* Tinggi diperbesar untuk format portrait */
  background: #f1f5f9; /* Warna background abu-abu muda seperti di gambar */
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  border-radius: 20px 20px 0 0;
}

.poster-beasiswa {
  width: 85%; /* Lebar tidak full agar ada jarak di kiri-kanan */
  height: 90%; /* Tinggi tidak full agar ada jarak di atas-bawah */
  object-fit: contain; /* KUNCI: Agar seluruh poster terlihat utuh tanpa terpotong */
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08); /* Efek bayangan halus agar elegan */
  position: relative; /* Diubah dari absolute agar bisa di-tengah-kan dengan flexbox */
}

/* Responsive untuk tablet & mobile */
@media (max-width: 991px) {
  .scholarship-img {
    height: 300px;
  }
}

@media (max-width: 576px) {
  .scholarship-img {
    height: 260px;
  }
}

    .badge-level {
      position: absolute;
      top: 14px;
      left: 14px;
      background: white;
      color: var(--blue);
      font-weight: 800;
      font-size: 11px;
      padding: 7px 11px;
      border-radius: 999px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
      z-index: 2;
    }

    .badge-funding {
      position: absolute;
      top: 14px;
      right: 14px;
      background: #effffb;
      color: #087f6f;
      border: 1px solid var(--teal);
      font-weight: 800;
      font-size: 11px;
      padding: 7px 11px;
      border-radius: 999px;
      z-index: 2;
    }

    .scholarship-body {
      padding: 22px;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .scholarship-title {
      font-weight: 800;
      font-size: 18px;
      margin-bottom: 8px;
    }

    .organizer {
      color: var(--muted);
      font-size: 13px;
      margin-bottom: 14px;
    }

    /* Penyesuaian Deskripsi agar Rapi & Terpotong Otomatis jika terlalu panjang */
    .scholarship-desc {
      font-size: 13px;
      color: #4b5563;
      margin-bottom: 16px;
      display: -webkit-box;
      -webkit-line-clamp: 2; /* Batasi maksimal hanya keluar 2 baris teks */
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      line-height: 1.6;
    }

    .meta-row {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 14px;
      margin-top: auto; /* Mendorong meta & tombol ke bawah agar tingginya presisi seimbang */
    }

    .meta-pill {
      background: #f1f5f9;
      color: #334155;
      border-radius: 999px;
      padding: 7px 11px;
      font-size: 12px;
      font-weight: 600;
    }

    .deadline-box {
      background: #fff7ed;
      color: #c2410c;
      border: 1px solid #fed7aa;
      border-radius: 12px;
      padding: 10px 12px;
      font-size: 13px;
      font-weight: 700;
      margin-bottom: 16px;
    }

    .detail-btn {
      width: 100%;
      border: none;
      border-radius: 12px;
      padding: 12px;
      background: linear-gradient(90deg, var(--blue), var(--teal));
      color: white;
      font-weight: 700;
      text-decoration: none;
      display: block;
      text-align: center;
    }

    @media (max-width: 991px) {
      .filter-card {
        position: static;
      }

      .hero-title {
        font-size: 32px;
      }

      .search-hero {
        flex-direction: column;
      }

      .search-hero button {
        padding: 12px;
      }
    }
    /* Responsif untuk mobile */
    @media (max-width: 768px) {
      .scholarship-img {
        height: 180px; /* Lebih pendek lagi di mobile */
        aspect-ratio: 16/9;
      }
      
      .scholarship-img .emoji {
        font-size: 42px;
      }
    }
    
  </style>
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
          <li class="nav-item"><a class="nav-link active" href="halamanBeasiswa.php">Beasiswa</a></li>
          <li class="nav-item"><a class="btn-gradient" href="halamanTransaksiBeasiswa.php">Publikasi Beasiswa</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="py-5 text-center">
    <div class="container">
      <h1 class="hero-title">Temukan Beasiswa Terbaik</h1>
      <p class="hero-subtitle">
        Jelajahi berbagai peluang beasiswa untuk mendukung perjalanan pendidikanmu.
      </p>

      <div class="search-hero">
        <input type="text" id="heroSearch" placeholder="Cari beasiswa, penyelenggara, atau jenjang..." onkeyup="filterScholarships()">
        <button onclick="filterScholarships()">Cari Beasiswa</button>
      </div>
    </div>
  </section>

  <main class="container pb-5">
    <div class="row g-4">

      <div class="col-lg-3">
        <div class="card-custom filter-card">
          <h3 class="filter-title">Filter Beasiswa</h3>

          <div class="mb-4">
            <label>Cari Nama Beasiswa</label>
            <input type="text" class="form-control" id="sideSearch" placeholder="Contoh: LPDP" onkeyup="filterScholarships()">
          </div>

          <div class="mb-4">
            <label>Jenjang Pendidikan</label>
            <select class="form-select" id="levelFilter" onchange="filterScholarships()">
              <option value="all">Semua Jenjang</option>
              <option value="SMA/SMK">SMA/SMK</option>
              <option value="D3/D4/S1">D3/D4/S1</option>
              <option value="S2">S2</option>
              <option value="S3">S3</option>
              <option value="Umum">Umum</option>
            </select>
          </div>

          <div class="mb-3">
            <label>Tipe Pendanaan</label>

            <div class="radio-item">
              <input type="radio" name="funding" value="all" checked onchange="filterScholarships()">
              <span>Semua</span>
            </div>

            <div class="radio-item">
              <input type="radio" name="funding" value="Fully Funded" onchange="filterScholarships()">
              <span>Fully Funded</span>
            </div>

            <div class="radio-item">
              <input type="radio" name="funding" value="Partial Funded" onchange="filterScholarships()">
              <span>Partial Funded</span>
            </div>
            
            <div class="radio-item">
              <input type="radio" name="funding" value="Bantuan Dana / One-Time Stipend" onchange="filterScholarships()">
              <span>Bantuan Dana / One-Time Stipend</span>
            </div>
          </div>

          <button class="filter-btn" onclick="filterScholarships()">Terapkan Filter</button>
          <button class="reset-btn" onclick="resetFilter()">Reset</button>
        </div>
      </div>

      <div class="col-lg-9">
        <div class="section-heading">
          <div>
            <h2>Daftar Beasiswa</h2>
            <p class="result-count mb-0" id="resultText">Memuat data...</p>
          </div>
        </div>

        <div class="row g-4" id="scholarshipGrid">
          <?php
          // Mengambil data yang status_verifikasinya 'disetujui' dan deadline masih berlaku
          $query_string = "SELECT b.* FROM beasiswa b 
                           JOIN iklan_beasiswa i ON b.id_beasiswa = i.id_beasiswa 
                           WHERE i.status_verifikasi = 'disetujui' AND b.deadline >= CURDATE()";
          
          $list_beasiswa = mysqli_query($conn, $query_string);

          if ($list_beasiswa && mysqli_num_rows($list_beasiswa) > 0) {
              while ($row = mysqli_fetch_assoc($list_beasiswa)) {
                  $file_poster = !empty($row['poster']) ? $row['poster'] : '';
                  $judul_tampil = $row['nama_beasiswa']; 
          ?>
              <div class="col-md-6 scholarship-item" 
                data-title="<?= htmlspecialchars(strtolower($judul_tampil)) ?>" 
                data-level="<?= htmlspecialchars($row['jenjang']) ?>" 
                data-funding="<?= htmlspecialchars($row['tipe_pendanaan']) ?>">
                
                <div class="card-custom scholarship-card">
                  <div class="scholarship-img">
                    <?php if (!empty($file_poster)): ?>
                      <img src="uploads/<?= htmlspecialchars($file_poster) ?>" alt="Poster <?= htmlspecialchars($judul_tampil) ?>" class="poster-beasiswa">
                    <?php else: ?>
                      🎓 
                    <?php endif; ?>

                    <span class="badge-level"><?= htmlspecialchars($row['jenjang']) ?></span>
                    <span class="badge-funding"><?= htmlspecialchars($row['tipe_pendanaan']) ?></span>
                  </div>
                  <div class="scholarship-body">
                    <h4 class="scholarship-title"><?= htmlspecialchars($judul_tampil) ?></h4>
                    <p class="organizer"><?= htmlspecialchars($row['penyelenggara']) ?></p>

                    <p class="scholarship-desc">
                      <?= !empty($row['deskripsi']) ? htmlspecialchars($row['deskripsi']) : 'Tidak ada deskripsi tambahan untuk program beasiswa ini.' ?>
                    </p>

                    <div class="meta-row">
                      <span class="meta-pill"><?= htmlspecialchars($row['tingkat_beasiswa']) ?></span>
                    </div>

                    <div class="deadline-box">
                      ⏰ Deadline: <?= ($row['deadline'] && $row['deadline'] != '0000-00-00') ? date('d F Y', strtotime($row['deadline'])) : 'Tanpa Deadline' ?>
                    </div>

                    <a href="detailBeasiswa.php?id=<?= $row['id_beasiswa'] ?>" class="detail-btn">Lihat Detail</a>
                  </div>
                </div>
              </div>
          <?php
              } 
          } else {
              echo "<div class='col-12 text-center py-5'><h5 class='text-muted'>Belum ada beasiswa aktif yang disetujui.</h5></div>";
          }
          ?>
        </div>
      </div>
    </div>
  </main>

  <script>
    function filterScholarships() {
      const heroSearch = document.getElementById('heroSearch').value.toLowerCase();
      const sideSearch = document.getElementById('sideSearch').value.toLowerCase();
      const searchText = heroSearch || sideSearch;

      const level = document.getElementById('levelFilter').value;
      const funding = document.querySelector('input[name="funding"]:checked').value;
      
      const items = document.querySelectorAll('.scholarship-item');
      let visibleCount = 0;

      items.forEach(item => {
        const title = item.dataset.title.toLowerCase();
        const itemLevel = item.dataset.level;
        const itemFunding = item.dataset.funding;

        const matchSearch = searchText === '' || title.includes(searchText);
        const matchLevel = level === 'all' || itemLevel === level;
        const matchFunding = funding === 'all' || itemFunding === funding;

        if (matchSearch && matchLevel && matchFunding) {
          item.style.display = 'block';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      });

      document.getElementById('resultText').textContent = `Menampilkan ${visibleCount} beasiswa`;
    }

    function resetFilter() {
      document.getElementById('heroSearch').value = '';
      document.getElementById('sideSearch').value = '';
      document.getElementById('levelFilter').value = 'all';
      document.querySelector('input[name="funding"][value="all"]').checked = true;
      
      filterScholarships();
    }

    window.addEventListener('DOMContentLoaded', () => {
      const totalItems = document.querySelectorAll('.scholarship-item').length;
      document.getElementById('resultText').textContent = `Menampilkan ${totalItems} beasiswa`;
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
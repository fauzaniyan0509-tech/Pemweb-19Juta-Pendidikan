<?php
// KONEKSI DATABASE
$host = "localhost"; $user = "root"; $pass = ""; $db = "19juta_pendidikan";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());
mysqli_set_charset($conn, "utf8mb4");

// Ambil semua tempat
$list_tempat = mysqli_query($conn, "SELECT * FROM tempat_edukatif ORDER BY id_tempat DESC");
$total       = mysqli_num_rows($list_tempat);

// Mapping ikon kategori
function ikonKategori($kat) {
    $map = [
        'perpustakaan'  => ['📚', 'Perpustakaan'],
        'kafe-belajar'  => ['☕', 'Kafe Belajar'],
        'teknologi'     => ['💡', 'Teknologi & Inovasi'],
        'museum'        => ['🏛️', 'Museum & Sejarah'],
        'ruang-kreatif' => ['🎨', 'Ruang Kreatif'],
    ];
    return $map[$kat] ?? ['📌', ucfirst($kat)];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Peta Akses Pendidikan – 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="tempatEdukatif.css">
  <style>
    /* Tambahan style untuk kartu dinamis */
    .rating-bintang { color: #f59e0b; font-size: 13px; font-weight: 700; }
    .sosmed-link { color: #6366f1; font-size: 12px; font-weight: 600; text-decoration: none; }
    .sosmed-link:hover { text-decoration: underline; }
    .tombol-maps {
      display: inline-flex; align-items: center; gap: 7px;
      background: linear-gradient(90deg, #2f6df6, #35c7b6);
      color: white; border: none; border-radius: 12px;
      padding: 10px 16px; font-size: 13px; font-weight: 700;
      text-decoration: none; transition: opacity .2s;
      width: 100%; justify-content: center; margin-top: 8px;
    }
    .tombol-maps:hover { opacity: .88; color: white; }
    .gambar-tempat { width: 100%; height: 180px; object-fit: cover; }
    .gambar-placeholder {
      width: 100%; height: 180px; background: linear-gradient(135deg, #dbeafe, #ccfbf1);
      display: flex; align-items: center; justify-content: center;
      font-size: 48px;
    }
    .prasarana-tags { display: flex; flex-wrap: wrap; gap: 6px; margin: 10px 0; }
    .tag-prasarana {
      background: #f0fdf4; color: #15803d; border-radius: 999px;
      padding: 3px 10px; font-size: 11px; font-weight: 600;
    }
    .info-baris { display: flex; align-items: flex-start; gap: 6px; font-size: 12px; color: #6b7280; margin-bottom: 4px; }
    .info-baris span { line-height: 1.4; }
    .kartu-tempat:hover { transform: translateY(-5px); }
    .kartu-tempat { transition: transform .25s ease; }
    .kosong-state { text-align: center; padding: 60px 20px; color: #6b7280; }
    .kosong-state .ikon { font-size: 56px; margin-bottom: 16px; }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg py-3">
    <div class="container">
      <a class="logo-teks" href="beranda.php">19JutaPendidikan</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center gap-lg-4">
          <li class="nav-item"><a class="nav-link" href="beranda.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="halamanLomba.php">Lomba</a></li>
          <li class="nav-item"><a class="nav-link" href="halamanBeasiswa.php">Beasiswa</a></li>
          <li class="nav-item"><a class="nav-link aktif" href="#">Peta Edukasi</a></li>
          <li class="nav-item"><a class="tombol-gradient" href="halamanLomba.php">Publikasi Lomba</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- HERO & PENCARIAN -->
  <section class="py-5 text-center">
    <div class="container">
      <h1 class="judul-hero">Jelajahi Tempat Edukatif</h1>
      <p class="subjudul-hero">
        Temukan perpustakaan, ruang belajar, dan tempat inspiratif di Solo dan sekitarnya untuk mendukung perjalanan belajarmu.
      </p>
      <div class="kotak-pencarian">
        <input type="text" id="pencarianHero" placeholder="Cari nama tempat, kategori, atau fasilitas..." oninput="filterTempat()">
        <button onclick="filterTempat()">Cari Tempat</button>
      </div>
    </div>
  </section>

  <!-- KONTEN UTAMA -->
  <main class="container pb-5">
    <div class="row g-4">

      <!-- PANEL FILTER -->
      <div class="col-lg-3">
        <div class="kartu panel-filter">
          <h3 class="judul-filter">Filter Tempat</h3>

          <div class="mb-4">
            <label>Cari Nama Tempat</label>
            <input type="text" class="input-filter" id="pencarianSamping" placeholder="Contoh: Perpustakaan" oninput="filterTempat()">
          </div>

          <div class="mb-4">
            <label>Kategori Tempat</label>
            <select class="select-filter" id="filterKategori" onchange="filterTempat()">
              <option value="semua">Semua Kategori</option>
              <option value="perpustakaan">Perpustakaan</option>
              <option value="kafe-belajar">Kafe Belajar</option>
              <option value="teknologi">Teknologi & Inovasi</option>
              <option value="museum">Museum & Sejarah</option>
              <option value="ruang-kreatif">Ruang Kreatif</option>
              <option value="tempat-makan">Tempat Makan</option>
              <option value="ruang-terbuka">Ruang Terbuka</option>
              <option value="lainnya">Lainnya</option>
            </select>
          </div>

          <div class="mb-3">
            <label>Rating Minimum</label>
            <select class="select-filter" id="filterRating" onchange="filterTempat()">
              <option value="0">Semua Rating</option>
              <option value="3">⭐ 3+</option>
              <option value="4">⭐ 4+</option>
              <option value="4.5">⭐ 4.5+</option>
            </select>
          </div>

          <button class="tombol-filter" onclick="filterTempat()">Terapkan Filter</button>
          <button class="tombol-reset" onclick="resetFilter()">Reset Filter</button>
        </div>
      </div>

      <!-- GRID TEMPAT -->
      <div class="col-lg-9">
        <div class="judul-section">
          <div>
            <h2>Daftar Tempat Edukatif</h2>
            <p class="teks-jumlah mb-0" id="teksJumlah">Menampilkan <?= $total ?> tempat</p>
          </div>
        </div>

        <div class="row g-4" id="gridTempat">

          <?php if ($total == 0): ?>
          <div class="col-12">
            <div class="kosong-state">
              <div class="ikon">🗺️</div>
              <h5>Belum ada tempat edukatif</h5>
              <p>Admin belum menambahkan tempat. Cek lagi nanti ya!</p>
            </div>
          </div>
          <?php endif; ?>

          <?php while ($t = mysqli_fetch_assoc($list_tempat)):
            [$ikon, $labelKat] = ikonKategori($t['kategori'] ?? '');
            // Prasarana jadi array tag
            $prasaranaList = [];
            if ($t['prasarana']) {
              $prasaranaList = array_map('trim', explode(',', $t['prasarana']));
            }
          ?>
          <div class="col-md-6 item-tempat"
            data-nama="<?= strtolower(htmlspecialchars($t['nama_tempat_edukatif'])) ?>"
            data-kategori="<?= htmlspecialchars($t['kategori'] ?? '') ?>"
            data-rating="<?= floatval($t['rating'] ?? 0) ?>">

            <div class="kartu kartu-tempat h-100">

              <!-- GAMBAR -->
              <div class="wrapper-gambar">
                <?php if ($t['foto']): ?>
                  <img src="uploads/<?= htmlspecialchars($t['foto']) ?>"
                       alt="<?= htmlspecialchars($t['nama_tempat_edukatif']) ?>"
                       class="gambar-tempat">
                <?php else: ?>
                  <div class="gambar-placeholder"><?= $ikon ?></div>
                <?php endif; ?>
                <span class="label-kategori"><?= $ikon ?> <?= htmlspecialchars($labelKat) ?></span>
              </div>

              <!-- ISI KARTU -->
              <div class="isi-kartu">
                <h4 class="nama-tempat"><?= htmlspecialchars($t['nama_tempat_edukatif']) ?></h4>

                <!-- Rating -->
                <?php if ($t['rating']): ?>
                <div class="rating-bintang mb-2">⭐ <?= htmlspecialchars($t['rating']) ?> / 5</div>
                <?php endif; ?>

                <!-- Jam Operasional -->
                <?php if ($t['jam_operasional']): ?>
                <div class="info-baris">
                  <span>🕐</span>
                  <span><?= htmlspecialchars($t['jam_operasional']) ?></span>
                </div>
                <?php endif; ?>

                <!-- Sosial Media -->
                <?php if ($t['sosial_media']): ?>
                <div class="info-baris">
                  <span>📱</span>
                  <?php
                    $sosmed = $t['sosial_media'];
                    $isLink = str_starts_with($sosmed, 'http');
                  ?>
                  <?php if ($isLink): ?>
                    <a href="<?= htmlspecialchars($sosmed) ?>" target="_blank" class="sosmed-link"><?= htmlspecialchars($sosmed) ?></a>
                  <?php else: ?>
                    <span class="sosmed-link"><?= htmlspecialchars($sosmed) ?></span>
                  <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Prasarana Tags -->
                <?php if (!empty($prasaranaList)): ?>
                <div class="prasarana-tags">
                  <?php foreach (array_slice($prasaranaList, 0, 5) as $p): ?>
                    <span class="tag-prasarana">✓ <?= htmlspecialchars(trim($p)) ?></span>
                  <?php endforeach; ?>
                  <?php if (count($prasaranaList) > 5): ?>
                    <span class="tag-prasarana">+<?= count($prasaranaList) - 5 ?> lagi</span>
                  <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- TOMBOL MAPS -->
                <?php if ($t['alamat_maps']): ?>
                <a href="<?= htmlspecialchars($t['alamat_maps']) ?>" target="_blank" class="tombol-maps">
                  🗺️ Lihat di Google Maps
                </a>
                <?php else: ?>
                <button class="tombol-maps" style="opacity:.45; cursor:not-allowed;" disabled>
                  🗺️ Link Maps belum tersedia
                </button>
                <?php endif; ?>

              </div>
            </div>
          </div>
          <?php endwhile; ?>

        </div><!-- /gridTempat -->

        <!-- EMPTY STATE FILTER -->
        <div class="kondisi-kosong" id="kondisiKosong" style="display:none;">
          <div class="ikon-kosong">🔍</div>
          <h5>Tempat tidak ditemukan</h5>
          <p>Coba ubah kata kunci atau filter yang kamu gunakan.</p>
        </div>

      </div>
    </div>
  </main>

  <script>
    function filterTempat() {
      const cariHero    = document.getElementById('pencarianHero').value.toLowerCase().trim();
      const cariSamping = document.getElementById('pencarianSamping').value.toLowerCase().trim();
      const kategori    = document.getElementById('filterKategori').value;
      const ratingMin   = parseFloat(document.getElementById('filterRating').value) || 0;

      const items    = document.querySelectorAll('.item-tempat');
      const kataCari = cariHero || cariSamping;
      let jumlah     = 0;

      items.forEach(item => {
        const nama       = item.dataset.nama;
        const itemKat    = item.dataset.kategori;
        const itemRating = parseFloat(item.dataset.rating) || 0;

        const cocokTeks    = kataCari === '' || nama.includes(kataCari);
        const cocokKat     = kategori === 'semua' || itemKat === kategori;
        const cocokRating  = itemRating >= ratingMin;

        if (cocokTeks && cocokKat && cocokRating) {
          item.style.display = 'block';
          jumlah++;
        } else {
          item.style.display = 'none';
        }
      });

      document.getElementById('teksJumlah').textContent = `Menampilkan ${jumlah} tempat`;
      document.getElementById('kondisiKosong').style.display = jumlah === 0 ? 'block' : 'none';
    }

    function resetFilter() {
      document.getElementById('pencarianHero').value    = '';
      document.getElementById('pencarianSamping').value = '';
      document.getElementById('filterKategori').value   = 'semua';
      document.getElementById('filterRating').value     = '0';
      filterTempat();
    }

    // Sinkronisasi dua input pencarian
    document.getElementById('pencarianHero').addEventListener('input', () => {
      document.getElementById('pencarianSamping').value = '';
    });
    document.getElementById('pencarianSamping').addEventListener('input', () => {
      document.getElementById('pencarianHero').value = '';
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

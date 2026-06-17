<?php
// KONEKSI DATABASE
$host = "localhost"; $user = "root"; $pass = ""; $db = "19juta_pendidikan";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());
mysqli_set_charset($conn, "utf8mb4");

require_once 'helperSosmed.php';

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

    /* FILTER BARIS ATAS */
    .select-filter-top {
      border-radius: 12px; padding: 10px 16px; font-size: 14px; font-weight: 600;
      border: 1px solid #e2e8f0; background: white; color: #334155;
      font-family: 'Poppins', sans-serif; cursor: pointer; min-width: 180px;
    }
    .select-filter-top:focus { outline: none; border-color: #2f6df6; box-shadow: 0 0 0 3px rgba(47,109,246,.1); }
    .tombol-reset-top {
      border-radius: 12px; padding: 10px 18px; font-size: 14px; font-weight: 700;
      border: 1px solid #e2e8f0; background: white; color: #64748b; cursor: pointer;
    }
    .tombol-reset-top:hover { background: #f1f5f9; }

    .tag-lagi {
      background: #ede9fe; color: #6d28d9; border-radius: 999px;
      padding: 3px 10px; font-size: 11px; font-weight: 700;
      cursor: pointer; border: none; transition: background .2s;
    }
    .tag-lagi:hover { background: #ddd6fe; }

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
          <li class="nav-item"><a class="nav-link aktif" href="halamanTempatEdukatif.php">Tempat Edukatif</a></li>
          <li class="nav-item"><a class="tombol-gradient" href="PengajuanTempat.php">Pengajuan Tempat</a></li>
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
      <!-- Filter baris bawah search -->
      <div class="filter-baris mt-3 d-flex justify-content-center gap-3 flex-wrap">
        <select class="select-filter-top" id="filterKategori" onchange="filterTempat()">
          <option value="semua">Semua Kategori</option>
          <option value="perpustakaan">📚 Perpustakaan</option>
          <option value="kafe-belajar">☕ Kafe Belajar</option>
          <option value="teknologi">💡 Teknologi & Inovasi</option>
          <option value="museum">🏛️ Museum & Sejarah</option>
          <option value="ruang-kreatif">🎨 Ruang Kreatif</option>
          <option value="lainnya">📌 Lainnya</option>
        </select>
        <select class="select-filter-top" id="filterRating" onchange="filterTempat()">
          <option value="0">Semua Rating</option>
          <option value="3">⭐ 3+</option>
          <option value="4">⭐ 4+</option>
          <option value="4.5">⭐ 4.5+</option>
        </select>
        <button class="tombol-reset-top" onclick="resetFilter()">Reset</button>
      </div>
    </div>
  </section>

  <!-- KONTEN UTAMA -->
  <main class="container pb-5">
    <div class="row g-4">

      <!-- GRID TEMPAT (full width) -->
      <div class="col-12">
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
                <?php $listSosmed = daftarSosmed($t); ?>
                <?php if (!empty($listSosmed)): ?>
                <div class="info-baris" style="flex-wrap: wrap; gap: 10px;">
                  <?php foreach ($listSosmed as $sm): ?>
                    <?php if ($sm['url']): ?>
                      <a href="<?= htmlspecialchars($sm['url']) ?>" target="_blank" class="sosmed-link"><?= $sm['icon'] ?> <?= htmlspecialchars($sm['label']) ?></a>
                    <?php else: ?>
                      <span class="sosmed-link"><?= $sm['icon'] ?> <?= htmlspecialchars($sm['label']) ?></span>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Prasarana Tags -->
                <?php if (!empty($prasaranaList)):
                  $tampil  = array_slice($prasaranaList, 0, 5);
                  $sisanya = array_slice($prasaranaList, 5);
                ?>
                <div class="prasarana-tags">
                  <?php foreach ($tampil as $p): ?>
                    <span class="tag-prasarana">✓ <?= htmlspecialchars(trim($p)) ?></span>
                  <?php endforeach; ?>
                  <?php if (count($sisanya) > 0): ?>
                    <span class="tags-tersembunyi" style="display:none;">
                      <?php foreach ($sisanya as $p): ?>
                        <span class="tag-prasarana">✓ <?= htmlspecialchars(trim($p)) ?></span>
                      <?php endforeach; ?>
                    </span>
                    <button class="tag-lagi" onclick="expandTags(this)">+<?= count($sisanya) ?> lagi</button>
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

      </div><!-- /col-12 -->
    </div>
  </main>

  <script>
    function expandTags(btn) {
      const tersembunyi = btn.previousElementSibling;
      if (tersembunyi && tersembunyi.classList.contains('tags-tersembunyi')) {
        const parent = btn.parentElement;
        while (tersembunyi.firstChild) {
          parent.insertBefore(tersembunyi.firstChild, btn);
        }
        tersembunyi.remove();
      }
      btn.remove();
    }

    function filterTempat() {
      const kataCari  = document.getElementById('pencarianHero').value.toLowerCase().trim();
      const kategori  = document.getElementById('filterKategori').value;
      const ratingMin = parseFloat(document.getElementById('filterRating').value) || 0;

      const items = document.querySelectorAll('.item-tempat');
      let jumlah  = 0;

      items.forEach(item => {
        const cocokTeks   = kataCari === '' || item.dataset.nama.includes(kataCari);
        const cocokKat    = kategori === 'semua' || item.dataset.kategori === kategori;
        const cocokRating = (parseFloat(item.dataset.rating) || 0) >= ratingMin;

        if (cocokTeks && cocokKat && cocokRating) {
          item.style.display = 'block'; jumlah++;
        } else {
          item.style.display = 'none';
        }
      });

      document.getElementById('teksJumlah').textContent = `Menampilkan ${jumlah} tempat`;
      document.getElementById('kondisiKosong').style.display = jumlah === 0 ? 'block' : 'none';
    }

    function resetFilter() {
      document.getElementById('pencarianHero').value  = '';
      document.getElementById('filterKategori').value = 'semua';
      document.getElementById('filterRating').value   = '0';
      filterTempat();
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
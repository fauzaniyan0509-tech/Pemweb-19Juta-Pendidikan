<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Pengguna - 19JutaPendidikan</title>

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
    }

    .nav-link {
      font-size: 14px;
      color: #1f2937;
      font-weight: 500;
    }

    .dropdown-menu {
      border: none; border-radius: 14px; padding: 8px;
      box-shadow: 0 12px 30px rgba(20,33,61,.12);
      margin-top: 10px; min-width: 190px;
    }
    .dropdown-item {
      border-radius: 10px; padding: 10px 14px; font-size: 14px;
      font-weight: 600; color: #1f2937; transition: background .15s, color .15s;
    }
    .dropdown-item:hover, .dropdown-item:focus {
      background: linear-gradient(90deg, var(--blue), var(--teal));
      color: white;
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

    .page-title {
      font-weight: 800;
      font-size: 36px;
      background: linear-gradient(90deg, var(--blue), var(--teal));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .page-subtitle {
      color: var(--muted);
      font-size: 15px;
    }

    .card-custom {
      background: var(--card);
      border-radius: 20px;
      box-shadow: var(--shadow);
      border: 1px solid #eef2f7;
    }

    .profile-card {
      padding: 30px;
      position: relative;
      overflow: hidden;
    }

    .profile-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 90px;
      background: linear-gradient(90deg, var(--blue), var(--teal));
    }

    .avatar {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      background: white;
      border: 5px solid white;
      box-shadow: 0 10px 25px rgba(0,0,0,0.12);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 44px;
      position: relative;
      z-index: 2;
      margin-top: 35px;
    }

    .badge-role {
      display: inline-block;
      background: #effffb;
      color: #087f6f;
      border: 1px solid var(--teal);
      padding: 7px 14px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
    }

    .info-item {
      border-bottom: 1px solid #eef2f7;
      padding: 14px 0;
    }

    .info-item:last-child {
      border-bottom: none;
    }

    .info-label {
      font-size: 12px;
      color: var(--muted);
      margin-bottom: 4px;
    }

    .info-value {
      font-weight: 600;
      color: var(--dark);
    }

    .stat-card {
      padding: 22px;
      transition: 0.25s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-icon {
      width: 46px;
      height: 46px;
      border-radius: 14px;
      background: #eaf5ff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      margin-bottom: 12px;
    }

    .stat-number {
      font-size: 28px;
      font-weight: 800;
      color: var(--blue);
    }

    .section-title {
      font-size: 18px;
      font-weight: 800;
      margin-bottom: 18px;
    }

    .activity-item {
      display: flex;
      gap: 14px;
      padding: 16px 0;
      border-bottom: 1px solid #eef2f7;
    }

    .activity-item:last-child {
      border-bottom: none;
    }

    .activity-icon {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      background: #f1f5f9;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .activity-title {
      font-weight: 700;
      margin-bottom: 2px;
    }

    .activity-desc {
      color: var(--muted);
      font-size: 13px;
      margin: 0;
    }

    .status-badge {
      font-size: 11px;
      font-weight: 700;
      border-radius: 999px;
      padding: 6px 10px;
    }

    .status-active {
      background: #dcfce7;
      color: #15803d;
    }

    .status-review {
      background: #fef3c7;
      color: #b45309;
    }

    .edit-modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.45);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .edit-box {
      background: white;
      border-radius: 22px;
      padding: 30px;
      max-width: 520px;
      width: 100%;
      box-shadow: var(--shadow);
      animation: pop 0.25s ease;
    }

    @keyframes pop {
      from { transform: scale(0.92); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }

    .form-control {
      border-radius: 12px;
      padding: 11px 13px;
      font-size: 14px;
    }

    .form-control:focus {
      border-color: var(--teal);
      box-shadow: 0 0 0 4px rgba(53, 199, 182, 0.15);
    }

    @media (max-width: 768px) {
      .page-title {
        font-size: 28px;
      }

      .profile-card {
        text-align: center;
      }

      .avatar {
        margin-left: auto;
        margin-right: auto;
      }
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg py-3">
    <div class="container">
      <a class="logo-text" href="beranda.php">19JutaPendidikan</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center gap-lg-4">
          <li class="nav-item"><a class="nav-link" href="beranda.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="halamanLomba.php">Lomba</a></li>
          <li class="nav-item"><a class="nav-link" href="halamanBeasiswa.php">Beasiswa</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdownPetaEdukasi" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Peta Edukasi
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownPetaEdukasi">
              <li><a class="dropdown-item" href="halamanTempatEdukatif.php">🔍 Cari Tempat</a></li>
              <li><a class="dropdown-item" href="PengajuanTempat.php">📍 Posting Tempat</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="btn-gradient" href="halamanTransaksiLomba.php">Publikasi Lomba</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container py-5">

    <div class="text-center mb-5">
      <h1 class="page-title">Profil Pengguna</h1>
      <p class="page-subtitle">Kelola data akun, aktivitas, dan riwayat publikasi pendidikanmu.</p>
    </div>

    <div class="row g-4">

      <div class="col-lg-4">
        <div class="card-custom profile-card">
          <div class="avatar">👤</div>

          <div class="mt-4">
            <h3 class="fw-bold mb-1" id="profileName">Mukhammad Fauzan</h3>
            <p class="text-muted mb-3" id="profileEmail">fauzan@example.com</p>
            <span class="badge-role">Pelajar / Mahasiswa</span>
          </div>

          <button class="btn-gradient w-100 mt-4" onclick="openEditModal()">Edit Profil</button>

          <div class="mt-4">
            <div class="info-item">
              <div class="info-label">Nama Lengkap</div>
              <div class="info-value" id="infoName">Mukhammad Fauzan</div>
            </div>

            <div class="info-item">
              <div class="info-label">Email</div>
              <div class="info-value" id="infoEmail">fauzan@example.com</div>
            </div>

            <div class="info-item">
              <div class="info-label">Status Akun</div>
              <div class="info-value text-success">Aktif</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-8">

        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="card-custom stat-card">
              <div class="stat-icon">🏆</div>
              <div class="stat-number">4</div>
              <p class="text-muted mb-0 small">Lomba Diikuti</p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card-custom stat-card">
              <div class="stat-icon">🎓</div>
              <div class="stat-number">7</div>
              <p class="text-muted mb-0 small">Beasiswa Disimpan</p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card-custom stat-card">
              <div class="stat-icon">📍</div>
              <div class="stat-number">3</div>
              <p class="text-muted mb-0 small">Tempat Favorit</p>
            </div>
          </div>
        </div>

        <div class="card-custom p-4 mb-4">
          <h4 class="section-title">Aktivitas Terbaru</h4>

          <div class="activity-item">
            <div class="activity-icon">🏆</div>
            <div>
              <div class="activity-title">Mengikuti Lomba Desain Poster Pendidikan</div>
              <p class="activity-desc">Terdaftar pada event tanggal 19 bulan ini.</p>
            </div>
          </div>

          <div class="activity-item">
            <div class="activity-icon">🎓</div>
            <div>
              <div class="activity-title">Menyimpan Beasiswa Unggulan</div>
              <p class="activity-desc">Disimpan untuk dilihat kembali nanti.</p>
            </div>
          </div>

          <div class="activity-item">
            <div class="activity-icon">📍</div>
            <div>
              <div class="activity-title">Menambahkan Perpustakaan Kota ke Favorit</div>
              <p class="activity-desc">Tempat edukatif dengan WiFi dan ruang belajar.</p>
            </div>
          </div>
        </div>

        <div class="card-custom p-4">
          <h4 class="section-title">Riwayat Publikasi Lomba</h4>

          <div class="activity-item justify-content-between align-items-center">
            <div class="d-flex gap-3">
              <div class="activity-icon">📢</div>
              <div>
                <div class="activity-title">Lomba Essay Pendidikan Nasional</div>
                <p class="activity-desc">Paket Langganan Tahunan • Rp499.000</p>
              </div>
            </div>
            <span class="status-badge status-active">Aktif</span>
          </div>

          <div class="activity-item justify-content-between align-items-center">
            <div class="d-flex gap-3">
              <div class="activity-icon">📄</div>
              <div>
                <div class="activity-title">Kompetisi UI/UX Pelajar</div>
                <p class="activity-desc">Paket Per Publikasi • Rp50.000</p>
              </div>
            </div>
            <span class="status-badge status-review">Review</span>
          </div>
        </div>

      </div>
    </div>
  </main>

  <div class="edit-modal" id="editModal">
    <div class="edit-box">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Edit Profil</h4>
        <button class="btn btn-light rounded-circle" onclick="closeEditModal()">×</button>
      </div>

      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" id="editName" value="Mukhammad Fauzan">
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" id="editEmail" value="fauzan@example.com">
      </div>

      <div class="mb-4">
        <label class="form-label">No. WhatsApp</label>
        <input type="text" class="form-control" id="editPhone" value="08xxxxxxxxxx">
      </div>

      <button class="btn-gradient w-100" onclick="saveProfile()">Simpan Perubahan</button>
    </div>
  </div>

  <script>
    const editModal = document.getElementById('editModal');

    function openEditModal() {
      editModal.style.display = 'flex';
    }

    function closeEditModal() {
      editModal.style.display = 'none';
    }

    function saveProfile() {
      const name = document.getElementById('editName').value;
      const email = document.getElementById('editEmail').value;
      const phone = document.getElementById('editPhone').value;

      document.getElementById('profileName').textContent = name;
      document.getElementById('profileEmail').textContent = email;
      document.getElementById('infoName').textContent = name;
      document.getElementById('infoEmail').textContent = email;
      document.getElementById('infoPhone').textContent = phone;

      closeEditModal();
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
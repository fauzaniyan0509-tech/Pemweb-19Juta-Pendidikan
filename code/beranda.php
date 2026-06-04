<?php
session_start();

// Sistem Proteksi: Jika user mencoba masuk tanpa login, langsung diusir ke halaman login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== "sudah_login") {
    header("Location: halamanLogin.php");
    exit();
}

// Logika Pemasangan Foto Profil Dinamis dari database/folder uploads
$foto_user = "foto-profil-user.jpg"; // Gambar cadangan jika user tidak mengunggah foto profil
if (!empty($_SESSION['foto_profil'])) {
    $foto_user = "uploads/" . $_SESSION['foto_profil'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="landingpage.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg py-3 sticky-top shadow-sm" style="background-color: #fff;">
        <div class="container">
            <a class="navbar-brand logo-text" href="beranda.php">19JutaPendidikan</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-lg-4">
                    <li class="nav-item"><a class="nav-link active" href="beranda.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>

                    <li class="nav-item mt-3 mt-lg-0">
                        <a href="logout.php" class="btn btn-danger px-3 py-2">Logout</a>
                    </li>

                    <li class="nav-item ms-lg-2 mt-3 mt-lg-0">
                        <a href="ProfilPengguna.php" class="profile-nav-link d-inline-block shadow-sm">
                            <img src="<?php echo $foto_user; ?>" alt="Profile" class="navbar-profile-img" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold section-title">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>! Silahkan pilih fitur dari website kami</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <a href="halamanTempatEdukatif.php" class="feature-link">
                        <div class="feature-card h-100 bg-white shadow-sm border-0 rounded-4 p-3">
                            <img src="aset gambar/gambar-peta-pendidikan-2.jpg" alt="Peta Pendidikan" class="img-feature mb-4 w-100 rounded-3">
                            <h3 class="feature-title">Peta akses pendidikan</h3>
                            <p class="feature-desc text-muted">Cari sekolah, kampus, tempat belajar yang nyaman dan free wi-fi? disini tempatnya!!!</p>
                            <div class="d-flex align-items-center mt-4 pt-3 border-top">
                                <img src="contoh-gambar-admin.jpg" class="avatar-img me-2" alt="Admin" style="width:30px; height:30px; border-radius:50%;">
                                <div>
                                    <p class="mb-0 fw-bold small">Admin Pendidikan</p>
                                    <p class="mb-0 text-muted extra-small">20.12.2025</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="halamanBeasiswa.php" class="feature-link">
                        <div class="feature-card h-100 bg-white shadow-sm border-0 rounded-4 p-3">
                            <img src="aset gambar/gambar-beasiswa.jpg" alt="Beasiswa" class="img-feature mb-4 w-100 rounded-3">
                            <h3 class="feature-title">Info Beasiswa</h3>
                            <p class="feature-desc text-muted">Mau cari beasiswa dari yang berbayar, hingga gratis? dari yang nasional sampai internasional? klik disini yaaa....</p>
                            <div class="d-flex align-items-center mt-4 pt-3 border-top">
                                <img src="contoh-gambar-admin.jpg" class="avatar-img me-2" alt="Admin" style="width:30px; height:30px; border-radius:50%;">
                                <div>
                                    <p class="mb-0 fw-bold small">Info Scholarship</p>
                                    <p class="mb-0 text-muted extra-small">20.12.2025</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="halamanLomba.php" class="feature-link">
                        <div class="feature-card h-100 bg-white shadow-sm border-0 rounded-4 p-3">
                            <img src="gambar-lomba.jpg" alt="Lomba" class="img-feature mb-4 w-100 rounded-3">
                            <h3 class="feature-title">Info Lomba</h3>
                            <p class="feature-desc text-muted">Mau cari lomba dari berbagai kategori, jenjang, hingga tempat lomba? cuss kepoin disini!!!</p>
                            <div class="d-flex align-items-center mt-4 pt-3 border-top">
                                <img src="contoh-gambar-admin.jpg" class="avatar-img me-2" alt="Admin" style="width:30px; height:30px; border-radius:50%;">
                                <div>
                                    <p class="mb-0 fw-bold small">Humas Lomba</p>
                                    <p class="mb-0 text-muted extra-small">20.12.2025</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
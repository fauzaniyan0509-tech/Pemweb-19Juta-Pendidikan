<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - 19JutaPendidikan</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="landingpage.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg py-3">
        <div class="container">
            <a class="navbar-brand logo-text" href="#">19JutaPendidikan</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-lg-4">
                    <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0 d-flex flex-column flex-lg-row gap-2">
                        <a href="#" class="btn btn-primary btn-nav px-4 py-2">Contact us</a>
                        <a href="halamanLoginAdmin.php" class="btn btn-outline-dark btn-nav px-4 py-2">Admin Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center flex-column-reverse flex-lg-row">
                
                <div class="col-lg-6 pe-lg-5 text-center text-lg-start mt-5 mt-lg-0">
                    <h1 class="hero-title fw-bold mb-4">
                        Memetakan <span class="text-blue">Pendidikan di Indonesia</span> yang lebih merata & lebih baik.
                    </h1>
                    <p class="hero-text text-muted mb-5">
                        Membuka akses belajar berkualitas bagi setiap anak bangsa melalui teknologi yang inklusif dan merata
                    </p>
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                        <a href="halamanRegistrasi.php" class="btn btn-primary custom-btn-primary">Daftar</a>
                        <a href="halamanLogin.php" class="btn btn-outline-primary custom-btn-outline">Login</a>
                    </div>
                </div>

                <div class="col-lg-6 text-center">
                    <img src="aset gambar/gambar-orang-jelas.png" alt="3D Illustration" class="img-fluid hero-img float-animation">
                </div>

            </div>
        </div>
    </section>

    <footer class="custom-footer">
        <div class="container">
            <div class="row gy-4 header-footer-space">
                <div class="col-lg-4 col-md-6 text-white text-opacity-75">
                    <h4 class="footer-logo mb-3">19JutaPendidikan</h4>
                    <p class="footer-desc">
                        Platform digital untuk memetakan dan meningkatkan akses pendidikan di seluruh Indonesia, mendukung pemerataan kesempatan belajar bagi semua.
                    </p>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-heading mb-3">Tautan Cepat</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Tentang Kami</a></li>
                        <li><a href="#">Peta Akses</a></li>
                        <li><a href="halamanLomba.html">Lomba</a></li> 
                        <li><a href="#">Beasiswa</a></li>
                        <li><a href="#">Seminar & Event</a></li>
                        <li><a href="#">Berita Edukasi</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading mb-3">Hubungi Kami</h5>
                    <ul class="list-unstyled footer-contact">
                        <li><i class="fa-regular fa-envelope me-2"></i> info@19jutapendidikan.id</li>
                        <li><i class="fa-solid fa-phone me-2"></i> +62 21 1234 5678</li>
                        <li><i class="fa-solid fa-location-dot me-2"></i> Jakarta, Indonesia</li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading mb-3">Ikuti Kami</h5>
                    <p class="text-white text-opacity-75 footer-desc mb-3">
                        Dapatkan update terbaru tentang program dan inisiatif pendidikan
                    </p>
                    <div class="footer-socials d-flex gap-2">
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between text-center text-md-start pt-4 mt-5">
                <p class="mb-2 mb-md-0">&copy; 2026 19JutaPendidikan. All rights reserved.</p>
                <div class="footer-bottom-links d-flex gap-3 justify-content-center">
                    <a href="#">Kebijakan Privasi</a>
                    <a href="#">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
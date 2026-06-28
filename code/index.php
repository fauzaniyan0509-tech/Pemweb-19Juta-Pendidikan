<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - 19JutaPendidikan</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
                <ul class="navbar-nav align-items-center gap-lg-3">
                    <li class="nav-item mt-2 mt-lg-0">
                        <a href="contactUs.php" class="btn px-4 py-2" style="background:linear-gradient(90deg,#148fcd,#35c7b6); color:white; border:none; border-radius:999px; font-weight:600; font-size:14px; text-decoration:none;">
                            Hubungi Kami
                        </a>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0">
                        <a href="halamanLoginAdmin.php" class="btn px-4 py-2" style="background:#0f2942; color:white; border:none; border-radius:999px; font-weight:600; font-size:14px; text-decoration:none;">
                            Admin Login
                        </a>
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
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start flex-wrap">
                        <?php if (isset($_SESSION['status_login']) && $_SESSION['status_login'] === "sudah_login"): ?>
                            <a href="beranda.php" class="btn px-5 py-3" style="background:linear-gradient(90deg,#148fcd,#35c7b6); color:white; border:none; border-radius:999px; font-weight:700; font-size:15px;">Ke Beranda →</a>
                        <?php else: ?>
                            <a href="halamanRegistrasi.php" class="btn px-5 py-3" style="background:linear-gradient(90deg,#148fcd,#35c7b6); color:white; border:none; border-radius:999px; font-weight:700; font-size:15px;">Daftar Sekarang</a>
                            <a href="halamanLogin.php" class="btn px-5 py-3" style="background:white; color:#148fcd; border:2px solid #148fcd; border-radius:999px; font-weight:700; font-size:15px;">Login</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-6 text-center">
                    <img src="../aset gambar/gambar-orang-jelas.png" alt="3D Illustration" class="img-fluid hero-img float-animation">
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
                        <li><a href="tentangKami.php">Tentang Kami</a></li>
                        <li><a href="halamanTempatEdukatif.php">Peta Akses</a></li>
                        <li><a href="halamanLomba.php">Lomba</a></li> 
                        <li><a href="halamanBeasiswa.php">Beasiswa</a></li>
                        <li><a href="blog.php">Berita Edukasi</a></li>
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
                    
                    <!-- ═══ BAGIAN YANG DIUBAH: 3 IKON SOSIAL MEDIA ═══ -->
                    <div class="footer-socials d-flex gap-2">
                        <!-- WhatsApp -->
                        <a href="https://wa.me/6281234567890" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="social-icon" 
                           title="Chat via WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        
                        <!-- Instagram -->
                        <a href="https://instagram.com/19jutapendidikan" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="social-icon" 
                           title="Follow Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        
                        <!-- Copy Link Website -->
                        <a href="javascript:void(0);" 
                           onclick="salinLinkWebsite()" 
                           class="social-icon" 
                           id="btnCopyLink"
                           title="Salin Link Website">
                            <i class="fa-solid fa-link" id="iconLink"></i>
                        </a>
                    </div>
                    
                    <!-- Notifikasi kecil saat link berhasil disalin -->
                    <div id="notifCopy" style="display:none; margin-top:10px; font-size:12px; color:#35c7b6; font-weight:600;">
                        ✅ Link berhasil disalin ke clipboard!
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
    
    <!-- ═══ SCRIPT UNTUK COPY LINK ═══ -->
    <script>
        function salinLinkWebsite() {
            // Ambil URL website saat ini
            const urlWebsite = window.location.href;
            
            // Gunakan Clipboard API untuk menyalin
            navigator.clipboard.writeText(urlWebsite).then(() => {
                // Ubah ikon link jadi checkmark sementara
                const iconLink = document.getElementById('iconLink');
                iconLink.classList.remove('fa-link');
                iconLink.classList.add('fa-check');
                
                // Tampilkan notifikasi
                const notif = document.getElementById('notifCopy');
                notif.style.display = 'block';
                
                // Kembalikan ke kondisi semula setelah 2 detik
                setTimeout(() => {
                    iconLink.classList.remove('fa-check');
                    iconLink.classList.add('fa-link');
                    notif.style.display = 'none';
                }, 2000);
            }).catch(err => {
                // Fallback untuk browser lama
                const tempInput = document.createElement('input');
                tempInput.value = urlWebsite;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                alert('Link berhasil disalin: ' + urlWebsite);
            });
        }
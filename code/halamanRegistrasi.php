<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="landingpage.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg py-3">
        <div class="container">
            <a class="navbar-brand logo-text" href="index.html">19JutaPendidikan</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav align-items-center gap-lg-4">
                    <li class="nav-item"><a class="nav-link" href="login.php">Sudah punya akun? Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="form-section d-flex align-items-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    
                    <div class="text-center mb-4">
                        <h2 class="fw-bold form-title">Daftar Akun Baru</h2>
                        <p class="text-muted form-subtitle">Bergabunglah bersama kami untuk masa depan lebih baik.</p>
                    </div>

                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Gagal Daftar!</strong> 
                            <?php 
                                if($_GET['error'] == "password_pendek") echo "Password minimal harus 8 karakter.";
                                else if($_GET['error'] == "data_kosong") echo "Semua data wajib diisi.";
                                else if($_GET['error'] == "email_terdaftar") echo "Email sudah digunakan.";
                                else if($_GET['error'] == "gagal_upload") echo "Gagal mengunggah foto profil.";
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                        <form action="penghubung.php" method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label custom-label">Nama Lengkap</label>
                                <input type="text" name="username" class="form-control custom-input" id="username" required placeholder="Contoh: Budi Widodo">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label custom-label">Email Address</label>
                                <input type="email" name="email" class="form-control custom-input" id="email" required placeholder="name@example.com">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label custom-label">Password (Min. 8 Karakter)</label>
                                <input type="password" name="password" class="form-control custom-input" id="password" required placeholder="********">
                            </div>

                            <div class="mb-4">
                                <label for="foto_profil" class="form-label custom-label">Foto Profil (Opsional)</label>
                                <input type="file" name="foto_profil" class="form-control custom-input" id="foto_profil" accept="image/*">
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="daftar" class="btn btn-primary custom-btn-primary py-3">Buat Akun Sekarang</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
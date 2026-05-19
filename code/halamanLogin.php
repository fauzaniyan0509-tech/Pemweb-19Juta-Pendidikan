<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="landingpage.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg py-3">
        <div class="container">
            <a class="navbar-brand logo-text" href="index.html">19JutaPendidikan</a>
        </div>
    </nav>

    <section class="form-section d-flex align-items-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8"> 
                    
                    <div class="text-center mb-4">
                        <h2 class="fw-bold form-title">Selamat Datang!</h2>
                        <p class="text-muted form-subtitle">Silahkan masukkan akun anda untuk masuk ke beranda.</p>
                    </div>

                    <?php if(isset($_GET['pesan']) && $_GET['pesan'] == "gagal"): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Login Gagal!</strong> Email atau Password salah.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($_GET['pesan']) && $_GET['pesan'] == "berhasil_daftar"): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Pendaftaran Berhasil!</strong> Akun Anda sudah aktif. Silakan login di bawah ini.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                        <form action="penghubung.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label custom-label">Email Address</label>
                                <input type="email" name="email" class="form-control custom-input" id="email" required placeholder="name@example.com">
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label custom-label">Password</label>
                                <input type="password" name="password" class="form-control custom-input" id="password" required placeholder="********">
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="login" class="btn btn-primary custom-btn-primary py-3">Masuk Sekarang</button>
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
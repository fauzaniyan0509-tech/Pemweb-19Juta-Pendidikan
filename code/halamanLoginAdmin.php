<?php
session_start();

// 1. KONEKSI KE DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "19juta_pendidikan"; // <--- UBAH INI sesuai dengan nama database yang kamu buat di phpMyAdmin

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

$error_message = "";

// 2. PROSES COCOKKAN DATA SAAT TOMBOL DIKLIK
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_admin = $_POST['nama_admin'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query mencocokan 3 inputan sekaligus (nama, email, password) ke tabel admin
    $stmt = $conn->prepare("SELECT * FROM admin WHERE nama_admin = ? AND email = ? AND password = ?");
    $stmt->bind_param("sss", $nama_admin, $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika cocok, simpan session nama admin dan lempar ke dashboard
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = $nama_admin;
        
        header("Location: adminDashboard.php"); // <--- Sesuaikan ekstensinya jika dashboard-mu berubah jadi .php
        exit();
    } else {
        // Jika salah satu atau ketiganya tidak cocok
        $error_message = "Kombinasi Nama, Email, atau Password salah!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin - 19JutaPendidikan</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

    :root {
      --blue: #2f6df6;
      --blue-dark: #1751d1;
      --teal: #35c7b6;
      --dark: #14213d;
      --muted: #6b7280;
      --soft-bg: #eef8fb;
      --card: #ffffff;
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
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-container {
      width: 100%;
      max-width: 450px;
      padding: 20px;
    }

    .login-card {
      background: var(--card);
      border-radius: 22px;
      padding: 40px 35px;
      box-shadow: var(--shadow);
      border: 1px solid #eef2f7;
    }

    .brand-logo {
      font-weight: 800;
      font-size: 24px;
      color: var(--blue);
      text-decoration: none;
      display: inline-block;
      margin-bottom: 5px;
    }

    .login-title {
      font-size: 20px;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 25px;
    }

    label {
      font-size: 13px;
      font-weight: 600;
      color: #334155;
      margin-bottom: 6px;
    }

    .form-control {
      border-radius: 12px;
      font-size: 14px;
      padding: 12px 15px;
      border-color: #cbd5e1;
    }

    .form-control:focus {
      border-color: var(--teal);
      box-shadow: 0 0 0 4px rgba(53, 199, 182, 0.15);
    }

    .btn-login {
      width: 100%;
      padding: 13px;
      border-radius: 12px;
      border: none;
      background: linear-gradient(90deg, var(--blue), var(--teal));
      color: white;
      font-weight: 700;
      font-size: 15px;
      margin-top: 15px;
      transition: 0.25s ease;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 25px rgba(47, 109, 246, 0.25);
    }

    .back-to-home {
      display: block;
      text-align: center;
      margin-top: 25px;
      font-size: 13px;
      color: var(--muted);
      text-decoration: none;
      font-weight: 500;
      transition: 0.2s ease;
    }

    .back-to-home:hover {
      color: var(--blue);
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-card">
      
      <div class="text-center mb-4">
        <a href="#" class="brand-logo">19JutaAdmin</a>
        <p class="login-title">Portal Login Administrator</p>
      </div>

      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger py-2 px-3 mb-3 text-center" style="border-radius: 10px; font-size: 13px;">
          <?= $error_message; ?>
        </div>
      <?php endif; ?>

      <form action="halamanLoginAdmin.php" method="POST">
        
        <div class="mb-3">
          <label for="nama_admin">Nama Lengkap Admin</label>
          <input type="text" id="nama_admin" name="nama_admin" class="form-control" required autocomplete="off">
        </div>

        <div class="mb-3">
          <label for="email">Email Official Admin</label>
          <input type="email" id="email" name="email" class="form-control" required autocomplete="off">
        </div>

        <div class="mb-4">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn-login">
          Masuk ke Dashboard
        </button>

      </form>

      <a href="index.php" class="back-to-home">← Kembali ke Beranda Utama</a>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
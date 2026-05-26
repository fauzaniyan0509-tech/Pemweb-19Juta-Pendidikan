<?php
// Wajib ditaruh di baris paling atas untuk mencatat data user yang login
session_start();

// =======================================================
// 1. KONEKSI UTAMA DATABASE (TERHUBUNG KE KONEKSI.PHP)
// =======================================================
include 'koneksi.php'; 

// Jembatan variabel: karena koneksi.php pakai $conn, sedangkan 
// kode di bawah menggunakan $koneksi, kita samakan nilainya di sini
$koneksi = $conn; 


// =======================================================
// 2. LOGIKA PROSES REGISTRASI USER (OTOMATIS LOGIN)
// =======================================================
if (isset($_POST['daftar'])) {
    
    // Ambil input dan bersihkan dari karakter berbahaya
    $nama_input = $_POST['username']; 
    $nama = mysqli_real_escape_string($koneksi, $nama_input);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password_mentah = $_POST['password'];

    // Validasi data kosong
    if (empty($nama) || empty($email) || empty($password_mentah)) {
        header("Location: halamanRegistrasi.php?error=data_kosong");
        exit();
    }

    // Validasi panjang password
    if (strlen($password_mentah) < 8) {
        header("Location: halamanRegistrasi.php?error=password_pendek");
        exit();
    }

    // Cek apakah email sudah terdaftar
    $cek_email = mysqli_query($koneksi, "SELECT email FROM user WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        header("Location: halamanRegistrasi.php?error=email_terdaftar");
        exit();
    }

    // Enkripsi Password
    $password_hashed = password_hash($password_mentah, PASSWORD_DEFAULT);

    // Proses Upload Foto Profil
    $nama_foto = NULL; 
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $tmp_file = $_FILES['foto_profil']['tmp_name'];
        $nama_asli_file = $_FILES['foto_profil']['name'];
        
        $nama_foto = uniqid() . "_" . $nama_asli_file;
        $direktori_tujuan = "uploads/" . $nama_foto;

        if (!move_uploaded_file($tmp_file, $direktori_tujuan)) {
            $nama_foto = NULL; 
        }
    }

    // Simpan data ke tabel user
    $query = "INSERT INTO user (nama, email, password, foto_profil) 
              VALUES ('$nama', '$email', '$password_hashed', '$nama_foto')";
              
    $simpan = mysqli_query($koneksi, $query);

    if ($simpan) {
        // Ambil ID user yang baru saja digenerate oleh AUTO_INCREMENT database
        $id_user_baru = mysqli_insert_id($koneksi);

        // Langsung buat session agar statusnya otomatis masuk (Login)
        $_SESSION['id_user']      = $id_user_baru;
        $_SESSION['nama']         = $nama_input; 
        $_SESSION['email']        = $email;
        $_SESSION['foto_profil']  = $nama_foto;
        $_SESSION['status_login'] = "sudah_login";

        // Alihkan langsung ke halaman beranda tanpa lewat login dulu
        header("Location: beranda.php");
        exit();
        
    } else {
        echo "Gagal menyimpan data ke database: " . mysqli_error($koneksi);
    }
}


// =======================================================
// 3. LOGIKA PROSES LOGIN USER
// =======================================================
if (isset($_POST['login'])) {
    
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password_input = $_POST['password'];

    // Cari user berdasarkan email
    $query_user = mysqli_query($koneksi, "SELECT * FROM user WHERE email = '$email'");
    
    if (mysqli_num_rows($query_user) === 1) {
        $data_user = mysqli_fetch_assoc($query_user);
        
        // Memeriksa kecocokan password
        if (password_verify($password_input, $data_user['password'])) {
            
            // Sukses login, rekam identitas ke session server
            $_SESSION['id_user']      = $data_user['id_user'];
            $_SESSION['nama']         = $data_user['nama'];
            $_SESSION['email']        = $data_user['email'];
            $_SESSION['foto_profil']  = $data_user['foto_profil'];
            $_SESSION['status_login'] = "sudah_login";
            
            header("Location: beranda.php");
            exit();
        }
    }
    
    // Jika gagal login, balikkan ke halaman login membawa pesan gagal
    header("Location: halamanLogin.php?pesan=gagal");
    exit();
}


// =======================================================
// 4. ENDPOINT API UNTUK CEK NOTIFIKASI ADMIN via AJAX (DIPERBARUI)
// =======================================================
if (isset($_GET['aksi']) && $_GET['aksi'] === 'cek_notif') {
    header('Content-Type: application/json');
    
    // Query matematika SQL langsung menjumlahkan baris 'menunggu' dari kedua tabel iklan
    $sql_gabungan = "
        SELECT 
            (SELECT COUNT(*) FROM iklan_lomba WHERE status_verifikasi = 'menunggu') + 
            (SELECT COUNT(*) FROM iklan_beasiswa WHERE status_verifikasi = 'menunggu') 
        AS total";
        
    $query_notif = mysqli_query($koneksi, $sql_gabungan);
    
    if ($query_notif) {
        $data_notif = mysqli_fetch_assoc($query_notif);
        echo json_encode(['total_pending' => (int)$data_notif['total']]);
    } else {
        echo json_encode(['total_pending' => 0, 'error' => mysqli_error($koneksi)]);
    }
    exit(); 
}
?>
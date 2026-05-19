<?php
// Konfigurasi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "19juta_pendidikan"; // Pastikan nama ini sama persis dengan yang ada di phpMyAdmin

// Membuat Koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek Koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Opsional: Set karakter agar tidak berantakan saat menampilkan data (UTF-8)
mysqli_set_charset($conn, "utf8mb4");
?>
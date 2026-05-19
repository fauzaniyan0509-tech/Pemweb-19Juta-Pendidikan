<?php
// 1. KONEKSI KE DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "19juta_pendidikan"; // Pastikan nama database sudah sesuai

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// 2. PROSES KETIK FORM DI-SUBMIT DENGAN METHOD POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Tampung semua inputan form data teks
    $paket_langganan   = $_POST['paket_langganan'];
    $jumlah_bayar      = $_POST['jumlah']; 
    $judul_lomba       = $_POST['judul_lomba'];
    $penyelenggara     = $_POST['penyelenggara'];
    $kategori          = $_POST['kategori'];
    $tingkat_lomba     = $_POST['tingkat_lomba'];
    $deadline          = $_POST['deadline'];
    $tipe_biaya        = $_POST['tipe_biaya'];
    $biaya_pendaftaran = $_POST['biaya']; 
    $deskripsi         = $_POST['deskripsi'];
    $judul_iklan       = $_POST['judul_iklan'];
    $metode_pembayaran = $_POST['metode_pembayaran'];

    // Sesi User Dummy (Sesuaikan dengan sistem login/session aplikasi 19JutaPendidikan kamu)
    $id_user_aktif     = 1; 

    // PROSES INTEGRASI FILE GAMBAR BUKTI PEMBAYARAN
    $bukti_pembayaran = $_FILES['bukti_pembayaran']['name'];
    $tmp_name         = $_FILES['bukti_pembayaran']['tmp_name'];
    
    // Potong ekstensi file asli (.png / .jpg)
    $ekstensi          = pathinfo($bukti_pembayaran, PATHINFO_EXTENSION);
    // Enskripsi nama file baru agar unik memanfaatkan fungsi waktu timestamp
    $nama_file_baru    = "bukti_" . time() . "_" . rand(100, 999) . "." . $ekstensi;
    $folder_upload     = "uploads/";

    // Bikin folder uploads otomatis kalau belum tersedia di direktori htdocs
    if (!is_dir($folder_upload)) {
        mkdir($folder_upload, 0777, true);
    }

    // Pindahkan file dari folder temporary local server ke folder uploads utama
    if (move_uploaded_file($tmp_name, $folder_upload . $nama_file_baru)) {
        
        // [LANGKAH A]: INSERT DATA KE TABEL 'lomba' (Sesuai Struktur Alter Kolom Kamu)
        $query_lomba = "INSERT INTO lomba (judul_lomba, penyelenggara, kategori, tingkat_lomba, deadline, tipe_biaya, biaya, deskripsi, status_publish) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt_lomba = $conn->prepare($query_lomba);
        $stmt_lomba->bind_param("ssssssis", $judul_lomba, $penyelenggara, $kategori, $tingkat_lomba, $deadline, $tipe_biaya, $biaya_pendaftaran, $deskripsi);
        $stmt_lomba->execute();
        
        // Dapatkan ID auto_increment yang baru saja tercipta dari tabel lomba
        $id_lomba_baru = $conn->insert_id; 
        $stmt_lomba->close();


        // [LANGKAH B]: INSERT DATA KE TABEL 'pembayaran' (Termasuk ID User & Bukti Pembayaran Baru)
        $query_pembayaran = "INSERT INTO pembayaran (id_user, jumlah, metode_pembayaran, bukti_pembayaran, status_pembayaran) 
                             VALUES (?, ?, ?, ?, 'pending')";
        $stmt_pembayaran = $conn->prepare($query_pembayaran);
        $stmt_pembayaran->bind_param("iiss", $id_user_aktif, $jumlah_bayar, $metode_pembayaran, $nama_file_baru);
        $stmt_pembayaran->execute();
        
        // Dapatkan ID auto_increment yang baru saja tercipta dari tabel pembayaran
        $id_pembayaran_baru = $conn->insert_id; 
        $stmt_pembayaran->close();


        // [LANGKAH C]: INTEGRASIKAN KEDUA ID KEDALAM TABEL UTAMA 'iklan_lomba'
        $status_awal = "menunggu"; // Menggunakan ENUM 'menunggu' sesuai rancangan tabel iklan_lomba milikmu
        $query_iklan = "INSERT INTO iklan_lomba (id_user, id_lomba, id_pembayaran, judul_iklan, paket_langganan, status_verifikasi, is_read) 
                        VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt_iklan = $conn->prepare($query_iklan);
        $stmt_iklan->bind_param("iiisss", $id_user_aktif, $id_lomba_baru, $id_pembayaran_baru, $judul_iklan, $paket_langganan, $status_awal);
        
        if ($stmt_iklan->execute()) {
            $stmt_iklan->close();
            mysqli_close($conn);
            
            // Pengalihan otomatis (Redirect) langsung ke halaman_lomba.php dengan membawa parameter status sukses
            header("Location: halamanLomba.php?status=sukses");
            exit();
        } else {
            echo "Gagal menyimpan relasi data ke tabel iklan_lomba: " . $conn->error;
        }

    } else {
        echo "Gagal memindahkan file gambar bukti pendaftaran ke direktori server.";
    }
}

// Tutup Koneksi Database jika lolos dari kondisi POST
mysqli_close($conn);
?>
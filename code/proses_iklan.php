<?php
// 1. KONEKSI KE DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "19juta_pendidikan"; 

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

    // Sesi User Dummy
    $id_user_aktif     = 1; 

    // Folder tujuan upload
    $folder_upload     = "uploads/";
    if (!is_dir($folder_upload)) {
        mkdir($folder_upload, 0777, true);
    }

    // --- PROSES FILE 1: BUKTI PEMBAYARAN ---
    $bukti_pembayaran = $_FILES['bukti_pembayaran']['name'];
    $tmp_bukti        = $_FILES['bukti_pembayaran']['tmp_name'];
    $ekstensi_bukti   = pathinfo($bukti_pembayaran, PATHINFO_EXTENSION);
    $nama_bukti_baru  = "bukti_" . time() . "_" . rand(100, 999) . "." . $ekstensi_bukti;

    // --- PROSES FILE 2: POSTER LOMBA ---
    $poster_lomba     = $_FILES['poster']['name'];
    $tmp_poster       = $_FILES['poster']['tmp_name'];
    $ekstensi_poster  = pathinfo($poster_lomba, PATHINFO_EXTENSION);
    $nama_poster_baru = "poster_" . time() . "_" . rand(100, 999) . "." . $ekstensi_poster;

    // Pindahkan KEDUA file dari temporary ke folder uploads
    $upload_bukti_sukses  = move_uploaded_file($tmp_bukti, $folder_upload . $nama_bukti_baru);
    $upload_poster_sukses = move_uploaded_file($tmp_poster, $folder_upload . $nama_poster_baru);

    if ($upload_bukti_sukses && $upload_poster_sukses) {
        
        // [LANGKAH A]: INSERT DATA KE TABEL 'lomba' (Ditambah kolom poster)
        $query_lomba = "INSERT INTO lomba (judul_lomba, penyelenggara, kategori, tingkat_lomba, deadline, tipe_biaya, biaya, deskripsi, poster, status_publish) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt_lomba = $conn->prepare($query_lomba);
        
        // Perhatikan tambahan 's' di parameter pertama dan variabel $nama_poster_baru di akhir
        $stmt_lomba->bind_param("ssssssiss", $judul_lomba, $penyelenggara, $kategori, $tingkat_lomba, $deadline, $tipe_biaya, $biaya_pendaftaran, $deskripsi, $nama_poster_baru);
        $stmt_lomba->execute();
        
        $id_lomba_baru = $conn->insert_id; 
        $stmt_lomba->close();


        // [LANGKAH B]: INSERT DATA KE TABEL 'pembayaran'
        $query_pembayaran = "INSERT INTO pembayaran (id_user, jumlah, metode_pembayaran, bukti_pembayaran, status_pembayaran) 
                             VALUES (?, ?, ?, ?, 'pending')";
        $stmt_pembayaran = $conn->prepare($query_pembayaran);
        $stmt_pembayaran->bind_param("iiss", $id_user_aktif, $jumlah_bayar, $metode_pembayaran, $nama_bukti_baru);
        $stmt_pembayaran->execute();
        
        $id_pembayaran_baru = $conn->insert_id; 
        $stmt_pembayaran->close();


        // [LANGKAH C]: INTEGRASIKAN KEDUA ID KEDALAM TABEL UTAMA 'iklan_lomba'
        $status_awal = "menunggu"; 
        $query_iklan = "INSERT INTO iklan_lomba (id_user, id_lomba, id_pembayaran, judul_iklan, paket_langganan, status_verifikasi, is_read) 
                        VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt_iklan = $conn->prepare($query_iklan);
        $stmt_iklan->bind_param("iiisss", $id_user_aktif, $id_lomba_baru, $id_pembayaran_baru, $judul_iklan, $paket_langganan, $status_awal);
        
        if ($stmt_iklan->execute()) {
            $stmt_iklan->close();
            mysqli_close($conn);
            
            // Redirect jika sukses
            header("Location: halamanLomba.php?status=sukses");
            exit();
        } else {
            echo "Gagal menyimpan relasi data ke tabel iklan_lomba: " . $conn->error;
        }

    } else {
        echo "Gagal mengunggah file gambar (poster atau bukti pembayaran) ke direktori server. Pastikan ukuran file tidak melebihi batas dan folder 'uploads' memiliki izin tulis (write permission).";
    }
}

// Tutup Koneksi Database jika lolos dari kondisi POST
mysqli_close($conn);
?>
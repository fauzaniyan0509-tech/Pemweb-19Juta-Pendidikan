<?php
// 1. WAJIB JALANKAN SESSION DI BARIS PERTAMA
// Supaya file ini bisa membaca siapa user yang sedang login
session_start();

// 2. PROTEKSI: Jika user belum login tapi nekat buka halaman ini, tendang ke halaman login
if (!isset($_SESSION['id_user'])) {
    header("Location: halamanLogin.php?pesan=belum_login");
    exit();
}

// 3. GUNAKAN FILE KONEKSI YANG SUDAH KAMU BUAT
// Kita pakai koneksi.php karena menggunakan variabel $conn (cocok dengan kode prepared statement di bawah)
include 'koneksi.php'; 

// 4. AKTIFKAN ERROR REPORTING (Untuk memunculkan eror jika database menolak data)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 5. AMBIL ID ASLI USER DARI SESSION (Bukan angka dummy 1 lagi)
    $id_user_aktif = $_SESSION['id_user']; 

    // Ambil data umum dari form transaksi
    $jenis_iklan       = isset($_POST['nama_beasiswa']) ? 'beasiswa' : 'lomba';
    $paket_langganan   = $_POST['paket_langganan'];
    $jumlah_bayar      = $_POST['jumlah']; 
    $judul_iklan       = $_POST['judul_iklan'];
    $metode_pembayaran = $_POST['metode_pembayaran'];

    // Siapkan folder upload gambar
    $folder_upload = "uploads/";
    if (!is_dir($folder_upload)) {
        mkdir($folder_upload, 0777, true);
    }

    // Penamaan file gambar secara unik biar tidak saling menimpa
    $nama_bukti_baru  = "bukti_" . time() . "_" . rand(100, 999) . "." . pathinfo($_FILES['bukti_pembayaran']['name'], PATHINFO_EXTENSION);
    $nama_poster_baru = "poster_" . time() . "_" . rand(100, 999) . "." . pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);

    // Proses pindah file dari browser ke folder server
    $upload_bukti_sukses  = move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $folder_upload . $nama_bukti_baru);
    $upload_poster_sukses = move_uploaded_file($_FILES['poster']['tmp_name'], $folder_upload . $nama_poster_baru);

    if ($upload_bukti_sukses && $upload_poster_sukses) {
        
        $id_item_baru = 0;

        // =========================================================
        // [LANGKAH 1]: INPUT KE TABEL UTAMA (BEASISWA / LOMBA)
        // =========================================================
        if ($jenis_iklan == 'lomba') {
            $judul_lomba       = $_POST['judul_lomba'];
            $penyelenggara     = $_POST['penyelenggara'];
            $kategori          = $_POST['kategori'];
            $tingkat_lomba     = $_POST['tingkat_lomba'];
            $deadline          = $_POST['deadline'];
            $tipe_biaya        = $_POST['tipe_biaya'];
            $biaya_pendaftaran = $_POST['biaya']; 
            $deskripsi         = $_POST['deskripsi'];

            $query_lomba = "INSERT INTO lomba (judul_lomba, penyelenggara, kategori, tingkat_lomba, deadline, tipe_biaya, biaya, deskripsi, poster, status_publish) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $stmt_lomba = $conn->prepare($query_lomba);
            $stmt_lomba->bind_param("ssssssiss", $judul_lomba, $penyelenggara, $kategori, $tingkat_lomba, $deadline, $tipe_biaya, $biaya_pendaftaran, $deskripsi, $nama_poster_baru);
            
            if (!$stmt_lomba->execute()) {
                die("❌ GAGAL PADA TABEL LOMBA: " . $stmt_lomba->error);
            }
            $id_item_baru = $conn->insert_id; 
            $stmt_lomba->close();

        } else {
            $nama_beasiswa    = $_POST['nama_beasiswa'];
            $penyelenggara    = $_POST['penyelenggara'];
            $jenjang          = $_POST['jenjang'];
            $tingkat_beasiswa = $_POST['tingkat_beasiswa'];
            $deadline         = $_POST['deadline'];
            $tipe_pendanaan   = $_POST['tipe_pendanaan'];
            $deskripsi        = $_POST['deskripsi'];

            $query_beasiswa = "INSERT INTO beasiswa (nama_beasiswa, penyelenggara, jenjang, tingkat_beasiswa, deskripsi, poster, deadline, tipe_pendanaan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_beasiswa = $conn->prepare($query_beasiswa);
            $stmt_beasiswa->bind_param("ssssssss", $nama_beasiswa, $penyelenggara, $jenjang, $tingkat_beasiswa, $deskripsi, $nama_poster_baru, $deadline, $tipe_pendanaan);
            
            if (!$stmt_beasiswa->execute()) {
                die("❌ GAGAL PADA TABEL BEASISWA: " . $stmt_beasiswa->error);
            }
            $id_item_baru = $conn->insert_id; 
            $stmt_beasiswa->close();
        }

        // =========================================================
        // [LANGKAH 2]: INPUT KE TABEL PEMBAYARAN
        // =========================================================
        $query_pembayaran = "INSERT INTO pembayaran (id_user, jumlah, metode_pembayaran, bukti_pembayaran, status_pembayaran) VALUES (?, ?, ?, ?, 'pending')";
        $stmt_pembayaran = $conn->prepare($query_pembayaran);
        $stmt_pembayaran->bind_param("iiss", $id_user_aktif, $jumlah_bayar, $metode_pembayaran, $nama_bukti_baru);
        
        if (!$stmt_pembayaran->execute()) {
            die("❌ GAGAL PADA TABEL PEMBAYARAN: " . $stmt_pembayaran->error);
        }
        $id_pembayaran_baru = $conn->insert_id; 
        $stmt_pembayaran->close();

        // =========================================================
        // [LANGKAH 3]: INPUT KE TABEL RELASI IKLAN
        // =========================================================
        if ($jenis_iklan == 'lomba') {
            $query_iklan = "INSERT INTO iklan_lomba (id_lomba, id_pembayaran, id_user, judul_iklan, status_verifikasi, paket_langganan, is_read) VALUES (?, ?, ?, ?, 'menunggu', ?, 0)";
            $stmt_iklan = $conn->prepare($query_iklan);
            $stmt_iklan->bind_param("iiiss", $id_item_baru, $id_pembayaran_baru, $id_user_aktif, $judul_iklan, $paket_langganan);
            
            if (!$stmt_iklan->execute()) {
                die("❌ GAGAL PADA TABEL IKLAN_LOMBA: " . $stmt_iklan->error);
            }
            $stmt_iklan->close();
            mysqli_close($conn);
            
            header("Location: halamanLomba.php?status=sukses");
            exit();

        } else {
            $query_iklan = "INSERT INTO iklan_beasiswa (id_beasiswa, id_pembayaran, id_user, judul_iklan, status_verifikasi, paket_langganan, is_read) VALUES (?, ?, ?, ?, 'menunggu', ?, 0)";
            $stmt_iklan = $conn->prepare($query_iklan);
            $stmt_iklan->bind_param("iiiss", $id_item_baru, $id_pembayaran_baru, $id_user_aktif, $judul_iklan, $paket_langganan);
            
            if (!$stmt_iklan->execute()) {
                die("❌ GAGAL PADA TABEL IKLAN_BEASISWA: " . $stmt_iklan->error);
            }
            $stmt_iklan->close();
            mysqli_close($conn);
            
            header("Location: halamanBeasiswa.php?status=sukses");
            exit();
        }

    } else {
        die("❌ Gagal mengunggah file gambar ke dalam folder server.");
    }
}
mysqli_close($conn);
?>
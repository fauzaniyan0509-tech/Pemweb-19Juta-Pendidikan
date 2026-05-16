<?php
// Set timezone agar waktu sesuai dengan lokasi user (WIB)
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $paket = $_POST['paket'];
    $tanggal_sekarang = (int)date('d'); // Mengambil tanggal hari ini (1-31)
    $harga_akhir = 0;
    $pesan_promo = "";

    if ($paket == "publikasi") {
        $harga_akhir = 50000;
        $nama_paket = "Paket per Publikasi";
    } 
    elseif ($paket == "tahunan") {
        $harga_dasar = 600000;
        $nama_paket = "Paket Langganan Tahunan";

        // Cek apakah hari ini tanggal 19
        if ($tanggal_sekarang === 19) {
            $diskon = 101000;
            $harga_akhir = $harga_dasar - $diskon;
            $pesan_promo = "🎉 Selamat! Kamu dapat potongan 'Spesial 19JutaPendidikan' sebesar Rp 101.000!";
        } else {
            $harga_akhir = $harga_dasar;
            $pesan_promo = "Info: Promo potongan Rp 101rb hanya berlaku setiap tanggal 19.";
        }
    }

    // Tampilkan Hasil
    echo "<h2>Konfirmasi Transaksi</h2>";
    echo "<p>Paket yang dipilih: <strong>$nama_paket</strong></p>";
    echo "<p>Tanggal transaksi: " . date('d F Y') . "</p>";
    
    if (!empty($pesan_promo)) {
        echo "<p style='color: green;'>$pesan_promo</p>";
    }

    echo "<h3>Total yang harus dibayar: Rp " . number_format($harga_akhir, 0, ',', '.') . "</h3>";
    echo "<br><a href='halamanTransaksi.html'>Kembali</a>";
}
?>
<?php
// ============================================================
// PANDUAN PATCH: proses_iklan.php
// ============================================================
// File proses_iklan.php tidak dilampirkan, sehingga tidak dapat
// diedit langsung. Namun perubahan yang diperlukan SANGAT KECIL.
//
// Cari bagian INSERT INTO lomba pada file proses_iklan.php Anda,
// kemudian tambahkan dua field berikut:
//
// ---- SEBELUM (contoh perkiraan kode lama) ----
//
//   $query = "INSERT INTO lomba 
//               (judul_lomba, penyelenggara, kategori, tingkat_lomba,
//                deadline, tipe_biaya, biaya, deskripsi, poster, status_publish)
//             VALUES 
//               ('$judul', '$penyelenggara', '$kategori', '$tingkat',
//                '$deadline', '$tipe_biaya', '$biaya', '$deskripsi', '$poster', 'pending')";
//
// ---- SESUDAH (tambahkan persyaratan dan kontak_pengaju) ----
//
//   // Tangkap field baru dari form
//   $persyaratan    = mysqli_real_escape_string($conn, $_POST['persyaratan'] ?? '');
//   $kontak_pengaju = mysqli_real_escape_string($conn, $_POST['kontak_pengaju'] ?? '');
//
//   $query = "INSERT INTO lomba 
//               (judul_lomba, penyelenggara, kategori, tingkat_lomba,
//                deadline, tipe_biaya, biaya, deskripsi, persyaratan,
//                kontak_pengaju, poster, status_publish)
//             VALUES 
//               ('$judul', '$penyelenggara', '$kategori', '$tingkat',
//                '$deadline', '$tipe_biaya', '$biaya', '$deskripsi', '$persyaratan',
//                '$kontak_pengaju', '$poster', 'pending')";
//
// ============================================================
// RINGKASAN: hanya 2 langkah:
// 1. Tambahkan 2 baris tangkap variabel ($persyaratan, $kontak_pengaju)
// 2. Tambahkan nama kolom dan value-nya di query INSERT INTO lomba
// ============================================================
?>

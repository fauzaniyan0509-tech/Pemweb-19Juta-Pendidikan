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
    <!-- firstname -->
    <?php
    // Cek apakah data sudah dikirim dan tidak kosong
    if (isset($_POST['firstname']) && !empty(trim($_POST['firstname']))) {
        $firstname = htmlspecialchars($_POST['firstname']); // Gunakan htmlspecialchars agar aman dari XSS
        echo "<p>Nama Depan : $firstname </p>";
    } else {
        // Jika kosong, beri peringatan atau tendang balik ke halaman form
        echo "<p style='color:red;'>Waduh! Nama depan wajib diisi, jangan dikosongin ya.</p>";
        echo "<a href='halamanLogin.html'>Kembali ke Form</a>";
    }
    ?>

    <!-- lastname -->
    <?php
    // Cek apakah data sudah dikirim dan tidak kosong
    if (isset($_POST['lastname']) && !empty(trim($_POST['lastname']))) {
        $lastname = htmlspecialchars($_POST['lastname']); // Gunakan htmlspecialchars agar aman dari XSS
        echo "<p>Nama Belakang : $lastname </p>";
    } else {
        // Jika kosong, beri peringatan atau tendang balik ke halaman form
        echo "<p style='color:red;'>Waduh! Nama belakang wajib diisi, jangan dikosongin ya.</p>";
        echo "<a href='halamanLogin.html'>Kembali ke Form</a>";
    }
    ?>

    <!-- email -->
    <?php
    // Cek apakah data sudah dikirim dan tidak kosong
    if (isset($_POST['email']) && !empty(trim($_POST['email']))) {
        $email = htmlspecialchars($_POST['email']); // Gunakan htmlspecialchars agar aman dari XSS
        echo "<p>Email : $email </p>";
    } else {
        // Jika kosong, beri peringatan atau tendang balik ke halaman form
        echo "<p style='color:red;'>Waduh! email wajib diisi, jangan dikosongin ya.</p>";
        echo "<a href='halamanLogin.html'>Kembali ke Form</a>";
    }
    ?>

    <!-- password -->
    <?php
    if (isset($_POST['password'])) {
        $password = $_POST['password']; // Jangan di-trim jika spasi dianggap karakter valid, tapi biasanya di-trim
        $errors = [];

        // 1. Cek Minimal 8 Karakter
        if (strlen($password) < 8) {
            $errors[] = "Password minimal harus 8 karakter.";
        }

        // 2. Cek Huruf Besar (A-Z)
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password wajib punya minimal satu huruf BESAR.";
        }

        // 3. Cek Huruf Kecil (a-z)
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password wajib punya minimal satu huruf kecil.";
        }

        // 4. Cek Angka (0-9)
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password wajib punya minimal satu angka.";
        }

        // --- HASIL VALIDASI ---
        if (empty($errors)) {
            // Jika tidak ada error, lanjut proses (Misal: Hash password)
            echo "<p>Password : $password </p>";
            echo "<p style='color:green;'>Sip! Password kamu sudah memenuhi syarat keamanan.</p>";
            // Catatan: Demi keamanan, JANGAN PERNAH echo password asli ke layar ya!
        } else {
            // Jika ada error, tampilkan semua pesannya
            echo "<div style='color:red;'>";
            echo "<strong>Waduh, password kamu belum aman:</strong><ul>";
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul></div>";
            echo "<a href='halamanLogin.html'>Coba lagi</a>";
        }
    }
    ?>



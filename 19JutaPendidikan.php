<?php

// ================= USER =================
class User {
    public $idUser;
    public $nama;
    public $email;
    public $password;

    function login() {
        echo "User berhasil login <br>";
    }

    function register() {
        echo "User berhasil register <br>";
    }

    function editProfile() {
        echo "Profil berhasil diubah <br>";
    }

}

// ================= LOMBA =================
class Lomba {
    public $idLomba;
    public $judul;
    public $deadline;

    function tampilkanLomba() {
        echo "Menampilkan daftar lomba <br>";
    }

}


// ================= BEASISWA =================
class Beasiswa {
    public $idBeasiswa;
    public $namaBeasiswa;

    function tampilkanBeasiswa() {
        echo "Menampilkan beasiswa <br>";
    }

}


// ================= TEMPAT EDUKATIF =================
class TempatEdukatif {
    public $idTempat;
    public $namaTempat;

    function tampilkanTempat() {
        echo "Menampilkan tempat edukatif <br>";
    }

}


// ================= PEMBAYARAN =================
class Pembayaran {
    public $idPembayaran;
    public $jumlah;

    function prosesPembayaran() {
        echo "Memproses pembayaran <br>";
    }


}


// ================= ADMIN =================
class Admin {
    public $idAdmin;
    public $nama;

    function kelolaData() {
        echo "Admin mengelola lomba <br>";
    }

}

?>
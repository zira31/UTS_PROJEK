<?php
$host = "localhost";   // biasanya tetap localhost
$user = "root";        // default XAMPP username
$pass = "";            // kosong kalau kamu tidak pakai password MySQL
$db   = "db_users";    // nama database kamu di phpMyAdmin

// Ubah dari new mysqli() jadi mysqli_connect()
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>

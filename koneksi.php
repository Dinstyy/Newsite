<?php
// koneksi.php

$host = 'localhost'; // Sesuaikan dengan host database Anda
$user = 'root'; // Sesuaikan dengan username database Anda
$password = 'users'; // Sesuaikan dengan password database Anda
$database = 'newsite'; // Sesuaikan dengan nama database Anda

$koneksi = mysqli_connect($host, $user, $password, $database);

// Periksa koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>

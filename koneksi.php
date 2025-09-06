<?php
$host = "localhost";
$user = "root"; // default XAMPP
$pass = "";     // default kosong
$db   = "toko_ponsel"; // pastikan sama dengan database di phpMyAdmin

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>

<?php
$host = "148.222.53.10";
$user = "u510831173_danni";
$pass = "Danni345!";
$db   = "u510831173_danni"; // Sesuaikan nama database kamu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>
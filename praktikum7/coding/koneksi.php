<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "foto"; 
$port = "8111";

$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>

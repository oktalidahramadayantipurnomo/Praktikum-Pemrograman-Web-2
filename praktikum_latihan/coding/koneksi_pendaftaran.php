<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pendaftaran_sekolah";
$port = "8111";

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

?>

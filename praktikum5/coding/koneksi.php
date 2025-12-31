<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'myweb';
$port = 8111;

$cnn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$cnn) {
    exit('Koneksi Gagal: ' . mysqli_connect_error());
}

?>

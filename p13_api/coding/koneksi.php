<?php
$host = "localhost";          
$user = "root";               
$pass = "";                   
$db   = "koperasidb"; 
$port = "8111";              

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>

<?php
session_start();
include "koneksi_pendaftaran.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' LIMIT 1");
$data = mysqli_fetch_assoc($query);

if ($data) {

    if ($password == $data['password']) {

        $_SESSION['login'] = true;
        $_SESSION['username'] = $data['username'];

        header("Location: index_pendaftaran.php");
        exit();

    } else {
        header("Location: login.php?error=Password salah");
        exit();
    }

} else {
    header("Location: login.php?error=Username tidak ditemukan");
    exit();
}
?>

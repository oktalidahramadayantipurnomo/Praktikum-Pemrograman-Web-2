<?php
$host = 'localhost';    
$user = 'root';         
$password = '';         
$database = 'latihan3_praktikum4';   
$port = 8111;           

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

$sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
    echo "Data berhasil disimpan!<br>";
    echo "<a href='registrasi.php'> Tambah User Baru </a>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

//Menutup koneksi
$conn->close();
?>
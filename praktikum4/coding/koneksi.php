<?php
// Konfigurasi database
$host = 'localhost'; 	//Host
$user = 'root'; 		//Username MySQL
$password = '';			//Password MySQL
$database = 'market';	//Nama database
$port = 8111;			//Port

//Membuat koneksi
$conn = new mysqli($host, $user, $password, $database, $port);

// Cek Koneksi
if ($conn->connect_error) {
	die("Koneksi gagal: " . $conn->connect_error);
}

// Mengambil data dari form
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

//Query untuk menyimpan data ke tabel users
$sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
	echo "Data berhasil disimpan!<br>";
	echo "<a href='Form_users.html'> Tambah User Baru </a>";
} else {
	echo "Error: " . $sql . "<br>" . $conn->error;
}

//Menutup koneksi
$conn->close();
?>
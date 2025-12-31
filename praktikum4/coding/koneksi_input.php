<?php
$host = 'localhost';       
$user = 'root';            
$password = '';            
$database = 'latihan2_praktikum4'; 
$port = 8111;             

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$name    = $_POST['name'];
$email   = $_POST['email'];
$website = $_POST['website'];
$comment = $_POST['comment'];
$gender  = $_POST['gender'];

$sql = "INSERT INTO form_validation (name, email, website, comment, gender) 
        VALUES ('$name', '$email', '$website', '$comment', '$gender')";

if ($conn->query($sql) === TRUE) {
    echo "<h2>Your Input:</h2>";
    echo "Name: " . htmlspecialchars($name) . "<br>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Website: " . htmlspecialchars($website) . "<br>";
    echo "Comment: " . htmlspecialchars($comment) . "<br>";
    echo "Gender: " . htmlspecialchars($gender) . "<br><br>";
    echo "<b>Data berhasil disimpan ke database!</b><br>";
    echo "<a href='form_input.php'>Kembali ke Form</a>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

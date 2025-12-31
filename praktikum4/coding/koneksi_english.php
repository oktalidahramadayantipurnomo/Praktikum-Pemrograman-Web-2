<?php
$host = 'localhost';    
$user = 'root';         
$password = '';         
$database = 'latihan4_praktikum4';   
$port = 8111;           

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $postal_code = $_POST['postal_code'];
    $telephone = $_POST['telephone'];
    $birth_place_date = $_POST['birth_place_date'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $attended_school = $_POST['attended_school'];

    $sql = "INSERT INTO registration 
            (full_name, address, postal_code, telephone, birth_place_date, gender, religion, attended_school)
            VALUES ('$full_name', '$address', '$postal_code', '$telephone', '$birth_place_date', '$gender', '$religion', '$attended_school')";

    if ($conn->query($sql) === TRUE) {
        echo "<h2>Data berhasil disimpan!</h2>";
        echo "<p><b>Nama Lengkap:</b> $full_name</p>";
        echo "<p><b>Alamat:</b> $address ($postal_code)</p>";
        echo "<p><b>No. Telepon:</b> $telephone</p>";
        echo "<p><b>Tempat/Tgl Lahir:</b> $birth_place_date</p>";
        echo "<p><b>Jenis Kelamin:</b> $gender</p>";
        echo "<p><b>Agama:</b> $religion</p>";
        echo "<p><b>Sekolah Asal:</b> $attended_school</p>";
        echo '<br><a href="form_english.php">← Kembali ke Form</a>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

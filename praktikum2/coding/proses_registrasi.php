<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = htmlspecialchars($_POST["nama"]);
    $nim = htmlspecialchars($_POST["nim"]);
    $jurusan = htmlspecialchars($_POST["jurusan"]);
    $email = htmlspecialchars($_POST["email"]);

    // Simpan data ke file
    $data = "$nama,$nim,$jurusan,$email\n";
    file_put_contents("data_mahasiswa.txt", $data, FILE_APPEND);
} else {
    header("Location: form_registrasi.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hasil Registrasi</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            background-color: #eef3f7;
        }
        .container {
            width: 400px;
            background: white;
            padding: 20px;
            margin: 80px auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            text-align: center;
        }
        h2 {
            color: #007bff;
            font-family: "Times New Roman", Times, serif;
        }
        p {
            text-align: left;
            margin: 5px 0;
            font-size: 16px;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 8px 12px;
            border-radius: 5px;
            font-family: "Times New Roman", Times, serif;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Registrasi Berhasil!</h2>
    <p><strong>Nama:</strong> <?= $nama ?></p>
    <p><strong>NIM:</strong> <?= $nim ?></p>
    <p><strong>Jurusan:</strong> <?= $jurusan ?></p>
    <p><strong>Email:</strong> <?= $email ?></p>
    <a href="form_registrasi.php">Kembali ke Form</a>
</div>

</body>
</html>

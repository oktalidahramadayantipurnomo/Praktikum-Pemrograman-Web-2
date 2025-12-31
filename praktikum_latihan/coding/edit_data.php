<?php
include "protect.php";
include "koneksi_pendaftaran.php";

$id = $_GET['id'];
$q = mysqli_query($conn, "SELECT * FROM pendaftar WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $nama = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $tgl = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $program = $_POST['program_dipilih'];

    $fotoBaru = $_FILES['foto']['name'];
    $fotoLama = $_POST['foto_lama'];

    if ($fotoBaru != "") {

        $allowed_ext = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($fotoBaru, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            echo "<script>alert('Format foto tidak valid! Harus JPG/PNG');history.back();</script>";
            exit;
        }

        $namaFileBaru = time() . "_" . $fotoBaru;

        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $namaFileBaru);

        if ($fotoLama != "" && file_exists("uploads/" . $fotoLama)) {
            unlink("uploads/" . $fotoLama);
        }

        $query = "UPDATE pendaftar SET
                    nama_lengkap='$nama',
                    email='$email',
                    tanggal_lahir='$tgl',
                    alamat='$alamat',
                    program_dipilih='$program',
                    foto='$namaFileBaru'
                  WHERE id='$id'";
    } else {

        $query = "UPDATE pendaftar SET
                    nama_lengkap='$nama',
                    email='$email',
                    tanggal_lahir='$tgl',
                    alamat='$alamat',
                    program_dipilih='$program'
                  WHERE id='$id'";
    }

    mysqli_query($conn, $query);

    header("Location: index_pendaftaran.php?update=success");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pendaftar</title>

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #eef2f7;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
            border-left: 5px solid #4a90e2;
            padding-left: 10px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        input[type="text"], input[type="email"], input[type="date"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 8px;
            margin-top: 5px;
        }
        input[type="file"] {
            margin-top: 8px;
        }
        .foto-preview {
            margin-top: 10px;
        }
        img {
            width: 120px;
            border-radius: 10px;
            border: 2px solid #ddd;
        }
        .btn {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            background: #4a90e2;
            color: white;
            border-radius: 7px;
            cursor: pointer;
        }
        .back-btn {
            margin-top: 10px;
            padding: 10px 15px;
            font-size: 17px;
            border: none;
            background: #4d5c71ff;
            color: white;
            border-radius: 7px;
            cursor: pointer;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Edit Data Pendaftar</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">
        <input type="hidden" name="foto_lama" value="<?= $data['foto'] ?>">

        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" value="<?= $data['nama_lengkap'] ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= $data['email'] ?>" required>

        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" value="<?= $data['tanggal_lahir'] ?>" required>

        <label>Alamat</label>
        <input type="text" name="alamat" value="<?= $data['alamat'] ?>" required>

        <label>Program Dipilih</label>
        <select name="program_dipilih" required>
            <option value="AKL" <?= $data['program_dipilih']=="AKL"?"selected":"" ?>>AKL</option>
            <option value="OTKP" <?= $data['program_dipilih']=="OTKP"?"selected":"" ?>>OTKP</option>
            <option value="TKJ" <?= $data['program_dipilih']=="TKJ"?"selected":"" ?>>TKJ</option>
            <option value="BDP" <?= $data['program_dipilih']=="BDP"?"selected":"" ?>>BDP</option>
            <option value="RPL" <?= $data['program_dipilih']=="RPL"?"selected":"" ?>>RPL</option>
        </select>

        <label>Foto Saat Ini</label>
        <div class="foto-preview">
            <?php if ($data['foto'] != "") { ?>
                <img src="uploads/<?= $data['foto'] ?>" alt="Foto Pendaftar">
            <?php } else { ?>
                <p><i>Tidak ada foto</i></p>
            <?php } ?>
        </div>

        <label>Ganti Foto (opsional)</label>
        <input type="file" name="foto" accept="image/*">

        <br>
        <button type="submit" class="btn">Update Data</button>
        <a href="index_pendaftaran.php" class="btn back-btn">Kembali</a>
    </form>

</div>

</body>
</html>

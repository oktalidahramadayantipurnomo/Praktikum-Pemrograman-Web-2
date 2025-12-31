<?php
include "protect.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pendaftar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            color: #333;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        input[type="file"] {
            margin-top: 8px;
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

        .btn:hover {
            background: #357acb;
        }

        .back-btn {
            background: #555;
            margin-right: 10px;
            font-size: 17px;
        }

        .back-btn:hover {
            background: #333;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Tambah Data Pendaftar</h2>

    <form action="proses_pendaftaran.php" method="POST" enctype="multipart/form-data">

        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" required>

        <label>Alamat</label>
        <input type="text" name="alamat" required>

        <label>Program Dipilih</label>
        <select name="program_dipilih" required>
            <option value="">-- Pilih Program --</option>
            <option value="AKL">Akuntansi Keuangan dan Lembaga</option>
            <option value="OTKP">Otomatisasi Perkantoran</option>
            <option value="TKJ">Teknik Komputer dan Jaringan</option>
            <option value="BDP">Bisnis Daring & Pemasaran</option>
            <option value="RPL">Rekayasa Perangkat Lunak</option>
        </select>

        <label>Foto</label>
        <input type="file" name="foto" accept="image/*">

        <button type="submit" class="btn">Simpan</button>
        <a href="index_pendaftaran.php" class="btn back-btn">Kembali</a>

    </form>
</div>

</body>
</html>

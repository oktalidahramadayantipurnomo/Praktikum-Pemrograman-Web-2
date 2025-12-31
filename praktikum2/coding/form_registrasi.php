<!DOCTYPE html>
<html>
<head>
    <title>Form Registrasi Mahasiswa Baru</title>
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
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-family: "Times New Roman", Times, serif;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        h2 {
            text-align: center;
            color: #007bff;
            font-family: "Times New Roman", Times, serif;
        }
        label {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Form Registrasi Mahasiswa Baru</h2>
    <form action="proses_registrasi.php" method="POST">
        <label>Nama Lengkap:</label>
        <input type="text" name="nama" required>

        <label>NIM:</label>
        <input type="text" name="nim" required>

        <label>Jurusan:</label>
        <select name="jurusan" required>
            <option value="">-- Pilih Jurusan --</option>
            <option value="Informatika">Informatika</option>
            <option value="Sistem Informasi">Sistem Informasi</option>
            <option value="Teknik Elektro">Teknik Elektro</option>
        </select>

        <label>Email:</label>
        <input type="email" name="email" required>

        <input type="submit" value="Daftar">
    </form>
</div>

</body>
</html>

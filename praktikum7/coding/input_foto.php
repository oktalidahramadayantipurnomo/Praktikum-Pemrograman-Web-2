<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Gambar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background: #ffffff;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background-color: #4a90e2;
            color: white;
            padding: 12px;
            font-size: 16px;
            text-align: center;
        }

        td {
            padding: 10px;
            color: #333;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 7px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="text"]:focus,
        input[type="file"]:focus {
            outline: none;
            border-color: #4a90e2;
        }

        input[type="submit"] {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #357abd;
        }
    </style>
</head>
<body>

<form method="post" action="proses.php" enctype="multipart/form-data">
    <table>
        <tr>
            <th colspan="2">SIMPAN & TAMPIL GAMBAR</th>
        </tr>

        <tr>
            <td>Masukkan Nama</td>
            <td>
                <input type="text" name="nama" id="nama" placeholder="Masukkan nama" required>
            </td>
        </tr>

        <tr>
            <td>Pilih Foto</td>
            <td>
                <input type="file" name="foto" id="foto" required>
            </td>
        </tr>

        <tr>
            <td></td>
            <td>
                <input type="submit" name="kirim" id="kirim" value="SIMPAN">
            </td>
        </tr>
    </table>
</form>

</body>
</html>

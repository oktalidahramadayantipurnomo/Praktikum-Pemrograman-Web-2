<html>
<head>
<title>Upload Gambar</title>
</head>
<body>
<form method="post" action="proses.php" enctype="multipart/form-data">
<table>
<tr>
<th colspan="2">SIMPAN & TAMPIL GAMBAR</th>
</tr>
<tr>
<td>Masukan Nama</td>
<td>Pilih Foto</td>
</tr>
<tr>
<td><input type="text" name="nama" id="nama" placeholder="masukan nama"
required=""></td>
<td><input type="file" name="foto" id="foto" required=""></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="kirim" id="kirim" value="SIMPAN"></td>
</tr>
</form>
</body>
</html>
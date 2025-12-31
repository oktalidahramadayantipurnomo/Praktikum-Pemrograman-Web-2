<!DOCTYPE html>
<html>
<head>
	<title>Latihan Variabel Gaji</title>
</head>
<body>
<?php
	//Deklarasi variabel
	$gaji_per_hari = 17500;
	$jumlah_karyawan = 300;
	$hari_kerja = 30;

	//Hitung toal gaji semua karyawan
	$total_gaji = $gaji_per_hari * $jumlah_karyawan * $hari_kerja;

	//Tampilkan hasil
	echo "<h3>Perhitungan Gaji Karyawan</h3>";
	echo "Gaji per hari : Rp $gaji_per_hari <br>";
	echo "Jumlah karyawan : $jumlah_karyawan orang <br>";
	echo "Jumlah hari kerja : $hari_kerja<br>";
	echo "<hr>";
	echo "<b>Total gaji yang harus dibayarkan : Rp " .number_format($total_gaji, 0, ',', '.'). "</br>";

?>
</body>
</html>
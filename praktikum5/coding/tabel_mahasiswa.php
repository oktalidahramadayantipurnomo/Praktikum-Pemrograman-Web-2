<!DOCTYPE html>
<html lang="en">
<head>
	<title>Menciptakan Tabel</title>
</head>

<body>

<?php
require_once './koneksi.php';
$sql = 'CREATE TABLE mahasiswa (
	nim VARCHAR(12) NOT NULL,
	nama VARCHAR(40) NOT NULL,
	alamat VARCHAR(100),
	PRIMARY KEY (nim)
	) ENGINE=MyISAM;';

$res = mysqli_query($cnn, $sql);

if ($res) {
	echo 'Tabel Created';
} else {
	echo "Gagal membuat tabel" . mysqli_error($cnn);
}

mysqli_close($cnn);
?>
</body>
</html>
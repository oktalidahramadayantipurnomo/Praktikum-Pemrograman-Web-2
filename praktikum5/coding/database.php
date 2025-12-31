<!DOCTYPE html>
<html lang="en">
<head>
    <title>Menciptakan Database</title>
</head>

<body>

<?php
require_once './koneksi.php'; 

$db = 'myweb';
$query = "CREATE DATABASE $db";
$res = mysqli_query($cnn, $query);

if ($res) {
    echo "Database $db berhasil dibuat.";
} else {
    echo "Gagal membuat database: " . mysqli_error($cnn);
}

mysqli_close($cnn); 
?>
</body>
</html>

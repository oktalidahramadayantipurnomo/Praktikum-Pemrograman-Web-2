<?php
include('koneksi.php');

$id   = $_POST['id'];
$nama = $_POST['nama'];
$foto_lama = $_POST['foto_lama'];

if ($_FILES['foto']['name'] != "") {

    $file = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];
    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $valid = ['jpg','jpeg','png','gif'];

    if (!in_array($ext, $valid)) {
        die("Format file tidak didukung");
    }

    $foto_baru = rand(1000,999999).".".$ext;
    move_uploaded_file($tmp, "gambar/".$foto_baru);
    unlink("gambar/".$foto_lama);

} else {
    $foto_baru = $foto_lama;
}

mysqli_query($koneksi,
    "UPDATE namasiswa SET nama='$nama', foto='$foto_baru' WHERE id='$id'"
);

header("Location: tampil_foto.php");

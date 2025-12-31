<?php
include "protect.php";
include "koneksi_pendaftaran.php";

$id = $_GET['id'];

$getData = mysqli_query($conn, "SELECT foto FROM pendaftar WHERE id='$id'");
$data = mysqli_fetch_assoc($getData);

$foto = $data['foto'];

if (!empty($foto) && file_exists("uploads/" . $foto)) {
    unlink("uploads/" . $foto);
}

$query = "DELETE FROM pendaftar WHERE id='$id'";

if (mysqli_query($conn, $query)) {
    header("Location: index_pendaftaran.php");
    exit();
} else {
    echo "Gagal menghapus data!";
}
?>

<?php
include "koneksi_pendaftaran.php";

$nama           = $_POST['nama_lengkap'];
$email          = $_POST['email'];
$tanggal_lahir  = $_POST['tanggal_lahir'];
$alamat         = $_POST['alamat'];
$program        = $_POST['program_dipilih'];

$folder = "uploads/";

if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$foto_name = $_FILES['foto']['name'];
$foto_tmp  = $_FILES['foto']['tmp_name'];
$foto_size = $_FILES['foto']['size'];

$nama_baru = "";

if (!empty($foto_name)) {

    $allowed_ext = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        die("Format foto tidak diperbolehkan. Hanya JPG, JPEG, PNG.");
    }

    if ($foto_size > 2 * 1024 * 1024) {
        die("Ukuran foto maksimal 2MB.");
    }

    $nama_baru = "foto_" . time() . "_" . rand(1000, 9999) . "." . $ext;

    if (!move_uploaded_file($foto_tmp, $folder . $nama_baru)) {
        die("Gagal mengupload foto.");
    }
}

$query = "INSERT INTO pendaftar 
            (nama_lengkap, email, tanggal_lahir, alamat, program_dipilih, foto) 
          VALUES 
            ('$nama', '$email', '$tanggal_lahir', '$alamat', '$program', '$nama_baru')";

if (mysqli_query($conn, $query)) {
    header("Location: index_pendaftaran.php");
    exit();
} else {
    echo "Gagal menambahkan data: " . mysqli_error($conn);
}
?>

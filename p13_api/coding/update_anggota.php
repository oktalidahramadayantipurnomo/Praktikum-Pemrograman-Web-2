<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/koneksi.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_anggota   = $data['id_anggota'];
$nama_anggota = $data['nama_anggota'];
$alamat       = $data['alamat'];
$no_hp        = $data['no_hp'];
$status       = $data['status'];

$query = "UPDATE anggota SET
    nama_anggota = '$nama_anggota',
    alamat       = '$alamat',
    no_hp        = '$no_hp',
    status       = '$status'
WHERE id_anggota = '$id_anggota'";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        "status" => true,
        "message" => "Data anggota berhasil diupdate"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Gagal update data"
    ]);
}

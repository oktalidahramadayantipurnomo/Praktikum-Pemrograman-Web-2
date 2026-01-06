<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/koneksi.php";

$data = json_decode(file_get_contents("php://input"), true);

// ambil id yang akan dihapus
$id_anggota = $data['id_anggota'];

$query = "DELETE FROM anggota WHERE id_anggota = '$id_anggota'";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        "status" => true,
        "message" => "Data anggota berhasil dihapus"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Gagal menghapus data"
    ]);
}

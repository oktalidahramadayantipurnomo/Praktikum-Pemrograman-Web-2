<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/koneksi.php";

$data = json_decode(file_get_contents("php://input"), true);

$query = "INSERT INTO anggota (
id_anggota, no_anggota, nama_anggota, alamat, no_hp,
jenis_kelamin, status, tanggal_daftar, created_at
) VALUES (
'{$data['id_anggota']}',
'{$data['no_anggota']}',
'{$data['nama_anggota']}',
'{$data['alamat']}',
'{$data['no_hp']}',
'{$data['jenis_kelamin']}',
'aktif',
'{$data['tanggal_daftar']}',
NOW()
)";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => true, "message" => "Data berhasil ditambahkan"]);
} else {
    echo json_encode(["status" => false, "message" => "Gagal menambahkan data"]);
}

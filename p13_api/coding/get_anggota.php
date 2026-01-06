<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/koneksi.php";

$query = "SELECT * FROM anggota";
$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);

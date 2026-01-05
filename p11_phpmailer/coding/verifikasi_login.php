<?php
session_start();
include "../config/koneksi.php";

if (!isset($_GET['token'])) {
    die("Token tidak ditemukan.");
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

// Cari user berdasarkan token
$sql = "SELECT * FROM users 
        WHERE email_token='$token' 
        AND status='aktif'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) !== 1) {
    echo "<script>
        alert('Token tidak valid atau sudah kedaluwarsa!');
        window.location='login.php';
    </script>";
    exit();
}

$row = mysqli_fetch_assoc($result);

// Hapus token agar tidak bisa digunakan ulang
mysqli_query($conn, "
    UPDATE users SET 
        email_token = NULL,
        email_verified = 1,
        last_login = NOW()
    WHERE id_user = '{$row['id_user']}'
");

// Set session login
session_regenerate_id(true);

$_SESSION['id_user']      = $row['id_user'];
$_SESSION['username']     = $row['username'];
$_SESSION['nama_lengkap'] = $row['nama_lengkap'];
$_SESSION['role']         = $row['role'];

// Redirect sesuai role
if ($row['role'] === 'Admin') {
    header("Location: ../admin/dashboard/dashboard_admin.php");
} elseif ($row['role'] === 'Kepala Koperasi') {
    header("Location: ../kepala/dashboard.php");
} elseif ($row['role'] === 'Bendahara') {
    header("Location: ../bendahara/dashboard_bendahara.php");
} else {
    header("Location: login.php");
}
exit();

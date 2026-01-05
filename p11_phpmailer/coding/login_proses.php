<?php
session_start();
include "../config/koneksi.php";
require "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_POST['login'])) {
    header("Location: login.php");
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND status='aktif' LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: login.php?error=login");
    exit();
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    header("Location: login.php?error=login");
    exit();
}

if ($user['email_verified'] == 0) {
    $token   = bin2hex(random_bytes(32));
    $expired = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    $stmt2 = $conn->prepare("UPDATE users SET email_token=?, email_token_expired=? WHERE id_user=?");
    $stmt2->bind_param("sss", $token, $expired, $user['id_user']);
    $stmt2->execute();

    $linkVerifikasi = "http://localhost/koperasi/auth/verifikasi_login.php?token=$token";

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'koperasisp23@gmail.com';
        $mail->Password   = 'avsrhkmwdmfajdda';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('koperasisp23@gmail.com', 'Koperasi Simpan Pinjam');
        $mail->addAddress($user['email'], $user['nama_lengkap']);

        $mail->isHTML(true);
        $mail->Subject = 'Verifikasi Login Akun';
        $mail->Body = "
            <h3>Verifikasi Login</h3>
            <p>Halo <b>{$user['nama_lengkap']}</b>,</p>
            <p>Kami mendeteksi percobaan login ke akun Anda.</p>
            <p>Silakan klik tombol di bawah ini untuk melanjutkan login:</p>
            <p>
                <a href='$linkVerifikasi' style='display:inline-block;background:#28a745;color:#fff;padding:12px 20px;border-radius:6px;text-decoration:none;font-weight:bold'>VERIFIKASI LOGIN</a>
            </p>
            <p>Jika tombol tidak bisa diklik, salin link berikut:</p>
            <p style='word-break:break-all;'>$linkVerifikasi</p>
            <p style='font-size:13px;color:#666;'>Link berlaku 30 menit.</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        echo "Gagal mengirim email: {$mail->ErrorInfo}";
        exit();
    }

    header("Location: login.php?msg=cek_email");
    exit();
}

// set session & update last login
session_regenerate_id(true);
$_SESSION['id_user']  = $user['id_user'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'];

$stmt3 = $conn->prepare("UPDATE users SET last_login=NOW() WHERE id_user=?");
$stmt3->bind_param("s", $user['id_user']);
$stmt3->execute();

if ($user['role'] == 'Admin') {
    header("Location: ../admin/dashboard/dashboard_admin.php");
} elseif ($user['role'] == 'Kepala Koperasi') {
    header("Location: ../kepala/dashboard.php");
} elseif ($user['role'] == 'Bendahara') {
    header("Location: ../bendahara/dashboard/dashboard_bendahara.php");
} else {
    header("Location: login.php");
}
exit();

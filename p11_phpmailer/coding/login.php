<?php
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        header("Location: ../admin/dashboard/dashboard_admin.php");
    } elseif ($_SESSION['role'] == 'Kepala Koperasi') {
        header("Location: ../kepala/dashboard.php");
    } elseif ($_SESSION['role'] == 'Bendahara') {
        header("Location: ../bendahara/dashboard_bendahara.php");
    }
    exit();
}

$cek_email   = isset($_GET['msg']) && $_GET['msg'] === 'cek_email';
$error_login = isset($_GET['error']) && $_GET['error'] === 'login';
$error_token = isset($_GET['error']) && $_GET['error'] === 'token';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Koperasi Simpan Pinjam</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Times New Roman', Times, serif;
}
body{
    background: linear-gradient(135deg,#64b3f4,#c2e59c);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    flex-direction:column;
}
.login-container{
    background:#fff;
    padding:40px 35px;
    width:380px;
    border-radius:20px;
    box-shadow:0 8px 25px rgba(0,0,0,.2);
    text-align:center;
}
.logo-container img{
    width:90px;
    margin-bottom:10px;
}
.logo-container h2{
    font-weight:700;
    margin-bottom:4px;
}
.logo-container p{
    color:#6c757d;
    font-size:14px;
    margin-bottom:25px;
}
.input-group{
    text-align:left;
    margin-bottom:15px;
}
.input-group label{
    font-weight:bold;
}
.input-group input{
    width:100%;
    padding:10px;
    border-radius:10px;
    border:1.5px solid #ccc;
    margin-top:5px;
    font-size:16px;
}
button{
    width:100%;
    padding:10px;
    background:#64b3f4;
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:16px;
    cursor:pointer;
}
button:hover{
    background:#4a9fe0;
}
.extra-link{
    margin-top:12px;
    font-size:14px;
}
.extra-link a{
    color:#0077cc;
    text-decoration:none;
}
.extra-link a:hover{
    text-decoration:underline;
}
.error-msg{
    color:red;
    margin-bottom:15px;
}

.footer{
    position:fixed;
    bottom:0;
    width:100%;
    text-align:center;
    padding:10px 0;
    font-size:13px;
    color:#fff;
    background:rgba(0,0,0,0.2);
}
</style>
</head>

<body>
<div class="login-container">
    <div class="logo-container">
        <img src="../assets/img/logo_koperasi.png" alt="Logo Koperasi">
        <h2>Koperasi Simpan Pinjam</h2>
        <p>Silahkan login untuk melanjutkan</p>
    </div>

    <?php if ($error_login): ?>
        <div class="error-msg">Username atau password salah!</div>
    <?php endif; ?>

    <?php if ($error_token): ?>
        <div class="error-msg">Link verifikasi tidak valid atau sudah kedaluwarsa!</div>
    <?php endif; ?>

    <form action="login_proses.php" method="POST" autocomplete="off">
        <div class="input-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" name="login">Login</button>

        <div class="extra-link">
            <a href="lupa_password.php">Lupa Password?</a>
        </div>
    </form>
</div>

<div class="footer">
    © <?php echo date('Y'); ?> Koperasi Simpan Pinjam • Sistem Informasi Koperasi
</div>

<?php if ($cek_email): ?>
<script>
    alert("Link verifikasi telah dikirim ke email Anda. Silakan cek email untuk melanjutkan login.");
    window.history.replaceState({}, document.title, "login.php");
</script>
<?php endif; ?>
</body>
</html>

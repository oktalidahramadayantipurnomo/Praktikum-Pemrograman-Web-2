<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: index_pendaftaran.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Pendaftaran</title>

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #f3f3f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            width: 400px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #45A049;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>

</head>
<body>

<div class="login-box">
    <h2>Login Admin</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="error"><?= $_GET['error']; ?></div>
    <?php endif; ?>

    <form action="proses_login.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">LOGIN</button>
    </form>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Form Registrasi</title>
  <style>
    body {
      font-family: Times sans-serif;
      background-color: #f8f9fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    form {
      background: #fff;
      width: 300px;
      padding: 30px 35px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      font-size: 20px;
      font-weight: bold;
      color: #333;
    }

    label {
      font-size: 14px;
      color: #333;
      display: block;
      margin-bottom: 5px;
    }

    input {
      width: 90%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 15px;
      font-size: 14px;
    }

    input:focus {
      border-color: #4CAF50;
      outline: none;
      box-shadow: 0 0 3px #4CAF50;
    }

    button {
      width: 90%;
      background-color: #4CAF50;
      font-family: times sans-serif;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
  <form method="POST" action="koneksi_registrasi.php">
    <h2>Form Registrasi</h2>
    <label>Username</label>
    <input type="text" name="username" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Konfirmasi Password</label>
    <input type="password" name="confirm_password" required>

    <button type="submit" name="daftar">Daftar</button>
  </form>
</body>
</html>
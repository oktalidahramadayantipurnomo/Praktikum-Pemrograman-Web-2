<?php
include('koneksi.php');

// Ambil ID dari URL
$id = $_GET['id'] ?? 0;

// Query data siswa
$query = mysqli_query($koneksi, "SELECT * FROM namasiswa WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan
if(!$data) {
    die("Data tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Foto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .container {
            background: white;
            padding: 30px;
            width: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .foto-preview {
            text-align: center;
            margin: 15px 0;
        }
        
        .foto-preview img {
            width: 100px;
            height: 130px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        
        .note {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
        
        button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background: #4a90e2;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        
        button:hover {
            background: #357ae8;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #4a90e2;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>Edit Data Siswa</h3>
    
    <form method="post" action="update_foto.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $data['id']; ?>">
        <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($data['foto']); ?>">
        
        <label>Nama Siswa</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" required>
        
        <label>Foto Saat Ini</label>
        <div class="foto-preview">
            <?php if(!empty($data['foto'])): ?>
                <img src="gambar/<?= htmlspecialchars($data['foto']); ?>" 
                     alt="Foto <?= htmlspecialchars($data['nama']); ?>">
            <?php else: ?>
                <span style="color: #999;">(Tidak ada foto)</span>
            <?php endif; ?>
        </div>
        
        <label>Ganti Foto (Opsional)</label>
        <input type="file" name="foto" accept="image/*">
        <div class="note">Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG, GIF. Maks: 2MB</div>
        
        <button type="submit">Update Data</button>
        <a href="tampil_foto.php" class="back-link">Kembali ke Data</a>
    </form>
</div>

</body>
</html>
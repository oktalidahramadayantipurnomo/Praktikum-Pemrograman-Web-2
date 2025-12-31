<?php
include('koneksi.php');

$query = mysqli_query($koneksi, "SELECT * FROM namasiswa ORDER BY id DESC");
$no = 1;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Foto Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eaeaea;
        }
        
        .header h2 {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 600;
        }
        
        .btn-add {
            background-color: #4a90e2;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }
        
        .btn-add:hover {
            background-color: #357ae8;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        thead {
            background-color: #f8f9fa;
        }
        
        th {
            color: #495057;
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            font-size: 13px;
        }
        
        td {
            padding: 10px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: middle;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .foto {
            width: 50px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .btn {
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 12px;
            transition: all 0.2s;
            display: inline-block;
        }
        
        .btn-edit {
            background-color: #ff9800;
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #e68900;
        }
        
        .btn-delete {
            background-color: #e53935;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #d32f2f;
        }
        
        .aksi-container {
            display: flex;
            gap: 5px;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            th, td {
                padding: 8px 6px;
                font-size: 13px;
            }
            
            .foto {
                width: 40px;
                height: 50px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Data Foto Siswa</h2>
        <a href="input_foto.php" class="btn-add">+ Tambah Data</a>
    </div>

    <table>
        <thead>
            <tr>
                <th width="50">NO</th>
                <th>NAMA SISWA</th>
                <th width="80">FOTO</th>
                <th width="120">AKSI</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($query) > 0): ?>
                <?php while($d = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($d['nama']); ?></td>
                        <td>
                            <?php if(!empty($d['foto'])): ?>
                                <img src="gambar/<?= htmlspecialchars($d['foto']); ?>" 
                                     alt="Foto <?= htmlspecialchars($d['nama']); ?>" 
                                     class="foto">
                            <?php else: ?>
                                <span style="color: #999; font-size: 12px;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="aksi-container">
                                <a href="edit_foto.php?id=<?= $d['id']; ?>" 
                                   class="btn btn-edit">Edit</a>
                                <a href="hapus.php?del=<?= $d['id']; ?>" 
                                   class="btn btn-delete"
                                   onclick="return confirm('Hapus data ini?')">
                                   Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="no-data">
                        Tidak ada data siswa. <a href="input_foto.php" style="color: #4a90e2;">Tambah data</a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
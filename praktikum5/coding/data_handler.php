<?php
require_once './koneksi.php';
define('MHS', 'mahasiswa'); 

function data_handler($root) {
    global $cnn;

    $act = isset($_GET['act']) ? $_GET['act'] : '';

    switch ($act) {
        case 'add':
            data_add($root);
            return;
        case 'edit':
            if (isset($_GET['id'])) data_edit($root, $_GET['id']);
            else show_admin_data($root);
            return;
        case 'view':
            if (isset($_GET['id'])) data_detail($root, $_GET['id']);
            else show_admin_data($root);
            return;
        case 'del':
            if (isset($_GET['id'])) data_delete($root, $_GET['id']);
            else show_admin_data($root);
            return;
        default:
            show_admin_data($root);
    }
}

// TAMPILAN DAFTAR DATA

function show_admin_data($root) {
    global $cnn;
    echo '<h2>Administrasi Data Mahasiswa</h2>';

    $sql = 'SELECT nim, nama, alamat FROM ' . MHS;
    $res = mysqli_query($cnn, $sql);

    if ($res) {
        $num = mysqli_num_rows($res);
        if ($num) {
            echo '<div style="padding:5px;">';
            echo '<a href="'.$root.'&act=add">Tambah Data</a>';
            echo '</div>';

            echo '<table border="1" width="700" cellpadding="4" cellspacing="0">';
            echo '<tr>
                    <th>#</th>
                    <th width="120">NIM</th>
                    <th width="200">Nama</th>
                    <th width="200">Alamat</th>
                    <th>Menu</th>
                  </tr>';

            $i = 1;
            while ($row = mysqli_fetch_row($res)) {
                $id = $row[0];
                $bg = ($i % 2 == 0) ? '#EEEEEE' : '#FFFFFF';
                echo '<tr bgcolor="'.$bg.'">';
                echo '<td align="center">'.$i.'</td>';
                echo '<td>'.$row[0].'</td>';
                echo '<td>'.$row[1].'</td>';
                echo '<td>'.$row[2].'</td>';
                echo '<td align="center">
                        <a href="'.$root.'&act=view&id='.$id.'">Lihat</a> |
                        <a href="'.$root.'&act=edit&id='.$id.'">Edit</a> |
                        <a href="'.$root.'&act=del&id='.$id.'" onclick="return confirm(\'Hapus data ini?\')">Hapus</a>
                      </td>';
                echo '</tr>';
                $i++;
            }
            echo '</table>';
        } else {
            echo 'Belum ada data, isi <a href="'.$root.'&act=add">di sini</a>.';
        }
    } else {
        echo 'Gagal mengambil data: ' . mysqli_error($cnn);
    }
}

function data_add($root) {
    global $cnn;

    if (isset($_POST['submit'])) {
        $nim = mysqli_real_escape_string($cnn, $_POST['nim']);
        $nama = mysqli_real_escape_string($cnn, $_POST['nama']);
        $alamat = mysqli_real_escape_string($cnn, $_POST['alamat']);

        if ($nim != '' && $nama != '') {
            $sql = "INSERT INTO " . MHS . " (nim, nama, alamat) VALUES ('$nim', '$nama', '$alamat')";
            $res = mysqli_query($cnn, $sql);

            if ($res) {
                echo '<p>✅ Data berhasil ditambahkan.</p>';
            } else {
                echo '<p>❌ Gagal menambah data: ' . mysqli_error($cnn) . '</p>';
            }
        } else {
            echo '<p style="color:red;">NIM dan Nama wajib diisi.</p>';
        }
    }
    ?>
    <style>
        form {
            width: 400px;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
            margin-bottom: 3px;
        }
        input[type="text"] {
            width: 100%;
            padding: 4px;
            box-sizing: border-box;
        }
        input[type="submit"], input[type="button"] {
            margin-top: 10px;
            padding: 5px 15px;
            cursor: pointer;
        }
        hr {
            margin-top: 20px;
            margin-bottom: 15px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 700px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>

    <h2>Tambah Data Mahasiswa</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?act=add" method="post">
        <label for="nim">NIM</label>
        <input type="text" name="nim" id="nim" required />

        <label for="nama">Nama</label>
        <input type="text" name="nama" id="nama" required />

        <label for="alamat">Alamat</label>
        <input type="text" name="alamat" id="alamat" />

        <input type="submit" name="submit" value="Simpan" />
        <input type="button" value="Batal" onclick="window.location.href='<?php echo $root; ?>';" />
    </form>
    <hr>
    <?php

    $sql = "SELECT * FROM " . MHS . " ORDER BY nim";
    $res = mysqli_query($cnn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        echo '<table>';
        echo '<tr><th>#</th><th>NIM</th><th>Nama</th><th>Alamat</th></tr>';
        $i = 1;
        while ($row = mysqli_fetch_assoc($res)) {
            echo '<tr>';
            echo '<td>'.$i.'</td>';
            echo '<td>'.$row['nim'].'</td>';
            echo '<td>'.$row['nama'].'</td>';
            echo '<td>'.$row['alamat'].'</td>';
            echo '</tr>';
            $i++;
        }
        echo '</table>';
    } else {
        echo '<p>Belum ada data mahasiswa.</p>';
    }
}

function data_edit($root, $id) {
    global $cnn;

    $sql = "SELECT * FROM " . MHS . " WHERE nim='$id'";
    $res = mysqli_query($cnn, $sql);

    $nim = '';
    $nama = '';
    $alamat = '';

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $nim = $row['nim'];
        $nama = $row['nama'];
        $alamat = $row['alamat'];
    }

    if (isset($_POST['submit'])) {
        $nama_baru = mysqli_real_escape_string($cnn, $_POST['nama']);
        $alamat_baru = mysqli_real_escape_string($cnn, $_POST['alamat']);
        $sql = "UPDATE " . MHS . " SET nama='$nama_baru', alamat='$alamat_baru' WHERE nim='$id'";
        $res = mysqli_query($cnn, $sql);
        if ($res) {
            header("Location: " . $root);
            exit;
        } else {
            echo 'Gagal menyimpan data: ' . mysqli_error($cnn);
        }
    }

    ?>
    <h2>Edit Data Mahasiswa</h2>
    <form method="post" action="">
        <table border="1" cellpadding="4" cellspacing="0" width="500">
            <tr>
                <td width="100">NIM</td>
                <td><input type="text" name="nim" size="10" value="<?php echo $nim; ?>" readonly /></td>
            </tr>
            <tr>
                <td>Nama</td>
                <td><input type="text" name="nama" size="40" value="<?php echo $nama; ?>" /></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td><input type="text" name="alamat" size="60" value="<?php echo $alamat; ?>" /></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name="submit" value="Simpan" />
                    <input type="button" value="Batal" onclick="document.location.href='<?php echo $root; ?>';" />
                </td>
            </tr>
        </table>
    </form>
    <br>
    <p>Ket: * Harus diisi</p>
    <?php
}

// HAPUS DATA

function data_delete($root, $id) {
    global $cnn;
    $id = mysqli_real_escape_string($cnn, $id);
    $sql = "DELETE FROM " . MHS . " WHERE nim='$id'";
    $res = mysqli_query($cnn, $sql);
    if ($res) {
        header("Location: ".$root);
        exit;
    } else {
        echo 'Gagal menghapus data: ' . mysqli_error($cnn);
    }
}

// DETAIL DATA

function data_detail($root, $id) {
    global $cnn;
    $id = mysqli_real_escape_string($cnn, $id);

    $sql = "SELECT nim, nama, alamat FROM " . MHS . " WHERE nim='$id'";
    $res = mysqli_query($cnn, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        echo '<h2>Detail Data Mahasiswa</h2>';
        echo '<table border="1" cellpadding="4" cellspacing="0" width="400">';
        echo '<tr><td><b>NIM</b></td><td>'.$row['nim'].'</td></tr>';
        echo '<tr><td><b>Nama</b></td><td>'.$row['nama'].'</td></tr>';
        echo '<tr><td><b>Alamat</b></td><td>'.$row['alamat'].'</td></tr>';
        echo '</table>';
        echo '<br><a href="'.$root.'">Kembali ke daftar</a>';
    } else {
        echo 'Data tidak ditemukan.';
    }
}
?>

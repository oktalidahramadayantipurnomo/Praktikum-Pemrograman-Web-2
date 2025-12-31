<?php
/**
 * Fungsi untuk menampilkan detail data mahasiswa
 * @param string $root nama parameter menu
 * @param string $id NIM mahasiswa
 */

define('MHS', 'mahasiswa'); // nama tabel

// Koneksi ke database
$cnn = mysqli_connect('localhost', 'root', '', 'myweb');
if (!$cnn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

function data_detail($root, $id) {
    global $cnn;

    // Hindari SQL Injection
    $id = mysqli_real_escape_string($cnn, $id);

    $sql = "SELECT nim, nama, alamat FROM " . MHS . " WHERE nim='$id'";
    $res = mysqli_query($cnn, $sql);

    if ($res) {
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_row($res);
            ?>
            <h2>Detail Data Mahasiswa</h2>
            <div class="tabel">
                <table border="1" width="700" cellpadding="4" cellspacing="0">
                    <tr>
                        <td width="150">NIM</td>
                        <td><?php echo $row[0]; ?></td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td><?php echo $row[1]; ?></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td><?php echo $row[2]; ?></td>
                    </tr>
                </table>
            </div>
            <p><a href="index.php">Kembali ke Daftar Mahasiswa</a></p>
            <?php
        } else {
            echo "<p>Data tidak ditemukan untuk NIM <b>$id</b>.</p>";
        }
    } else {
        echo "<p>Terjadi kesalahan query: " . mysqli_error($cnn) . "</p>";
    }
}

if (isset($_GET['nim'])) {
    $nim = $_GET['nim'];
    data_detail('menu_mhs', $nim);
} else {
    echo "<p>NIM tidak ditemukan.</p>";
}

// Tutup koneksi
mysqli_close($cnn);
?>

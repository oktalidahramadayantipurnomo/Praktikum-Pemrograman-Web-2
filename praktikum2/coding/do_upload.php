<html>
<head>
    <title>proses upload</title>
</head>
<body>
    <h1>proses penyimpanan file</h1>
    <?php
    $targetDir="files/";
    if (!is_dir($targetDir)){
        mkdir($targetDir, 0777, true);
    }
    if (isset($_FILES['file1']) && $_FILES['file1']['error'] === UPLOAD_ERR_OK){
        $namaFileAsli = $_FILES['file1']['name'];
        $lokasiSementara = $_FILES['file1']['tmp_name'];
        $tujuan = $targetDir . $namaFileAsli;
        
        if (move_uploaded_file($lokasiSementara, $tujuan)){
            echo "<p> file berhasil diupload! </P>";
            echo "<p> Nama File: <b>$namaFileAsli</b></p>";
            echo "<P>Disimpan di folder: <b>$targetDir</b></p>";
        } else {
            echo "<p> Gagal memindahkan file ke folder tujuan.</p>";
        }
    }
    ?>
</body>
</html>
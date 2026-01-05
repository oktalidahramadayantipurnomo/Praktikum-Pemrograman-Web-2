<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../auth/login.php");
    exit;
}

require_once __DIR__ . '/../../vendor/mpdf/mpdf/mpdf.php';

include __DIR__ . "/../../config/koneksi.php";

function getDataFromView($conn, $view_name, $filter_bulan = null, $filter_tahun = null) {
    $where = "";
    
    if ($view_name === 'v_laporan_simpanan' && $filter_bulan && $filter_tahun) {
        $where = " WHERE MONTH(tanggal) = '$filter_bulan' AND YEAR(tanggal) = '$filter_tahun'";
    } elseif ($view_name === 'v_laporan_pinjaman' && $filter_bulan && $filter_tahun) {
        $where = " WHERE MONTH(tanggal_verifikasi) = '$filter_bulan' AND YEAR(tanggal_verifikasi) = '$filter_tahun'";
    } elseif ($view_name === 'v_laporan_angsuran' && $filter_bulan && $filter_tahun) {
        $where = " WHERE MONTH(tanggal_bayar) = '$filter_bulan' AND YEAR(tanggal_bayar) = '$filter_tahun'";
    }
    
    $query = "SELECT * FROM $view_name $where ORDER BY 
              CASE 
                WHEN '$view_name' = 'v_laporan_anggota' THEN tanggal_daftar 
                WHEN '$view_name' = 'v_laporan_simpanan' THEN tanggal 
                WHEN '$view_name' = 'v_laporan_pinjaman' THEN tanggal_verifikasi 
                WHEN '$view_name' = 'v_laporan_angsuran' THEN tanggal_bayar 
              END DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return [];
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    return $data;
}

function getColumnNames($conn, $view_name) {
    $query = "SHOW COLUMNS FROM $view_name";
    $result = mysqli_query($conn, $query);
    
    $columns = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row['Field'];
    }
    
    return $columns;
}

$jenis_laporan = $_GET['jenis'] ?? 'anggota';
$filter_bulan = $_GET['bulan'] ?? date('m');
$filter_tahun = $_GET['tahun'] ?? date('Y');

switch ($jenis_laporan) {
    case 'anggota':
        $view_name = 'v_laporan_anggota';
        $judul = 'LAPORAN DATA ANGGOTA';
        $periode = 'Data per ' . date('d F Y');
        break;
        
    case 'simpanan':
        $view_name = 'v_laporan_simpanan';
        $judul = 'LAPORAN SIMPANAN';
        $periode = 'Periode: ' . date("F", mktime(0, 0, 0, $filter_bulan, 1)) . ' ' . $filter_tahun;
        break;
        
    case 'pinjaman':
        $view_name = 'v_laporan_pinjaman';
        $judul = 'LAPORAN PINJAMAN';
        $periode = 'Periode: ' . date("F", mktime(0, 0, 0, $filter_bulan, 1)) . ' ' . $filter_tahun;
        break;
        
    case 'angsuran':
        $view_name = 'v_laporan_angsuran';
        $judul = 'LAPORAN ANGSURAN';
        $periode = 'Periode: ' . date("F", mktime(0, 0, 0, $filter_bulan, 1)) . ' ' . $filter_tahun;
        break;
        
    default:
        $view_name = 'v_laporan_anggota';
        $judul = 'LAPORAN DATA ANGGOTA';
        $periode = 'Data per ' . date('d F Y');
        break;
}

$data = getDataFromView($conn, $view_name, $filter_bulan, $filter_tahun);
$columns = getColumnNames($conn, $view_name);

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $judul . '</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10pt; 
            margin: 0;
            padding: 0;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #000; 
            padding-bottom: 10px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 16pt; 
            color: #2554bb;
        }
        .header h2 { 
            margin: 5px 0; 
            font-size: 14pt;
        }
        .header p { 
            margin: 3px 0; 
            font-size: 10pt;
        }
        .info { 
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .info table { 
            width: 100%; 
        }
        .info td { 
            padding: 5px;
            font-size: 9pt;
        }
        .table-container {
            margin-top: 15px;
            overflow-x: auto;
        }
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
            font-size: 8pt;
        }
        .data-table th { 
            background-color: #2554bb; 
            color: white;
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
            font-weight: bold;
            font-size: 9pt;
        }
        .data-table td { 
            border: 1px solid #ddd; 
            padding: 6px; 
            vertical-align: top;
        }
        .text-right { 
            text-align: right; 
        }
        .text-center { 
            text-align: center; 
        }
        .text-left { 
            text-align: left; 
        }
        .footer { 
            margin-top: 30px; 
            padding-top: 10px;
            border-top: 1px solid #000; 
            font-size: 9pt;
        }
        .signature { 
            float: right; 
            text-align: center;
            margin-top: 30px;
        }
        .total { 
            font-weight: bold; 
            font-size: 10pt;
        }
        .page-break {
            page-break-before: always;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 20px;
        }
        .summary {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KOPERASI SIMPAN PINJAM</h1>
        <h2>' . $judul . '</h2>
        <p>' . $periode . '</p>
        <p>Dicetak pada: ' . date('d/m/Y H:i:s') . '</p>
    </div>
    
    <div class="info">
        <table>
            <tr>
                <td width="50%"><strong>Jenis Laporan:</strong> ' . ucfirst($jenis_laporan) . '</td>
                <td width="50%"><strong>Jumlah Data:</strong> ' . count($data) . ' record</td>
            </tr>
            <tr>
                <td><strong>Nama View:</strong> ' . $view_name . '</td>
                <td><strong>Pengguna:</strong> ' . $_SESSION['username'] . '</td>
            </tr>
        </table>
    </div>';

$total_nominal = 0;
if (in_array($jenis_laporan, ['simpanan', 'pinjaman', 'angsuran'])) {
    foreach ($data as $row) {
        $key = '';
        switch ($jenis_laporan) {
            case 'simpanan': $key = 'jumlah_simpanan'; break;
            case 'pinjaman': $key = 'jumlah_pinjaman'; break;
            case 'angsuran': $key = 'jumlah_angsuran'; break;
        }
        $total_nominal += $row[$key] ?? 0;
    }
    
    $html .= '
    <div class="summary">
        <strong>Total ' . ucfirst($jenis_laporan) . ':</strong> Rp ' . number_format($total_nominal, 0, ',', '.') . '
    </div>';
}

if (!empty($data)) {
    $html .= '
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>';
    

    foreach ($columns as $col) {
        $col_name = ucwords(str_replace('_', ' ', $col));
       
        $short_col = $col_name;
        if (strlen($col_name) > 20) {
            $short_col = substr($col_name, 0, 18) . '...';
        }
        
        $html .= '<th class="text-center">' . $short_col . '</th>';
    }
    
    $html .= '
                </tr>
            </thead>
            <tbody>';

    foreach ($data as $row) {
        $html .= '<tr>';
        
        foreach ($columns as $col) {
            $value = $row[$col] ?? '';
            

            if (strpos($col, 'tanggal') !== false && !empty($value)) {
                $formatted_value = date('d/m/Y', strtotime($value));
                $html .= '<td class="text-center">' . $formatted_value . '</td>';
            } 
            elseif (strpos($col, 'jumlah') !== false || 
                   strpos($col, 'total') !== false || 
                   strpos($col, 'sisa') !== false ||
                   strpos($col, 'angsuran') !== false) {
                if (is_numeric($value)) {
                    $formatted_value = 'Rp ' . number_format($value, 0, ',', '.');
                    $html .= '<td class="text-right">' . $formatted_value . '</td>';
                } else {
                    $html .= '<td>' . htmlspecialchars($value) . '</td>';
                }
            }
            elseif (strpos($col, 'bunga') !== false && is_numeric($value)) {
                $html .= '<td class="text-center">' . $value . '%</td>';
            }
            elseif (strpos($col, 'status') !== false) {
                $bg_color = ($value == 'Aktif' || $value == 'Disetujui') ? '#d4edda' : '#f8d7da';
                $text_color = ($value == 'Aktif' || $value == 'Disetujui') ? '#155724' : '#721c24';
                $html .= '<td class="text-center" style="background-color: ' . $bg_color . '; color: ' . $text_color . '; font-weight: bold;">' . $value . '</td>';
            }
            else {
                if (strlen($value) > 50) {
                    $value = substr($value, 0, 47) . '...';
                }
                $html .= '<td>' . htmlspecialchars($value) . '</td>';
            }
        }
        
        $html .= '</tr>';
    }
    
    $html .= '
            </tbody>
        </table>
    </div>';
} else {
    $html .= '
    <div class="no-data">
        <p>Tidak ada data yang ditemukan untuk laporan ini.</p>
        <p>Silakan periksa filter atau periode yang dipilih.</p>
    </div>';
}

$html .= '
    <div class="footer">
        <div class="signature">
            <p>Mengetahui,</p>
            <br><br><br>
            <p><strong>' . $_SESSION['username'] . '</strong></p>
            <p><em>Admin Koperasi</em></p>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>';

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L', 
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 20,
    'margin_bottom' => 20,
    'margin_header' => 10,
    'margin_footer' => 10,
]);

$mpdf->SetHTMLHeader('
<div style="text-align: center; font-size: 8pt; color: #666; border-bottom: 1px solid #eee; padding-bottom: 5px;">
    ' . $judul . ' - Koperasi Simpan Pinjam
</div>');

$mpdf->SetHTMLFooter('
<div style="text-align: center; font-size: 8pt; color: #666; border-top: 1px solid #eee; padding-top: 5px;">
    Halaman {PAGENO} dari {nbpg} | Dicetak pada: ' . date('d/m/Y H:i:s') . '
</div>');


$mpdf->WriteHTML($html);


$filename = 'laporan_' . $jenis_laporan . '_' . date('Ymd_His') . '.pdf';
$mpdf->Output($filename, 'I'); 
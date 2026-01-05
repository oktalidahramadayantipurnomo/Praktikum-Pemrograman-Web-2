<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../auth/login.php");
    exit;
}

include __DIR__ . "/../../config/koneksi.php";

function getDataFromView($conn, $view_name, $filter_bulan = null, $filter_tahun = null, $filter_status = null) {
    $where = "";
    $order_by = "";

    if ($view_name === 'v_laporan_anggota') {
        $order_by = "tanggal_daftar";
        if ($filter_status && $filter_status !== 'all') {
            $where = " WHERE status = '$filter_status'";
        }
    } 
    elseif ($view_name === 'v_laporan_simpanan') {
        $order_by = "tanggal";
        if ($filter_bulan && $filter_tahun) {
            $where = " WHERE MONTH(tanggal) = '$filter_bulan' AND YEAR(tanggal) = '$filter_tahun'";
        }
    } 
    elseif ($view_name === 'v_laporan_pinjaman') {
        $order_by = "tanggal_verifikasi";
        if ($filter_bulan && $filter_tahun) {
            $where = " WHERE MONTH(tanggal_verifikasi) = '$filter_bulan' AND YEAR(tanggal_verifikasi) = '$filter_tahun'";
        }
    } 
    elseif ($view_name === 'v_laporan_angsuran') {
        $order_by = "tanggal_bayar";
        if ($filter_bulan && $filter_tahun) {
            $where = " WHERE MONTH(tanggal_bayar) = '$filter_bulan' AND YEAR(tanggal_bayar) = '$filter_tahun'";
        }
    }

    $query = "SELECT * FROM $view_name $where ORDER BY $order_by DESC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "<!-- SQL Error: " . mysqli_error($conn) . " -->";
        return [];
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

$jenis_laporan = $_GET['jenis'] ?? 'anggota';
$filter_bulan = $_GET['bulan'] ?? date('m');
$filter_tahun = $_GET['tahun'] ?? date('Y');
$filter_status = $_GET['status'] ?? 'all';
$action = $_GET['action'] ?? 'view';

switch ($jenis_laporan) {
    case 'anggota':
        $view_name = 'v_laporan_anggota';
        $data_laporan = getDataFromView($conn, $view_name, null, null, $filter_status);
        $judul_laporan = 'LAPORAN DATA ANGGOTA';
        if ($filter_status === 'aktif') {
            $subjudul = 'DATA ANGGOTA AKTIF';
        } elseif ($filter_status === 'nonaktif') {
            $subjudul = 'DATA ANGGOTA NONAKTIF';
        } else {
            $subjudul = 'DATA SEMUA ANGGOTA';
        }
        $periode_text = 'Periode: ' . date('d F Y');
        $columns = ['No', 'No Anggota', 'Nama Anggota', 'Alamat', 'No Telepon', 'Jenis Kelamin', 'Tanggal Daftar', 'Status Anggota'];
        break;
        
    case 'simpanan':
        $view_name = 'v_laporan_simpanan';
        $data_laporan = getDataFromView($conn, $view_name, $filter_bulan, $filter_tahun, $filter_status);
        $judul_laporan = 'LAPORAN SIMPANAN';
        $subjudul = 'DATA SIMPANAN ANGGOTA';
        $periode_text = 'Periode: ' . date("F", mktime(0, 0, 0, $filter_bulan, 1)) . ' ' . $filter_tahun;
        $columns = ['No', 'ID Simpanan', 'Nama Anggota', 'Tanggal', 'Jenis Simpanan', 'Jumlah Simpanan', 'Keterangan'];
        break;
        
    case 'pinjaman':
        $view_name = 'v_laporan_pinjaman';
        $data_laporan = getDataFromView($conn, $view_name, $filter_bulan, $filter_tahun, $filter_status);
        $judul_laporan = 'LAPORAN PINJAMAN';
        $subjudul = 'DATA PINJAMAN ANGGOTA';
        $periode_text = 'Periode: ' . date("F", mktime(0, 0, 0, $filter_bulan, 1)) . ' ' . $filter_tahun;
        $columns = ['No', 'ID Pinjaman', 'Nama Anggota', 'Tanggal Verifikasi', 'Jumlah Pinjaman', 'Jangka Waktu', 'Bunga', 'Status', 'Total Angsuran', 'Sisa Pinjaman'];
        break;
        
    case 'angsuran':
        $view_name = 'v_laporan_angsuran';
        $data_laporan = getDataFromView($conn, $view_name, $filter_bulan, $filter_tahun, $filter_status);
        $judul_laporan = 'LAPORAN ANGSURAN';
        $subjudul = 'DATA ANGSURAN PINJAMAN';
        $periode_text = 'Periode: ' . date("F", mktime(0, 0, 0, $filter_bulan, 1)) . ' ' . $filter_tahun;
        $columns = ['No', 'ID Angsuran', 'Nama Anggota', 'Tanggal Bayar', 'Jumlah Angsuran', 'Keterangan', 'Metode Pembayaran'];
        break;
        
    default:
        $view_name = 'v_laporan_anggota';
        $data_laporan = getDataFromView($conn, $view_name);
        $judul_laporan = 'LAPORAN DATA ANGGOTA';
        $subjudul = 'DATA SEMUA ANGGOTA';
        $periode_text = 'Data per ' . date('d F Y');
        $columns = ['No', 'No Anggota', 'Nama Anggota', 'Alamat', 'No Telepon', 'Jenis Kelamin', 'Tanggal Daftar', 'Status Anggota'];
        break;
}

if ($action == 'export-pdf') {
    require_once __DIR__ . '/export_pdf.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Koperasi Simpan Pinjam</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/sidebar_admin.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <style>
        :root {
            --primary-color: #2c5aa0;
            --secondary-color: #3a7bd5;
            --accent-color: #4CAF50;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --sidebar-width: 240px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: var(--light-bg);
            display: flex;
            min-height: 100vh;
            color: var(--text-primary);
        }

        .content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
 
        .header {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .header h1 i {
            color: var(--secondary-color);
            font-size: 16px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--light-bg);
            padding: 8px 15px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-size: 13px;
        }
        
        .user-info i {
            color: var(--primary-color);
            font-size: 14px;
        }
        
        .user-info span {
            font-weight: 500;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .card-header {
            padding: 15px 20px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .card-header h3 {
            font-size: 16px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-header h3 i {
            font-size: 14px;
        }
        
        .card-header small {
            opacity: 0.9;
            font-size: 11px;
            display: block;
            margin-top: 3px;
        }
        
        .card-body {
            padding: 20px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .form-group label {
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .form-group label i {
            font-size: 12px;
        }
        
        .form-control {
            padding: 8px 12px;
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            font-size: 13px;
            transition: all 0.2s;
            background: white;
            color: var(--text-primary);
            height: 36px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(44, 90, 160, 0.1);
        }
        
        .form-control:hover {
            border-color: var(--primary-color);
        }
        
        .btn-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            text-decoration: none;
            height: 36px;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #244785;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(44, 90, 160, 0.2);
        }
        
        .btn-success {
            background: var(--accent-color);
            color: white;
        }
        
        .btn-success:hover {
            background: #3d8b40;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(76, 175, 80, 0.2);
        }
        
        .btn-danger {
            background: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(231, 76, 60, 0.2);
        }
        
        .btn-warning {
            background: var(--warning-color);
            color: white;
        }
        
        .btn-warning:hover {
            background: #e67e22;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(243, 156, 18, 0.2);
        }
        
        .btn-export {
            background: #9c27b0;
            color: white;
        }
        
        .btn-export:hover {
            background: #7b1fa2;
            transform: translateY(-1px);
        }
        
        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        
        .laporan-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
            font-size: 12px;
        }
        
        .laporan-table thead {
            background: var(--light-bg);
        }
        
        .laporan-table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: var(--primary-color);
            border-bottom: 2px solid var(--border-color);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .laporan-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            font-size: 12px;
        }
        
        .laporan-table tbody tr {
            transition: all 0.2s;
        }
        
        .laporan-table tbody tr:hover {
            background: rgba(44, 90, 160, 0.04);
        }
        
        .laporan-table .text-right {
            text-align: right;
        }
        
        .laporan-table .text-center {
            text-align: center;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-active {
            background: #d1f7c4;
            color: #0a5a0a;
        }
        
        .status-inactive {
            background: #ffeaea;
            color: #c53030;
        }
        
        .no-data {
            text-align: center;
            padding: 40px 15px;
            color: var(--text-secondary);
        }
        
        .no-data i {
            font-size: 40px;
            color: var(--border-color);
            margin-bottom: 10px;
        }
        
        .no-data h4 {
            font-size: 14px;
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        
        .no-data p {
            font-size: 12px;
        }
        
        .summary-section {
            background: var(--light-bg);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .summary-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid var(--border-color);
            transition: transform 0.2s;
        }
        
        .summary-item:hover {
            transform: translateY(-3px);
        }
        
        .summary-item h4 {
            font-size: 11px;
            color: var(--text-secondary);
            margin-bottom: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .summary-item p {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            justify-content: center;
        }
        
        @media print {

            * {
                font-family: 'Times New Roman', serif !important;
                color: black !important;
                background: transparent !important;
            }

            .sidebar, .header, .filter-section, .btn-group, .action-buttons, 
            .summary-section, .card-header, .no-data, .user-info,
            .summary-grid, .btn, nav, .logo {
                display: none !important;
            }
 
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0.5cm !important;
                font-size: 11pt !important;
                line-height: 1.4 !important;
            }
            
            .content {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            
            .kop-surat {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 3px solid #000;
                padding-bottom: 15px;
                page-break-after: avoid;
            }
            
            .kop-header-1 {
                font-size: 13pt !important;
                font-weight: bold;
                margin-bottom: 5px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .kop-header-2 {
                font-size: 15pt !important;
                font-weight: bold;
                margin-bottom: 8px;
                text-decoration: underline;
            }
            
            .kop-info {
                font-size: 10pt !important;
                line-height: 1.4;
                margin: 5px 0;
            }
            
            .kop-divider {
                border-bottom: 1px solid #000;
                margin: 10px 0;
            }
            
            .judul-laporan {
                text-align: center;
                font-size: 14pt !important;
                font-weight: bold;
                margin: 20px 0 10px 0;
                text-transform: uppercase;
            }
            
            .subjudul-laporan {
                text-align: center;
                font-size: 12pt !important;
                font-weight: bold;
                margin-bottom: 15px;
                text-decoration: underline;
            }

            .info-laporan {
                margin: 15px 0;
                font-size: 10pt !important;
                padding: 10px;
                border: 1px solid #000;
                background: #f8f8f8 !important;
            }
            
            .info-laporan table {
                width: 100%;
                border-collapse: collapse;
            }
            
            .info-laporan td {
                padding: 5px 10px;
                border: 1px solid #ddd;
            }
            
            .tanggal-cetak {
                text-align: right;
                font-size: 10pt !important;
                margin-bottom: 20px;
            }
            
            .laporan-table {
                width: 100% !important;
                border: 2px solid #000 !important;
                margin: 10px 0;
                page-break-inside: avoid;
                font-size: 10pt !important;
            }
            
            .laporan-table th {
                background: #e0e0e0 !important;
                color: #000 !important;
                border: 1px solid #000 !important;
                padding: 8px 5px !important;
                font-weight: bold;
                font-size: 10pt !important;
                text-align: center !important;
            }
            
            .laporan-table td {
                border: 1px solid #000 !important;
                padding: 6px 5px !important;
                font-size: 10pt !important;
                text-align: left !important;
            }
            
            .ttd-area {
                margin-top: 60px;
                text-align: right;
                padding-right: 80px;
                page-break-before: avoid;
            }
            
            .ttd-nama {
                text-align: center;
                display: inline-block;
                margin-top: 60px;
            }
            
            .ttd-nama span {
                font-weight: bold;
                display: block;
                margin-top: 70px;
                border-top: 1px solid #000;
                padding-top: 5px;
                width: 200px;
            }
            
            .card {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
            }
            
            .card-body {
                padding: 0 !important;
            }
            
            .table-responsive {
                border: none !important;
                overflow: visible !important;
            }
 
            @page {
                margin: 1cm;
                size: A4 portrait;
            }
            
            body:after {
                content: "Hal. " counter(page);
                position: fixed;
                bottom: 1cm;
                right: 1cm;
                font-size: 9pt;
                color: #666;
            }
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 60px;
            }
            
            .logo span,
            nav a span,
            .logout-section a span {
                display: none;
            }
            
            .content {
                margin-left: 60px;
                padding: 15px;
            }
            
            .filter-form {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .user-info {
                width: 100%;
                justify-content: center;
            }
            
            .btn-group {
                flex-direction: column;
                width: 100%;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .content {
                padding: 10px;
            }
            
            .header {
                padding: 12px 15px;
            }
            
            .card-body {
                padding: 15px;
            }
            
            .laporan-table th,
            .laporan-table td {
                padding: 8px 10px;
                font-size: 11px;
            }
        }

        .print-only {
            display: none;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <img src="../../assets/img/logo_koperasi.png">
        <span>Koperasi<br>Simpan Pinjam</span>
    </div>
    <nav>
        <a href="../dashboard/dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="index_anggota.php"><i class="fas fa-users"></i> Anggota</a>
        <a href="../simpanan/index_simpanan.php"><i class="fas fa-wallet"></i> Simpanan</a>
        <a href="../pinjaman/index_pinjaman.php"><i class="fas fa-hand-holding-usd"></i> Pinjaman</a>
        <a href="../angsuran/index_angsuran.php"><i class="fas fa-calendar-check"></i> Angsuran</a>
        <a class="active" href="index_laporan.php"><i class="fas fa-file-alt"></i> Laporan</a>
        <a href="../../auth/logout.php" class="logout"
           onclick="return confirm('Yakin ingin logout?')">
           <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>

<div class="content">

    <div class="header">
        <h1><i class="fas fa-chart-bar"></i> Sistem Laporan</h1>
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span><?= htmlspecialchars($_SESSION['username']) ?> (Admin)</span>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-filter"></i> Filter Laporan</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="" class="filter-form">
                <div class="form-group">
                    <label for="jenis_laporan"><i class="fas fa-file-signature"></i> Jenis Laporan</label>
                    <select name="jenis" id="jenis_laporan" class="form-control" onchange="this.form.submit()">
                        <option value="anggota" <?= $jenis_laporan == 'anggota' ? 'selected' : '' ?>>Laporan Anggota</option>
                        <option value="simpanan" <?= $jenis_laporan == 'simpanan' ? 'selected' : '' ?>>Laporan Simpanan</option>
                        <option value="pinjaman" <?= $jenis_laporan == 'pinjaman' ? 'selected' : '' ?>>Laporan Pinjaman</option>
                        <option value="angsuran" <?= $jenis_laporan == 'angsuran' ? 'selected' : '' ?>>Laporan Angsuran</option>
                    </select>
                </div>
                
                <?php if($jenis_laporan == 'anggota'): ?>
                <div class="form-group">
                    <label for="status"><i class="fas fa-user-check"></i> Status Anggota</label>
                    <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                        <option value="all" <?= $filter_status == 'all' ? 'selected' : '' ?>>Semua Status</option>
                        <option value="aktif" <?= $filter_status == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $filter_status == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <?php endif; ?>
                
                <?php if(in_array($jenis_laporan, ['simpanan', 'pinjaman', 'angsuran'])): ?>
                <div class="form-group">
                    <label for="bulan"><i class="fas fa-calendar-alt"></i> Bulan</label>
                    <select name="bulan" id="bulan" class="form-control">
                        <?php 
                        $bulan = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                        foreach($bulan as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $filter_bulan == $key ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tahun"><i class="fas fa-calendar"></i> Tahun</label>
                    <select name="tahun" id="tahun" class="form-control">
                        <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                            <option value="<?= $y ?>" <?= $filter_tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label style="visibility: hidden;">Aksi</label>
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        <?php if(!empty($data_laporan)): ?>
                        <button type="button" class="btn btn-export" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                        <button type="button" class="btn btn-success" onclick="printFormalReport()">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                        <?php endif; ?>
                        <a href="index_laporan.php" class="btn btn-danger">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-table"></i> <?= $judul_laporan ?></h3>
            <small><?= $periode_text ?> • Sumber: <?= $view_name ?></small>
        </div>
        
        <div class="card-body">
   
            <div class="print-only kop-surat">
                <div class="kop-header-1">KOPERASI SIMPAN PINJAM</div>
                <div class="kop-header-2">"AMERTHA"</div>
                <div class="kop-info">
                    JJl. Raya Mekarsari No. 123, Tegal<br>
                    Telp: (0361) 123456 | Fax: (0361) 123457<br>
                    Email: kspamertha@email.com | Website: www.kspamertha.co.id<br>
                    NPWP: 01.234.567.8-901.000
                </div>
                <div class="kop-divider"></div>
            </div>
 
            <div class="print-only judul-laporan">
                <?= $judul_laporan ?>
            </div>

            <div class="print-only subjudul-laporan">
                <?= $subjudul ?>
            </div>

            <div class="print-only info-laporan">
                <table>
                    <tr>
                        <td width="50%"><strong>Periode :</strong> <?= $periode_text ?></td>
                        <td width="50%"><strong>Tanggal Cetak :</strong> <?= date('d F Y') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Laporan :</strong> <?= ucfirst($jenis_laporan) ?></td>
                        <td><strong>Jumlah Data :</strong> <?= count($data_laporan) ?> record</td>
                    </tr>
                </table>
            </div>
            

            <div class="table-responsive">
                <table class="laporan-table">
                    <thead>
                        <tr>
                            <?php foreach($columns as $column): ?>
                                <th><?= $column ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data_laporan)): ?>
                            <tr>
                                <td colspan="<?= count($columns) ?>">
                                    <div class="no-data">
                                        <i class="fas fa-database"></i>
                                        <h4>Tidak ada data ditemukan</h4>
                                        <p>
                                            <?php if(in_array($jenis_laporan, ['simpanan', 'pinjaman', 'angsuran'])): ?>
                                                Coba ganti filter bulan/tahun atau pilih jenis laporan lain.
                                            <?php elseif($jenis_laporan == 'anggota'): ?>
                                                <?= $filter_status !== 'all' ? 'Tidak ada data dengan status ' . $filter_status : 'Data anggota belum tersedia.' ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $counter = 1;
                            $total = 0;
                            foreach($data_laporan as $row): ?>
                                <tr>
                                    <?php 
                                    if($jenis_laporan == 'anggota'): ?>
                                        <td class="text-center"><?= $counter ?></td>
                                        <td><?= $row['no_anggota'] ?? $row['no'] ?? '' ?></td>
                                        <td><strong><?= htmlspecialchars($row['nama_anggota'] ?? $row['nama'] ?? '') ?></strong></td>
                                        <td><?= htmlspecialchars(substr($row['alamat'] ?? '', 0, 25)) ?>...</td>
                                        <td><?= htmlspecialchars($row['no_hp'] ?? $row['telepon'] ?? '') ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['jenis_kelamin'] ?? '') ?></td>
                                        <td><?= !empty($row['tanggal_daftar'] ?? $row['tanggal'] ?? '') ? date('d/m/Y', strtotime($row['tanggal_daftar'] ?? $row['tanggal'] ?? '')) : '' ?></td>
                                        <td class="text-center">
                                            <span class="status-badge <?= (($row['status'] ?? $row['status'] ?? '') == 'aktif') ? 'status-active' : 'status-inactive' ?>">
                                                <?= $row['status'] ?? $row['status'] ?? '' ?>
                                            </span>
                                        </td>
                                    
                                    <?php elseif($jenis_laporan == 'simpanan'): 
                                        $total += $row['jumlah_simpanan'] ?? $row['jumlah'] ?? 0;
                                    ?>
                                        <td class="text-center"><?= $counter ?></td>
                                        <td><?= $row['id_simpanan'] ?? $row['id'] ?? '' ?></td>
                                        <td><strong><?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?></strong></td>
                                        <td><?= !empty($row['tanggal'] ?? '') ? date('d/m/Y', strtotime($row['tanggal'] ?? '')) : '' ?></td>
                                        <td><?= htmlspecialchars($row['jenis_simpanan'] ?? $row['jenis'] ?? '') ?></td>
                                        <td class="text-right"><strong>Rp <?= number_format($row['jumlah_simpanan'] ?? $row['jumlah'] ?? 0, 0, ',', '.') ?></strong></td>
                                        <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
                                    
                                    <?php elseif($jenis_laporan == 'pinjaman'): 
                                        $total += $row['jumlah_pinjaman'] ?? $row['jumlah'] ?? 0;
                                    ?>
                                        <td class="text-center"><?= $counter ?></td>
                                        <td><?= $row['id_pinjaman'] ?? $row['id'] ?? '' ?></td>
                                        <td><strong><?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?></strong></td>
                                        <td><?= !empty($row['tanggal_verifikasi'] ?? '') ? date('d/m/Y', strtotime($row['tanggal_verifikasi'] ?? '')) : '' ?></td>
                                        <td class="text-right"><strong>Rp <?= number_format($row['jumlah_pinjaman'] ?? $row['jumlah'] ?? 0, 0, ',', '.') ?></strong></td>
                                        <td class="text-center"><?= $row['jangka_waktu'] ?? $row['jangka'] ?? 0 ?> bln</td>
                                        <td class="text-center"><?= $row['bunga'] ?? 0 ?>%</td>
                                        <td class="text-center">
                                            <span class="status-badge <?= ($row['status'] ?? '') == 'Disetujui' ? 'status-active' : 'status-inactive' ?>">
                                                <?= $row['status'] ?? '' ?>
                                            </span>
                                        </td>
                                        <td class="text-right">Rp <?= number_format($row['total_angsuran'] ?? $row['angsuran'] ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-right"><strong>Rp <?= number_format($row['sisa_pinjaman'] ?? $row['sisa'] ?? 0, 0, ',', '.') ?></strong></td>
                                    
                                    <?php elseif($jenis_laporan == 'angsuran'): 
                                        $total += $row['jumlah_angsuran'] ?? $row['jumlah'] ?? 0;
                                    ?>
                                        <td class="text-center"><?= $counter ?></td>
                                        <td><?= $row['id_angsuran'] ?? $row['id'] ?? '' ?></td>
                                        <td><strong><?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?></strong></td>
                                        <td><?= !empty($row['tanggal_bayar'] ?? $row['tanggal'] ?? '') ? date('d/m/Y', strtotime($row['tanggal_bayar'] ?? $row['tanggal'] ?? '')) : '' ?></td>
                                        <td class="text-right"><strong>Rp <?= number_format($row['jumlah_angsuran'] ?? $row['jumlah'] ?? 0, 0, ',', '.') ?></strong></td>
                                        <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
                                        <td><?= $row['metode_pembayaran'] ?? $row['metode'] ?? '' ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php 
                            $counter++;
                            endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if(!empty($data_laporan) && in_array($jenis_laporan, ['simpanan', 'pinjaman', 'angsuran'])): ?>
                    <tfoot>
                        <tr>
                            <td colspan="<?= $jenis_laporan == 'simpanan' ? 5 : ($jenis_laporan == 'pinjaman' ? 8 : ($jenis_laporan == 'angsuran' ? 4 : 5)) ?>" class="text-right" style="font-weight: bold;">TOTAL</td>
                            <td colspan="<?= $jenis_laporan == 'simpanan' ? 2 : ($jenis_laporan == 'pinjaman' ? 2 : ($jenis_laporan == 'angsuran' ? 3 : 2)) ?>" class="text-right" style="font-weight: bold;">
                                Rp <?= number_format($total, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
            
            <?php if(!empty($data_laporan)): ?>
            <div class="print-only ttd-area">
                <div class="ttd-nama">
                    Tegal, <?= date('d F Y') ?><br><br><br><br>
                    <span>Ketua Koperasi</span>
                </div>
            </div>
            
            <div class="summary-section">
                <div class="summary-grid">
                    <div class="summary-item">
                        <h4>Total Data</h4>
                        <p><?= count($data_laporan) ?> Record</p>
                    </div>
                    
                    <?php 
                    $total = 0;
                    if($jenis_laporan == 'simpanan') {
                        foreach($data_laporan as $row) {
                            $total += $row['jumlah_simpanan'] ?? $row['jumlah'] ?? 0;
                        }
                        $total_label = 'Total Simpanan';
                    } elseif($jenis_laporan == 'pinjaman') {
                        foreach($data_laporan as $row) {
                            $total += $row['jumlah_pinjaman'] ?? $row['jumlah'] ?? 0;
                        }
                        $total_label = 'Total Pinjaman';
                    } elseif($jenis_laporan == 'angsuran') {
                        foreach($data_laporan as $row) {
                            $total += $row['jumlah_angsuran'] ?? $row['jumlah'] ?? 0;
                        }
                        $total_label = 'Total Angsuran';
                    }
                    
                    if(in_array($jenis_laporan, ['simpanan', 'pinjaman', 'angsuran'])): ?>
                        <div class="summary-item">
                            <h4><?= $total_label ?></h4>
                            <p>Rp <?= number_format($total, 0, ',', '.') ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($jenis_laporan == 'pinjaman'): 
                        $total_angsuran = 0;
                        foreach($data_laporan as $row) {
                            $total_angsuran += $row['total_angsuran'] ?? $row['angsuran'] ?? 0;
                        }
                    ?>
                        <div class="summary-item">
                            <h4>Angsuran Dibayar</h4>
                            <p>Rp <?= number_format($total_angsuran, 0, ',', '.') ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($jenis_laporan == 'anggota'): 
                        $total_aktif = 0;
                        $total_nonaktif = 0;
                        foreach($data_laporan as $row) {
                            if(($row['status'] ?? $row['status'] ?? '') == 'aktif') {
                                $total_aktif++;
                            } else {
                                $total_nonaktif++;
                            }
                        }
                    ?>
                        <div class="summary-item">
                            <h4>Anggota Aktif</h4>
                            <p><?= $total_aktif ?> Org</p>
                        </div>
                        <div class="summary-item">
                            <h4>Anggota Nonaktif</h4>
                            <p><?= $total_nonaktif ?> Org</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        
            <?php endif; ?>
        </div>
    </div>
</div>

<script>

document.getElementById('jenis_laporan').addEventListener('change', function() {
    if(this.value === 'anggota') {
        document.getElementById('bulan').value = '<?= date('m') ?>';
        document.getElementById('tahun').value = '<?= date('Y') ?>';
    }
    this.form.submit();
});

function printFormalReport() {

    const printWindow = window.open('', '_blank');
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title><?= $judul_laporan ?> - KSP Amertha</title>
            <style>
                @page {
                    margin: 1cm;
                    size: A4 portrait;
                }
                
                body {
                    font-family: 'Times New Roman', serif;
                    font-size: 11pt;
                    line-height: 1.4;
                    margin: 0;
                    padding: 0.5cm;
                    color: black;
                }
                
                .kop-surat {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 3px solid #000;
                    padding-bottom: 15px;
                }
                
                .kop-header-1 {
                    font-size: 13pt;
                    font-weight: bold;
                    margin-bottom: 5px;
                    text-transform: uppercase;
                }
                
                .kop-header-2 {
                    font-size: 15pt;
                    font-weight: bold;
                    margin-bottom: 8px;
                    text-decoration: underline;
                }
                
                .kop-info {
                    font-size: 10pt;
                    line-height: 1.4;
                    margin: 5px 0;
                }
                
                .kop-divider {
                    border-bottom: 2px solid #000;
                    margin: 15px 0;
                }
                
                .judul-laporan {
                    text-align: center;
                    font-size: 14pt;
                    font-weight: bold;
                    margin: 20px 0 10px 0;
                    text-transform: uppercase;
                }
                
                .subjudul-laporan {
                    text-align: center;
                    font-size: 12pt;
                    font-weight: bold;
                    margin-bottom: 15px;
                    text-decoration: underline;
                }
                
                .info-laporan {
                    margin: 15px 0;
                    font-size: 10pt;
                    padding: 10px;
                    border: 1px solid #000;
                    background: #f8f8f8;
                }
                
                .info-laporan table {
                    width: 100%;
                    border-collapse: collapse;
                }
                
                .info-laporan td {
                    padding: 5px 10px;
                    border: 1px solid #ddd;
                }
                
                .tanggal-cetak {
                    text-align: right;
                    font-size: 10pt;
                    margin-bottom: 20px;
                }
                
                .laporan-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                    font-size: 10pt;
                }
                
                .laporan-table th {
                    background: #e0e0e0;
                    border: 1px solid #000;
                    padding: 8px 5px;
                    font-weight: bold;
                    text-align: center;
                }
                
                .laporan-table td {
                    border: 1px solid #000;
                    padding: 6px 5px;
                }
                
                .ttd-area {
                    margin-top: 60px;
                    text-align: right;
                    padding-right: 80px;
                }
                
                .ttd-nama {
                    text-align: center;
                    display: inline-block;
                }
                
                .ttd-nama span {
                    font-weight: bold;
                    display: block;
                    margin-top: 70px;
                    border-top: 1px solid #000;
                    padding-top: 5px;
                    width: 200px;
                }
                
                @media print {
                    body {
                        padding: 1cm;
                    }
                }
            </style>
        </head>
        <body>
            <div class="kop-surat">
                <div class="kop-header-1">KOPERASI SIMPAN PINJAM</div>
                <div class="kop-header-2">"AMERTHA"</div>
                <div class="kop-info">
                    Jl. Raya Mekarsari No. 123, Tegal<br>
                    Telp: (0361) 123456 | Fax: (0361) 123457<br>
                    Email: kspamertha@email.com | Website: www.kspamertha.co.id<br>
                    NPWP: 01.234.567.8-901.000
                </div>
                <div class="kop-divider"></div>
            </div>
            
            <div class="judul-laporan">
                <?= $judul_laporan ?>
            </div>
            
            <div class="subjudul-laporan">
                <?= $subjudul ?>
            </div>
            
            <div class="info-laporan">
                <table>
                    <tr>
                        <td width="50%"><strong>Periode:</strong> <?= $periode_text ?></td>
                        <td width="50%"><strong>Tanggal Cetak:</strong> <?= date('d F Y H:i:s') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Laporan:</strong> <?= ucfirst($jenis_laporan) ?></td>
                        <td><strong>Jumlah Data:</strong> <?= count($data_laporan) ?> record</td>
                    </tr>
                </table>
            </div>
            
            <div class="tanggal-cetak">
                Tegal, <?= date('d F Y') ?>
            </div>
            
            <table class="laporan-table">
                <thead>
                    <tr>
                        <?php foreach($columns as $column): ?>
                            <th><?= $column ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data_laporan)): ?>
                        <?php 
                        $counter = 1;
                        $total = 0;
                        foreach($data_laporan as $row): ?>
                            <tr>
                                <?php 
                                if($jenis_laporan == 'anggota'): ?>
                                    <td align="center"><?= $counter ?></td>
                                    <td><?= $row['no_anggota'] ?? $row['no'] ?? '' ?></td>
                                    <td><?= htmlspecialchars($row['nama_anggota'] ?? $row['nama'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['alamat'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['no_hp'] ?? $row['telepon'] ?? '') ?></td>
                                    <td align="center"><?= htmlspecialchars($row['jenis_kelamin'] ?? '') ?></td>
                                    <td align="center"><?= !empty($row['tanggal_daftar'] ?? $row['tanggal'] ?? '') ? date('d/m/Y', strtotime($row['tanggal_daftar'] ?? $row['tanggal'] ?? '')) : '' ?></td>
                                    <td align="center"><?= $row['status'] ?? $row['status'] ?? '' ?></td>
                                
                                <?php elseif($jenis_laporan == 'simpanan'): 
                                    $total += $row['jumlah_simpanan'] ?? $row['jumlah'] ?? 0;
                                ?>
                                    <td align="center"><?= $counter ?></td>
                                    <td><?= $row['id_simpanan'] ?? $row['id'] ?? '' ?></td>
                                    <td><?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?></td>
                                    <td align="center"><?= !empty($row['tanggal'] ?? '') ? date('d/m/Y', strtotime($row['tanggal'] ?? '')) : '' ?></td>
                                    <td><?= htmlspecialchars($row['jenis_simpanan'] ?? $row['jenis'] ?? '') ?></td>
                                    <td align="right">Rp <?= number_format($row['jumlah_simpanan'] ?? $row['jumlah'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
                                
                                <?php elseif($jenis_laporan == 'pinjaman'): 
                                    $total += $row['jumlah_pinjaman'] ?? $row['jumlah'] ?? 0;
                                ?>
                                    <td align="center"><?= $counter ?></td>
                                    <td><?= $row['id_pinjaman'] ?? $row['id'] ?? '' ?></td>
                                    <td><?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?></td>
                                    <td align="center"><?= !empty($row['tanggal_verifikasi'] ?? '') ? date('d/m/Y', strtotime($row['tanggal_verifikasi'] ?? '')) : '' ?></td>
                                    <td align="right">Rp <?= number_format($row['jumlah_pinjaman'] ?? $row['jumlah'] ?? 0, 0, ',', '.') ?></td>
                                    <td align="center"><?= $row['jangka_waktu'] ?? $row['jangka'] ?? 0 ?></td>
                                    <td align="center"><?= $row['bunga'] ?? 0 ?></td>
                                    <td align="center"><?= $row['status'] ?? '' ?></td>
                                    <td align="right">Rp <?= number_format($row['total_angsuran'] ?? $row['angsuran'] ?? 0, 0, ',', '.') ?></td>
                                    <td align="right">Rp <?= number_format($row['sisa_pinjaman'] ?? $row['sisa'] ?? 0, 0, ',', '.') ?></td>
                                
                                <?php elseif($jenis_laporan == 'angsuran'): 
                                    $total += $row['jumlah_angsuran'] ?? $row['jumlah'] ?? 0;
                                ?>
                                    <td align="center"><?= $counter ?></td>
                                    <td><?= $row['id_angsuran'] ?? $row['id'] ?? '' ?></td>
                                    <td><?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?></td>
                                    <td align="center"><?= !empty($row['tanggal_bayar'] ?? $row['tanggal'] ?? '') ? date('d/m/Y', strtotime($row['tanggal_bayar'] ?? $row['tanggal'] ?? '')) : '' ?></td>
                                    <td align="right">Rp <?= number_format($row['jumlah_angsuran'] ?? $row['jumlah'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
                                    <td><?= $row['metode_pembayaran'] ?? $row['metode'] ?? '' ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php 
                        $counter++;
                        endforeach; ?>
                        
                        <?php if(in_array($jenis_laporan, ['simpanan', 'pinjaman', 'angsuran'])): ?>
                        <tr>
                            <td colspan="<?= $jenis_laporan == 'simpanan' ? 5 : ($jenis_laporan == 'pinjaman' ? 8 : ($jenis_laporan == 'angsuran' ? 4 : 5)) ?>" align="right" style="font-weight: bold;">TOTAL</td>
                            <td colspan="<?= $jenis_laporan == 'simpanan' ? 2 : ($jenis_laporan == 'pinjaman' ? 2 : ($jenis_laporan == 'angsuran' ? 3 : 2)) ?>" align="right" style="font-weight: bold;">
                                Rp <?= number_format($total, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="ttd-area">
                <div class="ttd-nama">
                    <br><br><br><br>
                    <span>Ketua Koperasi</span>
                </div>
            </div>
            
            <script>
                window.onload = function() {
                    window.print();
                    // window.close(); // Uncomment untuk auto close setelah print
                }
            <\/script>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
}

async function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    

    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text("KOPERASI SIMPAN PINJAM", 105, 20, { align: 'center' });
    
    doc.setFontSize(16);
    doc.text("\"AMERTHA\"", 105, 28, { align: 'center' });
    
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    const kopInfo = [
        "Jl. Raya Mekarsari No. 123, Tegal",
        "Telp: (0361) 123456 | Fax: (0361) 123457",
        "Email: kspamertha@email.com | Website: www.kspamertha.co.id",
        "NPWP: 01.234.567.8-901.000"
    ];
    
    let yPos = 38;
    kopInfo.forEach(line => {
        doc.text(line, 105, yPos, { align: 'center' });
        yPos += 5;
    });
    
    doc.setLineWidth(0.5);
    doc.line(20, yPos + 2, 190, yPos + 2);

    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text("<?= $judul_laporan ?>", 105, yPos + 12, { align: 'center' });

    doc.setFontSize(11);
    doc.text("<?= $subjudul ?>", 105, yPos + 20, { align: 'center' });

    yPos += 30;
    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    
    doc.rect(20, yPos, 170, 25); // Box info
    doc.setFont('helvetica', 'bold');
    doc.text("Periode:", 25, yPos + 7);
    doc.text("Tanggal Cetak:", 25, yPos + 14);
    doc.text("Jenis Laporan:", 25, yPos + 21);
    doc.text("Jumlah Data:", 110, yPos + 7);
    
    doc.setFont('helvetica', 'normal');
    doc.text("<?= $periode_text ?>", 45, yPos + 7);
    doc.text("<?= date('d F Y H:i:s') ?>", 55, yPos + 14);
    doc.text("<?= ucfirst($jenis_laporan) ?>", 55, yPos + 21);
    doc.text("<?= count($data_laporan) ?> record", 130, yPos + 7);
    
    yPos += 35;
    
    const tableData = [];
    const tableHeaders = <?= json_encode($columns) ?>;
    
    <?php if(!empty($data_laporan)): ?>
        <?php 
        $counter = 1;
        foreach($data_laporan as $row): ?>
            <?php if($jenis_laporan == 'anggota'): ?>
                tableData.push([
                    '<?= $counter ?>',
                    '<?= $row['no_anggota'] ?? $row['no'] ?? '' ?>',
                    '<?= htmlspecialchars($row['nama_anggota'] ?? $row['nama'] ?? '') ?>',
                    '<?= htmlspecialchars(substr($row['alamat'] ?? '', 0, 20)) ?>',
                    '<?= htmlspecialchars($row['no_hp'] ?? $row['telepon'] ?? '') ?>',
                    '<?= htmlspecialchars($row['jenis_kelamin'] ?? '') ?>',
                    '<?= !empty($row['tanggal_daftar'] ?? $row['tanggal'] ?? '') ? date('d/m/Y', strtotime($row['tanggal_daftar'] ?? $row['tanggal'] ?? '')) : '' ?>',
                    '<?= $row['status'] ?? $row['status'] ?? '' ?>'
                ]);
            <?php elseif($jenis_laporan == 'simpanan'): ?>
                tableData.push([
                    '<?= $counter ?>',
                    '<?= $row['id_simpanan'] ?? $row['id'] ?? '' ?>',
                    '<?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?>',
                    '<?= !empty($row['tanggal'] ?? '') ? date('d/m/Y', strtotime($row['tanggal'] ?? '')) : '' ?>',
                    '<?= htmlspecialchars($row['jenis_simpanan'] ?? $row['jenis'] ?? '') ?>',
                    'Rp <?= number_format($row['jumlah_simpanan'] ?? $row['jumlah'] ?? 0, 0, ',', '.') ?>',
                    '<?= htmlspecialchars($row['keterangan'] ?? '') ?>'
                ]);
            <?php elseif($jenis_laporan == 'pinjaman'): ?>
                tableData.push([
                    '<?= $counter ?>',
                    '<?= $row['id_pinjaman'] ?? $row['id'] ?? '' ?>',
                    '<?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?>',
                    '<?= !empty($row['tanggal_verifikasi'] ?? '') ? date('d/m/Y', strtotime($row['tanggal_verifikasi'] ?? '')) : '' ?>',
                    'Rp <?= number_format($row['jumlah_pinjaman'] ?? $row['jumlah'] ?? 0, 0, ',', '.') ?>',
                    '<?= $row['jangka_waktu'] ?? $row['jangka'] ?? 0 ?>',
                    '<?= $row['bunga'] ?? 0 ?>%',
                    '<?= $row['status'] ?? '' ?>',
                    'Rp <?= number_format($row['total_angsuran'] ?? $row['angsuran'] ?? 0, 0, ',', '.') ?>',
                    'Rp <?= number_format($row['sisa_pinjaman'] ?? $row['sisa'] ?? 0, 0, ',', '.') ?>'
                ]);
            <?php elseif($jenis_laporan == 'angsuran'): ?>
                tableData.push([
                    '<?= $counter ?>',
                    '<?= $row['id_angsuran'] ?? $row['id'] ?? '' ?>',
                    '<?= htmlspecialchars($row['nama_lengkap'] ?? $row['nama'] ?? '') ?>',
                    '<?= !empty($row['tanggal_bayar'] ?? $row['tanggal'] ?? '') ? date('d/m/Y', strtotime($row['tanggal_bayar'] ?? $row['tanggal'] ?? '')) : '' ?>',
                    'Rp <?= number_format($row['jumlah_angsuran'] ?? $row['jumlah'] ?? 0, 0, ',', '.') ?>',
                    '<?= htmlspecialchars($row['keterangan'] ?? '') ?>',
                    '<?= $row['metode_pembayaran'] ?? $row['metode'] ?? '' ?>'
                ]);
            <?php endif; ?>
            <?php $counter++; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    doc.autoTable({
        head: [tableHeaders],
        body: tableData,
        startY: yPos,
        margin: { left: 20, right: 20 },
        styles: { fontSize: 8, cellPadding: 2 },
        headStyles: { fillColor: [200, 200, 200], textColor: [0, 0, 0], fontStyle: 'bold' },
        columnStyles: {
            0: { cellWidth: 'auto' },
            4: { cellWidth: 'auto' }
        }
    });
    
    const finalY = doc.lastAutoTable.finalY || yPos;
    doc.text("Tegal, <?= date('d F Y') ?>", 140, finalY + 20);
    doc.text("Ketua Koperasi", 140, finalY + 40);
    doc.setLineWidth(0.5);
    doc.line(120, finalY + 50, 160, finalY + 50);
    
    doc.save('Laporan_<?= ucfirst($jenis_laporan) ?>_KSP_Amertha_<?= date('Ymd_His') ?>.pdf');
}

window.onbeforeprint = function() {
    const printElements = document.querySelectorAll('.print-only');
    printElements.forEach(el => {
        el.style.display = 'block';
    });
    
    const nonPrintElements = document.querySelectorAll('.summary-section, .card-header, .filter-form, .header, .sidebar');
    nonPrintElements.forEach(el => {
        el.style.display = 'none';
    });
    
    document.querySelector('.content').style.marginLeft = '0';
};

window.onafterprint = function() {
    const printElements = document.querySelectorAll('.print-only');
    printElements.forEach(el => {
        el.style.display = 'none';
    });
    
    const nonPrintElements = document.querySelectorAll('.summary-section, .card-header, .filter-form, .header, .sidebar');
    nonPrintElements.forEach(el => {
        el.style.display = '';
    });
    
    document.querySelector('.content').style.marginLeft = 'var(--sidebar-width)';
};
</script>

</body>
</html>
<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../auth/login.php");
    exit;
}

include __DIR__ . "/../../config/koneksi.php";

function getValue($conn, $sql) {
    $q = mysqli_query($conn, $sql);
    $r = mysqli_fetch_row($q);
    return $r[0] ?? 0;
}

function rupiah($angka) {
    if ($angka == 0) return "Rp 0";
    return "Rp " . number_format($angka, 0, ',', '.');
}

$total_anggota  = getValue($conn, "SELECT COUNT(*) FROM anggota");
$total_simpanan = getValue($conn, "SELECT COALESCE(SUM(jumlah_simpanan), 0) FROM simpanan");
$total_pinjaman = getValue($conn, "SELECT COALESCE(SUM(jumlah_pinjaman), 0) FROM pinjaman");
$total_angsuran = getValue($conn, "SELECT COALESCE(SUM(jumlah_angsuran), 0) FROM angsuran");

$bulan = date('m');
$tahun = date('Y');

$simpanan_bulan_ini = getValue($conn, "
    SELECT COALESCE(SUM(jumlah_simpanan), 0)
    FROM simpanan
    WHERE MONTH(tanggal)=$bulan AND YEAR(tanggal)=$tahun
");

$pinjaman_bulan_ini = getValue($conn, "
    SELECT COALESCE(SUM(jumlah_pinjaman), 0)
    FROM pinjaman
    WHERE MONTH(tanggal_verifikasi)=$bulan
    AND YEAR(tanggal_verifikasi)=$tahun
    AND status='Disetujui'
");

$angsuran_bulan_ini = getValue($conn, "
    SELECT COALESCE(SUM(jumlah_angsuran), 0)
    FROM angsuran
    WHERE MONTH(tanggal_bayar)=$bulan
    AND YEAR(tanggal_bayar)=$tahun
");

$labels = [];
$jumlah_simpanan = [];
$jumlah_pinjaman = [];
$jumlah_angsuran = [];

for ($i = 2; $i >= 0; $i--) {
    $month = date('m', strtotime("-$i months"));
    $year = date('Y', strtotime("-$i months"));
    $month_name = date('M', strtotime("-$i months"));
    
    $labels[] = $month_name;

    $simpanan_count = getValue($conn, "
        SELECT COUNT(*)
        FROM simpanan
        WHERE MONTH(tanggal)=$month AND YEAR(tanggal)=$year
    ");
    $jumlah_simpanan[] = $simpanan_count;

    $pinjaman_count = getValue($conn, "
        SELECT COUNT(*)
        FROM pinjaman
        WHERE MONTH(tanggal_verifikasi)=$month
        AND YEAR(tanggal_verifikasi)=$year
        AND status='Disetujui'
    ");
    $jumlah_pinjaman[] = $pinjaman_count;
    
    $angsuran_count = getValue($conn, "
        SELECT COUNT(*)
        FROM angsuran
        WHERE MONTH(tanggal_bayar)=$month
        AND YEAR(tanggal_bayar)=$tahun
    ");
    $jumlah_angsuran[] = $angsuran_count;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - Koperasi</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../../assets/css/sidebar_admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
*{margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: 'Open Sans', 'Arial', sans-serif;
    }

body{
    background:#f4f6f8;
}

.content{
    margin-left:240px;
    min-height:100vh;
    padding:30px;
}

.dashboard-header{
    background:#2554bbff;
    color:#fff;
    padding:10px 25px;
    border-radius:12px;
    font-size:14px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 10px rgba(0,0,0,.15);
}

.header-right{
    display:flex;
    align-items:center;
    gap:8px;
    background:rgba(255,255,255,.2);
    padding:8px 14px;
    border-radius:8px;
}

.welcome-box{
    margin-top:20px;
    background:#e3f2fd;
    color:#0d47a1;
    padding:15px 20px;
    border-radius:12px;
}

.cards{
    margin-top:25px;
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(220px,1fr));
    gap:20px;
}

.card{
    background:#fff;
    padding:20px;
    border-radius:14px;
    box-shadow:0 6px 12px rgba(0,0,0,.1);
    text-align:center;
    transition:.25s;
}

.card:hover{
    transform:translateY(-5px);
}

.card h3{
    font-size:17px;
    margin-bottom:10px;
    color:#555;
}

.card p{
    font-size:20px;
    font-weight:bold;
    color:#2554bb;
    margin:5px 0;
}

.card small{
    font-size:14px;
    color:#888;
    display:block;
    margin-top:5px;
}

.chart-box{
    background:#fff;
    margin-top:30px;
    padding:20px;
    border-radius:14px;
    box-shadow:0 6px 12px rgba(0,0,0,.1);
}

.chart-wrapper{
    height:220px;
}

.simple-stats{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(200px,1fr));
    gap:15px;
    margin-top:20px;
}

.stat-item{
    background:#fff;
    padding:15px;
    border-radius:10px;
    box-shadow:0 4px 8px rgba(0,0,0,.08);
    text-align:center;
}

.stat-item h4{
    font-size:14px;
    color:#666;
    margin-bottom:5px;
}

.stat-item p{
    font-size:16px;
    font-weight:bold;
    color:#2554bb;
}
</style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="../../assets/img/logo_koperasi.png" alt="Logo">
        <span>Koperasi<br>Simpan Pinjam</span>
    </div>

    <nav>
        <a class="active" href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="../anggota/index_anggota.php"><i class="fas fa-users"></i> Anggota</a>
        <a href="../simpanan/index_simpanan.php"><i class="fas fa-wallet"></i> Simpanan</a>
        <a href="../pinjaman/index_pinjaman.php"><i class="fas fa-hand-holding-usd"></i> Pinjaman</a>
        <a href="../angsuran/index_angsuran.php"><i class="fas fa-calendar-check"></i> Angsuran</a>
        <a href="../laporan/index_laporan.php"><i class="fas fa-file-alt"></i> Laporan</a>
        <a href="../../auth/logout.php" class="logout"
           onclick="return confirm('Yakin ingin logout?')">
            <i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="content">

    <div class="dashboard-header">
        <h2>Dashboard Admin</h2>
        <div class="header-right">
            <i class="fas fa-user-circle"></i>
            <span><?= $_SESSION['username']; ?></span>
        </div>
    </div>

    <div class="welcome-box">
        Selamat datang kembali, <b><?= $_SESSION['username']; ?></b>.
        Semoga hari Anda menyenangkan 🌤
    </div>

    <div class="cards">
        <div class="card">
            <h3>Total Anggota</h3>
            <p><?= $total_anggota ?></p>
            <small>Orang</small>
        </div>
        <div class="card">
            <h3>Total Simpanan</h3>
            <p><?= rupiah($total_simpanan) ?></p>
            <small>Total Uang</small>
        </div>
        <div class="card">
            <h3>Total Pinjaman</h3>
            <p><?= rupiah($total_pinjaman) ?></p>
            <small>Total Uang</small>
        </div>
        <div class="card">
            <h3>Total Angsuran</h3>
            <p><?= rupiah($total_angsuran) ?></p>
            <small>Total Uang</small>
        </div>
    </div>

    <div class="simple-stats">
        <div class="stat-item">
            <h4>Simpanan Bulan Ini</h4>
            <p><?= rupiah($simpanan_bulan_ini) ?></p>
        </div>
        <div class="stat-item">
            <h4>Pinjaman Bulan Ini</h4>
            <p><?= rupiah($pinjaman_bulan_ini) ?></p>
        </div>
        <div class="stat-item">
            <h4>Angsuran Bulan Ini</h4>
            <p><?= rupiah($angsuran_bulan_ini) ?></p>
        </div>
        <div class="stat-item">
            <h4>Bulan</h4>
            <p><?= date('F Y') ?></p>
        </div>
    </div>

    <div class="chart-box">
        <h3>Jumlah Transaksi 3 Bulan Terakhir</h3>
        <p style="color:#666; font-size:14px; margin-bottom:10px;">Menunjukkan berapa kali transaksi terjadi tiap bulan</p>
        <div class="chart-wrapper">
            <canvas id="myChart"></canvas>
        </div>
    </div>

</div>

<script>
const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
            {
                label: 'Jumlah Simpanan',
                data: <?= json_encode($jumlah_simpanan) ?>,
                backgroundColor: '#28a745',
                borderColor: '#218838',
                borderWidth: 1
            },
            {
                label: 'Jumlah Pinjaman',
                data: <?= json_encode($jumlah_pinjaman) ?>,
                backgroundColor: '#ffc107',
                borderColor: '#e0a800',
                borderWidth: 1
            },
            {
                label: 'Jumlah Angsuran',
                data: <?= json_encode($jumlah_angsuran) ?>,
                backgroundColor: '#17a2b8',
                borderColor: '#138496',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1, 
                    callback: function(value) {
                        return value + ' transaksi';
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.parsed.y + ' kali';
                        return label;
                    }
                }
            }
        }
    }
});
</script>

</body>
</html>
<?php
session_start();

// Cek role bendahara
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Bendahara') {
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

function formatDate($date) {
    if (empty($date) || $date == '0000-00-00') return '-';
    return date('d M Y', strtotime($date));
}

// =================== A. RINGKASAN KEUANGAN ===================
// 1. Saldo Kas (total kas masuk - total kas keluar)
$saldo_kas = getValue($conn, "
    SELECT 
        (COALESCE(SUM(CASE WHEN jenis = 'Masuk' THEN jumlah END), 0) - 
         COALESCE(SUM(CASE WHEN jenis = 'Keluar' THEN jumlah END), 0)) AS saldo
    FROM transaksi_keuangan
");

// 2. Total Kas Masuk (bulan ini)
$bulan = date('m');
$tahun = date('Y');
$kas_masuk_bulan_ini = getValue($conn, "
    SELECT COALESCE(SUM(jumlah), 0)
    FROM transaksi_keuangan
    WHERE jenis = 'Masuk' 
    AND MONTH(tanggal) = $bulan 
    AND YEAR(tanggal) = $tahun
");

// 3. Total Kas Keluar (bulan ini)
$kas_keluar_bulan_ini = getValue($conn, "
    SELECT COALESCE(SUM(jumlah), 0)
    FROM transaksi_keuangan
    WHERE jenis = 'Keluar'
    AND MONTH(tanggal) = $bulan 
    AND YEAR(tanggal) = $tahun
");

// 4. Jumlah Transaksi Hari Ini
$hari_ini = date('Y-m-d');
$transaksi_hari_ini = getValue($conn, "
    SELECT COUNT(*)
    FROM transaksi_keuangan
    WHERE DATE(tanggal) = '$hari_ini'
");

// =================== B. NOTIFIKASI / TUGAS ===================
// 1. Pengajuan pinjaman menunggu verifikasi
$menunggu = getValue($conn, "
    SELECT COUNT(*)
    FROM pinjaman
    WHERE status = 'menunggu'
");

// Ambil detail pinjaman yang menunggu verifikasi
$pinjaman_pending = [];
if ($menunggu > 0) {
    $query = mysqli_query($conn, "
        SELECT 
            p.id_pinjaman,
            a.nama_anggota,
            p.jumlah_pinjaman,
            p.tenor,
            p.bunga,
            p.status,
            p.tanggal_pengajuan,
            p.diajukan_oleh
        FROM pinjaman p
        JOIN anggota a ON p.id_anggota = a.id_anggota
        WHERE p.status = 'menunggu'
        ORDER BY p.tanggal_pengajuan ASC
        LIMIT 3
    ");
    while ($row = mysqli_fetch_assoc($query)) {
        $pinjaman_pending[] = $row;
    }
}

// 2. Angsuran jatuh tempo hari ini
$angsuran_jatuh_tempo = getValue($conn, "
    SELECT COUNT(*)
    FROM angsuran
    WHERE tanggal_bayar = CURDATE()
    AND status = 'Belum Lunas'
");

// Ambil detail angsuran jatuh tempo
$angsuran_due = [];
if ($angsuran_jatuh_tempo > 0) {
    $query = mysqli_query($conn, "
        SELECT 
            a.id,
            ag.nama,
            a.jumlah_angsuran,
            a.tanggal_bayar,
            p.id_pinjaman
        FROM angsuran a
        JOIN pinjaman p ON a.id_pinjaman = p.id_pinjaman
        JOIN anggota ag ON p.id_anggota = ag.id
        WHERE a.tanggal_bayar = CURDATE()
        AND a.status = 'Belum Lunas'
        ORDER BY a.tanggal_bayar ASC
        LIMIT 3
    ");
    while ($row = mysqli_fetch_assoc($query)) {
        $angsuran_due[] = $row;
    }
}

// 3. Data statistik tambahan
$total_pinjaman_aktif = getValue($conn, "
    SELECT COUNT(*)
    FROM pinjaman
    WHERE status = 'disetujui'
");

$total_simpanan_bulan_ini = getValue($conn, "
    SELECT COALESCE(SUM(jumlah), 0)
    FROM transaksi_keuangan
    WHERE jenis = 'Masuk'
    AND keterangan LIKE '%simpanan%'
    AND MONTH(tanggal) = $bulan
    AND YEAR(tanggal) = $tahun
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Bendahara - Koperasi</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../../assets/css/sidebar_bendahara.css">

<style>
/* HANYA CSS UNTUK KONTEN DASHBOARD SAJA */
/* CSS untuk sidebar sudah dipindah ke file terpisah */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Open Sans', 'Arial', sans-serif;
}

body {
    background: #f4f6f8;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.content {
    margin-left: 250px;
    flex: 1;
    padding: 20px;
}

/* HEADER */
.dashboard-header {
    background: #0d7484ff;
    color: #fff;
    padding: 10px 20px;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 8px rgba(0,0,0,.15);
    margin-bottom: 15px;
}

.dashboard-header h2 {
    font-size: 18px;
    font-weight: 600;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,.2);
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
}

.header-right i {
    font-size: 16px;
}

.welcome-box {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #0d47a1;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 4px solid #2554bb;
    font-size: 13px;
}

.welcome-box h3 {
    font-size: 14px;
    margin-bottom: 5px;
}

.welcome-box b {
    color: #2554bb;
}

.welcome-box p {
    font-size: 12px;
    opacity: 0.9;
}

/* CARDS - LEBIH KECIL */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.card {
    background: #fff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,.1);
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
    border-top: 3px solid;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,.15);
}

.card-icon {
    font-size: 22px;
    margin-bottom: 10px;
    opacity: 0.8;
}

.card h3 {
    font-size: 13px;
    margin-bottom: 8px;
    color: #555;
    font-weight: 600;
}

.card p {
    font-size: 18px;
    font-weight: bold;
    margin: 5px 0;
    line-height: 1.3;
}

.card small {
    font-size: 11px;
    color: #777;
    display: block;
    margin-top: 5px;
}

/* Card Colors */
.card:nth-child(1) { border-color: #2554bb; }
.card:nth-child(2) { border-color: #28a745; }
.card:nth-child(3) { border-color: #dc3545; }
.card:nth-child(4) { border-color: #6f42c1; }

.saldo-kas { color: #2554bb; }
.kas-masuk { color: #28a745; }
.kas-keluar { color: #dc3545; }
.transaksi { color: #6f42c1; }

/* NOTIFIKASI - FONT LEBIH KECIL */
.notifikasi-box {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,.1);
    margin-bottom: 20px;
}

.notifikasi-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f0;
}

.notif-title-section {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #2554bb;
}

.notif-title-section i {
    font-size: 18px;
}

.notif-title-section h3 {
    font-size: 16px;
    font-weight: 600;
}

.notif-count-badge {
    background: #2554bb;
    color: #fff;
    padding: 3px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}

.notifikasi-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.notif-item {
    padding: 15px;
    border-radius: 8px;
    background: #f8f9fa;
    transition: transform 0.3s;
}

.notif-item:hover {
    transform: translateY(-2px);
}

.notif-item.warning {
    border-left: 4px solid #ffc107;
    background: #fff8e1;
}

.notif-item.danger {
    border-left: 4px solid #dc3545;
    background: #fdecea;
}

.notif-item-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.notif-item-header i {
    font-size: 16px;
    width: 24px;
}

.notif-item-title {
    font-weight: bold;
    font-size: 14px;
    color: #333;
}

.notif-item-body {
    margin-left: 34px;
    font-size: 12px;
}

.notif-item-body > p {
    margin-bottom: 10px;
    font-size: 12px;
}

.notif-detail-list {
    margin-top: 10px;
}

.notif-detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
    font-size: 12px;
}

.notif-detail-item:last-child {
    border-bottom: none;
}

.notif-name {
    color: #555;
    font-weight: 500;
    font-size: 12px;
}

.notif-info {
    font-size: 11px;
    color: #888;
    margin-top: 2px;
    line-height: 1.4;
}

.notif-amount {
    font-weight: bold;
    color: #2554bb;
    text-align: right;
    font-size: 12px;
}

.notif-action {
    margin-top: 10px;
    text-align: right;
}

.btn-view {
    background: #2554bb;
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    font-size: 12px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background 0.3s;
}

.btn-view:hover {
    background: #1e429f;
    color: #fff;
    text-decoration: none;
}

/* STATISTIK TAMBAHAN - LEBIH KECIL */
.stats-container {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,.1);
    margin-top: 15px;
    margin-bottom: 15px;
}

.stats-header {
    color: #2554bb;
    margin-bottom: 15px;
    font-size: 16px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
}

.stat-box {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    border-left: 3px solid #2554bb;
}

.stat-box h4 {
    font-size: 12px;
    color: #666;
    margin-bottom: 6px;
}

.stat-box p {
    font-size: 14px;
    font-weight: bold;
    color: #2554bb;
}

/* FOOTER */
.footer {
    margin-left: 240px;
    background: #286a7eff;
    color: white;
    padding: 15px 20px;
    text-align: center;
    font-size: 12px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
}

.footer-left {
    text-align: left;
}

.footer-right {
    text-align: right;
}

.footer a {
    color: #bbdefb;
    text-decoration: none;
}

.footer a:hover {
    color: white;
    text-decoration: underline;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 20px;
    color: #888;
    font-size: 12px;
}

.empty-state i {
    font-size: 36px;
    margin-bottom: 10px;
    opacity: 0.5;
}

.empty-state p {
    font-size: 13px;
}

/* Responsive */
@media (max-width: 768px) {
    .content {
        margin-left: 0;
        padding: 15px;
    }
    
    .footer {
        margin-left: 0;
    }
    
    .footer-content {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    
    .footer-left, .footer-right {
        text-align: center;
    }
    
    .cards {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .notifikasi-list {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .cards {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>
<?php include 'sidebar_bendahara.php'; ?>

<div class="content">

    <div class="dashboard-header">
        <h2>Dashboard Bendahara</h2>
        <div class="header-right">
            <i class="fas fa-user-circle"></i>
            <span><?= htmlspecialchars($_SESSION['username']); ?></span>
        </div>
    </div>

    <div class="welcome-box">
        <h3>Selamat datang kembali, <b><?= htmlspecialchars($_SESSION['username']); ?></b>!</h3>
        <p>Tanggal: <?= date('d F Y'); ?></p>
    </div>

    <!-- A. RINGKASAN KEUANGAN -->
    <div class="cards">
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <h3>Saldo Kas</h3>
            <p class="saldo-kas"><?= rupiah($saldo_kas) ?></p>
            <small>Total saldo kas saat ini</small>
        </div>
        
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-arrow-down"></i>
            </div>
            <h3>Kas Masuk (Bulan Ini)</h3>
            <p class="kas-masuk"><?= rupiah($kas_masuk_bulan_ini) ?></p>
            <small>Penerimaan bulan <?= date('F'); ?></small>
        </div>
        
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-arrow-up"></i>
            </div>
            <h3>Kas Keluar (Bulan Ini)</h3>
            <p class="kas-keluar"><?= rupiah($kas_keluar_bulan_ini) ?></p>
            <small>Pengeluaran bulan <?= date('F'); ?></small>
        </div>
        
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <h3>Transaksi Hari Ini</h3>
            <p class="transaksi"><?= $transaksi_hari_ini ?> transaksi</p>
            <small>Tanggal <?= date('d/m/Y'); ?></small>
        </div>
    </div>

    <!-- B. NOTIFIKASI / TUGAS -->
    <div class="notifikasi-box">
        <div class="notifikasi-header">
            <div class="notif-title-section">
                <i class="fas fa-tasks"></i>
                <h3>Tugas dan Notifikasi</h3>
            </div>
            <div class="notif-count-badge">
                <?= ($menunggu + $angsuran_jatuh_tempo) ?> Tugas
            </div>
        </div>
        
        <div class="notifikasi-list">
            <!-- Verifikasi Pinjaman -->
            <div class="notif-item warning">
                <div class="notif-item-header">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <div class="notif-item-title">Verifikasi Pinjaman</div>
                </div>
                <div class="notif-item-body">
                    <?php if($menunggu > 0): ?>
                        <p>Ada <b><?= $menunggu ?> pengajuan pinjaman</b> yang membutuhkan verifikasi:</p>
                        <div class="notif-detail-list">
                            <?php foreach($pinjaman_pending as $pinjaman): ?>
                                <div class="notif-detail-item">
                                    <div>
                                        <div class="notif-name"><?= htmlspecialchars($pinjaman['nama_anggota']) ?></div>
                                        <div class="notif-info">
                                            Tenor: <?= $pinjaman['tenor'] ?> bln | 
                                            Bunga: <?= $pinjaman['bunga'] ?>% |
                                            <?= formatDate($pinjaman['tanggal_pengajuan']) ?>
                                        </div>
                                    </div>
                                    <div class="notif-amount"><?= rupiah($pinjaman['jumlah_pinjaman']) ?></div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if($menunggu > 3): ?>
                                <div class="notif-detail-item">
                                    <div class="notif-name">Dan <?= $menunggu - 3 ?> pengajuan lainnya...</div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="notif-action">
                            <a href="../verifikasi_pinjaman/verifikasi_pinjaman.php" class="btn-view">
                                <i class="fas fa-check-circle"></i> Verifikasi
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <p>Tidak ada pengajuan pinjaman yang menunggu</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Angsuran Jatuh Tempo -->
            <div class="notif-item danger">
                <div class="notif-item-header">
                    <i class="fas fa-calendar-times"></i>
                    <div class="notif-item-title">Angsuran Jatuh Tempo</div>
                </div>
                <div class="notif-item-body">
                    <?php if($angsuran_jatuh_tempo > 0): ?>
                        <p>Ada <b><?= $angsuran_jatuh_tempo ?> angsuran</b> yang jatuh tempo hari ini:</p>
                        <div class="notif-detail-list">
                            <?php foreach($angsuran_due as $angsuran): ?>
                                <div class="notif-detail-item">
                                    <div>
                                        <div class="notif-name"><?= htmlspecialchars($angsuran['nama']) ?></div>
                                        <div class="notif-info">
                                            ID: <?= $angsuran['id_pinjaman'] ?>
                                        </div>
                                    </div>
                                    <div class="notif-amount"><?= rupiah($angsuran['jumlah_angsuran']) ?></div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if($angsuran_jatuh_tempo > 3): ?>
                                <div class="notif-detail-item">
                                    <div class="notif-name">Dan <?= $angsuran_jatuh_tempo - 3 ?> lainnya...</div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="notif-action">
                            <a href="../angsuran/index_angsuran.php" class="btn-view">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-check"></i>
                            <p>Tidak ada angsuran jatuh tempo hari ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- C. STATISTIK TAMBAHAN -->
    <div class="stats-container">
        <div class="stats-header">
            <i class="fas fa-chart-line"></i>
            <h3>Statistik Lainnya</h3>
        </div>
        <div class="stats-grid">
            <div class="stat-box">
                <h4>Simpanan Bulan Ini</h4>
                <p><?= rupiah($total_simpanan_bulan_ini) ?></p>
            </div>
            <div class="stat-box">
                <h4>Pinjaman Aktif</h4>
                <p><?= $total_pinjaman_aktif ?> pinjaman</p>
            </div>
            <div class="stat-box">
                <h4>Pengajuan Menunggu</h4>
                <p><?= $menunggu ?> pinjaman</p>
            </div>
            <div class="stat-box">
                <h4>Angsuran Jatuh Tempo</h4>
                <p><?= $angsuran_jatuh_tempo ?> angsuran</p>
            </div>
        </div>
    </div>

</div>

<!-- FOOTER -->
<div class="footer">
    <div class="footer-content">
        <div class="footer-left">
            <p>&copy; <?= date('Y'); ?> Koperasi Simpan Pinjam.</p>
        </div>
        <div class="footer-right">
            <p>
                <i class="fas fa-user"></i>  
                | <a href="dashboard_bendahara.php">Dashboard</a>
            </p>
        </div>
    </div>
</div>

<script>
// Animasi sederhana untuk cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
        card.style.animation = 'fadeInUp 0.5s ease forwards';
        card.style.opacity = '0';
    });
    
    // Tambahkan style untuk animasi
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
});

// Update waktu di footer setiap menit
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: false 
    });
    
    const timeElement = document.querySelector('.fa-clock');
    if (timeElement) {
        const parent = timeElement.parentElement;
        parent.innerHTML = `<i class="fas fa-clock"></i> ${timeString}`;
    }
}

setInterval(updateTime, 60000);
</script>

</body>
</html>
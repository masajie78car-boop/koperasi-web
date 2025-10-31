<?php
include 'functions.php';
requireLogin();

if (getUserRole() !== 'bendahara' && getUserRole() !== 'ketua') {
    header('Location: dashboard.php');
    exit;
}

$pdo = connectDB();

$stmt = $pdo->query("SELECT SUM(jumlah) as total FROM simpanan");
$total_simpanan = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$stmt = $pdo->query("SELECT SUM(jumlah_pinjam) as total FROM pinjaman");
$total_pinjaman = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$stmt = $pdo->query("SELECT SUM(jumlah) as total FROM angsuran");
$total_angsuran = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$stmt = $pdo->query("SELECT SUM(jumlah_pinjam - jumlah_cicil) as sisa FROM pinjaman WHERE status != 'lunas'");
$sisa_pinjaman = $stmt->fetch(PDO::FETCH_ASSOC)['sisa'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as jumlah FROM anggota");
$jumlah_anggota = $stmt->fetch(PDO::FETCH_ASSOC)['jumlah'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - Koperasi</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/jspdf.min.js"></script>
    <script src="assets/js/main.js"></script>
</head>
<body>
    <div class="container">
        <h1>Laporan Keuangan Koperasi</h1>

        <div class="summary">
            <h2>Ringkasan</h2>
            <p>Total Anggota: <?= $jumlah_anggota ?></p>
            <p>Total Simpanan: <strong>Rp <?= number_format($total_simpanan, 2) ?></strong></p>
            <p>Total Pinjaman Diberikan: <strong>Rp <?= number_format($total_pinjaman, 2) ?></strong></p>
            <p>Total Angsuran Diterima: <strong>Rp <?= number_format($total_angsuran, 2) ?></strong></p>
            <p>Sisa Pinjaman Belum Lunas: <strong>Rp <?= number_format($sisa_pinjaman, 2) ?></strong></p>
        </div>

        <hr>

        <h2>Daftar Simpanan</h2>
        <input type="text" id="searchSimpanan" onkeyup="searchTable('searchSimpanan', 'tableSimpanan')" placeholder="Cari simpanan...">
        <button onclick="printTableAsPDF('#tableSimpanan', 'Laporan_Simpanan')">Cetak PDF</button>
        <table id="tableSimpanan">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>No Anggota</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $stmt = $pdo->query("
                SELECT s.jumlah, s.jenis, s.tanggal, a.nama, a.no_anggota
                FROM simpanan s
                JOIN anggota a ON s.anggota_id = a.id
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['no_anggota']) ?></td>
                <td><?= htmlspecialchars($row['jenis']) ?></td>
                <td>Rp <?= number_format($row['jumlah'], 2) ?></td>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Daftar Pinjaman</h2>
        <input type="text" id="searchPinjaman" onkeyup="searchTable('searchPinjaman', 'tablePinjaman')" placeholder="Cari pinjaman...">
        <button onclick="printTableAsPDF('#tablePinjaman', 'Laporan_Pinjaman')">Cetak PDF</button>
        <table id="tablePinjaman">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>No Anggota</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Jumlah Cicil</th>
                    <th>Sisa</th>
                    <th>Status</th>
                    <th>Tgl Jatuh Tempo</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $stmt = $pdo->query("
                SELECT p.jumlah_pinjam, p.jumlah_cicil, p.status, p.tgl_jatuh_tempo, a.nama, a.no_anggota
                FROM pinjaman p
                JOIN anggota a ON p.anggota_id = a.id
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                $sisa = $row['jumlah_pinjam'] - $row['jumlah_cicil'];
            ?>
            <tr>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['no_anggota']) ?></td>
                <td>Rp <?= number_format($row['jumlah_pinjam'], 2) ?></td>
                <td>Rp <?= number_format($row['jumlah_cicil'], 2) ?></td>
                <td>Rp <?= number_format($sisa, 2) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['tgl_jatuh_tempo']) ?></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php">Kembali ke Dashboard</a>
    </div>
</body>
</html>
<?php
include 'functions.php';
requireLogin();

$user = $_SESSION['user'];
$role = $user['role'];
$pdo = connectDB();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Koperasi</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <header>
            <h1>Selamat Datang, <?= htmlspecialchars($user['username']) ?> (<?= ucfirst($role) ?>)</h1>
            <a href="logout.php">Logout</a>
        </header>

        <main>
            <?php if ($role === 'bendahara' || $role === 'ketua'): ?>
                <h2>Pencatatan Keuangan</h2>
                <a href="simpanan.php">Input Simpanan</a> |
                <a href="pinjaman.php">Input Pinjaman</a> |
                <a href="angsuran.php">Input Angsuran</a> |
                <a href="laporan.php">Laporan Keuangan</a> |
                <a href="backup.php">Backup Data</a>
            <?php endif; ?>

            <?php if ($role === 'anggota'): ?>
                <h2>Informasi Pribadi</h2>
                <p>Saldo Simpanan: Rp 500.000</p>
                <p>Pinjaman Aktif: Rp 2.000.000</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
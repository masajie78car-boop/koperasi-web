<?php
include 'functions.php';
requireLogin();

if (getUserRole() !== 'bendahara' && getUserRole() !== 'ketua') {
    header('Location: dashboard.php');
    exit;
}

$pdo = connectDB();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anggota_id = $_POST['anggota_id'];
    $jumlah_pinjam = $_POST['jumlah_pinjam'];
    $tgl_jatuh_tempo = $_POST['tgl_jatuh_tempo'];

    $stmt = $pdo->prepare("INSERT INTO pinjaman (anggota_id, jumlah_pinjam, tgl_jatuh_tempo, tgl_pinjam) VALUES (?, ?, ?, CURDATE())");
    if ($stmt->execute([$anggota_id, $jumlah_pinjam, $tgl_jatuh_tempo])) {
        $message = 'Pinjaman berhasil ditambahkan.';
    } else {
        $message = 'Gagal menambahkan pinjaman.';
    }
}

$anggota_list = $pdo->query("SELECT id, nama, no_anggota FROM anggota")->fetchAll();
$pinjaman_list = $pdo->query("
    SELECT p.*, a.nama, a.no_anggota
    FROM pinjaman p
    JOIN anggota a ON p.anggota_id = a.id
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Pinjaman</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Input Pinjaman</h1>
        <?php if ($message): ?>
            <p class="success"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST">
            <select name="anggota_id" required>
                <option value="">Pilih Anggota</option>
                <?php foreach ($anggota_list as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nama']) ?> (<?= htmlspecialchars($a['no_anggota']) ?>)</option>
                <?php endforeach; ?>
            </select><br>
            <input type="number" name="jumlah_pinjam" placeholder="Jumlah Pinjaman" required><br>
            <input type="date" name="tgl_jatuh_tempo" required><br>
            <button type="submit">Simpan</button>
        </form>

        <h2>Daftar Pinjaman</h2>
        <table>
            <tr>
                <th>Nama</th>
                <th>No Anggota</th>
                <th>Jumlah Pinjaman</th>
                <th>Sisa Cicilan</th>
                <th>Status</th>
                <th>Tgl Jatuh Tempo</th>
            </tr>
            <?php foreach ($pinjaman_list as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nama']) ?></td>
                <td><?= htmlspecialchars($p['no_anggota']) ?></td>
                <td>Rp <?= number_format($p['jumlah_pinjam'], 2) ?></td>
                <td>Rp <?= number_format($p['jumlah_pinjam'] - $p['jumlah_cicil'], 2) ?></td>
                <td><?= htmlspecialchars($p['status']) ?></td>
                <td><?= htmlspecialchars($p['tgl_jatuh_tempo']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <a href="dashboard.php">Kembali ke Dashboard</a>
    </div>
</body>
</html>
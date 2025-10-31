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
    $jenis = $_POST['jenis'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];

    $stmt = $pdo->prepare("INSERT INTO simpanan (anggota_id, jenis, jumlah, tanggal) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$anggota_id, $jenis, $jumlah, $tanggal])) {
        $message = 'Simpanan berhasil ditambahkan.';
    } else {
        $message = 'Gagal menambahkan simpanan.';
    }
}

$anggota_list = $pdo->query("SELECT id, nama, no_anggota FROM anggota")->fetchAll();
$simpanan_list = $pdo->query("
    SELECT s.*, a.nama, a.no_anggota
    FROM simpanan s
    JOIN anggota a ON s.anggota_id = a.id
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Simpanan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Input Simpanan</h1>
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
            <select name="jenis" required>
                <option value="wajib">Wajib</option>
                <option value="pokok">Pokok</option>
                <option value="sukarela">Sukarela</option>
            </select><br>
            <input type="number" name="jumlah" placeholder="Jumlah" required><br>
            <input type="date" name="tanggal" required><br>
            <button type="submit">Simpan</button>
        </form>

        <h2>Daftar Simpanan</h2>
        <table>
            <tr>
                <th>Nama</th>
                <th>No Anggota</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
            </tr>
            <?php foreach ($simpanan_list as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['nama']) ?></td>
                <td><?= htmlspecialchars($s['no_anggota']) ?></td>
                <td><?= htmlspecialchars($s['jenis']) ?></td>
                <td>Rp <?= number_format($s['jumlah'], 2) ?></td>
                <td><?= htmlspecialchars($s['tanggal']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <a href="dashboard.php">Kembali ke Dashboard</a>
    </div>
</body>
</html>
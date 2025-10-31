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
    $pinjaman_id = $_POST['pinjaman_id'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];

    $stmt = $pdo->prepare("SELECT jumlah_pinjam, jumlah_cicil FROM pinjaman WHERE id = ?");
    $stmt->execute([$pinjaman_id]);
    $pinjaman = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pinjaman) {
        $total_cicil_baru = $pinjaman['jumlah_cicil'] + $jumlah;
        $status = ($total_cicil_baru >= $pinjaman['jumlah_pinjam']) ? 'lunas' : 'belum lunas';

        $stmt = $pdo->prepare("INSERT INTO angsuran (pinjaman_id, jumlah, tanggal) VALUES (?, ?, ?)");
        if ($stmt->execute([$pinjaman_id, $jumlah, $tanggal])) {
            $stmt = $pdo->prepare("UPDATE pinjaman SET jumlah_cicil = ?, status = ? WHERE id = ?");
            $stmt->execute([$total_cicil_baru, $status, $pinjaman_id]);
            $message = 'Angsuran berhasil ditambahkan.';
        } else {
            $message = 'Gagal menambahkan angsuran.';
        }
    } else {
        $message = 'ID Pinjaman tidak ditemukan.';
    }
}

$pinjaman_list = $pdo->query("
    SELECT p.id, p.jumlah_pinjam, p.jumlah_cicil, a.nama, a.no_anggota
    FROM pinjaman p
    JOIN anggota a ON p.anggota_id = a.id
    WHERE p.status != 'lunas'
")->fetchAll();

$angsuran_list = $pdo->query("
    SELECT an.*, p.jumlah_pinjam, a.nama, a.no_anggota
    FROM angsuran an
    JOIN pinjaman p ON an.pinjaman_id = p.id
    JOIN anggota a ON p.anggota_id = a.id
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Angsuran</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Input Angsuran</h1>
        <?php if ($message): ?>
            <p class="success"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="pinjaman_id">Pilih Pinjaman (Belum Lunas)</label>
            <select name="pinjaman_id" id="pinjaman_id" required>
                <option value="">Pilih Pinjaman</option>
                <?php foreach ($pinjaman_list as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['nama']) ?> - Rp <?= number_format($p['jumlah_pinjam'] - $p['jumlah_cicil'], 2) ?> (Sisa)
                    </option>
                <?php endforeach; ?>
            </select><br>
            <input type="number" name="jumlah" placeholder="Jumlah Angsuran" required><br>
            <input type="date" name="tanggal" required><br>
            <button type="submit">Simpan Angsuran</button>
        </form>

        <h2>Daftar Angsuran</h2>
        <table>
            <tr>
                <th>Nama</th>
                <th>No Anggota</th>
                <th>Jumlah Pinjaman</th>
                <th>Angsuran</th>
                <th>Tanggal</th>
            </tr>
            <?php foreach ($angsuran_list as $an): ?>
            <tr>
                <td><?= htmlspecialchars($an['nama']) ?></td>
                <td><?= htmlspecialchars($an['no_anggota']) ?></td>
                <td>Rp <?= number_format($an['jumlah_pinjam'], 2) ?></td>
                <td>Rp <?= number_format($an['jumlah'], 2) ?></td>
                <td><?= htmlspecialchars($an['tanggal']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <a href="dashboard.php">Kembali ke Dashboard</a>
    </div>
</body>
</html>
<?php
include 'functions.php';
requireLogin();

if (getUserRole() !== 'ketua' && getUserRole() !== 'bendahara') {
    header('Location: dashboard.php');
    exit;
}

$pdo = connectDB();

if (isset($_GET['action']) && $_GET['action'] === 'backup') {
    $tables = ['users', 'anggota', 'simpanan', 'pinjaman', 'angsuran'];
    $sql_dump = "-- Backup Database koperasi_keuangan\n-- " . date('Y-m-d H:i:s') . "\n\n";

    foreach ($tables as $table) {
        $result = $pdo->query("SELECT * FROM $table");
        $sql_dump .= "DROP TABLE IF EXISTS `$table`;\n";
        $create_table = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
        $sql_dump .= $create_table[1] . ";\n\n";

        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $values = array_map(function ($v) use ($pdo) {
                return $pdo->quote($v);
            }, $row);
            $sql_dump .= "INSERT INTO `$table` VALUES (" . implode(',', $values) . ");\n";
        }
        $sql_dump .= "\n";
    }

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="backup_koperasi_' . date('Y-m-d') . '.sql"');
    echo $sql_dump;
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Backup Data - Koperasi</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Backup Data Koperasi</h1>
        <p>Gunakan tombol di bawah ini untuk mengunduh salinan semua data dalam format SQL.</p>
        <a href="?action=backup" class="btn">Unduh Backup SQL</a>
        <br><br>
        <a href="dashboard.php">Kembali ke Dashboard</a>
    </div>
</body>
</html>
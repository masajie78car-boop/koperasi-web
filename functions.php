<?php
session_start();

function connectDB() {
    $host = 'localhost';
    $dbname = 'koperasi_keuangan';
    $user = 'root';
    $pass = '';
    try {
        return new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    } catch (PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function getUserRole() {
    return $_SESSION['user']['role'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}
?>
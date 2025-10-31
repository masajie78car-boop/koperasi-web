function connectDB() {
    $host = getenv('MYSQL_HOST') ?: 'sql305.infinityfree.com';
    $dbname = getenv('MYSQL_DB') ?: 'if0_40304062_dbujikoperasi';
    $user = getenv('MYSQL_USER') ?: 'if0_40304062';
    $pass = getenv('MYSQL_PASS') ?: 'borisbaker78';

    try {
        return new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    } catch (PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }
}

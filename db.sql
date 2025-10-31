-- Tabel user (login)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('ketua', 'bendahara', 'anggota') NOT NULL
);

-- Tabel anggota
CREATE TABLE anggota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    no_anggota VARCHAR(20) UNIQUE NOT NULL,
    alamat TEXT,
    tgl_daftar DATE NOT NULL
);

-- Tabel simpanan
CREATE TABLE simpanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anggota_id INT NOT NULL,
    jenis ENUM('wajib', 'pokok', 'sukarela') NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    tanggal DATE NOT NULL,
    FOREIGN KEY (anggota_id) REFERENCES anggota(id)
);

-- Tabel pinjaman
CREATE TABLE pinjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anggota_id INT NOT NULL,
    jumlah_pinjam DECIMAL(15,2) NOT NULL,
    jumlah_cicil DECIMAL(15,2) DEFAULT 0,
    status ENUM('lunas', 'belum lunas') DEFAULT 'belum lunas',
    tgl_pinjam DATE NOT NULL,
    tgl_jatuh_tempo DATE,
    FOREIGN KEY (anggota_id) REFERENCES pinjaman(id)
);

-- Tabel angsuran
CREATE TABLE angsuran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pinjaman_id INT NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    tanggal DATE NOT NULL,
    FOREIGN KEY (pinjaman_id) REFERENCES angsuran(id)
);

-- Tambahkan user default (password: 12345)
INSERT INTO users (username, password, role) VALUES
('ketua', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ketua'),
('bendahara', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'bendahara'),
('anggota1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'anggota');
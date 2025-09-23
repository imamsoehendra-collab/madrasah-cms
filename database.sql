-- Buat database
CREATE DATABASE madrasah_db;

-- Gunakan database
USE madrasah_db;

-- Buat tabel articles
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    author VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Buat tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin','editor') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Buat tabel gallery
CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert user admin (password: admin123)
INSERT INTO users (username, password, email, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@madrasah.sch.id', 'admin');

-- Insert contoh artikel
INSERT INTO articles (title, content, author) VALUES 
('Selamat Datang di Madrasah Al-Ihsan', 'Ini adalah artikel pertama di website madrasah.', 'Administrator'),
('Kegiatan Pesantren Kilat', 'Informasi mengenai kegiatan pesantren kilat bulan depan.', 'Kepala Madrasah');

-- Buat database
CREATE DATABASE IF NOT EXISTS quiz_system;
USE quiz_system;

-- Tabel hasil quiz
CREATE TABLE IF NOT EXISTS quiz_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    absen VARCHAR(10) NOT NULL,
    kelas VARCHAR(50) NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL DEFAULT 10,
    submitted_at DATETIME NOT NULL
);

-- Tabel soal quiz
CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_answer ENUM('a', 'b', 'c', 'd') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data untuk soal quiz
INSERT INTO quiz_questions (question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES
('Apa ibukota Indonesia?', 'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'a'),
('2 + 2 = ?', '3', '4', '5', '6', 'b'),
('Planet terdekat dengan matahari?', 'Venus', 'Mars', 'Mercury', 'Jupiter', 'c'),
('Warna daun biasanya?', 'Merah', 'Biru', 'Hijau', 'Kuning', 'c'),
('Bendera Indonesia berwarna?', 'Merah Putih', 'Merah Biru', 'Putih Hijau', 'Kuning Merah', 'a'),
('Hewan yang bisa terbang?', 'Ikan', 'Burung', 'Kucing', 'Anjing', 'b'),
('Musim panas di Indonesia biasanya?', 'Dingin', 'Panen', 'Hujan', 'Kemarau', 'd'),
('Alat untuk menulis di papan tulis?', 'Pensil', 'Spidol', 'Kapur', 'Pulpen', 'c'),
('Buah yang berwarna orange?', 'Apel', 'Jeruk', 'Anggur', 'Durian', 'b'),
('Ibu kota Jawa Barat?', 'Semarang', 'Surabaya', 'Bandung', 'Malang', 'c'),
('Tahun berapa Indonesia merdeka?', '1944', '1945', '1946', '1947', 'b'),
('Satuan jarak dalam SI?', 'Kilogram', 'Meter', 'Liter', 'Detik', 'b'),
('Bilangan prima terkecil?', '1', '2', '3', '4', 'b'),
('Presiden pertama Indonesia?', 'Soeharto', 'Soekarno', 'BJ Habibie', 'Gus Dur', 'b'),
('Hewan mamalia terbesar di dunia?', 'Gajah', 'Paus Biru', 'Jerapah', 'Badak', 'b');
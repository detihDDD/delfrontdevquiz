<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'quiz_system';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function createTables($conn) {
    // Tabel hasil quiz
    $sql1 = "CREATE TABLE IF NOT EXISTS quiz_results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        absen VARCHAR(10) NOT NULL,
        kelas VARCHAR(50) NOT NULL,
        score INT NOT NULL,
        total_questions INT NOT NULL DEFAULT 10,
        submitted_at DATETIME NOT NULL
    )";
    
    // Tabel soal quiz
    $sql2 = "CREATE TABLE IF NOT EXISTS quiz_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_text TEXT NOT NULL,
        option_a VARCHAR(255) NOT NULL,
        option_b VARCHAR(255) NOT NULL,
        option_c VARCHAR(255) NOT NULL,
        option_d VARCHAR(255) NOT NULL,
        correct_answer ENUM('a', 'b', 'c', 'd') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->query($sql1);
    $conn->query($sql2);
    
    // Insert sample questions jika belum ada
    $checkQuestions = "SELECT COUNT(*) as count FROM quiz_questions";
    $result = $conn->query($checkQuestions);
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        insertSampleQuestions($conn);
    }
}

function insertSampleQuestions($conn) {
    $sampleQuestions = [
        [
            "Apa ibukota Indonesia?",
            "Jakarta", "Surabaya", "Bandung", "Medan", "a"
        ],
        [
            "2 + 2 = ?",
            "3", "4", "5", "6", "b"
        ],
        [
            "Planet terdekat dengan matahari?",
            "Venus", "Mars", "Mercury", "Jupiter", "c"
        ],
        [
            "Warna daun biasanya?",
            "Merah", "Biru", "Hijau", "Kuning", "c"
        ],
        [
            "Bendera Indonesia berwarna?",
            "Merah Putih", "Merah Biru", "Putih Hijau", "Kuning Merah", "a"
        ],
        [
            "Hewan yang bisa terbang?",
            "Ikan", "Burung", "Kucing", "Anjing", "b"
        ],
        [
            "Musim panas di Indonesia biasanya?",
            "Dingin", "Panen", "Hujan", "Kemarau", "d"
        ],
        [
            "Alat untuk menulis di papan tulis?",
            "Pensil", "Spidol", "Kapur", "Pulpen", "c"
        ],
        [
            "Buah yang berwarna orange?",
            "Apel", "Jeruk", "Anggur", "Durian", "b"
        ],
        [
            "Ibu kota Jawa Barat?",
            "Semarang", "Surabaya", "Bandung", "Malang", "c"
        ],
        [
            "Tahun berapa Indonesia merdeka?",
            "1944", "1945", "1946", "1947", "b"
        ],
        [
            "Satuan jarak dalam SI?",
            "Kilogram", "Meter", "Liter", "Detik", "b"
        ],
        [
            "Bilangan prima terkecil?",
            "1", "2", "3", "4", "b"
        ],
        [
            "Presiden pertama Indonesia?",
            "Soeharto", "Soekarno", "BJ Habibie", "Gus Dur", "b"
        ],
        [
            "Hewan mamalia terbesar di dunia?",
            "Gajah", "Paus Biru", "Jerapah", "Badak", "b"
        ]
    ];
    
    $stmt = $conn->prepare("INSERT INTO quiz_questions (question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($sampleQuestions as $question) {
        $stmt->bind_param("ssssss", $question[0], $question[1], $question[2], $question[3], $question[4], $question[5]);
        $stmt->execute();
    }
}

// Panggil fungsi create tables
createTables($conn);
?>
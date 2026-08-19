<?php
include '../includes/functions.php';
include '../config/database.php';

if (!isStudentLoggedIn() || !isset($_SESSION['quiz_score'])) {
    header('Location: login.php');
    exit;
}

$score = $_SESSION['quiz_score'];
$total_questions = $_SESSION['total_questions'] ?? 10;
$student_data = $_SESSION['student_data'];

// Hitung jumlah benar
$correct_count = round(($score / 100) * $total_questions);
?>

<?php include '../includes/header.php'; ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="result-card text-center">
                <h2 class="mb-4">Hasil Quiz</h2>
                
                <div class="score-circle <?= $score >= 70 ? 'score-high' : ($score >= 50 ? 'score-medium' : 'score-low') ?>">
                    <h1 class="mb-0"><?= $score ?></h1>
                </div>
                
                <div class="student-info mt-4">
                    <p><strong>Nama:</strong> <?= htmlspecialchars($student_data['nama']) ?></p>
                    <p><strong>Absen:</strong> <?= htmlspecialchars($student_data['absen']) ?></p>
                    <p><strong>Kelas:</strong> <?= htmlspecialchars($student_data['kelas']) ?></p>
                    <p><strong>Jumlah Soal:</strong> <?= $total_questions ?> soal</p>
                    <p><strong>Jawaban Benar:</strong> <?= $correct_count ?> dari <?= $total_questions ?></p>
                </div>
                
                <?php if ($score == 100): ?>
                    <div class="alert alert-success mt-4">
                        <h4>🎉 Selamat! Nilai Sempurna!</h4>
                        <p class="mb-0">Anda menjawab semua soal dengan benar!</p>
                    </div>
                <?php elseif ($score >= 80): ?>
                    <div class="alert alert-info mt-4">
                        <h4>👍 Hasil Sangat Baik!</h4>
                        <p class="mb-0">Pertahankan prestasi Anda!</p>
                    </div>
                <?php elseif ($score >= 70): ?>
                    <div class="alert alert-primary mt-4">
                        <h4>💪 Hasil Baik!</h4>
                        <p class="mb-0">Tingkatkan lagi untuk hasil yang lebih baik!</p>
                    </div>
                <?php elseif ($score >= 60): ?>
                    <div class="alert alert-warning mt-4">
                        <h4>📚 Hasil Cukup Baik!</h4>
                        <p class="mb-0">Tingkatkan lagi belajar Anda!</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger mt-4">
                        <h4>📖 Perlu Belajar Lagi</h4>
                        <p class="mb-0">Jangan menyerah, terus berlatih!</p>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <a href="login.php" class="btn btn-primary btn-lg">Kembali ke Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
// Bersihkan session
unset($_SESSION['quiz_score']);
unset($_SESSION['total_questions']);
unset($_SESSION['student_logged_in']);
unset($_SESSION['student_data']);
include '../includes/footer.php'; 
?>
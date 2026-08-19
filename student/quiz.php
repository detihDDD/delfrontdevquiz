<?php
include '../includes/functions.php';
include '../config/database.php';

if (!isStudentLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Ambil soal dari database secara RANDOM, maksimal 10 soal
$sql = "SELECT * FROM quiz_questions ORDER BY RAND() LIMIT 10";
$result = $conn->query($sql);
$questions = [];
$total_questions = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[$row['id']] = [
            'question' => $row['question_text'],
            'options' => [
                'a' => $row['option_a'],
                'b' => $row['option_b'],
                'c' => $row['option_c'],
                'd' => $row['option_d']
            ],
            'correct' => $row['correct_answer']
        ];
    }
    $total_questions = count($questions);
}

// Jika soal di database kurang dari 10
if ($total_questions < 10) {
    $warning = "Peringatan: Hanya tersedia $total_questions soal dari 10 yang seharusnya.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_answers = $_POST['answers'];
    $correct_answers = [];
    
    foreach ($questions as $id => $question) {
        $correct_answers[$id] = $question['correct'];
    }
    
    $score = calculateScore($student_answers, $correct_answers, $total_questions);
    
    // Simpan hasil ke database
    $student_data = $_SESSION['student_data'];
    $sql = "INSERT INTO quiz_results (nama, absen, kelas, score, total_questions, submitted_at) 
            VALUES ('{$student_data['nama']}', '{$student_data['absen']}', '{$student_data['kelas']}', '$score', '$total_questions', NOW())";
    
    if ($conn->query($sql)) {
        $_SESSION['quiz_score'] = $score;
        $_SESSION['total_questions'] = $total_questions;
        header('Location: result.php');
        exit;
    } else {
        $error = "Terjadi kesalahan saat menyimpan hasil: " . $conn->error;
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">Quiz - <?= $total_questions ?> Soal</h2>
            <p class="text-muted mb-0">Soal dipilih secara acak dari bank soal</p>
        </div>
        <div class="card-body">
            <?php if (isset($warning)): ?>
                <div class="alert alert-warning"><?= $warning ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($total_questions > 0): ?>
                <div class="alert alert-info">
                    <strong>Informasi:</strong> 
                    <?php if ($total_questions == 10): ?>
                        Anda akan mengerjakan 10 soal pilihan acak dari <?= getTotalQuestions($conn) ?> soal yang tersedia.
                    <?php else: ?>
                        Anda akan mengerjakan <?= $total_questions ?> soal (dari <?= $total_questions ?> soal yang tersedia).
                    <?php endif; ?>
                </div>
                
                <form method="POST" id="quizForm">
                    <?php $question_number = 1; ?>
                    <?php foreach ($questions as $id => $question): ?>
                    <div class="question-card">
                        <h4>Soal <?= $question_number++ ?>: <?= htmlspecialchars($question['question']) ?></h4>
                        <?php foreach ($question['options'] as $key => $option): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" 
                                   name="answers[<?= $id ?>]" 
                                   value="<?= $key ?>" 
                                   id="q<?= $id ?>_<?= $key ?>" 
                                   required>
                            <label class="form-check-label" for="q<?= $id ?>_<?= $key ?>">
                                <strong><?= strtoupper($key) ?>.</strong> <?= htmlspecialchars($option) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <?php endforeach; ?>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">Kirim Jawaban</button>
                        <a href="login.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-danger text-center">
                    <h4>Belum ada soal yang tersedia!</h4>
                    <p>Silakan hubungi guru untuk menambahkan soal quiz.</p>
                    <a href="login.php" class="btn btn-primary">Kembali</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
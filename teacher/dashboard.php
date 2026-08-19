<?php
include '../includes/functions.php';
include '../config/database.php';

if (!isTeacherLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Handle tambah soal
if (isset($_POST['add_question'])) {
    $question_text = $conn->real_escape_string($_POST['question_text']);
    $option_a = $conn->real_escape_string($_POST['option_a']);
    $option_b = $conn->real_escape_string($_POST['option_b']);
    $option_c = $conn->real_escape_string($_POST['option_c']);
    $option_d = $conn->real_escape_string($_POST['option_d']);
    $correct_answer = $conn->real_escape_string($_POST['correct_answer']);
    
    $sql = "INSERT INTO quiz_questions (question_text, option_a, option_b, option_c, option_d, correct_answer) 
            VALUES ('$question_text', '$option_a', '$option_b', '$option_c', '$option_d', '$correct_answer')";
    
    if ($conn->query($sql)) {
        $success = "Soal berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan soal: " . $conn->error;
    }
}

// Handle hapus soal
if (isset($_GET['delete_question'])) {
    $id = intval($_GET['delete_question']);
    $sql = "DELETE FROM quiz_questions WHERE id = $id";
    
    if ($conn->query($sql)) {
        $success = "Soal berhasil dihapus!";
    } else {
        $error = "Gagal menghapus soal: " . $conn->error;
    }
}

// Ambil data hasil quiz
$results_sql = "SELECT * FROM quiz_results ORDER BY submitted_at DESC";
$results_result = $conn->query($results_sql);

// Ambil data soal
$questions_sql = "SELECT * FROM quiz_questions ORDER BY id";
$questions_result = $conn->query($questions_sql);

// Ambil statistik
$stats = getQuestionStats($conn);
$total_questions_in_db = getTotalQuestions($conn);
?>

<?php include '../includes/header.php'; ?>
<div class="container">
    <h2 class="mb-4">Dashboard Guru</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h5>Total Soal</h5>
                    <h2><?= $total_questions_in_db ?></h2>
                    <small>Soal dalam bank</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h5>Total Attempt</h5>
                    <h2><?= $stats['total_attempts'] ?? 0 ?></h2>
                    <small>Kali quiz dikerjakan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h5>Rata-rata Nilai</h5>
                    <h2><?= round($stats['average_score'] ?? 0) ?></h2>
                    <small>Skor rata-rata</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <h5>Soal per Quiz</h5>
                    <h2>10</h2>
                    <small>Soal acak</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs" id="teacherTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="results-tab" data-toggle="tab" href="#results">Hasil Quiz Siswa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="questions-tab" data-toggle="tab" href="#questions">Kelola Soal</a>
        </li>
    </ul>
    
    <!-- Tab Content -->
    <div class="tab-content" id="teacherTabsContent">
        <!-- Tab Hasil Quiz -->
        <div class="tab-pane fade show active" id="results">
            <div class="mt-3">
                <h4>Data Hasil Quiz</h4>
                
                <?php if ($results_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Absen</th>
                                    <th>Kelas</th>
                                    <th>Nilai</th>
                                    <th>Jumlah Soal</th>
                                    <th>Waktu Submit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php while ($row = $results_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['absen']) ?></td>
                                    <td><?= htmlspecialchars($row['kelas']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= 
                                            $row['score'] >= 80 ? 'success' : 
                                            ($row['score'] >= 60 ? 'warning' : 'danger') 
                                        ?> p-2">
                                            <?= $row['score'] ?>
                                        </span>
                                    </td>
                                    <td><?= $row['total_questions'] ?> soal</td>
                                    <td><?= date('d M Y H:i', strtotime($row['submitted_at'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <h5>Belum ada data hasil quiz</h5>
                        <p>Belum ada siswa yang mengerjakan quiz.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Tab Kelola Soal -->
        <div class="tab-pane fade" id="questions">
            <div class="mt-3">
                <div class="alert alert-info">
                    <strong>Info:</strong> Siswa akan mengerjakan <strong>10 soal acak</strong> dari total <strong><?= $total_questions_in_db ?> soal</strong> yang tersedia.
                    <?php if ($total_questions_in_db < 10): ?>
                        <br><span class="text-danger">⚠️ Peringatan: Minimal 10 soal diperlukan untuk quiz yang lengkap.</span>
                    <?php endif; ?>
                </div>
                
                <!-- Form Tambah Soal -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Tambah Soal Baru</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>Pertanyaan:</label>
                                <textarea name="question_text" class="form-control" rows="3" required 
                                          placeholder="Masukkan pertanyaan..."></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Opsi A:</label>
                                        <input type="text" name="option_a" class="form-control" required 
                                               placeholder="Jawaban A">
                                    </div>
                                    <div class="form-group">
                                        <label>Opsi B:</label>
                                        <input type="text" name="option_b" class="form-control" required 
                                               placeholder="Jawaban B">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Opsi C:</label>
                                        <input type="text" name="option_c" class="form-control" required 
                                               placeholder="Jawaban C">
                                    </div>
                                    <div class="form-group">
                                        <label>Opsi D:</label>
                                        <input type="text" name="option_d" class="form-control" required 
                                               placeholder="Jawaban D">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Jawaban Benar:</label>
                                <select name="correct_answer" class="form-control" required>
                                    <option value="">Pilih jawaban benar</option>
                                    <option value="a">A</option>
                                    <option value="b">B</option>
                                    <option value="c">C</option>
                                    <option value="d">D</option>
                                </select>
                            </div>
                            <button type="submit" name="add_question" class="btn btn-primary">
                                ➕ Tambah Soal
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Daftar Soal -->
                <h5>Daftar Soal Tersedia (<?= $total_questions_in_db ?> soal)</h5>
                <?php if ($questions_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Pertanyaan</th>
                                    <th width="120">Opsi A</th>
                                    <th width="120">Opsi B</th>
                                    <th width="120">Opsi C</th>
                                    <th width="120">Opsi D</th>
                                    <th width="80">Jawaban</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php while ($question = $questions_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($question['question_text']) ?></td>
                                    <td><?= htmlspecialchars($question['option_a']) ?></td>
                                    <td><?= htmlspecialchars($question['option_b']) ?></td>
                                    <td><?= htmlspecialchars($question['option_c']) ?></td>
                                    <td><?= htmlspecialchars($question['option_d']) ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-success">
                                            <strong><?= strtoupper($question['correct_answer']) ?></strong>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="?delete_question=<?= $question['id'] ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Yakin hapus soal ini?')">
                                            🗑️ Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <h5>Belum ada soal</h5>
                        <p>Gunakan form di atas untuk menambahkan soal pertama.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="?logout=true" class="btn btn-secondary">Logout</a>
    </div>
</div>
<?php include '../includes/footer.php'; ?><?php
include '../includes/functions.php';
include '../config/database.php';

if (!isTeacherLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Handle tambah soal
if (isset($_POST['add_question'])) {
    $question_text = $conn->real_escape_string($_POST['question_text']);
    $option_a = $conn->real_escape_string($_POST['option_a']);
    $option_b = $conn->real_escape_string($_POST['option_b']);
    $option_c = $conn->real_escape_string($_POST['option_c']);
    $option_d = $conn->real_escape_string($_POST['option_d']);
    $correct_answer = $conn->real_escape_string($_POST['correct_answer']);
    
    $sql = "INSERT INTO quiz_questions (question_text, option_a, option_b, option_c, option_d, correct_answer) 
            VALUES ('$question_text', '$option_a', '$option_b', '$option_c', '$option_d', '$correct_answer')";
    
    if ($conn->query($sql)) {
        $success = "Soal berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan soal: " . $conn->error;
    }
}

// Handle hapus soal
if (isset($_GET['delete_question'])) {
    $id = intval($_GET['delete_question']);
    $sql = "DELETE FROM quiz_questions WHERE id = $id";
    
    if ($conn->query($sql)) {
        $success = "Soal berhasil dihapus!";
    } else {
        $error = "Gagal menghapus soal: " . $conn->error;
    }
}

// Ambil data hasil quiz
$results_sql = "SELECT * FROM quiz_results ORDER BY submitted_at DESC";
$results_result = $conn->query($results_sql);

// Ambil data soal
$questions_sql = "SELECT * FROM quiz_questions ORDER BY id";
$questions_result = $conn->query($questions_sql);

// Ambil statistik
$stats = getQuestionStats($conn);
$total_questions_in_db = getTotalQuestions($conn);
?>

<?php include '../includes/header.php'; ?>
<div class="container">
    <h2 class="mb-4">Dashboard Guru</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h5>Total Soal</h5>
                    <h2><?= $total_questions_in_db ?></h2>
                    <small>Soal dalam bank</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h5>Total Attempt</h5>
                    <h2><?= $stats['total_attempts'] ?? 0 ?></h2>
                    <small>Kali quiz dikerjakan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h5>Rata-rata Nilai</h5>
                    <h2><?= round($stats['average_score'] ?? 0) ?></h2>
                    <small>Skor rata-rata</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <h5>Soal per Quiz</h5>
                    <h2>10</h2>
                    <small>Soal acak</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs" id="teacherTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="results-tab" data-toggle="tab" href="#results">Hasil Quiz Siswa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="questions-tab" data-toggle="tab" href="#questions">Kelola Soal</a>
        </li>
    </ul>
    
    <!-- Tab Content -->
    <div class="tab-content" id="teacherTabsContent">
        <!-- Tab Hasil Quiz -->
        <div class="tab-pane fade show active" id="results">
            <div class="mt-3">
                <h4>Data Hasil Quiz</h4>
                
                <?php if ($results_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Absen</th>
                                    <th>Kelas</th>
                                    <th>Nilai</th>
                                    <th>Jumlah Soal</th>
                                    <th>Waktu Submit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php while ($row = $results_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['absen']) ?></td>
                                    <td><?= htmlspecialchars($row['kelas']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= 
                                            $row['score'] >= 80 ? 'success' : 
                                            ($row['score'] >= 60 ? 'warning' : 'danger') 
                                        ?> p-2">
                                            <?= $row['score'] ?>
                                        </span>
                                    </td>
                                    <td><?= $row['total_questions'] ?> soal</td>
                                    <td><?= date('d M Y H:i', strtotime($row['submitted_at'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <h5>Belum ada data hasil quiz</h5>
                        <p>Belum ada siswa yang mengerjakan quiz.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Tab Kelola Soal -->
        <div class="tab-pane fade" id="questions">
            <div class="mt-3">
                <div class="alert alert-info">
                    <strong>Info:</strong> Siswa akan mengerjakan <strong>10 soal acak</strong> dari total <strong><?= $total_questions_in_db ?> soal</strong> yang tersedia.
                    <?php if ($total_questions_in_db < 10): ?>
                        <br><span class="text-danger">⚠️ Peringatan: Minimal 10 soal diperlukan untuk quiz yang lengkap.</span>
                    <?php endif; ?>
                </div>
                
                <!-- Form Tambah Soal -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Tambah Soal Baru</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>Pertanyaan:</label>
                                <textarea name="question_text" class="form-control" rows="3" required 
                                          placeholder="Masukkan pertanyaan..."></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Opsi A:</label>
                                        <input type="text" name="option_a" class="form-control" required 
                                               placeholder="Jawaban A">
                                    </div>
                                    <div class="form-group">
                                        <label>Opsi B:</label>
                                        <input type="text" name="option_b" class="form-control" required 
                                               placeholder="Jawaban B">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Opsi C:</label>
                                        <input type="text" name="option_c" class="form-control" required 
                                               placeholder="Jawaban C">
                                    </div>
                                    <div class="form-group">
                                        <label>Opsi D:</label>
                                        <input type="text" name="option_d" class="form-control" required 
                                               placeholder="Jawaban D">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Jawaban Benar:</label>
                                <select name="correct_answer" class="form-control" required>
                                    <option value="">Pilih jawaban benar</option>
                                    <option value="a">A</option>
                                    <option value="b">B</option>
                                    <option value="c">C</option>
                                    <option value="d">D</option>
                                </select>
                            </div>
                            <button type="submit" name="add_question" class="btn btn-primary">
                                ➕ Tambah Soal
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Daftar Soal -->
                <h5>Daftar Soal Tersedia (<?= $total_questions_in_db ?> soal)</h5>
                <?php if ($questions_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Pertanyaan</th>
                                    <th width="120">Opsi A</th>
                                    <th width="120">Opsi B</th>
                                    <th width="120">Opsi C</th>
                                    <th width="120">Opsi D</th>
                                    <th width="80">Jawaban</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php while ($question = $questions_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($question['question_text']) ?></td>
                                    <td><?= htmlspecialchars($question['option_a']) ?></td>
                                    <td><?= htmlspecialchars($question['option_b']) ?></td>
                                    <td><?= htmlspecialchars($question['option_c']) ?></td>
                                    <td><?= htmlspecialchars($question['option_d']) ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-success">
                                            <strong><?= strtoupper($question['correct_answer']) ?></strong>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="?delete_question=<?= $question['id'] ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Yakin hapus soal ini?')">
                                            🗑️ Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <h5>Belum ada soal</h5>
                        <p>Gunakan form di atas untuk menambahkan soal pertama.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="?logout=true" class="btn btn-secondary">Logout</a>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
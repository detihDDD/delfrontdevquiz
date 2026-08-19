<?php
include '../includes/functions.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $absen = $conn->real_escape_string($_POST['absen']);
    $kelas = $conn->real_escape_string($_POST['kelas']);
    
    // Validasi input
    if (empty($nama) || empty($absen) || empty($kelas)) {
        $error = "Semua field harus diisi!";
    } else {
        // Simpan data siswa ke session
        $_SESSION['student_data'] = [
            'nama' => $nama,
            'absen' => $absen,
            'kelas' => $kelas
        ];
        $_SESSION['student_logged_in'] = true;
        
        header('Location: quiz.php');
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Login Siswa</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap:</label>
                            <input type="text" id="nama" name="nama" class="form-control" required 
                                   placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="form-group">
                            <label for="absen">Nomor Absen:</label>
                            <input type="number" id="absen" name="absen" class="form-control" required 
                                   placeholder="Masukkan nomor absen" min="1">
                        </div>
                        <div class="form-group">
                            <label for="kelas">Kelas:</label>
                            <input type="text" id="kelas" name="kelas" class="form-control" required 
                                   placeholder="Contoh: X IPA 1">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Mulai Quiz</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Dengan mengisi data, Anda setuju mengerjakan quiz dengan jujur
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<?php
include '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Simple authentication
    if ($username === 'guru' && $password === 'guru123') {
        $_SESSION['teacher_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Login Guru</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" class="form-control" 
                                   value="guru" readonly
                                   placeholder="Masukkan username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" class="form-control" 
                                   value="guru123" readonly
                                   placeholder="Masukkan password">
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Login</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Default username: <strong>guru</strong><br>
                            Default password: <strong>guru123</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="jumbotron text-center">
        <h1 class="display-4">Selamat Datang di Sistem Quiz</h1>
        <p class="lead">Platform quiz interaktif untuk siswa dan guru</p>
        <hr class="my-4">
        <p>Pilih mode yang sesuai dengan peran Anda</p>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3>🎓 Mode Siswa</h3>
                    <p>Klik untuk mengerjakan quiz</p>
                    <a href="student/login.php" class="btn btn-primary btn-lg">Masuk sebagai Siswa</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3>👨‍🏫 Mode Guru</h3>
                    <p>Klik untuk melihat hasil quiz</p>
                    <a href="teacher/login.php" class="btn btn-success btn-lg">Masuk sebagai Guru</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
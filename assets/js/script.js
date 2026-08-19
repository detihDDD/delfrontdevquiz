// JavaScript untuk sistem quiz
document.addEventListener('DOMContentLoaded', function() {
    // Konfirmasi sebelum submit quiz
    const quizForm = document.getElementById('quizForm');
    if (quizForm) {
        quizForm.addEventListener('submit', function(e) {
            const unanswered = document.querySelectorAll('input[type="radio"]:checked').length;
            const totalQuestions = document.querySelectorAll('.question-card').length;
            
            if (unanswered < totalQuestions) {
                const confirmSubmit = confirm(`Anda belum menjawab semua soal. Masih ada ${totalQuestions - unanswered} soal yang belum dijawab. Yakin ingin submit?`);
                if (!confirmSubmit) {
                    e.preventDefault();
                }
            }
        });
    }
    
    // Auto-hide alerts setelah 5 detik
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const fadeEffect = setInterval(function() {
                if (!alert.style.opacity) {
                    alert.style.opacity = 1;
                }
                if (alert.style.opacity > 0) {
                    alert.style.opacity -= 0.1;
                } else {
                    clearInterval(fadeEffect);
                    alert.remove();
                }
            }, 50);
        });
    }, 5000);
    
    // Smooth scroll untuk navigasi
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    
    // Validasi form tambah soal
    const questionForm = document.querySelector('form[method="POST"]');
    if (questionForm && document.querySelector('input[name="option_a"]')) {
        questionForm.addEventListener('submit', function(e) {
            const options = [
                document.querySelector('input[name="option_a"]').value,
                document.querySelector('input[name="option_b"]').value,
                document.querySelector('input[name="option_c"]').value,
                document.querySelector('input[name="option_d"]').value
            ];
            
            // Cek duplikasi opsi
            const uniqueOptions = new Set(options);
            if (uniqueOptions.size !== options.length) {
                alert('Peringatan: Terdapat opsi jawaban yang duplikat!');
                e.preventDefault();
            }
        });
    }
    
    // Tambah efek loading pada tombol submit
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.form.checkValidity()) {
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
                this.disabled = true;
            }
        });
    });
});
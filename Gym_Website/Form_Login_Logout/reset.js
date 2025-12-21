document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. LOGIC ẨN/HIỆN MẬT KHẨU ---
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('myPassword');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('bxs-hide');
            this.classList.toggle('bxs-show');
        });
    }

    // --- 2. LOGIC XÓA FORM KHI ẤN NÚT BACK (QUAN TRỌNG) ---
    // Sự kiện 'pageshow' chạy ngay cả khi load từ cache (nút Back)
    window.addEventListener('pageshow', function(event) {
        var form = document.querySelector('form');
        if (form) {
            form.reset(); // Reset form về trắng tinh
        }
    });
});
 // Script hiệu ứng Toast
    const toastBox = document.getElementById('toast-box');
    if (toastBox) {
        setTimeout(() => { toastBox.classList.add('show'); }, 100);
        setTimeout(() => { toastBox.classList.remove('show'); }, 3000);
    }

    // Script chặn F5 gửi lại form
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

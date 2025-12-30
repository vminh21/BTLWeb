<?php
session_start();
require_once 'connectdb.php';

// Nếu chưa có OTP trong Session (truy cập trái phép) -> Quay về trang quên mật khẩu
if (!isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_otp      = trim($_POST['otp']);
    $new_pass      = $_POST['new_password'];
    $confirm_pass  = $_POST['confirm_password'];
    
    // Lấy thông tin từ Session
    $server_otp    = $_SESSION['reset_otp'];
    $email_to_reset= $_SESSION['reset_email'];
    
    // 1. Kiểm tra các trường trống (vì đã tắt novalidate)
    if (empty($user_otp) || empty($new_pass) || empty($confirm_pass)) {
        $error_message = "Vui lòng nhập đầy đủ tất cả các trường.";
    } 
    // 2. Kiểm tra OTP
    elseif ($user_otp != $server_otp) {
        $error_message = "Mã xác thực (OTP) không chính xác.";
    } 
    // 3. Kiểm tra độ dài mật khẩu (tối thiểu 6 ký tự chẳng hạn)
    elseif (strlen($new_pass) < 6) {
        $error_message = "Mật khẩu mới phải có ít nhất 6 ký tự.";
    }
    // 4. Kiểm tra mật khẩu khớp nhau
    elseif ($new_pass !== $confirm_pass) {
        $error_message = "Mật khẩu xác nhận không trùng khớp.";
    }
    // 5. Thực hiện đổi mật khẩu (Dùng Prepared Statement)
    else {
        // Khuyên dùng password_hash để bảo mật, nhưng ở đây tôi giữ logic UPDATE cho ông
        // Nếu DB ông dùng MD5 thì: $hashed_pass = md5($new_pass);
        // Ở đây tôi dùng pass thuần hoặc escape tùy cấu trúc cũ của ông
        $safe_pass = $conn->real_escape_string($new_pass);
        
        $stmt = $conn->prepare("UPDATE members SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $safe_pass, $email_to_reset);
        
        if ($stmt->execute()) {
            // Xóa session để hoàn tất quá trình
            unset($_SESSION['reset_otp']);
            unset($_SESSION['reset_email']);
            
            echo "<script>
                    alert('Đổi mật khẩu thành công! Vui lòng đăng nhập bằng mật khẩu mới.');
                    window.location.href = 'login.php';
                  </script>";
            exit();
        } else {
            $error_message = "Đã xảy ra lỗi trong quá trình cập nhật. Vui lòng thử lại.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu | FitPhysique</title>
    <link rel="stylesheet" href="styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    <?php if($error_message != ""): ?>
        <div id="toast-box" class="toast-error">
            <i class='bx bx-error-circle'></i> 
            <span><?php echo htmlspecialchars($error_message); ?></span>
        </div>
    <?php endif; ?>

    <div class="wrapper">
        <form action="" method="POST" autocomplete="off" novalidate>
            <h1>Đặt Lại Mật Khẩu</h1>
            
            <p style="color:#bdc3c7; text-align:center; margin-bottom:15px; font-size: 14px;">
                Mã xác thực đã được gửi tới: <br>
                <b style="color: #fff;"><?php echo htmlspecialchars($_SESSION['reset_email']); ?></b>
            </p>

            <div class="input-box">
                <input type="text" name="otp" placeholder="Nhập mã OTP (6 số)">
                <i class='bx bxs-key'></i>
            </div>

            <div class="input-box">
                <input type="password" name="new_password" placeholder="Mật khẩu mới">
                <i class='bx bxs-lock-alt'></i>
            </div>
            
            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới">
                <i class='bx bxs-lock-alt'></i>
            </div>

            <button type="submit" class="btn">Xác Nhận Đổi</button>
            
            <div class="register-link">
                <p>Không nhận được mã? <a href="forgot_password.php">Gửi lại</a></p>
            </div>
        </form>
    </div>

    <script src="reset.js"></script>
</body>
</html>
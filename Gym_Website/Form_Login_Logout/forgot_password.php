<?php
session_start();
require_once 'connectdb.php';

// --- NHÚNG PHPMAILER ---
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error_message = "";

// Hàm gửi OTP qua Email
function sendOTP($toEmail, $otpCode) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nguyenvanminh859323@gmail.com'; 
        $mail->Password   = 'rqwj secl emei qcwk'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('no-reply@fitphysique.com', 'FitPhysique Support');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Mã xác nhận khôi phục mật khẩu';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; line-height: 1.6;'>
                <h3 style='color: #2c3e50;'>Yêu cầu cấp lại mật khẩu</h3>
                <p>Chào bạn,</p>
                <p>Mã xác nhận (OTP) của bạn là: <b style='color:#e74c3c; font-size:24px;'>$otpCode</b></p>
                <p>Vui lòng sử dụng mã này để hoàn tất quá trình đổi mật khẩu. Mã sẽ hết hạn sau khi bạn đóng trình duyệt.</p>
                <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.</p>
                <hr>
                <p style='font-size: 12px; color: #7f8c8d;'>Đây là email tự động từ hệ thống FitPhysique.</p>
            </div>
        ";
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Xử lý khi nhấn nút Gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error_message = "Vui lòng nhập địa chỉ email của bạn.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Địa chỉ email không đúng định dạng. Vui lòng kiểm tra lại.";
    } else {
        $stmt = $conn->prepare("SELECT email FROM members WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $otp = rand(100000, 999999);
            $_SESSION['reset_otp']   = $otp;
            $_SESSION['reset_email'] = $email;
            
            if (sendOTP($email, $otp)) {
                header("Location: reset_password.php");
                exit();
            } else {
                $error_message = "Hệ thống không thể gửi mail lúc này. Vui lòng thử lại sau.";
            }
        } else {
            $error_message = "Email này chưa được đăng ký trong hệ thống.";
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
    <title>Quên mật khẩu | FitPhysique</title>
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
            <h1>Quên Mật Khẩu</h1>
            <p style="text-align: center; color: #bfd1c9ff; font-size: 14px; margin-bottom: 20px;">
                Nhập email đã đăng ký để nhận mã OTP xác thực.
            </p>

            <div class="input-box">
                <input type="email" name="email" placeholder="Nhập địa chỉ Email" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <i class='bx bxs-envelope'></i>
            </div>

            <button type="submit" class="btn">Gửi Mã OTP</button>

            <div class="register-link">
                <p>Quay lại trang <a href="login.php">Đăng nhập</a></p>
            </div>
        </form>
    </div>

    <script src="reset.js"></script>
</body>
</html>
<?php
session_start();
require_once 'connectdb.php';

// --- NHÚNG PHPMAILER (Giữ nguyên như cũ) ---
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$msg_type = "";

function sendOTP($toEmail, $otpCode) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        // --- ĐIỀN EMAIL & PASS ỨNG DỤNG CỦA ÔNG ---
        $mail->Username   = 'nguyenvanminh859323@gmail.com'; 
        $mail->Password   = 'rqwj secl emei qcwk'; 
        // -------------------------------------------
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('no-reply@fitphysique.com', 'FitPhysique Support');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Mã xác nhận đổi mật khẩu - FitPhysique';
        $mail->Body    = "
            <h3>Xin chào,</h3>
            <p>Mã xác nhận (OTP) của bạn là: <b style='color:red; font-size:24px;'>$otpCode</b></p>
            <p>Mã này sẽ hết hạn khi bạn tắt trình duyệt.</p>
        ";
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    // Kiểm tra Email có tồn tại trong Members không
    $safe_email = $conn->real_escape_string($email);
    $checkMember = $conn->query("SELECT * FROM members WHERE email = '$safe_email'");
    
    if ($checkMember->num_rows > 0) {
        // 1. Tạo mã OTP 6 số ngẫu nhiên
        $otp = rand(100000, 999999);
        
        // 2. Lưu OTP và Email vào SESSION (quan trọng)
        $_SESSION['reset_otp']   = $otp;
        $_SESSION['reset_email'] = $email;
        
        // 3. Gửi Mail
        if (sendOTP($email, $otp)) {
            // Gửi thành công -> Chuyển sang trang nhập mã
            header("Location: reset_password.php");
            exit();
        } else {
            $message = "Lỗi gửi mail. Vui lòng thử lại.";
            $msg_type = "error";
        }
    } else {
        $message = "Email này chưa đăng ký thành viên!";
        $msg_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quên mật khẩu</title>
  <link rel="stylesheet" href="styles.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>.msg-box { padding: 10px; margin-bottom: 20px; text-align: center; color: white; background: rgba(255,0,0,0.5); border-radius: 5px; }</style>
</head>
<body>
  <div class="wrapper">
    <form action="" method="POST" autocomplete="off">
      <h1>Quên Mật Khẩu</h1>
      
      <?php if($message != ""): ?>
          <div class="msg-box"><?php echo $message; ?></div>
      <?php endif; ?>

      <p style="color: #eee; text-align: center; margin-bottom: 20px;">
          Nhập email để nhận mã xác nhận (OTP).
      </p>

      <div class="input-box">
        <input type="email" name="email" placeholder="Email của bạn" required>
        <i class='bx bxs-envelope'></i>
      </div>

      <button type="submit" class="btn">Gửi Mã OTP</button>

      <div class="register-link">
        <p>Quay lại <a href="login.php">Đăng nhập</a></p>
      </div>
    </form>
  </div>
</body>
</html>
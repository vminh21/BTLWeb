<?php
session_start();
require_once 'connectdb.php';

// Nếu chưa có OTP trong Session (nghĩa là chưa qua bước 1) -> Đá về trang quên pass
if (!isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_otp      = $_POST['otp'];
    $new_pass      = $_POST['new_password'];
    $confirm_pass  = $_POST['confirm_password'];
    
    // Lấy thông tin từ Session
    $server_otp    = $_SESSION['reset_otp'];
    $email_to_reset= $_SESSION['reset_email'];
    
    // 1. Kiểm tra OTP
    if ($user_otp != $server_otp) {
        $message = "Mã OTP không chính xác!";
    } 
    // 2. Kiểm tra mật khẩu khớp nhau
    elseif ($new_pass !== $confirm_pass) {
        $message = "Mật khẩu nhập lại không khớp!";
    }
    // 3. Thực hiện đổi mật khẩu
    else {
        $safe_pass = $conn->real_escape_string($new_pass);
        // Nếu dùng MD5: $safe_pass = md5($new_pass);
        
        $sql = "UPDATE members SET password = '$safe_pass' WHERE email = '$email_to_reset'";
        
        if ($conn->query($sql)) {
            // Xóa session để không đổi được lần nữa
            unset($_SESSION['reset_otp']);
            unset($_SESSION['reset_email']);
            
            // Hiện thông báo thành công (bằng JS cho tiện chuyển trang)
            echo "<script>
                    alert('Đổi mật khẩu thành công! Vui lòng đăng nhập lại.');
                    window.location.href = 'login.php';
                  </script>";
            exit();
        } else {
            $message = "Lỗi Database: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Đặt lại mật khẩu</title>
  <link rel="stylesheet" href="styles.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>.msg-box { padding: 10px; margin-bottom: 20px; text-align: center; color: white; background: rgba(255,0,0,0.5); border-radius: 5px; }</style>
</head>
<body>
  <div class="wrapper">
    <form action="" method="POST" autocomplete="off">
      <h1>Đặt Lại Mật Khẩu</h1>
      
      <?php if($message != ""): ?>
          <div class="msg-box"><?php echo $message; ?></div>
      <?php endif; ?>
      
      <p style="color:#eee; text-align:center; margin-bottom:15px;">
        Mã OTP đã được gửi tới: <b><?php echo $_SESSION['reset_email']; ?></b>
      </p>

      <div class="input-box">
        <input type="text" name="otp" placeholder="Nhập mã OTP (6 số)" required pattern="[0-9]*">
        <i class='bx bxs-key'></i>
      </div>

      <div class="input-box">
        <input type="password" name="new_password" placeholder="Mật khẩu mới" required>
        <i class='bx bxs-lock-alt'></i>
      </div>
      
      <div class="input-box">
        <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
        <i class='bx bxs-lock-alt'></i>
      </div>

      <button type="submit" class="btn">Xác Nhận Đổi</button>
      
      <div class="register-link">
          <p><a href="forgot_password.php">Gửi lại mã?</a></p>
      </div>
    </form>
  </div>
</body>
</html>
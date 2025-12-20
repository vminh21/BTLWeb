<?php
session_start();

// --- THAY ĐOẠN KẾT NỐI BẰNG FILE RIÊNG ---
require_once 'connectdb.php'; 
// Biến $conn từ file connectdb.php sẽ được dùng ở dưới

$error_message = "";

// --- XỬ LÝ LOGIN (Giữ nguyên logic cũ) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_user = $conn->real_escape_string($_POST['username']);
    $input_pass = $conn->real_escape_string($_POST['password']);

    // 1. KIỂM TRA ADMIN
    $sql_admin = "SELECT * FROM admins WHERE username = '$input_user' AND password = '$input_pass'";
    $result_admin = $conn->query($sql_admin);

    if ($result_admin->num_rows > 0) {
        $row = $result_admin->fetch_assoc();
        $_SESSION['user_id'] = $row['admin_id'];
        $_SESSION['full_name'] = $row['full_name'];
        $_SESSION['role'] = 'admin';

        header("Location: ../QL_Members/admin_dashboard.php");
        exit();
    } else {
        // 2. KIỂM TRA MEMBER
        $sql_member = "SELECT * FROM members WHERE email = '$input_user' AND password = '$input_pass'";
        $result_member = $conn->query($sql_member);

        if ($result_member->num_rows > 0) {
            $row = $result_member->fetch_assoc();
            $_SESSION['user_id'] = $row['member_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = 'member';

            header("Location: ../index.html");
            exit();
        } else {
            $error_message = "Tài khoản hoặc mật khẩu không chính xác!";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - FitPhysique</title>
  <link rel="stylesheet" href="styles.css"> 
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  
  <style>
    .error-text {
        color: #ff4d4d;
        background-color: rgba(255, 0, 0, 0.1);
        padding: 10px;
        border-radius: 5px;
        font-size: 14px;
        text-align: center;
        margin-bottom: 15px;
        border: 1px solid #ff4d4d;
    }
    #togglePassword { cursor: pointer; }
  </style>
</head>
<body>
  <div class="wrapper">
    <form action="" method="POST">
      <h1>Login</h1>
      
      <img src="../assets/logo.png" alt="Logo" style="width: 100px; display: block; margin: 0 auto 20px;">

      <?php if (!empty($error_message)): ?>
          <div class="error-text"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <div class="input-box">
        <input type="text" name="username" placeholder="Email (Member) or Username (Admin)" required>
        <i class='bx bxs-user'></i>
      </div>

      <div class="input-box">
        <input type="password" name="password" id="myPassword" placeholder="Password" required>
        <i class='bx bxs-hide' id="togglePassword"></i>
      </div>

      <div class="remember-forgot">
        <label><input type="checkbox">Remember Me</label>
        <a href="#">Forgot Password</a>
      </div>

      <button type="submit" class="btn">Login</button>

      <div class="register-link">
        <p>Don't have an account? <a href="signup.php" id="register-link">Register</a></p>
      </div>
    </form>
  </div>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('myPassword');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('bxs-hide');
        this.classList.toggle('bxs-show');
    });
  </script>
</body>
</html>
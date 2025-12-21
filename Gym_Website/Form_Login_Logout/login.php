<?php
session_start();
require_once 'connectdb.php'; 

$error_message = "";

// XỬ LÝ KHI FORM ĐƯỢC SUBMIT
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $input_pass = isset($_POST['password']) ? trim($_POST['password']) : '';

    // --- 1. VALIDATE DỮ LIỆU ĐẦU VÀO (TRIỆT ĐỂ) ---
    
    // Trường hợp 1: Không nhập gì cả
    if (empty($input_user) && empty($input_pass)) {
        $error_message = "Vui lòng nhập đầy đủ Tài khoản và Mật khẩu!";
    } 
    // Trường hợp 2: Nhập pass rồi mà quên nhập user
    elseif (empty($input_user)) {
        $error_message = "Vui lòng nhập Email";
    }
    // Trường hợp 3: Nhập user rồi mà quên nhập pass
    elseif (empty($input_pass)) {
        $error_message = "Vui lòng nhập Mật khẩu!";
    }
    
    // --- 2. NẾU ĐỦ DỮ LIỆU THÌ MỚI CHECK DB ---
    else {
        $safe_user = $conn->real_escape_string($input_user);
        $safe_pass = $conn->real_escape_string($input_pass);

        // A. Check Admin
        $sql_admin = "SELECT * FROM admins WHERE username = '$safe_user' AND password = '$safe_pass'";
        $result_admin = $conn->query($sql_admin);

        if ($result_admin->num_rows > 0) {
            $row = $result_admin->fetch_assoc();
            $_SESSION['user_id'] = $row['admin_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = 'admin';
            header("Location: ../QL_Members/admin_dashboard.php");
            exit();
        } else {
            // B. Check Member
            $sql_member = "SELECT * FROM members WHERE email = '$safe_user' AND password = '$safe_pass'";
            $result_member = $conn->query($sql_member);

            if ($result_member->num_rows > 0) {
                $row = $result_member->fetch_assoc();
                $_SESSION['user_id'] = $row['member_id'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['role'] = 'member';
                header("Location: ../index.php");
                exit();
            } else {
                // C. SAI TÀI KHOẢN HOẶC MẬT KHẨU
                $error_message = "Tài khoản hoặc mật khẩu không chính xác!";
            }
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
  <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

  <?php if($error_message != ""): ?>
      <div id="toast-box" class="toast-error">
          <i class='bx bxs-error-circle'></i> <span><?php echo $error_message; ?></span>
      </div>
  <?php endif; ?>

  <div class="wrapper">
    <form action="" method="POST" novalidate>
      <h1>Login</h1>
      
      <img src="../assets/logo.png" alt="Logo" style="width: 100px; display: block; margin: 0 auto 20px;">

      <div class="input-box">
        <input type="text" name="username" placeholder="Email (Member) or Username (Admin)" 
               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
        <i class='bx bxs-user'></i>
      </div>

      <div class="input-box">
        <input type="password" name="password" id="myPassword" placeholder="Password">
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
  <script src="reset.js"></script>
</body>
</html>
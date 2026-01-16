<?php
session_start();
require_once 'connectdb.php'; 

$error_message = "";

// --- 1. CÁC HÀM HỖ TRỢ XỬ LÝ COOKIE (MULTI-ACCOUNT) ---
function getSavedAccounts() {
    if (isset($_COOKIE['saved_accounts'])) {
        return json_decode($_COOKIE['saved_accounts'], true) ?? [];
    }
    return [];
}

function saveAccountToCookie($username, $password) {
    $accounts = getSavedAccounts();
    $accounts[$username] = $password;
    setcookie('saved_accounts', json_encode($accounts), time() + (86400 * 30), "/");
}

function removeAccountFromCookie($username) {
    $accounts = getSavedAccounts();
    if (isset($accounts[$username])) {
        unset($accounts[$username]); 
        setcookie('saved_accounts', json_encode($accounts), time() + (86400 * 30), "/");
    }
}

// XỬ LÝ KHI FORM ĐƯỢC SUBMIT
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $input_pass = isset($_POST['password']) ? trim($_POST['password']) : '';
    $remember   = isset($_POST['remember_me']); 

    // VALIDATE
    if (empty($input_user) && empty($input_pass)) {
        $error_message = "Vui lòng nhập đầy đủ Tài khoản và Mật khẩu!";
    } elseif (empty($input_user)) {
        $error_message = "Vui lòng nhập Email";
    } elseif (empty($input_pass)) {
        $error_message = "Vui lòng nhập Mật khẩu!";
    } 
    
    // CHECK DB
    else {
        $safe_user = $conn->real_escape_string($input_user);
        $safe_pass = $conn->real_escape_string($input_pass);

        // A. Check Admin
        $sql_admin = "SELECT * FROM admins WHERE email = '$safe_user' AND password = '$safe_pass'";
        $result_admin = $conn->query($sql_admin);

        // B. Check Member
        $sql_member = "SELECT * FROM members WHERE email = '$safe_user' AND password = '$safe_pass'";
        $result_member = $conn->query($sql_member);

        $login_success = false;
        $redirect_url = "";

        // Logic Admin
        if ($result_admin->num_rows > 0) {
            $row = $result_admin->fetch_assoc();
            $_SESSION['admin_id']  = $row['admin_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role']      = $row['position'];
            $redirect_url          = "../QL_Members/admin_dashboard.php";
            $login_success         = true;
        } 
        // Logic Member
        elseif ($result_member->num_rows > 0) {
            $row = $result_member->fetch_assoc();
            $_SESSION['member_id'] = $row['member_id']; 
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role']      = 'member';
            $redirect_url          = "../index.php";
            $login_success         = true;
        } 
        else {
            $error_message = "Tài khoản hoặc mật khẩu không chính xác!";
        }

        if ($login_success) {
            if ($remember) {
                saveAccountToCookie($input_user, $input_pass);
            } else {
                removeAccountFromCookie($input_user);
            }
            header("Location: " . $redirect_url);
            exit();
        }
    }
}
$conn->close();

$saved_accounts = getSavedAccounts();
$json_accounts_for_js = json_encode($saved_accounts);
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
    <form action="" method="POST" novalidate autocomplete="off">
      <h1>Login</h1>
      
      <img src="../assets/logo.png" alt="Logo" style="width: 100px; display: block; margin: 0 auto 20px;">

      <div class="input-box">
        <input type="text" name="username" id="usernameInput" placeholder="Email" list="saved-users"
               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
        <i class='bx bxs-user'></i>
        
        <datalist id="saved-users">
            <?php foreach ($saved_accounts as $email => $pass): ?>
                <option value="<?php echo htmlspecialchars($email); ?>">
            <?php endforeach; ?>
        </datalist>
      </div>

      <div class="input-box">
        <input type="password" name="password" id="passwordInput" placeholder="Password">
        <i class='bx bxs-hide' id="togglePassword"></i>
      </div>

      <div class="remember-forgot">
        <label><input type="checkbox" name="remember_me" id="rememberCheck">Remember Me</label>
        <a href="forgot_password.php">Forgot Password</a>
      </div>

      <button type="submit" class="btn">Login</button>

      <div class="register-link">
        <p>Don't have an account? <a href="signup.php" id="register-link">Register</a></p>
      </div>
    </form>
  </div>

  <script>
      const savedAccounts = <?php echo $json_accounts_for_js; ?>;
      
      const userInput = document.getElementById('usernameInput');
      const passInput = document.getElementById('passwordInput');
      const rememberCheck = document.getElementById('rememberCheck');


      userInput.addEventListener('input', function() {
          const email = this.value;
          if (savedAccounts.hasOwnProperty(email)) {
              passInput.value = savedAccounts[email];
              rememberCheck.checked = true;
          } else {
              passInput.value = ''; 
              rememberCheck.checked = false;
          }
      });
  </script>

  <script src="reset.js"></script>
</body>
</html>
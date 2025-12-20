<?php
session_start();
require_once 'connectdb.php';

// DANH SÁCH TỈNH THÀNH (Anh có thể thêm bớt tùy ý)
$cities = [
    "Hà Nội", "Hồ Chí Minh", "Đà Nẵng", "Hải Phòng", "Cần Thơ", 
    "An Giang", "Bà Rịa - Vũng Tàu", "Bắc Giang", "Bắc Kạn", "Bạc Liêu",
    "Bắc Ninh", "Bến Tre", "Bình Định", "Bình Dương", "Bình Phước",
    "Bình Thuận", "Cà Mau", "Cao Bằng", "Đắk Lắk", "Đắk Nông",
    "Điện Biên", "Đồng Nai", "Đồng Tháp", "Gia Lai", "Hà Giang",
    "Hà Nam", "Hà Tĩnh", "Hải Dương", "Hậu Giang", "Hòa Bình",
    "Hưng Yên", "Khánh Hòa", "Kiên Giang", "Kon Tum", "Lai Châu",
    "Lâm Đồng", "Lạng Sơn", "Lào Cai", "Long An", "Nam Định",
    "Nghệ An", "Ninh Bình", "Ninh Thuận", "Phú Thọ", "Quảng Bình",
    "Quảng Nam", "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", "Sóc Trăng",
    "Sơn La", "Tây Ninh", "Thái Bình", "Thái Nguyên", "Thanh Hóa",
    "Thừa Thiên Huế", "Tiền Giang", "Trà Vinh", "Tuyên Quang", "Vĩnh Long",
    "Vĩnh Phúc", "Yên Bái","Hà Tĩnh"
];

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu
    $full_name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = isset($_POST['address']) ? trim($_POST['address']) : ""; 
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // --- VALIDATION ---

    // 1. Kiểm tra rỗng
    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($password) || empty($confirm_password)) {
        $error_message = "Vui lòng nhập đầy đủ thông tin!";
    }
    // 2. Validate Tên
    elseif (strlen($full_name) < 3) {
        $error_message = "Tên hiển thị phải có ít nhất 5 ký tự!";
    }
    // 3. Validate SĐT
    elseif (!preg_match('/^[0-9]{10,}$/', $phone)) {
        $error_message = "Số điện thoại không hợp lệ!";
    }
    // 4. Validate Mật khẩu
    elseif (strlen($password) < 8 || !preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error_message = "Mật khẩu yếu! Cần 8 ký tự gồm chữ và số.";
    }
    // 5. Validate Khớp mật khẩu
    elseif ($password !== $confirm_password) {
        $error_message = "Mật khẩu nhập lại không khớp!";
    } 
    else {
        // --- LOGIC DATABASE ---
        $safe_email = $conn->real_escape_string($email);
        $check_sql = "SELECT * FROM members WHERE email = '$safe_email'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            $error_message = "Email này đã được đăng ký rồi!";
        } else {
            $safe_fullname = $conn->real_escape_string($full_name);
            $safe_phone = $conn->real_escape_string($phone);
            $safe_address = $conn->real_escape_string($address);
            $safe_password = $conn->real_escape_string($password); 

            $sql = "INSERT INTO members (full_name, email, password, phone_number, address) 
                    VALUES ('$safe_fullname', '$safe_email', '$safe_password', '$safe_phone', '$safe_address')";

            if ($conn->query($sql) === TRUE) {
                $success_message = "Đăng ký thành công! Đang chuyển hướng...";
                echo "<script>setTimeout(function(){ window.location.href = 'login.php'; }, 2000);</script>";
            } else {
                $error_message = "Lỗi hệ thống: " . $conn->error;
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
  <title>Sign Up - FitPhysique</title>
  <link rel="stylesheet" href="styles.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  
  <style>
    /* CSS THÔNG BÁO TOAST */
    #toast-box {
        position: fixed; top: -100px; left: 50%; transform: translateX(-50%);
        background: #fff; padding: 15px 25px; border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: flex; align-items: center; gap: 10px;
        font-size: 16px; font-weight: 500; z-index: 1000;
        transition: top 0.5s ease-in-out; min-width: 300px; justify-content: center;
    }
    #toast-box.show { top: 30px; }
    .toast-error { border-left: 5px solid #ff4d4d; color: #ff4d4d; }
    .toast-error i { font-size: 24px; }
    .toast-success { border-left: 5px solid #2ecc71; color: #2ecc71; }
    .toast-success i { font-size: 24px; }

    /* --- CSS CHO COMBOBOX (SELECT) --- */
    .input-box select {
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        border: none;
        outline: none;
        border-radius: 40px;
        font-size: 16px;
        color: #333; 
        padding: 0 45px 0 20px; 
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;
    }
    .input-box select:focus {
        background: #fff;
    }
  </style>
</head>
<body>

  <?php if($error_message != ""): ?>
      <div id="toast-box" class="toast-error">
          <i class='bx bxs-error-circle'></i> <span><?php echo $error_message; ?></span>
      </div>
  <?php endif; ?>
  <?php if($success_message != ""): ?>
      <div id="toast-box" class="toast-success">
          <i class='bx bxs-check-circle'></i> <span><?php echo $success_message; ?></span>
      </div>
  <?php endif; ?>

  <div class="wrapper">
    <form action="" method="POST">
      <h1>Register</h1>

      <div class="input-box">
        <input type="text" name="fullname" placeholder="Full Name" 
               value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
        <i class='bx bxs-user'></i>
      </div>

      <div class="input-box">
        <input type="email" name="email" placeholder="Email Address" 
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        <i class='bx bxs-envelope'></i>
      </div>

      <div class="input-box">
        <input type="text" name="phone" placeholder="Phone Number" 
               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
        <i class='bx bxs-phone'></i>
      </div>

      <div class="input-box">
        <select name="address">
            <option value="" disabled <?php echo !isset($_POST['address']) ? 'selected' : ''; ?>>Chọn Tỉnh/Thành phố</option>
            
            <?php 
                // Vòng lặp in danh sách thành phố
                foreach ($cities as $city) {
                    // Logic: Nếu form submit lỗi, giữ lại thành phố đã chọn
                    $selected = (isset($_POST['address']) && $_POST['address'] == $city) ? 'selected' : '';
                    echo "<option value='$city' $selected>$city</option>";
                }
            ?>
        </select>
        <i class='bx bxs-map'></i>
      </div>

      <div class="input-box">
        <input type="password" name="password" placeholder="Password">
        <i class='bx bxs-lock-alt' ></i>
      </div>

      <div class="input-box">
        <input type="password" name="confirm_password" placeholder="Confirm Password">
        <i class='bx bxs-lock-alt' ></i>
      </div>

      <button type="submit" class="btn">Register</button>

      <div class="register-link">
        <p>Already have an account? <a href="login.php">Login</a></p>
      </div>
    </form>
  </div>

  <script>
    const toastBox = document.getElementById('toast-box');
    if (toastBox) {
        setTimeout(() => { toastBox.classList.add('show'); }, 100);
        setTimeout(() => { toastBox.classList.remove('show'); }, 3000);
    }
  </script>

</body>
</html>
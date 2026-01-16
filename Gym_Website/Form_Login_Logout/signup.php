<?php
session_start();
require_once 'connectdb.php';

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
    "Vĩnh Phúc", "Yên Bái"
];

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = isset($_POST['address']) ? trim($_POST['address']) : ""; 
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : "";
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // --- VALIDATION ---
    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($gender) || empty($password) || empty($confirm_password)) {
        $error_message = "Vui lòng nhập đầy đủ thông tin!";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Định dạng Email không hợp lệ (ví dụ: abc@gmail.com)!";
    }
    elseif (strlen($full_name) < 5) {
        $error_message = "Tên hiển thị phải có ít nhất 5 ký tự!";
    }
    elseif (!preg_match('/^[0-9]{10,}$/', $phone)) {
        $error_message = "Số điện thoại không hợp lệ (phải là số, ít nhất 10 số)!";
    }
    elseif (strlen($password) < 8 || !preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error_message = "Mật khẩu yếu! Cần ít nhất 8 ký tự, bao gồm cả chữ và số.";
    }
    elseif ($password !== $confirm_password) {
        $error_message = "Mật khẩu nhập lại không khớp!";
    } 
    else {
        $safe_email = $conn->real_escape_string($email);
        
        // Kiểm tra xem Email đã tồn tại chưa
        $check_sql = "SELECT * FROM members WHERE email = '$safe_email'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            $error_message = "Email này đã được đăng ký rồi! Vui lòng dùng email khác.";
        } else {
            $safe_fullname = $conn->real_escape_string($full_name);
            $safe_phone = $conn->real_escape_string($phone);
            $safe_address = $conn->real_escape_string($address);
            $safe_gender = $conn->real_escape_string($gender);
            $safe_password = $conn->real_escape_string($password); 
            $sql = "INSERT INTO members (full_name, email, password, phone_number, address, gender) 
                    VALUES ('$safe_fullname', '$safe_email', '$safe_password', '$safe_phone', '$safe_address', '$safe_gender')";

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
  <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  
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
    <form action="" method="POST" novalidate> 
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
        <select name="gender">
            <option value="" disabled <?php echo !isset($_POST['gender']) ? 'selected' : ''; ?>>Gender (Giới tính)</option>
            <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male (Nam)</option>
            <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female (Nữ)</option>
            <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'selected' : ''; ?>>Other (Khác)</option>
        </select>
        <i class='bx bx-male-female'></i>
      </div>

      <div class="input-box">
        <select name="address">
            <option value="" disabled <?php echo !isset($_POST['address']) ? 'selected' : ''; ?>>City (Tỉnh/Thành phố)</option>
            <?php 
                foreach ($cities as $city) {
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

  <script src="reset.js"></script>
</body>
</html>
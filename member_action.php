<?php
session_start();

// 1. KẾT NỐI DATABASE
$conn = new mysqli("localhost", "root", "", "gymmanagement");
if ($conn->connect_error) { die("Lỗi kết nối: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

// 2. KIỂM TRA QUYỀN TRUY CẬP
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    exit("Bạn không có quyền thực hiện hành động này.");
}

// 3. XỬ LÝ LƯU THÔNG TIN (THÊM/SỬA)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save') {
    $id = $_POST['member_id'];
    $name = $conn->real_escape_string($_POST['full_name']);
    $phone = $conn->real_escape_string($_POST['phone_number']);
    $address = $conn->real_escape_string($_POST['address']);
    $status = $_POST['status'];
    
    if (empty($id)) {
        // THÊM MỚI
        $email = "mem_" . time() . "@fit.com";
        $sql = "INSERT INTO members (full_name, email, password, phone_number, address, gender, status) 
                VALUES ('$name', '$email', '123456', '$phone', '$address', 'Male', '$status')";
        
        if ($conn->query($sql)) {
            $new_id = $conn->insert_id;
            if (!empty($_POST['package_id'])) {
                $pkg_id = $_POST['package_id'];
                $start = $_POST['start_date'];
                $end = $_POST['end_date'];
                
                $conn->query("INSERT INTO member_subscriptions (member_id, package_id, start_date, end_date) 
                             VALUES ('$new_id', '$pkg_id', '$start', '$end')");
                
                $pkg_q = $conn->query("SELECT price FROM membership_packages WHERE package_id = '$pkg_id'");
                $pkg_data = $pkg_q->fetch_assoc();
                $amount = $pkg_data['price'] ?? 0;

                $conn->query("INSERT INTO transactions (member_id, transaction_type, amount, payment_method, transaction_date) 
                             VALUES ('$new_id', 'Registration', '$amount', 'Tiền mặt', NOW())");
            }
        }
    } else {
        // CẬP NHẬT
        $sql = "UPDATE members SET full_name='$name', phone_number='$phone', address='$address', status='$status' 
                WHERE member_id='$id'";
        $conn->query($sql);
    }
    header("Location: members.php?msg=success");
    exit();
}

// 4. XỬ LÝ KHÓA/MỞ TÀI KHOẢN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'toggle_status') {
    $id = $_POST['member_id'];
    $new_st = ($_POST['current_status'] == 'Active') ? 'Inactive' : 'Active';
    $conn->query("UPDATE members SET status='$new_st' WHERE member_id='$id'");
    header("Location: members.php?msg=updated");
    exit();
}

// 5. XỬ LÝ XÓA HỘI VIÊN
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM members WHERE member_id='$id'");
    header("Location: members.php?msg=deleted");
    exit();
}
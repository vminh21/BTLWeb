<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gymmanagement");
if ($conn->connect_error) { die("Lỗi kết nối: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff' ) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['delete_id'])) {
        exit("Bạn không có quyền thực hiện hành động này.");
    } else {
        header("Location: login.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save') {
    $id = $_POST['member_id'];
    $name = $conn->real_escape_string($_POST['full_name']);
    $phone = $conn->real_escape_string($_POST['phone_number']);
    $address = $conn->real_escape_string($_POST['address']);
    $status = $_POST['status'];
    
    if (empty($id)) {
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
        $sql = "UPDATE members SET full_name='$name', phone_number='$phone', address='$address', status='$status' 
                WHERE member_id='$id'";
        $conn->query($sql);
    }
    header("Location: members.php?msg=success");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'toggle_status') {
    $id = $_POST['member_id'];
    $new_st = ($_POST['current_status'] == 'Active') ? 'Inactive' : 'Active';
    $conn->query("UPDATE members SET status='$new_st' WHERE member_id='$id'");
    header("Location: members.php?msg=updated");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM members WHERE member_id='$id'");
    header("Location: members.php?msg=deleted");
    exit();
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_package = isset($_GET['filter_package']) ? $_GET['filter_package'] : '';
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';
$where_clauses = ["1=1"];

if (!empty($search)) { $where_clauses[] = "m.full_name LIKE '%$search%'"; }
if (!empty($filter_package)) { $where_clauses[] = "p.package_id = '$filter_package'"; }
if (!empty($filter_status)) {
    if ($filter_status == 'Expired') { $where_clauses[] = "ms.end_date < CURDATE()"; } 
    elseif ($filter_status == 'Active') { $where_clauses[] = "m.status = 'Active' AND (ms.end_date >= CURDATE() OR ms.end_date IS NULL)"; } 
    else { $where_clauses[] = "m.status = '$filter_status'"; }
}

$where_sql = implode(" AND ", $where_clauses);
$sql = "SELECT m.*, m.status as m_status, ms.start_date, ms.end_date, p.package_name, p.package_id
        FROM members m
        LEFT JOIN (
            SELECT * FROM member_subscriptions 
            WHERE subscription_id IN (SELECT MAX(subscription_id) FROM member_subscriptions GROUP BY member_id)
        ) ms ON m.member_id = ms.member_id
        LEFT JOIN membership_packages p ON ms.package_id = p.package_id
        WHERE $where_sql
        ORDER BY m.member_id DESC";

$result = $conn->query($sql);
$today = date('Y-m-d');
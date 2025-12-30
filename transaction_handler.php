<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$conn = new mysqli("localhost", "root", "", "GymManagement");
if ($conn->connect_error) { die("Lỗi kết nối: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

if (isset($_POST['btn_save'])) {
    $member_id = $_POST['member_id'];
    $end_date = $_POST['end_date'];
    $amount = $_POST['amount'];
    $type = $_POST['transaction_type'];
    $method = $_POST['payment_method'];

    // LOGIC MỚI: Xác định package_id dựa trên số tiền hoặc gói tập
    // 500.000 -> ID 1 (1 tháng), 1.350.000 -> ID 2 (3 tháng), 5.000.000 -> ID 3 (1 năm)
    $package_id = 1; 
    if ($amount == "1350000") {
        $package_id = 2;
    } elseif ($amount == "5000000") {
        $package_id = 3;
    }

    $conn->begin_transaction();
    try {
        // Nếu là thêm người mới hoàn toàn
        if ($member_id === "new_member") {
            $name = $conn->real_escape_string($_POST['new_member_name']);
            // Tạo member đơn giản, không cần email/pass phức tạp
            $conn->query("INSERT INTO members (full_name, status) VALUES ('$name', 'Active')");
            $member_id = $conn->insert_id;
        }

        // Lưu vào bảng giao dịch
        $stmt = $conn->prepare("INSERT INTO transactions (member_id, amount, payment_method, transaction_type, transaction_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("idss", $member_id, $amount, $method, $type);
        $stmt->execute();

        // Cập nhật hoặc thêm mới thời hạn tập vào bảng member_subscriptions
        $check = $conn->query("SELECT * FROM member_subscriptions WHERE member_id = '$member_id'");
        if ($check->num_rows > 0) {
            // Cập nhật gói mới nhất và ngày hết hạn mới
            $stmt_sub = $conn->prepare("UPDATE member_subscriptions SET package_id = ?, end_date = ?, status = 'Active' WHERE member_id = ?");
            $stmt_sub->bind_param("isi", $package_id, $end_date, $member_id);
        } else {
            // Lấy ngày bắt đầu từ form (nếu bạn có thêm trường start_date) hoặc mặc định CURDATE()
            $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d');
            $stmt_sub = $conn->prepare("INSERT INTO member_subscriptions (member_id, package_id, start_date, end_date, status) VALUES (?, ?, ?, ?, 'Active')");
            $stmt_sub->bind_param("iiss", $member_id, $package_id, $start_date, $end_date);
        }
        $stmt_sub->execute();

        $conn->commit();
        header("Location: admin_dashboard.php?msg=success");
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: admin_dashboard.php?msg=error");
    }
    exit();
}

// 2. CẬP NHẬT
if (isset($_POST['btn_update'])) {
    $t_id = $_POST['transaction_id'];
    $m_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $type = $_POST['transaction_type'];
    $method = $_POST['payment_method'];
    $end_date = $_POST['end_date'];

    $conn->begin_transaction();
    try {
        $stmt1 = $conn->prepare("UPDATE transactions SET amount=?, payment_method=?, transaction_type=? WHERE transaction_id=?");
        $stmt1->bind_param("dssi", $amount, $method, $type, $t_id);
        $stmt1->execute();

        $stmt2 = $conn->prepare("UPDATE member_subscriptions SET end_date=? WHERE member_id=?");
        $stmt2->bind_param("si", $end_date, $m_id);
        $stmt2->execute();

        $conn->commit();
        header("Location: admin_dashboard.php?msg=updated");
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: admin_dashboard.php?msg=error");
    }
    exit();
}

// 3. XÓA
if (isset($_GET['delete_id'])) {
    $t_id = intval($_GET['delete_id']);
    $res = $conn->query("SELECT member_id FROM transactions WHERE transaction_id = $t_id");
    if ($row = $res->fetch_assoc()) {
        $m_id = $row['member_id'];
        $conn->begin_transaction();
        try {
            $conn->query("DELETE FROM transactions WHERE transaction_id = $t_id");
            $check = $conn->query("SELECT COUNT(*) FROM transactions WHERE member_id = $m_id");
            if ($check->fetch_row()[0] == 0) {
                $conn->query("DELETE FROM members WHERE member_id = $m_id");
            }
            $conn->commit();
            header("Location: admin_dashboard.php?msg=deleted");
        } catch (Exception $e) {
            $conn->rollback();
            header("Location: admin_dashboard.php?msg=error");
        }
    }
    exit();
}
?>
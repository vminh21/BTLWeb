<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gymmanagement");
$conn->set_charset("utf8mb4");

// 1. XỬ LÝ THÊM
if (isset($_POST['btn_save'])) {
    $member_id = $_POST['member_id'];
    $end_date = $_POST['end_date'];
    $amount = $_POST['amount'];
    $type = $_POST['transaction_type'];
    $method = $_POST['payment_method'];

    $conn->begin_transaction();
    try {
        if ($member_id === "new_member") {
            $name = $conn->real_escape_string($_POST['new_member_name']);
            $email = "mem_" . time() . "@fit.com";
            $conn->query("INSERT INTO members (full_name, email, password, gender, status) VALUES ('$name', '$email', '123456', 'Male', 'Active')");
            $member_id = $conn->insert_id;
        }

        // Lưu transaction
        $stmt = $conn->prepare("INSERT INTO transactions (member_id, amount, payment_method, transaction_type, transaction_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("idss", $member_id, $amount, $method, $type);
        $stmt->execute();

        // Cập nhật subscription
        $check = $conn->query("SELECT * FROM member_subscriptions WHERE member_id = '$member_id'");
        if ($check->num_rows > 0) {
            $stmt_sub = $conn->prepare("UPDATE member_subscriptions SET end_date = ?, status = 'Active' WHERE member_id = ?");
            $stmt_sub->bind_param("si", $end_date, $member_id);
        } else {
            $stmt_sub = $conn->prepare("INSERT INTO member_subscriptions (member_id, package_id, start_date, end_date, status) VALUES (?, 1, CURDATE(), ?, 'Active')");
            $stmt_sub->bind_param("is", $member_id, $end_date);
        }
        $stmt_sub->execute();

        $conn->commit();
        header("Location: admin_dashboard.php?msg=success");
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: admin_dashboard.php?msg=error");
    }
}

// 2. XỬ LÝ CẬP NHẬT (SỬA)
if (isset($_POST['btn_update'])) {
    $t_id = $_POST['transaction_id'];
    $m_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $type = $_POST['transaction_type'];
    $method = $_POST['payment_method'];
    $end_date = $_POST['end_date'];

    $conn->begin_transaction();
    try {
        // Cập nhật tiền và loại giao dịch
        $stmt1 = $conn->prepare("UPDATE transactions SET amount=?, payment_method=?, transaction_type=? WHERE transaction_id=?");
        $stmt1->bind_param("dssi", $amount, $method, $type, $t_id);
        $stmt1->execute();

        // Cập nhật hạn dùng tương ứng của hội viên đó
        $stmt2 = $conn->prepare("UPDATE member_subscriptions SET end_date=? WHERE member_id=?");
        $stmt2->bind_param("si", $end_date, $m_id);
        $stmt2->execute();

        $conn->commit();
        header("Location: admin_dashboard.php?msg=updated");
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: admin_dashboard.php?msg=error");
    }
}

// Đoạn xử lý xóa trong transaction_handler.php
if (isset($_GET['delete_id'])) {
    $t_id = $_GET['delete_id'];
    
    // 1. Lấy member_id trước khi xóa
    $res = $conn->query("SELECT member_id FROM transactions WHERE transaction_id = $t_id");
    if ($row = $res->fetch_assoc()) {
        $m_id = $row['member_id'];

        $conn->begin_transaction();
        try {
            // 2. Xóa giao dịch
            $conn->query("DELETE FROM transactions WHERE transaction_id = $t_id");

            // 3. Kiểm tra: Nếu người này không còn giao dịch nào khác
            $check = $conn->query("SELECT COUNT(*) FROM transactions WHERE member_id = $m_id");
            if ($check->fetch_row()[0] == 0) {
                // Xóa luôn hội viên và gói tập để giảm số lượng "Đang tập" và "Thành viên"
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
<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// KẾT NỐI DATABASE
$conn = new mysqli("localhost", "root", "", "GymManagement");
if ($conn->connect_error) { die("Lỗi kết nối: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

/**
 * Hàm lấy danh sách giao dịch dựa trên bộ lọc
 */
function layDanhSachGiaoDich($conn, $search_name = '', $search_type = '') {
    $where_clauses = ["1=1"];
    
    if (!empty($search_name)) {
        $name = $conn->real_escape_string($search_name);
        $where_clauses[] = "m.full_name LIKE '%$name%'";
    }
    
    if (!empty($search_type)) {
        $type = $conn->real_escape_string($search_type);
        $where_clauses[] = "t.transaction_type = '$type'";
    }
    
    $where_sql = implode(" AND ", $where_clauses);

    $sql = "SELECT t.*, m.full_name, 
            (SELECT MAX(ms.end_date) FROM member_subscriptions ms WHERE ms.member_id = t.member_id) AS end_date 
            FROM transactions t 
            JOIN members m ON t.member_id = m.member_id 
            WHERE $where_sql
            ORDER BY t.transaction_date DESC LIMIT 20";
            
    return $conn->query($sql);
}

// --- XỬ LÝ CÁC HÀNH ĐỘNG (POST/GET) ---

// 1. THÊM MỚI
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

        $stmt = $conn->prepare("INSERT INTO transactions (member_id, amount, payment_method, transaction_type, transaction_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("idss", $member_id, $amount, $method, $type);
        $stmt->execute();

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
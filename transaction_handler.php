<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$conn = new mysqli("localhost", "root", "", "GymManagement");
$conn->set_charset("utf8mb4");

if (isset($_POST['btn_save'])) {
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $type = $_POST['transaction_type'];
    $method = $_POST['payment_method'];

    $has_sub = false;
    if ($member_id !== "new_member") {
        $check_sub = $conn->query("SELECT COUNT(*) FROM member_subscriptions WHERE member_id = '$member_id'");
        $has_sub = ($check_sub->fetch_row()[0] > 0);
    }

    if ($member_id === "new_member" && $type === "Renewal") {
        header("Location: admin_dashboard.php?msg=error_renewal_new"); exit();
    }
    if ($has_sub && $type === "Registration") {
        header("Location: admin_dashboard.php?msg=error_reg_exists"); exit();
    }
    if (!$has_sub && $member_id !== "new_member" && $type === "Renewal") {
        header("Location: admin_dashboard.php?msg=error_renewal_not_found"); exit();
    }

    $months_to_add = 1;
    $package_id = 1;
    if ($amount == "1350000") { $package_id = 2; $months_to_add = 3; }
    elseif ($amount == "5000000") { $package_id = 3; $months_to_add = 12; }

    $conn->begin_transaction();
    try {
        if ($member_id === "new_member") {
            $name = $conn->real_escape_string($_POST['new_member_name']);
            $conn->query("INSERT INTO members (full_name, status) VALUES ('$name', 'Active')");
            $member_id = $conn->insert_id;
        }

        $stmt = $conn->prepare("INSERT INTO transactions (member_id, amount, payment_method, transaction_type, transaction_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("idss", $member_id, $amount, $method, $type);
        $stmt->execute();

        $today = new DateTime();
        $check = $conn->query("SELECT end_date FROM member_subscriptions WHERE member_id = '$member_id'");
        
        if ($check->num_rows > 0) {
            $row = $check->fetch_assoc();
            $current_end_date = new DateTime($row['end_date']);
            $start_calc = ($current_end_date > $today) ? $current_end_date : $today;
            $start_calc->modify("+$months_to_add month");
            $new_end_date = $start_calc->format('Y-m-d');

            $stmt_sub = $conn->prepare("UPDATE member_subscriptions SET package_id = ?, end_date = ?, status = 'Active' WHERE member_id = ?");
            $stmt_sub->bind_param("isi", $package_id, $new_end_date, $member_id);
        } else {
            $new_date = clone $today;
            $new_date->modify("+$months_to_add month");
            $new_end_date = $new_date->format('Y-m-d');
            $today_str = $today->format('Y-m-d');

            $stmt_sub = $conn->prepare("INSERT INTO member_subscriptions (member_id, package_id, start_date, end_date, status) VALUES (?, ?, ?, ?, 'Active')");
            $stmt_sub->bind_param("iiss", $member_id, $package_id, $today_str, $new_end_date);
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

if (isset($_POST['btn_update'])) {
    $t_id = $_POST['transaction_id'];
    $m_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $type = $_POST['transaction_type'];
    $method = $_POST['payment_method'];
    $end_date = $_POST['end_date'];

    if ($type === 'Registration') {
        $check_reg = $conn->query("SELECT transaction_id FROM transactions WHERE member_id = '$m_id' AND transaction_type = 'Registration' AND transaction_id != '$t_id'");
        if ($check_reg->num_rows > 0) {
            header("Location: admin_dashboard.php?msg=error_multiple_reg"); exit();
        }
    }

    $conn->begin_transaction();
    try {
        $stmt1 = $conn->prepare("UPDATE transactions SET amount=?, payment_method=?, transaction_type=? WHERE transaction_id=?");
        $stmt1->bind_param("dssi", $amount, $method, $type, $t_id);
        $stmt1->execute();

        $stmt2 = $conn->prepare("UPDATE member_subscriptions SET end_date=?, status='Active' WHERE member_id=?");
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

if (isset($_GET['delete_id'])) {
    $t_id = intval($_GET['delete_id']);
    $res = $conn->query("SELECT member_id FROM transactions WHERE transaction_id = $t_id");
    
    if ($row = $res->fetch_assoc()) {
        $m_id = $row['member_id'];
        $conn->begin_transaction();
        try {
            $conn->query("DELETE FROM transactions WHERE transaction_id = $t_id");
            $remaining = $conn->query("SELECT amount, transaction_date FROM transactions WHERE member_id = $m_id ORDER BY transaction_date ASC");

            if ($remaining->num_rows > 0) {
                $calc_date = null;
                $first_trans = true;
                $last_package_id = 1;

                while ($t_row = $remaining->fetch_assoc()) {
                    $amt = $t_row['amount'];
                    $months = 1; $current_pkg_id = 1;
                    if ($amt == "1350000") { $months = 3; $current_pkg_id = 2; }
                    elseif ($amt == "5000000") { $months = 12; $current_pkg_id = 3; }

                    if ($first_trans) {
                        $calc_date = new DateTime($t_row['transaction_date']);
                        $first_trans = false;
                    }
                    $calc_date->modify("+$months month");
                    $last_package_id = $current_pkg_id;
                }
                $new_expiry = $calc_date->format('Y-m-d');
                $new_status = (new DateTime($new_expiry) >= new DateTime()) ? 'Active' : 'Expired';

                $stmt_up = $conn->prepare("UPDATE member_subscriptions SET end_date = ?, package_id = ?, status = ? WHERE member_id = ?");
                $stmt_up->bind_param("sisi", $new_expiry, $last_package_id, $new_status, $m_id);
                $stmt_up->execute();
            } else {
                $conn->query("DELETE FROM member_subscriptions WHERE member_id = $m_id");
                $conn->query("DELETE FROM members WHERE member_id = $m_id");
            }
            $conn->commit();
            header("Location: members.php?msg=deleted");
        } catch (Exception $e) {
            $conn->rollback();
            header("Location: members.php?msg=error");
        }
    }
    exit();
}
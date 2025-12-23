<?php
session_start();
require_once 'connectdb.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['member_id'])) {
    die("Vui lòng đăng nhập!");
}

$member_id = $_SESSION['member_id'];

// 2. Xử lý khi bấm nút Thanh toán
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $package_id = intval($_POST['package_id']);
    $payment_method = $_POST['payment_method'];

    // Lấy thông tin gói tập được chọn
    $sql_pkg = "SELECT * FROM membership_packages WHERE package_id = $package_id";
    $result_pkg = $conn->query($sql_pkg);
    
    if ($result_pkg->num_rows == 0) {
        die("Gói tập không tồn tại!");
    }

    $pkg = $result_pkg->fetch_assoc();
    $duration = intval($pkg['duration_days']);
    $price = $pkg['price'];
    $pkg_name = $pkg['package_name'];

    // ==================================================================
    // LOGIC MỚI: KHÔNG CỘNG DỒN (RESET THỜI GIAN TỪ HÔM NAY)
    // ==================================================================
    
    $start_date = date("Y-m-d"); // Bắt đầu từ hôm nay
    $end_date = date('Y-m-d', strtotime("+$duration days")); // Kết thúc = Hôm nay + Số ngày gói

    // BƯỚC 1: Hủy tất cả các gói đang Active cũ (để tránh trùng lặp)
    $conn->query("UPDATE member_subscriptions SET status = 'Expired' WHERE member_id = $member_id AND status = 'Active'");

    // BƯỚC 2: Tạo đăng ký mới (Subscription)
    $sql_sub = "INSERT INTO member_subscriptions (member_id, package_id, start_date, end_date, status) 
                VALUES (?, ?, ?, ?, 'Active')";
    $stmt = $conn->prepare($sql_sub);
    $stmt->bind_param("iiss", $member_id, $package_id, $start_date, $end_date);
    
    if ($stmt->execute()) {
        // BƯỚC 3: Lưu lịch sử giao dịch (Transaction)
        $note = "Đăng ký gói $pkg_name ($duration ngày)";
        $type = 'Registration'; // Hoặc check logic để để 'Renewal' tùy ý, nhưng để Registration cho đơn giản

        $sql_trans = "INSERT INTO transactions (member_id, amount, payment_method, transaction_type, transaction_date, note) 
                      VALUES (?, ?, ?, ?, NOW(), ?)";
        $stmt_trans = $conn->prepare($sql_trans);
        $stmt_trans->bind_param("idsss", $member_id, $price, $payment_method, $type, $note);
        $stmt_trans->execute();

        // Báo thành công và quay về trang cá nhân
        echo "<script>
                alert('Thanh toán thành công! Gói $pkg_name đã được kích hoạt đến ngày " . date("d/m/Y", strtotime($end_date)) . "');
                window.location.href = 'member_profile.php';
              </script>";
    } else {
        echo "Lỗi hệ thống: " . $conn->error;
    }
} else {
    // Nếu ai đó cố tình truy cập trực tiếp file này mà không submit form
    header("Location: member_profile.php");
}
?>
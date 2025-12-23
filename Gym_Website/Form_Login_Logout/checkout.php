<?php
session_start();
require_once 'connectdb.php'; 

// --- 1. KIỂM TRA ĐĂNG NHẬP (ĐÃ ĐỒNG BỘ) ---
if (!isset($_SESSION['member_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để mua gói!'); window.location.href='login.php';</script>";
    exit();
}

// Gán biến để dùng xuống dưới
$member_id = $_SESSION['member_id']; 
$member_name = $_SESSION['full_name'] ?? 'Khách hàng';

// 2. LẤY THÔNG TIN GÓI TỪ DB
if (isset($_GET['package_id'])) {
    $pkg_id = intval($_GET['package_id']);
    
    $sql_pkg = "SELECT * FROM membership_packages WHERE package_id = $pkg_id";
    $result_pkg = $conn->query($sql_pkg);

    if ($result_pkg->num_rows > 0) {
        $package = $result_pkg->fetch_assoc();
    } else {
        die("Gói tập không tồn tại!");
    }
} else {
    die("Lỗi: Không tìm thấy ID gói tập. Vui lòng quay lại chọn gói.");
}

// 3. XỬ LÝ KHI BẤM THANH TOÁN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method']; 
    $transaction_type = 'Registration'; 
    $note = "Đăng ký gói " . $package['package_name'];

    // Tính ngày hết hạn
    $start_date = date("Y-m-d");
    $days = $package['duration_days'];
    $end_date = date("Y-m-d", strtotime("+$days days")); 
    $price = $package['price'];

    $conn->begin_transaction();

    try {
        // A. Lưu Subscription
        // Logic chuẩn: Cột 'member_id' trong DB nhận giá trị từ biến $member_id
        $sql_sub = "INSERT INTO member_subscriptions (member_id, package_id, start_date, end_date, status) 
                    VALUES ('$member_id', '$pkg_id', '$start_date', '$end_date', 'Active')";
        if (!$conn->query($sql_sub)) throw new Exception($conn->error);

        // B. Lưu Giao dịch
        $sql_trans = "INSERT INTO transactions (member_id, amount, payment_method, transaction_type, transaction_date, note) 
                      VALUES ('$member_id', '$price', '$payment_method', '$transaction_type', NOW(), '$note')";
        if (!$conn->query($sql_trans)) throw new Exception($conn->error);

        // C. Kích hoạt Member
        $sql_update_mem = "UPDATE members SET status = 'Active' WHERE member_id = '$member_id'";
        if (!$conn->query($sql_update_mem)) throw new Exception($conn->error);

        $conn->commit();

        echo "<script>
                alert('Thanh toán thành công! Gói cước hết hạn ngày: $end_date');
                window.location.href = '../index.php'; 
              </script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "Lỗi hệ thống: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán - FitPhysique</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .checkout-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: 400px; }
        h2 { text-align: center; color: #333; margin-top: 0; }
        .info-row { display: flex; justify-content: space-between; margin: 15px 0; border-bottom: 1px dashed #ccc; padding-bottom: 10px; }
        .total { font-size: 1.2em; color: #d32f2f; font-weight: bold; }
        select, button { width: 100%; padding: 12px; margin-top: 15px; border-radius: 5px; box-sizing: border-box; }
        button { background: #d32f2f; color: white; border: none; font-weight: bold; cursor: pointer; }
        button:hover { background: #b71c1c; }
        .back-link { text-align: center; margin-top: 15px; display: block; text-decoration: none; color: #666; }
    </style>
</head>
<body>

<div class="checkout-box">
    <h2>XÁC NHẬN ĐƠN HÀNG</h2>
    
    <div class="info-row">
        <span>Khách hàng:</span>
        <strong><?php echo htmlspecialchars($member_name); ?></strong>
    </div>

    <div class="info-row">
        <span>Gói đăng ký:</span>
        <strong><?php echo htmlspecialchars($package['package_name']); ?></strong>
    </div>

    <div class="info-row">
        <span>Thời hạn:</span>
        <strong><?php echo $package['duration_days']; ?> ngày</strong>
    </div>

    <div class="info-row">
        <span>Tổng tiền:</span>
        <span class="total"><?php echo number_format($package['price'], 0, ',', '.'); ?> VNĐ</span>
    </div>

    <form method="POST">
        <label for="payment_method">Hình thức thanh toán:</label>
        <select name="payment_method" required>
            <option value="Tiền mặt">Tiền mặt (Tại quầy)</option>
            <option value="Chuyển khoản">Chuyển khoản Ngân hàng</option>
            <option value="Momo">Ví Momo</option>
        </select>

        <button type="submit">THANH TOÁN NGAY</button>
    </form>
    
    <a href="../index.php" class="back-link">Hủy bỏ</a>
</div>

</body>
</html>
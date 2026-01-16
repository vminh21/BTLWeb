<?php
session_start();
require_once 'connectdb.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['member_id'])) { die("Vui lòng đăng nhập!"); }
$member_id = $_SESSION['member_id'];
$show_qr = false; 

// =======================================================================
// HÀM HỖ TRỢ
// =======================================================================

function execPostRequest($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data))
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function activatePackage($conn, $member_id, $package_id, $payment_method) {
    $sql_pkg = "SELECT * FROM membership_packages WHERE package_id = ?";
    $stmt_pkg = $conn->prepare($sql_pkg);
    $stmt_pkg->bind_param("i", $package_id);
    $stmt_pkg->execute();
    $pkg = $stmt_pkg->get_result()->fetch_assoc();

    if (!$pkg) return false;

    $duration = intval($pkg['duration_days']);
    $price = $pkg['price'];
    $pkg_name = $pkg['package_name'];
    
    $start_date = date("Y-m-d");
    $end_date = date('Y-m-d', strtotime("+$duration days"));

    $conn->query("UPDATE member_subscriptions SET status = 'Expired' WHERE member_id = $member_id AND status = 'Active'");
    
    $sql_sub = "INSERT INTO member_subscriptions (member_id, package_id, start_date, end_date, status) VALUES (?, ?, ?, ?, 'Active')";
    $stmt = $conn->prepare($sql_sub);
    $stmt->bind_param("iiss", $member_id, $package_id, $start_date, $end_date);
    
    if ($stmt->execute()) {
        $note = "Thanh toán ($payment_method): " . $pkg_name;
        $sql_trans = "INSERT INTO transactions (member_id, amount, payment_method, transaction_type, note) VALUES (?, ?, ?, 'Registration', ?)";
        $stmt_t = $conn->prepare($sql_trans);
        $stmt_t->bind_param("idss", $member_id, $price, $payment_method, $note);
        $stmt_t->execute();
        return true;
    }
    return false;
}

// =======================================================================
// XỬ LÝ KẾT QUẢ MOMO TRẢ VỀ (GET)
// =======================================================================
if (isset($_GET['partnerCode']) && isset($_GET['resultCode'])) {
    $resultCode = $_GET['resultCode'];
    $package_id = isset($_GET['extraData']) ? intval($_GET['extraData']) : 0;

    if ($resultCode == 0) { 
        if (activatePackage($conn, $member_id, $package_id, "Momo E-Wallet")) {
            echo "<script>alert('✅ Thanh toán MoMo thành công! Gói tập đã kích hoạt.'); window.location.href='member_profile.php';</script>";
        } else {
            echo "<script>alert('Lỗi kích hoạt gói sau thanh toán.'); window.location.href='member_profile.php';</script>";
        }
    } else {
        echo "<script>alert('❌ Thanh toán MoMo thất bại hoặc bị hủy.'); window.location.href='member_profile.php';</script>";
    }
    exit();
}

// =======================================================================
// XỬ LÝ FORM GỬI LÊN (POST)
// =======================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['confirm_qr_payment'])) {
        $package_id = intval($_POST['package_id']);
        if (activatePackage($conn, $member_id, $package_id, "Chuyển khoản")) {
            echo "<script>alert('✅ Xác nhận chuyển khoản thành công! Gói tập đã kích hoạt.'); window.location.href='member_profile.php';</script>";
            exit();
        }
    }

    $package_id = intval($_POST['package_id']);
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : ''; 
    
    $sql_check = "SELECT * FROM membership_packages WHERE package_id = $package_id";
    $pkg_info = $conn->query($sql_check)->fetch_assoc();
    $price = $pkg_info['price'];

    if ($payment_method == "Tiền mặt") {
        if (activatePackage($conn, $member_id, $package_id, "Tiền mặt")) {
            echo "<script>alert('✅ Đăng ký thành công! Vui lòng thanh toán tại quầy.'); window.location.href='member_profile.php';</script>";
            exit();
        }
    }
    elseif ($payment_method == "Chuyển khoản") {
        $show_qr = true;
        $my_bank = "TECHCOMBANK";         
        $my_stk = "1117122005";  
        $account_name = "NHU TUNG LAM"; 
        $content = "GYM" . $member_id . "PKG" . $package_id; 
        $qr_url = "https://img.vietqr.io/image/$my_bank-$my_stk-compact2.png?amount=$price&addInfo=$content&accountName=$account_name";
    }
    elseif ($payment_method == "Momo") {
        
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = "MOMOBKUN20180529";
        $accessKey = "klm05TvNBzhg7h7j";
        $secretKey = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa";
        
        $orderInfo = "Thanh toan goi tap GYM MASTER";
        
        // --- SỬA LỖI Ở ĐÂY: Ép kiểu về số nguyên (int) để mất .00 ---
        $amount = strval((int)$price); 
        
        $orderId = time() . ""; 
        $requestId = time() . "";
        $extraData = strval($package_id);
        
        // Link máy ông
        $domain = "http://localhost/BTLWeb/Gym_Website"; 
        $redirectUrl = $domain . "/QL_Profile/process_payment.php"; 
        $ipnUrl = $domain . "/QL_Profile/process_payment.php";

        $rawHash = "accessKey=".$accessKey."&amount=".$amount."&extraData=".$extraData."&ipnUrl=".$ipnUrl."&orderId=".$orderId."&orderInfo=".$orderInfo."&partnerCode=".$partnerCode."&redirectUrl=".$redirectUrl."&requestId=".$requestId."&requestType=captureWallet";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "GYM MASTER",
            'storeId' => 'GymStore',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => 'captureWallet',
            'signature' => $signature
        );

        $result = execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);

        if (isset($jsonResult['payUrl'])) {
            header('Location: ' . $jsonResult['payUrl']);
            exit();
        } else {
            // Nếu vẫn lỗi thì in ra xem
            echo "<script>alert('Lỗi MoMo: " . $jsonResult['message'] . "'); window.history.back();</script>";
            exit();
        }
    }
}
?>

<?php if ($show_qr): ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh Toán Chuyển Khoản</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="process_payment.css">
</head>
<body>
<div class="payment-container">
    <div class="header">
        <div class="header-icon"><i class="ri-bank-card-2-line"></i></div>
        <h2>Thanh Toán Chuyển Khoản</h2>
        <p>Quét mã VietQR để kích hoạt ngay</p>
    </div>
    <div class="body">
        <div class="amount-box">
            <div class="label">Tổng thanh toán</div>
            <div class="amount"><?= number_format($price) ?> đ</div>
        </div>
        <div class="qr-wrapper"><img src="<?= $qr_url ?>" alt="QR Code" class="qr-img"></div>
        <div class="info-list">
            <div class="info-item"><span class="info-label">Ngân hàng</span><span class="info-value"><?= $my_bank ?></span></div>
            <div class="info-item"><span class="info-label">Số tài khoản</span><span class="info-value"><?= $my_stk ?></span></div>
            <div class="info-item"><span class="info-label">Chủ tài khoản</span><span class="info-value" style="text-transform: uppercase;"><?= $account_name ?></span></div>
            <div class="info-item"><span class="info-label">Nội dung</span><span class="info-value highlight"><?= $content ?></span></div>
        </div>
        <form method="POST">
            <input type="hidden" name="package_id" value="<?= $package_id ?>">
            <input type="hidden" name="confirm_qr_payment" value="1">
            <button type="submit" class="btn-confirm"><i class="ri-checkbox-circle-line"></i> Xác nhận đã chuyển tiền</button>
        </form>
        <button class="btn-back" onclick="window.history.back()"><i class="ri-arrow-left-line"></i> Quay lại</button>
    </div>
</div>
</body>
</html>
<?php exit(); endif; ?>
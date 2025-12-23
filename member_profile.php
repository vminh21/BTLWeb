<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'connectdb.php';

/* ================== KIỂM TRA ĐĂNG NHẬP ================== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../Login/login.php");
    exit();
}

$member_id = $_SESSION['user_id'];
$success = "";
$error = "";

/* 1. LẤY THÔNG TIN HỘI VIÊN (Sửa SELECT * để lấy cả address, gender) */
$sql = "SELECT * FROM members WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

/* 2. LẤY GÓI TẬP ĐANG ACTIVE */
$sql_active = "SELECT p.package_name, s.end_date, s.package_id 
               FROM member_subscriptions s
               JOIN membership_packages p ON s.package_id = p.package_id
               WHERE s.member_id = ? AND s.status = 'Active' AND s.end_date >= CURDATE()
               ORDER BY s.end_date DESC LIMIT 1";
$stmt_active = $conn->prepare($sql_active);
$stmt_active->bind_param("i", $member_id);
$stmt_active->execute();
$active_sub = $stmt_active->get_result()->fetch_assoc();

// Tính ngày còn lại
$days_left = 0;
if ($active_sub) {
    $today = new DateTime();
    $expiry = new DateTime($active_sub['end_date']);
    if ($expiry >= $today) {
        $days_left = $today->diff($expiry)->days + 1;
    }
}

/* 3. XỬ LÝ CẬP NHẬT HỒ SƠ (Đã thêm Address và Gender) */
if (isset($_POST['update_profile'])) {
    $name  = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']); // Mới
    $gender = $_POST['gender'];         // Mới
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Kiểm tra mật khẩu
    if ($password !== "") {
        if ($password !== $confirm_password) {
            $error = "Mật khẩu xác nhận không khớp!";
        } else {
            // Cập nhật CÓ đổi mật khẩu (Thêm address, gender)
            $sql = "UPDATE members SET full_name = ?, email = ?, address = ?, gender = ?, password = ? WHERE member_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $name, $email, $address, $gender, $password, $member_id);
            
            if ($stmt->execute()) {
                $success = "Đổi mật khẩu & cập nhật thành công!";
                // Cập nhật lại biến hiển thị ngay lập tức
                $member['full_name'] = $name; $member['email'] = $email;
                $member['address'] = $address; $member['gender'] = $gender;
            } else { $error = "Lỗi: " . $conn->error; }
        }
    } else {
        // Cập nhật KHÔNG đổi mật khẩu (Thêm address, gender)
        $sql = "UPDATE members SET full_name = ?, email = ?, address = ?, gender = ? WHERE member_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $address, $gender, $member_id);
        
        if ($stmt->execute()) {
            $success = "Cập nhật thông tin thành công!";
            $member['full_name'] = $name; $member['email'] = $email;
            $member['address'] = $address; $member['gender'] = $gender;
        } else { $error = "Lỗi: " . $conn->error; }
    }
}

/* 4. LẤY DỮ LIỆU GÓI & LỊCH SỬ */
$packages = $conn->query("SELECT * FROM membership_packages");
$sql_trans = "SELECT * FROM transactions WHERE member_id = ? ORDER BY transaction_date DESC";
$stmt_trans = $conn->prepare($sql_trans);
$stmt_trans->bind_param("i", $member_id);
$stmt_trans->execute();
$transactions = $stmt_trans->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Hội Viên</title>
    <link rel="stylesheet" href="member_profile.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* SỬA LỖI INPUT & ICON */
        .input-wrapper { position: relative; width: 100%; display: flex; align-items: center; }
        .input-wrapper input, .input-wrapper select { width: 100%; position: relative; z-index: 1; padding-left: 45px; padding-right: 50px; }
        .input-wrapper > i:not(.eye-icon) { position: absolute; left: 15px; color: #666; z-index: 2; pointer-events: none; }
        .input-wrapper .eye-icon { position: absolute; right: 0; top: 0; height: 100%; width: 45px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #a0a0a0; z-index: 10; }
        .input-wrapper .eye-icon:hover { color: #d92027; }
        
        .active-package { margin-top: 5px; font-size: 0.9rem; color: #00c851; font-weight: 700; display: flex; align-items: center; gap: 5px; }
        .days-left { font-size: 0.8rem; color: #e0e0e0; margin-top: 2px; }
        .days-left b { color: #ffeb3b; }

        /* --- CSS GÓI ĐANG SỬ DỤNG --- */
        .membership__card.current-pack {
            border: 2px solid #00c851 !important; 
            background: rgba(0, 200, 81, 0.05);
            box-shadow: 0 0 15px rgba(0, 200, 81, 0.2);
        }
        .tag-active {
            position: absolute; top: 0; right: 0;
            background: #00c851; color: white;
            font-size: 0.7rem; font-weight: bold;
            padding: 4px 10px; border-bottom-left-radius: 8px;
        }
        .btn-card.active-btn {
            background: #00c851; border-color: #00c851; color: white; cursor: default;
        }
        .btn-card.active-btn:hover { background: #00c851; transform: none; box-shadow: none; }
    </style>
</head>
<body>
<div class="main-wrapper">
    <aside class="sidebar">
        <div class="logo"><i class="ri-fitness-fill"></i> <span>GYM MASTER</span></div>
        <div class="user-preview">
            <div class="avatar"><?= strtoupper(substr($member['full_name'], 0, 1)) ?></div>
            <div>
                <h4><?= htmlspecialchars($member['full_name']) ?></h4>
                <?php if($active_sub): ?>
                    <p class="active-package"><i class="ri-vip-crown-fill" style="color:#ffeb3b"></i> <?= $active_sub['package_name'] ?></p>
                    <p class="days-left">Còn lại: <b><?= $days_left ?> ngày</b></p>
                    <p style="font-size:0.75rem; color:#666;">(Hết hạn: <?= date("d/m/Y", strtotime($active_sub['end_date'])) ?>)</p>
                <?php else: ?>
                    <p style="font-size:0.8rem; color:#a0a0a0; margin-top:5px;">Chưa đăng ký gói</p>
                <?php endif; ?>
            </div>
        </div>
        <ul class="nav-links">
            <li class="active" onclick="location.reload()"><i class="ri-user-settings-line"></i> Quản lý tài khoản</li>
            <li onclick="window.location.href='logout.php'"><i class="ri-logout-box-line"></i> Đăng xuất</li>
        </ul>
    </aside>

    <main class="content-area">
        <header>
            <h1>Xin chào, <?= htmlspecialchars($member['full_name']) ?>! 👋</h1>
            <p>Quản lý thông tin và gói tập của bạn tại đây.</p>
        </header>

        <?php if ($success): ?><div class="alert success"><i class="ri-checkbox-circle-fill"></i> <?= $success ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert error"><i class="ri-error-warning-fill"></i> <?= $error ?></div><?php endif; ?>

        <div class="tabs-container">
            <nav class="tabs-nav">
                <button class="tab-btn active" onclick="showTab(event,'profile')"><i class="ri-user-line"></i> Hồ sơ cá nhân</button>
                <button class="tab-btn" onclick="showTab(event,'membership')"><i class="ri-vip-crown-line"></i> Gói hội viên</button>
                <button class="tab-btn" onclick="showTab(event,'history')"><i class="ri-history-line"></i> Lịch sử giao dịch</button>
            </nav>

            <div class="tab-content">
                <div id="profile" class="section active">
                    <h3 class="section-title">Chỉnh sửa thông tin</h3>
                    <form method="post" class="modern-form" onsubmit="return validatePassword()">
                        <div class="form-group">
                            <label>Họ và tên</label>
                            <div class="input-wrapper">
                                <i class="ri-user-smile-line"></i>
                                <input type="text" name="full_name" value="<?= htmlspecialchars($member['full_name']) ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <div class="input-wrapper">
                                <i class="ri-mail-line"></i>
                                <input type="email" name="email" value="<?= htmlspecialchars($member['email']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <div class="input-wrapper">
                                <i class="ri-map-pin-line"></i>
                                <input type="text" name="address" value="<?= htmlspecialchars($member['address'] ?? '') ?>" placeholder="Nhập địa chỉ của bạn...">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Giới tính</label>
                            <div class="input-wrapper">
                                <i class="ri-men-line"></i>
                                <select name="gender">
                                    <option value="Male" <?= ($member['gender'] == 'Male') ? 'selected' : '' ?>>Nam</option>
                                    <option value="Female" <?= ($member['gender'] == 'Female') ? 'selected' : '' ?>>Nữ</option>
                                    <option value="Other" <?= ($member['gender'] == 'Other') ? 'selected' : '' ?>>Khác</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Mật khẩu mới</label>
                            <div class="input-wrapper">
                                <i class="ri-lock-password-line"></i>
                                <input type="password" name="password" id="newPass" placeholder="••••••••">
                                <i class="ri-eye-off-line eye-icon" onclick="togglePass('newPass', this)"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Xác nhận mật khẩu</label>
                            <div class="input-wrapper">
                                <i class="ri-key-2-line"></i>
                                <input type="password" name="confirm_password" id="confirmPass" placeholder="Nhập lại mật khẩu">
                                <i class="ri-eye-off-line eye-icon" onclick="togglePass('confirmPass', this)"></i>
                            </div>
                        </div>
                        <button class="btn-submit" name="update_profile">Lưu thay đổi <i class="ri-save-line"></i></button>
                    </form>
                </div>

                <div id="membership" class="section">
                    <div class="pricing-header">
                        <h3>Các gói tập nổi bật</h3>
                        <p style="padding: 12px 0;">Chọn gói phù hợp nhất với mục tiêu của bạn</p>
                    </div>
                    <div class="membership__grid">
                        <?php 
                        $packages->data_seek(0);
                        while($p = $packages->fetch_assoc()): 
                            $isCurrent = ($active_sub && $active_sub['package_id'] == $p['package_id']);
                            // Chỉ hiện HOT nếu chưa có gói nào active
                            $isPopular = ($p['package_id'] == 2 && !$active_sub) ? 'popular' : '';
                            
                            if ($isCurrent) { $cardClass = 'current-pack'; } 
                            else { $cardClass = $isPopular; }
                        ?>
                        <div class="membership__card <?= $cardClass ?>">
                            <?php if($isCurrent): ?>
                                <div class="tag-active"><i class="ri-check-double-line"></i> ĐÃ ĐĂNG KÝ</div>
                            <?php elseif($isPopular): ?>
                                <div class="tag">HOT</div>
                            <?php endif; ?>

                            <div class="card-header">
                                <h4><?= strtoupper($p['package_name']) ?></h4>
                                <h3><?= number_format($p['price']) ?>đ<span>/ <?= $p['duration_days'] ?> ngày</span></h3>
                            </div>
                            <ul>
                                <li><i class="ri-check-line"></i> <?= htmlspecialchars($p['description']) ?></li>
                                <li><i class="ri-check-line"></i> Sử dụng thiết bị cao cấp</li>
                            </ul>
                            
                            <?php if($isCurrent): ?>
                                <button class="btn-card active-btn" type="button">Đang sử dụng</button>
                            <?php else: ?>
                                <button class="btn-card <?= $isPopular ? 'primary' : '' ?>" onclick="selectPackage(<?= $p['package_id'] ?>)">Chọn gói này</button>
                            <?php endif; ?>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <hr class="divider">
                    
                    <h3 class="section-title" id="register-form">Đăng ký / Nâng cấp gói</h3>
                    <form class="modern-form row-form" action="process_payment.php" method="POST">
                        <div class="form-group">
                            <label>Chọn gói hội viên</label>
                            <div class="input-wrapper">
                                <i class="ri-box-3-line"></i>
                                <select name="package_id" id="packageSelect">
                                    <?php $packages->data_seek(0); while ($p = $packages->fetch_assoc()): ?>
                                        <option value="<?= $p['package_id'] ?>"><?= $p['package_name'] ?> - <?= number_format($p['price']) ?> VNĐ</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Hình thức thanh toán</label>
                            <div class="input-wrapper">
                                <i class="ri-bank-card-line"></i>
                                <select name="payment_method">
                                    <option value="Tiền mặt">Tiền mặt tại quầy</option>
                                    <option value="Chuyển khoản">Chuyển khoản ngân hàng</option>
                                    <option value="Momo">Ví Momo / ZaloPay</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn-submit full-width">Thanh toán ngay</button>
                    </form>
                </div>

                <div id="history" class="section">
                    <h3 class="section-title">Lịch sử giao dịch</h3>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Ngày</th><th>Số tiền</th><th>Hình thức</th><th>Loại GD</th><th>Trạng thái</th></tr></thead>
                            <tbody>
                                <?php if($transactions->num_rows > 0): while ($t = $transactions->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date("d/m/Y", strtotime($t['transaction_date'])) ?></td>
                                    <td class="amount"><?= number_format($t['amount']) ?> đ</td>
                                    <td><?= $t['payment_method'] ?></td>
                                    <td><span class="badge"><?= $t['transaction_type'] ?></span></td>
                                    <td><span class="status-success">Thành công</span></td>
                                </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="5" class="empty-state">Chưa có giao dịch nào</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script>
    function showTab(event, id) {
        document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(id).classList.add('active');
        event.currentTarget.classList.add('active');
    }
    
    function selectPackage(id) {
        document.getElementById('packageSelect').value = id;
        document.getElementById('register-form').scrollIntoView({ behavior: 'smooth' });
    }

    function togglePass(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") { input.type = "text"; icon.classList.replace('ri-eye-off-line', 'ri-eye-line'); }
        else { input.type = "password"; icon.classList.replace('ri-eye-line', 'ri-eye-off-line'); }
    }
    function validatePassword() {
        if (document.getElementById('newPass').value !== "" && document.getElementById('newPass').value !== document.getElementById('confirmPass').value) {
            alert("❌ Mật khẩu xác nhận không khớp!"); return false;
        } return true;
    }
</script>
</body>
</html>
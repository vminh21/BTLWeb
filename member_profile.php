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

/* ================== 1. LẤY THÔNG TIN HỘI VIÊN ================== */
$sql = "SELECT member_id, full_name, email FROM members WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();

/* ================== 2. LẤY GÓI TẬP ĐANG KÍCH HOẠT (MỚI) ================== */
// Lấy gói tập còn hạn sử dụng từ bảng member_subscriptions
$sql_active = "SELECT p.package_name, s.end_date 
               FROM member_subscriptions s
               JOIN membership_packages p ON s.package_id = p.package_id
               WHERE s.member_id = ? AND s.status = 'Active' AND s.end_date >= CURDATE()
               ORDER BY s.end_date DESC LIMIT 1";
$stmt_active = $conn->prepare($sql_active);
$stmt_active->bind_param("i", $member_id);
$stmt_active->execute();
$active_sub = $stmt_active->get_result()->fetch_assoc();

/* ================== 3. XỬ LÝ CẬP NHẬT HỒ SƠ ================== */
if (isset($_POST['update_profile'])) {
    $name  = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== "") {
        if ($password !== $confirm_password) {
            $error = "Mật khẩu xác nhận không khớp!";
        } else {
            // Cập nhật password (Plain text theo DB của bạn)
            $sql = "UPDATE members SET full_name = ?, email = ?, password = ? WHERE member_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $name, $email, $password, $member_id);
            if ($stmt->execute()) {
                $success = "Đổi mật khẩu & cập nhật thành công!";
                $member['full_name'] = $name;
                $member['email'] = $email;
            } else {
                $error = "Lỗi: " . $conn->error;
            }
        }
    } else {
        // Chỉ cập nhật thông tin, không đổi pass
        $sql = "UPDATE members SET full_name = ?, email = ? WHERE member_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $email, $member_id);
        if ($stmt->execute()) {
            $success = "Cập nhật thông tin thành công!";
            $member['full_name'] = $name;
            $member['email'] = $email;
        } else {
            $error = "Lỗi: " . $conn->error;
        }
    }
}

/* ================== 4. LẤY DANH SÁCH GÓI TẬP & GIAO DỊCH ================== */
// Lấy danh sách gói (Sửa id thành package_id theo DB)
$packages = $conn->query("SELECT * FROM membership_packages");

// Lấy lịch sử giao dịch
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
    <title>Hồ Sơ Hội Viên - Gym Master</title>
    <link rel="stylesheet" href="member_profile.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
     /* 1. Thiết lập khung bao quanh */
    .input-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
    }

    /* 2. Cấu hình ô nhập liệu (Input) */
    .input-wrapper input {
        width: 100%;
        position: relative;
        z-index: 1; /* Nằm ở mức 1 */
        padding-left: 45px;  /* Chừa chỗ cho icon bên trái */
        padding-right: 50px; /* Chừa chỗ cho con mắt bên phải */
    }

    /* 3. Icon trang trí bên trái (User, Lock...) */
    .input-wrapper > i:not(.eye-icon) {
        position: absolute;
        left: 15px;
        color: #666;
        z-index: 2; /* Nằm trên input về mặt hình ảnh */
        pointer-events: none; /* QUAN TRỌNG: Cho phép chuột bấm xuyên qua icon này để vào ô input */
    }

    /* 4. Icon con mắt bên phải (Eye Icon) */
    .input-wrapper .eye-icon {
        position: absolute;
        right: 0; /* Căn sát phải */
        top: 0;
        height: 100%; /* Chiều cao bằng ô input */
        width: 45px;  /* Chiều rộng cố định để dễ bấm */
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #a0a0a0;
        z-index: 10; /* Nằm cao nhất để bấm được */
        transition: color 0.3s;
    }

    .input-wrapper .eye-icon:hover {
        color: #d92027; /* Đổi màu đỏ khi di chuột vào */
    }

    /* Style hiển thị gói tập (giữ nguyên) */
    .active-package {
        margin-top: 5px; font-size: 0.85rem; color: #00c851; font-weight: 600; display: flex; align-items: center; gap: 5px;
    }
        /* Style cho phần hiển thị gói tập active */
        .active-package {
            margin-top: 5px; font-size: 0.85rem; color: #00c851; font-weight: 600; display: flex; align-items: center; gap: 5px;
        }
    </style>
</head>

<body>
</div>
<div class="main-wrapper">
    <aside class="sidebar">
        <div class="logo">
            <i class="ri-fitness-fill"></i> <span>GYM MASTER</span>
        </div>
        <div class="user-preview">
            <div class="avatar"><?= strtoupper(substr($member['full_name'], 0, 1)) ?></div>
            <div>
                <h4><?= htmlspecialchars($member['full_name']) ?></h4>
                <?php if($active_sub): ?>
                    <p class="active-package"><i class="ri-checkbox-circle-line"></i> <?= $active_sub['package_name'] ?></p>
                <?php else: ?>
                    <p style="font-size: 0.8rem; color: #a0a0a0;">Chưa đăng ký gói</p>
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

        <?php if ($success): ?>
            <div class="alert success"><i class="ri-checkbox-circle-fill"></i> <?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert error"><i class="ri-error-warning-fill"></i> <?= $error ?></div>
        <?php endif; ?>

        <div class="tabs-container">
            <nav class="tabs-nav">
                <button class="tab-btn active" onclick="showTab(event,'profile')">
                    <i class="ri-user-line"></i> Hồ sơ cá nhân
                </button>
                <button class="tab-btn" onclick="showTab(event,'membership')">
                    <i class="ri-vip-crown-line"></i> Gói hội viên
                </button>
                <button class="tab-btn" onclick="showTab(event,'history')">
                    <i class="ri-history-line"></i> Lịch sử giao dịch
                </button>
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
                            <label>Địa chỉ Email</label>
                            <div class="input-wrapper">
                                <i class="ri-mail-line"></i>
                                <input type="email" name="email" value="<?= htmlspecialchars($member['email']) ?>" required>
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
                        <p>Chọn gói phù hợp nhất với mục tiêu của bạn</p>
                    </div>
                    
                    <div class="membership__grid">
                        <?php 
                        // Reset pointer về đầu danh sách
                        $packages->data_seek(0);
                        while($p = $packages->fetch_assoc()): 
                            // Xác định class highlight nếu là gói Standard (ví dụ)
                            $isPopular = ($p['package_id'] == 2) ? 'popular' : '';
                        ?>
                        <div class="membership__card <?= $isPopular ?>">
                            <?php if($isPopular): ?><div class="tag">HOT</div><?php endif; ?>
                            <div class="card-header">
                                <h4><?= strtoupper($p['package_name']) ?></h4>
                                <h3><?= number_format($p['price']) ?>đ<span>/ <?= $p['duration_days'] ?> ngày</span></h3>
                            </div>
                            <ul>
                                <li><span><i class="ri-check-line"></i></span> <?= htmlspecialchars($p['description']) ?></li>
                                <li><span><i class="ri-check-line"></i></span> Sử dụng thiết bị cao cấp</li>
                            </ul>
                            <button class="btn-card <?= $isPopular ? 'primary' : '' ?>" onclick="scrollToForm()">Chọn gói này</button>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <hr class="divider">

                    <h3 class="section-title" id="register-form">Đăng ký / Gia hạn</h3>
                    <form class="modern-form row-form" action="process_payment.php" method="POST">
                        <div class="form-group">
                            <label>Chọn gói hội viên</label>
                            <div class="input-wrapper">
                                <i class="ri-box-3-line"></i>
                                <select name="package_id">
                                    <?php 
                                    $packages->data_seek(0); // Reset lại lần nữa để nạp vào select
                                    while ($p = $packages->fetch_assoc()): ?>
                                        <option value="<?= $p['package_id'] ?>">
                                            <?= $p['package_name'] ?> - <?= number_format($p['price']) ?> VNĐ
                                        </option>
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
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Số tiền</th>
                                    <th>Hình thức</th>
                                    <th>Loại GD</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($transactions->num_rows > 0): ?>
                                    <?php while ($t = $transactions->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= date("d/m/Y", strtotime($t['transaction_date'])) ?></td>
                                        <td class="amount"><?= number_format($t['amount']) ?> đ</td>
                                        <td><?= $t['payment_method'] ?></td>
                                        <td><span class="badge"><?= $t['transaction_type'] ?></span></td>
                                        <td><span class="status-success">Thành công</span></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
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
    function scrollToForm() { document.getElementById('register-form').scrollIntoView({ behavior: 'smooth' }); }
    function togglePass(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text"; icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
        } else {
            input.type = "password"; icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
        }
    }
    function validatePassword() {
        const pass = document.getElementById('newPass').value;
        const confirm = document.getElementById('confirmPass').value;
        if (pass !== "" && pass !== confirm) {
            alert("❌ Mật khẩu xác nhận không khớp!"); return false;
        }
        return true;
    }
</script>
</body>
</html>
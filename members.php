<?php
session_start();
// 1. KẾT NỐI & KIỂM TRA QUYỀN
$conn = new mysqli("localhost", "root", "", "gymmanagement");
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

// 2. LẤY DỮ LIỆU BỘ LỌC TỪ URL
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_package = isset($_GET['filter_package']) ? $_GET['filter_package'] : '';
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';

// 3. XÂY DỰNG CÂU LỆNH SQL CÓ ĐIỀU KIỆN
$where_clauses = ["1=1"];

if (!empty($search)) {
    $where_clauses[] = "m.full_name LIKE '%$search%'";
}
if (!empty($filter_package)) {
    $where_clauses[] = "p.package_id = '$filter_package'";
}
if (!empty($filter_status)) {
    if ($filter_status == 'Expired') {
        $where_clauses[] = "ms.end_date < CURDATE()"; 
    } else {
        $where_clauses[] = "m.status = '$filter_status'"; 
    }
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
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý hội viên - FitPhysique</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="members.css">
</head>
<body>

<div class="sidebar">
    <div class="logo"><h2>FitPhysique<span>Admin</span></h2></div>
    <ul>
        <li><a href="admin_dashboard.php"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
        <li><a href="members.php" class="active"><i class='bx bxs-user-detail'></i> Quản lý thành viên</a></li>
        <li><a href="thongke.php"><i class='bx bxs-report'></i> Báo cáo</a></li>
        <li><a href="admin_thongke.php"><i class='bx bxs-report'></i> Lịch sử giao dịch</a></li>
        <li><a href="admin_thongbao.php"><i class='bx bxs-bell'></i> Thông báo</a></li>
        <li class="logout"><a href="logout1.php"><i class='bx bxs-log-out'></i> Đăng xuất</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header">
        <h1>Danh sách hội viên</h1>
        <div class="user-info">
            <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></strong></span>
            <i class='bx bxs-user-circle'></i>
        </div>
    </div>

    <div class="search-bar-container">
        <form action="members.php" method="GET">
            <div class="search-group">
                <i class='bx bx-search'></i>
                <input type="text" name="search" placeholder="Tìm tên hội viên..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="filter-group">
                <select name="filter_package">
                    <option value="">-- Tất cả gói --</option>
                    <?php
                    $pkgs = $conn->query("SELECT package_id, package_name FROM membership_packages");
                    while($p = $pkgs->fetch_assoc()):
                    ?>
                    <option value="<?= $p['package_id'] ?>" <?= $filter_package == $p['package_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['package_name']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="filter-group">
                <select name="filter_status">
                    <option value="">-- Trạng thái --</option>
                    <option value="Active" <?= $filter_status == 'Active' ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="Inactive" <?= $filter_status == 'Inactive' ? 'selected' : '' ?>>Bị khóa</option>
                    <option value="Expired" <?= $filter_status == 'Expired' ? 'selected' : '' ?>>Hết hạn</option>
                </select>
            </div>

            <button type="submit" class="btn-submit-search">Lọc</button>

            <?php if($search || $filter_package || $filter_status): ?>
                <a href="members.php" class="btn-reset">Xóa lọc</a>
            <?php endif; ?>

            <button type="button" onclick="openAddModal()" class="btn-add-new">
                <i class='bx bx-plus-circle'></i> THÊM HỘI VIÊN
            </button>
        </form>
    </div>

    <div class="recent-transactions">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Họ và Tên</th><th>Gói tập</th><th>Thời hạn</th><th>Trạng thái</th><th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): 
                    $is_expired = ($row['end_date'] && $today > $row['end_date']);
                ?>
                <tr>
                    <td>#<?= $row['member_id'] ?></td>
                    <td><strong><?= htmlspecialchars($row['full_name']) ?></strong></td>
                    <td><span class="badge badge-<?= $row['package_id'] ?? 'none' ?>"><?= htmlspecialchars($row['package_name'] ?? 'Chưa đăng ký') ?></span></td>
                    <td><small><?= $row['end_date'] ? date("d/m/Y", strtotime($row['end_date'])) : '—' ?></small></td>
                    <td>
                        <span class="status <?= ($row['m_status'] == 'Inactive' || $is_expired) ? 'type-renew' : 'type-reg' ?>">
                            <?= $row['m_status'] == 'Inactive' ? 'Bị khóa' : ($is_expired ? 'Hết hạn' : 'Hoạt động') ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="javascript:void(0)" onclick='openEditModal(<?= json_encode($row) ?>)'>
                                <i class='bx bxs-edit icon-edit'></i>
                            </a>
                            
                            <form action="member_action.php" method="POST">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="member_id" value="<?= $row['member_id'] ?>">
                                <input type="hidden" name="current_status" value="<?= $row['m_status'] ?>">
                                <button type="submit">
                                    <i class='bx <?= $row['m_status']=='Active'?'bxs-lock-open':'bxs-lock' ?> icon-lock' 
                                       style="color:<?= $row['m_status']=='Active'?'#2ecc71':'#f1c40f' ?>;"></i>
                                </button>
                            </form>

                            <a href="member_action.php?delete_id=<?= $row['member_id'] ?>" onclick="return confirm('Xác nhận xóa?')">
                                <i class='bx bxs-trash icon-delete'></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="memberModal" class="modal-overlay" onclick="handleOverlayClick(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <h2 id="modalTitle">Thông tin hội viên</h2>
        <form action="member_action.php" method="POST">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="member_id" id="form_id">
            
            <div class="form-group"><label>Họ và Tên *</label><input type="text" name="full_name" id="form_name" required></div>
            <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone_number" id="form_phone"></div>
            <div class="form-group"><label>Địa chỉ</label><input type="text" name="address" id="form_address"></div>

            <div id="package_section">
                <div class="form-group">
                    <label>Gói tập</label>
                    <select name="package_id" id="form_package" onchange="calculateExpiry()">
                        <option value="">-- Không đăng ký gói --</option>
                        <option value="1" data-months="1">Gói 1 Tháng</option>
                        <option value="2" data-months="3">Gói 3 Tháng</option>
                        <option value="3" data-months="12">Gói 1 Năm</option>
                    </select>
                </div>
                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;"><label>Ngày bắt đầu</label><input type="date" name="start_date" id="form_start" value="<?= $today ?>" onchange="calculateExpiry()"></div>
                    <div class="form-group" style="flex:1;"><label>Ngày hết hạn</label><input type="date" name="end_date" id="form_end" readonly style="background:#f5f5f5;"></div>
                </div>
            </div>

            <div class="form-group">
                <label>Trạng thái</label>
                <select name="status" id="form_status">
                    <option value="Active">Hoạt động</option>
                    <option value="Inactive">Khóa</option>
                </select>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeModal()" style="padding: 10px 20px; border-radius: 5px; cursor: pointer; border: 1px solid #ddd;">Hủy</button>
                <button type="submit" style="background:#3498db; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer; font-weight:bold;">Lưu dữ liệu</button>
            </div>
        </form>
    </div>
</div>

<script src="members.js"></script>
</body>
</html>
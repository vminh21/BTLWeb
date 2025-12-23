<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

$conn = new mysqli("localhost", "root", "", "gymmanagement");
$conn->set_charset("utf8mb4");

// TRUY VẤN DỮ LIỆU ĐỂ HIỂN THỊ
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sql = "SELECT m.*, m.status as m_status, ms.start_date, ms.end_date, p.package_name, p.package_id
        FROM members m
        LEFT JOIN (
            SELECT * FROM member_subscriptions 
            WHERE subscription_id IN (SELECT MAX(subscription_id) FROM member_subscriptions GROUP BY member_id)
        ) ms ON m.member_id = ms.member_id
        LEFT JOIN membership_packages p ON ms.package_id = p.package_id
        WHERE m.full_name LIKE '%$search%'
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
        <li><a href="packages.php"><i class='bx bxs-credit-card'></i> Gói tập & Hạn</a></li>
        <li><a href="reports.php"><i class='bx bxs-report'></i> Báo cáo</a></li>
        <li class="logout"><a href="logout1.php"><i class='bx bxs-log-out'></i> Đăng xuất</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header">
        <h1>Danh sách hội viên</h1>
        <div class="user-info" style="display: flex; align-items: center; gap: 10px;">
            <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></strong></span>
            <i class='bx bxs-user-circle' style="font-size: 2rem;"></i>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px;">
        <form action="members.php" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" placeholder="Tìm tên hội viên..." value="<?= htmlspecialchars($search) ?>" 
                   style="padding: 10px 15px; border-radius: 5px; border: 1px solid #ddd; width: 300px;">
            <button type="submit" class="btn-search" style="background:#34495e; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">
                <i class='bx bx-search-alt'></i> Tìm kiếm
            </button>
        </form>
        <button onclick="openAddModal()" class="btn-add-new" style="background:#2ecc71; color:white; border:none; padding:12px 25px; border-radius:5px; cursor:pointer; font-weight:bold;">
            <i class='bx bx-plus-circle'></i> THÊM HỘI VIÊN
        </button>
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
                        <div style="display:flex; gap:12px;">
                            <a href="javascript:void(0)" onclick='openEditModal(<?= json_encode($row) ?>)'><i class='bx bxs-edit' style="color:#3498db; font-size:1.3rem;"></i></a>
                            
                            <form action="member_action.php" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="member_id" value="<?= $row['member_id'] ?>">
                                <input type="hidden" name="current_status" value="<?= $row['m_status'] ?>">
                                <button type="submit" style="border:none; background:none; cursor:pointer;">
                                    <i class='bx <?= $row['m_status']=='Active'?'bxs-lock-open':'bxs-lock' ?>' style="color:<?= $row['m_status']=='Active'?'#2ecc71':'#f1c40f' ?>; font-size:1.3rem;"></i>
                                </button>
                            </form>

                            <a href="member_action.php?delete_id=<?= $row['member_id'] ?>" onclick="return confirm('Xác nhận xóa?')"><i class='bx bxs-trash' style="color:#e74c3c; font-size:1.3rem;"></i></a>
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
                <select name="status" id="form_status"><option value="Active">Hoạt động</option><option value="Inactive">Khóa</option></select>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeModal()">Hủy</button>
                <button type="submit" style="background:#3498db; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">Lưu dữ liệu</button>
            </div>
        </form>
    </div>
</div>

<script src="members.js"></script>
</body>
</html>
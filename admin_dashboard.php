<?php
session_start();

// 1. KIỂM TRA BẢO MẬT
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. KẾT NỐI DATABASE
$conn = new mysqli("localhost", "root", "", "GymManagement");
if ($conn->connect_error) { die("Lỗi kết nối: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

// --- 3. XỬ LÝ TÌM KIẾM ---
$search_name = isset($_GET['search_name']) ? $conn->real_escape_string($_GET['search_name']) : '';
$search_type = isset($_GET['search_type']) ? $conn->real_escape_string($_GET['search_type']) : '';

// Xây dựng điều kiện WHERE động
$where_clauses = ["1=1"];
if (!empty($search_name)) {
    $where_clauses[] = "m.full_name LIKE '%$search_name%'";
}
if (!empty($search_type)) {
    $where_clauses[] = "t.transaction_type = '$search_type'";
}
$where_sql = implode(" AND ", $where_clauses);

// 4. LẤY DỮ LIỆU THỐNG KÊ (Tính toán dựa trên bộ lọc nếu muốn, ở đây tôi giữ nguyên tổng quan)
$total_members = $conn->query("SELECT COUNT(DISTINCT member_id) FROM transactions")->fetch_row()[0] ?? 0;
$active_members = $conn->query("SELECT COUNT(DISTINCT member_id) FROM member_subscriptions WHERE end_date >= CURDATE()")->fetch_row()[0] ?? 0;
$expired_members = $conn->query("SELECT COUNT(DISTINCT member_id) FROM member_subscriptions WHERE end_date < CURDATE()")->fetch_row()[0] ?? 0;
$total_revenue = $conn->query("SELECT SUM(amount) FROM transactions")->fetch_row()[0] ?? 0;

// 5. TRUY VẤN DANH SÁCH GIAO DỊCH (Có áp dụng bộ lọc)
$sql_recent = "SELECT t.*, m.full_name, 
              (SELECT MAX(ms.end_date) FROM member_subscriptions ms WHERE ms.member_id = t.member_id) AS end_date 
              FROM transactions t 
              JOIN members m ON t.member_id = m.member_id 
              WHERE $where_sql
              ORDER BY t.transaction_date DESC LIMIT 20";
$recent_transactions = $conn->query($sql_recent);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitPhysique</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="admin_dashboard.css?v=1.1">
</head>
<body>

    <div class="sidebar">
        <div class="logo"><h2>FitPhysique<span>Admin</span></h2></div>
        <ul>
            <li><a href="admin_dashboard.php" class="active"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
            <li><a href="members.php"><i class='bx bxs-user-detail'></i> Quản lý thành viên</a></li>
            <li><a href="thongke.php"><i class='bx bxs-report'></i> Báo cáo</a></li>
            <li><a href="admin_thongke.php"><i class='bx bxs-report'></i> Lịch sử giao dịch</a></li>
            <li><a href="admin_thongbao.php"><i class='bx bxs-bell'></i> Thông báo</a></li>
            <li class="logout"><a href="logout1.php"><i class='bx bxs-log-out'></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Tổng quan hệ thống</h1>
            <div class="user-info">
                <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></strong></span>
                <i class='bx bxs-user-circle'></i>
            </div>
        </div>

        <div class="cards">
            <div class="card"><div class="card-info"><h3><?= number_format($total_members) ?></h3><p>Thành Viên</p></div><div class="card-icon blue"><i class='bx bxs-group'></i></div></div>
            <div class="card"><div class="card-info"><h3><?= number_format($active_members) ?></h3><p>Đang Tập</p></div><div class="card-icon green"><i class='bx bxs-check-circle'></i></div></div>
            <div class="card"><div class="card-info"><h3><?= number_format($expired_members) ?></h3><p>Hết Hạn</p></div><div class="card-icon red"><i class='bx bxs-x-circle'></i></div></div>
            <div class="card"><div class="card-info"><h3><?= number_format($total_revenue, 0, ',', '.') ?>đ</h3><p>Doanh Thu</p></div><div class="card-icon gold"><i class='bx bxs-wallet'></i></div></div>
        </div>

        <div class="search-bar-container">
    <form method="GET" action="admin_dashboard.php">
        <div class="search-group">
            <i class='bx bx-search'></i>
            <input type="text" name="search_name" placeholder="Nhập tên hội viên cần tìm..." value="<?= htmlspecialchars($search_name) ?>">
        </div>
        
        <div class="filter-group">
            <select name="search_type">
                <option value="">-- Loại giao dịch --</option>
                <option value="Registration" <?= $search_type == 'Registration' ? 'selected' : '' ?>>Đăng ký mới</option>
                <option value="Renewal" <?= $search_type == 'Renewal' ? 'selected' : '' ?>>Gia hạn</option>
            </select>
        </div>

        <button type="submit" class="btn-submit-search">
            <i class='bx bx-filter-alt'></i> Lọc dữ liệu
        </button>

        <?php if(!empty($search_name) || !empty($search_type)): ?>
            <a href="admin_dashboard.php" class="btn-reset">Xóa lọc</a>
        <?php endif; ?>
    </form>
</div>

        <div class="recent-transactions">
            <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Danh sách giao dịch</h2>
                <button type="button" id="btnOpenModal" class="btn-add-new">
                    <i class='bx bx-plus'></i> Thêm giao dịch
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hội viên</th>
                        <th>Loại</th>
                        <th>Số Tiền</th>
                        <th>Ngày Giao Dịch</th>
                        <th>Hạn Dùng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($recent_transactions && $recent_transactions->num_rows > 0): ?>
                    <?php while ($row = $recent_transactions->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['transaction_id'] ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td>
                                <span class="status <?= $row['transaction_type'] == 'Registration' ? 'type-reg' : 'type-renew' ?>">
                                    <?= $row['transaction_type'] == 'Registration' ? 'Đăng ký' : 'Gia hạn' ?>
                                </span>
                            </td>
                            <td><strong><?= number_format($row['amount'], 0, ',', '.') ?>đ</strong></td>
                            <td><?= date("d/m/Y H:i", strtotime($row['transaction_date'])) ?></td>
                            <td><?= $row['end_date'] ? date("d/m/Y", strtotime($row['end_date'])) : '—' ?></td>
                            <td>
                                <a href="javascript:void(0)" class="btn-edit-trigger" 
                                   data-id="<?= $row['transaction_id'] ?>"
                                   data-memberid="<?= $row['member_id'] ?>"
                                   data-type="<?= $row['transaction_type'] ?>"
                                   data-amount="<?= $row['amount'] ?>"
                                   data-enddate="<?= $row['end_date'] ?>">
                                   <i class='bx bxs-edit' style="color:#3498db; cursor:pointer; font-size: 1.2rem;"></i>
                                </a>
                                <a href="transaction_handler.php?delete_id=<?= $row['transaction_id'] ?>" onclick="return confirm('Xác nhận xóa giao dịch này?')">
                                    <i class='bx bxs-trash' style="color:#e74c3c; margin-left: 10px; font-size: 1.2rem;"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align: center; padding: 20px;">Không tìm thấy dữ liệu phù hợp.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="addTransactionModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header"><h2>Thêm Giao Dịch Mới</h2><span class="close-modal">&times;</span></div>
            <form action="transaction_handler.php" method="POST">
                <div class="form-group">
                    <label>Thành viên:</label>
                    <select name="member_id" id="memberSelect" required>
                        <option value="">-- Chọn thành viên --</option>
                        <option value="new_member" style="color:red; font-weight:bold;">+ THÊM NGƯỜI MỚI</option>
                        <?php
                        $list = $conn->query("SELECT member_id, full_name FROM members ORDER BY full_name ASC");
                        while($m = $list->fetch_assoc()) echo "<option value='".$m['member_id']."'>".htmlspecialchars($m['full_name'])."</option>";
                        ?>
                    </select>
                </div>
                <div id="newNameGroup" class="form-group" style="display:none;">
                    <label>Tên thành viên mới:</label>
                    <input type="text" name="new_member_name" id="newMemberName">
                </div>
                <div class="form-group">
                    <label>Ngày hết hạn tập:</label>
                    <input type="date" name="end_date" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
    <label>Gói tập & Số tiền (VNĐ):</label>
    <select name="amount" id="amountSelect" required>   
        <option value="">-- Chọn gói tập --</option>
        <option value="500000" >Gói 1 tháng - 500.000đ</option>
        <option value="1350000" >Gói 3 tháng - 1.350.000đ</option>
        <option value="5000000" >Gói 1 năm - 5.000.000đ</option>
    </select>
</div>
                <div class="form-group">
                    <label>Loại & Phương thức:</label>
                    <div style="display: flex; gap: 10px;">
                        <select name="transaction_type"><option value="Registration">Đăng ký</option><option value="Renewal">Gia hạn</option></select>
                        <select name="payment_method"><option value="Tiền mặt">Tiền mặt</option><option value="Chuyển khoản">Chuyển khoản</option></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="close-modal">Hủy</button>
                    <button type="submit" name="btn_save" class="btn-save">Lưu Giao Dịch</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editTransactionModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header"><h2>Sửa Giao Dịch #<span id="display_edit_id"></span></h2><span class="close-modal">&times;</span></div>
            <form action="transaction_handler.php" method="POST">
                <input type="hidden" name="transaction_id" id="edit_trans_id">
                <input type="hidden" name="member_id" id="edit_member_id"> <div class="form-group">
                    <label>Ngày hết hạn hiện tại:</label>
                    <input type="date" name="end_date" id="edit_end_date" required>
                </div>
                <div class="form-group">
                    <label>Số tiền (VNĐ):</label>
                    <input type="number" name="amount" id="edit_amount" required>
                </div>
                <div class="form-group">
                    <label>Loại giao dịch:</label>
                    <select name="transaction_type" id="edit_type">
                        <option value="Registration">Đăng ký mới</option>
                        <option value="Renewal">Gia hạn</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phương thức thanh toán:</label>
                    <select name="payment_method" id="edit_method">
                        <option value="Tiền mặt">Tiền mặt</option>
                        <option value="Chuyển khoản">Chuyển khoản</option>
                        <option value="Momo">Momo</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="close-modal">Hủy</button>
                    <button type="submit" name="btn_update" class="btn-save" style="background: #3498db;">Cập nhật thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin_dashboard.js"></script>
    <?php if(isset($_GET['msg'])): ?>
        <script>
            const msgs = {success: 'Thêm thành công!', updated: 'Cập nhật thành công!', deleted: 'Đã xóa!', error: 'Có lỗi xảy ra!'};
            alert(msgs['<?= $_GET['msg'] ?>'] || 'Thông báo');
        </script>
    <?php endif; ?>
</body>
</html>
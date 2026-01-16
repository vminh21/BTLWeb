<?php
session_start();

// BẢO MẬT: admin mới được vào
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// KẾT NỐI DB
$conn = new mysqli("localhost", "root", "", "gymmanagement");
if ($conn->connect_error) die("Lỗi kết nối DB");
$conn->set_charset("utf8mb4");

// ====== THỐNG KÊ ======
$total_transactions = $conn->query(
    "SELECT COUNT(*) AS total FROM transactions"
)->fetch_assoc()['total'];

$total_revenue = $conn->query(
    "SELECT SUM(amount) AS revenue FROM transactions"
)->fetch_assoc()['revenue'] ?? 0;

// ====== TÌM KIẾM ======
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sql = "
    SELECT t.*, m.full_name
    FROM transactions t
    LEFT JOIN members m ON t.member_id = m.member_id
";

if (!empty($search)) {
    $sql .= "
        WHERE m.full_name LIKE '%$search%'
           OR t.payment_method LIKE '%$search%'
           OR t.transaction_type LIKE '%$search%'
    ";
}

$sql .= " ORDER BY t.transaction_date DESC";
$transactions = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống kê - FitPhysique Admin</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="members.css">
    <style>
        /* Tùy chỉnh riêng cho các thẻ thống kê để giống Dashboard */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo"><h2>FitPhysique<span>Admin</span></h2></div>
    <ul>
        <li><a href="admin_dashboard.php"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
        <li><a href="members.php"><i class='bx bxs-user-detail'></i> Quản lý thành viên</a></li>
        <li><a href="thongke.php"><i class='bx bxs-bell'></i> Báo cáo</a></li>
        <li><a href="admin_thongke.php" class="active"><i class='bx bxs-report'></i> Lịch sử giao dịch</a></li>
        <li><a href="admin_thongbao.php"><i class='bx bxs-bell'></i> Thông báo</a></li>
        <li class="logout"><a href="logout1.php"><i class='bx bxs-log-out'></i> Đăng xuất</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header">
        <h1>Thống kê giao dịch</h1>
        <div class="user-info">
            <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></strong></span>
            <i class='bx bxs-user-circle'></i>
        </div>
    </div>

    <div class="stat-cards">
        <div class="card">
            <div class="card-info">
                <h3><?= number_format($total_transactions) ?></h3>
                <p>Tổng giao dịch</p>
            </div>
            <div class="card-icon blue"><i class='bx bx-transfer'></i></div>
        </div>
        <div class="card">
            <div class="card-info">
                <h3><?= number_format($total_revenue) ?></h3>
                <p>Doanh thu (VNĐ)</p>
            </div>
            <div class="card-icon green"><i class='bx bx-money'></i></div>
        </div>
    </div>

    <div class="search-bar-container">
        <form action="reports.php" method="GET">
            <div class="search-group">
                <i class='bx bx-search'></i>
                <input type="text" name="search" placeholder="Tìm tên hội viên, phương thức..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <button type="submit" class="btn-submit-search">Lọc dữ liệu</button>
            <?php if($search): ?>
                <a href="reports.php" class="btn-reset">Xóa lọc</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="recent-transactions">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ và Tên</th>
                    <th>Số tiền</th>
                    <th>Phương thức</th>
                    <th>Loại giao dịch</th>
                    <th>Ngày thực hiện</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($transactions->num_rows > 0): ?>
                    <?php while($row = $transactions->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['transaction_id'] ?></td>
                        <td class="name-col"><?= htmlspecialchars($row['full_name'] ?? 'Khách lẻ') ?></td>
                        <td><strong><?= number_format($row['amount']) ?> VNĐ</strong></td>
                        <td><span class="status type-reg"><?= htmlspecialchars($row['payment_method']) ?></span></td>
                        <td><?= htmlspecialchars($row['transaction_type']) ?></td>
                        <td><?= date("d/m/Y", strtotime($row['transaction_date'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">Không tìm thấy dữ liệu giao dịch.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
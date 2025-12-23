<?php
session_start();

// 1. BẢO MẬT: Kiểm tra xem đã đăng nhập chưa và có phải Admin không?
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    // Nếu không phải admin, đá về trang login
    header("Location: login.php");
    exit();
}

// 2. KẾT NỐI DATABASE
$conn = new mysqli("localhost", "root", "", "GymManagement");
if ($conn->connect_error) { die("Lỗi kết nối: " . $conn->connect_error); }

// 3. LẤY SỐ LIỆU THỐNG KÊ (QUERY)

// a. Tổng số thành viên
$sql_total = "SELECT COUNT(*) as total FROM members";
$res_total = $conn->query($sql_total);
$total_members = $res_total->fetch_assoc()['total'];

// b. Thành viên đang hoạt động (Tính theo thẻ Active và chưa hết hạn)
$sql_active = "SELECT COUNT(*) as active FROM member_subscriptions WHERE status = 'Active' AND end_date >= CURDATE()";
$res_active = $conn->query($sql_active);
$active_members = $res_active->fetch_assoc()['active'];

// c. Thành viên hết hạn
$sql_expired = "SELECT COUNT(*) as expired FROM member_subscriptions WHERE end_date < CURDATE()";
$res_expired = $conn->query($sql_expired);
$expired_members = $res_expired->fetch_assoc()['expired'];

// d. Doanh thu (Tổng tiền trong bảng transactions)
$sql_revenue = "SELECT SUM(amount) as revenue FROM transactions";
$res_revenue = $conn->query($sql_revenue);
$total_revenue = $res_revenue->fetch_assoc()['revenue'];

// e. Lấy 5 Giao dịch gần nhất
$sql_recent = "SELECT t.*, m.full_name 
               FROM transactions t 
               JOIN members m ON t.member_id = m.member_id 
               ORDER BY t.transaction_date DESC LIMIT 5";
$recent_transactions = $conn->query($sql_recent);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitPhysique</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* --- CSS CƠ BẢN --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; min-height: 100vh; background: #f5f6fa; }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: #1e1e2d;
            color: #fff;
            padding: 20px;
        }
        .sidebar h2 { text-align: center; margin-bottom: 40px; color: #ff6b6b; }
        .sidebar ul { list-style: none; }
        .sidebar ul li { margin: 20px 0; }
        .sidebar ul li a {
            color: #b0b0b0;
            text-decoration: none;
            font-size: 18px;
            display: flex; align-items: center; gap: 10px;
            transition: 0.3s;
        }
        .sidebar ul li a:hover, .sidebar ul li a.active { color: #fff; }

        /* MAIN CONTENT */
        .main-content { flex: 1; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: #333; }
        .user-info { font-weight: bold; color: #555; }

        /* CARDS (THỐNG KÊ) */
        .cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: flex; justify-content: space-between; align-items: center;
        }
        .card-info h3 { font-size: 28px; color: #333; }
        .card-info p { color: #777; font-size: 14px; }
        .card-icon { font-size: 40px; color: #ff6b6b; }

        /* TABLE (GIAO DỊCH) */
        .recent-transactions { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .recent-transactions h2 { margin-bottom: 20px; color: #333; font-size: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background-color: #f8f9fa; color: #555; }
        table tr:hover { background-color: #f1f1f1; }
        .status { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .type-reg { background: #e3fcef; color: #00b894; } /* Đăng ký mới - Xanh */
        .type-renew { background: #fff0c2; color: #f39c12; } /* Gia hạn - Vàng */
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>FitPhysique Admin</h2>
        <ul>
            <li><a href="#" class="active"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
            <li><a href="#"><i class='bx bxs-user-detail'></i> Quản lý thành viên</a></li>
            <li><a href="#"><i class='bx bxs-credit-card'></i> Gói tập & Hạn</a></li>
            <li><a href="#"><i class='bx bxs-bell'></i> Thông báo</a></li>
            <li><a href="#"><i class='bx bxs-report'></i> Báo cáo</a></li>
            <li><a href="logout1.php"><i class='bx bxs-log-out'></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Tổng quan</h1>
            <div class="user-info">
                Xin chào, <?php echo $_SESSION['full_name']; ?> 
                <i class='bx bxs-user-circle' style="font-size: 24px; vertical-align: middle;"></i>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <div class="card-info">
                    <h3><?php echo $total_members; ?></h3>
                    <p>Tổng Thành Viên</p>
                </div>
                <div class="card-icon"><i class='bx bxs-group'></i></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3><?php echo $active_members; ?></h3>
                    <p>Đang Hoạt Động</p>
                </div>
                <div class="card-icon" style="color: #2ecc71;"><i class='bx bxs-check-circle'></i></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3><?php echo $expired_members; ?></h3>
                    <p>Đã Hết Hạn</p>
                </div>
                <div class="card-icon" style="color: #e74c3c;"><i class='bx bxs-x-circle'></i></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3><?php echo number_format($total_revenue, 0, ',', '.'); ?>đ</h3>
                    <p>Tổng Doanh Thu</p>
                </div>
                <div class="card-icon" style="color: #f1c40f;"><i class='bx bxs-wallet'></i></div>
            </div>
        </div>

        <div class="recent-transactions">
            <h2>Giao dịch gần đây</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Thành Viên</th>
                        <th>Loại Giao Dịch</th>
                        <th>Số Tiền</th>
                        <th>Ngày</th>
                        <th>Phương Thức</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_transactions->num_rows > 0): ?>
                        <?php while($row = $recent_transactions->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['transaction_id']; ?></td>
                                <td><?php echo $row['full_name']; ?></td>
                                <td>
                                    <?php 
                                        $typeClass = ($row['transaction_type'] == 'Registration') ? 'type-reg' : 'type-renew';
                                        echo "<span class='status $typeClass'>" . $row['transaction_type'] . "</span>"; 
                                    ?>
                                </td>
                                <td><?php echo number_format($row['amount'], 0, ',', '.'); ?> đ</td>
                                <td><?php echo date("d/m/Y H:i", strtotime($row['transaction_date'])); ?></td>
                                <td><?php echo $row['payment_method']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">Chưa có giao dịch nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
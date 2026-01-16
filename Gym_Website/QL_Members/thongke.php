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

$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// 1. Data Doanh thu theo tháng
$res_rev = $conn->query("SELECT MONTH(transaction_date) as m, SUM(amount) as t FROM transactions WHERE YEAR(transaction_date) = $selected_year GROUP BY m ORDER BY m ASC");
$months = []; $revenues = [];
for($i=1; $i<=12; $i++) { $months[] = "Tháng ".$i; $revenues[$i] = 0; }
while($r = $res_rev->fetch_assoc()) { $revenues[(int)$r['m']] = $r['t']; }
$revenues = array_values($revenues);

// 2. Data Tổng quan
$grand_total = array_sum($revenues);
$total_nam = $conn->query("SELECT COUNT(*) FROM members WHERE gender = 'Male'")->fetch_row()[0] ?? 0;
$total_nu = $conn->query("SELECT COUNT(*) FROM members WHERE gender = 'Female'")->fetch_row()[0] ?? 0;

// 3. Data Biểu đồ tròn (Cơ cấu Gói tập)
$res_pie = $conn->query("SELECT p.package_name, COUNT(s.subscription_id) as count 
    FROM membership_packages p 
    LEFT JOIN member_subscriptions s ON p.package_id = s.package_id 
    GROUP BY p.package_id");
$pie_labels = []; $pie_counts = [];
while($row = $res_pie->fetch_assoc()) {
    $pie_labels[] = $row['package_name'];
    $pie_counts[] = (int)$row['count'];
}

// 4. Top 5 Khách hàng
$res_top = $conn->query("SELECT m.full_name, SUM(t.amount) as total FROM transactions t JOIN members m ON t.member_id = m.member_id WHERE YEAR(t.transaction_date) = $selected_year GROUP BY m.member_id ORDER BY total DESC LIMIT 5");

// 5. Giao dịch chi tiết
$res_details = $conn->query("SELECT m.full_name, m.gender, p.package_name, t.amount, t.transaction_type, t.transaction_date 
    FROM transactions t 
    JOIN members m ON t.member_id = m.member_id 
    LEFT JOIN member_subscriptions s ON m.member_id = s.member_id 
    LEFT JOIN membership_packages p ON s.package_id = p.package_id 
    WHERE YEAR(t.transaction_date) = $selected_year 
    ORDER BY t.transaction_date DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống kê chuyên sâu - FitPhysique Admin</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="members.css">
    <style>
        .main-content { background: #f4f7f6; }
        .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
        
        /* Layout cho 3 biểu đồ/thống kê */
        .analytics-row { display: grid; grid-template-columns: 1.5fr 1fr 0.8fr; gap: 20px; margin-bottom: 25px; }
        .box-white { background: #fff; padding: 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); }
        .canvas-container { position: relative; height: 250px; width: 100%; }

        .top-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f8f8f8; font-size: 13px; }
        .top-item:last-child { border-bottom: none; }
        .top-item b { color: #f64e60; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo"><h2>FitPhysique<span>Admin</span></h2></div>
    <ul>
        <li><a href="admin_dashboard.php"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
        <li><a href="members.php"><i class='bx bxs-user-detail'></i> Quản lý thành viên</a></li>
        <li><a href="thongke.php" class="active"><i class='bx bxs-report'></i> Báo cáo</a></li>
        <li><a href="admin_thongke.php"><i class='bx bxs-bell'></i> Lịch sử giao dịch</a></li>
        <li><a href="admin_thongbao.php"><i class='bx bxs-bell'></i> Thông báo</a></li>
        <li class="logout"><a href="logout1.php"><i class='bx bxs-log-out'></i> Đăng xuất</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header">
        <h1>Phân tích hệ thống</h1>
        <div class="user-info">
            <form method="GET" style="margin-right:15px;">
                <select name="year" onchange="this.form.submit()" style="padding:7px; border-radius:8px; border:1px solid #ddd;">
                    <option value="2024" <?= $selected_year==2024?'selected':'' ?>>Năm 2024</option>
                    <option value="2025" <?= $selected_year==2025?'selected':'' ?>>Năm 2025</option>
                </select>
            </form>
            <span>Chào, <strong><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></strong></span>
            <i class='bx bxs-user-circle'></i>
        </div>
    </div>

    <div class="stat-grid">
        <div class="card">
            <div class="card-info"><h3><?= number_format($grand_total) ?>đ</h3><p>Doanh thu năm</p></div>
            <div class="card-icon red"><i class='bx bx-wallet'></i></div>
        </div>
        <div class="card">
            <div class="card-info"><h3><?= $total_nam ?></h3><p>Hội viên Nam</p></div>
            <div class="card-icon blue"><i class='bx bx-male'></i></div>
        </div>
        <div class="card">
            <div class="card-info"><h3><?= $total_nu ?></h3><p>Hội viên Nữ</p></div>
            <div class="card-icon gold"><i class='bx bx-female'></i></div>
        </div>
    </div>

    <div class="analytics-row">
        <div class="box-white">
            <h4 style="margin-bottom:15px; font-size:14px;">Xu hướng doanh thu</h4>
            <div class="canvas-container"><canvas id="lineChart"></canvas></div>
        </div>

        <div class="box-white">
            <h4 style="margin-bottom:15px; font-size:14px;">Cơ cấu Gói tập</h4>
            <div class="canvas-container"><canvas id="pieChart"></canvas></div>
        </div>

        <div class="box-white">
            <h4 style="margin-bottom:15px; font-size:14px;">Top 5 chi tiêu</h4>
            <div class="top-list">
                <?php while($t = $res_top->fetch_assoc()): ?>
                <div class="top-item">
                    <span><?= htmlspecialchars($t['full_name']) ?></span>
                    <b><?= number_format($t['total']) ?>đ</b>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div class="recent-transactions">
        <table>
            <thead>
                <tr>
                    <th>Hội viên</th>
                    <th>Giới tính</th>
                    <th>Gói tập</th>
                    <th>Loại</th>
                    <th>Số tiền</th>
                    <th>Ngày</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $res_details->fetch_assoc()): ?>
                <tr>
                    <td class="name-col"><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= $row['gender']=='Male'?'Nam':'Nữ' ?></td>
                    <td><?= htmlspecialchars($row['package_name'] ?? 'Cơ bản') ?></td>
                    <td><span class="status <?= $row['transaction_type']=='Renewal'?'type-renew':'type-reg' ?>"><?= $row['transaction_type']=='Renewal'?'Gia hạn':'Đăng ký' ?></span></td>
                    <td><strong><?= number_format($row['amount']) ?>đ</strong></td>
                    <td><?= date("d/m/Y", strtotime($row['transaction_date'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Biểu đồ đường
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                data: <?= json_encode($revenues) ?>,
                borderColor: '#f64e60',
                backgroundColor: 'rgba(246, 78, 96, 0.1)',
                fill: true, tension: 0.4, borderWidth: 3
            }]
        },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    // Biểu đồ tròn (Doughnut)
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($pie_labels) ?>,
            datasets: [{
                data: <?= json_encode($pie_counts) ?>,
                backgroundColor: ['#3699ff', '#1bc5bd', '#ffa800', '#f64e60'],
                borderWidth: 0
            }]
        },
        options: { 
            maintainAspectRatio: false, 
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } } 
        }
    });
</script>
</body>
</html>
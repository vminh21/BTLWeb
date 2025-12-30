<?php
session_start();
// 1. Kết nối Database
$conn = new mysqli("localhost", "root", "", "gymmanagement");
if ($conn->connect_error) { die("Lỗi kết nối: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

// 2. Lấy năm được chọn
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// 3. Truy vấn tổng doanh thu của năm
$sql_year_total = "SELECT SUM(amount) as total FROM transactions WHERE YEAR(transaction_date) = $selected_year";
$res_year_total = $conn->query($sql_year_total);
$grand_total_year = $res_year_total->fetch_assoc()['total'] ?? 0;

// 4. Truy vấn doanh thu từng tháng
$sql_revenue = "SELECT MONTH(transaction_date) as month, SUM(amount) as total 
                FROM transactions 
                WHERE YEAR(transaction_date) = $selected_year 
                GROUP BY MONTH(transaction_date) 
                ORDER BY month ASC";
$res_revenue = $conn->query($sql_revenue);
$months = []; $revenues = [];
while($row = $res_revenue->fetch_assoc()) {
    $months[] = "Tháng " . $row['month'];
    $revenues[] = (float)$row['total'];
}

// 5. Thống kê giới tính
$sql_gender = "SELECT m.gender, COUNT(*) as count 
               FROM members m
               JOIN transactions t ON m.member_id = t.member_id
               WHERE YEAR(t.transaction_date) = $selected_year
               GROUP BY m.gender";
$res_gender = $conn->query($sql_gender);
$gender_labels = []; $gender_counts = [];
$total_nam = 0; $total_nu = 0;
while($row = $res_gender->fetch_assoc()) {
    $label = ($row['gender'] == 'Male') ? 'Nam' : 'Nữ';
    $gender_labels[] = $label;
    $gender_counts[] = $row['count'];
    if($row['gender'] == 'Male') $total_nam = $row['count'];
    else $total_nu = $row['count'];
}

// 6. Danh sách giao dịch
$sql_members = "SELECT m.full_name, m.gender, p.package_name, t.transaction_date, t.amount
                FROM transactions t
                JOIN members m ON t.member_id = m.member_id
                JOIN member_subscriptions s ON m.member_id = s.member_id
                JOIN membership_packages p ON s.package_id = p.package_id
                WHERE YEAR(t.transaction_date) = $selected_year
                ORDER BY t.transaction_date DESC";
$res_members = $conn->query($sql_members);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo Cáo Doanh Thu - FitPhysique</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../QL_Members/admin_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .main-content { padding: 20px; }
        .filter-header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px 25px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .summary-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
        .summary-card { background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border-bottom: 4px solid #e74c3c; }
        .summary-card h4 { color: #888; font-size: 13px; text-transform: uppercase; margin-bottom: 8px; }
        .summary-card h2 { color: #2c3e50; font-size: 24px; }
        .charts-grid { display: grid; grid-template-columns: 2.2fr 1fr; gap: 20px; margin-bottom: 30px; }
        .chart-box { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        select { padding: 8px 15px; border-radius: 6px; border: 1px solid #ddd; font-weight: 600; cursor: pointer; }
        .gender-nam { color: #3498db; font-weight: 600; } 
        .gender-nu { color: #e91e63; font-weight: 600; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo"><h2>FitPhysique<span>Admin</span></h2></div>
        <ul>
            <li><a href="../QL_Members/admin_dashboard.php"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
            <li><a href="thongke.php" class="active"><i class='bx bxs-report'></i> Báo cáo</a></li>
            <li class="logout"><a href="../QL_Members/logout1.php"><i class='bx bxs-log-out'></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="filter-header">
            <h2>Phân tích dữ liệu năm <?= $selected_year ?></h2>
            <form method="GET">
                <select name="year" onchange="this.form.submit()">
                    <option value="2023" <?= $selected_year == 2023 ? 'selected' : '' ?>>2023</option>
                    <option value="2024" <?= $selected_year == 2024 ? 'selected' : '' ?>>2024</option>
                    <option value="2025" <?= $selected_year == 2025 ? 'selected' : '' ?>>2025</option>
                </select>
            </form>
        </div>

        <div class="summary-row">
            <div class="summary-card">
                <h4>DOANH THU NĂM</h4>
                <h2 style="color: #e74c3c;"><?= number_format($grand_total_year, 0, ',', '.') ?>đ</h2>
            </div>
            <div class="summary-card" style="border-bottom-color: #3498db;">
                <h4>HỘI VIÊN NAM</h4>
                <h2 class="gender-nam"><?= $total_nam ?></h2>
            </div>
            <div class="summary-card" style="border-bottom-color: #e91e63;">
                <h4>HỘI VIÊN NỮ</h4>
                <h2 class="gender-nu"><?= $total_nu ?></h2>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-box">
                <h3 style="margin-bottom:15px; color: #555;">Xu hướng doanh thu</h3>
                <canvas id="monthlyChart" height="110"></canvas>
            </div>
            <div class="chart-box">
                <h3 style="margin-bottom:15px; color: #555;">Cơ cấu giới tính</h3>
                <canvas id="genderChart"></canvas>
            </div>
        </div>

        <div class="recent-transactions">
            <h2>Chi tiết giao dịch</h2>
            <table>
                <thead>
                    <tr>
                        <th>Hội viên</th>
                        <th>Giới tính</th>
                        <th>Gói tập</th>
                        <th>Ngày đăng ký</th>
                        <th>Số tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $res_members->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['full_name']) ?></strong></td>
                        <td>
                            <span class="<?= ($row['gender'] == 'Male') ? 'gender-nam' : 'gender-nu' ?>">
                                <i class='bx <?= ($row['gender'] == 'Male') ? 'bx-male' : 'bx-female' ?>'></i>
                                <?= ($row['gender'] == 'Male') ? 'Nam' : 'Nữ' ?>
                            </span>
                        </td>
                        <td><?= $row['package_name'] ?></td>
                        <td><?= date("d/m/Y", strtotime($row['transaction_date'])) ?></td>
                        <td style="font-weight:bold; color:#27ae60;"><?= number_format($row['amount'], 0, ',', '.') ?>đ</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Cấu hình biểu đồ DÂY (Line Chart) mảnh
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    label: 'Doanh thu tháng',
                    data: <?= json_encode($revenues) ?>,
                    borderColor: '#e74c3c', // Màu dây đỏ
                    borderWidth: 2,         // Độ dày dây (số nhỏ = dây mảnh)
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#e74c3c',
                    pointRadius: 4,         // Điểm tròn trên dây
                    tension: 0.4,           // Độ cong của dây (0 là đường thẳng, 0.4 là cong mượt)
                    fill: true,             // Đổ màu mờ phía dưới dây
                    backgroundColor: 'rgba(231, 76, 60, 0.1)' // Màu nền mờ dưới dây
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: { callback: (v) => v.toLocaleString() + 'đ' }
                    }
                }
            }
        });

        // Biểu đồ tròn
        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($gender_labels) ?>,
                datasets: [{
                    data: <?= json_encode($gender_counts) ?>,
                    backgroundColor: ['#3498db', '#e91e63'],
                    borderWidth: 0
                }]
            },
            options: { cutout: '70%', plugins: { legend: { position: 'bottom' } } }
        });
    </script>
</body>
</html>
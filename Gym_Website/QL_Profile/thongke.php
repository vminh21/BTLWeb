<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gymmanagement");
$conn->set_charset("utf8mb4");

$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// 1. Data Doanh thu
$res_rev = $conn->query("SELECT MONTH(transaction_date) as m, SUM(amount) as t FROM transactions WHERE YEAR(transaction_date) = $selected_year GROUP BY m ORDER BY m ASC");
$months = []; $revenues = [];
for($i=1; $i<=12; $i++) { $months[] = "Tháng ".$i; $revenues[$i] = 0; }
while($r = $res_rev->fetch_assoc()) { $revenues[(int)$r['m']] = $r['t']; }
$revenues = array_values($revenues);

// 2. Data Tổng quan
$grand_total = array_sum($revenues);
$total_nam = $conn->query("SELECT COUNT(*) FROM members WHERE gender = 'Male'")->fetch_row()[0] ?? 0;
$total_nu = $conn->query("SELECT COUNT(*) FROM members WHERE gender = 'Female'")->fetch_row()[0] ?? 0;

// 3. Top 5 Khách hàng
$res_top = $conn->query("SELECT m.full_name, SUM(t.amount) as total FROM transactions t JOIN members m ON t.member_id = m.member_id WHERE YEAR(t.transaction_date) = $selected_year GROUP BY m.member_id ORDER BY total DESC LIMIT 5");

// 4. Giao dịch chi tiết
$res_details = $conn->query("SELECT m.full_name, m.gender, p.package_name, t.amount FROM transactions t JOIN members m ON t.member_id = m.member_id LEFT JOIN member_subscriptions s ON m.member_id = s.member_id LEFT JOIN membership_packages p ON s.package_id = p.package_id WHERE YEAR(t.transaction_date) = $selected_year ORDER BY t.transaction_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phân tích hệ thống - FitPhysique</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; background: #f4f6f9; color: #333; }
        
        .sidebar { width: 250px; background: #1e1e2d; height: 100vh; position: fixed; padding: 20px; color: #a2a3b7; }
        .sidebar .logo { color: #fff; font-size: 20px; font-weight: bold; margin-bottom: 30px; }
        .sidebar .logo span { color: #f64e60; font-size: 12px; margin-left: 4px; }
        .sidebar ul { list-style: none; }
        .sidebar ul li a { display: flex; align-items: center; padding: 12px; color: #a2a3b7; text-decoration: none; border-radius: 8px; margin-bottom: 5px; }
        .sidebar ul li a.active { background: #2b2b40; color: #f64e60; }

        .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 25px; }
        
        .header-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: #fff; padding: 15px 25px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        
        /* Summary Cards - Giống ảnh 100% */
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
        .s-card { background: #fff; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .s-card p { font-size: 12px; color: #888; text-transform: uppercase; font-weight: bold; margin-bottom: 10px; }
        .s-card h2 { font-size: 22px; color: #2c3e50; }
        .val-red { color: #f64e60 !important; }
        .val-blue { color: #3699ff !important; }

        /* Layout Grid cho Chart - Fix biểu đồ to vcl */
        .chart-row { display: grid; gap: 20px; margin-bottom: 20px; }
        .row-1 { grid-template-columns: 2.2fr 1fr; }
        .row-2 { grid-template-columns: 1fr 2.2fr; }

        .box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .box h3 { font-size: 15px; margin-bottom: 15px; color: #444; border-left: 4px solid #f64e60; padding-left: 10px; }

        /* Khống chế chiều cao biểu đồ */
        .chart-container { position: relative; height: 220px; width: 100%; }
        .pie-container { position: relative; height: 250px; width: 100%; }

        /* List & Table */
        .top-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f8f8f8; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        table th { text-align: left; font-size: 11px; color: #bbb; text-transform: uppercase; padding: 10px; }
        table td { padding: 12px 10px; font-size: 13px; border-bottom: 1px solid #fbfbfb; }
        .btn-year { padding: 6px 12px; border-radius: 6px; border: 1px solid #eee; background: #fff; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">FitPhysique<span>Admin</span></div>
        <ul>
            <li><a href="../QL_Members/admin_dashboard.php"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
            <li><a href="#"><i class='bx bxs-user-detail'></i> Quản lý thành viên</a></li>
            <li><a href="#"><i class='bx bxs-credit-card'></i> Gói tập & Hạn</a></li>
            <li><a href="thongke.php" class="active"><i class='bx bxs-report'></i> Báo cáo</a></li>
            <li><a href="../QL_Members/logout1.php"><i class='bx bxs-log-out'></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header-box">
            <h2 style="font-size: 18px;">Phân tích hệ thống năm <?= $selected_year ?></h2>
            <form method="GET">
                <select name="year" class="btn-year" onchange="this.form.submit()">
                    <option value="2023" <?= $selected_year==2023?'selected':'' ?>>Năm 2023</option>
                    <option value="2024" <?= $selected_year==2024?'selected':'' ?>>Năm 2024</option>
                    <option value="2025" <?= $selected_year==2025?'selected':'' ?>>Năm 2025</option>
                </select>
            </form>
        </div>

        <div class="summary-grid">
            <div class="s-card"><p>DOANH THU NĂM</p><h2 class="val-red"><?= number_format($grand_total) ?>đ</h2></div>
            <div class="s-card"><p>HỘI VIÊN NAM</p><h2 class="val-blue"><?= $total_nam ?></h2></div>
            <div class="s-card"><p>HỘI VIÊN NỮ</p><h2><?= $total_nu ?></h2></div>
        </div>

        <div class="chart-row row-1">
            <div class="box">
                <h3>Xu hướng doanh thu</h3>
                <div class="chart-container"><canvas id="lineChart"></canvas></div>
            </div>
            <div class="box">
                <h3>Top 5 Khách hàng</h3>
                <?php $i=1; while($t = $res_top->fetch_assoc()): ?>
                <div class="top-item">
                    <span>#<?= $i++ ?> <?= htmlspecialchars($t['full_name']) ?></span>
                    <b style="color:#555;"><?= number_format($t['total']) ?>đ</b>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="chart-row row-2">
            <div class="box">
                <h3>Cơ cấu Gói tập</h3>
                <div class="pie-container"><canvas id="pieChart"></canvas></div>
            </div>
            <div class="box">
                <h3>Giao dịch chi tiết</h3>
                <table>
                    <thead><tr><th>Hội viên</th><th>Phái</th><th>Gói tập</th><th>Số tiền</th></tr></thead>
                    <tbody>
                        <?php while($d = $res_details->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight:600;"><?= $d['full_name'] ?></td>
                            <td style="color:#3699ff;"><?= $d['gender']=='Male'?'Nam':'Nữ' ?></td>
                            <td style="color:#888;"><?= $d['package_name'] ?? 'Cơ bản' ?></td>
                            <td style="color:#1bc5bd; font-weight:bold;"><?= number_format($d['amount']) ?>đ</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false, // QUAN TRỌNG: Để Chart tuân thủ theo container height
            plugins: { legend: { display: false } }
        };

        // Biểu đồ Line - Thanh mảnh, không bị to vcl
        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    data: <?= json_encode($revenues) ?>,
                    borderColor: '#f64e60',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(246, 78, 96, 0.05)'
                }]
            },
            options: {
                ...commonOptions,
                scales: { 
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Biểu đồ Doughnut
        new Chart(document.getElementById('pieChart'), {
            type: 'doughnut',
            data: {
                labels: ['Gói 1 Tháng', 'Gói 3 Tháng', 'Gói 1 Năm'],
                datasets: [{
                    data: [40, 35, 25],
                    backgroundColor: ['#ffc107', '#3699ff', '#1bc5bd'],
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                cutout: '75%',
                plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
            }
        });
    </script>
</body>
</html>
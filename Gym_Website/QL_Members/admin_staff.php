<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_dashboard.php"); 
    exit();
}
include("../QL_Profile/connectdb.php");

if (isset($_POST['btn_save'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $pass = $_POST['password']; 
    $name = $conn->real_escape_string($_POST['full_name']);
    $phone = $conn->real_escape_string($_POST['phone_number']);
    $sal = $_POST['salary'];

    $sql = "INSERT INTO admins (email, password, full_name, phone_number, position, salary) 
            VALUES ('$email', '$pass', '$name', '$phone', 'staff', '$sal')";
    $conn->query($sql);
    header("Location: admin_staff.php?msg=success");
    exit();
}

if (isset($_POST['btn_update'])) {
    $id = $_POST['admin_id'];
    $name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone_number']);
    $sal = $_POST['salary'];

    $sql = "UPDATE admins SET full_name='$name', email='$email', phone_number='$phone', salary='$sal' 
            WHERE admin_id = $id AND position = 'staff'";
    $conn->query($sql);
    header("Location: admin_staff.php?msg=updated");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM admins WHERE admin_id = $id AND position = 'staff'");
    header("Location: admin_staff.php?msg=deleted");
    exit();
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where_sql = "WHERE position = 'staff'";
if (!empty($search)) { $where_sql .= " AND (full_name LIKE '%$search%' OR email LIKE '%$search%' OR phone_number LIKE '%$search%')"; }

$total_staff = $conn->query("SELECT COUNT(*) FROM admins WHERE position = 'staff'")->fetch_row()[0] ?? 0;
$result = $conn->query("SELECT * FROM admins $where_sql ORDER BY admin_id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý nhân sự - FitPhysique</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="admin_dashboard.css?v=1.2">
    <link href='staff.css' rel='stylesheet'>
</head>
<body>

    <div class="sidebar">
        <div class="logo"><h2>FitPhysique<span>Admin</span></h2></div>
        <ul>
            <li><a href="admin_dashboard.php"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
            <li><a href="members.php"><i class='bx bxs-user-detail'></i> Quản lý thành viên</a></li>
            <li><a href="thongke.php"><i class='bx bxs-report'></i> Báo cáo</a></li>
            <li><a href="admin_thongbao.php"><i class='bx bxs-bell'></i> Thông báo</a></li>
            <li><a href="admin_staff.php" class="active"><i class='bx bxs-group'></i> Quản lí nhân sự</a></li>
            <li class="logout"><a href="logout1.php"><i class='bx bxs-log-out'></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Quản lý nhân sự</h1>
            <div class="user-info">
                <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong></span>
                <i class='bx bxs-user-circle'></i>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <div class="card-info">
                    <h3><?= $total_staff ?></h3>
                    <p>Nhân Viên</p>
                </div>
                <div class="card-icon blue"><i class='bx bxs-user-badge'></i></div>
            </div>
        </div>

        <div class="search-bar-container">
            <form method="GET" action="admin_staff.php" style="width: 100%; display: flex; gap: 10px;">
                <div class="search-group" style="flex: 1;">
                    <i class='bx bx-search'></i>
                    <input type="text" name="search" placeholder="Nhập tên hoặc email cần tìm..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <button type="submit" class="btn-submit-search">Lọc dữ liệu</button>
            </form>
        </div>

        <div class="recent-transactions">
            <div class="table-header">
                <h2>Danh sách Staff</h2>
                <div class="test">
                <button type="button" class="btn-add-staff" onclick="openModal('addStaffModal')">
                    <i class='bx bx-plus'></i> Thêm nhân viên
                </button>
                <button type="button" class="btn-reset" onclick="window.location.href='admin_staff.php'">
                <i class='bx bx-refresh'></i> Làm mới
        </button>
</div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email đăng nhập</th>
                        <th>Số điện thoại</th>
                        <th>Lương</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['admin_id'] ?></td>
                            <td><strong><?= htmlspecialchars($row['full_name']) ?></strong></td>
                            <td style="color:#3498db;"><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone_number']) ?></td>
                            <td><strong><?= number_format($row['salary'], 0, ',', '.') ?>đ</strong></td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0)" onclick='openEditModal(<?= json_encode($row) ?>)'>
                                    <i class='bx bxs-edit' style="color:#3498db; cursor:pointer; font-size: 1.2rem; margin-right: 10px;"></i>
                                </a>
                                <a href="?delete_id=<?= $row['admin_id'] ?>" onclick="return confirm('Xác nhận xóa?')">
                                    <i class='bx bxs-trash' style="color:#e74c3c; cursor:pointer; font-size: 1.2rem;"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding: 20px;">Không tìm thấy nhân viên nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addStaffModal" class="modal-overlay">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px;">Thêm Staff Mới</h2>
            <form method="POST">
                <div class="form-group"><label>Email đăng nhập:</label><input type="email" name="email" required placeholder="nhanvien@gym.com"></div>
                <div class="form-group"><label>Mật khẩu:</label><input type="password" name="password" required></div>
                <div class="form-group"><label>Họ tên:</label><input type="text" name="full_name" required></div>
                <div class="form-group"><label>Số điện thoại:</label><input type="text" name="phone_number"></div>
                <div class="form-group"><label>Lương tháng:</label><input type="number" name="salary"></div>
                <div class="modal-footer">
                    <button type="button" class="btn-close" onclick="closeModal('addStaffModal')">Hủy</button>
                    <button type="submit" name="btn_save" class="btn-save" style="background: #27ae60;">Lưu dữ liệu</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editStaffModal" class="modal-overlay">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px;">Sửa thông tin Staff</h2>
            <form method="POST">
                <input type="hidden" name="admin_id" id="edit_id">
                <div class="form-group"><label>Họ tên:</label><input type="text" name="full_name" id="edit_name" required></div>
                <div class="form-group"><label>Email:</label><input type="email" name="email" id="edit_email" required></div>
                <div class="form-group"><label>Số điện thoại:</label><input type="text" name="phone_number" id="edit_phone"></div>
                <div class="form-group"><label>Lương:</label><input type="number" name="salary" id="edit_salary"></div>
                <div class="modal-footer">
                    <button type="button" class="btn-close" onclick="closeModal('editStaffModal')">Đóng</button>
                    <button type="submit" name="btn_update" class="btn-save">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        
        function openEditModal(data) {
            document.getElementById('edit_id').value = data.admin_id;
            document.getElementById('edit_name').value = data.full_name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_phone').value = data.phone_number;
            document.getElementById('edit_salary').value = data.salary;
            openModal('editStaffModal');
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal-overlay') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
<?php
session_start();
// 1. KẾT NỐI DATABASE
include("../QL_Profile/connectdb.php");

// 2. XỬ LÝ LOGIC CRUD
$status_msg = "";

// --- CHỨC NĂNG XÓA ---
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $sql_delete = "DELETE FROM notifications WHERE notification_id = $id";
    if (mysqli_query($conn, $sql_delete)) {
        header("Location: admin_thongbao.php?msg=deleted");
        exit();
    }
}

// --- CHỨC NĂNG THÊM HOẶC SỬA ---
if (isset($_POST['btn_save'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $notif_id = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;
    $admin_id = $_SESSION['admin_id'] ?? 1;

    // Xử lý Upload ảnh
    $image_query = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], "../assets/" . $image_name);
        $image_query = ", image = '$image_name'";
    }

    if ($notif_id > 0) {
        // CẬP NHẬT (SỬA)
        $sql = "UPDATE notifications SET title='$title', content='$content' $image_query WHERE notification_id=$notif_id";
    } else {
        // THÊM MỚI
        $final_image = ($image_query != "") ? "'$image_name'" : "NULL";
        $sql = "INSERT INTO notifications (title, content, image, created_by) VALUES ('$title', '$content', $final_image, '$admin_id')";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_thongbao.php?msg=success");
        exit();
    }
}

// Lấy thông tin để sửa (nếu có)
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $res_edit = mysqli_query($conn, "SELECT * FROM notifications WHERE notification_id = $edit_id");
    $edit_data = mysqli_fetch_assoc($res_edit);
}

// 3. LẤY DANH SÁCH HIỂN THỊ
$result = mysqli_query($conn, "SELECT * FROM notifications ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý thông báo - FitPhysique Admin</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        .form-container { background: #fff; padding: 25px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }
        textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; }
        .btn-submit { background: #ff6b6b; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: 600; margin-top: 15px; }
        .btn-cancel { background: #eee; color: #333; text-decoration: none; padding: 12px 25px; border-radius: 8px; display: inline-block; }
        .img-preview { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo"><h2>FitPhysique<span>Admin</span></h2></div>
        <ul>
            <li><a href="admin_dashboard.php"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
            <li><a href="members.php"><i class='bx bxs-user-detail'></i> Quản lý thành viên</a></li>
            <li><a href="admin_thongbao.php" class="active"><i class='bx bxs-bell'></i> Thông báo</a></li>
            <li class="logout"><a href="logout1.php"><i class='bx bxs-log-out'></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1><?php echo $edit_data ? 'Sửa thông báo' : 'Thêm thông báo mới'; ?></h1>
            <div class="user-info"><span>Admin</span><i class='bx bxs-user-circle'></i></div>
        </div>

        <div class="form-container">
            <form action="admin_thongbao.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="notification_id" value="<?php echo $edit_data['notification_id'] ?? 0; ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Tiêu đề thông báo</label>
                        <input type="text" name="title" value="<?php echo $edit_data['title'] ?? ''; ?>" required placeholder="Nhập tiêu đề...">
                    </div>
                    <div class="form-group">
                        <label>Hình ảnh (Để trống nếu dùng mặc định)</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <div class="form-group full-width">
                        <label>Nội dung thông báo</label>
                        <textarea name="content" rows="5" required placeholder="Nhập nội dung chi tiết..."><?php echo $edit_data['content'] ?? ''; ?></textarea>
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <button type="submit" name="btn_save" class="btn-submit">
                        <i class='bx bx-save'></i> <?php echo $edit_data ? 'Cập nhật' : 'Đăng thông báo'; ?>
                    </button>
                    <?php if($edit_data): ?>
                        <a href="admin_thongbao.php" class="btn-cancel">Hủy</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="recent-transactions">
            <div class="table-header"><h3>Thông báo đã đăng</h3></div>
            <table>
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Ngày tạo</th>
                        <th>Người tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>
                            <img src="../assets/<?php echo ($row['image'] && $row['image'] != 'NULL') ? $row['image'] : 'banner-3.png'; ?>" class="img-preview">
                        </td>
                        <td class="name-col"><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo date("d/m/Y", strtotime($row['created_at'])); ?></td>
                        <td>ID: <?php echo $row['created_by']; ?></td>
                        <td>
                            <a href="admin_thongbao.php?edit_id=<?php echo $row['notification_id']; ?>" style="color: #3498db; font-size: 1.2rem; margin-right: 10px;"><i class='bx bxs-edit'></i></a>
                            <a href="admin_thongbao.php?delete_id=<?php echo $row['notification_id']; ?>" onclick="return confirm('Xác nhận xóa thông báo này?')" style="color: #e74c3c; font-size: 1.2rem;"><i class='bx bxs-trash'></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Tự động ẩn thông báo sau 3 giây
        if(window.location.search.includes('msg')) {
            setTimeout(() => { window.history.replaceState({}, document.title, "admin_thongbao.php"); }, 3000);
        }
    </script>
</body>
</html>
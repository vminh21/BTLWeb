<?php
session_start();

// BẢO MẬT: chỉ admin mới được vào
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// KẾT NỐI DATABASE
$conn = new mysqli("localhost", "root", "", "GymManagement");
if ($conn->connect_error) { die("Lỗi kết nối: " . $conn->connect_error); }
$conn->set_charset("utf8");

// XỬ LÝ FORM THÊM THÔNG BÁO
if (isset($_POST['submit'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $created_by = $_SESSION['user_id']; // Lưu admin_id

    $image_name = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image_name);
    }

    $sql = "INSERT INTO notifications (title, content, image, created_by) 
            VALUES ('$title', '$content', '$image_name', '$created_by')";
    if ($conn->query($sql)) {
        header("Location: admin_thongbao.php");
        exit();
    } else {
        echo "Lỗi khi thêm thông báo: " . $conn->error;
    }
}

// LẤY DANH SÁCH THÔNG BÁO (JOIN admins để lấy tên người tạo)
$sql_notifications = "
    SELECT n.*, a.full_name AS created_name 
    FROM notifications n
    JOIN admins a ON n.created_by = a.admin_id
    ORDER BY n.created_at DESC
";
$notifications = $conn->query($sql_notifications);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Thông báo</title>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { display:flex; min-height:100vh; background:#f5f6fa; }

/* SIDEBAR */
.sidebar { width:250px; background:#1e1e2d; color:#fff; padding:20px; }
.sidebar h2 { text-align:center; margin-bottom:40px; color:#ff6b6b; }
.sidebar ul { list-style:none; }
.sidebar ul li { margin:20px 0; }
.sidebar ul li a { color:#b0b0b0; text-decoration:none; font-size:18px; display:flex; align-items:center; gap:10px; transition:0.3s; }
.sidebar ul li a:hover, .sidebar ul li a.active { color:#fff; }

/* MAIN CONTENT */
.main-content { flex:1; padding:30px; }
.header { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
.header h1 { color:#333; }
.user-info { font-weight:bold; color:#555; }

/* FORM & CARD */
.form-card, .notification-card { background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 8px rgba(0,0,0,0.1); margin-bottom:20px; }
.form-card h2, .notification-card h3 { margin-bottom:15px; color:#333; }
.form-card input[type=text], .form-card textarea { width:100%; padding:10px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc; }
.form-card input[type=file] { margin-bottom:15px; }
.form-card button { padding:10px 20px; background:#ff6b6b; color:#fff; border:none; border-radius:5px; cursor:pointer; }

/* DANH SÁCH THÔNG BÁO */
.notification-grid { display:flex; flex-direction:column; gap:15px; }
.notification-item { display:flex; align-items:flex-start; background:#f9f9f9; padding:15px; border-radius:8px; }
.notification-item img { max-width:100px; margin-right:15px; border-radius:5px; }
.notification-info { display:flex; flex-direction:column; }
.notification-meta { font-size:12px; color:#999; margin-top:5px; }
</style>
</head>
<body>

<div class="sidebar">
    <h2>FitPhysique Admin</h2>
    <ul>
        <li><a href="dashboard.php"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
        <li><a href="#" class="active"><i class='bx bxs-bell'></i> Thông báo</a></li>
        <li><a href="logout1.php"><i class='bx bxs-log-out'></i> Đăng xuất</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header">
        <h1>Quản lý Thông Báo</h1>
        <div class="user-info">
            Xin chào, <?php echo $_SESSION['full_name']; ?> 
            <i class='bx bxs-user-circle' style="font-size:24px; vertical-align:middle;"></i>
        </div>
    </div>

    <!-- FORM THÊM THÔNG BÁO -->
    <div class="form-card">
        <h2>Thêm Thông Báo Mới</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Tiêu đề" required>
            <textarea name="content" placeholder="Nội dung" rows="4" required></textarea>
            <input type="file" name="image" accept="image/*">
            <button type="submit" name="submit">Thêm Thông Báo</button>
        </form>
    </div>

    <!-- DANH SÁCH THÔNG BÁO -->
    <div class="notification-grid">
        <?php if ($notifications->num_rows > 0): ?>
            <?php while($row = $notifications->fetch_assoc()): ?>
                <div class="notification-item">
                    <?php if ($row['image']): ?>
                        <img src="uploads/<?php echo $row['image']; ?>" alt="img">
                    <?php endif; ?>
                    <div class="notification-info">
                        <h3><?php echo $row['title']; ?></h3>
                        <p><?php echo $row['content']; ?></p>
                        <span class="notification-meta">
                            By <?php echo $row['created_name']; ?> | <?php echo date("d/m/Y H:i", strtotime($row['created_at'])); ?>
                        </span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Chưa có thông báo nào.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>

<?php
// File này chỉ chuyên lo việc kết nối thôi
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "GymManagement";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối, nếu lỗi thì dừng luôn
if ($conn->connect_error) {
    die("Kết nối Database thất bại: " . $conn->connect_error);
}

// Nếu muốn gõ tiếng Việt không bị lỗi font thì thêm dòng này
$conn->set_charset("utf8");
?>
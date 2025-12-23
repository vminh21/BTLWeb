<?php
session_start(); // Bắt đầu session để có thể thao tác với nó

// 1. Xóa tất cả các biến session hiện có
session_unset();

// 2. Hủy hoàn toàn session trên server
session_destroy();

// 3. Chuyển hướng về trang chính
// Dùng đường dẫn tuyệt đối để đảm bảo chạy đúng từ bất kỳ thư mục nào
header("Location: /BTLWeb/Gym_Website/");
exit();
?>
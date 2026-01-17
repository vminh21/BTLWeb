-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 17, 2026 lúc 01:51 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `gymmanagement`
--
CREATE DATABASE IF NOT EXISTS `gymmanagement` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `gymmanagement`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`admin_id`, `email`, `password`, `full_name`, `phone_number`, `position`, `salary`) VALUES
(1, 'admin', 'admin123', 'Nguyễn Quản Trị', '0985772330', 'admin', 0.00),
(2, 'staff@gym.com', 'staff123', 'quocvit', '0901234567', 'staff', 1000000.00),
(4, 'hiepcuta@gym.com', '123', 'Nguyễn Hoàng Hiệp', '0901234567', 'staff', 100000.00),
(5, 'hehe@gmail.com', '123', 'Trần Vân Anh', '123', 'staff', 100.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `members`
--

INSERT INTO `members` (`member_id`, `full_name`, `email`, `password`, `phone_number`, `address`, `gender`, `status`) VALUES
(1, 'Phạm Văn Mạnh', 'manh.pham@email.com', '123456', '0912345678', 'Hà Nội', 'Male', 'Active'),
(2, 'Trần Thị Hương', 'huong.tran@email.com', '123456', '0987654321', 'Cầu Giấy', 'Male', 'Active'),
(3, 'Lê Hoàng Nam', 'nam.le@email.com', '123456', '0909090909', 'Thanh Xuân', 'Male', 'Active'),
(4, 'Nguyễn Thu Thảo', 'thao.nguyen@email.com', '123456', '0911223344', 'Đống Đa', 'Male', 'Inactive'),
(5, 'Đỗ Hùng Dũng', 'dung.do@email.com', '123456', '0977889900', 'Hai Bà Trưng', 'Male', 'Active'),
(8, 'manhbim', 'hehe@gmail.com', 'manhngu123', '0989089809', 'Hà Tĩnh', 'Male', 'Active'),
(9, 'Trần Vân Anh', 'vananh@gmail.com', 'vananh123', '0934355436', 'Hà Nội', 'Female', 'Active'),
(10, 'quenmk', 'minh6a1boi@gmail.com', 'hehe123', '0989089809', 'Hồ Chí Minh', 'Male', 'Active'),
(11, 'hehe', 'mem_1767109658@fit.com', '123456', NULL, NULL, 'Male', 'Active'),
(12, 'pre', 'mem_1767112125@fit.com', '123456', NULL, NULL, 'Male', 'Active');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `membership_packages`
--

CREATE TABLE `membership_packages` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(100) NOT NULL,
  `duration_days` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `membership_packages`
--

INSERT INTO `membership_packages` (`package_id`, `package_name`, `duration_days`, `price`, `description`) VALUES
(1, 'Gói 1 Tháng (Basic)', 30, 500000.00, 'Tập full giờ, không PT'),
(2, 'Gói 3 Tháng (Standard)', 90, 1350000.00, 'Tặng 1 buổi PT, khăn tắm'),
(3, 'Gói 1 Năm (VIP)', 365, 5000000.00, 'Full dịch vụ, tủ đồ riêng, xông hơi');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `member_subscriptions`
--

CREATE TABLE `member_subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Active','Expired','Cancelled') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `member_subscriptions`
--

INSERT INTO `member_subscriptions` (`subscription_id`, `member_id`, `package_id`, `start_date`, `end_date`, `status`) VALUES
(1, 1, 3, '2023-01-10', '2024-01-10', 'Active'),
(2, 2, 2, '2023-08-20', '2023-11-20', 'Expired'),
(3, 3, 1, '2023-12-01', '2023-12-01', 'Expired'),
(4, 4, 1, '2022-12-01', '2023-01-01', 'Expired'),
(5, 5, 2, '2023-12-15', '2024-03-15', 'Active'),
(6, 9, 3, '2025-12-21', '2026-12-21', 'Expired'),
(7, 11, 1, '2025-12-30', '2026-01-30', 'Active'),
(8, 12, 1, '2025-12-30', '2030-12-30', 'Active'),
(9, 8, 1, '2026-01-16', '2026-02-16', 'Active'),
(14, 9, 2, '2026-01-16', '2026-04-16', 'Active');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `image` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`notification_id`, `title`, `image`, `content`, `created_at`, `created_by`) VALUES
(1, 'Thông báo nghỉ lễ', NULL, 'Phòng tập sẽ đóng cửa vào ngày 01/01/2024 để bảo trì.', '2025-12-20 08:20:45', 1),
(2, 'Khuyến mãi Giáng sinh', NULL, 'Giảm 20% cho tất cả các gói gia hạn trước ngày 24/12.', '2025-12-20 08:20:45', 1),
(4, 'hehe', '1768543725_buoi2lis.png', 'hehe', '2026-01-16 13:08:45', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_type` enum('Registration','Renewal','Upgrade') NOT NULL,
  `transaction_date` datetime DEFAULT current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `member_id`, `amount`, `payment_method`, `transaction_type`, `transaction_date`, `note`) VALUES
(1, 1, 5000000.00, 'Chuyển khoản', 'Registration', '2023-01-10 09:00:00', 'Đăng ký VIP'),
(2, 2, 1350000.00, 'Tiền mặt', 'Renewal', '2023-05-20 10:30:00', 'Gia hạn lần 1'),
(3, 2, 1350000.00, 'Tiền mặt', 'Renewal', '2023-08-20 10:30:00', 'Gia hạn lần 2'),
(4, 3, 500000.00, 'Momo', 'Registration', '2023-11-01 14:00:00', 'Khách vãng lai'),
(6, 5, 1350000.00, 'Chuyển khoản', 'Registration', '2023-12-15 17:45:00', 'Khách mới'),
(7, 9, 5000000.00, 'Tiền mặt', 'Registration', '2025-12-21 20:49:56', 'Đăng ký gói Gói 1 Năm (VIP)'),
(8, 11, 500000.00, 'Chuyển khoản', 'Registration', '2025-12-30 22:47:38', NULL),
(9, 12, 5000000.00, 'Tiền mặt', 'Registration', '2025-12-30 23:28:45', NULL),
(11, 8, 500000.00, 'Tiền mặt', 'Renewal', '2026-01-16 13:44:43', NULL),
(16, 9, 1350000.00, 'Chuyển khoản', 'Registration', '2026-01-16 22:06:21', 'Thanh toán (Chuyển khoản): Gói 3 Tháng (Standard)');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Chỉ mục cho bảng `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `membership_packages`
--
ALTER TABLE `membership_packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Chỉ mục cho bảng `member_subscriptions`
--
ALTER TABLE `member_subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Chỉ mục cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `member_id` (`member_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `membership_packages`
--
ALTER TABLE `membership_packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `member_subscriptions`
--
ALTER TABLE `member_subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `member_subscriptions`
--
ALTER TABLE `member_subscriptions`
  ADD CONSTRAINT `member_subscriptions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `member_subscriptions_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `membership_packages` (`package_id`);

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`admin_id`);

--
-- Các ràng buộc cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

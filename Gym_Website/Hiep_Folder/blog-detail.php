<?php
// LẤY ID TỪ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// BLOG MẪU (4 BÀI)
$static_blogs = [
    1 => [
        "title" => "Dinh dưỡng tối ưu cho hiệu suất tập luyện",
        "content" => "Dinh dưỡng đóng vai trò cốt lõi trong quá trình tập luyện và phục hồi cơ thể. Dù bạn tập gym, chạy bộ hay chơi thể thao, chế độ ăn hợp lý sẽ giúp cải thiện hiệu suất và giảm nguy cơ chấn thương.

Trong khi tập luyện, cơ thể tiêu hao glycogen dự trữ trong cơ bắp để tạo năng lượng. Đồng thời, các sợi cơ chịu tác động và xuất hiện những tổn thương nhỏ. Sau buổi tập, cơ thể cần được cung cấp đủ dinh dưỡng để phục hồi và phát triển.

Protein giúp sửa chữa và xây dựng cơ bắp. Người tập luyện nên bổ sung khoảng 20–40g protein sau buổi tập. Carbohydrate giúp tái tạo glycogen, đặc biệt cần thiết với người tập cường độ cao. Chất béo lành mạnh hỗ trợ hoạt động hormone và không ảnh hưởng xấu nếu sử dụng hợp lý.

Thời điểm ăn sau tập tốt nhất là trong vòng 30–45 phút. Một số thực phẩm phù hợp gồm: cơm, khoai lang, yến mạch, trứng, cá hồi, ức gà, sữa chua và trái cây.

Bên cạnh ăn uống, uống đủ nước giúp bù điện giải, hạn chế chuột rút và tăng tốc độ hồi phục. Dinh dưỡng đúng cách kết hợp tập luyện đều đặn sẽ mang lại sức khỏe và vóc dáng bền vững."
    ],

    2 => [
        "title" => "Hướng dẫn đặt mục tiêu Fitness thông minh",
        "content" => "Nhiều người bắt đầu tập luyện với quyết tâm cao nhưng nhanh chóng bỏ cuộc do thiếu mục tiêu rõ ràng. Việc đặt mục tiêu đúng giúp bạn duy trì động lực và tập luyện lâu dài.

SMART là phương pháp đặt mục tiêu hiệu quả:
Specific – Cụ thể
Measurable – Đo lường được
Achievable – Khả thi
Relevant – Phù hợp
Time-bound – Có thời hạn

Ví dụ, thay vì nói \"tập thể dục nhiều hơn\", bạn có thể đặt mục tiêu: \"Đi bộ 30 phút mỗi sáng, 5 ngày mỗi tuần trong 2 tháng\".

Mục tiêu SMART giúp bạn dễ theo dõi tiến trình, đánh giá kết quả và điều chỉnh phù hợp. Bắt đầu từ những mục tiêu nhỏ sẽ giúp bạn tránh nản chí và hình thành thói quen tốt.

Đặt mục tiêu đúng cách không chỉ cải thiện sức khỏe mà còn giúp bạn duy trì lối sống năng động lâu dài."
    ],

    3 => [
        "title" => "Kỹ thuật tập luyện hiệu quả cho người bận rộn",
        "content" => "Cuộc sống bận rộn khiến nhiều người không còn thời gian chăm sóc sức khỏe. Tuy nhiên, chỉ cần 15–20 phút mỗi ngày, bạn vẫn có thể duy trì thể lực tốt.

Các bài tập ngắn như squat, hít đất, jumping jacks hay burpees giúp đốt năng lượng nhanh và cải thiện sức bền. Bạn có thể tập ngay tại nhà mà không cần dụng cụ.

Ngoài ra, hãy tận dụng sinh hoạt hằng ngày để vận động:
Đi cầu thang bộ thay vì thang máy
Đi bộ quãng đường ngắn
Xuống xe sớm hơn một trạm

Yoga hoặc giãn cơ nhẹ nhàng giúp giảm căng thẳng, cải thiện sự linh hoạt và phục hồi tinh thần. Quan trọng nhất là duy trì sự đều đặn và xem vận động như một phần của cuộc sống.

Chỉ cần kiên trì, dù bận rộn đến đâu, bạn vẫn có thể giữ gìn sức khỏe."
    ],

    4 => [
        "title" => "Cẩm nang chạy bộ cho người mới bắt đầu",
        "content" => "Chạy bộ là hình thức vận động đơn giản nhưng mang lại nhiều lợi ích cho sức khỏe. Người mới bắt đầu hoàn toàn có thể tập luyện nếu biết cách đúng.

Chạy bộ giúp cải thiện tim mạch, giảm cân, tăng sức bền và giải tỏa căng thẳng. Hoạt động này còn giúp nâng cao chất lượng giấc ngủ và tinh thần tích cực.

Trước khi chạy, hãy chuẩn bị giày phù hợp và khởi động kỹ. Người mới nên bắt đầu bằng đi bộ xen kẽ chạy chậm, sau đó tăng dần thời gian và quãng đường.

Giữ tư thế chạy đúng: lưng thẳng, vai thả lỏng, bước chân vừa phải. Sau khi chạy, giãn cơ giúp hạn chế đau nhức và chấn thương.

Kết hợp chạy bộ với chế độ ăn uống hợp lý và nghỉ ngơi đầy đủ sẽ giúp bạn duy trì thói quen này lâu dài và an toàn."
    ]
];

// NẾU ID KHÔNG HỢP LỆ → LẤY BÀI 1
if (!isset($static_blogs[$id])) {
    $id = 1;
}

$blog = $static_blogs[$id];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $blog['title']; ?></title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 40px;
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            line-height: 1.8;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
        }
        .back-btn {
            text-decoration: none;
            color: #ff6b6b;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
        }
        h1 {
            margin-top: 0;
            color: #333;
            border-left: 5px solid #ff6b6b;
            padding-left: 15px;
        }
        .content {
            white-space: pre-line;
            color: #444;
            font-size: 16px;
        }
        .nav-links {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .nav-links a {
            margin-right: 10px;
            text-decoration: none;
            background: #eee;
            padding: 6px 12px;
            border-radius: 5px;
            color: #333;
        }
        .nav-links a:hover {
            background: #ff6b6b;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="../index.php" class="back-btn">
        <i class='bx bx-left-arrow-alt'></i> Quay lại
    </a>

    <h1><?php echo $blog['title']; ?></h1>

    <div class="content">
        <?php echo $blog['content']; ?>
    </div>

    <div class="nav-links">
        <a href="?id=1">Dinh dưỡng</a>
        <a href="?id=2">Mục tiêu Fitness</a>
        <a href="?id=3">Người bận rộn</a>
        <a href="?id=4">Chạy bộ</a>
    </div>
</div>

</body>
</html>
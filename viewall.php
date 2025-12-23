<?php
$blogs = [
    1 => [
        "title" => "Fueling Your Body for Optimal Performance",
        "image" => "assets/blog-1.jpg",
        "desc" => "Bài viết về dinh dưỡng giúp tối ưu hiệu suất tập luyện..."
    ],
    2 => [
        "title" => "A Guide to Setting and Achieving Fitness Goals",
        "image" => "assets/blog-2.jpg",
        "desc" => "Hướng dẫn đặt mục tiêu và đạt được mục tiêu thể hình..."
    ],
    3 => [
        "title" => "Tips and Techniques for Efficient Exercise",
        "image" => "assets/blog-3.jpg",
        "desc" => "Mẹo và kỹ thuật tập luyện hiệu quả hơn..."
    ],
    4 => [
        "title" => "A Beginner's Guide to Starting Your Running Journey",
        "image" => "assets/blog-4.jpg",
        "desc" => "Hướng dẫn cho người mới bắt đầu chạy bộ..."
    ],
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tất cả bài viết Blog</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        body { padding: 40px; font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; }
        .viewall__container { max-width: 1200px; margin: auto; }
        .header__title { text-align: center; margin-bottom: 40px; }
        .header__title h2 { font-size: 2.5rem; color: #333; }
        
        /* Tái sử dụng Grid từ trang chủ của bạn */
        .blog__grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .blog__card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .blog__card:hover { transform: translateY(-5px); }
        .blog__card img { width: 100%; border-radius: 5px; margin-bottom: 1rem; }
        .blog__card h4 { font-size: 1.2rem; color: #333; margin-bottom: 10px; }
        .blog__card p { font-size: 0.9rem; color: #666; }

        .back-home {
            display: inline-block;
            margin-bottom: 20px;
            color: red;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="viewall__container">
    <a href="index.php#blog" class="back-home">← Quay lại Trang chủ</a>
    
    <div class="header__title">
        <h2>TẤT CẢ BÀI VIẾT</h2>
    </div>

    <div class="blog__grid">
        <?php foreach ($blogs as $id => $blog): ?>
            <a href="blog-detail.php?id=<?php echo $id; ?>" class="blog__card">
                <img src="<?php echo $blog['image']; ?>" alt="blog" />
                <h4><?php echo htmlspecialchars($blog['title']); ?></h4>
                <p><?php echo htmlspecialchars($blog['desc']); ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
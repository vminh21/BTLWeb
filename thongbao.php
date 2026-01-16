<?php
include("../QL_Profile/connectdb.php"); 
$sql = "SELECT * FROM notifications ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tất cả bài viết & Thông báo</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        body { 
            padding: 40px; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f8f9fa; 
            color: #333;
        }

        .thongbao__container { 
            max-width: 1200px; 
            margin: auto; 
        }

        .header__title { 
            text-align: center; 
            margin-bottom: 50px; 
        }

        .header__title h2 { 
            font-size: 2.5rem; 
            color: #1e1e2d; 
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }

        .header__title h2::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: #ff6b6b;
            border-radius: 2px;
        }

        
        .blog__grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2.5rem;
            align-items: stretch; 
        }

        
        .blog__card {
            display: flex;
            flex-direction: column; 
            background: white;
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            height: 100%;
            border: 1px solid #eee;
        }

        .blog__card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        
        .blog__card img { 
            width: 100%; 
            height: 220px; 
            object-fit: cover; 
            border-bottom: 1px solid #f0f0f0;
        }

        .blog__info {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1; 
        }

        .blog__card h4 { 
            font-size: 1.25rem; 
            color: #1e1e2d; 
            margin-bottom: 12px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.8em; 
        }

        .blog__card p { 
            font-size: 0.95rem; 
            color: #6c757d; 
            line-height: 1.6;
            flex-grow: 1; 
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 15px;
        }

        
        .badge-notify {
            background: #ff6b6b;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: inline-block;
            width: fit-content;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            margin-bottom: 25px;
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .back-home:hover {
            color: #e55b5b;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="thongbao__container">
    <a href="../index.php#blog" class="back-home">
        <i class='bx bx-left-arrow-alt'></i> ← Quay lại Trang chủ
    </a>
    
    <div class="header__title">
        <h2>THÔNG BÁO</h2>
    </div>

    <div class="blog__grid">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <a href="blog-detail.php?id=<?php echo $row['notification_id']; ?>" class="blog__card">
                    <?php 
                        // Kiểm tra nếu image là NULL, rỗng hoặc chuỗi "NULL" thì dùng banner-3.png
                        $img_source = $row['image'];
                        if (empty($img_source) || $img_source == 'NULL') {
                            $image_path = "../assets/banner-3.png";
                        } else {
                            $image_path = "../assets/" . $img_source;
                        }
                    ?>
                    <img src="<?php echo $image_path; ?>" alt="notification" />
                    
                    <div class="blog__info">
                        <span class="badge-notify">Thông báo Admin</span>
                        <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                        <p><?php echo htmlspecialchars($row['content']); ?></p>
                        <small style="color: #adb5bd;">Ngày đăng: <?php echo date("d/m/Y", strtotime($row['created_at'])); ?></small>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php endif; ?>

        
    </div>
</div>

</body>
</html>

<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="URLjs/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title>Fitness Program - FitPhysique</title>
</head>
<body>
    <main style="background-color: #0f0f0f; color: #fff; font-family: 'Segoe UI', sans-serif; padding-top: 80px;">
        
        <div style="text-align: center; padding: 60px 20px; background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/header.jpg') center/cover;">
            <h1 style="color: #f31221; font-size: 3rem; text-transform: uppercase;">General Fitness</h1>
            <p style="color: #ccc;">Duy trì vóc dáng, tăng cường sự dẻo dai và cải thiện chất lượng cuộc sống.</p>
        </div>

        <div class="section__container" style="padding: 40px 20px;">
            
            <div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; margin-bottom: 50px;">
                <div style="background: #fff; color: #000; border-radius: 15px; overflow: hidden; width: 45%; min-width: 300px; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                    <h3 style="text-align: center; padding: 15px; margin: 0; background: #eee;">MỤC TIÊU FITNESS</h3>
                    <img src="assets/blog-3.jpg" alt="Fitness Goals" style="width: 100%; height: 280px; object-fit: cover; display: block;">
                    <p style="padding: 20px; font-size: 0.95rem; color: #333; line-height: 1.5;">
                        Phù hợp cho mọi đối tượng muốn cải thiện sức khỏe tổng quát, tăng cường độ linh hoạt của cơ thể và giảm căng thẳng hàng ngày.
                    </p>
                </div>

                <div style="background: #fff; color: #000; border-radius: 15px; overflow: hidden; width: 45%; min-width: 300px; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                    <h3 style="text-align: center; padding: 15px; margin: 0; background: #eee;">LỐI SỐNG LÀNH MẠNH</h3>
                    <img src="assets/gallery-3.jpg" alt="Healthy Lifestyle" style="width: 100%; height: 280px; object-fit: cover; display: block;">
                    <p style="padding: 20px; font-size: 0.95rem; color: #333; line-height: 1.5;">
                        Kết hợp giữa tập luyện nhẹ nhàng, dinh dưỡng cân bằng và thói quen sinh hoạt khoa học để đạt được trạng thái tốt nhất.
                    </p>
                </div>
            </div>

            <div style="max-width: 700px; margin: 0 auto 50px auto; background: #1a1a1a; border-radius: 12px; overflow: hidden; border: 1px solid #333;">
                <div style="background: #f31221; padding: 15px; text-align: center;">
                    <h3 style="margin: 0; text-transform: uppercase; color: #fff;">Lịch Tập Fitness Hàng Tuần</h3>
                </div>
                <table style="width: 100%; border-collapse: collapse; color: #ddd;">
                    <thead>
                        <tr style="background: #252525; color: #f31221;">
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #333;">THỨ</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #333;">BÀI TẬP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 2</td><td>Tập toàn thân với tạ nhẹ (Full Body)</td></tr>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 3</td><td>Yoga hoặc Giãn cơ chuyên sâu</td></tr>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 4</td><td>Cardio nhẹ (Đi bộ nhanh/Bơi lội)</td></tr>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 5</td><td style="color: #888;">Nghỉ ngơi phục hồi</td></tr>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 6</td><td>Bài tập bụng và thăng bằng (Core)</td></tr>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 7</td><td>Hoạt động ngoài trời (Cycling/Hiking)</td></tr>
                        <tr><td style="padding: 12px 15px; color: #fff;">CN</td><td style="color: #888;">Nghỉ ngơi hoàn toàn</td></tr>
                    </tbody>
                </table>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="index.php" style="
                    display: inline-block; 
                    background-color: #f31221; 
                    color: #fff; 
                    padding: 15px 45px; 
                    text-decoration: none; 
                    text-transform: uppercase; 
                    font-weight: 700; 
                    border-radius: 50px;
                    box-shadow: 0 5px 15px rgba(243, 18, 33, 0.4);
                    transition: 0.3s;
                " onmouseover="this.style.backgroundColor='#d60b18'" onmouseout="this.style.backgroundColor='#f31221'">
                    BACK TO HOME
                </a>
            </div>
        </div>
    </main>

    </body>
</html>
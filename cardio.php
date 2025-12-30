<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="URLjs/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title>Cardio Training - FitPhysique</title>
</head>
<body>
    <main style="background-color: #0f0f0f; color: #fff; font-family: 'Segoe UI', sans-serif; padding-top: 80px;">
        
        <div style="text-align: center; padding: 60px 20px; background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/header.jpg') center/cover;">
            <h1 style="color: #f31221; font-size: 3rem; text-transform: uppercase;">Cardio Training</h1>
            <p style="color: #ccc;">Đốt cháy năng lượng, cải thiện tim mạch và tăng cường sức bền bỉ.</p>
        </div>

        <div class="section__container" style="padding: 40px 20px;">
            
            <div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; margin-bottom: 50px;">
                <div style="background: #fff; color: #000; border-radius: 15px; overflow: hidden; width: 45%; min-width: 300px; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                    <h3 style="text-align: center; padding: 15px; margin: 0; background: #eee;">LỢI ÍCH CARDIO</h3>
                    <img src="assets/trainer-2.jpg" alt="Cardio Benefit" style="width: 100%; height: 280px; object-fit: cover; display: block;">
                    <ul style="padding: 20px; font-size: 0.95rem; color: #333; line-height: 1.6; list-style: none;">
                        <li><i class="ri-heart-pulse-fill" style="color: #f31221;"></i> Tăng cường sức khỏe hệ tim mạch.</li>
                        <li><i class="ri-fire-fill" style="color: #f31221;"></i> Đốt cháy mỡ thừa hiệu quả nhất.</li>
                        <li><i class="ri-windy-line" style="color: #f31221;"></i> Tăng cường trao đổi chất và sức bền.</li>
                    </ul>
                </div>

                <div style="background: #fff; color: #000; border-radius: 15px; overflow: hidden; width: 45%; min-width: 300px; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                    <h3 style="text-align: center; padding: 15px; margin: 0; background: #eee;">PHƯƠNG PHÁP LISS & HIIT</h3>
                    <img src="assets/blog-2.jpg" alt="HIIT Training" style="width: 100%; height: 280px; object-fit: cover; display: block;">
                    <p style="padding: 20px; font-size: 0.95rem; color: #333; line-height: 1.5;">
                        Kết hợp giữa cường độ thấp ổn định (LISS) và cường độ cao ngắt quãng (HIIT) để tối ưu hóa việc giảm cân và thể lực.
                    </p>
                </div>
            </div>

            <div style="max-width: 700px; margin: 0 auto 50px auto; background: #1a1a1a; border-radius: 12px; overflow: hidden; border: 1px solid #333;">
                <div style="background: #f31221; padding: 15px; text-align: center;">
                    <h3 style="margin: 0; text-transform: uppercase; color: #fff;">Lịch Tập Cardio 7 Ngày</h3>
                </div>
                <table style="width: 100%; border-collapse: collapse; color: #ddd;">
                    <thead>
                        <tr style="background: #252525; color: #f31221;">
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #333;">THỨ</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 2px solid #333;">HOẠT ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 2</td><td>Chạy bộ nhẹ nhàng (30 phút LISS)</td></tr>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 3</td><td><b style="color:#f31221">HIIT:</b> 20 phút cường độ cao</td></tr>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 4</td><td>Đạp xe hoặc Bơi lội (45 phút)</td></tr>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 5</td><td style="color: #888;">Nghỉ ngơi chủ động (Yoga/Giãn cơ)</td></tr>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 6</td><td>Nhảy dây cường độ cao (15 phút)</td></tr>
                        <tr style="border-bottom: 1px solid #222;"><td style="padding: 12px 15px; color: #fff;">Thứ 7</td><td><b style="color:#f31221">Outdoor:</b> Chạy bộ hoặc Leo núi</td></tr>
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
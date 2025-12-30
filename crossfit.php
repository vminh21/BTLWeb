<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title>Huấn Luyện CrossFit - FitPhysique</title>
</head>
<body style="margin: 0; background-color: #0f0f0f;">

    <main style="color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding-top: 80px;">
        
        <div style="text-align: center; padding: 80px 20px; background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('assets/header.jpg') center/cover;">
            <h1 style="color: #f31221; font-size: 3.5rem; text-transform: uppercase; margin: 0; letter-spacing: 2px;">HUẤN LUYỆN CROSSFIT</h1>
            <p style="color: #ccc; font-size: 1.1rem; margin-top: 10px;">Vượt qua mọi giới hạn với các bài tập phối hợp cường độ cao nhất.</p>
        </div>

        <div class="section__container" style="max-width: 1200px; margin: 0 auto; padding: 60px 20px;">
            
            <div style="display: flex; gap: 30px; flex-wrap: wrap; justify-content: center; margin-bottom: 60px;">
                
                <div style="background: #fff; color: #000; border-radius: 20px; overflow: hidden; width: 45%; min-width: 320px; box-shadow: 0 15px 30px rgba(0,0,0,0.5);">
                    <h3 style="text-align: center; padding: 20px; margin: 0; background: #f8f8f8; color: #f31221; border-bottom: 1px solid #ddd;">BÀI TẬP TRONG NGÀY (WOD)</h3>
                    <img src="assets/trainer-4.jpg" alt="CrossFit WOD" style="width: 100%; height: 300px; object-fit: cover; display: block;">
                    <p style="padding: 25px; font-size: 1rem; color: #444; line-height: 1.6;">
                        Các bài tập thay đổi liên tục mỗi ngày giúp cơ thể luôn trong trạng thái thử thách, ép buộc các nhóm cơ phát triển toàn diện về sức mạnh và tốc độ.
                    </p>
                </div>

                <div style="background: #fff; color: #000; border-radius: 20px; overflow: hidden; width: 45%; min-width: 320px; box-shadow: 0 15px 30px rgba(0,0,0,0.5);">
                    <h3 style="text-align: center; padding: 20px; margin: 0; background: #f8f8f8; color: #f31221; border-bottom: 1px solid #ddd;">TINH THẦN ĐỒNG ĐỘI</h3>
                    <img src="assets/gallery-6.jpg" alt="Cộng đồng" style="width: 100%; height: 300px; object-fit: cover; display: block;">
                    <p style="padding: 25px; font-size: 1rem; color: #444; line-height: 1.6;">
                        Tập luyện cùng nhóm giúp đẩy cao tinh thần cạnh tranh. Sự cổ vũ từ đồng đội sẽ giúp bạn hoàn thành những mức tạ tưởng chừng không thể.
                    </p>
                </div>
            </div>

            <div style="max-width: 750px; margin: 0 auto 60px auto; background: #1a1a1a; border-radius: 15px; overflow: hidden; border: 1px solid #333; box-shadow: 0 10px 40px rgba(0,0,0,0.7);">
                <div style="background: #f31221; padding: 20px; text-align: center;">
                    <h3 style="margin: 0; text-transform: uppercase; color: #fff; letter-spacing: 1px;">Lịch Tập CrossFit Chuyên Sâu</h3>
                </div>
                <table style="width: 100%; border-collapse: collapse; color: #eee; font-size: 1rem;">
                    <thead>
                        <tr style="background: #252525; color: #f31221;">
                            <th style="padding: 18px; text-align: left; border-bottom: 2px solid #333;">NGÀY</th>
                            <th style="padding: 18px; text-align: left; border-bottom: 2px solid #333;">NỘI DUNG WOD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #2a2a2a;">
                            <td style="padding: 15px 18px; font-weight: bold; color: #fff;">Thứ Hai</td>
                            <td style="padding: 15px 18px;"><b style="color: #f31221;">Sức Mạnh:</b> Cử tạ Olympic & Chạy nước rút</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #2a2a2a;">
                            <td style="padding: 15px 18px; font-weight: bold; color: #fff;">Thứ Ba</td>
                            <td style="padding: 15px 18px;"><b style="color: #f31221;">Sức Bền:</b> Chèo thuyền & Bài tập trọng lượng cơ thể</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #2a2a2a;">
                            <td style="padding: 15px 18px; font-weight: bold; color: #fff;">Thứ Tư</td>
                            <td style="padding: 15px 18px;"><b style="color: #f31221;">Kỹ Thuật:</b> Xà đơn, Vòng treo & Cơ bụng</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #2a2a2a;">
                            <td style="padding: 15px 18px; font-weight: bold; color: #fff;">Thứ Năm</td>
                            <td style="padding: 15px 18px; color: #888; font-style: italic;">Nghỉ ngơi chủ động (Giãn cơ nhẹ)</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #2a2a2a;">
                            <td style="padding: 15px 18px; font-weight: bold; color: #fff;">Thứ Sáu</td>
                            <td style="padding: 15px 18px;"><b style="color: #f31221;">MetCon:</b> Vòng lặp cường độ cao ngắt quãng</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #2a2a2a;">
                            <td style="padding: 15px 18px; font-weight: bold; color: #fff;">Thứ Bảy</td>
                            <td style="padding: 15px 18px;"><b style="color: #f31221;">Đồng Đội:</b> Bài tập phối hợp nhóm cuối tuần</td>
                        </tr>
                        <tr>
                            <td style="padding: 15px 18px; font-weight: bold; color: #fff;">Chủ Nhật</td>
                            <td style="padding: 15px 18px; color: #888; font-style: italic;">Nghỉ ngơi hoàn toàn</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="text-align: center; margin-bottom: 40px;">
                <a href="index.php" style="
                    display: inline-block; 
                    background-color: #f31221; 
                    color: #fff; 
                    padding: 15px 50px; 
                    text-decoration: none; 
                    text-transform: uppercase; 
                    font-weight: 700; 
                    border-radius: 50px;
                    font-size: 1rem;
                    transition: all 0.3s ease;
                    box-shadow: 0 0 20px rgba(243, 18, 33, 0.5); /* Hiệu ứng phát sáng đỏ */
                " onmouseover="
                    this.style.backgroundColor='#d60b18'; 
                    this.style.transform='translateY(-3px)';
                    this.style.boxShadow='0 0 30px rgba(243, 18, 33, 0.8)';
                " onmouseout="
                    this.style.backgroundColor='#f31221'; 
                    this.style.transform='translateY(0)';
                    this.style.boxShadow='0 0 20px rgba(243, 18, 33, 0.5)';
                ">
                    BACK TO HOME
                </a>
            </div>

        </div>
    </main>

</body>
</html>
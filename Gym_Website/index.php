<?php
session_start();
// if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
//     // Nếu là Admin, chuyển hướng ngay lập tức sang trang quản trị
//     header("Location: Form_Login_Logout/Login.php");
//     exit(); 
// }
$chat_greeting = "Chào bạn";
    if (isset($_SESSION['full_name'])) {
        $chat_greeting = "Chào " . htmlspecialchars($_SESSION['full_name']);
    }
?>

<!DOCTYPE html>
<html lang="vi"> <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="URLjs/remixicon.css" rel="stylesheet"/>
        <link rel="stylesheet" href="URLjs/swiper-bundle.min.css"/>
        <link rel="stylesheet" href="URLjs/font-awesome.min.css">
        <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
        <title>FitPhysique</title>
    </head>
    <body>
        
        <nav>
    <div class="nav__bar">
        <div class="nav__header">
            <div class="nav__logo">
                <a href="#"><img src="assets/logo.png" alt="logo" /></a>
            </div>
            <div class="nav__menu__btn" id="menu-btn">
                <i class="ri-menu-line"></i>
            </div>
        </div>
        <ul class="nav__links" id="nav-links">
            <li><a href="#">HOME</a></li>
            <li><a href="#about">ABOUT</a></li>
            <li><a href="#trainer">TRAINER</a></li>
            <li><a href="#client">CLIENT</a></li>
            <li><a href="#blog">BLOG</a></li>
            <li><a href="#contact">CONTACT</a></li>
            
            <?php if (isset($_SESSION['full_name'])): ?>
                
                <li class="user-info-box">
                    <span style="color: white; font-weight: 600;">
                        Xin chào, <?php echo $_SESSION['full_name']; ?>
                    </span>
                    
                    <a href="QL_Profile/member_profile.php" class="btn-profile">Hồ sơ</a>
                    
                    <a href="Form_Login_Logout/logout.php" title="Đăng xuất" style="color: #ccc;">
                        <i class="ri-logout-box-r-line"></i>
                    </a>
                </li>

            <?php else: ?>

                <li><a href="Form_Login_Logout/login.php" class="nav__login">ĐĂNG NHẬP</a></li>

            <?php endif; ?>
            </ul>
    </div>
</nav>
        <header class="header" id="header">
            <div class="section__container header__container">
                <div class="header__content">
                    <h1>HARD WORK</h1>
                    <h2>ISS FOR EVERY SUCCESS</h2>
                    <p>Bắt đầu bằng việc lấy cảm hứng, tiếp tục để truyền cảm hứng.</p>
                    <div class="header__btn">
                        <button class="btn btn__primary" id="btn-login">GET STARTED</button>
                    </div>
                </div>
            </div>
        </header>

        <section class="section__container about__container" id="about">
            <div class="about__header">
            <h2 class="section__header">ABOUT US</h2>
            <p class="section__description">
                Sứ mệnh của chúng tôi là truyền cảm hứng và hỗ trợ mọi người đạt được các mục tiêu về sức khỏe và thể chất, bất kể trình độ hay nền tảng của họ.
            </p>
            </div>
            <div class="about__grid">
            <div class="about__card">
             <h4>WINNER COACHES</h4>
             <p>
                Chúng tôi tự hào sở hữu đội ngũ huấn luyện viên tận tâm và giàu kinh nghiệm, cam kết giúp bạn thành công.
             </p>
            </div>
            <div class="about__card">
                <h4>AFFORDABLE PRICE</h4>
                <p>
                    Chúng tôi tin rằng mọi người đều xứng đáng được tiếp cận các cơ sở tập luyện chất lượng cao với mức giá hợp lý.
                </p>
               </div>
               <div class="about__card">
                <h4>MODERN EQUIPMENTS</h4>
                <p>
                    Luôn dẫn đầu xu hướng với các trang thiết bị hiện đại được thiết kế để nâng cao trải nghiệm tập luyện của bạn.
                </p>
               </div>
            </div>
        </section>

        <section class="session">
            <div class="session__card">
                <h4>BODY BUILDING</h4>
                <p>
                 Kiến tạo vóc dáng và xây dựng khối lượng cơ bắp với các chương trình thể hình chuyên biệt tại FitPhysique.
                </p>
                <a href="Tuan_Folder/bodybuilding.php" class="btn btn__secondary">
                 READ MORE <i class="ri-arrow-right-line"></i>
            </a>
              </div>
              <div class="session__card">
                <h4>CARDIO</h4>
                <p>
                 Tăng nhịp tim và cải thiện sức bền với các bài tập cardio năng động tại FitPhysique.
                </p>
                <a href="Tuan_Folder/cardio.php"class="btn btn__secondary">
                 READ MORE <i class="ri-arrow-right-line"></i>
            </a>
              </div>
              <div class="session__card">
                <h4>FITNESS</h4>
                <p>
                 Tiếp cận thể dục toàn diện với các chương trình fitness phong phú và đa dạng tại FitPhysique.
                </p>
                <a href="Tuan_Folder/fitness.php" class="btn btn__secondary">
                 READ MORE <i class="ri-arrow-right-line"></i>
            </a>
              </div>
              <div class="session__card">
                <h4>CROSSFIT</h4>
                <p>
                 Trải nghiệm bài tập toàn thân đỉnh cao với các lớp CrossFit cường độ cao tại FitPhysique.
                </p>
                <a href="Tuan_Folder/crossfit.php" class="btn btn__secondary">
                 READ MORE <i class="ri-arrow-right-line"></i>
            </a>
              </div>
        </section>

        <section class="section__container trainer__container" id="trainer">
            <h2 class="section__header">MEET OUR TRAINERS</h2>
            <div class="trainer__grid">
                <div class="trainer__card">
                    <img src="assets/pthiepcuta.jpg" alt="trainer" />
                    <h4>Hiệp Cử Tạ</h4>
                    <p>HLV Thể Hình</p>
                    <div class="trainer__socials">
                      <a href="https://www.facebook.com/hoang.hiep.853060"><i class="ri-facebook-fill"></i></a>
                      <a href="#"><i class="ri-twitter-fill"></i></a>
                      <a href="#"><i class="ri-youtube-fill"></i></a>
                    </div>
                  </div>
                  <div class="trainer__card">
                    <img src="assets/trainer-2.jpg" alt="trainer" />
                    <h4>ROSY RIVERA</h4>
                    <p>HLV Cardio</p>
                    <div class="trainer__socials">
                      <a href="#"><i class="ri-facebook-fill"></i></a>
                      <a href="#"><i class="ri-twitter-fill"></i></a>
                      <a href="#"><i class="ri-youtube-fill"></i></a>
                    </div>
                  </div>
                  <div class="trainer__card">
                    <img src="assets/trainer-3.jpg" alt="trainer" />
                    <h4>MATT STONIE</h4>
                    <p>HLV Fitness</p>
                    <div class="trainer__socials">
                      <a href="#"><i class="ri-facebook-fill"></i></a>
                      <a href="#"><i class="ri-twitter-fill"></i></a>
                      <a href="#"><i class="ri-youtube-fill"></i></a>
                    </div>
                  </div>
                  <div class="trainer__card">
                    <img src="assets/trainer-4.jpg" alt="trainer" />
                    <h4>SOFIA LAUREN</h4>
                    <p>HLV Crossfit</p>
                    <div class="trainer__socials">
                      <a href="#"><i class="ri-facebook-fill"></i></a>
                      <a href="#"><i class="ri-twitter-fill"></i></a>
                      <a href="#"><i class="ri-youtube-fill"></i></a>
                    </div>
                  </div>
            </div>
        </section>

        <section class="membership">
    <div class="section__container membership__container">
        <h2 class="section__header">MEMBERSHIP</h2>
        <div class="membership__grid">
            
            <div class="membership__card">
                <h4>STANDARD</h4>
                <ul>
                    <li><span><i class="ri-check-line"></i></span> Truy cập sàn tập và thiết bị tiêu chuẩn.</li>
                    <li><span><i class="ri-check-line"></i></span> Các lớp tập nhóm: Yoga, Zumba, Pilates.</li>
                    <li><span><i class="ri-check-line"></i></span> Tư vấn thể hình miễn phí.</li>
                    <li><span><i class="ri-check-line"></i></span> Phòng thay đồ và phòng tắm.</li>
                    <li><span><i class="ri-check-line"></i></span> Hướng dẫn dinh dưỡng và đồ ăn nhẹ.</li>
                    <li><span><i class="ri-check-line"></i></span> Giảm giá hàng hóa cho hội viên.</li>
                </ul>
                <h3><sup>$</sup>500000<span>/THÁNG</span></h3>
                
                <a href="Form_Login_Logout/checkout.php?package_id=1" class="btn btn__primary">BUY NOW</a>
            </div>

            <div class="membership__card">
                <h4>PROFESSIONAL</h4>
                <ul>
                    <li><span><i class="ri-check-line"></i></span> Bao gồm mọi quyền lợi gói Standard.</li>
                    <li><span><i class="ri-check-line"></i></span> Ưu tiên đặt lịch huấn luyện cá nhân.</li>
                    <li><span><i class="ri-check-line"></i></span> Truy cập khu vực thiết bị nâng cao.</li>
                    <li><span><i class="ri-check-line"></i></span> Tư vấn thể hình miễn phí.</li>
                    <li><span><i class="ri-check-line"></i></span> Tham gia sự kiện và workshop độc quyền.</li>
                    <li><span><i class="ri-check-line"></i></span> Giảm giá cho các dịch vụ bổ sung.</li>
                </ul>
                <h3><sup>$</sup>1350000<span>/3 THÁNG</span></h3>
                
                <a href="Form_Login_Logout/checkout.php?package_id=2" class="btn btn__primary">BUY NOW</a>
            </div>

            <div class="membership__card">
                <h4>ULTIMATE</h4>
                <ul>
                    <li><span><i class="ri-check-line"></i></span> Bao gồm quyền lợi gói Standard và Professional.</li>
                    <li><span><i class="ri-check-line"></i></span> Truy cập không giới hạn tiện ích cao cấp.</li>
                    <li><span><i class="ri-check-line"></i></span> Chỗ đậu xe riêng hoặc dịch vụ đỗ xe.</li>
                    <li><span><i class="ri-check-line"></i></span> Các lớp học thể hình cao cấp miễn phí.</li>
                    <li><span><i class="ri-check-line"></i></span> Kế hoạch tập luyện cá nhân hóa.</li>
                    <li><span><i class="ri-check-line"></i></span> Quyền ưu tiên vé khách mời và sự kiện.</li>
                </ul>
                <h3><sup>$</sup>5000000<span>/NĂM</span></h3>
                
                <a href="Form_Login_Logout/checkout.php?package_id=3" class="btn btn__primary">BUY NOW</a>
            </div>

        </div>
    </div>
</section>

        <section class="section__container client__container" id="client">
          <h2 class="section__header">OUR TESTIMONIALS</h2>
          <div class="swiper">
            <div class="swiper-wrapper">
              <div class="swiper-slide">
                <div class="client__card">
                  <img src="assets/client-1.jpg" alt="client" />
                  <div><i class="ri-double-quotes-r"></i></div>
                  <p>
                    Tôi đã là hội viên tại FitPhysique hơn một năm nay và cực kỳ hài lòng với trải nghiệm của mình. Các lớp học ở đây rất ấn tượng - từ những buổi cardio năng lượng cao đến các lớp yoga thư giãn, có đủ mọi thứ cho mọi người.
                  </p>
                  <h4>Sarah Johnson</h4>
                </div>
              </div>
              <div class="swiper-slide">
                <div class="client__card">
                  <img src="assets/client-2.jpg" alt="client" />
                  <div><i class="ri-double-quotes-r"></i></div>
                  <p>
                    Các lớp học luôn được lên kế hoạch kỹ lưỡng và hấp dẫn, các huấn luyện viên làm rất tốt việc giữ động lực cho chúng tôi trong suốt buổi tập. Tôi rất biết ơn vì đã tìm thấy một phòng tập hỗ trợ và hòa đồng như vậy.
                  </p>
                  <h4>Michael Wong</h4>
                </div>
              </div>
              <div class="swiper-slide">
                <div class="client__card">
                  <img src="assets/client-3.jpg" alt="client" />
                  <div><i class="ri-double-quotes-r"></i></div>
                  <p>
                    Tôi đã thử nhiều phòng tập trước đây, nhưng không đâu sánh bằng FitPhysique. Ngay từ khoảnh khắc bước qua cánh cửa, tôi đã cảm thấy được chào đón và hỗ trợ bởi đội ngũ nhân viên cũng như các hội viên khác.
                  </p>
                  <h4>Emily Davis</h4>
                </div>
              </div>
            </div>
            <div class="swiper-pagination"></div>
          </div>
        </section>

        <section class="blog" id="blog">
  <div class="section__container blog__container">
    <h2 class="section__header">LATEST BLOGS</h2> <div class="blog__grid">

      <a href="Hiep_Folder/blog-detail.php?id=1" class="blog__card">
        <img src="assets/blog-1.jpg" alt="blog" />
        <h4>Dinh dưỡng tối ưu cho hiệu suất tập luyện</h4>
        <p>Bí quyết nạp năng lượng đúng cách trước và sau khi tập.</p>
      </a>

      <a href="Hiep_Folder/blog-detail.php?id=2" class="blog__card">
        <img src="assets/blog-2.jpg" alt="blog" />
        <h4>Hướng dẫn đặt mục tiêu Fitness thông minh</h4>
        <p>Cách áp dụng nguyên tắc SMART để đạt được vóc dáng mơ ước.</p>
      </a>

      <a href="Hiep_Folder/blog-detail.php?id=3" class="blog__card">
        <img src="assets/blog-3.jpg" alt="blog" />
        <h4>Kỹ thuật tập luyện hiệu quả cho người bận rộn</h4>
        <p>Tối ưu hóa thời gian với các phương pháp tập cường độ cao.</p>
      </a>

      <a href="Hiep_Folder/blog-detail.php?id=4" class="blog__card">
        <img src="assets/blog-4.jpg" alt="blog" />
        <h4>Cẩm nang chạy bộ cho người mới bắt đầu</h4>
        <p>Những lưu ý quan trọng để bắt đầu hành trình chạy bộ an toàn.</p>
      </a>

    </div>

    <div class="blog__btn">
      <a href="Hiep_Folder/viewall.php" class="btn btn__primary">VIEW ALL</a>
    </div>

  </div>
</section>

        <section class="section__container bmi__container">
            
            <div class="wrapper">
            <h2 class="bmi__header">BMI CALCULATOR</h2>
            <p>Chiều cao (CM):
                <input type="text" id="height"><br><span id="height_error"></span>
            </p>
    
            <p>Cân nặng (KG):
                <input type="text" id="weight"><br><span id="weight_error"></span>
            </p>
    
            <button class="btn" id="btn">CALCULATE</button>
            <p id="output"></p>
          </div>
        </section>

        <footer class="footer" id="contact">
          <div class="section__container footer__container">
            <div class="footer__col">
              <div class="footer__logo">
                <a href="#"><img src="assets/logo.png" alt="logo" /></a>
              </div>
              <p>
                Chào mừng đến với FitPhysique, nơi chúng tôi tin rằng mỗi hành trình đến với thể hình đều độc đáo và đầy sức mạnh.
              </p>
              <ul class="footer__links">
                <li>
                  <a href="#">
                    <span><i class="ri-map-pin-2-fill"></i></span>
                    243 Đường Nguyễn Xiển<br />Thanh Xuân, Hà Nội
                  </a>
                </li>
                <li>
                  <a href="#">
                    <span><i class="ri-phone-fill"></i></span>
                    +84 985772330
                  </a>
                </li>
                <li>
                  <a href="#">
                    <span><i class="ri-mail-fill"></i></span>
                    info@fitphysique.com
                  </a>
                </li>
              </ul>
            </div>
            <div class="footer__col">
              <h4>GALLERY</h4>
              <div class="gallery__grid">
                <img src="assets/gallery-1.jpg" alt="gallery" />
                <img src="assets/gallery-2.jpg" alt="gallery" />
                <img src="assets/gallery-3.jpg" alt="gallery" />
                <img src="assets/gallery-4.jpg" alt="gallery" />
                <img src="assets/gallery-5.jpg" alt="gallery" />
                <img src="assets/gallery-6.jpg" alt="gallery" />
                <img src="assets/gallery-7.jpg" alt="gallery" />
                <img src="assets/gallery-8.jpg" alt="gallery" />
                <img src="assets/gallery-9.jpg" alt="gallery" />
              </div>
            </div>
            <div class="footer__col">
              <h4>NEWSLETTER</h4>
              <p>
                Đừng bỏ lỡ những tin tức và ưu đãi mới nhất - đăng ký ngay hôm nay và tham gia cộng đồng thể hình thịnh vượng của chúng tôi!
              </p>
              <form onsubmit="sendEmail(); reset(); return false;">
                <input type="text" id="name" placeholder="Nhập tên" />
                <input type="text" id="email" placeholder="Nhập Email" />
                <input type="text" id="phone" placeholder="Nhập SĐT" />
                <button type="submit" class="btn btn__primary">SEND</button>
              </form>
              <div class="footer__socials">
                <a href="#"><i class="ri-facebook-fill"></i></a>
                <a href="#"><i class="ri-twitter-fill"></i></a>
                <a href="#"><i class="ri-youtube-fill"></i></a>
              </div>
            </div>
          </div>
          <div class="footer__bar">
            Copyright © 2024 Web Design Mastery. All rights reserved.
          </div>
        </footer>
        <div class="chat-toggle-btn" onclick="toggleChat()">
    <i class="ri-messenger-fill"></i>
</div>

<div class="chat-box-container" id="chatBox">
    <div class="chat-header">
        <div class="chat-title">
            <i class="ri-customer-service-2-fill"></i> FitPhysique Support
        </div>
        <div class="chat-close" onclick="toggleChat()">
            <i class="ri-close-line"></i>
        </div>
    </div>

    <div class="chat-body" id="chatBody">
        <div class="message bot-message">
            <p><?php echo $chat_greeting; ?>, tôi có thể giúp gì cho bạn?</p>
        </div>
        
        <div class="chat-options" id="chatOptions">
            <button onclick="selectOption('Tư vấn')">Tư vấn sức khỏe</button>
            <button onclick="selectOption('Gói tập')">Thông tin gói tập</button>
            <button onclick="selectOption('Gặp Admin')">Chat với người thật</button>
        </div>
    </div>
    
    <div class="chat-footer">
    <input type="text" id="chatInput" placeholder="Nhập tin nhắn..." onkeypress="handleEnter(event)">
    <button onclick="sendMessage()"><i class="ri-send-plane-fill"></i></button>
</div>
</div>
         
        <script src="URLjs/scrollreveal.js"></script>
        <script src="URLjs/swiper-bundle.min.js"></script>
        <script src="URLjs/jquery.min.js"></script>
        <script src="main.js"></script>
    </body>

</html>
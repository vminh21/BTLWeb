<section class="blog" id="blog">
  <div class="section__container blog__container">
    <h2 class="section__header">LATEST BLOGS</h2> <div class="blog__grid">

      <a href="Hiep_Folder/blog-detail.php?id=b1" class="blog__card">
        <img src="assets/blog-1.jpg" alt="blog" />
        <h4>Dinh dưỡng tối ưu cho hiệu suất tập luyện</h4>
        <p>Bí quyết nạp năng lượng đúng cách trước và sau khi tập.</p>
      </a>

      <a href="Hiep_Folder/blog-detail.php?id=b2" class="blog__card">
        <img src="assets/blog-2.jpg" alt="blog" />
        <h4>Hướng dẫn đặt mục tiêu Fitness thông minh</h4>
        <p>Cách áp dụng nguyên tắc SMART để đạt được vóc dáng mơ ước.</p>
      </a>

      <a href="Hiep_Folder/blog-detail.php?id=b3" class="blog__card">
        <img src="assets/blog-3.jpg" alt="blog" />
        <h4>Kỹ thuật tập luyện hiệu quả cho người bận rộn</h4>
        <p>Tối ưu hóa thời gian với các phương pháp tập cường độ cao.</p>
      </a>

      <a href="Hiep_Folder/blog-detail.php?id=b4" class="blog__card">
        <img src="assets/blog-4.jpg" alt="blog" />
        <h4>Cẩm nang chạy bộ cho người mới bắt đầu</h4>
        <p>Những lưu ý quan trọng để bắt đầu hành trình chạy bộ an toàn.</p>
      </a>

    </div>

    <div class="blog__btn">
      <a href="Hiep_Folder/thongbao.php" class="btn btn__primary">THÔNG BÁO</a>
    </div>

  </div>
</section>

        <section class="bmi__section">
    <div class="bmi__container">
        <div class="bmi__left">
            <img src="assets/bmi.png" alt="BMI Illustration">
        </div>

        <div class="bmi__right">
            <h2 class="bmi__title">TÍNH BMI</h2>
            <p class="bmi__subtitle">Nhập thông số để kiểm tra sức khỏe của bạn.</p>
            
            <div class="bmi__form-grid">
                <div>
                    <input type="number" id="height" placeholder="Chiều cao / cm">
                    <small id="height_error" style="color: #dc030a; display: block; margin-top: 5px;"></small>
                </div>
                <div>
                    <input type="number" id="weight" placeholder="Cân nặng / kg">
                    <small id="weight_error" style="color: #dc030a; display: block; margin-top: 5px;"></small>
                </div>
                <input type="number" placeholder="Tuổi">
                
                <select id="gender" class="bmi__select">
                    <option value="" disabled selected>Giới tính</option>
                    <option value="male">Nam</option>
                    <option value="female">Nữ</option>
                </select>
                

                
                <div class="bmi__action">
                    <button class="bmi__submit-btn" id="btn">NHẬN KẾT QUẢ</button>
                    <div id="output" class="bmi__result-text">/ Kết quả: <span>Chưa có dữ liệu</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

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
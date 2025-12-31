<section class="section__container blog__container" id="blog">
  <h2 class="section__header">LATEST BLOGS</h2>
  
  <div class="blog__grid">
    <?php
    // 1. KẾT NỐI DATABASE
    include_once("QL_Profile/connectdb.php");

    // 2. TRUY VẤN LẤY 4 BÀI MỚI NHẤT
    $sql_latest = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 4";
    $result_latest = mysqli_query($conn, $sql_latest);

    // 3. HIỂN THỊ DỮ LIỆU
    if ($result_latest && mysqli_num_rows($result_latest) > 0) {
        while($row = mysqli_fetch_assoc($result_latest)) {
            $image_src = (!empty($row['image']) && $row['image'] != 'NULL') 
                         ? "assets/" . $row['image'] 
                         : "assets/blog-1.jpg";
            
            $display_title = mb_strimwidth(htmlspecialchars($row['title']), 0, 60, "...");
            $display_desc = mb_strimwidth(strip_tags($row['content']), 0, 100, "...");
            ?>
            
            <a href="Hiep_Folder/blog-detail.php?id=<?php echo $row['notification_id']; ?>" class="blog__card">
              <img src="<?php echo $image_src; ?>" alt="blog" style="width: 100%; height: 200px; object-fit: cover;" />
              <h4 style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 15px; min-height: 3rem;">
                <?php echo $display_title; ?>
              </h4>
              <p style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin: 0 15px 15px; font-size: 0.9rem; color: #666;">
                <?php echo $display_desc; ?>
              </p>
            </a>

            <?php
        }
    } else { ?>
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
    <?php } ?>
  </div>

  <div class="blog__view-all">
    <a href="Hiep_Folder/viewall.php" class="btn" style="background-color: #d32f2f; color: white; padding: 12px 40px; text-decoration: none; font-weight: bold; display: inline-block;">
        VIEW ALL
    </a>
  </div>
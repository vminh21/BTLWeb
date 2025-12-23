document.addEventListener("DOMContentLoaded", function () {
  // 1. KHAI BÁO CÁC BIẾN (Dùng chung)
  const addModal = document.getElementById("addTransactionModal");
  const editModal = document.getElementById("editTransactionModal");
  const btnOpenAdd = document.getElementById("btnOpenModal");
  const memberSelect = document.getElementById("memberSelect");
  const newNameGroup = document.getElementById("newNameGroup");
  const newMemberName = document.getElementById("newMemberName");
  const editTriggers = document.querySelectorAll(".btn-edit-trigger");
  const closeBtns = document.querySelectorAll(".close-modal");

  // 2. CHỨC NĂNG MỞ MODAL THÊM
  if (btnOpenAdd) {
    btnOpenAdd.onclick = function () {
      addModal.style.display = "flex";
    };
  }

  // 3. CHỨC NĂNG MỞ MODAL SỬA & ĐỔ DỮ LIỆU
  editTriggers.forEach((trigger) => {
    trigger.onclick = function () {
      // Lấy dữ liệu từ thuộc tính data-*
      const data = {
        id: this.getAttribute("data-id"),
        memberId: this.getAttribute("data-memberid"),
        type: this.getAttribute("data-type"),
        amount: this.getAttribute("data-amount"),
        method: this.getAttribute("data-method"),
        endDate: this.getAttribute("data-enddate"),
      };

      // Đổ dữ liệu vào Form Sửa
      document.getElementById("display_edit_id").innerText = data.id;
      document.getElementById("edit_trans_id").value = data.id;
      document.getElementById("edit_member_id").value = data.memberId;
      document.getElementById("edit_amount").value = data.amount;
      document.getElementById("edit_type").value = data.type;
      document.getElementById("edit_method").value = data.method;
      document.getElementById("edit_end_date").value = data.endDate;

      // Hiển thị modal sửa
      editModal.style.display = "flex";
    };
  });

  // 4. CHỨC NĂNG THOÁT (Dùng chung cho cả 2 Modal)

  // Đóng bằng nút X hoặc nút Hủy
  closeBtns.forEach((btn) => {
    btn.onclick = function () {
      addModal.style.display = "none";
      editModal.style.display = "none";
    };
  });

  // Đóng khi bấm ra ngoài vùng xám (Overlay)
  window.onclick = function (event) {
    if (event.target == addModal) {
      addModal.style.display = "none";
    }
    if (event.target == editModal) {
      editModal.style.display = "none";
    }
  };

  // Đóng khi nhấn phím ESC
  document.onkeydown = function (event) {
    if (event.key === "Escape") {
      addModal.style.display = "none";
      editModal.style.display = "none";
    }
  };

  // 5. XỬ LÝ HIỆN/ẨN Ô NHẬP TÊN (Khi thêm mới)
  if (memberSelect) {
    memberSelect.onchange = function () {
      if (this.value === "new_member") {
        newNameGroup.style.display = "block";
        newMemberName.setAttribute("required", "required");
        newMemberName.focus();
      } else {
        newNameGroup.style.display = "none";
        newMemberName.removeAttribute("required");
        newMemberName.value = "";
      }
    };
  }
});

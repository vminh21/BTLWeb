function openAddModal() {
  document.getElementById("modalTitle").innerText = "Thêm Hội Viên Mới";
  document.getElementById("form_id").value = "";
  document.getElementById("form_name").value = "";
  document.getElementById("form_phone").value = "";
  document.getElementById("form_address").value = "";
  document.getElementById("form_status").value = "Active";
  document.getElementById("form_package").value = "";
  document.getElementById("form_end").value = "";
  document.getElementById("package_section").style.display = "block";
  document.getElementById("memberModal").style.display = "flex";
}

function openEditModal(data) {
  document.getElementById("modalTitle").innerText =
    "Chỉnh sửa hội viên #" + data.member_id;
  document.getElementById("form_id").value = data.member_id;
  document.getElementById("form_name").value = data.full_name;
  document.getElementById("form_phone").value = data.phone_number || "";
  document.getElementById("form_address").value = data.address || "";
  document.getElementById("form_status").value = data.m_status;
  document.getElementById("package_section").style.display = "none";
  document.getElementById("memberModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("memberModal").style.display = "none";
}

function handleOverlayClick(event) {
  if (event.target.id === "memberModal") closeModal();
}

function calculateExpiry() {
  const pkg = document.getElementById("form_package");
  const start = document.getElementById("form_start");
  const end = document.getElementById("form_end");
  const months = parseInt(
    pkg.options[pkg.selectedIndex].getAttribute("data-months")
  );

  if (months && start.value) {
    let d = new Date(start.value);
    d.setMonth(d.getMonth() + months);
    end.value = d.toISOString().split("T")[0];
  } else {
    end.value = "";
  }
}

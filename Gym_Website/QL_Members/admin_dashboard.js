let currentMemberExpiry = null;

document.addEventListener("DOMContentLoaded", function () {
  const addModal = document.getElementById("addTransactionModal");
  const editModal = document.getElementById("editTransactionModal");
  const btnOpenAdd = document.getElementById("btnOpenModal");
  const memberSelect = document.getElementById("memberSelect");
  const newNameGroup = document.getElementById("newNameGroup");
  const newMemberName = document.getElementById("newMemberName");
  const addType = document.getElementById("add_transaction_type");

  if (memberSelect) {
    memberSelect.addEventListener("change", function () {
      if (this.value === "new_member") {
        newNameGroup.style.display = "block";
        newMemberName.setAttribute("required", "required");
        if (addType) addType.value = "Registration";
        currentMemberExpiry = null;
      } else {
        newNameGroup.style.display = "none";
        newMemberName.removeAttribute("required");
        newMemberName.value = "";

        const selectedMemberId = this.value;
        if (selectedMemberId) {
          fetch(`get_member_expiry.php?member_id=${selectedMemberId}`)
            .then((res) => res.json())
            .then((data) => {
              currentMemberExpiry = data.end_date;
              calculateExpiry("add");
            });
        }
      }
      calculateExpiry("add");
    });
  }

  document.addEventListener("click", function (e) {
    const btn = e.target.closest(".btn-edit-trigger");
    if (btn) {
      e.preventDefault();
      const id = btn.getAttribute("data-id");
      const memberId = btn.getAttribute("data-memberid");
      const type = btn.getAttribute("data-type");
      const amount = btn.getAttribute("data-amount");

      document.getElementById("display_edit_id").innerText = id;
      document.getElementById("edit_trans_id").value = id;
      document.getElementById("edit_member_id").value = memberId;
      document.getElementById("edit_type").value = type;
      document.getElementById("edit_amount_select").value = amount;

      fetch(`get_member_expiry.php?member_id=${memberId}`)
        .then((res) => res.json())
        .then((data) => {
          currentMemberExpiry = data.end_date;
          calculateExpiry("edit");
          editModal.style.display = "flex";
        })
        .catch((err) => {
          editModal.style.display = "flex";
        });
    }
  });

  const addAmount = document.getElementById("amountSelect");
  const editAmount = document.getElementById("edit_amount_select");
  const editType = document.getElementById("edit_type");

  if (addAmount) addAmount.onchange = () => calculateExpiry("add");
  if (addType) addType.onchange = () => calculateExpiry("add");
  if (editAmount) editAmount.onchange = () => calculateExpiry("edit");
  if (editType) editType.onchange = () => calculateExpiry("edit");

  if (btnOpenAdd) {
    btnOpenAdd.onclick = () => {
      if (memberSelect) memberSelect.value = "";
      if (newNameGroup) newNameGroup.style.display = "none";
      addModal.style.display = "flex";
    };
  }

  document.querySelectorAll(".close-modal").forEach((btn) => {
    btn.onclick = () => {
      addModal.style.display = "none";
      editModal.style.display = "none";
    };
  });

  window.onclick = (e) => {
    if (e.target == addModal) addModal.style.display = "none";
    if (e.target == editModal) editModal.style.display = "none";
  };
});

function calculateExpiry(mode) {
  const amountSelect =
    mode === "add"
      ? document.getElementById("amountSelect")
      : document.getElementById("edit_amount_select");
  const endDateInput =
    mode === "add"
      ? document.getElementById("add_end_date")
      : document.getElementById("edit_end_date");
  const typeSelect =
    mode === "add"
      ? document.getElementById("add_transaction_type")
      : document.getElementById("edit_type");

  if (!amountSelect || !endDateInput || !typeSelect) return;

  const transType = typeSelect.value;
  const selectedOption = amountSelect.options[amountSelect.selectedIndex];
  if (!selectedOption || !selectedOption.getAttribute("data-months")) {
    endDateInput.value = "";
    return;
  }

  const months = parseInt(selectedOption.getAttribute("data-months"));

  if (months) {
    let startDate = new Date();
    if (transType === "Renewal" && currentMemberExpiry) {
      let oldDate = new Date(currentMemberExpiry);
      if (oldDate > startDate) startDate = oldDate;
    }

    let resultDate = new Date(startDate);
    resultDate.setMonth(resultDate.getMonth() + months);

    const yyyy = resultDate.getFullYear();
    const mm = String(resultDate.getMonth() + 1).padStart(2, "0");
    const dd = String(resultDate.getDate()).padStart(2, "0");

    endDateInput.value = `${yyyy}-${mm}-${dd}`;
    endDateInput.style.backgroundColor =
      transType === "Renewal" ? "#d4edda" : "#e8f4fd";
  }
}

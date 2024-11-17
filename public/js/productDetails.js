document.getElementById("prod-edit-btn").addEventListener("click", function () {
  const imgLabel = document.querySelector(".image-container label");
  const disabledElems = document.querySelectorAll("*[disabled]");
  const categorySearch = document.getElementById("category-search");
  const categryChips = document.getElementById("category-chips");
  const actionBtns = document.querySelector(".action-btns");

  imgLabel.style.display = "flex";
  disabledElems.forEach((element) => {
    // Keep ID readonly
    if (element.id !== "prod-id") {
      element.disabled = false;
    }
  });
  categorySearch.style.display = "flex";
  for (const chip of categryChips.children) {
    chip.children[0].style.display = "flex";
  }
  actionBtns.style.display = "flex";
  this.style.display = "none";
});

document.getElementById("image-input").addEventListener("change", function () {
  const imgPreview = document.getElementById("prod-image");
  imgPreview.src = URL.createObjectURL(this.files[0]);
});

document
  .querySelector(".details-container .reset-btn")
  .addEventListener("click", function () {
    window.location.reload();
  });

document.querySelectorAll(".chip span").forEach((element) => {
  element.addEventListener("click", function () {
    element.parentElement.remove();
  });
});

function handleCategorySelect(element) {
  const id = element.dataset.id;
  const name = element.innerHTML;
  document.querySelector("#category-search input").value = "";
  const chips = document.getElementById("category-chips");
  for (const chip of chips.children) {
    if (chip.dataset.id === id) {
      return;
    }
  }

  const chip = document.createElement("div");
  chip.classList.add("chip");
  chip.dataset.id = id;
  chip.innerHTML = `${name} <span class="material-symbols-rounded">close</span>`;
  chip.querySelector("span").addEventListener("click", (e) => {
    chip.remove();
  });
  chips.appendChild(chip);
}

document
  .querySelector("#category-search input")
  .addEventListener("input", async (e) => {
    await autocomplete(
      "category-search",
      "/categories/search",
      handleCategorySelect
    );
  });

function openBatchEditModal(row) {
  const modal = document.getElementById("batch-edit-form-modal");
  modal.querySelector("input[name='id']").value = row.dataset.id;
  modal.querySelector("input[name='bno']").value = row.children[0].innerHTML;
  modal.querySelector("input[name='qty']").value = row.children[1].innerHTML;
  modal.querySelector("input[name='price']").value = row.children[2].innerHTML;
  modal.querySelector("input[name='mfd']").value = row.children[3].innerHTML;
  modal.querySelector("input[name='exp']").value = row.children[4].innerHTML;
  modal.showModal();
}

function addEditIconToRow(row) {
  const td = document.createElement("td");
  const editIcon = document.createElement("span");
  editIcon.classList.add("material-symbols-rounded");
  editIcon.innerHTML = "edit";
  editIcon.style.cursor = "pointer";
  editIcon.addEventListener("click", function () {
    openBatchEditModal(row);
  });
  td.appendChild(editIcon);
  row.appendChild(td);
}

const table = document.querySelector(".batches-container .tbl tbody");
let row = table.firstChild;
row.appendChild(document.createElement("th"));
row = row.nextSibling.nextSibling;
while (row) {
  addEditIconToRow(row);
  row = row.nextSibling.nextSibling;
}

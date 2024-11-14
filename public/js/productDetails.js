const originalValues = {
  "prod-image": document.getElementById("prod-image").src,
  "prod-name": document.getElementById("prod-name").value,
  "prod-desc": document.getElementById("prod-desc").value,
  "prod-unit": document.getElementById("prod-unit").value,
};

document.getElementById("edit-btn").addEventListener("click", function () {
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
    document.getElementById("prod-image").src = originalValues["prod-image"];

    for (const key in originalValues) {
      if (key !== "prod-image") {
        const elem = document.getElementById(key);
        elem.value = originalValues[key];
      }
    }
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

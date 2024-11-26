document.addEventListener("DOMContentLoaded", function () {
  // Initialize form open button
  document.getElementById("new-discount-btn").addEventListener("click", (e) => {
    document.getElementById("discount-form-modal").showModal();
  });

  // Initialize filters
  document.querySelectorAll("#filters div").forEach((element) => {
    element.onclick = (e) => {
      e.target.classList.toggle("filter-active");
    };
  });

  // Initialize form sections
  const form = {
    type: document.getElementById("discount-type"),
    valueSection: document.getElementById("value-section"),
    categorySection: document.getElementById("category-section"),
    bundleSection: document.getElementById("bundle-section"),
    valueInput: document.getElementById("discount-value"),
    isPercentage: document.getElementById("is-percentage"),
    form: document.getElementById("discount-form"),
  };

  // Handle discount type changes
  form.type.addEventListener("change", function () {
    // Hide all sections first
    hideAllSections();

    // Show relevant sections based on type
    switch (this.value) {
      case "product":
        form.bundleSection.style.display = "flex";
        form.valueSection.style.display = "flex";
        break;
      case "category":
        form.categorySection.style.display = "flex";
        form.valueSection.style.display = "flex";
        break;
      case "bill":
        form.valueSection.style.display = "flex";
        break;
      case "bundle":
        form.bundleSection.style.display = "flex";
        break;
    }
  });

  function hideAllSections() {
    form.bundleSection.style.display = "none";
    form.categorySection.style.display = "none";
    form.valueSection.style.display = "none";
  }

  // Handle percentage toggle
  form.isPercentage.addEventListener("change", function () {
    if (this.checked) {
      form.valueInput.max = "100";
      form.valueInput.step = "0.1";
    } else {
      form.valueInput.removeAttribute("max");
      form.valueInput.step = "0.01";
    }
  });

  // Category search autocomplete
  const categorySearch = document.querySelector("#category-search input");
  if (categorySearch) {
    categorySearch.addEventListener("input", async (e) => {
      await autocomplete(
        "category-search",
        "/categories/search",
        handleCategorySelect
      );
    });
  }

  // Product search autocomplete
  const productSearch = document.querySelector("#product-search input");
  if (productSearch) {
    productSearch.addEventListener("input", async (e) => {
      await autocomplete(
        "product-search",
        "/products/search",
        handleProductSelect
      );
    });
  }

  // Handle category selection
  function handleCategorySelect(element) {
    const id = element.dataset.id;
    const name = element.innerHTML;
    document.querySelector("#category-search input").value = "";
    const chips = document.getElementById("category-chips");

    // Check if category already added
    for (const chip of chips.children) {
      if (chip.dataset.id === id) return;
    }

    // Add new category chip
    const chip = document.createElement("div");
    chip.classList.add("chip");
    chip.dataset.id = id;
    chip.innerHTML = `${name} <span class="material-symbols-rounded">close</span>`;

    // Add remove handler
    chip.querySelector("span").addEventListener("click", () => {
      chip.remove();
    });

    chips.appendChild(chip);
  }

  // Handle product selection
  function addBundleProduct(id, name) {
    const bundlesTable = document.getElementById("bundles-table");

    bundlesTable.style.display = "block";
    const table = bundlesTable.querySelector("table");
    const row = table.insertRow(-1);

    row.insertCell(0).innerHTML = name;
    row.insertCell(1).innerHTML =
      document.getElementById("bundle-qty")?.value || "NULL";

    // Add remove button
    const removeCell = row.insertCell(2);
    removeCell.innerHTML =
      '<span class="material-symbols-rounded" style="cursor: pointer;">close</span>';
    removeCell.querySelector("span").addEventListener("click", () => {
      row.remove();
      if (table.rows.length === 1) {
        bundlesTable.style.display = "none";
      }
    });
  }

  // Handle product selection
  function handleProductSelect(element) {
    const id = element.dataset.id;
    const name = element.innerHTML;
    const newBundleProductEditor =
      document.getElementById("new-bundle-product");

    newBundleProductEditor.style.display = "";
    const closeBtn = document.getElementById("bundle-product-close");
    closeBtn.onclick = () => {
      newBundleProductEditor.style.display = "none";
      newBundleProductEditor.querySelectorAll("input").forEach((input) => {
        input.value = "";
      });
    };

    const doneBtn = document.getElementById("bundle-product-done");
    doneBtn.onclick = () => {
      newBundleProductEditor.style.display = "none";
      addBundleProduct(id, name);
      newBundleProductEditor.querySelectorAll("input").forEach((input) => {
        input.value = "";
      });
    };
  }
});

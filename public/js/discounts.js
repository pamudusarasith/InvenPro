document.getElementById("new-discount-btn").addEventListener("click", (e) => {
  document.getElementById("discount-form-modal").showModal();
});

document.querySelectorAll("#filters div").forEach((element) => {
  element.onclick = (e) => {
    e.target.classList.toggle("filter-active");
  };
});

const discForm = {
  type: document.getElementById("disc-type"),
  minBillAmountChkbx: document.getElementById("disc-min-bill-amount-chkbx"),
  maxDiscAmountChkbx: document.getElementById("disc-max-disc-amount-chkbx"),
  amountType: document.getElementById("disc-amount-type"),
  amount: document.getElementById("disc-amount"),
};

window.addEventListener("load", () => {
  discForm.minBillAmountChkbx.dispatchEvent(new Event("change"));
  discForm.maxDiscAmountChkbx.dispatchEvent(new Event("change"));
  discForm.type.dispatchEvent(new Event("change"));
  discForm.amountType.dispatchEvent(new Event("change"));
});

discForm.type.addEventListener("change", (e) => {
  const select = e.target;
  const type = select.options[select.selectedIndex].text;

  document.querySelectorAll(".condition").forEach((element) => {
    element.style.display = "none";
  });

  switch (type) {
    case "Bill Value Discount":
      document.getElementById("cond-amount").style.display = "flex";
      break;
    case "Product Category Discount":
      document.getElementById("cond-category").style.display = "flex";
      break;
    case "Product Discount":
      document.getElementById("cond-trigs").style.display = "flex";
      break;
    default:
      break;
  }
});

discForm.amountType.addEventListener("change", (e) => {
  const type = e.target.value;
  const amount = document.getElementById("disc-amount");
  if (type === "percentage") {
    amount.placeholder = "Percentage";
    amount.max = 100;
  } else {
    amount.placeholder = "Amount";
    amount.max = null;
  }
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

function addTrigger(id, name) {
  const trigs = document.getElementById("trigs-table");
  trigs.style.display = "table";
  const table = trigs.querySelector("table");
  const row = table.insertRow(-1);
  row.insertCell(0).innerHTML = name;
  row.insertCell(1).innerHTML =
    document.getElementById("trig-min-qty").value;
  row.insertCell(2).innerHTML =
    document.getElementById("trig-max-qty").value;
  row.insertCell(
    3
  ).innerHTML = `<span class="material-symbols-rounded" style="cursor: pointer;">close</span>`;
  row.cells[3].querySelector("span").addEventListener("click", (e) => {
    row.remove();
    if (table.rows.length === 1) {
      trigs.style.display = "none";
    }
  });
}

function handleProductSelect(element) {
  const id = element.dataset.id;
  const name = element.innerHTML;
  const trigEditor = document.getElementById("new-trig");
  trigEditor.style.display = "block";
  const closeBtn = document.getElementById("trig-close");
  closeBtn.onclick = (e) => {
    trigEditor.style.display = "none";
    trigEditor.querySelectorAll("input").forEach((input) => {
      input.value = "";
    });
  };
  const doneBtn = document.getElementById("trig-done");
  doneBtn.onclick = (e) => {
    trigEditor.style.display = "none";
    addTrigger(id, name);
    trigEditor.querySelectorAll("input").forEach((input) => {
      input.value = "";
    });
  };
}

document
  .querySelector("#prod-search input")
  .addEventListener("input", async (e) => {
    await autocomplete("prod-search", "/products/search", handleProductSelect);
  });

discForm.minBillAmountChkbx.addEventListener("change", (e) => {
  if (e.target.checked) {
    document.querySelector("label[for='disc-min-bill-amount']").style.display =
      "block";
    document.getElementById("disc-min-bill-amount").style.display = "block";
    document.getElementById("disc-min-bill-amount").focus();
  } else {
    document.querySelector("label[for='disc-min-bill-amount']").style.display =
      "none";
    document.getElementById("disc-min-bill-amount").style.display = "none";
  }
});

discForm.maxDiscAmountChkbx.addEventListener("change", (e) => {
  if (e.target.checked) {
    document.querySelector("label[for='disc-max-disc-amount']").style.display =
      "block";
    document.getElementById("disc-max-disc-amount").style.display = "block";
    document.getElementById("disc-max-disc-amount").focus();
  } else {
    document.querySelector("label[for='disc-max-disc-amount']").style.display =
      "none";
    document.getElementById("disc-max-disc-amount").style.display = "none";
  }
});

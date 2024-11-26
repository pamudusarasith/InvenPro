document.getElementById("availability").onclick = (e) => {
  document.getElementById("branch-form-modal").showModal();
};

document.getElementById("branch-form").onsubmit = async (e) => {
  e.preventDefault();
  const branchTable = document.getElementById("branch-table");
  branchTable.style.display = "block";
};
document.querySelector(".modal-close-btn").onclick = (e) => {
  document.getElementById("branch-form-modal").close();
  const branchTable = document.getElementById("branch-table");
  branchTable.style.display = "none";
};

//add customer form
document.getElementById("add-customer").onclick = (e) => {
  document.getElementById("customer-form-modal").showModal();
};

async function getCustomerFormData() {
  const form = document.getElementById("customer-form");

  const formData = {
    name: form.querySelector("input[name=name]").value,
    email: form.querySelector("input[name=email]").value,
    phone: form.querySelector("input[name=phone]").value,
    address: form.querySelector("textarea[name=address]").value,
    dob: form.querySelector("input[name=dob]").value,
    gender: form.querySelector("select[name=gender]").value,
  };

  return formData;
}

function validateCustomerFormData(formdata) {
  return { isValid: true };
}

async function submitCustomerForm() {
  const form = document.getElementById("customer-form");
  const loader = form.querySelector(".loader");
  const error = form.querySelector("#error-msg");

  const formData = await getCustomerFormData();

  const validationResult = validateCustomerFormData(formData);

  if (!validationResult.isValid) {
    error.parentElement.classList.add("show");
    error.innerHTML = validationResult.error;
    return;
  }

  loader.classList.add("show");

  const response = await fetch(form.action, {
    method: form.method,
    body: JSON.stringify(formData),
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();
  if (data.success) {
    window.location.reload();
  } else {
    loader.classList.remove("show");
    error.parentElement.classList.add("show");
    error.innerHTML = data.error;
  }
}

document
  .getElementById("customer-form")
  .addEventListener("submit", async (e) => {
    e.preventDefault();
    await submitCustomerForm();
  });

function handleProductSelect(element, data) {
  const form = document.getElementById("item-form");
  form.dataset.id = data.id;
  form.dataset.name = data.name;
  form.dataset.price = data.batches[0].price;
  document.getElementById("item-form-modal").showModal();
}

function handleProduct(element, data) {
  const id = element.dataset.id;
  window.location.href = `/product?id=${id}`;
}

document
  .querySelector("#prod-search input")
  .addEventListener("input", async (e) => {
    await autocomplete(
      "prod-search",
      "/pos/search",
      handleProductSelect,
      handleProduct
    );
  });

document.querySelector("#prod-search input").onkeydown = async (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    if (!e.target.value) return;
    const response = await fetch(`/pos/search?q=${e.target.value}`);
    const data = await response.json();
    e.target.value = "";
    e.target.dispatchEvent(new Event("input", { bubbles: true }));

    const resultsContainer = document.querySelector(".items-results");
    resultsContainer.innerHTML = "";

    data.data.results.forEach((item) => {
      const div = document.createElement("div");
      div.classList.add("item-card");
      div.innerHTML = `
           <img id="item-img" src=${item.image} alt="">
           <div class="item-details">
                <p>${item.id}</p>
                <h4><b>${item.name}</b></h4>
           </div>`;
      div.addEventListener("click", (e) => {
        handleProductSelect(div, item);
      });
      resultsContainer.appendChild(div);
    });
  }
};

document.getElementById("item-form").onsubmit = (e) => {
  e.preventDefault();
  const tableContainer = document.getElementById("items-table");
  const table = tableContainer.querySelector("table");
  let tbody = table.querySelector("tbody");
  if (!tbody) {
    tbody = document.createElement("tbody");
    table.appendChild(tbody);
  }

  const row = tbody.insertRow(-1);
  row.insertCell(0).innerText = e.target.dataset.name;
  row.insertCell(1).innerText = e.target.dataset.price;
  row.insertCell(2).innerText = e.target.querySelector("#item-qty").value;
  row.insertCell(3).innerText = (
    parseFloat(row.cells[1].innerText) * parseFloat(row.cells[2].innerText)
  ).toFixed(2);
  row.insertCell(4).innerHTML =
    '<span class="material-symbols-rounded">delete</span>';
  document.getElementById("item-form-modal").close();
  tableContainer.style.display = "block";
  e.target.querySelector("#item-qty").value = "";
  document.querySelector(".No-items").style.display = "none";
  row.cells[4].children[0].addEventListener("click", (e) => {
    row.remove();
    if (tbody.children.length === 0) {
      tableContainer.style.display = "none";
      document.querySelector(".No-items").style.display = "block";
    }
  });

  document.querySelector(".checkoutbtn").addEventListener("click", (e) => {
    tbody.remove();
    tableContainer.style.display = "none";
    document.querySelector(".No-items").style.display = "block";
  });

  let total = 0;
  console.log(tbody.children);
  for (let row of tbody.children) {
    total += parseFloat(row.cells[3].innerText);
    console.log(total);
  }
  document.querySelector(".item-total-value").innerText = "Rs. " + total;
};

//update-customer-details
document.getElementById("edit-customer-profile").onclick = (e) => {
  document.getElementById("customerProfile-modal").close();
  const profileInfo = document.querySelector(".profile-info");
  const name = profileInfo
    .querySelector("p:nth-child(1)")
    .textContent.split(":")[1]
    .trim();
  const email = profileInfo
    .querySelector("p:nth-child(2)")
    .textContent.split(":")[1]
    .trim();
  const phone = profileInfo
    .querySelector("p:nth-child(3)")
    .textContent.split(":")[1]
    .trim();
  const address = profileInfo
    .querySelector("p:nth-child(4)")
    .textContent.split(":")[1]
    .trim();
  const dob = new Date(
    Date.parse(
      profileInfo
        .querySelector("p:nth-child(5)")
        .textContent.split(":")[1]
        .trim()
    )
  )
    .toISOString()
    .split("T")[0];
  const gender = profileInfo
    .querySelector("p:nth-child(6)")
    .textContent.split(":")[1]
    .trim();

  document.getElementById("edit-customer-form-modal").showModal();
  document
    .getElementById("edit-customer-form")
    .querySelector("input[name='name']").value = name;
  document
    .getElementById("edit-customer-form")
    .querySelector("input[name='email']").value = email;
  document
    .getElementById("edit-customer-form")
    .querySelector("input[name='phone']").value = phone;
  document
    .getElementById("edit-customer-form")
    .querySelector("textarea[name='address']").value = address;
  document
    .getElementById("edit-customer-form")
    .querySelector("input[name='dob']").value = dob;
  document
    .getElementById("edit-customer-form")
    .querySelector("select[name='gender']").value = gender;
};

document
  .getElementById("edit-customer-form")
  .addEventListener("submit", function (event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch("/customer/update", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Customer updated successfully");
          location.reload();
        } else {
          document.getElementById("error-msg").textContent = data.message;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  });

document
  .getElementById("add-phone-no-button")
  .addEventListener("click", function (event) {
    event.preventDefault();
    const phone = event.target.parentNode.querySelector("input").value;
    const formData = new FormData();
    formData.append("phone", phone);
    fetch("/customer/retrieve", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          console.log(data);
        } else {
          console.log(data);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  });

//Item Return form
document.getElementById("returns").onclick = (e) => {
  document.getElementById("ItemReturn-form-modal").showModal();
};

document.getElementById("ItemReturn-form").onsubmit = async (e) => {
  e.preventDefault();
  document.getElementById("ItemReturn-form-modal").close();
  document.getElementById("Authorization-form-modal").showModal();
};

//customer profile
document.getElementById("customer-profile").onclick = (e) => {
  document.getElementById("customerProfile-modal").showModal();
};

//customer profile
document.getElementById("add-phone-no-button").onclick = (e) => {
  document.getElementById("customerProfile-modal").showModal();
};

document
  .querySelector(".btn-secondary.delete-customer")
  .addEventListener("click", async () => {
    const phoneNumber = document
      .querySelector(".profile-info p:nth-child(3)")
      .textContent.split(":")[1]
      .trim();
    const confirmDelete = confirm(
      "Are you sure you want to delete this customer?"
    );
    if (confirmDelete) {
      try {
        const response = await fetch(`/customer/delete`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ phone: phoneNumber }),
        });

        const result = await response.json();
        if (result.success) {
          alert("Customer deleted successfully!");
          document.getElementById("customerProfile-modal").close();
          window.location.reload();
        } else {
          alert("Failed to delete the customer.");
        }
      } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while deleting the customer.");
      }
    }
  });

//add customer form
document.getElementById("new-customer").onclick = (e) => {
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

document.addEventListener("DOMContentLoaded", async (e) => {
  const response = await fetch(`/pos/search?q=`);
  const data = await response.json();

  const resultsContainer = document.querySelector(".items-results");
  resultsContainer.innerHTML = "";

  data.data.results.slice(0, 8).forEach((item) => {
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
});

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

document.getElementById("add-customer").addEventListener("click", function () {
  document.querySelector(".customer-details").style.display = "block";
});

document.getElementById("phone").addEventListener("input", function (e) {
  let value = e.target.value;
  if (!/^\d*$/.test(value)) {
    document.getElementById("phone-error").style.display = "block";
    document.getElementById("phone-error").textContent =
      "Please enter numbers only";
    e.target.value = value.replace(/\D/g, "");
  } else {
    document.getElementById("phone-error").style.display = "none";
  }
});

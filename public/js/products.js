document.querySelectorAll("#filters div").forEach((element) => {
  element.onclick = (e) => {
    e.target.classList.toggle("filter-active");
  };
});

document.querySelectorAll(".collapsible").forEach((element) => {
  element.onclick = (e) => {
    e.target.nextElementSibling.classList.toggle("show");
  };
});

document
  .querySelectorAll(".tbl tr[data-id]:not([data-id=''])")
  .forEach((row) => {
    row.addEventListener("click", function () {
      const productId = this.getAttribute("data-id");
      window.location.href = `/product?id=${productId}`;
    });
  });

document.getElementById("new-category-btn").onclick = (e) => {
  document.getElementById("category-form-modal").showModal();
};

document.getElementById("new-prod-btn").onclick = (e) => {
  document.getElementById("prod-form-modal").showModal();
};

document.getElementById("new-batch-btn").onclick = (e) => {
  document.getElementById("batch-form-modal").showModal();
};

async function getCategoryFormData() {
  const form = document.getElementById("category-form");

  const formData = {
    name: form.querySelector("input[name=name]").value,
  };

  return formData;
}

async function getProductFormData() {
  const form = document.getElementById("prod-form");

  const formData = {
    id: form.querySelector("input[name=id]").value,
    name: form.querySelector("input[name=name]").value,
    description: form.querySelector("textarea[name=description]").value,
    unit: form.querySelector("select[name=unit]").value,
    categories: new Array(),
    image: form.querySelector("input[name=image]").files[0],
  };

  form.querySelectorAll("#category-chips .chip").forEach((chip) => {
    formData.categories.push(parseInt(chip.dataset.id));
  });

  const reader = new FileReader();
  reader.readAsDataURL(formData.image);
  const image = new Promise((resolve, reject) => {
    reader.onloadend = () => resolve(reader.result);
    reader.onerror = reject;
  });
  formData.image = (await image).split(",")[1];

  return formData;
}

async function getBatchFormData() {
  const form = document.getElementById("batch-form");

  const formData = {
    id: form.querySelector("input[name=id]").value,
    bno: form.querySelector("input[name=bno]").value,
    price: form.querySelector("input[name=price]").value,
    qty: form.querySelector("input[name=qty]").value,
    mfd: form.querySelector("input[name=mfd]").value,
    exp: form.querySelector("input[name=exp]").value,
  };

  return formData;
}

function validateCategoryFormData(formData) {
  // Add any additional validation logic here if needed
  return { isValid: true };
}

function validateProductFormData(formData) {
  // Add any additional validation logic here if needed
  return { isValid: true };
}

function validateBatchFormData(formData) {
  const mfd = new Date(formData.mfd);
  const exp = new Date(formData.exp);

  if (exp <= mfd) {
    return {
      isValid: false,
      error: "Expiration date must be greater than manufacturing date.",
    };
  }

  return { isValid: true };
}

const validators = {
  category: validateCategoryFormData,
  prod: validateProductFormData,
  batch: validateBatchFormData,
};

const formDataGetters = {
  category: getCategoryFormData,
  prod: getProductFormData,
  batch: getBatchFormData,
};

async function submitForm(formName) {
  const form = document.getElementById(`${formName}-form`);
  const loader = form.querySelector(".loader");
  const error = form.querySelector("#error-msg");

  const formData = await formDataGetters[formName]();

  const validationResult = validators[formName](formData);

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

const forms = ["category", "prod", "batch"];

forms.forEach((formName) => {
  document
    .getElementById(`${formName}-form`)
    .addEventListener("submit", async (e) => {
      e.preventDefault();
      await submitForm(formName);
    });
});

function handleProductSelect(element, data) {
  const id = element.dataset.id;
  window.location.href = `/product?id=${id}`;
}

document
  .querySelector("#prod-search input")
  .addEventListener("input", async (e) => {
    await autocomplete("prod-search", "/products/search", handleProductSelect);
  });

function handleCategorySelect(element, data) {
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

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

document.querySelectorAll(".products-tbl .tbl-r[data-id]").forEach((row) => {
  row.addEventListener("click", function () {
    const productId = this.getAttribute("data-id");
    window.location.href = `/product?id=${productId}`;
  });
});

document.getElementById("new-category-btn").onclick = (e) => {
  document.getElementById("category-form-modal").classList.toggle("show");
};

document.getElementById("new-prod-btn").onclick = (e) => {
  document.getElementById("prod-form-modal").classList.toggle("show");
};

document.getElementById("new-batch-btn").onclick = (e) => {
  document.getElementById("batch-form-modal").classList.toggle("show");
};

document
  .querySelectorAll(".modal-action-btns .cancel-btn")
  .forEach((element) => {
    element.addEventListener("click", (e) => {
      let element = e.target;
      while (!element.classList.contains("show")) {
        element = element.parentElement;
      }
      element.classList.toggle("show");
    });
  });

function validateCategoryFormData(formData) {
  // Add any additional validation logic here if needed
  return { isValid: true };
}

function validateProductFormData(formData) {
  // Add any additional validation logic here if needed
  return { isValid: true };
}

function validateBatchFormData(formData) {
  const mfd = new Date(formData.get("mfd"));
  const exp = new Date(formData.get("exp"));

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

async function submitForm(formName) {
  const loader = document.querySelector(`#${formName}-form .loader`);
  const error = document.querySelector(`#${formName}-form #error-msg`);
  const form = document.getElementById(`${formName}-form`);
  const formData = new FormData(form);

  const reader = new FileReader();
  reader.readAsDataURL(formData.get("image"));
  const image = new Promise((resolve, reject) => {
    reader.onloadend = () => resolve(reader.result);
    reader.onerror = reject;
  });
  formData.set("image", (await image).split(",")[1]);

  const validationResult = validators[formName](formData);

  if (!validationResult.isValid) {
    error.parentElement.classList.add("show");
    error.innerHTML = validationResult.error;
    return;
  }

  loader.classList.add("show");

  const response = await fetch(form.action, {
    method: form.method,
    body: JSON.stringify(Object.fromEntries(formData)),
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

document
  .querySelector("#prod-search input")
  .addEventListener("input", async (e) => {
    await autocomplete("prod-search");
  });

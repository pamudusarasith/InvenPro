document.getElementById("new-discount-btn").onclick = (e) => {
  document.getElementById("discount-form-modal").classList.toggle("show");
};

document.querySelectorAll(".close-btn").forEach((element) => {
  element.onclick = (e) => {
    let element = e.target;
    while (!element.classList.contains("show")) {
      element = element.parentElement;
    }
    element.classList.toggle("show");
  };
});

document.querySelector(".action-btns #close").onclick = (e) => {
  let element = e.target;
  while (!element.classList.contains("show")) {
    element = element.parentElement;
  }
  element.classList.toggle("show");
};

document.querySelectorAll("#filters div").forEach((element) => {
  element.onclick = (e) => {
    e.target.classList.toggle("filter-active");
  };
});

function handleProductSelect(element) {
  const id = element.dataset.id;
  window.location.href = `/product?id=${id}`;
}

document
  .querySelector("#prod-search input")
  .addEventListener("input", async (e) => {
    await autocomplete("prod-search", "/products/search", handleProductSelect);
  });

window.onclick = (e) => {
  document.querySelectorAll(".navbar .dropdown").forEach((element) => {
    if (!element.contains(e.target)) {
      element.querySelector(".dd-content").classList.remove("show");
    }
  });
};

function toggleDropdown(id) {
  let content = document.querySelector(`#${id} .dd-content`);
  content.classList.toggle("show");
}

function logout() {
  window.location.href = "/logout";
}

document.querySelectorAll(".modal-close-btn").forEach((element) => {
  element.onclick = (e) => {
    let element = e.target;
    while (!element.classList.contains("show")) {
      element = element.parentElement;
    }
    element.classList.toggle("show");
  };
});

document.querySelectorAll(".modal-cancel-btn").forEach((element) => {
  element.addEventListener("click", (e) => {
    let element = e.target;
    while (!element.classList.contains("show")) {
      element = element.parentElement;
    }
    element.classList.toggle("show");
  });
});

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

document.querySelectorAll(".modal-close").forEach((element) => {
  element.addEventListener("click", (e) => {
    let elem = e.target;
    while (elem.tagName !== "DIALOG") {
      elem = elem.parentElement;
    }
    elem.close();
  });
});

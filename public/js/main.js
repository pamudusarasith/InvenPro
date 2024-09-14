window.onclick = (e) => {
  document.querySelectorAll(".navbar .dropdown").forEach((element) => {
    if (!element.contains(e.target)) {
      element.querySelector(".dd-content").classList.remove("dd-show");
    }
  });
};

function toggleDropdown(id) {
  let content = document.querySelector(`#${id} .dd-content`);
  content.classList.toggle("dd-show");
}

function logout() {
  window.location.href = "/logout";
}

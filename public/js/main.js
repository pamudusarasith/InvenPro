window.onclick = (e) => {
  document.querySelectorAll(".navbar-dropdown").forEach((element) => {
    if (!element.contains(e.target)) {
      let content = element.querySelector(".dropdown-content");
      if (content.classList.contains("show")) content.classList.remove("show");
    }
  });
};

function toggleDropdown(id) {
  element = document.getElementById(id).querySelector(".dropdown-content");
  if (element.classList.contains("show")) element.classList.remove("show");
  else element.classList.toggle("show");
}

function logout() {
  window.location.href = "/logout";
}

window.onclick = (e) => {
  document.querySelectorAll(".navbar .dropdown").forEach((element) => {
    if (!element.contains(e.target)) {
      element.querySelector(".dd-content").classList.remove("show");
    }
  });
};

document.getElementById('profile-menu').addEventListener('click', function() {
  const ddContent = this.querySelector('.dd-content');
  ddContent.classList.toggle('show');
});

document.getElementById('notifications-btn').addEventListener('click', function() {
  const ddContent = this.querySelector('.dd-content');
  ddContent.classList.toggle('show');
});

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

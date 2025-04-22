class Dropdown {
  constructor(element) {
    this.dropdown = element;
    this.trigger = element.querySelector(".dropdown-trigger");
    this.menu = element.querySelector(".dropdown-menu");
    this.init();
  }

  init() {
    // Toggle dropdown
    this.trigger.addEventListener("click", (e) => {
      e.stopPropagation();
      this.toggle();
    });
  }

  toggle() {
    // Close other dropdowns
    document.querySelectorAll(".dropdown.active").forEach((d) => {
      if (d !== this.dropdown) {
        d.classList.remove("active");
      }
    });

    this.dropdown.classList.toggle("active");
  }

  close() {
    this.dropdown.classList.remove("active");
  }

  static init() {
    // Initialize all dropdowns
    const dropdowns = document.querySelectorAll(".dropdown");
    dropdowns.forEach((d) => new Dropdown(d));

    // Close all dropdowns when clicking outside
    document.addEventListener("click", () => {
      document.querySelectorAll(".dropdown.active").forEach((d) => {
        d.classList.remove("active");
      });
    });
  }
}

function openPopup() {
  const popup = document.getElementById("messagePopup");
  popup?.classList.add("show");
}

function openPopupWithMessage(message, type) {
  const popup = document.getElementById("messagePopup");
  const iconElem = popup.querySelector(".icon");
  const messageElem = popup.querySelector(".popup-message");

  popup?.setAttribute("class", "popup");
  switch (type) {
    case "success":
      popup?.classList.add("success");
      iconElem.innerHTML = "check_circle";
      break;
    case "warning":
      popup?.classList.add("warning");
      iconElem.innerHTML = "warning";
      break;
    default:
      popup?.classList.add("error");
      iconElem.innerHTML = "error";
      break;
  }
  messageElem.innerHTML = message;

  popup?.classList.add("show");
}

function closePopup() {
  const popup = document.getElementById("messagePopup");
  popup?.classList.remove("show");
}

function switchTab(tabId) {
  document
    .querySelectorAll(".tab-btn")
    .forEach((btn) => btn.classList.remove("active"));
  document
    .querySelectorAll(".tab-content")
    .forEach((content) => content.classList.remove("active"));

  document
    .querySelector(`.tab-btn[onclick*="${tabId}"]`)
    .classList.add("active");
  document.getElementById(tabId).classList.add("active");
}

// Initialize dropdowns when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  const popup = document.getElementById("messagePopup");
  if (popup && popup.querySelector(".popup-message")?.innerText !== "") {
    openPopup();
  }

  Dropdown.init();
});

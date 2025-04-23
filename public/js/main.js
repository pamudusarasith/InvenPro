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

function insertPagination(container, currentPage, totalPages, changeCallback) {
  let pages = new Set([
    1,
    totalPages,
    currentPage,
    Math.max(1, currentPage - 1),
    Math.min(totalPages, currentPage + 1),
  ]);
  pages = Array.from(pages).sort();

  container.innerHTML = "";
  const prevBtn = document.createElement("button");
  prevBtn.classList.add("page-btn");
  prevBtn.disabled = currentPage <= 1;
  prevBtn.innerHTML = "<span class='icon'>navigate_before</span>";
  prevBtn.onclick = () => changeCallback(currentPage - 1);
  container.appendChild(prevBtn);

  for (let i = 0; i < pages.length; i++) {
    if (i > 0 && pages[i] - pages[i - 1] > 1) {
      const dots = document.createElement("span");
      dots.classList.add("page-dots");
      dots.textContent = "...";
      container.appendChild(dots);
    }
    const pageBtn = document.createElement("button");
    pageBtn.classList.add("page-number");
    if (pages[i] === currentPage) {
      pageBtn.classList.add("active");
    }
    pageBtn.textContent = pages[i];
    pageBtn.onclick = () => changeCallback(pages[i]);
    container.appendChild(pageBtn);
  }

  const nextBtn = document.createElement("button");
  nextBtn.classList.add("page-btn");
  nextBtn.disabled = currentPage >= totalPages;
  nextBtn.innerHTML = "<span class='icon'>navigate_next</span>";
  nextBtn.onclick = () => changeCallback(currentPage + 1);
  container.appendChild(nextBtn);
}

function changeItemsPerPage(itemsPerPage) {
  const currentUrl = new URL(window.location.href);
  currentUrl.searchParams.set("ipp", itemsPerPage);
  currentUrl.searchParams.delete("p");
  window.location.href = currentUrl.href;
}

// Initialize dropdowns when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  const popup = document.getElementById("messagePopup");
  if (popup && popup.querySelector(".popup-message")?.innerText !== "") {
    openPopup();
  }

  Dropdown.init();
});

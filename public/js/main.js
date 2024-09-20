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

//pos

function selectCategory(category) {
  console.log("Selected Category: " + category);
}

document
  .getElementById("categorySearch")
  .addEventListener("input", function () {
    const searchQuery = this.value.toLowerCase();
    const categories = document.querySelectorAll(".category");

    categories.forEach(function (category) {
      const categoryName = category.getAttribute("data-category").toLowerCase();

      // Show or hide categories based on the search query
      if (categoryName.includes(searchQuery)) {
        category.style.display = "flex"; // Show matching category
      } else {
        category.style.display = "none"; // Hide non-matching category
      }
    });
  });

document.querySelector(".apply-btn").addEventListener("click", function () {
  const couponCode = document.getElementById("coupon-code").value;
  if (couponCode) {
    alert(`Coupon "${couponCode}" applied!`);
  } else {
    alert("Please enter a coupon code.");
  }
});

document.querySelector(".redeem-btn").addEventListener("click", function () {
  const loyaltyNumber = document.getElementById("loyalty-number").value;
  if (loyaltyNumber) {
    alert(`Loyalty points redeemed for number: ${loyaltyNumber}`);
  } else {
    alert("Please enter a loyalty number.");
  }
});

const addCustomerButton = document.querySelector(".add-customer");
const customerForm = document.getElementById("customerForm");

addCustomerButton.addEventListener("click", function () {
  customerForm.style.display = "block";
});

const form = document.getElementById("customerDetailsForm");
form.addEventListener("submit", function (e) {
  e.preventDefault();
  const customerDetails = {
    name: document.getElementById("customerName").value,
    phone: document.getElementById("phoneNumber").value,
    address: document.getElementById("address").value,
    id: document.getElementById("customerId").value,
  };
  console.log(customerDetails);

  customerForm.style.display = "none";
});

//pos

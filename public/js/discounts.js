const state = {
  conditionCount: 0,
  couponCount: 0,
  currentDiscountId: null,
};

// Document ready handler
document.addEventListener("DOMContentLoaded", function () {
  initStatusToggles();
  initEditFromDetailsButton();
});

/**
 * Initialize all status toggle switches in the UI
 */
function initStatusToggles() {
  // Add event delegation for toggle switches that may be added dynamically
  document.addEventListener("change", function (e) {
    if (
      e.target.type === "checkbox" &&
      e.target.id.startsWith("coupon_active_")
    ) {
      const toggleLabel = e.target.parentElement.querySelector(".toggle-label");
      if (toggleLabel) {
        toggleLabel.textContent = e.target.checked ? "Active" : "Inactive";
      }
    }
  });
}

/**
 * Initialize the Edit button in the discount details dialog
 */
function initEditFromDetailsButton() {
  const editFromDetailsBtn = document.getElementById("edit-from-details-btn");
  if (editFromDetailsBtn) {
    editFromDetailsBtn.addEventListener("click", function () {
      closeDiscountDetailsDialog();
      if (state.currentDiscountId) {
        editDiscount(state.currentDiscountId);
      }
    });
  }
}

/**
 * Toggle coupon section visibility based on application method
 */
function toggleCouponSection(select) {
  const applicationMethod = select.value;
  const couponsSection = document.getElementById("couponsSection");

  if (couponsSection) {
    if (applicationMethod === "coupon") {
      couponsSection.style.display = "flex";
      // If no coupons exist, add one by default
      if (document.querySelectorAll(".coupon-item").length === 0) {
        addCoupon();
      }
    } else {
      couponsSection.style.display = "none";
    }
  }
}

/**
 * Filter discounts based on search and filter criteria
 */
function filterItems() {
  const searchInput =
    document.getElementById("searchInput")?.value.toLowerCase() || "";
  const statusFilterElem = document.querySelector(
    ".filters select:nth-child(1)"
  );
  const typeFilterElem = document.querySelector(".filters select:nth-child(2)");
  const methodFilterElem = document.querySelector(
    ".filters select:nth-child(3)"
  );

  const statusFilter = statusFilterElem?.value || "";
  const typeFilter = typeFilterElem?.value || "";
  const methodFilter = methodFilterElem?.value || "";
  const fromDate = document.getElementById("fromDate")?.value || "";
  const toDate = document.getElementById("toDate")?.value || "";

  // Get all discount cards
  const discountCards = document.querySelectorAll(".discount-card");

  discountCards.forEach((card) => {
    let shouldShow = true;

    // Here you would implement the actual filtering logic
    // For now just log the filter parameters
    console.log("Filtering with:", {
      searchInput,
      statusFilter,
      typeFilter,
      methodFilter,
      fromDate,
      toDate,
    });

    // For demonstration purposes, just do a simple name search
    if (searchInput) {
      const discountName =
        card.querySelector("h3")?.textContent.toLowerCase() || "";
      const discountDesc =
        card
          .querySelector(".discount-description")
          ?.textContent.toLowerCase() || "";
      if (
        !discountName.includes(searchInput) &&
        !discountDesc.includes(searchInput)
      ) {
        shouldShow = false;
      }
    }

    // Apply status filter
    if (statusFilter === "active" && !card.querySelector(".badge.success")) {
      shouldShow = false;
    } else if (
      statusFilter === "inactive" &&
      card.querySelector(".badge.success")
    ) {
      shouldShow = false;
    }

    // Show or hide the card
    card.style.display = shouldShow ? "" : "none";
  });
}

/**
 * Open the create discount dialog
 */
function openCreateDiscountDialog() {
  const dialogTitle = document.querySelector(
    "#discountDialog .modal-header h2"
  );
  if (dialogTitle) {
    dialogTitle.textContent = "New Discount";
  }

  const discountForm = document.getElementById("discountForm");
  if (discountForm) {
    discountForm.reset();
  }

  // Set form action for creating a new discount
  discountForm.action = "/discounts/new";

  // Clear existing conditions and coupons
  const conditionsContainer = document.getElementById("conditionsContainer");
  const couponsContainer = document.getElementById("couponsContainer");

  if (conditionsContainer) {
    conditionsContainer.innerHTML = "";
  }

  if (couponsContainer) {
    couponsContainer.innerHTML = "";
  }

  state.conditionCount = 0;
  state.couponCount = 0;

  // Set default start date to today
  const today = new Date();
  const startDateInput = document.getElementById("startDate");
  if (startDateInput) {
    startDateInput.value = formatDateForInput(today);
  }

  // Hide coupon section by default
  const couponsSection = document.getElementById("couponsSection");
  if (couponsSection) {
    couponsSection.style.display = "none";
  }

  const dialog = document.getElementById("discountDialog");
  if (dialog) {
    dialog.showModal();
  }
}

/**
 * Open the edit discount dialog
 * @param {number} id - The ID of the discount to edit
 */
function editDiscount(id) {
  const discountDialogTitle = document.querySelector(
    "#discountDialog .modal-header h2"
  );
  if (discountDialogTitle) {
    discountDialogTitle.textContent = "Edit Discount";
  }

  // Find discount by ID
  const discount = discounts.find((d) => d.id === id);
  if (!discount) return;

  // Populate form
  const discountForm = document.getElementById("discountForm");
  if (!discountForm) return;

  discountForm.action = `/discounts/${id}/update`;

  // Add hidden input for discount ID if it doesn't exist
  let discountIdInput = document.getElementById("discountId");
  if (!discountIdInput) {
    discountIdInput = document.createElement("input");
    discountIdInput.type = "hidden";
    discountIdInput.id = "discountId";
    discountIdInput.name = "id";
    discountForm.appendChild(discountIdInput);
  }

  // Set values
  discountIdInput.value = discount.id;
  setFormValue("discountName", discount.name);
  setFormValue("discountDescription", discount.description || "");
  setFormValue("discountType", discount.discount_type);
  setFormValue("discountValue", discount.value);
  setFormValue("applicationMethod", discount.application_method);

  // Set is_combinable checkbox
  const isCombinable = document.getElementById("isCombinable");
  if (isCombinable) {
    isCombinable.checked = discount.is_combinable == 1;
  }

  // Handle dates
  setFormValue("startDate", formatDateForInput(new Date(discount.start_date)));

  if (discount.end_date) {
    setFormValue("endDate", formatDateForInput(new Date(discount.end_date)));
  } else {
    setFormValue("endDate", "");
  }

  // Clear and rebuild conditions
  const conditionsContainer = document.getElementById("conditionsContainer");
  if (conditionsContainer) {
    conditionsContainer.innerHTML = "";
    state.conditionCount = 0;

    if (discount.conditions && discount.conditions.length > 0) {
      discount.conditions.forEach((condition) => {
        addCondition(condition);
      });
    }
  }

  // Clear and rebuild coupons if this is a coupon-based discount
  const couponsContainer = document.getElementById("couponsContainer");
  const couponsSection = document.getElementById("couponsSection");

  if (couponsContainer && couponsSection) {
    couponsContainer.innerHTML = "";
    state.couponCount = 0;

    if (discount.application_method === "coupon") {
      couponsSection.style.display = "flex";

      // Find all coupons for this discount
      const discountCoupons = discount.coupons || [];

      if (discountCoupons.length > 0) {
        discountCoupons.forEach((coupon) => {
          addCoupon(coupon);
        });
      } else {
        addCoupon(); // Add an empty one
      }
    } else {
      couponsSection.style.display = "none";
    }
  }

  const dialog = document.getElementById("discountDialog");
  if (dialog) {
    dialog.showModal();
  }
}

/**
 * Close the discount dialog
 */
function closeDiscountDialog() {
  const dialog = document.getElementById("discountDialog");
  if (dialog) {
    dialog.close();
  }
}

/**
 * View discount details
 * @param {number} id - The ID of the discount to view
 */
function viewDiscountDetails(id) {
  state.currentDiscountId = id;

  // Find discount by ID
  const discount = discounts.find((d) => d.id === id);
  if (!discount) return;

  // Update details in dialog
  setElementText("detail-name", discount.name);

  // Set status badge
  const statusBadge = discount.is_active
    ? '<span class="badge success">Active</span>'
    : '<span class="badge">Inactive</span>';

  const detailStatus = document.getElementById("detail-status");
  if (detailStatus) {
    detailStatus.innerHTML = statusBadge;
  }

  setElementText(
    "detail-description",
    discount.description || "No description provided"
  );
  setElementText(
    "detail-type",
    discount.discount_type === "percentage" ? "Percentage" : "Fixed Amount"
  );

  // Format the value
  const formattedValue =
    discount.discount_type === "percentage"
      ? `${discount.value}%`
      : `Rs. ${formatCurrency(discount.value)}`;

  setElementText("detail-value", formattedValue);
  setElementText(
    "detail-application",
    discount.application_method === "regular"
      ? "Regular (Automatic)"
      : "Coupon-based"
  );

  // Format dates
  const startDate = new Date(discount.start_date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });

  setElementText("detail-start-date", startDate);

  const endDateText = discount.end_date
    ? new Date(discount.end_date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      })
    : "No end date";

  setElementText("detail-end-date", endDateText);

  setElementText("detail-combinable", discount.is_combinable ? "Yes" : "No");

  // Handle coupons section
  const couponSection = document.getElementById("coupon-section");
  const couponList = document.getElementById("coupon-list");

  if (couponSection && couponList) {
    couponList.innerHTML = "";

    if (discount.application_method === "coupon") {
      // Find coupons for this discount
      const discountCoupons = discount.coupons || [];

      if (discountCoupons.length > 0) {
        discountCoupons.forEach((coupon) => {
          const couponItem = document.createElement("div");
          couponItem.className = "coupon-item-detail";
          couponItem.innerHTML = `
            <div class="coupon-code-detail">
              <span>${coupon.code}</span>
              <button class="icon-btn" title="Copy code" onclick="copyToClipboard('${
                coupon.code
              }')">
                <span class="icon">content_copy</span>
              </button>
            </div>
            <span class="badge ${coupon.is_active ? "success" : ""}">${
            coupon.is_active ? "Active" : "Inactive"
          }</span>
          `;
          couponList.appendChild(couponItem);
        });
      } else {
        couponList.innerHTML =
          '<p class="empty-state">No coupons created yet</p>';
      }

      couponSection.style.display = "block";
    } else {
      couponSection.style.display = "none";
    }
  }

  // Handle conditions section
  const conditionsSection = document.getElementById("conditions-section");
  const conditionsList = document.getElementById("conditions-list");

  if (conditionsSection && conditionsList) {
    conditionsList.innerHTML = "";

    if (discount.conditions && discount.conditions.length > 0) {
      discount.conditions.forEach((condition) => {
        const conditionValue = condition.condition_value;
        let conditionText = "";

        switch (condition.condition_type) {
          case "min_amount":
            conditionText = `Minimum purchase: Rs. ${formatCurrency(
              conditionValue.min_amount
            )}`;
            break;

          case "min_quantity":
            conditionText = `Minimum quantity: ${conditionValue.product_name} - ${conditionValue.min_quantity}`;
            break;

          case "loyalty_points":
            conditionText = `Minimum loyalty points: ${formatNumber(
              conditionValue.min_points
            )}`;
            break;

          case "day_of_week":
            const dayNames = conditionValue.days.map((day) => {
              const daysMap = {
                1: "Monday",
                2: "Tuesday",
                3: "Wednesday",
                4: "Thursday",
                5: "Friday",
                6: "Saturday",
                7: "Sunday",
              };
              return daysMap[day];
            });
            conditionText = `Valid days: ${dayNames.join(", ")}`;
            break;

          case "time_of_day":
            conditionText = `Valid time: ${conditionValue.start_time} - ${conditionValue.end_time}`;
            break;
        }

        const li = document.createElement("li");
        li.innerHTML = `<span class="condition-icon">rule</span>${conditionText}`;
        conditionsList.appendChild(li);
      });

      conditionsSection.style.display = "block";
    } else {
      conditionsSection.style.display = "none";
    }
  }

  // Show the dialog
  const dialog = document.getElementById("discountDetailsDialog");
  if (dialog) {
    dialog.showModal();
  }
}

/**
 * Close the discount details dialog
 */
function closeDiscountDetailsDialog() {
  const dialog = document.getElementById("discountDetailsDialog");
  if (dialog) {
    dialog.close();
  }
}

/**
 * Add a new coupon to the form
 * @param {Object|null} existingCoupon - Optional existing coupon data
 */
function addCoupon(existingCoupon = null) {
  const couponsContainer = document.getElementById("couponsContainer");
  if (!couponsContainer) return;

  const template = document.getElementById("couponTemplate");
  if (!template) return;

  const clone = template.content.cloneNode(true);

  // Replace INDEX placeholder with the actual index
  const couponIndex = state.couponCount++;

  // Update IDs and names
  const elements = clone.querySelectorAll("[id], [name], [for]");
  elements.forEach((element) => {
    if (element.hasAttribute("id")) {
      element.id = element.id.replace("INDEX", couponIndex);
    }
    if (element.hasAttribute("name")) {
      element.setAttribute(
        "name",
        element.getAttribute("name").replace("INDEX", couponIndex)
      );
    }
    if (element.hasAttribute("for")) {
      element.setAttribute(
        "for",
        element.getAttribute("for").replace("INDEX", couponIndex)
      );
    }
  });

  // Add the coupon to the container
  couponsContainer.appendChild(clone);

  // Get the last added coupon item
  const couponItems = couponsContainer.querySelectorAll(".coupon-item");
  const couponItem = couponItems[couponItems.length - 1];

  // If we have an existing coupon, populate the fields
  if (existingCoupon && couponItem) {
    const codeInput = couponItem.querySelector('input[name$="[code]"]');
    const activeCheckbox = couponItem.querySelector('input[type="checkbox"]');
    const toggleLabel = couponItem.querySelector(".toggle-label");

    if (codeInput) {
      codeInput.value = existingCoupon.code || "";
    }

    if (activeCheckbox) {
      activeCheckbox.checked = existingCoupon.is_active === 1;
    }

    if (toggleLabel) {
      toggleLabel.textContent =
        existingCoupon.is_active === 1 ? "Active" : "Inactive";
    }
  }
}

/**
 * Remove a coupon from the form
 * @param {HTMLElement} button - The remove button element
 */
function removeCoupon(button) {
  const couponItem = button.closest(".coupon-item");
  if (couponItem) {
    couponItem.remove();
  }
}

/**
 * Generate a coupon code
 * @param {HTMLElement} button - The generate button element
 */
function generateCouponCode(button) {
  const couponItem = button.closest(".coupon-item");
  if (!couponItem) return;

  const codeInput = couponItem.querySelector('input[name$="[code]"]');
  if (!codeInput) return;

  // Get discount name for basing the code on
  const discountName = document.getElementById("discountName")?.value || "";
  const discountValue = document.getElementById("discountValue")?.value || "";
  const discountType =
    document.getElementById("discountType")?.value || "percentage";

  // Generate code based on discount name or random if no name
  let code = "";

  if (discountName) {
    // Use first letter of each word and add a random number
    const words = discountName.split(" ");
    code = words.map((word) => word.charAt(0).toUpperCase()).join("");
  } else {
    // Generate a random string
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for (let i = 0; i < 4; i++) {
      code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
  }

  // Add discount value
  if (discountValue) {
    code +=
      discountType === "percentage"
        ? Math.floor(Number(discountValue))
        : Math.floor(Number(discountValue) / 100);
  } else {
    // Add random number
    code += Math.floor(Math.random() * 90 + 10);
  }

  codeInput.value = code;
}

/**
 * Add a new condition to the form
 * @param {Object|null} existingCondition - Optional existing condition data
 */
function addCondition(existingCondition = null) {
  const conditionsContainer = document.getElementById("conditionsContainer");
  if (!conditionsContainer) return;

  const template = document.getElementById("conditionTemplate");
  if (!template) return;

  const clone = template.content.cloneNode(true);

  // Replace INDEX placeholder with the actual index
  const conditionIndex = state.conditionCount++;

  // Update IDs and names
  const elements = clone.querySelectorAll("[id], [name], [for]");
  elements.forEach((element) => {
    if (element.hasAttribute("id")) {
      element.id = element.id.replace("INDEX", conditionIndex);
    }
    if (element.hasAttribute("name")) {
      element.setAttribute(
        "name",
        element.getAttribute("name").replace("INDEX", conditionIndex)
      );
    }
    if (element.hasAttribute("for")) {
      element.setAttribute(
        "for",
        element.getAttribute("for").replace("INDEX", conditionIndex)
      );
    }
  });

  // Add the condition to the container
  conditionsContainer.appendChild(clone);

  // Get the last added condition card
  const conditionCards =
    conditionsContainer.querySelectorAll(".condition-card");
  const conditionCard = conditionCards[conditionCards.length - 1];

  // If we have an existing condition, populate the fields
  if (existingCondition && conditionCard) {
    const conditionTypeSelect = conditionCard.querySelector(
      'select[name$="[condition_type]"]'
    );

    if (conditionTypeSelect) {
      conditionTypeSelect.value = existingCondition.condition_type || "";

      // Trigger the condition fields update
      updateConditionFields(conditionTypeSelect);

      // Set the condition value
      if (existingCondition.condition_value) {
        const conditionValue = existingCondition.condition_value;

        switch (existingCondition.condition_type) {
          case "min_amount":
            setConditionValue(
              conditionCard,
              "min_amount",
              conditionValue.min_amount
            );
            break;

          case "min_quantity":
            setConditionValue(
              conditionCard,
              "product_id",
              conditionValue.product_id
            );
            setConditionValue(
              conditionCard,
              "product_name",
              conditionValue.product_name
            );
            conditionCard.querySelector(".selected-product-name").textContent =
              conditionValue.product_name;
            conditionCard.querySelector(".selected-product").style.display =
              "flex";
            setConditionValue(
              conditionCard,
              "min_quantity",
              conditionValue.min_quantity
            );
            break;

          case "loyalty_points":
            setConditionValue(
              conditionCard,
              "min_points",
              conditionValue.min_points
            );
            break;

          case "day_of_week":
            if (conditionValue.days && Array.isArray(conditionValue.days)) {
              conditionValue.days.forEach((day) => {
                const checkbox = conditionCard.querySelector(
                  `input[value="${day}"]`
                );
                if (checkbox) {
                  checkbox.checked = true;
                }
              });
            }
            break;

          case "time_of_day":
            setConditionValue(
              conditionCard,
              "start_time",
              conditionValue.start_time
            );
            setConditionValue(
              conditionCard,
              "end_time",
              conditionValue.end_time
            );
            break;
        }
      }
    }
  }
}

/**
 * Remove a condition from the form
 * @param {HTMLElement} button - The remove button element
 */
function removeCondition(button) {
  const conditionCard = button.closest(".condition-card");
  if (conditionCard) {
    conditionCard.remove();
  }
}

/**
 * Update condition fields based on selected condition type
 * @param {HTMLElement} select - The condition type select element
 */
function updateConditionFields(select) {
  const conditionType = select.value;
  const conditionCard = select.closest(".condition-card");
  if (!conditionCard) return;

  const conditionBody = conditionCard.querySelector(".condition-body");
  if (!conditionBody) return;

  // Clear existing fields
  conditionBody.innerHTML = "";

  if (!conditionType) return;

  // Get template for the selected condition type
  const templateId = conditionType + "_template";
  const template = document.getElementById(templateId);

  if (!template) return;

  // Clone template
  const clone = template.content.cloneNode(true);

  // Replace INDEX placeholder
  const conditionIndex = select.name.match(/\[(\d+)\]/)?.[1] || 0;

  // Update IDs and names
  const elements = clone.querySelectorAll("[id], [name], [for]");
  elements.forEach((element) => {
    if (element.hasAttribute("id")) {
      element.id = element.id.replace("INDEX", conditionIndex);
    }
    if (element.hasAttribute("name")) {
      element.setAttribute(
        "name",
        element.getAttribute("name").replace("INDEX", conditionIndex)
      );
    }
    if (element.hasAttribute("for")) {
      element.setAttribute(
        "for",
        element.getAttribute("for").replace("INDEX", conditionIndex)
      );
    }
  });

  if (conditionType === "min_quantity") {
    new SearchHandler({
      apiEndpoint: "/api/products/search",
      inputElement: clone.querySelector(".search-bar input"),
      resultsContainer: clone.querySelector(".search-bar .search-results"),
      itemsPerPage: 5,
      renderResultItem: (product) => {
        const element = document.createElement("div");
        element.classList.add("search-result");
        element.textContent = product.product_name;
        return element;
      },
      onSelect: (product, ctx) => {
        ctx.element.querySelector(
          ".selected-product-name"
        ).textContent = `${product.product_name} (${product.unit_symbol})`;
        ctx.element.querySelector("input[name*='product_id']").value =
          product.id;
        ctx.element.querySelector(
          "input[name*='product_name']"
        ).value = `${product.product_name} (${product.unit_symbol})`;
        ctx.element.style.display = "flex";
      },
      selectionContext: {
        element: clone.querySelector(".selected-product"),
      },
    });
  }

  conditionBody.appendChild(clone);
}

/**
 * Toggle a discount's active status
 * @param {number} id - The ID of the discount
 * @param {boolean} newStatus - The new status
 */
function toggleDiscountStatus(id, newStatus) {
  const action = newStatus ? "activate" : "deactivate";

  if (confirm("Are you sure you want to proceed?")) {
    location.href = `/discounts/${id}/${action}`;
  }
}

/**
 * Delete a discount
 * @param {number} id - The ID of the discount to delete
 */
function deleteDiscount(id) {
  if (confirm("Are you sure you want to delete this discount?")) {
    location.href = `/discounts/${id}/delete`;
  }
}

/**
 * Format a date for an input field (YYYY-MM-DD)
 * @param {Date} date - The date to format
 * @return {string} The formatted date string
 */
function formatDateForInput(date) {
  if (!date) return "";

  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");

  return `${year}-${month}-${day}`;
}

/**
 * Format a number as currency
 * @param {number|string} value - The value to format
 * @return {string} The formatted currency string
 */
function formatCurrency(value) {
  return Number(value).toLocaleString("en-IN", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

/**
 * Format a number with thousands separators
 * @param {number|string} value - The value to format
 * @return {string} The formatted number string
 */
function formatNumber(value) {
  return Number(value).toLocaleString("en-IN");
}

/**
 * Set a form element's value
 * @param {string} id - The element ID
 * @param {string} value - The value to set
 */
function setFormValue(id, value) {
  const element = document.getElementById(id);
  if (element) {
    element.value = value;
  }
}

/**
 * Set a condition field's value
 * @param {HTMLElement} conditionCard - The condition card element
 * @param {string} fieldName - The field name
 * @param {any} value - The value to set
 */
function setConditionValue(conditionCard, fieldName, value) {
  const input = conditionCard.querySelector(`[name$="[${fieldName}]"]`);
  if (input) {
    input.value = value;
  }
}

/**
 * Set an element's text content
 * @param {string} id - The element ID
 * @param {string} text - The text to set
 */
function setElementText(id, text) {
  const element = document.getElementById(id);
  if (element) {
    element.textContent = text;
  }
}

/**
 * Copy text to clipboard
 * @param {string} text - The text to copy
 */
function copyToClipboard(text) {
  navigator.clipboard
    .writeText(text)
    .then(() => {
      openPopupWithMessage("Coupon code copied to clipboard!", "success");
    })
    .catch((err) => {
      console.error("Could not copy text: ", err);
      openPopupWithMessage("Failed to copy to clipboard", "error");
    });
}

function applyFilters() {
  const status = document.getElementById("statusFilter").value;
  const type = document.getElementById("typeFilter").value;
  const application = document.getElementById("applicationFilter").value;
  const fromDate = document.getElementById("fromDate").value;
  const toDate = document.getElementById("toDate").value;
  const searchQuery = document.getElementById("discountSearch").value;

  const url = new URL(window.location.href);
  status
    ? url.searchParams.set("status", status)
    : url.searchParams.delete("status");
  type ? url.searchParams.set("type", type) : url.searchParams.delete("type");
  application
    ? url.searchParams.set("application", application)
    : url.searchParams.delete("application");
  fromDate
    ? url.searchParams.set("from", fromDate)
    : url.searchParams.delete("from");
  toDate ? url.searchParams.set("to", toDate) : url.searchParams.delete("to");
  searchQuery
    ? url.searchParams.set("q", searchQuery)
    : url.searchParams.delete("q");

  window.location.href = url.href;
}

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".pagination").forEach((pagination) => {
    const currentPage = parseInt(pagination.dataset.page);
    const totalPages = parseInt(pagination.dataset.totalPages);

    // Insert pagination
    insertPagination(pagination, currentPage, totalPages, (page) => {
      const currentUrl = new URL(window.location.href);
      currentUrl.searchParams.set("p", page);
      window.location.href = currentUrl.href;
    });
  });
});

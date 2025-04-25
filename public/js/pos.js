class PointOfSaleManager {
  constructor() {
    // Core POS data
    this.cartItems = new Map();
    this.calculatedTotal = 0;
    this.calculatedSubtotal = 0;
    this.calculatedDiscount = 0;
    this.selectedCustomer = null;
    this.discounts = [];

    // DOM element references
    this.elements = {
      productSearch: document.getElementById("productSearch"),
      productsGrid: document.querySelector(".products-grid"),
      cartItemsList: document.querySelector(".cart-items-list"),
      cartSubtotal: document.querySelector(".cart-subtotal"),
      cartMenu: document.getElementById("cart-menu"),
      checkoutForm: document.getElementById("checkoutForm"),
      checkoutDialog: document.getElementById("checkoutDialog"),
      discountsList: document.querySelector(".discounts-items"),
      cartItemEditDialog: document.getElementById("cartItemEditDialog"),
      cartItemEditForm: document.getElementById("cartItemEditForm"),
      customerSearchDialog: document.getElementById("customerSearchDialog"),
      customerSearchForm: document.getElementById("customerSearchForm"),
      newCustomerDialog: document.getElementById("newCustomerDialog"),
      loaderDialog: document.getElementById("loaderDialog"),
    };

    this.templates = {
      productCard: document.getElementById("productCardTemplate"),
      cartItem: document.getElementById("cartItemTemplate"),
      discountItem: document.getElementById("discountItemTemplate"),
      noDiscounts: document.getElementById("noDiscountsTemplate"),
    };
  }

  init() {
    // Set up event listeners
    this.elements.productSearch.addEventListener("input", (e) =>
      this.searchProducts(e)
    );
    this.elements.cartItemEditDialog.querySelector(".close-btn").onclick = () =>
      this.closeCartItemEdit();
    this.elements.cartItemEditDialog.querySelector(
      ".form-actions button[type='button']"
    ).onclick = () => this.closeCartItemEdit();

    // Load saved data from session storage
    this.restoreCartState();
  }

  restoreCartState() {
    const cartData = sessionStorage.getItem("cart");
    if (cartData) {
      this.cartItems = new Map(JSON.parse(cartData));
    }

    const customerData = sessionStorage.getItem("customer");
    if (customerData) {
      this.selectedCustomer = JSON.parse(customerData);
    }

    this.renderCart();
  }

  clearCartState() {
    this.cartItems.clear();
    this.selectedCustomer = null;
    this.calculatedSubtotal = 0;
    this.calculatedDiscount = 0;
    this.calculatedTotal = 0;
    sessionStorage.removeItem("cart");
    sessionStorage.removeItem("customer");
    this.renderCart();
  }

  createProductCard(product) {
    const productCard = this.templates.productCard.content.cloneNode(true);
    productCard.querySelector(".product-name").textContent =
      product.product_name;
    productCard.querySelector(".product-code").textContent =
      product.product_code;
    productCard.querySelector(
      ".product-price"
    ).textContent = `Rs. ${product.unit_price}`;
    productCard.querySelector(".edit-btn").onclick = () =>
      this.openCartItemEdit(product);
    productCard.querySelector(".add-btn").onclick = () =>
      this.addToCart(product);
    this.elements.productsGrid.appendChild(productCard);
  }

  renderSearchResults(products) {
    this.elements.productsGrid.innerHTML = "";

    products.forEach((product) => {
      Object.entries(product.prices).forEach(
        ([unit_price, available_quantity]) => {
          const productItem = {
            key: `${product.id}/${unit_price}`,
            ...product,
            unit_price,
            available_quantity,
          };

          this.createProductCard(productItem);
        }
      );
    });
  }

  async searchProducts(e) {
    try {
      const query = e.target.value;

      if (!query) {
        this.elements.productsGrid.innerHTML = "";
        return;
      }

      const response = await fetch(`/api/pos/search?q=${query}`);
      const data = await response.json();

      // Check if the search query is still relevant
      if (this.elements.productSearch.value !== query) {
        return;
      }

      if (data.success === false) {
        openPopupWithMessage(data.message, "error");
        return;
      }

      this.renderSearchResults(data.data);
    } catch (error) {
      console.error("Error searching products:", error);
      openPopupWithMessage("Error searching products", "error");
    }
  }

  createCartItemElement(product) {
    const cartItem = this.templates.cartItem.content.cloneNode(true);
    cartItem.querySelector(".cart-item-name").textContent =
      product.product_name;
    cartItem.querySelector(
      ".cart-item-price"
    ).textContent = `Rs. ${product.unit_price}`;
    cartItem.querySelector(".cart-item-quantity").textContent = `Qty: ${
      product.is_int ? product.quantity.toFixed(0) : product.quantity.toFixed(3)
    } ${product.unit_symbol}`;
    cartItem.querySelector(".cart-item-subtotal").textContent = `Rs. ${(
      product.unit_price * product.quantity
    ).toFixed(2)}`;
    cartItem.querySelector(".edit-btn").onclick = () =>
      this.openCartItemEdit(product);
    cartItem.querySelector(".delete-btn").onclick = () =>
      this.removeFromCart(product.key);

    this.elements.cartItemsList.appendChild(cartItem);
  }

  addToCart(product) {
    if (this.cartItems.has(product.key)) {
      this.cartItems.get(product.key).quantity++;
    } else {
      product.quantity = 1;
      this.cartItems.set(product.key, product);
    }

    // Check stock availability
    if (this.cartItems.get(product.key).quantity > product.available_quantity) {
      openPopupWithMessage(
        "Not enough stock available. Check again.",
        "warning"
      );
    }

    this.renderCart();
  }

  removeFromCart(key) {
    this.cartItems.delete(key);
    this.renderCart();
  }

  renderCart() {
    // Save cart state to sessionStorage
    sessionStorage.setItem(
      "cart",
      JSON.stringify(Array.from(this.cartItems.entries()))
    );

    sessionStorage.setItem("customer", JSON.stringify(this.selectedCustomer));

    // Calculate subtotal
    this.calculatedSubtotal = 0;
    this.cartItems.forEach((product) => {
      this.calculatedSubtotal += product.unit_price * product.quantity;
    });

    // Update UI
    this.elements.cartSubtotal.textContent = `Rs. ${this.calculatedSubtotal.toFixed(
      2
    )}`;
    this.renderCartItems();
    this.renderCustomerData();
  }

  renderCartItems() {
    this.elements.cartItemsList.innerHTML = "";

    this.cartItems.forEach((product) => {
      this.createCartItemElement(product);
    });
  }

  openCartItemEdit(product) {
    const form = this.elements.cartItemEditForm;
    const dialog = this.elements.cartItemEditDialog;

    form.querySelector(
      "label"
    ).textContent = `Quantity (${product.unit_symbol})`;
    form.elements["quantity"].value = product.quantity || 1;
    form.elements["quantity"].step = product.is_int ? "1" : "0.001";

    form.onsubmit = (e) => {
      e.preventDefault();
      const qty = form.elements["quantity"].value;
      product.quantity = product.is_int ? parseInt(qty) : parseFloat(qty);
      this.cartItems.set(product.key, product);

      if (
        this.cartItems.get(product.key).quantity > product.available_quantity
      ) {
        openPopupWithMessage(
          "Not enough stock available. Check again.",
          "warning"
        );
      }

      if (product.quantity <= 0) {
        this.cartItems.delete(product.key);
      }

      this.renderCart();
      this.closeCartItemEdit();
    };

    dialog.showModal();
  }

  closeCartItemEdit() {
    this.elements.cartItemEditForm.reset();
    this.elements.cartItemEditDialog.close();
  }

  renderDiscounts() {
    this.elements.discountsList.innerHTML = "";

    if (this.discounts.length === 0) {
      const noDiscounts = this.templates.noDiscounts.content.cloneNode(true);
      this.elements.discountsList.appendChild(noDiscounts);
      return;
    }

    this.discounts.forEach((discount) => {
      this.calculatedDiscount += discount.calculated_amount;
      const discountItem = this.templates.discountItem.content.cloneNode(true);
      discountItem.querySelector(".discount-name").textContent = discount.name;
      discountItem.querySelector(".discount-value").textContent =
        discount.discount_type === "fixed"
          ? `Rs. ${discount.value}`
          : `${discount.value}%`;
      this.elements.discountsList.appendChild(discountItem);
    });
  }

  async fetchAvailableDiscounts() {
    try {
      const items = Array.from(this.cartItems.values()).map((product) => {
        return {
          product_id: product.id,
          unit_price: product.unit_price,
          quantity: product.quantity,
        };
      });

      const requestData = {
        customer_id: this.selectedCustomer ? this.selectedCustomer.id : null,
        items,
      };

      this.showLoader();
      const response = await fetch("/api/pos/discounts/get", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(requestData),
      });

      const result = await response.json();
      this.hideLoader();

      if (result.success) {
        this.discounts = result.data;
        return true;
      } else {
        console.error("Error fetching discounts:", result.message);
      }
    } catch (error) {
      this.hideLoader();
      console.error("Error fetching discounts:", error);
    }
    return false;
  }

  async openCheckoutDialog() {
    if (this.cartItems.size === 0) {
      openPopupWithMessage("Your cart is empty", "warning");
      return;
    }

    // Reset discount and coupon state
    this.calculatedDiscount = 0;

    // Fetch available discounts
    const fetchedDiscounts = await this.fetchAvailableDiscounts();

    if (fetchedDiscounts) {
      this.renderDiscounts();
    }

    this.updateCheckoutSummary();

    // Set up and show the dialog
    const dialog = this.elements.checkoutDialog;
    dialog.querySelector(".close-btn").onclick = () =>
      this.closeCheckoutDialog();
    dialog.querySelector(".form-actions button[type='button']").onclick = () =>
      this.closeCheckoutDialog();
    dialog.showModal();

    if (!fetchedDiscounts) {
      openPopupWithMessage("Failed to fetch discounts", "error");
    }
  }

  updateCheckoutSummary() {
    this.calculatedTotal = this.calculatedSubtotal - this.calculatedDiscount;

    document.querySelector(
      ".checkout-subtotal"
    ).textContent = `Rs. ${this.calculatedSubtotal.toFixed(2)}`;
    document.querySelector(
      ".checkout-discount"
    ).textContent = `Rs. ${this.calculatedDiscount.toFixed(2)}`;
    document.querySelector(
      ".checkout-total"
    ).textContent = `Rs. ${this.calculatedTotal.toFixed(2)}`;
  }

  closeCheckoutDialog() {
    this.elements.checkoutForm.reset();
    this.elements.checkoutDialog.close();
  }

  /**
   * Process checkout
   * @param {Event} e - Form submit event
   */
  async checkout(e) {
    e.preventDefault();
    const form = e.target;

    try {
      const items = Array.from(this.cartItems.values()).map((product) => {
        return {
          product_id: product.id,
          unit_price: product.unit_price,
          quantity: product.quantity,
        };
      });

      const checkoutData = {
        customer_id: this.selectedCustomer ? this.selectedCustomer.id : null,
        items,
        payment_method: form.elements["payment_method"].value,
        notes: form.elements["notes"].value,
      };

      this.showLoader();
      const response = await fetch("/api/pos/checkout", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(checkoutData),
      });

      const result = await response.json();
      this.hideLoader();

      if (result.success) {
        this.clearCartState();
        this.closeCheckoutDialog();
        openPopupWithMessage(result.message, "success");
      } else {
        this.closeCheckoutDialog();
        openPopupWithMessage(result.message, "error");
      }
    } catch (error) {
      this.hideLoader();
      console.error("Error during checkout:", error);
      openPopupWithMessage("An error occurred during checkout", "error");
    }
  }

  /**
   * Open new customer dialog
   */
  openNewCustomerDialog() {
    this.elements.newCustomerDialog.showModal();
  }

  /**
   * Close new customer dialog
   */
  closeNewCustomerDialog() {
    document.getElementById("newCustomerForm").reset();
    this.elements.newCustomerDialog.close();
  }

  /**
   * Open customer search dialog
   */
  openCustomerSearchDialog() {
    this.elements.customerSearchDialog.showModal();
  }

  /**
   * Close customer search dialog
   */
  closeCustomerSearchDialog() {
    this.elements.customerSearchDialog.close();
    this.elements.customerSearchForm.reset();
  }

  /**
   * Render customer data in cart and checkout summaries
   */
  renderCustomerData() {
    // Remove existing customer info
    document
      .querySelector(".cart-summary .summary-item.customer-info")
      ?.remove();
    document
      .querySelector(".checkout-summary .summary-item.customer-info")
      ?.remove();
    document.getElementById("clear-customer-button")?.remove();

    if (this.selectedCustomer === null) return;

    // Create customer info element
    const summaryItem = document.createElement("div");
    summaryItem.classList.add("summary-item", "customer-info");
    summaryItem.innerHTML = `
            <span>Customer</span>
            <span>${this.selectedCustomer.name}</span>
        `;

    // Add to cart summary
    const cartSummary = document.querySelector(".cart-summary");
    cartSummary.insertBefore(summaryItem, cartSummary.firstChild);

    // Add to checkout summary if visible
    const checkoutSummary = document.querySelector(".checkout-summary");
    if (checkoutSummary) {
      checkoutSummary.insertBefore(
        summaryItem.cloneNode(true),
        checkoutSummary.firstChild
      );
    }

    // Add clear customer button
    const clearCustomerButton = document.createElement("button");
    clearCustomerButton.id = "clear-customer-button";
    clearCustomerButton.classList.add("dropdown-item");
    clearCustomerButton.innerHTML = `
            <span class="icon">person_remove</span>
            Clear Customer
        `;
    clearCustomerButton.addEventListener("click", () => this.clearCustomer());
    this.elements.cartMenu.appendChild(clearCustomerButton);
  }

  /**
   * Search for a customer
   * @param {Event} e - Form submit event
   */
  async searchCustomer(e) {
    e.preventDefault();
    const form = e.target;
    const phone = form.elements["phone"].value;

    try {
      this.showLoader();
      const response = await fetch(`/api/pos/customer/search?q=${phone}`);
      const result = await response.json();
      this.hideLoader();

      if (result.success) {
        this.selectedCustomer = result.data;
        this.renderCart();
      } else {
        openPopupWithMessage(result.message, "error");
      }

      this.closeCustomerSearchDialog();
    } catch (error) {
      this.hideLoader();
      console.error("Error searching for customer:", error);
      openPopupWithMessage(
        "An error occurred while searching for the customer",
        "error"
      );
    }
  }

  /**
   * Clear the selected customer
   */
  clearCustomer() {
    this.selectedCustomer = null;
    this.renderCart();
  }

  /**
   * Show the loading dialog
   */
  showLoader() {
    this.elements.loaderDialog.showModal();
  }

  /**
   * Hide the loading dialog
   */
  hideLoader() {
    this.elements.loaderDialog.close();
  }
}

// Initialize the POS system when the DOM is loaded
let pos;
document.addEventListener("DOMContentLoaded", () => {
  pos = new PointOfSaleManager();
  pos.init();
});

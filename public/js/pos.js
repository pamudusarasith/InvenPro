/**
 * Point of Sale (POS) System Controller
 * Manages all POS operations including product search, cart management, checkout and discounts
 */
class PointOfSaleManager {
  constructor() {
    // Core POS data
    this.cartItems = new Map();
    this.calculatedTotal = 0;
    this.calculatedSubtotal = 0;
    this.calculatedDiscount = 0;
    this.selectedCustomer = null;
    this.appliedCoupons = [];
    this.availableDiscounts = [];

    // DOM element references
    this.elements = {
      productSearch: document.getElementById("productSearch"),
      productsGrid: document.querySelector(".products-grid"),
      cartItemsList: document.querySelector(".cart-items-list"),
      cartSubtotal: document.querySelector(".cart-subtotal"),
      cartMenu: document.getElementById("cart-menu"),
      checkoutForm: document.getElementById("checkoutForm"),
      checkoutDialog: document.getElementById("checkoutDialog"),
      cartItemEditDialog: document.getElementById("cartItemEditDialog"),
      cartItemEditForm: document.getElementById("cartItemEditForm"),
      customerSearchDialog: document.getElementById("customerSearchDialog"),
      customerSearchForm: document.getElementById("customerSearchForm"),
      newCustomerDialog: document.getElementById("newCustomerDialog"),
      loaderDialog: document.getElementById("loaderDialog"),
    };
  }

  /**
   * Initialize the POS system
   */
  init() {
    // Set up event listeners
    this.elements.productSearch.addEventListener("input", (e) =>
      this.searchProducts(e)
    );

    // Load saved data from session storage
    this.restoreCartState();
  }

  /**
   * Restore cart state from sessionStorage if available
   */
  restoreCartState() {
    const cartData = sessionStorage.getItem("cart");
    if (cartData) {
      this.cartItems = new Map(JSON.parse(cartData));
    }

    const customerData = sessionStorage.getItem("customer");
    if (customerData) {
      this.selectedCustomer = JSON.parse(customerData);
    }

    this.updateCart();
  }

  /**
   * Create a product card for the products grid
   * @param {Object} product - Product data
   * @returns {HTMLElement} Product card element
   */
  createProductCard(product) {
    const productCard = document.createElement("div");
    productCard.classList.add("product-card", "card", "glass");
    productCard.innerHTML = `
            <div class="product-info">
                <div class="product-name">${product.product_name}</div>
                <div class="product-code">${product.product_code}</div>
                <div class="product-price">Rs. ${product.price}</div>
            </div>
        `;

    const productActions = document.createElement("div");
    productActions.classList.add("product-actions");

    const editButton = document.createElement("button");
    editButton.addEventListener("click", () => this.openCartItemEdit(product));
    editButton.innerHTML = `<span class="icon">edit</span>`;

    const addButton = document.createElement("button");
    addButton.addEventListener("click", () => this.addToCart(product));
    addButton.innerHTML = `<span class="icon">add_shopping_cart</span>`;

    productActions.appendChild(editButton);
    productActions.appendChild(addButton);
    productCard.appendChild(productActions);

    return productCard;
  }

  /**
   * Display search results in the products grid
   * @param {Array} products - Array of product data
   */
  renderSearchResults(products) {
    this.elements.productsGrid.innerHTML = "";

    products.forEach((product) => {
      Object.entries(product.prices).forEach(([price, available_quantity]) => {
        const productItem = {
          key: `${product.id}/${price}`,
          ...product,
          price,
          available_quantity,
        };

        const productCard = this.createProductCard(productItem);
        this.elements.productsGrid.appendChild(productCard);
      });
    });
  }

  /**
   * Search for products based on user input
   * @param {Event} e - Input event
   */
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
      this.hideLoader();
      console.error("Error searching products:", error);
      openPopupWithMessage("Error searching products", "error");
    }
  }

  /**
   * Create a cart item element for the cart list
   * @param {Object} product - Product data
   * @returns {HTMLElement} Cart item element
   */
  createCartItemElement(product) {
    const cartItem = document.createElement("div");
    cartItem.classList.add("cart-item");

    const subtotal = product.price * product.quantity;

    cartItem.innerHTML = `
            <div class="cart-item-info">
                <div class="cart-item-name">${product.product_name}</div>
                <div class="cart-item-price">Rs. ${product.price}</div>
            </div>
            <div class="cart-item-quantity">Qty: ${product.quantity.toFixed(
              3
            )}</div>
            <div class="cart-item-subtotal">Rs. ${subtotal.toFixed(2)}</div>
        `;

    // Add edit button
    const editButton = document.createElement("button");
    editButton.classList.add("icon-btn");
    editButton.innerHTML = `<span class="icon">edit</span>`;
    editButton.addEventListener("click", () => this.openCartItemEdit(product));
    cartItem.appendChild(editButton);

    // Add delete button
    const deleteButton = document.createElement("button");
    deleteButton.classList.add("icon-btn", "danger");
    deleteButton.innerHTML = `<span class="icon">delete</span>`;
    deleteButton.addEventListener("click", () =>
      this.removeFromCart(product.key)
    );
    cartItem.appendChild(deleteButton);

    return cartItem;
  }

  /**
   * Add a product to the cart
   * @param {Object} product - Product data
   */
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

    this.updateCart();
  }

  /**
   * Remove a product from the cart
   * @param {string} key - Product key
   */
  removeFromCart(key) {
    this.cartItems.delete(key);
    this.updateCart();
  }

  /**
   * Update cart state and UI
   */
  updateCart() {
    // Save cart state to sessionStorage
    sessionStorage.setItem(
      "cart",
      JSON.stringify(Array.from(this.cartItems.entries()))
    );

    sessionStorage.setItem("customer", JSON.stringify(this.selectedCustomer));

    // Calculate subtotal
    this.calculatedSubtotal = 0;
    this.cartItems.forEach((product) => {
      this.calculatedSubtotal += product.price * product.quantity;
    });

    // Update UI
    this.elements.cartSubtotal.textContent = `Rs. ${this.calculatedSubtotal.toFixed(
      2
    )}`;
    this.renderCartItems();
    this.renderCustomerData();
  }

  /**
   * Render all cart items in the cart list
   */
  renderCartItems() {
    this.elements.cartItemsList.innerHTML = "";

    this.cartItems.forEach((product) => {
      const cartItem = this.createCartItemElement(product);
      this.elements.cartItemsList.appendChild(cartItem);
    });
  }

  /**
   * Clear all items from the cart
   */
  clearCart() {
    this.cartItems.clear();
    this.updateCart();
  }

  /**
   * Open the cart item edit dialog
   * @param {Object} product - Product data
   */
  openCartItemEdit(product) {
    const form = this.elements.cartItemEditForm;
    const dialog = this.elements.cartItemEditDialog;

    form.elements["quantity"].value = product.quantity || 1;

    form.onsubmit = (e) => {
      e.preventDefault();
      product.quantity = parseFloat(form.elements["quantity"].value);
      this.cartItems.set(product.key, product);

      if (
        this.cartItems.get(product.key).quantity > product.available_quantity
      ) {
        openPopupWithMessage(
          "Not enough stock available. Check again.",
          "warning"
        );
      }

      this.updateCart();
      this.closeCartItemEdit();
    };

    dialog.querySelector(".close-btn").onclick = () => this.closeCartItemEdit();
    dialog.querySelector(".form-actions button[type='button']").onclick = () =>
      this.closeCartItemEdit();
    dialog.showModal();
  }

  /**
   * Close the cart item edit dialog
   */
  closeCartItemEdit() {
    this.elements.cartItemEditForm.reset();
    this.elements.cartItemEditDialog.close();
  }

  /**
   * Render applied coupons in the checkout dialog
   */
  renderCoupons() {
    const couponsList = document.querySelector(".coupons-items");
    couponsList.innerHTML = "";

    if (this.appliedCoupons.length === 0) {
      couponsList.innerHTML = `
                <div class="no-coupons">
                    <span class="icon">local_offer</span>
                    <p>No coupons applied</p>
                </div>
            `;
      return;
    }

    this.appliedCoupons.forEach((coupon) => {
      const couponItem = document.createElement("div");
      couponItem.classList.add("coupon-item");
      couponItem.innerHTML = `
                <div class="coupon-info">
                    <span class="coupon-name">${coupon.code}</span>
                    <span class="coupon-value">${coupon.value}</span>
                </div>
                <button type="button" class="icon-btn danger" title="Remove coupon">
                    <span class="icon">close</span>
                </button>
            `;

      couponItem
        .querySelector("button")
        .addEventListener("click", () => this.removeCoupon(coupon));
      couponsList.appendChild(couponItem);
    });
  }

  /**
   * Apply a coupon code to the current order
   */
  applyCoupon() {
    const couponCode = document.getElementById("coupon").value;

    if (!couponCode) {
      openPopupWithMessage("Please enter a coupon code", "warning");
      return;
    }

    const couponDiscounts = this.availableDiscounts.filter(
      (discount) =>
        discount.application_method === "coupon" &&
        discount.coupons.some((coupon) => coupon.code === couponCode)
    );

    if (couponDiscounts.length === 0) {
      openPopupWithMessage("Invalid coupon code", "error");
      return;
    }

    couponDiscounts.forEach((discount) => {
      discount.coupons.forEach((coupon) => {
        if (coupon.code === couponCode) {
          this.appliedCoupons.push({
            ...coupon,
            value: discount.value,
          });
          this.calculatedDiscount += discount.calculated_amount;
          this.renderCoupons();

          // Update total display
          this.updateCheckoutSummary();
        }
      });
    });
  }

  /**
   * Remove a coupon from the applied coupons
   * @param {Object} coupon - Coupon data
   */
  removeCoupon(coupon) {
    this.calculatedDiscount -= coupon.value;
    this.appliedCoupons = this.appliedCoupons.filter(
      (c) => c.code !== coupon.code
    );
    this.renderCoupons();

    // Update total display
    this.updateCheckoutSummary();
  }

  /**
   * Render available discounts in the checkout dialog
   */
  renderDiscounts() {
    const discountsList = document.querySelector(".discounts-items");
    discountsList.innerHTML = "";

    const regularDiscounts = this.availableDiscounts.filter(
      (discount) => discount.application_method === "regular"
    );

    if (regularDiscounts.length === 0) {
      discountsList.innerHTML = `
                <div class="no-discounts">
                    <span class="icon">percent</span>
                    <p>No discounts applied</p>
                </div>
            `;
      return;
    }

    regularDiscounts.forEach((discount) => {
      this.calculatedDiscount += discount.calculated_amount;

      const discountItem = document.createElement("div");
      discountItem.classList.add("discount-item");

      const discountValue =
        discount.discount_type === "fixed"
          ? `Rs. ${discount.value}`
          : `${discount.value}%`;

      discountItem.innerHTML = `
                <div class="discount-info">
                    <span class="discount-name">${discount.name}</span>
                    <span class="discount-value">${discountValue}</span>
                </div>
            `;
      discountsList.appendChild(discountItem);
    });
  }

  /**
   * Fetch available discounts from the server
   * @returns {boolean} Whether discounts were successfully fetched
   */
  async fetchAvailableDiscounts() {
    try {
      const items = Array.from(this.cartItems.values()).map((product) => {
        return {
          product_id: product.id,
          price: product.price,
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
        this.availableDiscounts = result.data;
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

  /**
   * Open the checkout dialog
   */
  async openCheckoutDialog() {
    if (this.cartItems.size === 0) {
      openPopupWithMessage("Your cart is empty", "warning");
      return;
    }

    // Reset discount and coupon state
    this.calculatedDiscount = 0;
    this.appliedCoupons = [];

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

  /**
   * Update the checkout summary with current totals
   */
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

  /**
   * Close the checkout dialog
   */
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
          price: product.price,
          quantity: product.quantity,
        };
      });

      const checkoutData = {
        customer_id: this.selectedCustomer ? this.selectedCustomer.id : null,
        items,
        payment_method: form.elements["payment_method"].value,
        notes: form.elements["notes"].value,
        discounts: this.availableDiscounts
          .filter((d) => d.application_method === "regular")
          .concat(
            this.appliedCoupons.map((c) => ({ type: "coupon", code: c.code }))
          ),
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
        this.clearCart();
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
        this.updateCart();
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
    this.updateCart();
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

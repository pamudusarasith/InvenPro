<?php

use App\Core\View;
use App\Services\RBACService;

?>

<div class="body pos">
    <?php View::render("Navbar"); ?>

    <div class="main">
        <div class="pos-container">
            <!-- Left side - Products section -->
            <div class="pos-products-section">
                <!-- Search and filters -->
                <div class="card glass" style="padding: 1rem;">
                    <div class="search-bar">
                        <span class="icon">search</span>
                        <input type="text" id="productSearch" placeholder="Search products by name or code...">
                    </div>
                </div>

                <!-- Products grid -->
                <div class="products-grid"></div>
            </div>

            <!-- Right side - Cart section -->
            <div class="pos-cart-section">
                <div class="cart-items card glass">
                    <div class="cart-items-header">
                        <h2>Shopping Cart</h2>
                        <div class="dropdown">
                            <button class="dropdown-trigger icon-btn" title="More options">
                                <span class="icon">more_vert</span>
                            </button>
                            <div id="cart-menu" class="dropdown-menu">
                                <button id="cart-clear-btn" class="dropdown-item danger" onclick="pos.clearCart()">
                                    <span class="icon">remove_shopping_cart</span>
                                    Clear Cart
                                </button>
                                <?php if (RBACService::hasPermission('add_customer') && $_SESSION['user']['id'] != $user['id']): ?>
                                    <button id="add-customer-btn" class="dropdown-item" onclick="pos.openNewCustomerDialog()">
                                        <span class="icon">person_add</span>
                                        New Customer
                                    </button>
                                <?php endif; ?>
                                <button class="dropdown-item" onclick="pos.openCustomerSearchDialog()">
                                    <span class="icon">search</span>
                                    Find Customer
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="cart-items-list"></div>
                </div>

                <div class="cart-summary card glass">
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span class="cart-subtotal">Rs. 0.00</span>
                    </div>

                    <div class="cart-actions">
                        <button id="checkout-btn" class="btn btn-primary btn-large" onclick="pos.openCheckoutDialog()">
                            <span class="icon">point_of_sale</span>
                            Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<dialog id="cartItemEditDialog" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Cart Item</h2>
            <button class="close-btn">
                <span class="icon">close</span>
            </button>
        </div>
        <form id="cartItemEditForm" class="modal-body">
            <div class="form-grid">
                <div class="form-field span-2">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="0" step="0.001" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Add to Cart
                </button>
            </div>
        </form>
    </div>
</dialog>

<!-- Customer search dialog -->
<dialog id="customerSearchDialog" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Find Customer</h2>
            <button class="close-btn" onclick="pos.closeCustomerSearchDialog()">
                <span class="icon">close</span>
            </button>
        </div>
        <form id="customerSearchForm" class="modal-body" onsubmit="pos.searchCustomer(event)">
            <div class="form-grid">
                <div class="form-field span-2">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="pos.closeCustomerSearchDialog()">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Search
                </button>
            </div>
        </form>
    </div>
</dialog>

<!-- New customer dialog -->
<dialog id="newCustomerDialog" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>New Customer</h2>
            <button class="close-btn" onclick="pos.closeNewCustomerDialog()">
                <span class="icon">close</span>
            </button>
        </div>
        <form id="newCustomerForm" class="modal-body" method="post" action="/customers/new">
            <div class="form-grid">
                <div class="form-field">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="first_name" required>
                </div>
                <div class="form-field">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="last_name">
                </div>
                <div class="form-field">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>
                <div class="form-field span-2">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="pos.closeNewCustomerDialog()">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Create Customer
                </button>
            </div>
        </form>
    </div>
</dialog>

<!-- Checkout dialog -->
<dialog id="checkoutDialog" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Checkout</h2>
            <button class="close-btn">
                <span class="icon">close</span>
            </button>
        </div>
        <form id="checkoutForm" class="modal-body" onsubmit="pos.checkout(event)">
            <div class="form-grid">
                <div class="form-field span-2">
                    <label for="paymentMethod">Payment Method</label>
                    <select id="paymentMethod" name="payment_method" required>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div class="form-field span-2">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3"></textarea>
                </div>
                <div class="form-field span-2">
                    <label for="coupon">Coupon Code</label>
                    <div class="coupon-field">
                        <input type="text" id="coupon" name="coupon_code">
                        <button type="button" class="btn btn-primary" onclick="applyCoupon()">
                            Apply
                        </button>
                    </div>
                </div>
            </div>
            <div class="discounts-list">
                <h3>Applied Discounts</h3>
                <div class="no-discounts">
                    <span class="icon">percent</span>
                    <p>No discounts applied</p>
                </div>
                <!-- <div class="applied-discounts">
                    <div class="discount-item">
                        <div class="discount-info">
                            <span class="discount-name">New Year Sale</span>
                            <span class="discount-value">-10%</span>
                        </div>
                        <button class="icon-btn danger" title="Remove discount">
                            <span class="icon">close</span>
                        </button>
                    </div>
                    <div class="discount-item">
                        <div class="discount-info">
                            <span class="discount-name">Loyalty Points</span>
                            <span class="discount-value">-Rs. 500.00</span>
                        </div>
                        <button class="icon-btn danger" title="Remove discount">
                            <span class="icon">close</span>
                        </button>
                    </div>
                </div> -->
            </div>
            <div class="checkout-summary">
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span class="checkout-subtotal">Rs. 0.00</span>
                </div>
                <div class="summary-item">
                    <span>Discount</span>
                    <span class="checkout-discount">Rs. 0.00</span>
                </div>
                <div class="summary-item total">
                    <span>Total</span>
                    <span class="checkout-total">Rs. 0.00</span>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary btn-large">
                    <span class="icon">done</span>
                    Complete Checkout
                </button>
            </div>
        </form>
    </div>
</dialog>

<script>
    class POS {
        constructor() {
            this.cart = new Map();
            this.cartTotal = 0;
            this.cartSubtotal = 0;
            this.cartDiscount = 0;
            this.customer = null;
            this.coupon = null;
        }

        init() {
            document
                .getElementById("productSearch")
                .addEventListener("input", (e) => this.searchProducts(e));

            const cartData = sessionStorage.getItem("cart");
            if (cartData) {
                this.cart = new Map(JSON.parse(cartData));
            }
            const customerData = sessionStorage.getItem("customer");
            if (customerData) {
                this.customer = JSON.parse(customerData);
            }
            this.updateCart();
        }

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

        renderSearchResults(products) {
            document.querySelector(".products-grid").innerHTML = "";
            products.forEach((product) => {
                product.prices.forEach((price) => {
                    const productCard = this.createProductCard({
                        key: `${product.id}/${price}`,
                        ...product,
                        price,
                    });
                    document.querySelector(".products-grid").appendChild(productCard);
                });
            });
        }

        async searchProducts(e) {
            try {
                const query = e.target.value;
                if (!query) {
                    document.querySelector(".products-grid").innerHTML = "";
                    return;
                }
                const response = await fetch(`/api/pos/search?q=${query}`);
                const data = await response.json();

                if (document.getElementById("productSearch").value !== query) {
                    return;
                }

                if (data.success === false) {
                    alert(data.message);
                    return;
                }

                this.renderSearchResults(data.data);
            } catch (error) {
                console.error(error);
            }
        }

        createCartItemElement(product) {
            const cartItem = document.createElement("div");
            cartItem.classList.add("cart-item");
            cartItem.innerHTML = `
                <div class="cart-item-info">
                    <div class="cart-item-name">${product.product_name}</div>
                    <div class="cart-item-price">Rs. ${product.price}</div>
                </div>
                <div class="cart-item-quantity">Qty: ${product.quantity.toFixed(
                3
                )}</div>
                <div class="cart-item-subtotal">$${(
                product.price * product.quantity
                ).toFixed(2)}</div>
            `;

            const editButton = document.createElement("button");
            editButton.classList.add("icon-btn");
            editButton.innerHTML = `<span class="icon">edit</span>`;
            editButton.addEventListener("click", () => this.openCartItemEdit(product));
            cartItem.appendChild(editButton);

            const deleteButton = document.createElement("button");
            deleteButton.classList.add("icon-btn", "danger");
            deleteButton.innerHTML = `<span class="icon">delete</span>`;
            deleteButton.addEventListener("click", () =>
                this.removeFromCart(product.key)
            );
            cartItem.appendChild(deleteButton);

            return cartItem;
        }

        addToCart(product) {
            if (this.cart.has(product.key)) {
                this.cart.get(product.key).quantity++;
            } else {
                product.quantity = 1;
                this.cart.set(product.key, product);
            }

            this.updateCart();
        }

        removeFromCart(key) {
            this.cart.delete(key);
            this.updateCart();
        }

        updateCart() {
            sessionStorage.setItem(
                "cart",
                JSON.stringify(Array.from(this.cart.entries()))
            );

            sessionStorage.setItem("customer", JSON.stringify(this.customer));

            this.cartSubtotal = 0;
            this.cart.forEach((product, _) => {
                this.cartSubtotal += product.price * product.quantity;
            });

            document.querySelector(
                ".cart-subtotal"
            ).textContent = `Rs. ${this.cartSubtotal.toFixed(2)}`;

            this.renderCartItems();
            this.renderCustomerData();
        }

        renderCartItems() {
            const cartItemsList = document.querySelector(".cart-items-list");
            cartItemsList.innerHTML = "";
            this.cart.forEach((product, index) => {
                const cartItem = this.createCartItemElement(product);
                cartItemsList.appendChild(cartItem);
            });
        }

        clearCart() {
            this.cart.clear();
            this.updateCart();
        }

        openCartItemEdit(product) {
            const form = document.getElementById("cartItemEditForm");

            form.elements["quantity"].value = product.quantity || 1;

            form.onsubmit = (e) => {
                e.preventDefault();
                product.quantity = parseFloat(form.elements["quantity"].value);
                this.cart.set(product.key, product);
                this.updateCart();
                this.closeCartItemEdit();
            };

            const modal = document.getElementById("cartItemEditDialog");
            modal.querySelector(".close-btn").onclick = () => this.closeCartItemEdit();
            modal.querySelector(".form-actions button[type='button']").onclick = () =>
                this.closeCartItemEdit();
            modal.showModal();
        }

        closeCartItemEdit() {
            document.getElementById("cartItemEditForm").reset();
            document.getElementById("cartItemEditDialog").close();
        }

        openCheckoutDialog() {
            if (this.cart.size === 0) {
                alert("Cart is empty");
                return;
            }

            this.cartTotal = this.cartSubtotal - this.cartDiscount;

            document.querySelector(
                ".checkout-subtotal"
            ).textContent = `Rs. ${this.cartSubtotal.toFixed(2)}`;
            document.querySelector(
                ".checkout-discount"
            ).textContent = `Rs. ${this.cartDiscount.toFixed(2)}`;
            document.querySelector(
                ".checkout-total"
            ).textContent = `Rs. ${this.cartTotal.toFixed(2)}`;

            const modal = document.getElementById("checkoutDialog");
            modal.querySelector(".close-btn").onclick = () =>
                this.closeCheckoutDialog();
            modal.querySelector(".form-actions button[type='button']").onclick = () =>
                this.closeCheckoutDialog();
            modal.showModal();
        }

        closeCheckoutDialog() {
            document.getElementById("checkoutForm").reset();
            document.getElementById("checkoutDialog").close();
        }

        async checkout(e) {
            e.preventDefault();
            const form = e.target;
            const items = Array.from(
                this.cart.values().map((product) => {
                    return {
                        product_id: product.id,
                        price: product.price,
                        quantity: product.quantity,
                    };
                })
            );
            const data = {
                customer_id: this.customer ? this.customer.id : null,
                items,
                payment_method: form.elements["payment_method"].value,
                notes: form.elements["notes"].value,
                discounts: [],
            };

            const response = await fetch("/api/pos/checkout", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                this.clearCart();
                this.closeCheckoutDialog();
                openPopupWithMessage(result.message, "success");
            } else {
                this.closeCheckoutDialog();
                openPopupWithMessage(result.message, "error");
            }
        }

        openNewCustomerDialog() {
            const modal = document.getElementById("newCustomerDialog");
            modal.showModal();
        }

        closeNewCustomerDialog() {
            document.getElementById("newCustomerForm").reset();
            document.getElementById("newCustomerDialog").close();
        }

        openCustomerSearchDialog() {
            const modal = document.getElementById("customerSearchDialog");
            modal.showModal();
        }

        closeCustomerSearchDialog() {
            document.getElementById("customerSearchDialog").close();
            document.getElementById("customerSearchForm").reset();
        }

        renderCustomerData() {
            document
                .querySelector(".cart-summary .summary-item.customer-info")
                ?.remove();
            document
                .querySelector(".checkout-summary .summary-item.customer-info")
                ?.remove();
            document.getElementById("clear-customer-button")?.remove();

            if (this.customer === null) return;

            const summaryItem = document.createElement("div");
            summaryItem.classList.add("summary-item", "customer-info");
            summaryItem.innerHTML = `
                <span>Customer</span>
                <span>${this.customer.name}</span>
            `;

            const cartSummary = document.querySelector(".cart-summary");
            const checkoutSummary = document.querySelector(".checkout-summary");
            cartSummary.insertBefore(summaryItem, cartSummary.firstChild);
            checkoutSummary.insertBefore(
                summaryItem.cloneNode(true),
                checkoutSummary.firstChild
            );

            const clearCustomerButton = document.createElement("button");
            clearCustomerButton.id = "clear-customer-button";
            clearCustomerButton.classList.add("dropdown-item");
            clearCustomerButton.innerHTML = `
                <span class="icon">person_remove</span>
                Clear Customer
            `;
            clearCustomerButton.onclick = () => this.clearCustomer();
            document.getElementById("cart-menu").appendChild(clearCustomerButton);
        }

        async searchCustomer(e) {
            e.preventDefault();
            const form = e.target;
            const phone = form.elements["phone"].value;

            const response = await fetch(`/api/pos/customer/search?q=${phone}`);
            const result = await response.json();

            if (result.success) {
                this.customer = result.data;
                this.updateCart();
            } else {
                openPopupWithMessage(result.message, "error");
            }

            this.closeCustomerSearchDialog();
        }

        clearCustomer() {
            this.customer = null;
            this.updateCart();
        }
    }

    let pos;

    document.addEventListener("DOMContentLoaded", () => {
        pos = new POS();
        pos.init();
    });
</script>

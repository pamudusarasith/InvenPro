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
                        <input type="text" id="productSearch" placeholder="Search products by name or code..." autocomplete="off">
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

<template id="productCardTemplate">
    <div class="product-card card glass">
        <div class="product-info">
            <div class="product-name"></div>
            <div class="product-code"></div>
            <div class="product-price"></div>
            <div class="product-actions">
                <button class="edit-btn">
                    <span class="icon">edit</span>
                </button>
                <button class="add-btn">
                    <span class="icon">add_shopping_cart</span>
                </button>
            </div>
        </div>
    </div>
</template>
<template id="cartItemTemplate">
    <div class="cart-item">
        <div class="cart-item-info">
            <div class="cart-item-name"></div>
            <div class="cart-item-price"></div>
        </div>
        <div class="cart-item-quantity"></div>
        <div class="cart-item-subtotal"></div>
        <button class="icon-btn edit edit-btn">
            <span class="icon">edit</span>
        </button>
        <button class="icon-btn danger delete-btn">
            <span class="icon">delete</span>
        </button>
    </div>
</template>
<template id="couponItemTemplate">
    <div class="coupon-item">
        <div class="coupon-info">
            <span class="coupon-name"></span>
            <span class="coupon-value"></span>
        </div>
        <button type="button" class="icon-btn danger" title="Remove coupon">
            <span class="icon">close</span>
        </button>
    </div>
</template>
<template id="noCouponsTemplate">
    <div class="no-coupons">
        <span class="icon">local_offer</span>
        <p>No coupons applied</p>
    </div>
</template>
<template id="discountItemTemplate">
    <div class="discount-item">
        <span class="discount-name"></span>
        <span class="discount-value"></span>
    </div>
</template>
<template id="noDiscountsTemplate">
    <div class="no-discounts">
        <span class="icon">percent</span>
        <p>No discounts applied</p>
    </div>
</template>

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
                    <input type="number" id="quantity" name="quantity" min="0" required>
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
                        <input type="text" id="coupon" placeholder="Enter coupon code" autocomplete="off">
                        <button type="button" class="btn btn-primary" onclick="pos.applyCoupon()">
                            Apply
                        </button>
                    </div>
                </div>
            </div>
            <!-- <div class="coupons-list">
                <h3>Applied Coupons</h3>
                <div class="coupons-items"> -->
                    <!-- Coupons will be dynamically added here -->
                <!-- </div>
            </div> -->
            <div class="discounts-list">
                <h3>Applied Discounts</h3>
                <div class="discounts-items">
                    <!-- Discounts will be dynamically added here -->
                </div>
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

<dialog id="loaderDialog">
    <div class="loader"></div>
</dialog>

<script src="/js/pos.js"></script>
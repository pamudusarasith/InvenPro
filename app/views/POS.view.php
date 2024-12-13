<div class="pos-body" id="touch">
    <?php App\View::render("components/Navbar") ?>
    <div class="content">
        <div class="items">
            <div class="row left-side-header">
                <div class="dropdown" id="menu">
                    <div class="icon-btn" onclick="toggleDropdown('menu')">
                        <span class="material-symbols-rounded  drop-down">menu</span>
                    </div>
                    <div class="dd-content" id="pos-menu">
                        <div class="dd-item">
                            <span>coupon code</span>
                        </div>
                        <div class="dd-item" id="customer-profile">
                            <span>Customer profile</span>
                        </div>
                        <div class="dd-item" id="new-customer">
                            <span>New Customer</span>
                        </div>
                        <div class="dd-item" id="returns">
                            <span>Returns</span>
                        </div>
                    </div>
                </div>

                <div id="prod-search" class="search-container items-search">
                    <div class="row search-bar">
                        <span class="material-symbols-rounded">search</span>
                        <input type="text" class="" placeholder="Search Products">
                    </div>
                </div>
            </div>
            <div class="items-results"></div>
            <?php App\View::render('components/AddItemForm') ?>
        </div>
        <div class="bill">
            <div class="bill-header row">
                <div class="orders">Order Details</div>
                <div id="add-customer" class="btn btn-primary sp8">
                    <span class="material-symbols-rounded">add</span>
                    Add customer
                </div>
            </div>
            <div class="customer-details" style="display: none;">
                <div class="customer-phone">
                    <div class="phone-input">
                        <span class="material-symbols-rounded">phone</span>
                        <input type="tel" id="phone" placeholder="Enter phone number"
                            pattern="[0-9]{10}" maxlength="10">
                        <button id="add-phone-no-button" class="add-button">Add</button>
                    </div>
                    <div id="phone-error" class="error-message"></div>
                </div>
            </div>
            <div class="bill-items">
                <div id="items-table" class="tbl" style="display: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <p class="No-items">No items</p>
            </div>
            <div class="bill-middle">
                <p class="sub-total">
                    <span class="sub-total-label">Sub-total</span>
                    <span class="sub-total-value">RS. 0.00</span>
                </p>
                <p class="sub-tot">
                    <span class="sub-total-label">Discount Sales</span>
                    <span class="sub-total-value">RS. 0.00</span>
                </p>

            </div>
            <div class="bill-footer">
                <p class="item-total">
                    <span class="item-total-label">TOTAL</span>
                    <span class="item-total-value">RS. 0.00</span>
                </p>
                <a href="javascript:void(0);" class="checkoutbtn">CHECKOUT</a>
            </div>
            <?php \App\View::render('components/CustomerForm'); ?>
            <?php \App\View::render('components/SearchavailabilityForm'); ?>
            <?php \App\View::render('components/ItemReturnForm'); ?>
            <?php \App\View::render('components/AdminAuthorizationForm'); ?>
            <?php \App\View::render('components/CustomerProfile'); ?>
        </div>
    </div>
</div>
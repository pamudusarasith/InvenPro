<div class="pos-body">
    <?php App\View::render("components/Navbar") ?>
    <div class="content">
        <div class="items">
            <div id="prod-search" class="search-container items-search">
                <div class="row search-bar">
                    <span class="material-symbols-rounded">search</span>
                    <input type="text" class="" placeholder="Search Products">
                </div>
            </div>
            <div class="items-results"></div>
            <?php App\View::render('components/AddItemForm') ?>
        </div>
        <div class="bill">
            <div class="bill-header row">
                <div class="dropdown" id="menu">
                    <div class="icon-btn" onclick="toggleDropdown('menu')">
                        <span class="material-symbols-rounded">menu</span>
                    </div>
                    <div class="dd-content">
                        <div class="dd-item" id="availability">
                            <span>Search Availability</span>
                        </div>
                        <div class="dd-item">
                            <span>Loylty Points</span>
                        </div>
                        <div class="dd-item">
                            <span>coupon code</span>
                        </div>
                    </div>
                </div>
                <div id="add-customer" class="btn btn-primary sp8">
                    <span class="material-symbols-rounded">add</span>
                    Add customer
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
            <div class="bill-footer">
                <p class="item-total">
                    <span class="item-total-label">TOTAL</span>
                    <span class="item-total-value">RS. 0.00</span>
                </p>
                <a href="javascript:void(0);" class="checkoutbtn">CHECKOUT</a>
            </div>
            <?php \App\View::render('components/CustomerForm'); ?>
            <!-- <?php \App\View::render('components/SearchavailabilityForm'); ?> -->
        </div>
    </div>
</div>
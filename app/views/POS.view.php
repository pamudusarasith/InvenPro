<div class="pos-body">
    <?php App\View::render("components/Navbar") ?>
    <div class="container">
        <div class="sidebar1">
            <div class="search-container">
                <input type="text" class="sidebar-search" placeholder="Search catagory...">
                <img src="/images/magnifying.png" alt="Search Icon" class="search-icon">
            </div>
            <div class="category-container">
                <div class="category" onclick="selectCategory('DAIRY PRODUCTS')">DAIRY PRODUCTS</div>
                <div class="category" onclick="selectCategory('BAKERY GOODS')">BAKERY GOODS</div>
                <div class="category" onclick="selectCategory('FRUITS & VEGETABLES')">FRUITS & VEGETABLES</div>
                <div class="category" onclick="selectCategory('MEAT & POULTRY')">MEAT & POULTRY</div>
                <div class="category" onclick="selectCategory('SNACKS & CONFECTIONERY')">SNACKS & CONFECTIONERY</div>
                <div class="category" onclick="selectCategory('SEAFOOD')">SEAFOOD</div>
                <div class="category" onclick="selectCategory('FROZEN FOODS')">FROZEN FOODS</div>
                <div class="category" onclick="selectCategory('BEVERAGES')">BEVERAGES</div>
                <div class="category" onclick="selectCategory('HOUSEHOLD ESSENTIALS')">HOUSEHOLD ESSENTIALS</div>
            </div>
        </div>

        <div class="main-content">
            <div class="customer-section">
                <div class="customer-icon">
                    <img src="/images/add-user.png" alt="Add Customer Icon">
                </div>
                <div class="add-customer">Add customer</div>
            </div>

            <div id="customerForm" class="customer-form" style="display: none;">
                <form id="customerDetailsForm">
                    <label for="customerName">Customer Name:</label>
                    <input type="text" id="customerName" name="customerName" required><br>

                    <label for="phoneNumber">Phone Number:</label>
                    <input type="tel" id="phoneNumber" name="phoneNumber" required><br>

                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required><br>

                    <label for="customerId">ID:</label>
                    <input type="text" id="customerId" name="customerId" required>

                    <button type="submit">Submit</button>
                </form>
            </div>

            <table>
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>fruit & nut ice corn</td>
                    <td>2</td>
                    <td>180.00</td>
                    <td>360.00</td>
                </tr>
                <tr>
                    <td>Candy Biscuits</td>
                    <td>2</td>
                    <td>345.00</td>
                    <td>690.00</td>
                </tr>
                <tr>
                    <td>Coca Cola</td>
                    <td>5</td>
                    <td>110.00</td>
                    <td>550.00</td>
                </tr>
                <tr>
                    <td>pencil case</td>
                    <td>1</td>
                    <td>169.00</td>
                    <td>169.00</td>
                </tr>
                <tr>
                    <td>suger</td>
                    <td>1</td>
                    <td>195.00</td>
                    <td>195.00</td>
                </tr>
                </tbody>
            </table>

            <div class="additional-actions">
                <div class="coupon-section">
                    <label for="coupon-code">Coupons</label>
                    <input id="coupon-code" type="text" placeholder="Enter coupon code">
                    <button class="apply-btn">Apply</button>
                </div>
                <div class="rewards-section">
                    <label for="loyalty-number">Customer Rewards</label>
                    <input id="loyalty-number" type="text" placeholder="Loyalty number">
                    <button class="redeem-btn">Redeem</button>
                </div>


                <div class="button-row">
                    <div class="loyalty-points">Total charge RS.2658.00</div>
                    <div class="charge-button">Prient Receipt</div>

                </div>
            </div>
        </div>
    </div>
</div>
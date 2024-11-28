<div class="body">
    <?php App\View::render("components/Navbar"); ?>
    <?php App\View::render("components/Sidebar"); ?>
    <div class="content">

        <div class="header-section">
            <h1 class="h1">Purchase Order Details</h1>
        </div>

        <!-- Details Section -->
        <div class="details-section">
                <p><span>Order ID:</span>001</p>
                <p><span>Supplier Name:</span>Maliban</p>
                <p><span>Product Category:</span>Biscuits</p>
                <p><span>Product:</span>Chocolate biscuits</p>
                <p><span>Quantity:</span>100 packets</p>
                <p><span>Amount:</span>10000</p>
                <p><span>Expected delivery date:</span>2024/11/29</p>
                <p><span>Contact No:</span>0712345678</p>
                <p><span>Special Notes:</span>No</p>
        </div>

        <!-- Action Buttons -->
        <div class="action-btn">
            <a href="/orders" class="btn-update">Update</a>
            <a href="/orders" class="btn-cancel">Cancel</a>
        </div>
    </div>
</div>



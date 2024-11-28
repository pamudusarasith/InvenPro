<!-- AddSupplierForm.view.php -->
<div class="container">
    <h2>Add New Order</h2>

    <form  id ="order-form" class="form-container" action="" method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="Order-id">Order ID</label>
                <input type="text" id="order-id" name="order-id" required>
            </div>
            <div class="form-group">
                <label for="supplier-name">Supplier Name</label>
                <input type="text" id="supplier-name" name="supplier-name" required>
            </div>
        </div>

        <!-- Product Categories and Products -->
        <div class="form-row">
            <div class="form-group">
                <label for="product-categories">Product Categories</label>
                <select id="product-categories" name="product-categories" required>
                    <option value="" disabled selected>Select a category</option>
                    <option value="Vegetables">Vegetable</option>
                    <option value="Fruits">Fruits</option>
                    <option value="Fish">Fish</option>
                    <option value="Meats">Meat</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div class="form-group">
                <label for="products">Products</label>
                <input type="text" id="products" name="products" required>
            </div>
        </div>

        <!-- Address -->
        <div class="form-row">
            <div class="form-group full-width">
                <label for="qty">Quantity</label>
                <input type="text" id="qty" name="qty" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="text" id="amount" name="amount" required>
            </div>
        </div>

        <!-- Contact No and Email -->
        <div class="form-row">
            <div class="form-group">
                <label for="contact-no">Supplier Contact No</label>
                <input type="text" id="contact-no" name="contact-no" required>
            </div>
            <div class="form-group">
                <label for="date">Expected Delivery Date</label>
                <input type="text" id="date" name="date" required>
            </div>
        </div>

        <!-- Special Notes -->
        <div class="form-row">
            <div class="form-group full-width">
                <label for="special-notes">Special Notes</label>
                <input type="text" id="special-notes" name="special-notes">
            </div>
        </div>

        <!-- Buttons -->
        <div class="form-actions">
            <button type="submit" class="save-btn">Save</button>
            <a href="/orders" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>


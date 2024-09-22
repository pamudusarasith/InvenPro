<!-- AddSupplierForm.view.php -->
<div class="container">
    <h2>Add New Supplier</h2>

    <form class="form-container" action="/suppliers/add" method="POST">
        <!-- Supplier ID and Supplier Name -->
        <div class="form-row">
            <div class="form-group">
                <label for="supplier-id">Supplier ID</label>
                <input type="text" id="supplier-id" name="supplier-id" required>
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
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
        </div>

        <!-- Contact No and Email -->
        <div class="form-row">
            <div class="form-group">
                <label for="contact-no">Contact No</label>
                <input type="text" id="contact-no" name="contact-no" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
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
            <a href="/suppliers" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>

<!-- CSS -->
<style>
    .container {
        width: 85%;
        margin: 0 auto;
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        font-family: Arial, sans-serif;
        margin-bottom: 50px;
        font-weight: bold;
    }

    .form-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .form-row {
        display: flex;
        justify-content: space-between;
        gap: 50px;
    }

    .form-group {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        flex: 1;
    }

    label {
        font-family: Arial, sans-serif;
        font-weight: bold;
        margin-bottom: 5px;
    }

    select {
        padding: 8px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f2f4f5; /* Light grey background */
        width: 100%;
        outline: none;
    }

    input[type="text"],
    input[type="email"] {
        padding: 8px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f2f4f5; /* Light grey background */
        width: 100%;
        outline: none;
    }

    input[type="text"]:focus,
    input[type="email"]:focus {
        border-color: #007bff;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
    }

    .save-btn {
        background-color:#28a745;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
    }

    .cancel-btn {
        background-color: #f0f0f0;
        color: #333;
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
    }

    .save-btn:hover {
        background-color: #0056b3;
    }

    .cancel-btn:hover {
        background-color: #d3d3d3;
    }

    /* Add spacing between fields */
    .form-row {
        margin-bottom: 15px;
    }
</style>

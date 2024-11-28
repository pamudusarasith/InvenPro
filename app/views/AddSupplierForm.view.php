<div class="container">
    <!-- Dynamically set the title -->
    <h2><?= isset($supplier) ? 'Edit Supplier Details' : 'Add New Supplier'; ?></h2>

    <!-- Dynamically set the form action -->
    <form id="sup-form" class="form-container" action="<?= isset($supplier) ? '/suppliers/update' : '/suppliers/add'; ?>" method="POST">
        <!-- Supplier ID and Supplier Name -->
        <div class="form-row">
            <div class="form-group">
                <label for="supplier-id">Supplier ID</label>
                <input 
                    type="text" 
                    id="supplier-id" 
                    name="supplier-id" 
                    value="<?= htmlspecialchars($supplier['supplierID'] ?? ''); ?>" 
                    <?= isset($supplier) ? 'readonly' : 'required'; ?> 
                >
            </div>
            <div class="form-group">
                <label for="supplier-name">Supplier Name</label>
                <input 
                    type="text" 
                    id="supplier-name" 
                    name="supplier-name" 
                    value="<?= htmlspecialchars($supplier['supplierName'] ?? ''); ?>" 
                    required>
            </div>
        </div>

        <!-- Product Categories and Products -->
        <div class="form-row">
            <div class="form-group">
                <label for="product-categories">Product Categories</label>
                <select id="product-categories" name="product-categories" required>
                    <option value="" disabled <?= empty($supplier['productCategories']) ? 'selected' : ''; ?>>Select a category</option>
                    <option value="Vegetables" <?= isset($supplier['productCategories']) && $supplier['productCategories'] == 'Vegetables' ? 'selected' : ''; ?>>Vegetables</option>
                    <option value="Fruits" <?= isset($supplier['productCategories']) && $supplier['productCategories'] == 'Fruits' ? 'selected' : ''; ?>>Fruits</option>
                    <option value="Fish" <?= isset($supplier['productCategories']) && $supplier['productCategories'] == 'Fish' ? 'selected' : ''; ?>>Fish</option>
                    <option value="Meats" <?= isset($supplier['productCategories']) && $supplier['productCategories'] == 'Meats' ? 'selected' : ''; ?>>Meats</option>
                    <option value="Others" <?= isset($supplier['productCategories']) && $supplier['productCategories'] == 'Others' ? 'selected' : ''; ?>>Others</option>
                </select>
            </div>
            <div class="form-group">
                <label for="products">Products</label>
                <input 
                    type="text" 
                    id="products" 
                    name="products" 
                    value="<?= htmlspecialchars($supplier['products'] ?? ''); ?>" 
                    required>
            </div>
        </div>

        <!-- Address -->
        <div class="form-row">
            <div class="form-group full-width">
                <label for="address">Address</label>
                <input 
                    type="text" 
                    id="address" 
                    name="address" 
                    value="<?= htmlspecialchars($supplier['address'] ?? ''); ?>" 
                    required>
            </div>
        </div>

        <!-- Contact No and Email -->
        <div class="form-row">
            <div class="form-group">
                <label for="contact-no">Contact No</label>
                <input 
                    type="text" 
                    id="contact-no" 
                    name="contact-no" 
                    value="<?= htmlspecialchars($supplier['contactNo'] ?? ''); ?>" 
                    required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($supplier['email'] ?? ''); ?>" 
                    required>
            </div>
        </div>

        <!-- Special Notes -->
        <div class="form-row">
            <div class="form-group full-width">
                <label for="special-notes">Special Notes</label>
                <input 
                    type="text" 
                    id="special-notes" 
                    name="special-notes" 
                    value="<?= htmlspecialchars($supplier['specialNotes'] ?? ''); ?>">
            </div>
        </div>

        <!-- Buttons -->
        <div class="form-actions">
            <button type="submit" class="save-btn">
                <?= isset($supplier) ? 'Update Supplier' : 'Save'; ?>
            </button>
            <a href="/suppliers" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>

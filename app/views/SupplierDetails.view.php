<div class="body">
    <?php App\View::render("components/Navbar"); ?>
    <?php App\View::render("components/Sidebar"); ?>
    <div class="content">

        <div class="header-section">
            <h1 class="h1">Supplier Details</h1>
        </div>

        <!-- Details Section -->
        <div class="details-section">
            <?php if (isset($supplier) && $supplier): ?>
                <p><span>Supplier ID:</span> <?= htmlspecialchars($supplier['supplierID']); ?></p>
                <p><span>Supplier Name:</span> <?= htmlspecialchars($supplier['supplierName']); ?></p>
                <p><span>Product Categories:</span> <?= htmlspecialchars($supplier['productCategories']); ?></p>
                <p><span>Products:</span> <?= htmlspecialchars($supplier['products']); ?></p>
                <p><span>Address:</span> <?= htmlspecialchars($supplier['address']); ?></p>
                <p><span>Email:</span> <?= htmlspecialchars($supplier['email']); ?></p>
                <p><span>Contact No:</span> <?= htmlspecialchars($supplier['contactNo']); ?></p>
                <p><span>Special Notes:</span> <?= htmlspecialchars($supplier['specialNotes']); ?></p>
            <?php else: ?>
                <p style="color:red;">No supplier found with the given ID.</p>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-btn">
            <a href="/suppliers" class="btn-update">Update</a>
            <a href="/suppliers" class="btn-cancel">Cancel</a>
        </div>
    </div>
</div>

<!-- CSS Styling -->
<style>
/* Supplier Details Page Styles */
.h1{
    margin-bottom: 20px;
}

.details-section p {
    padding: 6px;
    font-size: 16px;
    margin-bottom: 10px;
}

.details-section span {
    font-weight: bold;
}

.action-btn {
    display: flex;
    justify-content: flex-end; /* Aligns the buttons to the right */
    gap: 15px; /* Adds space between buttons */
    margin-top: 50px;
}

/* Styling for Cancel button */
.btn-cancel {
    background-color: #6c757d;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
}

.btn-cancel:hover {
    background-color: #5a6268;
}

/* Styling for Update button */
.btn-update {
    background-color: #28a745;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
}

.btn-update:hover {
    background-color: #218838;

}
</style>

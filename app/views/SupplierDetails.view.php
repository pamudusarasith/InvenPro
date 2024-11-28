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



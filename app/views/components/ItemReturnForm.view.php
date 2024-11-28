<dialog id="ItemReturn-form-modal" class="modal">
    <div class="modal-content">
        <div class="row">
            <span class="material-symbols-rounded modal-close-btn modal-close">close</span>
        </div>
        <h1 class="modal-header">Return Item Details</h1>
        <form id="ItemReturn-form" action="/ItemReturn/new" method="post">
            <label for="customer-name">Customer Name</label>
            <input id="product-code" type="text" name="code" required>
            <label for="productCode">Product Code</label>
            <input type="text" id="productCode" name="productCode" required>
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" required>
            <label for="reason">Return Reason</label>
            <select id="reason" name="reason" required>
                <option value="">Select Reason</option>
                <option value="Damaged">Damaged</option>
                <option value="Expired">Expired</option>
                <option value="Customer Request">Customer Request</option>
            </select>
            <div class="modal-error">
                <span class="material-symbols-rounded">error</span>
                <span id="error-msg" class="error-msg"></span>
            </div>
            <div class="row modal-action-btns">
                <span class="loader" style="margin: 24px 12px 0px; font-size: 12px"></span>
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                <button type="submit" class="btn btn-primary" id="autho">Request Return</button>
            </div>
        </form>
    </div>
</dialog>
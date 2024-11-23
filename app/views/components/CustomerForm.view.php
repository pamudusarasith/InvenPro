<div id="customer-form-modal" class="modal">
    <div class="modal-content">
        <div class="row">
            <span class="material-symbols-rounded modal-close-btn">close</span>
        </div>
        <h1 class="modal-header">Add New Customer</h1>
        <form id="customer-form" action="/customer/new" method="post">
            <label for="customer-name">Full Name</label>
            <input id="customer-name" type="text" name="name" required>
            <label for="customer-phone">Phone Number</label>
            <input id="customer-phone" type="text" name="phone" required>
            <label for="customer-id">ID number</label>
            <input id="customer-id" type="text" name="id" required>
            <label for="customer-address">Address</label>
            <textarea id="customer-address" name="address" rows="3" required></textarea>
            <label for="customer-country">Country*</label>

            <div class="model-error">
                <span class="material-symbols-rounded">error</span>
                <span id="error-msg" class="error-msg"></span>
            </div>
            <div class="row modal-action-btns">
                <span class="loader" style="margin: 24px 12px 0px; font-size: 12px"></span>
                <button type="button" class="btn btn-secondary cancel-btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </form>
    </div>
</div>
<dialog id="batch-edit-form-modal" class="modal">
    <div class="row">
        <span class="material-symbols-rounded modal-close-btn modal-close">close</span>
    </div>
    <h1 class="modal-header">Edit Batch</h1>
    <form id="batch-edit-form" action="/batch/new" method="post">
        <input id="prod-id" type="text" name="id" hidden>
        <input id="prod-bno" type="text" name="bno" hidden>
        <label for="prod-price">Price</label>
        <input id="prod-price" type="number" step="0.01" min="0" name="price" required>
        <label for="prod-qty">Quantity</label>
        <input id="prod-qty" type="number" step="0.001" min="0" name="qty" required>
        <label for="prod-mfd">Manufacture Date</label>
        <input id="prod-mfd" type="date" name="mfd" required>
        <label for="prod-exp">Expiry Date</label>
        <input id="prod-exp" type="date" name="exp" required>
        <div class="modal-error">
            <span class="material-symbols-rounded">error</span>
            <span id="error-msg" class="error-msg"></span>
        </div>
        <div class="row modal-action-btns">
            <span class="loader" style="margin: 24px 12px 0px; font-size: 12px"></span>
            <button type="button" class="btn btn-secondary modal-close">Cancel</button>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</dialog>

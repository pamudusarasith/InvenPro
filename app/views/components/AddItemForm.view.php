<dialog id="item-form-modal" class="modal">
    <div class="row">
        <span class="material-symbols-rounded modal-close-btn modal-close">close</span>
    </div>
    <h1 class="modal-header">Add New Item</h1>
    <form id="item-form" action="" method="">
        <label for="item-qty">Quantity</label>
        <input id="item-qty" type="number" step="0.001" min="0" name="qty" required>
        <div class="modal-error">
            <span class="material-symbols-rounded">error</span>
            <span id="error-msg" class="error-msg"></span>
        </div>
        <div class="row modal-action-btns">
            <button type="button" class="btn btn-secondary modal-close">Cancel</button>
            <button type="submit" class="btn btn-primary">Add</button>
        </div>
    </form>
</dialog>
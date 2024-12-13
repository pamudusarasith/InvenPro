<dialog id="Authorization-form-modal" class="modal">
    <div class="row">
        <span class="material-symbols-rounded modal-close-btn modal-close">close</span>
    </div>
    <!-- <h1 class="modal-header">Approve return product</h1> -->
    <form id="Authorization-form" action="/Authorization/new" method="post">
        <label for="customer-phone">Enter Admin Approved Code</label>
        <input id="customer-phone" type="text" name="phone" required>
        <div class="modal-error">
            <span class="material-symbols-rounded">error</span>
            <span id="error-msg" class="error-msg"></span>
        </div>
        <div class="row modal-action-btns">
            <button type="submit" class="btn btn-primary">Return Approved</button>
        </div>
    </form>
</dialog>
<div id="category-form-modal" class="modal">
    <div class="modal-content">
        <div class="row">
            <span class="material-symbols-rounded modal-close-btn">close</span>
        </div>
        <h1 class="modal-header">Create New Category</h1>
        <form id="category-form" action="/category/new" method="post">
            <label for="category-name">Name</label>
            <input id="category-name" type="text" name="name" required>
            <div class="modal-error">
                <span class="material-symbols-rounded">error</span>
                <span id="error-msg" class="error-msg"></span>
            </div>
            <div class="row modal-action-btns">
                <span class="loader" style="margin: 24px 12px 0px; font-size: 12px"></span>
                <button type="button" class="btn btn-secondary modal-cancel-btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

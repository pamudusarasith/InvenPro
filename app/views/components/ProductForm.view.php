<dialog id="prod-form-modal" class="modal">
    <div class="row">
        <span class="material-symbols-rounded modal-close-btn modal-close">close</span>
    </div>
    <h1 class="modal-header">Add New Product</h1>
    <form id="prod-form" action="/products/new" method="post">
        <label for="prod-id">Product ID</label>
        <input id="prod-id" type="text" name="id" required>
        <label for="prod-name">Name</label>
        <input id="prod-name" type="text" name="name" required>
        <label for="prod-desc">Description</label>
        <textarea id="prod-desc" rows="4" name="description"></textarea>
        <label for="prod-unit">Measuring Unit</label>
        <select id="prod-unit" name="unit" required>
            <option value="items">Items</option>
            <option value="kg">Kilogram - kg</option>
            <option value="l">Liters - l</option>
        </select>
        <label for="prod-category">Categories</label>
        <div id="category-search" class="search-container">
            <div class="row search-bar">
                <span class="material-symbols-rounded">search</span>
                <input type="text" class="" placeholder="Search categories">
            </div>
        </div>
        <div id="category-chips" class="chips"></div>
        <label for="prod-img">Image</label>
        <input id="prod-img" type="file" name="image" accept="image/png" required>
        <div class="modal-error">
            <span class="material-symbols-rounded">error</span>
            <span id="error-msg" class="error-msg"></span>
        </div>
        <div class="row modal-action-btns">
            <span class="loader" style="margin: 24px 12px 0px; font-size: 12px"></span>
            <button type="button" class="btn btn-secondary modal-close">Cancel</button>
            <button type="submit" class="btn btn-primary">Add</button>
        </div>
    </form>
</dialog>

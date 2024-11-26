<dialog id="discount-form-modal" class="modal">
  <div class="row">
    <span class="material-symbols-rounded modal-close-btn modal-close">close</span>
  </div>
  <h1 class="modal-header">Create New Discount</h1>
  <form id="discount-form" action="/discounts/new" method="post">
    <div class="form-section">
      <h4>Discount Details</h4>
      <label for="discount-name">Discount Name</label>
      <input id="discount-name" type="text" name="name" required>

      <label for="discount-type">Discount Type</label>
      <select id="discount-type" name="type" required>
        <option value="product">Product</option>
        <option value="category">Category</option>
        <option value="bill">Bill</option>
        <option value="bundle">Bundle</option>
      </select>

      <label for="discount-description">Description</label>
      <textarea id="discount-description" name="description" rows="3"></textarea>
    </div>

    <div class="form-section">
      <h4>Validity Period</h4>
      <div class="date-range">
        <div>
          <label for="discount-valid-from">Valid From</label>
          <input id="discount-valid-from" type="datetime-local" name="valid_from" required>
        </div>
        <div>
          <label for="discount-valid-until">Valid Until</label>
          <input id="discount-valid-until" type="datetime-local" name="valid_until" required>
        </div>
      </div>
    </div>

    <div class="form-section">
      <h4>Discount Values & Criteria</h4>
      <div id="value-section">
        <label for="discount-value">Discount Value</label>
        <input id="discount-value" type="number" name="value" step="0.01" min="0" required>
        <label class="checkbox-label">
          <input id="is-percentage" type="checkbox" name="is_percentage">
          Percentage
        </label>
      </div>

      <div id="category-section" style="display:none;">
        <label for="category-search">Categories</label>
        <div id="category-search" class="search-container">
          <div class="search-bar">
            <span class="material-symbols-rounded">search</span>
            <input type="text" placeholder="Search categories">
          </div>
        </div>
        <div id="category-chips" class="chips"></div>
      </div>

      <div id="bundle-section" style="display:none;">
        <label for="bundle-name">Bundle Name</label>
        <input id="bundle-name" type="text" name="bundle_name" required>

        <label for="bundle-description">Bundle Description</label>
        <textarea id="bundle-description" name="bundle_description" rows="3"></textarea>

        <label for="product-search">Products in Bundle</label>
        <div id="product-search" class="search-container">
          <div class="search-bar">
            <span class="material-symbols-rounded">search</span>
            <input type="text" placeholder="Search products">
          </div>
        </div>

        <div id="new-bundle-product" style="display: none;">
          <div class="row bundle-product-edit">
            <div class="column">
              <label for="bundle-product-qty">Quantity</label>
              <input id="bundle-product-qty" type="number" name="qty" step="0.001" min="0">
            </div>
          </div>
          <div class="row action-btns">
            <button id="bundle-product-close" type="button" class="btn btn-secondary">
              <span class="material-symbols-rounded">close</span>
            </button>
            <button id="bundle-product-done" type="button" class="btn btn-secondary">
              <span class="material-symbols-rounded">check</span>
            </button>
          </div>
        </div>

        <div id="bundles-table" class="tbl" style="display: none;">
          <table>
            <tr>
              <th>Product</th>
              <th>Quantity</th>
              <th></th>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <div class="form-section">
      <h4>Constraints</h4>
      <div class="constraints">
        <label class="checkbox-label">
          <input type="checkbox" name="is_loyalty_only">
          Loyalty Members Only
        </label>
        <label class="checkbox-label">
          <input type="checkbox" name="combinable">
          Can be combined with other discounts
        </label>
        <div class="limit-fields">
          <div>
            <label for="min-purchase">Minimum Purchase (Rs.)</label>
            <input id="min-purchase" type="number" name="min_purchase" step="0.01" min="0">
          </div>
          <div>
            <label for="max-discount">Maximum Discount (Rs.)</label>
            <input id="max-discount" type="number" name="max_discount" step="0.01" min="0">
          </div>
          <div>
            <label for="usage-limit">Usage Limit</label>
            <input id="usage-limit" type="number" name="usage_limit" min="0">
          </div>
          <div>
            <label for="priority">Priority</label>
            <input id="priority" type="number" name="priority" min="1">
          </div>
        </div>
      </div>
    </div>

    <div class="modal-error">
      <span class="material-symbols-rounded">error</span>
      <span id="error-msg" class="error-msg"></span>
    </div>

    <div class="row modal-action-btns">
      <span class="loader" style="margin: 24px 12px 0; font-size: 12px"></span>
      <button type="button" class="btn btn-secondary modal-close">Cancel</button>
      <button type="submit" class="btn btn-primary">Add</button>
    </div>
  </form>
</dialog>

<dialog id="discount-form-modal" class="modal">
  <div class="row">
    <span class="material-symbols-rounded modal-close-btn modal-close">close</span>
  </div>
  <h1 class="modal-header">Add Discount</h1>
  <form id="discount-form">
    <label for="disc-type">Type</label>
    <select id="disc-type" name="type" required>
      <?php if (isset($types)) {
        foreach ($types as $type) : ?>
          <option value="<?= $type ?>"><?= $type ?></option>
      <?php endforeach;
      } ?>
    </select>

    <label for="disc-type">Name</label>
    <input id="disc-type" type="text" name="name" required>

    <label for="disc-desc">Description</label>
    <textarea id="disc-desc" rows="4" name="description"></textarea>

    <label for="disc-from">Valid From</label>
    <input id="disc-from" type="datetime-local" name="from" required>

    <label for="disc-thru">Valid Thru</label>
    <input id="disc-thru" type="datetime-local" name="thru" required>

    <label for="disc-min-bill-amount-chkbx">
      <input id="disc-min-bill-amount-chkbx" type="checkbox" name="addMinAmount">
      Add Minimum Bill Amount
    </label>

    <label for="disc-min-bill-amount">Minimum Bill Amount (Rs.)</label>
    <input id="disc-min-bill-amount" type="number" name="minBillAmount" step="0.01" min="0">

    <label for="disc-max-disc-amount-chkbx">
      <input id="disc-max-disc-amount-chkbx" type="checkbox" name="addMaxAmount">
      Add Maximum Discount Amount
    </label>

    <label for="disc-max-disc-amount">Maximum Discount Amount (Rs.)</label>
    <input id="disc-max-disc-amount" type="number" name="maxDiscAmount" step="0.01" min="0">

    <label for="disc-loyalty">
      <input id="disc-loyalty" type="checkbox" name="loyalty" value="1">
      Loyalty Only
    </label>

    <label for="disc-combinable">
      <input id="disc-combinable" type="checkbox" name="combinable" value="1">
      Combinable
    </label>

    <div id="cond-amount" class="condition">
      <label for="disc-amount-type">Amount Type</label>
      <select id="disc-amount-type" name="amountType">
        <option value="fixed">Fixed</option>
        <option value="percentage">Percentage</option>
      </select>
      <label for="disc-amount">Amount</label>
      <input id="disc-amount" type="number" name="amount" step="0.01" min="0" max="100">
    </div>

    <div id="cond-category" class="condition">
      <label for="prod-category">Categories</label>
      <div id="category-search" class="search-container">
        <div class="row search-bar">
          <span class="material-symbols-rounded">search</span>
          <input type="text" class="" placeholder="Search categories">
        </div>
      </div>
      <div id="category-chips" class="chips"></div>
    </div>

    <div id="cond-trigs" class="condition">
      <label for="prod-search">Discount Activation Criteria</label>
      <div id="prod-search" class="search-container">
        <div class="row search-bar">
          <span class="material-symbols-rounded">search</span>
          <input type="text" class="" placeholder="Search products">
        </div>
      </div>

      <div id="new-trig" style="display: none;">
        <div class="row trig-edit">
          <div class="column">
            <label for="trig-min-qty">Minimum Quantity</label>
            <input id="trig-min-qty" type="number" name="minQty" step="0.001" min="0">
          </div>
          <div class="column">
            <label for="trig-max-qty">Maximum Quantity</label>
            <input id="trig-max-qty" type="number" name="maxQty" step="0.001" min="0">
          </div>
        </div>
        <div class="row action-btns">
          <button id="trig-close" type="button" class="btn btn-secondary">
            <span class="material-symbols-rounded">close</span>
          </button>
          <button id="trig-done" type="button" class="btn btn-secondary">
            <span class="material-symbols-rounded">check</span>
          </button>
        </div>
      </div>

      <div id="trigs-table" class="tbl" style="display: none;">
        <table>
          <tr>
            <th>Product</th>
            <th>Min Qty</th>
            <th>Max Qty</th>
            <th></th>
          </tr>
        </table>
      </div>
    </div>

    <div id="cond-discs" class="condition">
      <label for="prod-search">Discount Details</label>
      <div id="prod-search" class="search-container">
        <div class="row search-bar">
          <span class="material-symbols-rounded">search</span>
          <input type="text" class="" placeholder="Search products">
        </div>
      </div>

      <div id="new-disc" style="display: none;">
        <div class="row disc-edit">
          <div class="column">
            <label for="disc-min-qty">Minimum Quantity</label>
            <input id="disc-min-qty" type="number" name="minQty" step="0.001" min="0">
          </div>
          <div class="column">
            <label for="disc-max-qty">Maximum Quantity</label>
            <input id="disc-max-qty" type="number" name="maxQty" step="0.001" min="0">
          </div>
        </div>
        <div class="row action-btns">
          <button id="disc-close" type="button" class="btn btn-secondary">
            <span class="material-symbols-rounded">close</span>
          </button>
          <button id="disc-done" type="button" class="btn btn-secondary">
            <span class="material-symbols-rounded">check</span>
          </button>
        </div>
      </div>

      <div id="discs-table" class="tbl" style="display: none;">
        <table>
          <tr>
            <th>Product</th>
            <th>Min Qty</th>
            <th>Max Qty</th>
            <th></th>
          </tr>
        </table>
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

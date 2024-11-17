<div id="discount-form-modal" class="modal">
  <div class="modal-content">
    <div class="row">
      <span class="material-symbols-rounded close-btn">close</span>
    </div>
    <h1 class="modal-header">Add Discount</h1>
    <form id="discount-form">
      <label for="disc-type">Type</label>
      <select id="disc-type" name="type" required>
        <?php if (isset($types)) {
          foreach ($types as $type) : ?>
            <option value="<?= $type["id"] ?>"><?= $type["name"] ?></option>
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
      <div class="row" style="justify-content: space-evenly">
        <label for="disc-loyalty">
          <input id="disc-loyalty" type="checkbox" name="loyalty" value="1">
          Loyalty Only
        </label>
        <label for="disc-combinable">
          <input id="disc-combinable" type="checkbox" name="combinable" value="1">
          Combinable
        </label>
      </div>
      <label for="prod-search">Add Conditions</label>
      <div id="prod-search" class="search-container">
        <div class="row search-bar">
          <span class="material-symbols-rounded">search</span>
          <input type="text" class="" placeholder="Search Products">
        </div>
      </div>
      <div class="error">
        <span class="material-symbols-rounded">error</span>
        <span id="error-msg" class="error-msg"></span>
      </div>
      <div class="row action-btns">
        <span class="loader" style="margin: 24px 12px 0; font-size: 12px"></span>
        <button id="close" type="button" class="btn btn-secondary">Cancel</button>
        <button id="submit-btn" type="submit" class="btn btn-primary">Add</button>
      </div>
    </form>
  </div>
</div>
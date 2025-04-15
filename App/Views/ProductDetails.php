<?php

use App\Services\RBACService;

?>
<link rel="stylesheet" href="/css/productdetails.css">
<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <div class="profile-container">
      <div class="details-header">
        <div class="details-header-left">
          <img src="<?= htmlspecialchars($product['image_path']) ?>"
            alt="<?= htmlspecialchars($product['product_name']) ?>"
            class="product-image">
          <div class="profile-info">
            <div class="details-title">
              <h1 class="title-name"><?= htmlspecialchars($product['product_name']) ?></h1>
              <span class="badge <?= $product['deleted_at'] ? 'danger' : 'success' ?>">
                <?= $product['deleted_at'] ? 'Inactive' : 'Active' ?>
              </span>
            </div>
            <div class="details-meta">
              <div class="meta-item">
                <span class="icon">qr_code</span>
                <span class="meta-text"><?= htmlspecialchars($product['product_code']) ?></span>
              </div>
              <div class="meta-item">
                <span class="icon">straighten</span>
                <span class="meta-text"><?= htmlspecialchars($product['unit_name']) ?></span>
              </div>
            </div>
          </div>
        </div>
        <div class="profile-header-right">
          <div class="edit-actions">
            <button class="btn btn-secondary" onclick="cancelEdit()">
              <span class="icon">close</span>
              Cancel
            </button>
            <button class="btn btn-primary" onclick="saveChanges()">
              <span class="icon">save</span>
              Save
            </button>
          </div>
          <?php if (RBACService::hasPermission('edit_product')): ?>
            <div class="dropdown">
              <button class="dropdown-trigger icon-btn" title="More options">
                <span class="icon">more_vert</span>
              </button>
              <div class="dropdown-menu">
                <button class="dropdown-item" onclick="enableEditing()">
                  <span class="icon">edit</span>
                  Edit Product
                </button>
                <button class="dropdown-item danger" onclick="deleteProduct(<?= $product['id'] ?>)">
                  <span class="icon">delete</span>
                  Delete Product
                </button>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-header">
            <span class="icon">inventory_2</span>
            <span class="stat-label">Total Stock</span>
          </div>
          <?php
          $totalStock = array_sum(array_map(function ($batch) {
            return strtotime($batch['expiry_date']) > time() || !$batch['expiry_date'] ? $batch['current_quantity'] : 0;
          }, $product['batches']));
          ?>
          <div class="stat-value"><?= number_format($totalStock, 3) ?> <?= htmlspecialchars($product['unit_symbol']) ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="icon">trending_up</span>
            <span class="stat-label">Sales This Month</span>
          </div>
          <div class="stat-value"><?= number_format($stats['monthly_sales'], 3) ?> <?= htmlspecialchars($product['unit_symbol']) ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="icon">payments</span>
            <span class="stat-label">Out of Stock Batches</span>
          </div>
          <?php
          $outOfStockCount = count(array_filter($product['batches'], function ($batch) {
            return $batch['current_quantity'] <= 0;
          }));
          ?>
          <div class="stat-value"><?= $outOfStockCount ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="icon">shopping_cart</span>
            <span class="stat-label">Expired Batches</span>
          </div>
          <?php
          $expiredBatchesCount = count(array_filter($product['batches'], function ($batch) {
            return $batch['expiry_date'] && strtotime($batch['expiry_date']) < time();
          }));
          ?>
          <div class="stat-value"><?= $expiredBatchesCount ?></div>
        </div>
      </div>
    </div>

    <div class="tab-nav">
      <button class="tab-btn active" onclick="switchTab('overview')">Overview</button>
      <button class="tab-btn" onclick="switchTab('batches')">Stock Batches</button>
      <button class="tab-btn" onclick="switchTab('suppliers')">Suppliers</button>
      <button class="tab-btn" onclick="switchTab('history')">History</button>
    </div>

    <form id="details-form" method="POST" action="/products/<?= $product['id'] ?>/update">
      <div id="overview" class="tab-content active">
        <div class="card">
          <h3>Product Information</h3>
          <div class="content form-grid">
            <div class="form-field">
              <label for="product_code">Product Code</label>
              <input type="text" id="product_code" name="product_code"
                value="<?= htmlspecialchars($product['product_code']) ?>"
                disabled>
            </div>
            <div class="form-field">
              <label for="product_name">Product Name</label>
              <input type="text" id="product_name" name="product_name"
                value="<?= htmlspecialchars($product['product_name']) ?>"
                disabled>
            </div>
            <div class="form-field">
              <label for="unit">Unit of Measure</label>
              <select id="unit" name="unit_id" disabled>
                <?php foreach ($units as $unit): ?>
                  <option value="<?= $unit['id'] ?>"
                    <?= $unit['id'] === $product['unit_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($unit['unit_name']) ?> (<?= htmlspecialchars($unit['unit_symbol']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-field">
              <label for="image">Product Image</label>
              <input type="file" id="image" name="image" accept="image/*" disabled>
            </div>
            <div class="form-field span-2">
              <label for="description">Description</label>
              <textarea id="description" name="description" rows="3" disabled><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="form-field span-2">
              <label for="product-categories">Categories</label>
              <div id="product-categories" class="search-bar" style="display: none;">
                <span class="icon">search</span>
                <input type="text" placeholder="Search Categories..." oninput="searchCategories(event)">
                <div class="search-results"></div>
              </div>
            </div>
            <div class="form-field span-2 chip-container">
              <?php foreach ($product['categories'] as $category): ?>
                <div class="chip">
                  <?= htmlspecialchars($category['category_name']) ?>
                  <input type="hidden" name="categories[]" value="<?= $category['id'] ?>">
                  <button type="button" class="chip-delete" title="Remove Category" style="display: none;">
                    <span class="icon">close</span>
                  </button>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="form-field">
              <label for="reorder-level">Reorder Level</label>
              <input type="number" id="reorder-level" name="reorder_level"
                value="<?= $product['reorder_level'] ?>"
                disabled>
            </div>
            <div class="form-field">
              <label for="reorder-quantity">Reorder Quantity</label>
              <input type="number" id="reorder-quantity" name="reorder_quantity"
                value="<?= $product['reorder_quantity'] ?>"
                disabled>
            </div>
          </div>
        </div>
      </div>

      <div id="batches" class="tab-content">
        <div class="card">
          <h3>Stock Batches</h3>
          <div class="content">
            <div class="batch-grid">
              <?php foreach ($product['batches'] as $batch): ?>
                <div class="batch-card card glass" data-batch-id="<?= $batch['id'] ?>">
                  <div class="batch-header">
                    <span class="batch-title"><?= htmlspecialchars($batch['batch_code']) ?></span>
                    <div>
                      <?php if (RBACService::hasPermission('edit_batch')): ?>
                        <button type="button" class="icon-btn" title="Edit Batch" onclick="openEditBatchDetailsDialog(event)">
                          <span class=" icon">edit</span>
                        </button>
                      <?php endif; ?>
                      <?php if (RBACService::hasPermission('delete_batch')): ?>
                        <button type="button" class="icon-btn danger" title="Delete Batch" onclick="openEditBatchDetailsDialog(event)">
                          <span class=" icon">delete</span>
                        </button>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="batch-status">
                    <?php if ($batch['current_quantity'] <= 0): ?>
                      <span class="badge danger">Out of Stock</span>
                    <?php elseif ($batch['expiry_date'] && strtotime($batch['expiry_date']) < time()): ?>
                      <span class="badge warning">Expired</span>
                    <?php else: ?>
                      <span class="badge success">Active</span>
                    <?php endif; ?>
                  </div>
                  <div class="batch-details">
                    <div class="batch-info">
                      <span class="info-label">Current Stock</span>
                      <span class="info-value current-stock"><?= number_format($batch['current_quantity'], 3) ?> <?= htmlspecialchars($product['unit_symbol']) ?></span>
                    </div>
                    <div class="batch-info">
                      <span class="info-label">Unit Price</span>
                      <span class="info-value unit-value">Rs. <?= number_format($batch['unit_price'], 2) ?></span>
                    </div>
                    <div class="batch-info">
                      <span class="info-label">Manufacturing Date</span>
                      <span class="info-value mfg"><?= $batch['manufactured_date'] ? date('M d, Y', strtotime($batch['manufactured_date'])) : '-' ?></span>
                    </div>
                    <div class="batch-info">
                      <span class="info-label">Expiry Date</span>
                      <span class="info-value exp"><?= $batch['expiry_date'] ? date('M d, Y', strtotime($batch['expiry_date'])) : '-' ?></span>
                    </div>
                  </div>
                  <?php if ($batch['current_quantity'] > 0 && $batch['current_quantity'] <= $product['reorder_level']): ?>
                    <div class="stock-warning <?= $batch['current_quantity'] <= $product['reorder_level'] / 2 ? 'stock-critical' : '' ?>">
                      <span class="icon">warning</span>
                      <span>Stock is below reorder level</span>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <div id="suppliers" class="tab-content">
        <div class="card">
          <h3>Product Suppliers</h3>
          <div class="content">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Supplier Name</th>
                  <th>Contact Person</th>
                  <th>Preferred</th>
                  <th>Last Order</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (empty($suppliers)) {
                  echo '<tr><td colspan="6" style="text-align: center;">No suppliers found</td></tr>';
                } else {
                  foreach ($suppliers as $suppliers): ?>
                    <tr>
                      <td><?= htmlspecialchars($suppliers['supplier_name']) ?></td>
                      <td><?= htmlspecialchars($suppliers['contact_person']) ?></td>
                      <td>
                        <span class="badge <?= $suppliers['is_preferred_supplier'] ? 'success' : '' ?>">
                          <?= $suppliers['is_preferred_supplier'] ? 'Yes' : 'No' ?>
                        </span>
                      </td>
                      <td><?= $suppliers['last_order'] ? date('M d, Y', strtotime($suppliers['last_order'])) : '-' ?></td>
                      <td>
                        <button type="button" class="icon-btn"
                          onclick="window.location.href='/suppliers/<?= $suppliers['id'] ?>'">
                          <span class="icon">visibility</span>
                        </button>
                      </td>
                    </tr>
                <?php endforeach;
                } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div id="history" class="tab-content">
        <!-- Similar structure for transaction history -->
      </div>
    </form>
  </div>
</div>

<!-- Batch Details dialog-->
<dialog id="batchDetailsDialog" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2></h2>
      <button type="button" class="close-btn" onclick="closeEditBatchDetailsDialog()">
        <span class="icon">close</span>
      </button>
    </div>
    <form id="batchDetailsForm" class="modal-body" method="post">
      <div class="form-grid">
        <input type="text" name="product_id" value="<?= $product['id'] ?>" hidden>
        <div class="form-field">
          <label for="po_number">Purchase Order Number</label>
          <input type="text" name="po_number">
        </div>
        <div class="form-field">
          <label for="batch_code">Batch Code</label>
          <input type="text" name="batch_code">
        </div>
        <div class="form-field">
          <label for="manufactured_date">Manufacturing Date</label>
          <input type="date" name="manufactured_date">
        </div>
        <div class="form-field">
          <label for="expiry_date">Expiry Date</label>
          <input type="date" name="expiry_date">
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeEditBatchDetailsDialog()">
          Cancel
        </button>
        <button type="submit" class="btn btn-primary">
          Save
        </button>
      </div>
    </form>
  </div>
</dialog>

<?php if (RBACService::hasPermission('edit_product')): ?>
  <dialog id="addBatchModal" class="modal">
    <!-- Add batch form dialog content -->
  </dialog>
<?php endif; ?>

<!-- Message popup and script section similar to SupplierDetails.php -->
<script>
  function switchTab(tabId) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

    document.querySelector(`.tab-btn[onclick*="${tabId}"]`).classList.add('active');
    document.getElementById(tabId).classList.add('active');
  }

  function enableEditing() {
    // Add edit mode class to header
    document.querySelector('.details-header').classList.add('edit-mode');

    // Enable all form inputs
    document.querySelectorAll('.form-field :is(input, select, textarea)').forEach(input => {
      input.disabled = false;
    });

    document.getElementById('product-categories').style.display = 'flex';

    document.querySelectorAll('.chip-delete').forEach(btn => {
      btn.style.display = 'inline-flex';
      btn.onclick = function() {
        btn.parentElement.remove();
      };
    });

    // Scroll to form
    document.querySelector('.tab-content.active').scrollIntoView({
      behavior: 'smooth'
    });
  }

  function cancelEdit() {
    if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
      location.reload();
    }
  }

  function saveChanges() {
    if (!confirm('Are you sure you want to save these changes?')) {
      return;
    }
    document.getElementById('details-form').submit();
  }

  function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product?')) {
      return;
    }
    window.location.href = `/products/${productId}/delete`;
  }

  function createCategoryChip(category) {
    const chipContainer = document.querySelector(
      "#details-form .chip-container"
    );
    const chip = document.createElement("div");
    chip.classList.add("chip");
    chip.innerHTML = category.category_name;

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "categories[]";
    input.value = category.id;
    chip.appendChild(input);

    const chipDelete = document.createElement("button");
    chipDelete.type = "button";
    chipDelete.classList.add("chip-delete");
    chipDelete.innerHTML = `<span class="icon">close</span>`;
    chipDelete.addEventListener("click", () => {
      chip.remove();
    });
    chip.appendChild(chipDelete);

    chipContainer.appendChild(chip);
  }

  function renderCategorySearchResults(results) {
    const searchResults = document.querySelector(
      "#details-form #product-categories .search-results"
    );
    searchResults.innerHTML = "";

    results.forEach((category) => {
      const button = document.createElement("button");
      button.type = "button";
      button.classList.add("search-result");
      button.innerHTML = `<span>${category.category_name}</span>`;
      button.addEventListener("click", () => {
        createCategoryChip(category);
        searchResults.innerHTML = "";
        document.querySelector("#details-form #product-categories input").value =
          "";
      });

      searchResults.appendChild(button);
    });
  }

  async function searchCategories(e) {
    const query = e.target.value;

    if (!query) {
      renderCategorySearchResults([]);
      return;
    }

    const res = await fetch(`/api/category/search?q=${query}`);
    const data = await res.json();

    if (!data.success) {
      openPopupWithMessage(data.message);
      return;
    }

    renderCategorySearchResults(data.data);
  }

  function openEditBatchDetailsDialog(e) {
    document.querySelector('#batchDetailsDialog .modal-header h2').innerHTML = 'Edit Batch Details';
    const batchCard = e.target.closest('.batch-card');
    const batchCode = batchCard.querySelector('.batch-title').textContent;
    const mfgDate = new Date(batchCard.querySelector('.mfg').textContent).toISOString().split('T')[0];
    const expDate = new Date(batchCard.querySelector('.exp').textContent).toISOString().split('T')[0];

    const form = document.getElementById('batchDetailsForm');
    form.action = `/batch/${batchCard.dataset.batchId}/update`;
    form.querySelector('input[name="po_number"]').parentElement.style.display = 'none';
    form.querySelector('input[name="batch_code"]').value = batchCode;
    form.querySelector('input[name="manufactured_date"]').value = mfgDate;
    form.querySelector('input[name="expiry_date"]').value = expDate;
    document.getElementById('batchDetailsDialog').showModal();
  }


  function closeEditBatchDetailsDialog() {
    document.getElementById('batchDetailsForm').reset();
    document.getElementById('batchDetailsDialog').close();
  }
</script>
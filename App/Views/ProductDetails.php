<?php

use App\Services\RBACService;

$canEditProduct = RBACService::hasPermission('edit_product_Details');
$canDeleteProduct = RBACService::hasPermission('delete_product');
$canCreateReturn = RBACService::hasPermission('create_return');
$canPlaceOrder = RBACService::hasPermission('place_order');

?>

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <div class="profile-container">
      <div class="details-header">
        <div class="details-header-left">
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
          <?php if ($canCreateReturn || $canDeleteProduct || $canEditProduct): ?>
            <div class="dropdown">
              <button class="dropdown-trigger icon-btn" title="More options">
                <span class="icon">more_vert</span>
              </button>
              <div class="dropdown-menu">
                <?php if ($canCreateReturn): ?>
                  <button class="dropdown-item" onclick="openReturnDetailsDialog()">
                    <span class="icon">undo</span>
                    Returns
                  </button>
                <?php endif; ?>
                <?php if ($canDeleteProduct): ?>
                  <button class="dropdown-item" onclick="enableEditing()">
                    <span class="icon">edit</span>
                    Edit Product
                  </button>
                <?php endif; ?>
                <?php if ($canEditProduct): ?>
                  <button class="dropdown-item danger" onclick="deleteProduct(<?= $product['id'] ?>)">
                    <span class="icon">delete</span>
                    Delete Product
                  </button>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-header">
            <span class="icon text-success">inventory_2</span>
            <span class="stat-label">Total Stock</span>
          </div>
          <?php
          $totalStock = array_sum(array_map(function ($batch) {
            return strtotime($batch['expiry_date']) > time() || !$batch['expiry_date'] ? $batch['current_quantity'] : 0;
          }, $product['batches']));
          ?>
          <div class="stat-value"><?= $product['is_int'] ? number_format($totalStock ?? 0, 0) : number_format($totalStock, 3); ?> <?= htmlspecialchars($product['unit_symbol']) ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="icon text-info">trending_up</span>
            <span class="stat-label">Sales This Month</span>
          </div>
          <div class="stat-value"><?= number_format($sales['monthly_sales'], 2) ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="icon text-warning">error_outline</span>
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
            <span class="icon text-danger">event_busy</span>
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
            <div class="form-field span-2">
              <div class="chip-container">
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
                      <span class="info-label">Unit Cost</span>
                      <span class="info-value unit-value">Rs. <?= number_format($batch['unit_cost'], 2) ?></span>
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
                  <?php if ($canCreateReturn): ?>
                    <th>Actions</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php
                if (empty($suppliers)) {
                  echo '<tr><td colspan="6" style="text-align: center;">No suppliers found</td></tr>';
                } else {
                  foreach ($suppliers as $supplier): ?>
                    <tr>
                      <td><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                      <td><?= htmlspecialchars($supplier['contact_person']) ?></td>
                      <td>
                        <span class="badge <?= $supplier['is_preferred_supplier'] ? 'success' : '' ?>">
                          <?= $supplier['is_preferred_supplier'] ? 'Yes' : 'No' ?>
                        </span>
                      </td>
                      <td>
                        <?php if ($canPlaceOrder): ?>
                          <button type="button" class="action-btn badge" onclick="confirmPlaceOrder()">
                            Place Order
                          </button>
                        <?php endif; ?>
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

<!-- Return Details dialog-->
<dialog id="returnDetailsDialog" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Product Return Details</h2>
      <button type="button" class="close-btn" onclick="closeReturnDetailsDialog()">
        <span class="icon">close</span>
      </button>
    </div>
    <form id="returnDetailsForm" class="modal-body" method="post" onsubmit="validateForm(event);">
      <div class="form-grid">
        <h3><?= $product['product_code'] ?> - <?= $product['product_name'] ?></h3>
        <div class="form-field span-2">
          <label for="reason">Reason</label>
          <textarea id="reason" name="reason"></textarea>
        </div>
        <div class="form-field">
          <label for="price">Product Price *</label>
          <select id="price" name="price" required>
            <option value="" disabled selected>Select a price</option>
            <?php foreach ($prices as $price): ?>
              <option value="<?= htmlspecialchars($price['id']) ?>">
                <?= htmlspecialchars($price['unit_price']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-field">
          <label for="quantity">Quantity *</label>
          <input type="text" id="quantity" name="quantity">
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeReturnDetailsDialog()">
          Cancel
        </button>
        <button type="submit" class="btn btn-primary">
          Save
        </button>
      </div>
    </form>
  </div>
</dialog>


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

  function confirmPlaceOrder() {
    if (confirm('Are you sure want to place order?')) {
      window.location.href = '/products/<?= $product['id'] ?>/placeorder?supplier=<?= $supplier['id'] ?>'
    }
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


  function openReturnDetailsDialog() {
    document.getElementById("returnDetailsDialog").showModal();
  }

  function closeReturnDetailsDialog() {
    document.getElementById("returnDetailsDialog").close();
  }
</script>
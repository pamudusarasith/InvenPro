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
                <button class="dropdown-item" onclick="addBatch()">
                  <span class="icon">add_box</span>
                  Add Batch
                </button>
                <?php if (!$product['deleted_at']): ?>
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
            <span class="icon">inventory_2</span>
            <span class="stat-label">Total Stock</span>
          </div>
          <div class="stat-value"><?= number_format($stats['total_stock'], 3) ?> <?= htmlspecialchars($product['unit_symbol']) ?></div>
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
            <span class="stat-label">Average Price</span>
          </div>
          <div class="stat-value">$<?= number_format($stats['avg_price'], 2) ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="icon">shopping_cart</span>
            <span class="stat-label">Last Purchase</span>
          </div>
          <div class="stat-value">
            <?= $stats['last_purchase'] ? date('M d, Y', strtotime($stats['last_purchase'])) : 'Never' ?>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-nav">
      <button class="tab-btn active" onclick="switchTab('overview')">Overview</button>
      <button class="tab-btn" onclick="switchTab('batches')">Stock Batches</button>
      <button class="tab-btn" onclick="switchTab('suppliers')">Suppliers</button>
      <button class="tab-btn" onclick="switchTab('history')">History</button>
    </div>

    <form id="details-form" method="POST" action="/products/<?= $product['id'] ?>/update" enctype="multipart/form-data">
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
          </div>
        </div>
      </div>

      <div id="batches" class="tab-content">
        <div class="card">
          <h3>Stock Batches</h3>
          <div class="content">
            <div class="batch-grid">
              <?php foreach ($product['batches'] as $batch): ?>
                <div class="batch-card card glass">
                  <div class="batch-header">
                    <span class="batch-title"><?= htmlspecialchars($batch['batch_code']) ?></span>
                    <button type="button" class="icon-btn" title="Edit Batch">
                      <span class="icon">edit</span>
                    </button>
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
                      <span class="info-value"><?= number_format($batch['current_quantity'], 3) ?> <?= htmlspecialchars($product['unit_symbol']) ?></span>
                    </div>
                    <div class="batch-info">
                      <span class="info-label">Unit Price</span>
                      <span class="info-value">$<?= number_format($batch['unit_price'], 2) ?></span>
                    </div>
                    <div class="batch-info">
                      <span class="info-label">Manufacturing Date</span>
                      <span class="info-value"><?= $batch['manufactured_date'] ? date('M d, Y', strtotime($batch['manufactured_date'])) : '-' ?></span>
                    </div>
                    <div class="batch-info">
                      <span class="info-label">Expiry Date</span>
                      <span class="info-value"><?= $batch['expiry_date'] ? date('M d, Y', strtotime($batch['expiry_date'])) : '-' ?></span>
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
                  <th>Supplier Code</th>
                  <th>Preferred</th>
                  <th>Last Order</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($suppliers as $supplier): ?>
                  <tr>
                    <td><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                    <td><?= htmlspecialchars($supplier['supplier_product_code']) ?></td>
                    <td>
                      <span class="badge <?= $supplier['is_preferred_supplier'] ? 'success' : '' ?>">
                        <?= $supplier['is_preferred_supplier'] ? 'Yes' : 'No' ?>
                      </span>
                    </td>
                    <td><?= $supplier['last_order'] ? date('M d, Y', strtotime($supplier['last_order'])) : '-' ?></td>
                    <td>
                      <button type="button" class="icon-btn"
                        onclick="window.location.href='/suppliers/<?= $supplier['id'] ?>'">
                        <span class="icon">visibility</span>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
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
</script>
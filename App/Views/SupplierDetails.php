<?php

use App\Services\RBACService;

$message = $_SESSION['message'] ?? null;
$messageType = $_SESSION['message_type'] ?? 'error';
unset($_SESSION['message'], $_SESSION['message_type']);

$branches = $branches ?? [];
?>

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <div class="profile-container">
      <div class="details-header">
        <div class="details-header-left">
          <div class="details-avatar">
            <span class="icon">store</span>
          </div>
          <div class="profile-info">
            <div class="details-title">
              <h1 class="title-name"><?= htmlspecialchars($supplier['supplier_name']) ?></h1>
              <span class="badge <?= $supplier['deleted_at'] ? 'danger' : 'success' ?>">
                <?= $supplier['deleted_at'] ? 'Inactive' : 'Active' ?>
              </span>
            </div>
            <div class="details-meta">
              <div class="meta-item">
                <span class="icon">person</span>
                <span class="meta-text"><?= htmlspecialchars($supplier['contact_person']) ?></span>
              </div>
              <div class="meta-item">
                <span class="icon">email</span>
                <span class="meta-text"><?= htmlspecialchars($supplier['email']) ?></span>
              </div>
              <div class="meta-item">
                <span class="icon">phone</span>
                <span class="meta-text"><?= htmlspecialchars($supplier['phone']) ?></span>
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
          <?php if (RBACService::hasPermission('edit_supplier')): ?>
            <div class="dropdown">
              <button class="dropdown-trigger icon-btn" title="More options">
                <span class="icon">more_vert</span>
              </button>
              <div class="dropdown-menu">
                <button class="dropdown-item" onclick="enableEditing()">
                  <span class="icon">edit</span>
                  Edit Supplier
                </button>
                <button class="dropdown-item" onclick="assignProducts()">
                  <span class="icon">category</span>
                  Assign Products
                </button>
                <?php if (!$supplier['deleted_at']): ?>
                  <button class="dropdown-item danger" onclick="deleteSupplier(<?= $supplier['id'] ?>)">
                    <span class="icon">delete</span>
                    Delete Supplier
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
            <span class="icon">inventory</span>
            <span class="stat-label">Active Products</span>
          </div>
          <div class="stat-value"><?= $stats['active_products'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="icon">shopping_cart</span>
            <span class="stat-label">Total Orders</span>
          </div>
          <div class="stat-value"><?= $stats['total_orders'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="icon">payments</span>
            <span class="stat-label">Total Spend</span>
          </div>
          <div class="stat-value">$<?= number_format($stats['total_spend'] ?? 0, 2) ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="icon">schedule</span>
            <span class="stat-label">Last Order</span>
          </div>
          <div class="stat-value">
            <?= $stats['last_order'] ? date('M d, Y', strtotime($stats['last_order'])) : 'Never' ?>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-nav">
      <button class="tab-btn active" onclick="switchTab('overview')">Overview</button>
      <button class="tab-btn" onclick="switchTab('products')">Products</button>
      <button class="tab-btn" onclick="switchTab('orders')">Purchase Orders</button>
      <button class="tab-btn" onclick="switchTab('returns')">Returns</button>
    </div>

    <form id="details-form" method="POST" action="/suppliers/<?= $supplier['id'] ?>/update">
      <div id="overview" class="tab-content active">
        <div class="card">
          <h3>Supplier Information</h3>
          <div class="content form-grid">
            <div class="form-field">
              <label for="supplier_name">Company Name</label>
              <input type="text" id="supplier_name" name="supplier_name"
                value="<?= htmlspecialchars($supplier['supplier_name']) ?>"
                disabled>
            </div>
            <div class="form-field">
              <label for="contact_person">Contact Person</label>
              <input type="text" id="contact_person" name="contact_person"
                value="<?= htmlspecialchars($supplier['contact_person']) ?>"
                disabled>
            </div>
            <div class="form-field">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email"
                value="<?= htmlspecialchars($supplier['email']) ?>"
                disabled>
            </div>
            <div class="form-field">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone"
                value="<?= htmlspecialchars($supplier['phone']) ?>"
                disabled>
            </div>
            <div class="form-field">
              <label for="branch">Branch</label>
              <select id="branch" name="branch_id" disabled>
                <?php foreach ($branches as $branch): ?>
                  <option value="<?= $branch['id'] ?>" <?= $branch['branch_name'] === $supplier['branch_name'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($branch['branch_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-field">
              <label for="address">Address</label>
              <textarea id="address" name="address"
                disabled rows="3"><?= htmlspecialchars($supplier['address']) ?></textarea>
            </div>
          </div>
        </div>
      </div>

      <div id="products" class="tab-content">
        <div class="card">
          <h3>Assigned Products</h3>
          <div class="content">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Product Code</th>
                  <th>Product Name</th>
                  <th>Supplier Code</th>
                  <th>Preferred</th>
                  <th>Last Order</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($supplier_products as $product): ?>
                  <tr>
                    <td><?= htmlspecialchars($product['product_code']) ?></td>
                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                    <td><?= htmlspecialchars($product['supplier_product_code']) ?></td>
                    <td>
                      <span class="badge <?= $product['is_preferred_supplier'] ? 'success' : '' ?>">
                        <?= $product['is_preferred_supplier'] ? 'Yes' : 'No' ?>
                      </span>
                    </td>
                    <td><?= $product['last_order'] ? date('M d, Y', strtotime($product['last_order'])) : '-' ?></td>
                    <td>
                      <button type="button" class="icon-btn" onclick="editProduct(<?= $product['id'] ?>)">
                        <span class="icon">edit</span>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div id="orders" class="tab-content">
        <!-- Similar structure for purchase orders -->
      </div>

      <div id="returns" class="tab-content">
        <!-- Similar structure for returns -->
      </div>
    </form>
  </div>
</div>

<?php if (RBACService::hasPermission('edit_supplier')): ?>
  <dialog id="assignProductsModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Assign Products</h2>
        <button class="close-btn" onclick="closeAssignDialog()">
          <span class="icon">close</span>
        </button>
      </div>

      <form id="assignProductsForm" method="POST" action="/suppliers/<?= $supplier['id'] ?>/products">
        <div class="form-grid">
          <div class="search-bar span-2">
            <span class="icon">search</span>
            <input type="text" id="searchInput" placeholder="Search products...">
          </div>

          <div class="product-list span-2">
            <?php foreach ($available_products as $product): ?>
              <div class="product-assignment-item">
                <div class="product-info">
                  <label class="checkbox-wrapper">
                    <input type="checkbox"
                      name="products[]"
                      value="<?= $product['id'] ?>"
                      <?= isset($supplier_products[$product['id']]) ? 'checked' : '' ?>>
                    <span class="product-name"><?= htmlspecialchars($product['product_name']) ?></span>
                    <span class="product-code"><?= htmlspecialchars($product['product_code']) ?></span>
                  </label>
                </div>

                <div class="product-details" data-product="<?= $product['id'] ?>">
                  <div class="form-row">
                    <input type="text"
                      name="supplier_codes[<?= $product['id'] ?>]"
                      placeholder="Supplier Code"
                      value="<?= htmlspecialchars($supplier_products[$product['id']]['supplier_product_code'] ?? '') ?>"
                      class="form-input compact">

                    <label class="toggle-wrapper">
                      <input type="checkbox"
                        name="preferred[<?= $product['id'] ?>]"
                        <?= isset($supplier_products[$product['id']]) &&
                          $supplier_products[$product['id']]['is_preferred_supplier'] ? 'checked' : '' ?>>
                      <span class="toggle-label">Preferred Supplier</span>
                    </label>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="closeAssignDialog()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </dialog>
<?php endif; ?>

<!-- Include message popup from existing code -->
<?php
$popupIcon = 'error';
if ($messageType === 'success') {
  $popupIcon = 'check_circle';
} elseif ($messageType === 'warning') {
  $popupIcon = 'warning';
}
?>

<div id="messagePopup" class="popup <?= $messageType ?>">
  <span class="icon"><?= $popupIcon ?></span>
  <span class="popup-message"><?= htmlspecialchars($message ?? '') ?></span>
  <button class="popup-close" onclick="closePopup()">
    <span class="icon">close</span>
  </button>
</div>

<script>
  // Reuse existing tab switching functionality
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

  function deleteSupplier(supplierId) {
    if (!confirm('Are you sure you want to delete this supplier? This action cannot be undone.')) {
      return;
    }
    window.location.href = `/suppliers/${supplierId}/delete`;
  }

  function assignProducts() {
    const dialog = document.getElementById('assignProductsModal');
    dialog.showModal();

    // Setup product search filtering
    const searchInput = document.getElementById('productSearch');
    const productItems = document.querySelectorAll('.product-assignment-item');

    searchInput.addEventListener('input', (e) => {
      const searchTerm = e.target.value.toLowerCase();

      productItems.forEach(item => {
        const productName = item.querySelector('.product-name').textContent.toLowerCase();
        const productCode = item.querySelector('.product-code').textContent.toLowerCase();
        item.style.display = productName.includes(searchTerm) || productCode.includes(searchTerm) ?
          'flex' : 'none';
      });
    });

    // Show/hide product details when checkbox changes
    document.querySelectorAll('[name="products[]"]').forEach(checkbox => {
      checkbox.addEventListener('change', (e) => {
        const details = e.target.closest('.product-assignment-item')
          .querySelector('.product-details');
        details.classList.toggle('active', e.target.checked);
      });
    });
  }

  function closeAssignDialog() {
    const dialog = document.getElementById('assignProductsModal');
    dialog.close();
  }

  function editProduct(productId) {
    // Implement product editing logic
  }

  <?php if ($message): ?>
    window.addEventListener('load', () => {
      document.getElementById('messagePopup').classList.add('show');
    });
  <?php endif; ?>

  function closePopup() {
    document.getElementById('messagePopup').classList.remove('show');
  }
</script>
<?php

use App\Services\RBACService;

$branches = $branches ?? [];
?>

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <!-- Header Section -->
    <div class="card glass page-header">
      <div class="header-content">
        <h1>Supplier Management</h1>
        <p class="subtitle">Manage suppliers and their product associations</p>
      </div>
      <?php if (RBACService::hasPermission('add_supplier')): ?>
        <button class="btn btn-primary" onclick="openAddSupplierDialog()">
          <span class="icon">person_add</span>
          Add Supplier
        </button>
      <?php endif; ?>
    </div>

    <!-- Controls Section -->
    <div class="card glass controls">
      <div class="search-bar">
        <span class="icon">search</span>
        <input type="text" id="searchInput" name="search" placeholder="Search Suppliers..." value="<?= htmlspecialchars($search ?? '') ?>">
      </div>
      <div class="filters">
        <select id="filterBranch" name="branch">
          <option value="">All Branches</option>
          <?php foreach ($branches as $branch): ?>
            <option value="<?= $branch['id'] ?>" <?= isset($branchId) && $branchId == $branch['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($branch['branch_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Suppliers Table -->
    <div class="table-container">
      <table class="data-table clickable" id="suppliers-table">
        <thead>
          <tr>
            <th>Supplier Name</th>
            <th>Contact Person</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Branch</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (empty($suppliers)) {
            echo '<tr><td colspan="6" style="text-align: center;">No suppliers found</td></tr>';
          } else {
            foreach ($suppliers as $supplier):
          ?>
              <tr onclick="location.href = '/suppliers/<?= $supplier['id']; ?>'">
                <td><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                <td><?= htmlspecialchars($supplier['contact_person']) ?></td>
                <td><?= htmlspecialchars($supplier['email']) ?></td>
                <td><?= htmlspecialchars($supplier['phone']) ?></td>
                <td><?= htmlspecialchars($supplier['branch_name']) ?></td>
              
              </tr>
          <?php endforeach;
          } ?>
        </tbody>
      </table>

      <div class="pagination-controls">
        <div class="items-per-page">
          <span>Show:</span>
          <select class="items-select" onchange="changeItemsPerPage(this.value)">
            <option value="5" <?= $itemsPerPage == 5 ? "selected" : "" ?>>5</option>
            <option value="10" <?= $itemsPerPage == 10 ? "selected" : "" ?>>10</option>
            <option value="20" <?= $itemsPerPage == 20 ? "selected" : "" ?>>20</option>
            <option value="50" <?= $itemsPerPage == 50 ? "selected" : "" ?>>50</option>
          </select>
          <span>entries</span>
        </div>

        <?php if ($totalPages > 1): ?>
          <div class="pagination">
            <?php if ($page > 1): ?>
              <button class="page-btn" onclick="changePage(<?= $page - 1 ?>)">
                <span class="icon">chevron_left</span>
              </button>
            <?php endif; ?>

            <div class="page-numbers">
              <?php
              $maxButtons = 3;
              $halfMax = floor($maxButtons / 2);
              $start = max(1, min($page - $halfMax, $totalPages - $maxButtons + 1));
              $end = min($totalPages, $start + $maxButtons - 1);

              if ($start > 1) {
                echo '<span class="page-number">1</span>';
                if ($start > 2) {
                  echo '<span class="page-dots">...</span>';
                }
              }

              for ($i = $start; $i <= $end; $i++) {
                echo '<span class="page-number ' . ($page == $i ? 'active' : '') . '"
                    onclick="changePage(' . $i . ')">' . $i . '</span>';
              }

              if ($end < $totalPages) {
                if ($end < $totalPages - 1) {
                  echo '<span class="page-dots">...</span>';
                }
                echo '<span class="page-number">' . $totalPages . '</span>';
              }
              ?>
            </div>

            <?php if ($page < $totalPages): ?>
              <button class="page-btn" onclick="changePage(<?= $page + 1 ?>)">
                <span class="icon">chevron_right</span>
              </button>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php if (RBACService::hasPermission('add_supplier')): ?>
  <dialog id="addSupplierModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Add New Supplier</h2>
        <button class="close-btn" onclick="closeAddSupplierDialog()">
          <span class="icon">close</span>
        </button>
      </div>

      <form id="addSupplierForm" method="POST" action="/suppliers/new" onsubmit="validateForm(event);">
        <div class="form-grid">
          <div class="form-field span-2">
            <label for="supplierName">Supplier Name *</label>
            <input type="text" id="supplierName" name="supplier_name" required>
          </div>

          <div class="form-field">
            <label for="contactPerson">Contact Person *</label>
            <input type="text" id="contactPerson" name="contact_person">
          </div>

          <div class="form-field">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email">
          </div>

          <div class="form-field">
            <label for="phone">Phone *</label>
            <input type="tel" id="phone" name="phone">
          </div>

          <div class="form-field">
            <label for="branch">Branch *</label>
            <select id="branch" name="branch_id" required>
              <option value="">Select Branch</option>
              <?php foreach ($brachForCreateSupplier as $branch): ?>
                <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['branch_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-field span-2">
            <label for="address">Address *</label>
            <textarea id="address" name="address" rows="3"></textarea>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="closeAddSupplierDialog()">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Supplier</button>
        </div>
      </form>
    </div>
  </dialog>
<?php endif; ?>

<script>
  function changePage(pageNo) {
    const url = new URL(location.href);
    url.searchParams.set('p', pageNo);
    location.href = url.toString();
  }

  function changeItemsPerPage(itemsPerPage) {
    const url = new URL(location.href);
    url.searchParams.set('ipp', itemsPerPage);
    url.searchParams.delete('p');
    location.href = url.toString();
  }

  <?php if (RBACService::hasPermission('add_supplier')): ?>

    function openAddSupplierDialog() {
      const dialog = document.getElementById('addSupplierModal');
      dialog.showModal();
    }

    function closeAddSupplierDialog() {
      const dialog = document.getElementById('addSupplierModal');
      dialog.close();
    }

    function validateForm(event) {
      const form = event.target;
      const supplierName = form.querySelector('#supplierName');
      const branch = form.querySelector('#branch');

      const errorFields = form.querySelectorAll('.error');
      errorFields.forEach(field => {
        field.classList.remove('error');
        field.querySelector('.error-message')?.remove();
      });

      let hasError = false;

      if (!supplierName.value.trim()) {
        addErrorMessage(supplierName.parentElement, 'Supplier name is required');
        hasError = true;
      }

      if (!branch.value) {
        addErrorMessage(branch.parentElement, 'Branch is required');
        hasError = true;
      }

      if (hasError) {
        event.preventDefault();
      }
    }

    function addErrorMessage(field, message) {
      field.classList.add('error');
      let errorMessage = document.createElement('span');
      errorMessage.classList.add('error-message');
      errorMessage.innerText = message;
      field.appendChild(errorMessage);
    }

    function applyFilters() {
      const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
      const rows = document.querySelectorAll('#suppliers-table tbody tr');
      rows.forEach(row => {
        const status = row.cells[5].textContent.toLowerCase(); 
        const matchesStatus = statusFilter === '' || status === statusFilter;
        row.style.display = matchesStatus ? '' : 'none';
      });
    }

    function updateSearchParams() {
      const search = document.getElementById('searchInput').value;
      const branch = document.getElementById('filterBranch').value;

      const url = new URL(location.href);
      url.pathname = '/suppliers';
      url.searchParams.set('search', search);
      url.searchParams.set('branch', branch);
      location.href = url.toString();
    }

    document.getElementById('filterBranch').addEventListener('change', updateSearchParams);
    document.getElementById('searchInput').addEventListener('input', debounce(updateSearchParams, 500));

    function debounce(func, wait) {
      let timeout;
      return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
      };
    }
  <?php endif; ?>
</script>
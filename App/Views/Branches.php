<?php

use App\Services\RBACService;

// Simulate data from controller
$branches = $branches ?? [];

$page = $_GET['p'] ?? 1;
$itemsPerPage = $_GET['ipp'] ?? 10;
$totalPages = 1;

$canManageBranches = RBACService::hasPermission('manage_branches');
$canAddBranch = RBACService::hasPermission('add_branch');
$canEditBranch = RBACService::hasPermission('edit_branch');
$canDeleteBranch = RBACService::hasPermission('delete_branch');
?>

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <!-- Header Section -->
    <div class="card glass page-header">
      <div class="header-content">
        <h1>Branch Management</h1>
        <p class="subtitle">Manage your organization's branches and locations</p>
      </div>
      <?php if ($canAddBranch): ?>
        <button class="btn btn-primary" onclick="openAddBranchDialog()">
          <span class="icon">add_business</span>
          Add Branch
        </button>
      <?php endif; ?>
    </div>

    <!-- Stats Section -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-header">
          <span class="icon text-success">business</span>
          <span class="stat-label">Active Branches</span>
        </div>
        <div class="stat-value"><?= count(array_filter($branches, function ($branch) {
                                  return $branch['deleted_at'] === null;
                                })) ?></div>
      </div>

      <div class="stat-card">
        <div class="stat-header">
          <span class="icon text-danger">business_center</span>
          <span class="stat-label">Inactive Branches</span>
        </div>
        <div class="stat-value"><?= count(array_filter($branches, function ($branch) {
                                  return $branch['deleted_at'] !== null;
                                })) ?></div>
      </div>
    </div>

    <!-- Controls Section -->
    <div class="card glass controls">
      <div class="search-bar">
        <span class="icon">search</span>
        <input type="text" id="searchInput" placeholder="Search branches..." oninput="filterBranches()">
      </div>

      <div class="filters">
        <select id="filterStatus" onchange="filterByStatus()">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
    </div>

    <!-- Branches Table -->
    <div class="table-container">
      <table class="data-table clickable" id="branches-table">
        <thead>
          <tr>
            <th>Branch Code</th>
            <th>Branch Name</th>
            <th>Location</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Status</th>
            <?php if ($canEditBranch || $canDeleteBranch): ?>
              <th>Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php
          if (empty($branches)) {
            echo '<tr><td colspan="' . ($canEditBranch || $canDeleteBranch ? '7' : '6') . '" style="text-align: center;">No branches found</td></tr>';
          } else {
            foreach ($branches as $branch):
          ?>
              <tr <?php if (!($canEditBranch || $canDeleteBranch)): ?>onclick="viewBranchDetails(<?= $branch['id']; ?>)" <?php endif; ?>>
                <td><?= htmlspecialchars($branch['branch_code']) ?></td>
                <td><?= htmlspecialchars($branch['branch_name']) ?></td>
                <td><?= htmlspecialchars($branch['address']) ?></td>
                <td><?= htmlspecialchars($branch['phone']) ?></td>
                <td><?= htmlspecialchars($branch['email']) ?></td>
                <td>
                  <span class="badge <?= $branch['deleted_at'] ? 'danger' : 'success' ?>">
                    <?= $branch['deleted_at'] ? 'Inactive' : 'Active' ?>
                  </span>
                </td>
                <?php if ($canEditBranch || $canDeleteBranch): ?>
                  <td class="row gap-xs">
                    <?php if ($canEditBranch): ?>
                      <button class="icon-btn" title="Edit" onclick="openEditBranchDialog(event, <?= $branch['id']; ?>)">
                        <span class="icon">edit</span>
                      </button>
                    <?php endif; ?>
                    <?php if ($canDeleteBranch): ?>
                      <?php if ($branch['deleted_at']): ?>
                        <button class="icon-btn success" title="Restore" onclick="restoreBranch(<?= $branch['id']; ?>)">
                          <span class="icon">restore</span>
                        </button>
                      <?php else: ?>
                        <button class="icon-btn danger" title="Deactivate" onclick="deactivateBranch(<?= $branch['id']; ?>)">
                          <span class="icon">delete</span>
                        </button>
                      <?php endif; ?>
                    <?php endif; ?>
                  </td>
                <?php endif; ?>
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

<?php if (RBACService::hasPermission('add_branch')): ?>
  <dialog id="addBranchDialog" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Add New Branch</h2>
        <button class="close-btn" onclick="closeAddBranchDialog()">
          <span class="icon">close</span>
        </button>
      </div>

      <form id="addBranchForm" method="POST" action="/branches/new" onsubmit="validateForm(event);">
        <div class="form-grid">
          <div class="form-field">
            <label for="branchCode">Branch Code *</label>
            <input type="text" id="branchCode" name="branch_code" required>
          </div>

          <div class="form-field">
            <label for="branchName">Branch Name *</label>
            <input type="text" id="branchName" name="branch_name" required>
          </div>

          <div class="form-field">
            <label for="phone">Phone *</label>
            <input type="tel" id="phone" name="phone" required>
          </div>

          <div class="form-field">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>
          </div>

          <div class="form-field span-2">
            <label for="address">Address *</label>
            <textarea id="address" name="address" rows="3" required></textarea>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="closeAddBranchDialog()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </dialog>
<?php endif; ?>


<script src="/js/search.js"></script>
<script>
  // Navigation and pagination functions
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


  // Filtering functions
  function filterBranches() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const tableRows = document.querySelectorAll('#branches-table tbody tr');

    tableRows.forEach(row => {
      const branchCode = row.children[0].textContent.toLowerCase();
      const branchName = row.children[1].textContent.toLowerCase();
      const email = row.children[4].textContent.toLowerCase();

      if (branchCode.includes(searchInput) || branchName.includes(searchInput) || email.includes(searchInput)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  function filterByStatus() {
    const selectedStatus = document.getElementById('filterStatus').value.toLowerCase();
    const tableRows = document.querySelectorAll('#branches-table tbody tr');

    tableRows.forEach(row => {
      const status = row.children[5].textContent.toLowerCase();

      if (!selectedStatus || status.includes(selectedStatus)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  <?php if (RBACService::hasPermission('add_branch')): ?>

    function openAddBranchDialog() {
        document.querySelector('#addBranchDialog .modal-header h2').innerHTML = "Add New Branch";
        const dialog = document.getElementById('addBranchDialog');

        const form = document.getElementById('addBranchForm');
        form.action = '/branches/new';
        form.reset();
        dialog.showModal();
    }

    function closeAddBranchDialog() {
        const dialog = document.getElementById('addBranchDialog');
        dialog.close();
    }

  
    function addErrorMessage(field, message) {
      field.classList.add('error');
      let errorMessage = document.createElement('span');
      errorMessage.classList.add('error-message');
      errorMessage.innerText = message;
      field.appendChild(errorMessage);
    }

    function isValidEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    }
  <?php endif; ?>

  <?php if (RBACService::hasPermission('edit_branch')): ?>
    // Edit branch dialog functions
    function openEditBranchDialog(e, branchID) {
    document.querySelector('#addBranchDialog .modal-header h2').innerHTML = "Edit Branch";
    const tr = e.target.closest('tr');
    const branchCode = tr.querySelector('td:nth-child(1)').innerText; 
    const branchName = tr.querySelector('td:nth-child(2)').innerText;
    const phone = tr.querySelector('td:nth-child(4)').innerText;      
    const email = tr.querySelector('td:nth-child(5)').innerText;     
    const address = tr.querySelector('td:nth-child(3)').innerText;

    const form = document.getElementById('addBranchForm');
    form.action = `/branches/${branchID}/update`;
    form.querySelector('input[name="branch_code"]').value = branchCode;
    form.querySelector('input[name="branch_name"]').value = branchName;
    form.querySelector('input[name="phone"]').value = phone;
    form.querySelector('input[name="email"]').value = email;
    form.querySelector('textarea[name="address"]').value = address;

    document.getElementById('addBranchDialog').showModal();
}

    function closeEditBranchDialog() {
      const dialog = document.getElementById('editBranchModal');
      dialog.close();
    }

   

    // Helper function to get branch by ID (simulating data access)
    function getBranchById(id) {
      // This would normally be an API call
      const branches = <?= json_encode($branches) ?>;
      return branches.find(branch => branch.id === id);
    }
  <?php endif; ?>

  <?php if ($RBACService::hasPermission('delete_branch')): ?>
    // Deactivate and restore branch functions
    function deactivateBranch(branchId) {
      event.stopPropagation();
      if (confirm('Are you sure you want to deactivate this branch?')) {
        // In real implementation, this would submit a form or make an API call
        console.log('Deactivating branch ID:', branchId);
        // window.location.href = `/branches/${branchId}/delete`;
      }
    }

    function restoreBranch(branchId) {
      event.stopPropagation();

      if (confirm('Are you sure you want to restore this branch?')) {
        // In real implementation, this would submit a form or make an API call
        console.log('Restoring branch ID:', branchId);
        // window.location.href = `/branches/${branchId}/restore`;
      }
    }
  <?php endif; ?>
</script>
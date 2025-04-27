<?php

use App\Services\RBACService;

// Simulate data from controller
$branches = $branches ?? [];

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
        <input type="text" id="searchInput" name="search" placeholder="Search Branches..." value="<?= htmlspecialchars($search ?? '') ?>">
      </div>
      <div class="filters">
        <select id="filterStatus" name="status">
          <option value="">All Status</option>
          <option value="active" <?= isset($status) && $status === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= isset($status) && $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
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
                        <button class="icon-btn success" title="Restore" onclick="restoreBranch(event, <?= $branch['id']; ?>)">
                          <span class="icon">restore</span>
                        </button>
                      <?php else: ?>
                        <button class="icon-btn danger" title="Deactivate" onclick="deactivateBranch(event, <?= $branch['id']; ?>)">
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


<!-- <script src="/js/search.js"></script> -->

<script>
  // Navigation and pagination functions
  const branches = <?= json_encode($branches) ?>;
</script>
<script src="/js/branches.js"></script>
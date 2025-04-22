<?php

use App\Services\RBACService;

$roles = $roles ?? [];
$branches = $branches ?? [];
?>

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <!-- Header Section -->
    <div class="card glass page-header">
      <div class="header-content">
        <h1>User Management</h1>
        <p class="subtitle">Manage system users and their roles</p>
      </div>
      <?php if (RBACService::hasPermission('add_user')): ?>
        <button class="btn btn-primary" onclick="openAddUserDialog()">
          <span class="icon">person_add</span>
          Add User
        </button>
      <?php endif; ?>
    </div>

    <!-- Controls Section -->
    <div class="card glass controls">
      <div class="search-bar">
        <span class="icon">search</span>
        <input type="text" id="searchInput" placeholder="Search users...">
      </div>
      <div class="filters">
        <select id="filterRole">
          <option value="">All Roles</option>
          <?php foreach ($roles as $role): ?>
            <option value="<?= $role['id'] ?>"><?= ucfirst($role['role_name']) ?></option>
          <?php endforeach; ?>
        </select>
        <select id="filterBranch">
          <option value="">All Branches</option>
          <?php foreach ($branches as $branch): ?>
            <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['branch_name']) ?></option>
          <?php endforeach; ?>
        </select>
        <select id="filterStatus">
          <option value="">All Status</option>
          <option value="active" selected>Active</option>
          <option value="locked">Locked</option>
        </select>
      </div>
    </div>

    <!-- Users Table -->
    <div class="table-container">
      <table class="data-table clickable" id="users-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Branch</th>
            <th>Status</th>
            <th>Last Login</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (empty($users)) {
            echo '<tr><td colspan="7" style="text-align: center;">No users found</td></tr>';
          } else {
            foreach ($users as $user):
          ?>
              <tr onclick="location.href = '/users/<?= $user['id']; ?>'">
                <td><?= htmlspecialchars($user['display_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= ucfirst($user['role_name']) ?></td>
                <td><?= htmlspecialchars($user['branch_name']) ?></td>
                <td>
                  <span class="badge <?= $user['is_locked'] ? 'danger' : 'success' ?>">
                    <?= htmlspecialchars($user['status']) ?>
                  </span>
                </td>
                <td><?= $user['last_login'] ? date('M d, Y H:i', $user['last_login']) : "N/A" ?></td>
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
            <option value="100" <?= $itemsPerPage == 100 ? "selected" : "" ?>>100</option>
          </select>
          <span>entries</span>
        </div>
        <?php if ($totalPages > 1): ?>
          <div class="pagination">
            <?php if ($page > 1): ?>
              <button class="page-btn" onclick="changePage(<?= min($totalPages, $page - 1) ?>)">
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

<?php if (RBACService::hasPermission('add_user')): ?>

  <dialog id="addUserModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Add New User</h2>
        <button class="close-btn" onclick="closeAddUserDialog()">
          <span class="icon">close</span>
        </button>
      </div>

      <form id="addUserForm" method="POST" action="/users/new" onsubmit="validateForm(event);">
        <div class="form-grid">
          <div class="form-field span-2">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email">
          </div>

          <div class="form-field">
            <label for="role">Role *</label>
            <select id="role" name="role_id">
              <option value="">Select Role</option>
              <?php foreach ($roles as $role): ?>
                <option value="<?= $role['id'] ?>"><?= ucfirst($role['role_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-field">
            <label for="branch">Branch *</label>
            <select id="branch" name="branch_id">
              <option value="">Select Branch</option>
              <?php foreach ($branches as $branch): ?>
                <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['branch_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-info span-2">
            <p class="info-text">
              <span class="icon">info</span>
              An email will be sent to the user with instructions to complete their profile and set a password.
            </p>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="closeAddUserDialog()">Cancel</button>
          <button type="submit" class="btn btn-primary">Create User</button>
        </div>
      </form>
    </div>
  </dialog>

<?php endif; ?>

<?php if (RBACService::hasPermission('delete_user')): ?>

  <dialog id="deleteConfirmModal" class="delete-confirm-modal">
    <div class="modal-content">
      <span class="icon warning-icon">warning</span>
      <div class="dialog-message">
        <h3>Delete User</h3>
        <br>
        <p>Are you sure you want to delete this user? This action cannot be undone.</p>
      </div>
      <div class="dialog-actions">
        <button class="btn btn-secondary" onclick="closeDeleteDialog()">Cancel</button>
        <button class="btn btn-danger" onclick="confirmDelete()">Delete</button>
      </div>
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

  <?php if (RBACService::hasPermission('add_user')): ?>

    function openAddUserDialog() {
      const dialog = document.getElementById('addUserModal');
      dialog.showModal();
    }

    function closeAddUserDialog() {
      const dialog = document.getElementById('addUserModal');
      dialog.close();
    }

    function addErrorMessage(field, message) {
      field.classList.add('error');
      let errorMessage = document.createElement('span');
      errorMessage.classList.add('error-message');
      errorMessage.innerText = message;
      field.appendChild(errorMessage);
    }

    function validateForm(event) {
      const form = event.target;
      const email = form.querySelector('#email');
      const role = form.querySelector('#role');
      const branch = form.querySelector('#branch');

      const errorFields = form.querySelectorAll('.error');
      errorFields.forEach(field => {
        field.classList.remove('error');
        field.querySelector('.error-message')?.remove();
      });

      if (!email.value) {
        let field = email.parentElement;
        addErrorMessage(field, 'Email is required');
        event.preventDefault();
      }

      if (!role.value) {
        let field = role.parentElement;
        addErrorMessage(field, 'Role is required');
        event.preventDefault();
      }

      if (!branch.value) {
        let field = branch.parentElement;
        addErrorMessage(field, 'Branch is required');
        event.preventDefault();
      }
    }

  <?php endif; ?>

  <?php if (RBACService::hasPermission('edit_user')): ?>

    function editUser(userId) {
      event.stopPropagation();
      location.href = `/users/${userId}/edit`;
    }

  <?php endif; ?>

  <?php if (RBACService::hasPermission('delete_user')): ?>
    let userToDelete = null;

    function deleteUser(userId) {
      event.stopPropagation();
      userToDelete = userId;
      const dialog = document.getElementById('deleteConfirmModal');
      dialog.showModal();
    }

    function closeDeleteDialog() {
      const dialog = document.getElementById('deleteConfirmModal');
      dialog.close();
      userToDelete = null;
    }

    function confirmDelete() {
      if (userToDelete) {
        location.href = `/users/${userToDelete}/delete`;
      }
      closeDeleteDialog();
    }

  <?php endif; ?>
</script>

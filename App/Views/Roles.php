<?php
use App\Services\RBACService;
?>

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <!-- Header Section -->
    <div class="card glass page-header">
      <div class="header-content">
        <h1>Role Management</h1>
        <p class="subtitle">Manage user roles and permissions to control access throughout the system</p>
      </div>
      <?php if (RBACService::hasPermission('manage_roles')): ?>
        <button class="btn btn-primary" onclick="openAddRoleDialog()">
          <span class="icon">add</span>
          Add Role
        </button>
      <?php else: ?>
        <p>You do not have permission to manage roles.</p>
      <?php endif; ?>
    </div>

    <!-- Controls Section -->
    <div class="card glass controls">
      <div class="search-bar">
        <span class="icon">search</span>
        <input type="text" id="searchInput" placeholder="Search roles...">
      </div>
    </div>

    <!-- Role Cards Section -->
    <div class="role-cards">
      <?php foreach ($roles as $role): ?>
      <div class="role-card">
        <div class="role-card-header">
          <h3><?= htmlspecialchars($role['role_name']) ?></h3>
        </div>
        <div class="role-card-body">
          <p><?= htmlspecialchars($role['description']) ?></p>
          <div class="role-permission-count">
            <span class="icon">verified_user</span> 
            <?php if ($role['id'] === 1): ?>
              All permissions (<?= $role['permission_count'] ?>)
            <?php else: ?>
              <?= $role['permission_count'] ?> permissions
            <?php endif; ?>
          </div>
          <div class="role-chips">
            <?php foreach ($role['permission_categories'] as $category): ?>
              <div class="role-chip"><?= htmlspecialchars($category) ?></div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="role-card-actions">
          <button class="btn btn-sm btn-secondary" onclick="viewRoleDetails(<?= $role['id'] ?>)">
            <span class="icon">visibility</span>
            View
          </button>
          <?php if (RBACService::hasPermission('manage_roles')): ?>
            <button class="btn btn-sm btn-primary" onclick="openEditRoleDialog(<?= $role['id'] ?>)">
              <span class="icon">edit</span>
              Edit
            </button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Add/Edit Role Dialog -->
  <dialog id="roleDialog">
    <div class="modal-content wide-modal">
      <div class="modal-header">
        <h2 id="roleDialogTitle">Add New Role</h2>
        <button class="close-btn" onclick="closeRoleDialog()">
          <span class="icon">close</span>
        </button>
      </div>

      <form id="roleForm" method="POST" action="/roles/store">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
        <div class="form-grid">
          <div class="form-field span-2">
            <label for="roleName">Role Name *</label>
            <input type="text" id="roleName" name="role_name" required>
          </div>
          <div class="form-field span-2">
            <label for="roleDescription">Description</label>
            <textarea id="roleDescription" name="description" rows="3"></textarea>
          </div>
        </div>

        <div class="permissions-section">
          <h3>Permissions</h3>
          <p class="subtitle">Select the permissions to assign to this role</p>

          <div class="permission-tabs">
            <?php $isFirst = true; ?>
            <?php foreach ($permissionCategories as $key => $category): ?>
              <div class="permission-tab <?= $isFirst ? 'active' : '' ?>" data-tab="<?= $key ?>">
                <?= htmlspecialchars($category) ?>
              </div>
              <?php $isFirst = false; ?>
            <?php endforeach; ?>
          </div>

          <?php $isFirst = true; ?>
          <?php foreach ($permissionsByCategory as $categoryKey => $category): ?>
            <div class="permission-content" id="<?= $categoryKey ?>-tab" <?= $isFirst ? '' : 'style="display: none;"' ?>>
              <div class="permission-header">
                <h3><?= htmlspecialchars($category['category_name']) ?> Permissions</h3>
                <span class="select-all" data-group="<?= $categoryKey ?>">Select All</span>
              </div>
              <div class="permission-list">
                <?php foreach ($category['permissions'] as $permission): ?>
                  <div class="permission-item">
                    <label>
                      <input type="checkbox" name="permissions[]" value="<?= $permission['id'] ?>">
                      <?= htmlspecialchars($permission['description']) ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <?php $isFirst = false; ?>
          <?php endforeach; ?>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="closeRoleDialog()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Role</button>
        </div>
      </form>
    </div>
  </dialog>

  <!-- View Role Details Dialog -->
  <dialog id="roleDetailsDialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="roleDetailsTitle">Role Details</h2>
        <button class="close-btn" onclick="closeRoleDetailsDialog()">
          <span class="icon">close</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="details-section">
          <h3 id="detailsRoleName"></h3>
          <p id="detailsRoleDescription"></p>
          
          <div class="details-grid">
            <div class="details-item">
              <span class="details-label">Created</span>
              <span class="details-value" id="detailsCreatedAt"></span>
            </div>
            <div class="details-item">
              <span class="details-label">Users with this role</span>
              <span class="details-value" id="detailsUserCount"></span>
            </div>
          </div>

          <div class="details-divider"></div>
          
          <h4>Permissions</h4>
          <div id="permissionsContainer"></div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeRoleDetailsDialog()">Close</button>
        <?php if (RBACService::hasPermission('manage_roles')): ?>
          <button type="button" class="btn btn-primary" id="editRoleBtn">Edit</button>
        <?php endif; ?>
      </div>
    </div>
  </dialog>
</div>

<link rel="stylesheet" href="/css/pages/roles.css">

<script>
  // Global variables
  let currentRoleId = null;

  // PHP data passed to JavaScript
  const roles = <?= json_encode($roles) ?>;
  const rolePermissions = <?= json_encode($rolePermissions ?? []) ?>;
  const roleUserCounts = <?= json_encode($roleUserCounts) ?>;
  const rolePermissionsDetails = <?= json_encode($rolePermissionsDetails) ?>;
  const rolePermissionCategories = <?= json_encode($rolePermissionCategories) ?>;
  const permissionCategories = <?= json_encode($permissionCategories) ?>;

  // Reverse mapping of category names to keys
  const categoryNameToKey = Object.fromEntries(
    Object.entries(permissionCategories).map(([key, name]) => [name, key])
  );

  // DOM Ready
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initSearch();
    
    // Initialize permission tabs
    initPermissionTabs();
    
    // Initialize select all functionality
    initSelectAllPermissions();

    // Add event listeners for buttons
    const addRoleBtn = document.querySelector('.btn-primary[onclick="openAddRoleDialog()"]');
    if (addRoleBtn) {
      addRoleBtn.addEventListener('click', openAddRoleDialog);
    }

    const viewButtons = document.querySelectorAll('.btn-secondary[onclick*="viewRoleDetails"]');
    viewButtons.forEach(btn => {
      const roleId = btn.getAttribute('onclick').match(/\d+/)[0];
      btn.addEventListener('click', () => viewRoleDetails(parseInt(roleId)));
    });
  });

  function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    searchInput.addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const roleCards = document.querySelectorAll('.role-card');
      
      roleCards.forEach(card => {
        const roleName = card.querySelector('.role-card-header h3').textContent.toLowerCase();
        const roleDescription = card.querySelector('.role-card-body p').textContent.toLowerCase();
        card.style.display = roleName.includes(searchTerm) || roleDescription.includes(searchTerm) ? '' : 'none';
      });
    });
  }

  function initPermissionTabs() {
    const tabs = document.querySelectorAll('.permission-tab');
    tabs.forEach(tab => {
      tab.addEventListener('click', function() {
        document.querySelectorAll('.permission-content').forEach(content => {
          content.style.display = 'none';
        });
        tabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const tabId = this.getAttribute('data-tab');
        document.getElementById(`${tabId}-tab`).style.display = 'block';
      });
    });
  }

  function initSelectAllPermissions() {
    const selectAllBtns = document.querySelectorAll('.select-all');
    selectAllBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        const group = this.getAttribute('data-group');
        const tabContent = document.getElementById(`${group}-tab`);
        const checkboxes = tabContent.querySelectorAll('input[type="checkbox"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        this.textContent = allChecked ? 'Select All' : 'Deselect All';
      });
    });
  }

  function openAddRoleDialog() {
    const dialog = document.getElementById('roleDialog');
    if (!dialog) {
      console.error('Role dialog not found');
      return;
    }
    if (dialog.open) dialog.close();

    const form = document.getElementById('roleForm');
    form.reset();
    document.getElementById('roleDialogTitle').textContent = 'Add New Role';
    form.action = '/roles/store';

    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
    document.querySelectorAll('.select-all').forEach(btn => btn.textContent = 'Select All');

    dialog.showModal();
    currentRoleId = null;
  }

  function openEditRoleDialog(roleId) {
    const dialog = document.getElementById('roleDialog');
    if (!dialog) {
      console.error('Role dialog not found');
      return;
    }
    if (dialog.open) dialog.close();

    currentRoleId = roleId;
    document.getElementById('roleDialogTitle').textContent = 'Edit Role';
    document.getElementById('roleForm').action = `/roles/${roleId}/update`;

    const role = roles.find(r => r.id === roleId);
    if (role) {
      document.getElementById('roleName').value = role.role_name;
      document.getElementById('roleDescription').value = role.description;

      // Reset all checkboxes and Select All buttons
      document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
      document.querySelectorAll('.select-all').forEach(btn => btn.textContent = 'Select All');

      // Check permissions for the role
      if (rolePermissions[roleId]) {
        console.log('Permissions for role', roleId, rolePermissions[roleId]); // Debug
        rolePermissions[roleId].forEach(permId => {
          const checkbox = document.querySelector(`input[name="permissions[]"][value="${permId}"]`);
          if (checkbox) {
            checkbox.checked = true;
          } else {
            console.warn(`Checkbox not found for permission ID: ${permId}`); // Debug
          }
        });

        // Update Select All button text for each category
        document.querySelectorAll('.permission-content').forEach(tabContent => {
          const group = tabContent.id.replace('-tab', '');
          const checkboxes = tabContent.querySelectorAll('input[name="permissions[]"]');
          const allChecked = Array.from(checkboxes).every(cb => cb.checked);
          const selectAllBtn = document.querySelector(`.select-all[data-group="${group}"]`);
          if (selectAllBtn) {
            selectAllBtn.textContent = allChecked ? 'Deselect All' : 'Select All';
          }
        });
      } else {
        console.warn(`No permissions found for role ID: ${roleId}`); // Debug
      }
    } else {
      console.error(`Role not found for ID: ${roleId}`);
    }

    dialog.showModal();
  }

  function closeRoleDialog() {
    const dialog = document.getElementById('roleDialog');
    if (dialog) dialog.close();
  }

  function viewRoleDetails(roleId) {
    const dialog = document.getElementById('roleDetailsDialog');
    if (!dialog) {
      console.error('Role details dialog not found');
      return;
    }
    if (dialog.open) dialog.close();

    currentRoleId = roleId;
    const role = roles.find(r => r.id === roleId);

    if (role) {
      const createdDate = new Date(role.created_at);
      const formattedDate = createdDate.toLocaleDateString('en-US', { 
        year: 'numeric', month: 'long', day: 'numeric' 
      });

      document.getElementById('detailsRoleName').textContent = role.role_name;
      document.getElementById('detailsRoleDescription').textContent = role.description;
      document.getElementById('detailsCreatedAt').textContent = formattedDate;
      document.getElementById('detailsUserCount').textContent = roleUserCounts[roleId] || 0;

      const permissionsContainer = document.getElementById('permissionsContainer');
      permissionsContainer.innerHTML = '';

      const categories = rolePermissionCategories[roleId] || [];
      categories.forEach(categoryName => {
        const categoryKey = categoryNameToKey[categoryName];
        if (!categoryKey) return;

        const div = document.createElement('div');
        div.className = 'permission-category';

        const h5 = document.createElement('h5');
        h5.textContent = categoryName;
        div.appendChild(h5);

        const ul = document.createElement('ul');
        ul.className = 'permission-details-list';
        ul.id = `details${categoryKey.replace(/_/g, '')}Permissions`;

        if (rolePermissionsDetails[roleId] && rolePermissionsDetails[roleId][roleId] && rolePermissionsDetails[roleId][roleId][categoryKey]) {
          rolePermissionsDetails[roleId][roleId][categoryKey].forEach(perm => {
            const li = document.createElement('li');
            li.textContent = perm;
            ul.appendChild(li);
          });
        } else {
          const li = document.createElement('li');
          li.textContent = 'No permissions in this category';
          li.classList.add('no-permissions');
          ul.appendChild(li);
        }

        div.appendChild(ul);
        permissionsContainer.appendChild(div);
      });

      const editBtn = document.getElementById('editRoleBtn');
      if (editBtn) {
        editBtn.textContent = 'Edit'; // Fix: Was incorrectly labeled "Delete"
        editBtn.onclick = function() {
          closeRoleDetailsDialog();
          openEditRoleDialog(roleId);
        };
      }
    }

    dialog.showModal();
  }

  function closeRoleDetailsDialog() {
    const dialog = document.getElementById('roleDetailsDialog');
    if (dialog) dialog.close();
  }

  document.getElementById('roleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const roleName = formData.get('role_name');

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      if (data.success) {
        alert(`Role "${roleName}" saved successfully!`);
        location.reload();
      } else {
        alert('Error saving role: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Fetch error:', error);
      alert('Error saving role: ' + error.message);
    });

    closeRoleDialog();
  });
</script>
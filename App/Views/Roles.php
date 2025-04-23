<?php
use App\Services\RBACService;

// Validate data from controller to prevent undefined variable errors
$roles = isset($roles) && is_array($roles) ? $roles : [];
$permissionCategories = isset($permissionCategories) && is_array($permissionCategories) ? $permissionCategories : [];
$allPermissionsByCategory = isset($allPermissionsByCategory) && is_array($allPermissionsByCategory) ? $allPermissionsByCategory : [];

// Log data for debugging
//error_log("View: allPermissionsByCategory: " . print_r($allPermissionsByCategory, true));
//error_log("View: roles: " . print_r($roles, true)); // Log the roles for debugging
//error_log("View: permissionCategories: " . print_r($permissionCategories, true)); // Log the permission categories for debugging
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
      <?php foreach ($roles as $roleId => $role): ?>
      <div class="role-card">
        <div class="role-card-header">
          <h3><?= htmlspecialchars($role['role_name'] ?? 'Unnamed Role') ?></h3>
        </div>
        <div class="role-card-body">
          <p><?= htmlspecialchars($role['description'] ?? 'No description') ?></p>
          <div class="role-permission-count">
            <span class="icon">verified_user</span>
            <?php if ($roleId === 1): ?>
              All permissions (<?= $role['permission_count'] ?? 0 ?>)
            <?php else: ?>
              <?= $role['permission_count'] ?? 0 ?> permissions
            <?php endif; ?>
          </div>
          <div class="role-chips">
            <?php foreach ($role['permission_categories'] ?? [] as $catId => $category): ?>
              <div class="role-chip"><?= htmlspecialchars($category) ?></div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="role-card-actions">
          <button class="btn btn-sm btn-secondary" onclick="viewRoleDetails(<?= $roleId ?>)">
            <span class="icon">visibility</span>
            View
          </button>
          <?php if (RBACService::hasPermission('manage_roles')): ?>
            <button class="btn btn-sm btn-primary" onclick="openEditRoleDialog(<?= $roleId ?>)">
              <span class="icon">edit</span>
              Edit
            </button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Add/Edit Role Dialog -->
<?php if (RBACService::hasPermission('manage_roles')): ?>
  <dialog id="roleDialog">
    <div class="modal-content wide-modal">
      <div class="modal-header">
        <h2 id="roleDialogTitle">Add New Role</h2>
        <button class="btn btn-secondary" onclick="cancelEdit()">
        <button class="close-btn" onclick="closeRoleDialog()">
          <span class="icon">close</span>
        </button>
      </div>

      <form id="roleForm" method="POST" action="/roles/new">
        <div class="modal-body">
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
                <?php foreach ($allPermissionsByCategory as $category => $permissions): ?>
                    <div class="permission-tab <?= $isFirst ? 'active' : '' ?>" data-tab="<?= str_replace(' ', '_', strtolower($category)) ?>">
                        <?= htmlspecialchars($category) ?>
                    </div>
                    <?php $isFirst = false; ?>
                <?php endforeach; ?>
            </div>

            <?php $isFirst = true; ?>
            <?php foreach ($allPermissionsByCategory as $category => $permissions): ?>
                <div class="permission-content" id="<?= str_replace(' ', '_', strtolower($category)) ?>-tab" <?= $isFirst ? '' : 'style="display: none;"' ?>>
                    <div class="permission-header">
                        <h3><?= htmlspecialchars($category) ?> Permissions</h3>
                        <span class="select-all" data-group="<?= str_replace(' ', '_', strtolower($category)) ?>">Select All</span>
                    </div>
                    <div class="permission-list">
                        <?php foreach ($permissions as $permission): ?>
                            <div class="permission-item">
                                <label>
                                    <input type="checkbox" name="permissions[]" value="<?= htmlspecialchars($permission['id']) ?>">
                                    <?= htmlspecialchars($permission['description']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php $isFirst = false; ?>
            <?php endforeach; ?>
        </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="closeRoleDialog()">
            <span class="icon">close</span>
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <span class="icon">save</span>
            Save</button>
        </div>
      </form>
    </div>
  </dialog>
<?php endif; ?>

<!-- View Role Details Dialog -->
<?php if (RBACService::hasPermission('manage_roles')): ?>
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
        <?php if (!empty($allPermissionsByCategory)): ?>
          <?php foreach (array_keys($allPermissionsByCategory) as $category): ?>
            <div class="permission-category">
              <h5><?= htmlspecialchars($category) ?></h5>
              <ul class="permission-details-list" id="details<?= str_replace(' ', '', $category) ?>Permissions">
                <!-- Populated by JavaScript -->
              </ul>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No permission categories available.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="modal-footer">
      <?php if (RBACService::hasPermission('manage_roles')): ?>
        <button class="btn btn-danger" onclick="deleteRole(<?= htmlspecialchars($roleId ?? 'null') ?>)">
          <span class="icon">delete</span>
          Delete Role
        </button>
      <?php endif; ?>
      <?php if (RBACService::hasPermission('manage_roles')): ?>
        <button type="button" class="btn btn-primary" id="editRoleBtn">
          <span class="icon">edit</span>
          Edit Role
        </button>
      <?php endif; ?>
    </div>
  </dialog>
</div>
<?php endif; ?> 

<link rel="stylesheet" href="/css/pages/roles.css">

<!-- Include any additional scripts here -->
<?php if (isset($_SESSION['message'])): ?>
  <div class="card glass notification <?= htmlspecialchars($_SESSION['message_type']) ?>">
    <p><?= htmlspecialchars($_SESSION['message']) ?></p>
    <button class="close-btn" onclick="this.parentElement.remove()">✕</button>
  </div>
  <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

<script>
  let currentRoleId = null;
  const roles = <?= json_encode($roles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

  document.addEventListener('DOMContentLoaded', function() {
    initSearch(); 
    initPermissionTabs();
    initSelectAllPermissions();
  });

  // Initialize search functionality for roles
  function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
      searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const roleCards = document.querySelectorAll('.role-card');
        
        roleCards.forEach(card => {
          const roleName = card.querySelector('.role-card-header h3').textContent.toLowerCase();
          const roleDescription = card.querySelector('.role-card-body p').textContent.toLowerCase();
          
          if (roleName.includes(searchTerm) || roleDescription.includes(searchTerm)) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });
    }
  }

  // Initialize permission tabs
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
        const tabContent = document.getElementById(`${tabId}-tab`);
        if (tabContent) {
          tabContent.style.display = 'block';
        }
      });
    });
  }

  // Initialize select all functionality
  function initSelectAllPermissions() {
    const selectAllBtns = document.querySelectorAll('.select-all');
    
    selectAllBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        const group = this.getAttribute('data-group');
        const tabContent = document.getElementById(`${group}-tab`);
        if (tabContent) {
          const checkboxes = tabContent.querySelectorAll('input[type="checkbox"]');
          const allChecked = Array.from(checkboxes).every(cb => cb.checked);
          
          checkboxes.forEach(cb => {
            cb.checked = !allChecked;
          });
          
          this.textContent = allChecked ? 'Select All' : 'Deselect All';
        }
      });
    });
  }

  // Open the add role dialog
  function openAddRoleDialog() {
    const form = document.getElementById('roleForm');
    const dialogTitle = document.getElementById('roleDialogTitle');
    if (form && dialogTitle) {
      form.reset();
      dialogTitle.textContent = 'Add New Role';
      form.action = '/roles/new';
      
      document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
        cb.checked = false;
      });
      
      document.getElementById('roleDialog').showModal();
      currentRoleId = null;
    }
  }

  let initialPermissions = []; // To store the initial permissions of the role

  function openEditRoleDialog(roleId) {
      currentRoleId = roleId;
      const role = roles[roleId];
      const form = document.getElementById('roleForm');
      const dialogTitle = document.getElementById('roleDialogTitle');

      if (role && form && dialogTitle) {
          dialogTitle.textContent = 'Edit Role';
          form.action = `/roles/${roleId}/update`;

          // Populate the form fields
          document.getElementById('roleName').value = role.role_name || '';
          document.getElementById('roleDescription').value = role.description || '';

          // Reset all permission checkboxes
          document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
              cb.checked = false;
          });

          // Add the role_id as a hidden input field if it doesn't already exist
          let roleIdInput = document.getElementById('roleIdInput');
          if (!roleIdInput) {
              roleIdInput = document.createElement('input');
              roleIdInput.type = 'hidden';
              roleIdInput.id = 'roleIdInput';
              roleIdInput.name = 'role_id';
              form.appendChild(roleIdInput);
          }
          roleIdInput.value = roleId;

          // Populate permissions and store initial permissions
          initialPermissions = []; // Reset initial permissions
          const permissionIds = [];
          for (const category in role.permissions) {
              role.permissions[category].forEach(perm => {
                  <?php foreach ($allPermissionsByCategory as $category => $permissions): ?>
                      <?php foreach ($permissions as $p): ?>
                          if (perm === '<?= $p['permission_name'] ?>') {
                              permissionIds.push('<?= $p['id'] ?>');
                              initialPermissions.push('<?= $p['id'] ?>'); // Store initial permissions
                          }
                      <?php endforeach; ?>
                  <?php endforeach; ?>
              });
          }

          permissionIds.forEach(id => {
              const checkbox = document.querySelector(`input[value="${id}"]`);
              if (checkbox) {
                  checkbox.checked = true;
              }
          });

          // Show the dialog
          document.getElementById('roleDialog').showModal();
      }
  }

  // Add event listener to the form to handle submission
  document.getElementById('roleForm').addEventListener('submit', function (event) {
      event.preventDefault(); // Prevent default form submission

      // Get the current state of permissions
      const currentPermissions = Array.from(document.querySelectorAll('input[name="permissions[]"]:checked')).map(cb => cb.value);

      // Determine added and removed permissions
      const addedPermissions = currentPermissions.filter(id => !initialPermissions.includes(id));
      const removedPermissions = initialPermissions.filter(id => !currentPermissions.includes(id));

      // Log the arrays for debugging
      console.log('Added Permissions:', addedPermissions);
      console.log('Removed Permissions:', removedPermissions);

      // Create a hidden input to send the added and removed permissions
      let addedInput = document.getElementById('addedPermissionsInput');
      if (!addedInput) {
          addedInput = document.createElement('input');
          addedInput.type = 'hidden';
          addedInput.id = 'addedPermissionsInput';
          addedInput.name = 'added_permissions';
          this.appendChild(addedInput);
      }
      addedInput.value = JSON.stringify(addedPermissions);

      let removedInput = document.getElementById('removedPermissionsInput');
      if (!removedInput) {
          removedInput = document.createElement('input');
          removedInput.type = 'hidden';
          removedInput.id = 'removedPermissionsInput';
          removedInput.name = 'removed_permissions';
          this.appendChild(removedInput);
      }
      removedInput.value = JSON.stringify(removedPermissions);

      // Submit the form
      this.submit();
  });
  
  // Close the role dialog
  function closeRoleDialog() {
    const dialog = document.getElementById('roleDialog');
    if (dialog) {
      dialog.close();
    }
  }

  // View role details
  function viewRoleDetails(roleId) {
    currentRoleId = roleId;
    const role = roles[roleId];
    
    if (role) {
      const createdDate = new Date(role.created_at);
      const formattedDate = isNaN(createdDate) ? 'Unknown' : createdDate.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      });
      
      document.getElementById
      document.getElementById('detailsRoleName').textContent = role.role_name || 'Unnamed Role';
      document.getElementById('detailsRoleDescription').textContent = role.description || 'No description';
      document.getElementById('detailsCreatedAt').textContent = formattedDate;
      document.getElementById('detailsUserCount').textContent = role.user_count || 0;
      
      <?php foreach (array_keys($allPermissionsByCategory) as $category): ?>
        const listEl<?= str_replace(' ', '', $category) ?> = document.getElementById('details<?= str_replace(' ', '', $category) ?>Permissions');
        if (listEl<?= str_replace(' ', '', $category) ?>) {
          listEl<?= str_replace(' ', '', $category) ?>.innerHTML = '';
          
          const categoryKey = '<?= strtolower(str_replace(' ', '_', $category)) ?>';
          const perms = role.permissions[categoryKey] || (categoryKey === 'uncategorized' ? role.permissions[''] : null);
          
          if (perms && perms.length > 0) {
            perms.forEach(perm => {
              let description = perm;
              <?php foreach ($allPermissionsByCategory[$category] as $p): ?>
                if (perm === '<?= $p['permission_name'] ?>') {
                  description = '<?= addslashes($p['description']) ?>';
                }
              <?php endforeach; ?>
              const li = document.createElement('li');
              li.textContent = description;
              listEl<?= str_replace(' ', '', $category) ?>.appendChild(li);
            });
          } else {
            const li = document.createElement('li');
            li.textContent = 'No permissions in this category';
            li.classList.add('no-permissions');
            listEl<?= str_replace(' ', '', $category) ?>.appendChild(li);
          }
        }
      <?php endforeach; ?>
      
      const editBtn = document.getElementById('editRoleBtn');
      if (editBtn) {
        editBtn.onclick = function() {
          closeRoleDetailsDialog();
          openEditRoleDialog(roleId);
        };
      }
      
      document.getElementById('roleDetailsDialog').showModal();
    }
  }

  // Close the role details dialog
  function closeRoleDetailsDialog() {
    const dialog = document.getElementById('roleDetailsDialog');
    if (dialog) {
      dialog.close();
    }
  }

  function deleteRole(roleId) {
    // Use currentRoleId if roleId is not provided or invalid
    const id = roleId || currentRoleId;
    if (!id) {
        alert('Error: Role ID is missing.');
        return;
    }

    if (confirm('Are you sure you want to delete this role?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/roles/${id}/delete`;

        // Add role_id to the form
        const roleIdInput = document.createElement('input');
        roleIdInput.type = 'hidden';
        roleIdInput.name = 'role_id';
        roleIdInput.value = id;
        form.appendChild(roleIdInput);

        document.body.appendChild(form);
        form.submit();
    }
    closeRoleDetailsDialog();
  }

</script>
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
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
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
            <!-- <?= $permissionCategories[$categoryKey] ?> Permissions -->
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
        <h3 id="detailsRoleName">Administrator</h3>
        <p id="detailsRoleDescription">Full system access with all permissions enabled. Manages all aspects of the system.</p>
        
        <div class="details-grid">
          <div class="details-item">
            <span class="details-label">Created</span>
            <span class="details-value" id="detailsCreatedAt">April 15, 2025</span>
          </div>
          <div class="details-item">
            <span class="details-label">Users with this role</span>
            <span class="details-value" id="detailsUserCount">5</span>
          </div>
        </div>

        <div class="details-divider"></div>
        
        <h4>Permissions</h4>
        <?php foreach (['user_management', 'inventory', 'point_of_sale'] as $category): ?>
          <div class="permission-category">
            <h5><?= htmlspecialchars($permissionCategories[$category]) ?></h5>
            <ul class="permission-details-list" id="details<?= ucfirst(str_replace('_', '', $category)) ?>Permissions">
              <!-- This will be populated by JavaScript -->
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeRoleDetailsDialog()">Close</button>
      <?php if (RBACService::hasPermission('manage_roles')): ?>
        <button type="button" class="btn btn-primary" id="editRoleBtn">Edit Role</button>
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
  const rolePermissions = <?= json_encode($rolePermissions) ?>;
  const roleUserCounts = <?= json_encode($roleUserCounts) ?>;
  const rolePermissionsDetails = <?= json_encode($rolePermissionsDetails) ?>;
  
  // DOM Ready
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initSearch();
    
    // Initialize permission tabs
    initPermissionTabs();
    
    // Initialize select all functionality
    initSelectAllPermissions();
  });
  
  // Initialize search functionality for roles
  function initSearch() {
    const searchInput = document.getElementById('searchInput');
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
  
  // Initialize permission tabs
  function initPermissionTabs() {
    const tabs = document.querySelectorAll('.permission-tab');
    
    tabs.forEach(tab => {
      tab.addEventListener('click', function() {
        // Hide all content
        document.querySelectorAll('.permission-content').forEach(content => {
          content.style.display = 'none';
        });
        
        // Remove active class from all tabs
        tabs.forEach(t => t.classList.remove('active'));
        
        // Add active class to clicked tab
        this.classList.add('active');
        
        // Show corresponding content
        const tabId = this.getAttribute('data-tab');
        document.getElementById(`${tabId}-tab`).style.display = 'block';
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
        const checkboxes = tabContent.querySelectorAll('input[type="checkbox"]');
        
        // Check if all are checked
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        // Toggle checkboxes
        checkboxes.forEach(cb => {
          cb.checked = !allChecked;
        });
        
        // Update text
        this.textContent = allChecked ? 'Select All' : 'Deselect All';
      });
    });
  }
  
  // Open the add role dialog
  function openAddRoleDialog() {
    // Reset form
    document.getElementById('roleForm').reset();
    document.getElementById('roleDialogTitle').textContent = 'Add New Role';
    document.getElementById('roleForm').action = '/roles/store';
    
    // Uncheck all checkboxes
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
      cb.checked = false;
    });
    
    // Show dialog
    document.getElementById('roleDialog').showModal();
    
    // Reset currentRoleId
    currentRoleId = null;
  }
  
  // Open the edit role dialog
  function openEditRoleDialog(roleId) {
    // Set currentRoleId
    currentRoleId = roleId;
    
    // Update dialog title and form action
    document.getElementById('roleDialogTitle').textContent = 'Edit Role';
    document.getElementById('roleForm').action = `/roles/${roleId}/update`;
    
    // Find role data
    const role = roles.find(r => r.id === roleId);
    
    if (role) {
      // Populate form
      document.getElementById('roleName').value = role.role_name;
      document.getElementById('roleDescription').value = role.description;
      
      // Reset all checkboxes
      document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
        cb.checked = false;
      });
      
      // Check the appropriate permissions
      if (rolePermissions[roleId]) {
        rolePermissions[roleId].forEach(perm => {
          const checkbox = document.querySelector(`input[value="${perm}"]`);
          if (checkbox) {
            checkbox.checked = true;
          }
        });
      }
    }
    
    // Show dialog
    document.getElementById('roleDialog').showModal();
  }
  
  // Close the role dialog
  function closeRoleDialog() {
    document.getElementById('roleDialog').close();
  }
  
  // View role details
  function viewRoleDetails(roleId) {
    // Set current role ID
    currentRoleId = roleId;
    
    // Find role data
    const role = roles.find(r => r.id === roleId);
    
    if (role) {
      // Format the date
      const createdDate = new Date(role.created_at);
      const formattedDate = createdDate.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      });
      
      // Update dialog content
      document.getElementById('detailsRoleName').textContent = role.role_name;
      document.getElementById('detailsRoleDescription').textContent = role.description;
      document.getElementById('detailsCreatedAt').textContent = formattedDate;
      document.getElementById('detailsUserCount').textContent = roleUserCounts[roleId] || 0;
      
      // Update permissions lists
      const permCategories = ['user_management', 'inventory', 'point_of_sale'];
      permCategories.forEach(cat => {
        const listEl = document.getElementById(`details${cat.replace(/_/g, '')}Permissions`);
        if (listEl) {
          // Clear the list
          listEl.innerHTML = '';
          
          // If the role has permissions in this category
          if (rolePermissionsDetails[roleId] && rolePermissionsDetails[roleId][cat]) {
            rolePermissionsDetails[roleId][cat].forEach(perm => {
              const li = document.createElement('li');
              li.textContent = perm;
              listEl.appendChild(li);
            });
          } else {
            const li = document.createElement('li');
            li.textContent = 'No permissions in this category';
            li.classList.add('no-permissions');
            listEl.appendChild(li);
          }
        }
      });
      
      // Update edit button if it exists
      const editBtn = document.getElementById('editRoleBtn');
      if (editBtn) {
        editBtn.onclick = function() {
          closeRoleDetailsDialog();
          openEditRoleDialog(roleId);
        };
      }
    }
    
    // Show dialog
    document.getElementById('roleDetailsDialog').showModal();
  }
  
  // Close the role details dialog
  function closeRoleDetailsDialog() {
    document.getElementById('roleDetailsDialog').close();
  }
  
  // Form submission handler
  document.getElementById('roleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const roleName = formData.get('role_name');
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Role "${roleName}" saved successfully!`);
            location.reload(); // Consider updating DOM instead
        } else {
            alert('Error saving role: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving role');
    });
    
    closeRoleDialog();
  });
</script>
<?php

use App\Services\RBACService;

?>

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <div class="card glass page-header">
      <div class="header-content">
        <h1>Role Management</h1>
        <p class="subtitle">Manage user roles and permissions to control access throughout the system</p>
      </div>
      <?php if (RBACService::hasPermission('role_create')): ?>
        <button class="btn btn-primary" onclick="openAddRoleDialog()">
          <span class="icon">add</span>
          Add Role
        </button>
      <?php endif; ?>
    </div>

    <div class="card glass controls">
      <div class="search-bar">
        <span class="icon">search</span>
        <input type="text" id="searchInput" placeholder="Search roles...">
      </div>
    </div>

    <div class="role-cards">
      <?php foreach ($roles as $roleId => $role): ?>
      <div class="role-card">
        <div class="role-card-header">
          <h3><?= htmlspecialchars($role['role_name'] ?? 'Unnamed Role') ?></h3>
        </div>
        <div class="role-card-body">
          <p class=""><?= htmlspecialchars($role['description'] ?? 'No description') ?></p>
          <div class="role-permission-count">
            <span class="icon">verified_user</span>
              <?= $role['permission_count'] ?? 0 ?> permissions
          </div>
          <div class="role-chips">
            <?php foreach ($role['permission_categories'] ?? [] as $catId => $category): ?>
              <div class="role-chip"><?= htmlspecialchars($category) ?></div>
            <?php endforeach; ?>
          </div>
        </div>
          
          <div class="card-actions">
            <button class="icon-btn secondary" onclick="viewRoleDetails(<?= $roleId ?>)" title="">
              <span class="icon">visibility</span>
            </button>
            <?php if (RBACService::hasPermission('role_update')): ?>
              <button class="icon-btn edit" onclick="openEditRoleDialog(<?= $roleId ?>)" title="Edit Role">
                <span class="icon">edit</span>
              </button>
            <?php endif; ?>
            <?php if (RBACService::hasPermission('role_delete')): ?>
              <button class="icon-btn danger" onclick="deleteRole(<?= htmlspecialchars($roleId) ?>)" title="Delete discount">
                <span class="icon">delete</span>
              </button>
            <?php endif; ?>
          </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php if (RBACService::hasPermission('role_create') || RBACService::hasPermission('role_update')): ?>
  <dialog id="roleDialog" class="modal">
    <div class="modal-content wide-modal">
      <div class="modal-header">
        <h2 id="roleDialogTitle">Add New Role</h2>
        <button class="icon-btn secondary" onclick="cancelEdit()">
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
        
          <button type="submit" class="icon-btn primary" title="Save Role">
            <span class="icon">save</span>
            </button>
        </div>
      </form>
    </div>
  </dialog>
<?php endif; ?>

<?php if (RBACService::hasPermission('role_view')): ?>
  <dialog id="roleDetailsDialog" class="modal">
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
                </ul>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p>No permission categories available.</p>
          <?php endif; ?>
        </div>
      </div>

    <div class="modal-footer">
      <?php if (RBACService::hasPermission('role_update')): ?>
          <button class="icon-btn edit" onclick="openEditRoleDialog(<?= $roleId ?>)" title="Edit Role">
            <span class="icon">edit</span>
          </button>
          <?php endif; ?>
          <?php if (RBACService::hasPermission('role_delete')): ?>
            <button class="icon-btn danger" onclick="deleteRole(<?= htmlspecialchars($roleId) ?>)" title="Delete discount">
              <span class="icon">delete</span>
            </button>
          <?php endif; ?>
    </div>
  </dialog>
  </div>
<?php endif; ?>

<link rel="stylesheet" href="/css/pages/roles.css">

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

  let initialPermissions = [];

  function openEditRoleDialog(roleId) {
    currentRoleId = roleId;
    const role = roles[roleId];
    const form = document.getElementById('roleForm');
    const dialogTitle = document.getElementById('roleDialogTitle');

    if (role && form && dialogTitle) {
      dialogTitle.textContent = 'Edit Role';
      form.action = `/roles/${roleId}/update`;

      document.getElementById('roleName').value = role.role_name || '';
      document.getElementById('roleDescription').value = role.description || '';

      document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
        cb.checked = false;
      });

      let roleIdInput = document.getElementById('role_id');
      if (!roleIdInput) {
        roleIdInput = document.createElement('input');
        roleIdInput.type = 'hidden';
        roleIdInput.id = 'roleIdInput';
        roleIdInput.name = 'role_id';
        form.appendChild(roleIdInput);
      }
      roleIdInput.value = roleId;
      console.log(roleIdInput);

      initialPermissions = [];
      const permissionIds = [];
      for (const category in role.permissions) {
        role.permissions[category].forEach(perm => {
          <?php foreach ($allPermissionsByCategory as $category => $permissions): ?>
            <?php foreach ($permissions as $p): ?>
              if (perm === '<?= $p['permission_name'] ?>') {
                permissionIds.push('<?= $p['id'] ?>');
                initialPermissions.push('<?= $p['id'] ?>');
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

      document.getElementById('roleDialog').showModal();
    }
  }

  document.getElementById('roleForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const currentPermissions = Array.from(document.querySelectorAll('input[name="permissions[]"]:checked')).map(cb => cb.value);

    const addedPermissions = currentPermissions.filter(id => !initialPermissions.includes(id));
    const removedPermissions = initialPermissions.filter(id => !currentPermissions.includes(id));

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

    this.submit();
  });

  function closeRoleDialog() {
    const dialog = document.getElementById('roleDialog');
    if (dialog) {
      dialog.close();
    }
  }

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

  function closeRoleDetailsDialog() {
    const dialog = document.getElementById('roleDetailsDialog');
    if (dialog) {
      dialog.close();
    }
  }

  function deleteRole(roleId) {
    const id = roleId || currentRoleId;
    if (!id) {
      alert('Error: Role ID is missing.');
      return;
    }

    if (confirm('Are you sure you want to delete this role?')) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/roles/${id}/delete`;

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
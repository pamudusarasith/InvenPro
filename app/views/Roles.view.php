<div class="body">
  <?php App\View::render("components/Navbar") ?>
  <?php App\View::render("components/Sidebar") ?>

  <div class="content">
    <!-- Header Section -->
    <div class="page-header">
      <div class="header-content">
        <h1>Role Management</h1>
        <p class="header-description">Manage user roles and their permissions</p>
      </div>
      <div class="header-actions">
        <button id="addRoleBtn" class="btn btn-primary">
          <span class="material-symbols-rounded">add</span>
          Add Role
        </button>
      </div>
      <div class="tab-navigation">
        <button class="tab-btn active" data-tab="roles">Roles</button>
        <button class="tab-btn" data-tab="permissions">Permissions</button>
      </div>
    </div>

    <div id="permissions-table" class="table-container glass" style="display: none;">
      <table class="permission-table">
        <thead>
          <tr>
            <th>Permission Name</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($permissions as $permission): ?>
            <tr data-id="<?= $permission['id'] ?>">
              <td><?= htmlspecialchars($permission['name']) ?></td>
              <td><?= htmlspecialchars($permission['description'] ?? '-') ?></td>
              <td>
                <div class="actions">
                  <button class="action-btn" title="Edit" onclick="permissionManager.editPermission(<?= $permission['id'] ?>)">
                    <span class="material-symbols-rounded">edit</span>
                  </button>
                  <button class="action-btn" title="Delete" onclick="permissionManager.deletePermission(<?= $permission['id'] ?>)">
                    <span class="material-symbols-rounded">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Role Table -->
    <div class="table-container glass">
      <table class="role-table">
        <thead>
          <tr>
            <th>Role Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($roles as $role): ?>
            <tr data-id="<?= $role['id'] ?>">
              <td><?= htmlspecialchars($role['name']) ?></td>
              <td><?= htmlspecialchars($role['description'] ?? '-') ?></td>
              <td>
                <span class="status-badge <?= $role['is_active'] ? 'active' : 'inactive' ?>">
                  <?= $role['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
              </td>
              <td>
                <div class="actions">
                  <button class="action-btn" title="Edit" onclick="roleManager.editRole(<?= $role['id'] ?>)">
                    <span class="material-symbols-rounded">edit</span>
                  </button>
                  <button class="action-btn" title="Delete" onclick="roleManager.deleteRole(<?= $role['id'] ?>)">
                    <span class="material-symbols-rounded">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Role Modal -->
  <dialog id="roleModal" class="modal">
    <form id="roleForm" method="dialog">
      <div class="modal-header">
        <h2 id="modalTitle">Add New Role</h2>
        <button type="button" class="close-btn">
          <span class="material-symbols-rounded">close</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="roleId" name="id">
        <div class="form-group">
          <label for="name">Role Name</label>
          <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
          <label for="description">Description</label>
          <textarea id="description" name="description" rows="3"></textarea>
        </div>
        <div class="form-group">
          <label>Permissions</label>
          <div class="permission-grid">
            <?php foreach ($permissions as $permission): ?>
              <label class="permission-item">
                <input type="checkbox" name="permissions[]"
                  value="<?= $permission['id'] ?>"
                  data-name="<?= htmlspecialchars($permission['name']) ?>">
                <span><?= htmlspecialchars($permission['name']) ?></span>
                <?php if ($permission['description']): ?>
                  <small><?= htmlspecialchars($permission['description']) ?></small>
                <?php endif; ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" id="isActive" name="is_active" checked>
            <span>Active Role</span>
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Role</button>
      </div>
    </form>
  </dialog>

  <dialog id="permissionModal" class="modal">
    <form id="permissionForm" method="dialog">
      <div class="modal-header">
        <h2 id="permissionModalTitle">Add New Permission</h2>
        <button type="button" class="close-btn">
          <span class="material-symbols-rounded">close</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="permissionId" name="id">
        <div class="form-group">
          <label for="permissionName">Permission Name</label>
          <input type="text" id="permissionName" name="name" required>
        </div>
        <div class="form-group">
          <label for="permissionDescription">Description</label>
          <textarea id="permissionDescription" name="description" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="this.closest('dialog').close()">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Permission</button>
      </div>
    </form>
  </dialog>
</div>

<style>
  /* Role Management Page */
  .content {
    padding: 2rem;
    background: var(--surface-light);
    height: 100%;
    overflow: auto;
  }

  .glass {
    background: var(--glass-white);
    backdrop-filter: blur(10px);
    border: 1px solid var(--border-light);
    border-radius: 16px;
    box-shadow: var(--shadow-md);
  }

  /* Header Section */
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
  }

  .header-content h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
  }

  .header-description {
    color: var(--text-secondary);
    font-size: 0.875rem;
  }

  /* Role Table */
  .table-container {
    overflow: hidden;
  }

  .role-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }

  .role-table th {
    background: var(--surface-light);
    padding: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    text-align: left;
    letter-spacing: 0.05em;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border-light);
  }

  .role-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-light);
    vertical-align: middle;
  }

  .permission-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .permission-chip {
    padding: 0.25rem 0.75rem;
    background: var(--primary-50);
    color: var(--primary-600);
    border-radius: 1rem;
    font-size: 0.75rem;
    white-space: nowrap;
  }

  .status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.85rem;
    font-weight: 500;
  }

  .status-badge.active {
    background: var(--success-50);
    color: var(--success-600);
  }

  .status-badge.inactive {
    background: var(--danger-50);
    color: var(--danger-600);
  }

  .actions {
    display: flex;
    gap: 0.5rem;
  }

  .action-btn {
    padding: 0.5rem;
    border: none;
    border-radius: 6px;
    color: var(--text-tertiary);
    background: transparent;
    cursor: pointer;
    transition: all 0.2s;
  }

  .action-btn:hover {
    background: var(--surface-light);
    color: var(--text-primary);
  }

  /* Modal Dialog */
  .modal {
    border: none;
    border-radius: 16px;
    padding: 0;
    max-width: 600px;
    width: 90%;
    background: var(--surface-white);
    box-shadow: var(--shadow-lg);
  }

  .modal::backdrop {
    background: var(--glass-dark);
    backdrop-filter: blur(4px);
  }

  .modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .modal-body {
    padding: 1.5rem;
  }

  .modal-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border-light);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
  }

  /* Permission Grid */
  .permission-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    padding: 1rem;
    background: var(--surface-light);
    border-radius: 8px;
    max-height: 300px;
    overflow-y: auto;
  }

  .permission-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    padding: 0.75rem;
    background: var(--surface-white);
    border-radius: 8px;
    cursor: pointer;
  }

  .permission-item input[type="checkbox"] {
    margin-right: 0.5rem;
  }

  .permission-item small {
    color: var(--text-tertiary);
    font-size: 0.75rem;
  }

  /* Form Elements */
  .form-group {
    margin-bottom: 1.5rem;
  }

  .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-primary);
  }

  .form-group input,
  .form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-medium);
    border-radius: 8px;
    color: var(--text-primary);
    background: var(--surface-white);
  }

  .checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
  }

  .tab-navigation {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 1px solid var(--border-light);
    padding-bottom: 1rem;
  }

  .tab-btn {
    padding: 0.5rem 1rem;
    border: none;
    background: none;
    color: var(--text-secondary);
    font-weight: 500;
    cursor: pointer;
    position: relative;
  }

  .tab-btn.active {
    color: var(--primary-600);
  }

  .tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -1rem;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--primary-600);
  }

  .permission-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }

  .permission-table th,
  .permission-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-light);
  }

  .permission-table th {
    background: var(--surface-light);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-secondary);
  }

  /* Responsive Design */
  @media (max-width: 1024px) {
    .page-header {
      flex-direction: column;
      gap: 1rem;
    }

    .permission-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 768px) {
    .content {
      padding: 1rem;
    }

    .table-container {
      overflow-x: auto;
    }

    .modal {
      width: 95%;
    }

    .permission-chips {
      display: none;
    }
  }
</style>

<script>
  class RoleManager {
    constructor() {
      this.modal = document.getElementById('roleModal');
      this.form = document.getElementById('roleForm');
      this.currentId = null;

      this.init();
    }

    init() {
      // Add role button
      document.getElementById('addRoleBtn').addEventListener('click', () => {
        this.openModal();
      });

      // Form submission
      this.form.addEventListener('submit', (e) => {
        e.preventDefault();
        this.handleSubmit();
      });

      // Modal close button
      this.modal.querySelector('.close-btn').addEventListener('click', () => {
        this.closeModal();
      });

      // Cancel button
      document.getElementById('cancelBtn').addEventListener('click', () => {
        this.closeModal();
      });
    }

    openModal(roleId = null) {
      this.currentId = roleId;
      this.form.reset();

      const title = this.modal.querySelector('#modalTitle');
      title.textContent = roleId ? 'Edit Role' : 'Add New Role';

      if (roleId) {
        this.loadRole(roleId);
      }

      this.modal.showModal();
    }

    closeModal() {
      this.modal.close();
      this.currentId = null;
    }

    async loadRole(id) {
      try {
        const response = await fetch(`/api/roles/${id}`);
        if (!response.ok) throw new Error('Failed to load role');

        const role = await response.json();
        this.populateForm(role);
      } catch (error) {
        this.showError('Failed to load role details');
        console.error(error);
      }
    }

    populateForm(role) {
      document.getElementById('roleId').value = role.id;
      document.getElementById('name').value = role.name;
      document.getElementById('description').value = role.description || '';
      document.getElementById('isActive').checked = role.is_active;

      // Clear all permission checkboxes
      this.form.querySelectorAll('input[name="permissions[]"]')
        .forEach(checkbox => checkbox.checked = false);

      // Check permissions that role has
      role.permissions.forEach(permission => {
        const checkbox = this.form.querySelector(
          `input[name="permissions[]"][value="${permission.id}"]`
        );
        if (checkbox) checkbox.checked = true;
      });
    }

    async handleSubmit() {
      const formData = new FormData(this.form);
      const permissions = Array.from(formData.getAll('permissions[]'));

      const data = {
        name: formData.get('name'),
        description: formData.get('description'),
        is_active: formData.get('is_active') === 'on',
        permissions: permissions
      };

      if (!this.validateForm(data)) {
        return;
      }

      try {
        const url = this.currentId ?
          `/api/roles/${this.currentId}` :
          '/api/roles';

        const method = this.currentId ? 'PUT' : 'POST';

        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        });

        if (!response.ok) throw new Error('Failed to save role');

        this.closeModal();
        this.refreshTable();
        this.showSuccess(`Role successfully ${this.currentId ? 'updated' : 'created'}`);

      } catch (error) {
        this.showError('Failed to save role');
        console.error(error);
      }
    }

    async deleteRole(id) {
      if (!confirm('Are you sure you want to delete this role?')) {
        return;
      }

      try {
        const response = await fetch(`/api/roles/${id}`, {
          method: 'DELETE'
        });

        if (!response.ok) throw new Error('Failed to delete role');

        this.refreshTable();
        this.showSuccess('Role successfully deleted');

      } catch (error) {
        this.showError('Failed to delete role');
        console.error(error);
      }
    }

    validateForm(data) {
      if (!data.name?.trim()) {
        this.showError('Role name is required');
        return false;
      }

      if (!data.permissions.length) {
        this.showError('Select at least one permission');
        return false;
      }

      return true;
    }

    async refreshTable() {
      try {
        const response = await fetch('/api/roles');
        if (!response.ok) throw new Error('Failed to refresh roles');

        const roles = await response.json();
        const tbody = document.querySelector('.role-table tbody');
        tbody.innerHTML = roles.map(role => this.createRoleRow(role)).join('');

      } catch (error) {
        this.showError('Failed to refresh roles list');
        console.error(error);
      }
    }

    createRoleRow(role) {
      const permissionChips = role.permissions
        .map(p => `<span class="permission-chip">${p.name}</span>`)
        .join('');

      return `
            <tr data-id="${role.id}">
                <td>${role.name}</td>
                <td>${role.description || '-'}</td>
                <td>
                    <div class="permission-chips">${permissionChips}</div>
                </td>
                <td>
                    <span class="status-badge ${role.is_active ? 'active' : 'inactive'}">
                        ${role.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <button class="action-btn" title="Edit" onclick="roleManager.editRole(${role.id})">
                            <span class="material-symbols-rounded">edit</span>
                        </button>
                        <button class="action-btn" title="Delete" onclick="roleManager.deleteRole(${role.id})">
                            <span class="material-symbols-rounded">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    showError(message) {
      this.showNotification(message, 'error');
    }

    showSuccess(message) {
      this.showNotification(message, 'success');
    }

    showNotification(message, type = 'success') {
      const notification = document.createElement('div');
      notification.className = `notification ${type}`;
      notification.textContent = message;

      document.body.appendChild(notification);

      setTimeout(() => {
        notification.remove();
      }, 3000);
    }
  }

  // Initialize when document loads
  document.addEventListener('DOMContentLoaded', () => {
    window.roleManager = new RoleManager();
  });

  // Add to existing JavaScript
  class PermissionManager {
    constructor() {
      this.modal = document.getElementById('permissionModal');
      this.form = document.getElementById('permissionForm');
      this.currentId = null;

      this.init();
    }

    init() {
      this.form.addEventListener('submit', (e) => {
        e.preventDefault();
        this.handleSubmit();
      });

      // Tab switching
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => this.switchTab(btn.dataset.tab));
      });
    }

    switchTab(tab) {
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tab === tab);
      });

      document.getElementById('permissions-table').style.display =
        tab === 'permissions' ? 'block' : 'none';
      document.querySelector('.role-table').closest('.table-container').style.display =
        tab === 'roles' ? 'block' : 'none';
    }

    openModal(permissionId = null) {
      this.currentId = permissionId;
      this.form.reset();

      const title = this.modal.querySelector('#permissionModalTitle');
      title.textContent = permissionId ? 'Edit Permission' : 'Add New Permission';

      if (permissionId) {
        this.loadPermission(permissionId);
      }

      this.modal.showModal();
    }

    async loadPermission(id) {
      try {
        const response = await fetch(`/api/permissions/${id}`);
        if (!response.ok) throw new Error('Failed to load permission');

        const permission = await response.json();
        this.populateForm(permission);
      } catch (error) {
        this.showError('Failed to load permission details');
      }
    }

    populateForm(permission) {
      document.getElementById('permissionId').value = permission.id;
      document.getElementById('permissionName').value = permission.name;
      document.getElementById('permissionDescription').value = permission.description || '';
    }

    async handleSubmit() {
      const formData = new FormData(this.form);
      const data = {
        name: formData.get('name'),
        description: formData.get('description')
      };

      if (!this.validateForm(data)) return;

      try {
        const url = this.currentId ?
          `/api/permissions/${this.currentId}` :
          '/api/permissions';

        const method = this.currentId ? 'PUT' : 'POST';

        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        });

        if (!response.ok) throw new Error('Failed to save permission');

        this.modal.close();
        this.refreshTable();
        this.showSuccess(`Permission successfully ${this.currentId ? 'updated' : 'created'}`);

      } catch (error) {
        this.showError('Failed to save permission');
      }
    }

    validateForm(data) {
      if (!data.name?.trim()) {
        this.showError('Permission name is required');
        return false;
      }
      return true;
    }

    async deletePermission(id) {
      if (!confirm('Are you sure you want to delete this permission?')) return;

      try {
        const response = await fetch(`/api/permissions/${id}`, {
          method: 'DELETE'
        });

        if (!response.ok) throw new Error('Failed to delete permission');

        this.refreshTable();
        this.showSuccess('Permission successfully deleted');

      } catch (error) {
        this.showError('Failed to delete permission');
      }
    }

    async refreshTable() {
      try {
        const response = await fetch('/api/permissions');
        if (!response.ok) throw new Error('Failed to refresh permissions');

        const permissions = await response.json();
        const tbody = document.querySelector('.permission-table tbody');
        tbody.innerHTML = permissions.map(permission => this.createPermissionRow(permission)).join('');

      } catch (error) {
        this.showError('Failed to refresh permissions list');
      }
    }

    createPermissionRow(permission) {
      return `
            <tr data-id="${permission.id}">
                <td>${permission.name}</td>
                <td>${permission.description || '-'}</td>
                <td>
                    <div class="actions">
                        <button class="action-btn" title="Edit" onclick="permissionManager.editPermission(${permission.id})">
                            <span class="material-symbols-rounded">edit</span>
                        </button>
                        <button class="action-btn" title="Delete" onclick="permissionManager.deletePermission(${permission.id})">
                            <span class="material-symbols-rounded">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    showError(message) {
      roleManager.showError(message);
    }

    showSuccess(message) {
      roleManager.showSuccess(message);
    }
  }

  // Initialize both managers
  document.addEventListener('DOMContentLoaded', () => {
    window.roleManager = new RoleManager();
    window.permissionManager = new PermissionManager();
  });
</script>
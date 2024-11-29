<div class="body">
  <?php App\View::render("components/Navbar") ?>
  <?php App\View::render("components/Sidebar") ?>

  <div class="content">
    <!-- Header Section -->
    <div class="page-header">
      <div class="header-content">
        <h1>User Management</h1>
        <p class="header-description">Manage your users, roles and permissions</p>
      </div>
      <div class="header-actions">
        <button id="addUserBtn" class="btn btn-primary">
          <span class="material-symbols-rounded">person_add</span>
          Add User
        </button>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card glass">
        <div class="stat-icon" style="background: var(--primary-50)">
          <span class="material-symbols-rounded" style="color: var(--primary-600)">group</span>
        </div>
        <div class="stat-info">
          <h3>Total Users</h3>
          <p id="totalUsers">0</p>
        </div>
      </div>
      <div class="stat-card glass">
        <div class="stat-icon" style="background: var(--success-50)">
          <span class="material-symbols-rounded" style="color: var(--success-600)">check_circle</span>
        </div>
        <div class="stat-info">
          <h3>Active Users</h3>
          <p id="activeUsers">0</p>
        </div>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section glass">
      <div class="search-box">
        <span class="material-symbols-rounded">search</span>
        <input type="text" id="searchInput" placeholder="Search users...">
      </div>
      <div class="filter-actions">
        <div class="filter-group">
          <label>Status</label>
          <div class="filter-chips">
            <button class="filter-chip active" data-filter="all">
              All
              <span class="chip-count">0</span>
            </button>
            <button class="filter-chip" data-filter="active">
              Active
              <span class="chip-count">0</span>
            </button>
            <button class="filter-chip" data-filter="inactive">
              Inactive
              <span class="chip-count">0</span>
            </button>
          </div>
        </div>
        <div class="filter-group">
          <label>Role</label>
          <select id="roleFilter" class="filter-select">
            <option value="">All Roles</option>
            <?php foreach ($roles as $role): ?>
              <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="filter-group">
          <label>Branch</label>
          <select id="branchFilter" class="filter-select">
            <option value="">All Branches</option>
            <option value="1">Main Branch</option>
            <?php foreach ($branches as $branch): ?>
              <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <!-- User Table -->
    <div class="table-container glass">
      <table class="user-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Role</th>
            <th>Branch</th>
            <th>Joining Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="userTableBody">
          <?php foreach ($users as $user): ?>
            <tr data-id="<?= $user['id'] ?>">
              <td>
                <div class="user-info">
                  <div class="avatar">
                    <?= strtoupper(substr($user['full_name'], 0, 2)) ?>
                  </div>
                  <div>
                    <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                    <span><?= htmlspecialchars($user['email']) ?></span>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($user['role_name']) ?></td>
              <td><?= htmlspecialchars($user['branch_name']) ?></td>
              <td><?= date('M d, Y', strtotime($user['joining_date'])) ?></td>
              <td>
                <span class="status-badge <?= $user['is_active'] ? 'active' : 'inactive' ?>">
                  <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
              </td>
              <td>
                <div class="actions">
                  <button class="action-btn" title="Edit">
                    <span class="material-symbols-rounded">edit</span>
                  </button>
                  <button class="action-btn" title="Delete">
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

  <!-- Add/Edit User Modal -->
  <dialog id="userModal" class="modal">
    <form id="userForm" method="dialog">
      <div class="modal-header">
        <h2 id="modalTitle">Add New User</h2>
        <button type="button" class="close-btn">
          <span class="material-symbols-rounded">close</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="userId">
        <div class="form-group">
          <label for="fullName">Full Name</label>
          <input type="text" id="fullName" name="full_name" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role_id" required>
              <?php foreach ($roles as $role): ?>
                <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="branch">Branch</label>
            <select id="branch" name="branch_id" required>
              <option value="1">Main Branch</option>
              <?php foreach ($branches as $branch): ?>
                <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="tel" id="phone" name="phone_number">
        </div>
        <div class="form-group">
          <label for="address">Address</label>
          <textarea id="address" rows="3" name="address"></textarea>
        </div>
        <div class="form-group">
          <label for="joiningDate">Joining Date</label>
          <input type="date" id="joiningDate" name="joining_date" required>
        </div>
        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" id="isActive" name="is_active" checked>
            <span>Active User</span>
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
        <button type="submit" class="btn btn-primary">Save User</button>
      </div>
    </form>
  </dialog>
</div>

<style>
  /* Base Layout */
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

  /* Stats Grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  .stat-card {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.5rem;
    transition: transform 0.2s ease;
  }

  .stat-card:hover {
    transform: translateY(-2px);
  }

  .stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
    border-radius: 12px;
  }

  .stat-info h3 {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
  }

  .stat-info p {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
  }

  /* Filters Section */
  .filters-section {
    padding: 1.5rem;
    margin-bottom: 2rem;
  }

  .search-box {
    display: flex;
    align-items: center;
    background: var(--surface-white);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--border-light);
  }

  .search-box input {
    border: none;
    outline: none;
    width: 100%;
    margin-left: 0.75rem;
    font-size: 0.875rem;
    color: var(--text-primary);
  }

  .filter-actions {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
  }

  .filter-group {
    flex: 1;
    min-width: 200px;
  }

  .filter-group label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .filter-select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid var(--border-medium);
    border-radius: 8px;
    color: var(--text-primary);
    background: var(--surface-white);
  }

  .filter-chips {
    display: flex;
    gap: 0.75rem;
  }

  .filter-chip {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    background: var(--surface-white);
    border: 1px solid var(--border-medium);
    color: var(--text-secondary);
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
  }

  .filter-chip:hover {
    background: var(--primary-50);
    color: var(--primary-600);
    border-color: var(--primary-200);
  }

  .filter-chip.active {
    background: var(--primary-600);
    color: var(--surface-white);
    border-color: var(--primary-600);
  }

  /* User Table */
  .table-container {
    overflow: hidden;
  }

  .user-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }

  .user-table th {
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

  .user-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-light);
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .avatar {
    width: 40px;
    height: 40px;
    background: var(--primary-100);
    color: var(--primary-600);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
  }

  .user-info h4 {
    color: var(--text-primary);
    margin-bottom: 0.25rem;
  }

  .user-info span {
    font-size: 0.85rem;
    color: var(--text-tertiary);
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
  .form-group select,
  .form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-medium);
    border-radius: 8px;
    color: var(--text-primary);
    background: var(--surface-white);
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  /* Responsive Design */
  @media (max-width: 1024px) {
    .page-header {
      flex-direction: column;
      gap: 1rem;
    }

    .filter-actions {
      flex-direction: column;
      gap: 1rem;
    }

    .filter-group {
      width: 100%;
    }
  }

  @media (max-width: 768px) {
    .content {
      padding: 1rem;
    }

    .user-info {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5rem;
    }

    .table-container {
      overflow-x: auto;
    }

    .modal {
      width: 95%;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Core class for user management
    class UserManager {
      constructor() {
        this.users = [];
        this.modal = document.getElementById('userModal');
        this.form = document.getElementById('userForm');
        this.searchInput = document.getElementById('searchInput');
        this.roleFilter = document.getElementById('roleFilter');
        this.branchFilter = document.getElementById('branchFilter');
        this.filterChips = document.querySelectorAll('.filter-chip');

        this.init();
      }

      init() {
        this.loadUsers();
        this.setupEventListeners();
        this.updateStats();
      }

      async loadUsers() {
        try {
          const response = await fetch('/api/users');
          this.users = await response.json();
          this.updateTable();
          this.updateFilterCounts();
        } catch (error) {
          console.error('Error loading users:', error);
        }
      }

      setupEventListeners() {
        // Add user button
        document.getElementById('addUserBtn').addEventListener('click', () => {
          this.openModal();
        });

        // Search input
        this.searchInput.addEventListener('input', () => {
          this.filterUsers();
        });

        // Filter selects
        this.roleFilter.addEventListener('change', () => this.filterUsers());
        this.branchFilter.addEventListener('change', () => this.filterUsers());

        // Status filter chips
        this.filterChips.forEach(chip => {
          chip.addEventListener('click', () => {
            this.filterChips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            this.filterUsers();
          });
        });

        // Form submission
        this.form.addEventListener('submit', (e) => {
          e.preventDefault();
          this.handleSubmit();
        });

        // Modal close button
        this.modal.querySelector('.close-btn').addEventListener('click', () => {
          this.modal.close();
        });

        // Action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
          btn.addEventListener('click', (e) => {
            const action = e.currentTarget.title.toLowerCase();
            const row = e.currentTarget.closest('tr');
            const userId = row.dataset.id;

            if (action === 'edit') {
              this.openModal(userId);
            } else if (action === 'delete') {
              this.deleteUser(userId);
            }
          });
        });
      }

      openModal(userId = null) {
        this.form.reset();
        const title = this.modal.querySelector('#modalTitle');

        if (userId) {
          const user = this.users.find(e => e.id === userId);
          if (user) {
            title.textContent = 'Edit User';
            this.populateForm(user);
          }
        } else {
          title.textContent = 'Add New User';
        }

        this.modal.showModal();
      }

      populateForm(user) {
        const fields = ['userId', 'fullName', 'email', 'role', 'branch',
          'phone', 'address', 'joiningDate'
        ];

        fields.forEach(field => {
          const element = document.getElementById(field);
          if (element) {
            element.value = user[field] || '';
          }
        });

        document.getElementById('isActive').checked = user.is_active;
      }

      async handleSubmit() {
        const formData = new FormData(this.form);
        const userData = Object.fromEntries(formData);

        try {
          const url = userData.userId ? '/users/edit' : '/users/create';
          const response = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
          });
          const result = await response.json();

          if (result.success) {
            await this.loadUsers();
            this.modal.close();
          }
        } catch (error) {
          console.error('Error saving user:', error);
        }
      }

      async deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
          try {
            const response = await fetch(`/users?id=${userId}`, {
              method: 'GET'
            });

            if (response.ok) {
              await this.loadUsers();
            }
          } catch (error) {
            console.error('Error deleting user:', error);
          }
        }
      }

      filterUsers() {
        const searchTerm = this.searchInput.value.toLowerCase();
        const roleId = this.roleFilter.value;
        const branchId = this.branchFilter.value;
        const status = document.querySelector('.filter-chip.active').dataset.filter;

        const rows = document.querySelectorAll('#userTableBody tr');
        rows.forEach(row => {
          const user = this.users.find(e => e.id === row.dataset.id);
          if (!user) return;

          const matchesSearch = user.full_name.toLowerCase().includes(searchTerm) ||
            user.email.toLowerCase().includes(searchTerm);
          const matchesRole = !roleId || user.role_id === roleId;
          const matchesBranch = !branchId || user.branch_id === branchId;
          const matchesStatus = status === 'all' ||
            (status === 'active' && user.is_active) ||
            (status === 'inactive' && !user.is_active);

          row.style.display = matchesSearch && matchesRole &&
            matchesBranch && matchesStatus ? '' : 'none';
        });

        this.updateStats();
      }

      updateStats() {
        const visibleRows = document.querySelectorAll('#userTableBody tr:not([style*="display: none"])');
        const activeUsers = Array.from(visibleRows)
          .filter(row => row.querySelector('.status-badge.active')).length;

        document.getElementById('totalUsers').textContent = visibleRows.length;
        document.getElementById('activeUsers').textContent = activeUsers;

        // Calculate recent hires (last 30 days)
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

        const recentHires = Array.from(visibleRows).filter(row => {
          const user = this.users.find(e => e.id === row.dataset.id);
          return new Date(user.joining_date) >= thirtyDaysAgo;
        }).length;

        document.getElementById('recentHires').textContent = recentHires;
      }

      updateFilterCounts() {
        this.filterChips.forEach(chip => {
          const status = chip.dataset.filter;
          let count = 0;

          if (status === 'all') {
            count = this.users.length;
          } else if (status === 'active') {
            count = this.users.filter(e => e.is_active).length;
          } else if (status === 'inactive') {
            count = this.users.filter(e => !e.is_active).length;
          }

          chip.querySelector('.chip-count').textContent = count;
        });
      }
    }

    // Initialize the user manager
    const userManager = new UserManager();
  });
</script>
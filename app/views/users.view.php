<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        body {
            background-color: #f0f2f5;
            color: #1f2937;
            line-height: 1.5;
        }

        .container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 7vh);
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-color: transparent;
            border-radius: 12px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn-primary {
            background-color: #1976d2;
            color: white;
        }

        .btn-primary:hover {
            background-color: #1565c0;
        }

        .btn-secondary {
            background-color: #e0e0e0;
            color: black;
            }

        .btn-secondary:hover {
            background-color: #d0d0d0;
        }

        .search-section {
            margin-bottom: 20px;
        }

        .search-input {
            width: 100%;
            padding: 10px 40px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            color: #6b7280;
            background-color: #e0e0e0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 12px center;
            background-size: 20px;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .filter-btn {
            background: none;
            border: none;
            padding: 6px 12px;
            margin-right: 12px;
            font-weight: 600;
            cursor: pointer;
            color: #6b7280;
        }

        .filter-btn.active {
            color: #2563eb;
        }

        .table-container {
            width: 100%;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            text-align: left;
            padding: 12px 16px;
            background-color: #f0f2f5;
            
            font-weight: 600;
            color: #374151;
        }

        td {
            padding: 20px 16px;
            border-top: 1px solid #e0e0e0;
        }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .clickable-row:hover {
            background-color: #f0f2f5;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            min-width: 80px;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            padding: 20px;
        }

        .modal.visible {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 24px;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 24px;
            color: #111827;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 0;
                border-radius: 0;
                min-height: 100vh;
            }

            .header-section {
                flex-direction: column;
                gap: 16px;
                align-items: stretch;
            }

            .btn {
                width: 100%;
            }

            .table-container {
                margin: 0 -20px;
                width: calc(100% + 40px);
            }

            .modal-content {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <h1 class="page-title">Users</h1>
            <button class="btn btn-primary" onclick="openModal('addUserModal')">
                Add User
            </button>
        </div>

        <div class="search-section">
            <input type="text" class="search-input" placeholder="Search User">
        </div>

        <div class="filter-section">
            <button class="filter-btn active">All</button>
            <button class="filter-btn">Active</button>
            <button class="filter-btn">Inactive</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Branch</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="clickable-row" onclick="openUserDetails(<?= htmlspecialchars(json_encode($user)) ?>)">
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['branch']) ?></td>
                            <td>
                                <span class="status-badge <?= $user['status'] == 'active' ? 'status-active' : 'status-inactive' ?>">
                                    <?= ucfirst(htmlspecialchars($user['status'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <h2 class="modal-title">Add New User</h2>
            <form action="/users/add" method="POST">
                <div class="form-group">
                    <label class="form-label" for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Phone No</label>
                    <input type="tel" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="role_id">Role</label>
                    <select id="role_id" name="role_id" class="form-control" required>
                        <option value="">Select Role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="branch_id">Branch</label>
                    <select id="branch_id" name="branch_id" class="form-control" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="viewEditUserModal" class="modal">
        <div class="modal-content">
            <h2 class="modal-title">User Details</h2>
            <form id="editUserForm" action="/users/update" method="POST">
                <input type="hidden" id="edit_user_id" name="id">
                <div class="form-group">
                    <label class="form-label" for="edit_full_name">Full Name</label>
                    <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_phone">Phone No</label>
                    <input type="tel" id="edit_phone" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_role_id">Role</label>
                    <select id="edit_role_id" name="role_id" class="form-control" required>
                        <option value="">Select Role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_branch_id">Branch</label>
                    <select id="edit_branch_id" name="branch_id" class="form-control" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_status">Status</label>
                    <select id="edit_status" name="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('viewEditUserModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const searchInput = document.querySelector('.search-input');
        const filterButtons = document.querySelectorAll('.filter-btn');
        const userTable = document.querySelector('table tbody');
        const addUserForm = document.querySelector('#addUserModal form');
        const editUserForm = document.getElementById('editUserForm');

        let originalTableData = [];

        document.addEventListener('DOMContentLoaded', () => {
            originalTableData = Array.from(userTable.querySelectorAll('tr')).map(row => ({
                element: row,
                searchText: row.textContent.toLowerCase(),
                status: row.querySelector('.status-badge').textContent.trim().toLowerCase()
            }));

            setupEventListeners();
        });

        function setupEventListeners() {
            searchInput.addEventListener('input', handleSearch);

            filterButtons.forEach(button => {
                button.addEventListener('click', handleFilter);
            });

            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeModal(modal.id);
                    }
                });
            });

            addUserForm.addEventListener('submit', handleAddUser);
            editUserForm.addEventListener('submit', handleEditUser);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeAllModals();
                }
            });
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('visible');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('visible');
            document.body.style.overflow = 'auto';
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
            }
        }

        function closeAllModals() {
            document.querySelectorAll('.modal').forEach(modal => {
                closeModal(modal.id);
            });
        }

        function handleSearch(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            originalTableData.forEach(({ element, searchText }) => {
                const isVisible = searchText.includes(searchTerm);
                element.style.display = isVisible ? '' : 'none';
            });
        }

        function handleFilter(e) {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            e.target.classList.add('active');

            const filterValue = e.target.textContent.toLowerCase();
            
            originalTableData.forEach(({ element, status }) => {
                if (filterValue === 'all') {
                    element.style.display = '';
                } else {
                    element.style.display = status === filterValue ? '' : 'none';
                }
            });
        }

        function openUserDetails(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_full_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_phone').value = user.phone;
            document.getElementById('edit_role_id').value = user.role_id;
            document.getElementById('edit_branch_id').value = user.branch_id;
            document.getElementById('edit_status').value = user.status;

            openModal('viewEditUserModal');
        }

        function handleAddUser(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const userData = Object.fromEntries(formData.entries());

            if (!validateUserData(userData)) {
                return;
            }

            fetch('/users/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addUserToTable(data.user);
                    closeModal('addUserModal');
                    showNotification('User added successfully');
                } else {
                    showNotification(data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to add user', 'error');
            });
        }

        function handleEditUser(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const userData = Object.fromEntries(formData.entries());

            if (!validateUserData(userData)) {
                return;
            }

            fetch('/users/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateUserInTable(data.user);
                    closeModal('viewEditUserModal');
                    showNotification('User updated successfully');
                } else {
                    showNotification(data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to update user', 'error');
            });
        }

        function validateUserData(userData) {
            const requiredFields = ['full_name', 'email', 'phone', 'role_id', 'branch_id'];
            
            for (const field of requiredFields) {
                if (!userData[field]) {
                    showNotification(`Please fill in the ${field.replace('_', ' ')}`, 'error');
                    return false;
                }
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(userData.email)) {
                showNotification('Please enter a valid email address', 'error');
                return false;
            }

            const phoneRegex = /^\+?[\d\s-]{10,}$/;
            if (!phoneRegex.test(userData.phone)) {
                showNotification('Please enter a valid phone number', 'error');
                return false;
            }

            return true;
        }

        function addUserToTable(user) {
            const row = createUserTableRow(user);
            userTable.insertBefore(row, userTable.firstChild);
            originalTableData.unshift({
                element: row,
                searchText: row.textContent.toLowerCase(),
                status: user.status.toLowerCase()
            });
        }

        function updateUserInTable(user) {
            const existingRow = document.querySelector(`tr[data-user-id="${user.id}"]`);
            if (existingRow) {
                const newRow = createUserTableRow(user);
                existingRow.replaceWith(newRow);
                const index = originalTableData.findIndex(item => item.element === existingRow);
                if (index !== -1) {
                    originalTableData[index] = {
                        element: newRow,
                        searchText: newRow.textContent.toLowerCase(),
                        status: user.status.toLowerCase()
                    };
                }
            }
        }

        function createUserTableRow(user) {
            const row = document.createElement('tr');
            row.className = 'clickable-row';
            row.setAttribute('data-user-id', user.id);
            row.onclick = () => openUserDetails(user);

            row.innerHTML = `
                <td>${user.id}</td>
                <td>${user.name}</td>
                <td>${user.role}</td>
                <td>${user.branch}</td>
                <td>
                    <span class="status-badge ${user.status === 'active' ? 'status-active' : 'status-inactive'}">
                        ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                    </span>
                </td>
            `;

            return row;
        }

        function showNotification(message, type = 'success') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;

            // Add to document
            document.body.appendChild(notification);

            // Add styles dynamically if they don't exist
            if (!document.querySelector('#notification-styles')) {
                const styles = document.createElement('style');
                styles.id = 'notification-styles';
                styles.textContent = `
                    .notification {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        padding: 12px 24px;
                        border-radius: 4px;
                        color: white;
                        font-weight: 500;
                        animation: slideIn 0.3s ease-out forwards;
                        z-index: 1000;
                    }
                    .notification.success {
                        background-color: #10b981;
                    }
                    .notification.error {
                        background-color: #ef4444;
                    }
                    @keyframes slideIn {
                        from { transform: translateX(100%); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                    @keyframes slideOut {
                        from { transform: translateX(0); opacity: 1; }
                        to { transform: translateX(100%); opacity: 0; }
                    }
                `;
                document.head.appendChild(styles);
            }

            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-in forwards';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }
    </script>
</body>    
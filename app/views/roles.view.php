<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions</title>
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
        
        .role-list {
            padding: 20px;
        }

        .role-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 16px;
            border-bottom: 1px solid #e0e0e0;
            transition: background-color 0.2s;
        }

        .role-item:hover {
            background-color: #f0f2f5;
        }

        .role-item:last-child {
            border-bottom: none;
        }

        .role-name {
            font-weight: 600;
            color: #374151;
        }

        .role-details {
            font-size: 14px;
            color: #6b7280;
        }

        .edit-btn {
            background: none;
            border: none;
            color: #1976d2;
            cursor: pointer;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .edit-btn:hover {
            background-color: #e8f0fe;
        }

        .add-role-btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background-color: transparent;
            border: 2px dashed #1976d2;
            color: #1976d2;
            text-align: center;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        .add-role-btn:hover {
            background-color: #e8f0fe;
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
            background-color: #f0f2f5;
            padding: 24px;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            font-size: 24px;
            cursor: pointer;
            background: none;
            border: none;
            color: #6b7280;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 24px;
            color: #111827;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }

        .form-control {
            background-color: #f0f2f5;
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

        .checkbox-group {
            margin-top: 12px;
        }

        .checkbox-item {
            background-color: #f0f2f5;
            color: #f0f2f5;
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .checkbox-item input[type="checkbox"] {
            border-color: #d1d5db;
            margin-right: 10px;
        }

        .checkbox-item label {
            font-size: 14px;
            color: #374151;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
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

        @media (max-width: 768px) {
            .table-container {
                margin: 0 -20px;
                width: calc(100% + 40px);
                border-radius: 0;
            }

            .role-name {
                min-width: 150px;
            }

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
        }
    </style>
</head>
<body>
<div class="container">
        <div class="header-section">
            <h1 class="page-title">Roles & Permissions</h1>
            <button class="btn btn-primary" onclick="openPopup('add')">Add New Role</button>
        </div>
        <div class="role-list">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Members</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="role-name">Admin</td>
                            <td class="role-details">2 members</td>
                            <td class="actions-cell">
                                <button class="edit-btn" onclick="openPopup('edit', 'Admin')">Edit Role</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="role-name">Manager</td>
                            <td class="role-details">4 members</td>
                            <td class="actions-cell">
                                <button class="edit-btn" onclick="openPopup('edit', 'Manager')">Edit Role</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="role-name">Sales Associate</td>
                            <td class="role-details">12 members</td>
                            <td class="actions-cell">
                                <button class="edit-btn" onclick="openPopup('edit', 'Sales Associate')">Edit Role</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button class="add-role-btn" onclick="openPopup('add')">+ Add New Role</button>
        </div>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <h2 class="modal-title" id="modalTitle">Edit Role</h2>
            <div class="form-group">
                <label class="form-label" for="role-name">Role Name</label>
                <input type="text" id="role-name" name="role-name" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Permissions</label>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="create-edit-products">
                        <label for="create-edit-products">Create & edit products</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="edit-inventory">
                        <label for="edit-inventory">Edit inventory</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="create-edit-orders">
                        <label for="create-edit-orders">Create & edit orders</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="create-edit-customers">
                        <label for="create-edit-customers">Create & edit customers</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="create-edit-discounts">
                        <label for="create-edit-discounts">Create & edit discounts</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="manage-staff-accounts">
                        <label for="manage-staff-accounts">Manage staff accounts</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closePopup('addUserModal')">Cancel</button>
                <button class="btn btn-primary" onclick="saveRole()">Save</button>
            </div>
        </div>
    </div>

    <script>
        function openPopup(action, roleName = '') {
            const modal = document.getElementById('modal');
            modal.classList.add('visible');
            document.getElementById('modalTitle').textContent = action === 'add' ? 'Add New Role' : 'Edit Role';
            document.getElementById('role-name').value = roleName;
            // Reset checkboxes (you might want to load actual permissions for edit)
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        }

        function closePopup() {
            const modal = document.getElementById('modal');
            modal.classList.remove('visible');
        }

        function saveRole() {
            // Get form data
            const roleName = document.getElementById('role-name').value;
            const permissions = Array.from(document.querySelectorAll('input[type="checkbox"]'))
                .filter(cb => cb.checked)
                .map(cb => cb.id);

            // Here you would typically save the role data to your backend
            console.log('Saving role:', {
                name: roleName,
                permissions: permissions
            });

            // Show success message
            alert('Role saved successfully!');
            closePopup();
        }

        // Close modal when clicking outside
        document.getElementById('modal').addEventListener('click', function(event) {
            if (event.target === this) {
                closePopup();
            }
        });
    </script>
</body>
</html>
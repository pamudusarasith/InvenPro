<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>
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
</div>
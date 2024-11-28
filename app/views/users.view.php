<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>
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
                        <th>Actions</th>
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
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="event.stopPropagation(); confirmDeleteUser(<?= $user['id'] ?>)">Delete</button>
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
</div>

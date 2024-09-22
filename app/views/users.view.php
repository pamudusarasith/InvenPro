<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f0f2f5;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .status-active {
            background-color: #86efac;
            color: #166534;
        }
        .status-inactive {
            background-color: #fecaca;
            color: #991b1b;
        }
        .search-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 0.75rem center;
            background-size: 1rem;
            padding-left: 2.5rem;
        }
        .modal {
            background-color: rgba(0, 0, 0, 0.5);
        }
        .clickable-row {
            cursor: pointer;
        }
        .clickable-row:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="p-6">
    <div class="container max-w-4xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Users</h1>
            <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded" onclick="openModal('addUserModal')">
                Add User
            </button>
        </div>
        <div class="mb-4">
            <input type="text" placeholder="Search for a account" class="w-full p-2 border rounded-md search-input">
        </div>
        <div class="mb-4">
            <button class="mr-2 font-semibold text-blue-600">All</button>
            <button class="mr-2 font-semibold text-gray-500">Active</button>
            <button class="font-semibold text-gray-500">Inactive</button>
        </div>
        <table class="w-full bg-white rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 text-left">ID</th>
                    <th class="py-3 px-4 text-left">User</th>
                    <th class="py-3 px-4 text-left">Role</th>
                    <th class="py-3 px-4 text-left">Branch</th>
                    <th class="py-3 px-4 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="border-b clickable-row" onclick="openUserDetails(<?= htmlspecialchars(json_encode($user)) ?>)">
                        <td class="py-3 px-4"><?= htmlspecialchars($user['id']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($user['name']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($user['role']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($user['branch']) ?></td>
                        <td class="py-3 px-4">
                            <span class="<?= $user['status'] == 'active' ? 'status-active' : 'status-inactive' ?> px-2 py-1 rounded-full text-xs font-semibold">
                                <?= ucfirst(htmlspecialchars($user['status'])) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal fixed inset-0 flex items-center justify-center hidden">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6">Add New User</h2>
            <form action="/users/add" method="POST">
                <div class="mb-4">
                    <label for="full_name" class="block text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 mb-2">Phone No</label>
                    <input type="tel" id="phone" name="phone" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="role_id" class="block text-gray-700 mb-2">Role</label>
                    <select id="role_id" name="role_id" class="w-full p-2 border rounded-md" required>
                        <option value="">Select Role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-6">
                    <label for="branch_id" class="block text-gray-700 mb-2">Branch</label>
                    <select id="branch_id" name="branch_id" class="w-full p-2 border rounded-md" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2" onclick="closeModal('addUserModal')">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View/Edit User Modal -->
    <div id="viewEditUserModal" class="modal fixed inset-0 flex items-center justify-center hidden">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6">User Details</h2>
            <form id="editUserForm" action="/users/update" method="POST">
                <input type="hidden" id="edit_user_id" name="id">
                <div class="mb-4">
                    <label for="edit_full_name" class="block text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="edit_full_name" name="full_name" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="edit_email" class="block text-gray-700 mb-2">Email</label>
                    <input type="email" id="edit_email" name="email" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="edit_phone" class="block text-gray-700 mb-2">Phone No</label>
                    <input type="tel" id="edit_phone" name="phone" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="edit_role_id" class="block text-gray-700 mb-2">Role</label>
                    <select id="edit_role_id" name="role_id" class="w-full p-2 border rounded-md" required>
                        <option value="">Select Role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="edit_branch_id" class="block text-gray-700 mb-2">Branch</label>
                    <select id="edit_branch_id" name="branch_id" class="w-full p-2 border rounded-md" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-6">
                    <label for="edit_status" class="block text-gray-700 mb-2">Status</label>
                    <select id="edit_status" name="status" class="w-full p-2 border rounded-md" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2" onclick="closeModal('viewEditUserModal')">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function openUserDetails(user) {
            // Populate the form with user details
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_full_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_phone').value = user.phone;
            document.getElementById('edit_role_id').value = user.role_id;
            document.getElementById('edit_branch_id').value = user.branch_id;
            document.getElementById('edit_status').value = user.status;

            // Open the modal
            openModal('viewEditUserModal');
        }

        // Add event listener for form submission
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would typically send an AJAX request to update the user
            // For this example, we'll just log the form data and close the modal
            console.log('Updating user:', new FormData(this));
            closeModal('viewEditUserModal');
            // In a real application, you'd update the table row with the new data here
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f2f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        h1, h2 {
            font-size: 20px;
            margin: 0;
        }
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        .role-list {
            padding: 20px;
        }
        .role-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .role-item:last-child {
            border-bottom: none;
        }
        .role-name {
            display: flex;
            align-items: center;
        }
        .role-icon {
            margin-right: 10px;
            color: #6c757d;
        }
        .role-details {
            font-size: 14px;
            color: #6c757d;
        }
        .edit-btn {
            background-color: transparent;
            color: #6c757d;
            border: none;
            cursor: pointer;
        }
        .add-role-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: transparent;
            border: 2px dashed #007bff;
            color: #007bff;
            text-align: center;
            border-radius: 4px;
            cursor: pointer;
        }
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            background: none;
            border: none;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .checkbox-group {
            margin-top: 10px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .checkbox-item input[type="checkbox"] {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Roles & Permissions</h1>
            <button class="btn" onclick="openPopup('add')">Add New Role</button>
        </div>
        <div class="role-list">
            <h2>Role List</h2>
            <div class="role-item">
                <div class="role-name">
                    Admin
                </div>
                <div class="role-details">2 members</div>
                <button class="edit-btn" onclick="openPopup('edit', 'Admin')">Edit Role</button>
            </div>
            <div class="role-item">
                <div class="role-name">
                    Manager
                </div>
                <div class="role-details">4 members</div>
                <button class="edit-btn" onclick="openPopup('edit', 'Manager')">Edit Role</button>
            </div>
            <div class="role-item">
                <div class="role-name">
                    Sales Associate
                </div>
                <div class="role-details">12 members</div>
                <button class="edit-btn" onclick="openPopup('edit', 'Sales Associate')">Edit Role</button>
            </div>
            <button class="add-role-btn" onclick="openPopup('add')">+ Add New Role</button>
        </div>
    </div>

    <div id="popupOverlay" class="popup-overlay">
        <div class="popup">
            <button class="close-btn" onclick="closePopup()">&times;</button>
            <h2 id="popupTitle">Edit Role</h2>
            <div class="form-group">
                <label for="role-name">Role Name</label>
                <input type="text" id="role-name" name="role-name">
            </div>
            <div class="form-group">
                <label>Permissions</label>
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
            <button class="btn" onclick="saveRole()">Save</button>
        </div>
    </div>

    <script>
        function openPopup(action, roleName = '') {
            document.getElementById('popupOverlay').style.display = 'block';
            document.getElementById('popupTitle').textContent = action === 'add' ? 'Add New Role' : 'Edit Role';
            document.getElementById('role-name').value = roleName;
            // Reset checkboxes (you might want to load actual permissions for edit)
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        }

        function closePopup() {
            document.getElementById('popupOverlay').style.display = 'none';
        }

        function saveRole() {
            // Here you would typically save the role data
            // For this example, we'll just close the popup
            alert('Role saved!');
            closePopup();
        }
    </script>
</body>
</html>
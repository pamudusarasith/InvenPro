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
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

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

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in forwards';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function confirmDeleteUser(userId) {
    const confirmDelete = confirm('Are you sure you want to delete this user?');
    if (confirmDelete) {
        deleteUser(userId);
    }
}

function deleteUser(userId) {
    fetch('/users/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            removeUserFromTable(userId);
            showNotification('User deleted successfully');
        } else {
            showNotification(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to delete user', 'error');
    });
}

function removeUserFromTable(userId) {
    const rowToRemove = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (rowToRemove) {
        rowToRemove.remove();
        
        originalTableData = originalTableData.filter(item => 
            item.element.getAttribute('data-user-id') !== userId.toString()
        );
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
        <td>
            <button class="btn btn-danger btn-sm" onclick="event.stopPropagation(); confirmDeleteUser(${user.id})">Delete</button>
        </td>
    `;

    return row;
}
function openPopup(action, roleName = '') {
    const modal = document.getElementById('modal');
    modal.classList.add('visible');
    document.getElementById('modalTitle').textContent = action === 'add' ? 'Add New Role' : 'Edit Role';
    document.getElementById('role-name').value = roleName;
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
}

function closePopup() {
    const modal = document.getElementById('modal');
    modal.classList.remove('visible');
}

function saveRole() {
    const roleName = document.getElementById('role-name').value;
    const permissions = Array.from(document.querySelectorAll('input[type="checkbox"]'))
        .filter(cb => cb.checked)
        .map(cb => cb.id);

    console.log('Saving role:', {
        name: roleName,
        permissions: permissions
    });

    alert('Role saved successfully!');
    closePopup();
}

document.getElementById('modal').addEventListener('click', function(event) {
    if (event.target === this) {
        closePopup();
    }
});
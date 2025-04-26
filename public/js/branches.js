function changePage(pageNo) {
    const url = new URL(location.href);
    url.searchParams.set('p', pageNo);
    location.href = url.toString();
}

function changeItemsPerPage(itemsPerPage) {
    const url = new URL(location.href);
    url.searchParams.set('ipp', itemsPerPage);
    url.searchParams.delete('p');
    location.href = url.toString();
}


// Filtering functions
function filterBranches() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const tableRows = document.querySelectorAll('#branches-table tbody tr');

    tableRows.forEach(row => {
        const branchCode = row.children[0].textContent.toLowerCase();
        const branchName = row.children[1].textContent.toLowerCase();
        const email = row.children[4].textContent.toLowerCase();

        if (branchCode.includes(searchInput) || branchName.includes(searchInput) || email.includes(searchInput)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterByStatus() {
    const selectedStatus = document.getElementById('filterStatus').value.toLowerCase();
    const tableRows = document.querySelectorAll('#branches-table tbody tr');

    tableRows.forEach(row => {
        const status = row.children[5].textContent.toLowerCase();

        if (!selectedStatus || status.includes(selectedStatus)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}


function openAddBranchDialog() {
    document.querySelector('#addBranchDialog .modal-header h2').innerHTML = "Add New Branch";
    const dialog = document.getElementById('addBranchDialog');

    const form = document.getElementById('addBranchForm');
    form.action = '/branches/new';
    form.reset();
    dialog.showModal();
}

function closeAddBranchDialog() {
    const dialog = document.getElementById('addBranchDialog');
    dialog.close();
}


function addErrorMessage(field, message) {
    field.classList.add('error');
    let errorMessage = document.createElement('span');
    errorMessage.classList.add('error-message');
    errorMessage.innerText = message;
    field.appendChild(errorMessage);
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
// Edit branch dialog functions
function openEditBranchDialog(e, branchID) {
    document.querySelector('#addBranchDialog .modal-header h2').innerHTML = "Edit Branch";
    const tr = e.target.closest('tr');
    const branchCode = tr.querySelector('td:nth-child(1)').innerText;
    const branchName = tr.querySelector('td:nth-child(2)').innerText;
    const phone = tr.querySelector('td:nth-child(4)').innerText;
    const email = tr.querySelector('td:nth-child(5)').innerText;
    const address = tr.querySelector('td:nth-child(3)').innerText;

    const form = document.getElementById('addBranchForm');
    form.action = `/branches/${branchID}/update`;
    form.querySelector('input[name="branch_code"]').value = branchCode;
    form.querySelector('input[name="branch_name"]').value = branchName;
    form.querySelector('input[name="phone"]').value = phone;
    form.querySelector('input[name="email"]').value = email;
    form.querySelector('textarea[name="address"]').value = address;

    document.getElementById('addBranchDialog').showModal();
}

function closeEditBranchDialog() {
    const dialog = document.getElementById('editBranchModal');
    dialog.close();
}



// Helper function to get branch by ID (simulating data access)
function getBranchById(id) {
    // This would normally be an API call
    
    return branches.find(branch => branch.id === id);
}
// Deactivate and restore branch functions
function deactivateBranch(branchId) {
    event.stopPropagation();
    if (confirm('Are you sure you want to deactivate this branch?')) {
        // In real implementation, this would submit a form or make an API call
        console.log('Deactivating branch ID:', branchId);
        // window.location.href = `/branches/${branchId}/delete`;
    }
}

function restoreBranch(branchId) {
    event.stopPropagation();

    if (confirm('Are you sure you want to restore this branch?')) {
        // In real implementation, this would submit a form or make an API call
        console.log('Restoring branch ID:', branchId);
        // window.location.href = `/branches/${branchId}/restore`;
    }
}
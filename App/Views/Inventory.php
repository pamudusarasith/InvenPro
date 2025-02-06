<?php use App\Services\RBACService; ?>

<div class="body">
    <?php App\Core\View::render("Navbar") ?>
    <?php App\Core\View::render("Sidebar") ?>

    <div class="main">
        <!-- Header Section -->
        <div class="card glass page-header">
            <div class="header-content">
                <h1>Inventory Management</h1>
                <p class="subtitle">Manage your products and stock levels</p>
            </div>
            <?php if (RBACService::hasPermission('add_inventory')): ?>
            <button class="btn btn-primary" onclick="location.href='/inventory/add'">
                <span class="icon">add</span>
                Add Product
            </button>
            <?php endif; ?>
        </div>

        <!-- Controls Section -->
        <div class="card glass controls">
            <div class="search-bar">
                <span class="icon">search</span>
                <input type="text" id="searchInput" placeholder="Search products...">
            </div>
            <div class="filters">
                <select id="filterStatus">
                    <option value="">Status</option>
                    <option value="in-stock">In Stock</option>
                    <option value="low-stock">Low Stock</option>
                    <option value="out-stock">Out of Stock</option>
                </select>
                <select id="filterPrice">
                    <option value="">Price Range</option>
                    <option value="0-1000">$0 - $1000</option>
                    <option value="1001-5000">$1001 - $5000</option>
                    <option value="5001+">$5001+</option>
                </select>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="categories">
            <!-- Electronics Category -->
            <div class="card glass category">
                <button class="category-header" onclick="toggleCategory('electronics')">
                    <h2>Electronics</h2>
                    <span class="icon toggle-icon">expand_more</span>
                </button>
                <div class="category-content" id="electronics">
                    <div class="table-container">
                        <table class="data-table" id="electronics-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Name</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <?php if (RBACService::hasPermission('edit_inventory') || RBACService::hasPermission('delete_inventory')): ?>
                                    <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data -->
                                <tr>
                                    <td>EL001</td>
                                    <td>Laptop</td>
                                    <td>25</td>
                                    <td>$999.99</td>
                                    <td><span class="badge success">In Stock</span></td>
                                    <?php if (RBACService::hasPermission('edit_inventory') || RBACService::hasPermission('delete_inventory')): ?>
                                    <td>
                                        <?php if (RBACService::hasPermission('edit_inventory')): ?>
                                        <button class="action-btn edit" onclick="editProduct('EL001')">
                                            <span class="icon">edit</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (RBACService::hasPermission('delete_inventory')): ?>
                                        <button class="action-btn delete" onclick="deleteProduct('EL001')">
                                            <span class="icon">delete</span>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td>EL002</td>
                                    <td>Smartphone</td>
                                    <td>5</td>
                                    <td>$699.99</td>
                                    <td><span class="badge warning">Low Stock</span></td>
                                    <?php if (RBACService::hasPermission('edit_inventory') || RBACService::hasPermission('delete_inventory')): ?>
                                    <td>
                                        <?php if (RBACService::hasPermission('edit_inventory')): ?>
                                        <button class="action-btn edit" onclick="editProduct('EL002')">
                                            <span class="icon">edit</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (RBACService::hasPermission('delete_inventory')): ?>
                                        <button class="action-btn delete" onclick="deleteProduct('EL002')">
                                            <span class="icon">delete</span>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td>EL003</td>
                                    <td>Tablet</td>
                                    <td>0</td>
                                    <td>$449.99</td>
                                    <td><span class="badge danger">Out of Stock</span></td>
                                    <?php if (RBACService::hasPermission('edit_inventory') || RBACService::hasPermission('delete_inventory')): ?>
                                    <td>
                                        <?php if (RBACService::hasPermission('edit_inventory')): ?>
                                        <button class="action-btn edit" onclick="editProduct('EL003')">
                                            <span class="icon">edit</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (RBACService::hasPermission('delete_inventory')): ?>
                                        <button class="action-btn delete" onclick="deleteProduct('EL003')">
                                            <span class="icon">delete</span>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="pagination-controls">
                            <div class="items-per-page">
                                <span>Show:</span>
                                <select class="items-select" onchange="updateItemsPerPage('electronics', this.value)">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span>entries</span>
                            </div>
                            
                            <div class="pagination">
                                <button class="page-btn" onclick="changePage('electronics', 'prev')">
                                    <span class="icon">chevron_left</span>
                                </button>
                                <div class="page-numbers">
                                    <!-- Page numbers will be dynamically inserted -->
                                </div>
                                <button class="page-btn" onclick="changePage('electronics', 'next')">
                                    <span class="icon">chevron_right</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Office Supplies Category -->
            <div class="card glass category">
                <button class="category-header" onclick="toggleCategory('office')">
                    <h2>Office Supplies</h2>
                    <span class="icon toggle-icon">expand_more</span>
                </button>
                <div class="category-content" id="office">
                    <div class="table-container">
                        <table class="data-table" id="office-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Name</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <?php if (RBACService::hasPermission('edit_inventory') || RBACService::hasPermission('delete_inventory')): ?>
                                    <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>OF001</td>
                                    <td>Printer Paper</td>
                                    <td>500</td>
                                    <td>$4.99</td>
                                    <td><span class="badge success">In Stock</span></td>
                                    <?php if (RBACService::hasPermission('edit_inventory') || RBACService::hasPermission('delete_inventory')): ?>
                                    <td>
                                        <?php if (RBACService::hasPermission('edit_inventory')): ?>
                                        <button class="action-btn edit" onclick="editProduct('OF001')">
                                            <span class="icon">edit</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (RBACService::hasPermission('delete_inventory')): ?>
                                        <button class="action-btn delete" onclick="deleteProduct('OF001')">
                                            <span class="icon">delete</span>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td>OF002</td>
                                    <td>Ink Cartridge</td>
                                    <td>3</td>
                                    <td>$29.99</td>
                                    <td><span class="badge warning">Low Stock</span></td>
                                    <?php if (RBACService::hasPermission('edit_inventory') || RBACService::hasPermission('delete_inventory')): ?>
                                    <td>
                                        <?php if (RBACService::hasPermission('edit_inventory')): ?>
                                        <button class="action-btn edit" onclick="editProduct('OF002')">
                                            <span class="icon">edit</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (RBACService::hasPermission('delete_inventory')): ?>
                                        <button class="action-btn delete" onclick="deleteProduct('OF002')">
                                            <span class="icon">delete</span>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="pagination-controls">
                            <div class="items-per-page">
                                <span>Show:</span>
                                <select class="items-select" onchange="updateItemsPerPage('office', this.value)">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span>entries</span>
                            </div>
                            
                            <div class="pagination">
                                <button class="page-btn" onclick="changePage('office', 'prev')">
                                    <span class="icon">chevron_left</span>
                                </button>
                                <div class="page-numbers">
                                    <!-- Page numbers will be dynamically inserted -->
                                </div>
                                <button class="page-btn" onclick="changePage('office', 'next')">
                                    <span class="icon">chevron_right</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Furniture Category -->
            <div class="card glass category">
                <button class="category-header" onclick="toggleCategory('furniture')">
                    <h2>Furniture</h2>
                    <span class="icon toggle-icon">expand_more</span>
                </button>
                <div class="category-content" id="furniture">
                    <div class="table-container">
                        <table class="data-table" id="furniture-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Name</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <?php if (RBACService::hasPermission('edit_inventory') || RBACService::hasPermission('delete_inventory')): ?>
                                    <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>FN001</td>
                                    <td>Office Chair</td>
                                    <td>12</td>
                                    <td>$199.99</td>
                                    <td><span class="badge success">In Stock</span></td>
                                    <?php if (RBACService::hasPermission('edit_inventory') || RBACService::hasPermission('delete_inventory')): ?>
                                    <td>
                                        <?php if (RBACService::hasPermission('edit_inventory')): ?>
                                        <button class="action-btn edit" onclick="editProduct('FN001')">
                                            <span class="icon">edit</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (RBACService::hasPermission('delete_inventory')): ?>
                                        <button class="action-btn delete" onclick="deleteProduct('FN001')">
                                            <span class="icon">delete</span>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td>FN002</td>
                                    <td>Standing Desk</td>
                                    <td>0</td>
                                    <td>$399.99</td>
                                    <td><span class="badge danger">Out of Stock</span></td>
                                    <?php if (RBACService::hasPermission('edit_inventory') || RBACService::hasPermission('delete_inventory')): ?>
                                    <td>
                                        <?php if (RBACService::hasPermission('edit_inventory')): ?>
                                        <button class="action-btn edit" onclick="editProduct('FN002')">
                                            <span class="icon">edit</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (RBACService::hasPermission('delete_inventory')): ?>
                                        <button class="action-btn delete" onclick="deleteProduct('FN002')">
                                            <span class="icon">delete</span>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="pagination-controls">
                            <div class="items-per-page">
                                <span>Show:</span>
                                <select class="items-select" onchange="updateItemsPerPage('furniture', this.value)">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span>entries</span>
                            </div>
                            
                            <div class="pagination">
                                <button class="page-btn" onclick="changePage('furniture', 'prev')">
                                    <span class="icon">chevron_left</span>
                                </button>
                                <div class="page-numbers">
                                    <!-- Page numbers will be dynamically inserted -->
                                </div>
                                <button class="page-btn" onclick="changePage('furniture', 'next')">
                                    <span class="icon">chevron_right</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCategory(categoryId) {
    const content = document.getElementById(categoryId);
    const header = content.previousElementSibling;
    const icon = header.querySelector('.toggle-icon');
    
    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

document.getElementById('searchInput').addEventListener('input', function(e) {
    // Implement search functionality
    const searchTerm = e.target.value.toLowerCase();
    // Add your search logic here
});

document.querySelectorAll('select').forEach(select => {
    select.addEventListener('change', function() {
        // Implement filter functionality
        // Add your filter logic here
    });
});

function initializePagination() {
    const pageButtons = document.querySelectorAll('.page-number');
    pageButtons.forEach(button => {
        button.addEventListener('click', function() {
            pageButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            // Add your pagination logic here
        });
    });
}

function editProduct(id) {
    if (!<?php echo json_encode(RBACService::hasPermission('edit_inventory')); ?>) {
        alert('You do not have permission to edit products');
        return;
    }
    location.href = `/inventory/edit/${id}`;
}

function deleteProduct(id) {
    if (!<?php echo json_encode(RBACService::hasPermission('delete_inventory')); ?>) {
        alert('You do not have permission to delete products');
        return;
    }
    if (confirm('Are you sure you want to delete this product?')) {
        // Add delete logic here
        location.href = `/inventory/delete/${id}`;
    }
}

// Update pagination handling
function updateItemsPerPage(categoryId, value) {
    const tableState = getTableState(categoryId);
    tableState.itemsPerPage = parseInt(value);
    tableState.currentPage = 1;
    
    updateTableView(categoryId);
    saveTableState(categoryId, tableState);
}

function changePage(categoryId, direction) {
    const tableState = getTableState(categoryId);
    const totalPages = Math.ceil(tableState.totalItems / tableState.itemsPerPage);
    
    if (direction === 'prev' && tableState.currentPage > 1) {
        tableState.currentPage--;
    } else if (direction === 'next' && tableState.currentPage < totalPages) {
        tableState.currentPage++;
    }
    
    updateTableView(categoryId);
    saveTableState(categoryId, tableState);
}

function updateTableView(categoryId) {
    const tableState = getTableState(categoryId);
    const tbody = document.querySelector(`#${categoryId}-table tbody`);
    const rows = Array.from(tbody.children);
    
    // Calculate visible rows
    const start = (tableState.currentPage - 1) * tableState.itemsPerPage;
    const end = start + tableState.itemsPerPage;
    
    // Update visibility
    rows.forEach((row, index) => {
        row.style.display = (index >= start && index < end) ? '' : 'none';
    });
    
    // Update pagination numbers
    updatePaginationNumbers(categoryId, tableState);
}

function updatePaginationNumbers(categoryId, state) {
    const container = document.querySelector(`#${categoryId} .page-numbers`);
    const totalPages = Math.ceil(state.totalItems / state.itemsPerPage);
    
    let html = '';
    for (let i = 1; i <= totalPages; i++) {
        html += `<button class="page-number ${i === state.currentPage ? 'active' : ''}"
                        onclick="goToPage('${categoryId}', ${i})">${i}</button>`;
    }
    
    container.innerHTML = html;
}

function goToPage(categoryId, page) {
    const tableState = getTableState(categoryId);
    tableState.currentPage = page;
    
    updateTableView(categoryId);
    saveTableState(categoryId, tableState);
}

function getTableState(categoryId) {
    const saved = localStorage.getItem(`table_${categoryId}`);
    if (saved) return JSON.parse(saved);
    
    // Default state
    return {
        itemsPerPage: 10,
        currentPage: 1,
        totalItems: document.querySelector(`#${categoryId}-table tbody`).children.length
    };
}

function saveTableState(categoryId, state) {
    localStorage.setItem(`table_${categoryId}`, JSON.stringify(state));
}

// Initialize tables when document loads
document.addEventListener('DOMContentLoaded', function() {
    const categories = ['electronics', 'office', 'furniture'];
    categories.forEach(category => {
        updateTableView(category);
    });
});
</script>

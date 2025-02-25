<?php

use App\Services\RBACService; ?>

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
                <button class="btn btn-primary" onclick="openAddProductModal()">
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
            <?php foreach ($categories as $category): ?>
                <div class="card glass category <?= $category['id'] === (int) $_GET['c'] ? 'open' : '' ?>">
                    <button class="category-header">
                        <h2><?php echo $category['category_name']; ?></h2>
                        <span class="icon toggle-icon">expand_more</span>
                    </button>
                    <div class="category-content">
                        <div class="table-container">
                            <table class="data-table clickable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Stock</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($category['products'] as $product): ?>
                                        <tr data-id="<?= $product['id'] ?>">
                                            <td><?php echo $product['product_code']; ?></td>
                                            <td><?php echo $product['product_name']; ?></td>
                                            <td><?php echo $product['quantity']; ?></td>
                                            <td><?php echo $product['price']; ?></td>
                                            <td>
                                                <span class="badge <?php echo $product['status'] === 'In Stock' ? 'success' : ($product['status'] === 'Low Stock' ? 'warning' : 'danger'); ?>">
                                                    <?php echo $product['status']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="pagination-controls">
                                <div class="items-per-page">
                                    <span>Show:</span>
                                    <select class="items-select" onchange="changeItemsPerPage(this.value)">
                                        <option value="5" <?= $category['itemsPerPage'] == 5 ? "selected" : "" ?>>5</option>
                                        <option value="10" <?= $category['itemsPerPage'] == 10 ? "selected" : "" ?>>10</option>
                                        <option value="20" <?= $category['itemsPerPage'] == 20 ? "selected" : "" ?>>20</option>
                                        <option value="50" <?= $category['itemsPerPage'] == 50 ? "selected" : "" ?>>50</option>
                                        <option value="100" <?= $category['itemsPerPage'] == 100 ? "selected" : "" ?>>100</option>
                                    </select>
                                    <span>entries</span>
                                </div>
                                <?php if ($category['totalPages'] > 1): ?>
                                    <div class="pagination">
                                        <?php if ($category['page'] > 1): ?>
                                            <button class="page-btn" onclick="changePage(<?= min($category['totalPages'], $category['page'] - 1) ?>)">
                                                <span class="icon">chevron_left</span>
                                            </button>
                                        <?php endif; ?>

                                        <div class="page-numbers">
                                            <?php
                                            $maxButtons = 3;
                                            $halfMax = floor($maxButtons / 2);
                                            $start = max(1, min($category['page'] - $halfMax, $category['totalPages'] - $maxButtons + 1));
                                            $end = min($category['totalPages'], $start + $maxButtons - 1);

                                            if ($start > 1) {
                                                echo '<span class="page-number">1</span>';
                                                if ($start > 2) {
                                                    echo '<span class="page-dots">...</span>';
                                                }
                                            }

                                            for ($i = $start; $i <= $end; $i++) {
                                                echo '<span class="page-number ' . ($category['page'] == $i ? 'active' : '') . '"
                    onclick="changePage(' . $i . ')">' . $i . '</span>';
                                            }

                                            if ($end < $category['totalPages']) {
                                                if ($end < $category['totalPages'] - 1) {
                                                    echo '<span class="page-dots">...</span>';
                                                }
                                                echo '<span class="page-number">' . $category['totalPages'] . '</span>';
                                            }
                                            ?>
                                        </div>


                                        <?php if ($category['page'] < $category['totalPages']): ?>
                                            <button class="page-btn" onclick="changePage(<?= $category['page'] + 1 ?>)">
                                                <span class="icon">chevron_right</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php if (RBACService::hasPermission('add_inventory')): ?>
    <dialog id="addProductModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Product</h2>
                <button class="close-btn" onclick="closeAddProductModal()">
                    <span class="icon">close</span>
                </button>
            </div>

            <form id="addProductForm" method="POST" action="/products/new" onsubmit="validateForm(event);">
                <div class="form-grid">
                    <div class="form-field">
                        <label for="product-code">Product Code *</label>
                        <input type="text" id="product-code" name="product_code" required>
                    </div>
                    <div class="form-field">
                        <label for="product-name">Product Name *</label>
                        <input type="text" id="product-name" name="product_name" required>
                    </div>
                    <div class="form-field span-2">
                        <label for="product-description">Description</label>
                        <textarea id="product-description" name="description"></textarea>
                    </div>
                    <div class="form-field">
                        <label for="product-unit">Unit *</label>
                        <input type="text" id="product-unit" name="unit" required>
                    </div>
                    <div class="form-field">
                        <label for="product-image">Image</label>
                        <input type="file" id="product-image" name="image">
                    </div>
                    <div class="form-field span-2">
                        <label for="product-category">Category *</label>
                        <div id="product-category" class="search-bar">
                            <span class="icon">search</span>
                            <input type="text" id="searchInput" placeholder="Search Categories...">
                            <!-- <div class="search-results">
                                <div class="search-result">
                                    <span>Electronics</span>
                                </div>
                                <div class="search-result">
                                    <span>Phones & Tablets</span>
                                </div>
                                <div class="search-result">
                                    <span>Computers</span>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="form-field span-2 chip-container">
                        <div class="chip">
                            Electronics
                            <button class="chip-delete">
                                <span class="icon">close</span>
                            </button>
                        </div>
                        <div class="chip">
                            Phones & Tablets
                            <button class="chip-delete">
                                <span class="icon">close</span>
                            </button>
                        </div>
                        <div class="chip">
                            Computers
                            <button class="chip-delete">
                                <span class="icon">close</span>
                            </button>
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="product-reorder-level">Reorder Level *</label>
                        <input type="number" id="product-reorder-level" name="reorder_level" required>
                    </div>
                    <div class="form-field">
                        <label for="product-reorder-quantity">Reorder Quantity *</label>
                        <input type="number" id="product-reorder-quantity" name="reorder_quantity" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeAddProductModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Product</button>
                </div>
            </form>
        </div>
    </dialog>
<?php endif; ?>

<script>
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

        if (direction === "prev" && tableState.currentPage > 1) {
            tableState.currentPage--;
        } else if (direction === "next" && tableState.currentPage < totalPages) {
            tableState.currentPage++;
        }

        updateTableView(categoryId);
        saveTableState(categoryId, tableState);
    }

    function openAddProductModal() {
        document.getElementById("addProductModal").showModal();
    }

    function closeAddProductModal() {
        document.getElementById("addProductModal").close();
    }

    // Initialize tables when document loads
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".category-header").forEach((header) => {
            header.addEventListener("click", () => {
                header.parentElement.classList.toggle("open");
            });
        });

        document.querySelectorAll(".data-table tbody tr").forEach((row) => {
            row.addEventListener("click", () => {
                window.location.href = `/products/${row.dataset.id}`;
            });
        });
    });
</script>
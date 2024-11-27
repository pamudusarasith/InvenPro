<!-- Products.view.php -->
<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>

    <div class="content">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1>Inventory Management</h1>
                <p class="header-description">Manage your products, stock levels and categories</p>
            </div>
            <div class="header-actions">
                <button id="new-prod-btn" class="btn btn-light" onclick="openProductModal()">
                    <span class="material-symbols-rounded">add_box</span>
                    New Product
                </button>
                <button id="new-batch-btn" class="btn btn-primary" onclick="openBatchModal()">
                    <span class="material-symbols-rounded">inventory_2</span>
                    Add Batch
                </button>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="search-container glass">
            <div class="search-box">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="searchInput" placeholder="Search products by name, category or ID...">
            </div>

            <div class="filters">
                <div class="filter-group">
                    <label>Stock Status</label>
                    <div class="filter-chips">
                        <button class="filter-chip" data-filter="in-stock">
                            <span class="material-symbols-rounded">check_circle</span>
                            In Stock
                            <span class="chip-count">0</span>
                        </button>
                        <button class="filter-chip" data-filter="low-stock">
                            <span class="material-symbols-rounded">warning</span>
                            Low Stock
                            <span class="chip-count">0</span>
                        </button>
                        <button class="filter-chip" data-filter="out-stock">
                            <span class="material-symbols-rounded">error</span>
                            Out of Stock
                            <span class="chip-count">0</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="categories-grid">
            <?php if (isset($categories)):
                foreach ($categories as $category):
                    $categoryProducts = $products[$category] ?? [];
                    $totalProducts = count($categoryProducts);
                    $lowStock = count(array_filter($categoryProducts, fn($p) => $p['quantity'] <= 100 && $p['quantity'] > 0));
                    $outOfStock = count(array_filter($categoryProducts, fn($p) => $p['quantity'] === 0));
            ?>
                    <div class="category-card glass">
                        <div class="category-header">
                            <div class="category-info">
                                <span class="material-symbols-rounded">category</span>
                                <div>
                                    <h3><?= htmlspecialchars($category) ?></h3>
                                    <p><?= $totalProducts ?> Products</p>
                                </div>
                            </div>
                            <div class="category-stats">
                                <?php if ($lowStock > 0): ?>
                                    <span class="stat warning"><?= $lowStock ?> Low</span>
                                <?php endif; ?>
                                <?php if ($outOfStock > 0): ?>
                                    <span class="stat danger"><?= $outOfStock ?> Out</span>
                                <?php endif; ?>
                            </div>
                            <button class="expand-btn">
                                <span class="material-symbols-rounded">expand_more</span>
                            </button>
                        </div>

                        <div class="category-content">
                            <div class="table-container">
                                <table class="product-table">
                                    <thead>
                                        <tr>
                                            <th>Product Details</th>
                                            <th>Price</th>
                                            <th>Stock Level</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categoryProducts as $product): ?>
                                            <tr data-product-id="<?= $product['id'] ?>" class="product-row">
                                                <td class="product-cell">
                                                    <div class="product-info">
                                                        <div class="product-image">
                                                            <img src="<?= $product['image'] ?? '/assets/default-product.png' ?>"
                                                                alt="<?= htmlspecialchars($product['name']) ?>">
                                                        </div>
                                                        <div class="product-details">
                                                            <p class="product-name"><?= htmlspecialchars($product['name']) ?></p>
                                                            <span class="product-id">ID: <?= htmlspecialchars($product['id']) ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="price-cell">
                                                    <span class="price">
                                                        <?= is_numeric($product['price']) ? "Rs. " . number_format($product['price'], 2) : $product['price'] ?>
                                                    </span>
                                                </td>
                                                <td class="stock-cell">
                                                    <div class="stock-indicator">
                                                        <div class="stock-bar">
                                                            <div class="stock-level" style="width: <?= min(100, ($product['quantity'] / 100) * 100) ?>%"></div>
                                                        </div>
                                                        <span class="stock-count"><?= number_format($product['quantity']) ?> units</span>
                                                    </div>
                                                </td>
                                                <td class="status-cell">
                                                    <?php
                                                    $status = $product['quantity'] === 0 ? 'out' : ($product['quantity'] <= 100 ? 'low' : 'in');
                                                    $statusText = $product['quantity'] === 0 ? 'Out of Stock' : ($product['quantity'] <= 100 ? 'Low Stock' : 'In Stock');
                                                    ?>
                                                    <span class="status-badge <?= $status ?>"><?= $statusText ?></span>
                                                </td>
                                                <td class="actions-cell">
                                                    <div class="action-buttons">
                                                        <button class="action-btn" title="Edit Product" data-action="edit">
                                                            <span class="material-symbols-rounded">edit</span>
                                                        </button>
                                                        <button class="action-btn" title="Add Stock" data-action="stock">
                                                            <span class="material-symbols-rounded">add_circle</span>
                                                        </button>
                                                        <button class="action-btn" title="View History" data-action="history">
                                                            <span class="material-symbols-rounded">history</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="table-footer">
                                <div class="items-per-page">
                                    <span>Show:</span>
                                    <select class="items-select">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                    </select>
                                </div>
                                <div class="pagination">
                                    <button class="page-btn prev" disabled>
                                        <span class="material-symbols-rounded">chevron_left</span>
                                    </button>
                                    <div class="page-numbers"></div>
                                    <button class="page-btn next">
                                        <span class="material-symbols-rounded">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>
    </div>

    <?php
    App\View::render('components/ProductForm');
    App\View::render('components/BatchForm');
    ?>
</div>

<style>
    /* Products page styling */
    .content {
        padding: 2rem;
        background: var(--surface-light);
    }

    .glass {
        background: var(--glass-white);
        backdrop-filter: blur(10px);
        border: 1px solid var(--border-light);
        border-radius: 16px;
        box-shadow: var(--shadow-md);
    }

    /* Header Section */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        background: var(--surface-white);
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid var(--border-light);
    }

    .header-content h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .header-description {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    /* Search Container */
    .search-container {
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .search-box {
        display: flex;
        align-items: center;
        background: var(--surface-white);
        border-radius: 12px;
        padding: 0 1rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-light);
    }

    .search-box input {
        border: none;
        outline: none;
        width: 100%;
        margin-left: 0.75rem;
        font-size: 0.875rem;
        color: var(--text-primary);
    }

    .search-box input:focus {
        border: none;
        outline: none;
    }

    .filter-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .filter-chips {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .filter-chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        background: var(--surface-white);
        border: 1px solid var(--border-medium);
        color: var(--text-secondary);
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-chip:hover {
        background: var(--primary-50);
        color: var(--primary-600);
        border-color: var(--primary-200);
    }

    .filter-chip.active {
        background: var(--primary-600);
        color: var(--surface-white);
        border-color: var(--primary-600);
    }

    .chip-count {
        background: var(--overlay-dark);
        padding: 0.125rem 0.375rem;
        border-radius: 9999px;
        font-size: 0.75rem;
    }

    /* Category Cards */
    .categories-grid {
        display: grid;
        gap: 1.5rem;
    }

    .category-card {
        overflow: hidden;
        background: var(--surface-white);
    }

    .category-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem;
        border-bottom: 1px solid var(--border-light);
    }

    .category-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .category-info h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .category-info p {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    /* Status Badges */
    .stat {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .stat.warning {
        background: var(--warning-50);
        color: var(--warning-600);
    }

    .stat.danger {
        background: var(--danger-50);
        color: var(--danger-600);
    }

    /* Table Styles */
    .product-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .product-table th {
        background: var(--surface-light);
        padding: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-light);
    }

    .product-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-light);
    }

    .product-row:hover {
        background: var(--surface-light);
    }

    /* Stock Level Styling */
    .stock-bar {
        height: 4px;
        background: var(--surface-medium);
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .stock-level {
        height: 100%;
        background: var(--primary-500);
        border-radius: 2px;
        transition: width 0.3s ease;
    }

    /* Status Badge Colors */
    .status-badge.in {
        background: var(--success-50);
        color: var(--success-600);
    }

    .status-badge.low {
        background: var(--warning-50);
        color: var(--warning-600);
    }

    .status-badge.out {
        background: var(--danger-50);
        color: var(--danger-600);
    }

    /* Action Buttons */
    .action-btn {
        padding: 0.5rem;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: var(--text-tertiary);
        cursor: pointer;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: var(--primary-50);
        color: var(--primary-600);
    }

    /* Table Footer */
    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-top: 1px solid var(--border-light);
        background: var(--surface-light);
    }

    .items-select {
        padding: 0.375rem 0.75rem;
        border: 1px solid var(--border-medium);
        border-radius: 6px;
        background: var(--surface-white);
        color: var(--text-primary);
    }

    .page-btn {
        padding: 0.5rem;
        border: 1px solid var(--border-medium);
        border-radius: 6px;
        background: var(--surface-white);
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
    }

    .page-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .page-btn:hover:not(:disabled) {
        background: var(--primary-50);
        color: var(--primary-600);
        border-color: var(--primary-200);
    }

    .header-actions {
        display: flex;
        gap: 1rem;
    }

    .expand-btn {
        background: none;
        border: none;
        outline: none;
        cursor: pointer;
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .header-actions {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
    }

    @media (max-width: 768px) {
        .content {
            padding: 1rem;
        }

        .header-actions {
            grid-template-columns: 1fr;
        }

        .stock-indicator {
            width: 100px;
        }

        .table-footer {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>

<script>
    // Products page functionality
    document.addEventListener('DOMContentLoaded', () => {
        // Category Card Management
        class CategoryManager {
            constructor(card) {
                this.card = card;
                this.header = card.querySelector('.category-header');
                this.content = card.querySelector('.category-content');
                this.expandBtn = card.querySelector('.expand-btn span');
                this.isExpanded = false;

                this.init();
            }

            init() {
                this.content.style.display = 'none';
                this.header.addEventListener('click', () => this.toggle());
            }

            toggle() {
                this.isExpanded = !this.isExpanded;

                if (this.isExpanded) {
                    this.expand();
                } else {
                    this.collapse();
                }
            }

            expand() {
                this.content.style.display = 'block';
                this.expandBtn.style.transform = 'rotate(180deg)';
                this.animateStockBars();
            }

            collapse() {
                this.content.style.display = 'none';
                this.expandBtn.style.transform = 'rotate(0deg)';
            }

            animateStockBars() {
                const stockBars = this.content.querySelectorAll('.stock-level');
                stockBars.forEach(bar => {
                    const finalWidth = bar.style.width;
                    bar.style.width = '0';
                    setTimeout(() => bar.style.width = finalWidth, 50);
                });
            }
        }

        // Table Management
        class TableManager {
            constructor(table) {
                this.table = table;
                this.tbody = table.querySelector('tbody');
                this.rows = Array.from(this.tbody.querySelectorAll('tr'));
                this.itemsPerPage = 10;
                this.currentPage = 1;
                this.tableFooter = this.table.closest('.category-content').querySelector('.table-footer');

                this.init();
            }

            init() {
                this.setupPagination();
                this.setupItemsPerPage();
                this.updateTable();
            }

            setupPagination() {
                const pagination = this.tableFooter.querySelector('.pagination');
                const prevBtn = pagination.querySelector('.prev');
                const nextBtn = pagination.querySelector('.next');
                const pageNumbers = pagination.querySelector('.page-numbers');

                prevBtn.addEventListener('click', () => this.changePage(this.currentPage - 1));
                nextBtn.addEventListener('click', () => this.changePage(this.currentPage + 1));

                this.updatePaginationButtons();
            }

            setupItemsPerPage() {
                const select = this.tableFooter.querySelector('.items-select');
                select.addEventListener('change', (e) => {
                    this.itemsPerPage = parseInt(e.target.value);
                    this.currentPage = 1;
                    this.updateTable();
                });
            }

            updateTable() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;

                this.rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });

                this.updatePaginationButtons();
            }

            updatePaginationButtons() {
                const totalPages = Math.ceil(this.rows.length / this.itemsPerPage);
                const pagination = this.tableFooter.querySelector('.pagination');
                const prevBtn = pagination.querySelector('.prev');
                const nextBtn = pagination.querySelector('.next');
                const pageNumbers = pagination.querySelector('.page-numbers');

                prevBtn.disabled = this.currentPage === 1;
                nextBtn.disabled = this.currentPage === totalPages;

                this.renderPageNumbers(pageNumbers, totalPages);
            }

            renderPageNumbers(container, totalPages) {
                container.innerHTML = '';
                for (let i = 1; i <= totalPages; i++) {
                    const pageBtn = document.createElement('button');
                    pageBtn.classList.add('page-number');
                    if (i === this.currentPage) pageBtn.classList.add('active');
                    pageBtn.textContent = i;
                    pageBtn.addEventListener('click', () => this.changePage(i));
                    container.appendChild(pageBtn);
                }
            }

            changePage(page) {
                this.currentPage = page;
                this.updateTable();
            }
        }

        // Search and Filter Functionality
        class ProductFilter {
            constructor() {
                this.searchTerm = '';
                this.activeFilters = new Set();

                this.init();
            }

            init() {
                this.setupSearch();
                this.setupFilters();
                this.updateFilterCounts();
            }

            setupSearch() {
                let debounceTimer;
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        this.searchTerm = e.target.value.toLowerCase();
                        this.applyFilters();
                    }, 300);
                });
            }

            setupFilters() {
                filterChips.forEach(chip => {
                    chip.addEventListener('click', () => {
                        const filter = chip.dataset.filter;
                        if (chip.classList.toggle('active')) {
                            this.activeFilters.add(filter);
                        } else {
                            this.activeFilters.delete(filter);
                        }
                        this.applyFilters();
                    });
                });
            }

            updateFilterCounts() {
                filterChips.forEach(chip => {
                    const filter = chip.dataset.filter;
                    const count = this.getFilterCount(filter);
                    chip.querySelector('.chip-count').textContent = count;
                });
            }

            getFilterCount(filter) {
                return Array.from(document.querySelectorAll('.product-row')).filter(row => {
                    const status = this.getProductStatus(row);
                    return status === filter;
                }).length;
            }

            getProductStatus(row) {
                const quantity = parseInt(row.querySelector('.stock-count').textContent);
                return quantity === 0 ? 'out-stock' :
                    quantity <= 100 ? 'low-stock' : 'in-stock';
            }

            applyFilters() {
                const rows = document.querySelectorAll('.product-row');

                rows.forEach(row => {
                    const name = row.querySelector('.product-name').textContent.toLowerCase();
                    const status = this.getProductStatus(row);

                    const matchesSearch = !this.searchTerm || name.includes(this.searchTerm);
                    const matchesFilter = this.activeFilters.size === 0 ||
                        this.activeFilters.has(status);

                    row.style.display = matchesSearch && matchesFilter ? '' : 'none';
                });

                this.updateVisibleCategories();
            }

            updateVisibleCategories() {
                categoryCards.forEach(card => {
                    const visibleProducts = card.querySelectorAll('.product-row[style=""]').length;
                    card.style.display = visibleProducts > 0 ? '' : 'none';
                });
            }
        }

        const searchInput = document.getElementById('searchInput');
        const filterChips = document.querySelectorAll('.filter-chip');
        const categoryCards = document.querySelectorAll('.category-card');

        // Initialize TableManager for each category
        const tableManagers = new Map();
        categoryCards.forEach(card => {
            const table = card.querySelector('.product-table');
            if (table) {
                tableManagers.set(card, new TableManager(table));
            }
        });

        // Initialize components
        categoryCards.forEach(card => new CategoryManager(card));
        const productFilter = new ProductFilter();

        // Modal handlers
        const modals = {
            'new-prod-btn': 'prod-form-modal',
            'new-batch-btn': 'batch-form-modal'
        };

        Object.entries(modals).forEach(([btnId, modalId]) => {
            const button = document.getElementById(btnId);
            const modal = document.getElementById(modalId);

            button?.addEventListener('click', () => modal?.showModal());
        });

        // Action button handlers
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const action = btn.dataset.action;
                const row = btn.closest('.product-row');
                const productId = row.dataset.productId;

                handleAction(action, productId);
            });
        });

        function handleAction(action, productId) {
            const actions = {
                edit: openProductEditModal,
                stock: openAddStockModal,
                history: openHistoryModal
            };

            actions[action]?.(productId);
        }

        function openProductEditModal(productId) {
            console.log('Edit product:', productId);
        }

        function openAddStockModal(productId) {
            console.log('Add stock for:', productId);
        }

        function openHistoryModal(productId) {
            console.log('View history:', productId);
        }
    });
</script>
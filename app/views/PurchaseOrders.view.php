<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>
    <div class="content">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1>Orders Management</h1>
                <p class="header-description">View and manage all customer orders</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary">
                    <span class="material-symbols-rounded">add_shopping_cart</span>
                    New Order
                </button>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="search-container glass">
            <div class="search-box">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="searchInput" placeholder="Search orders by ID, customer name or status...">
            </div>
            <div class="filter-chips">
                <button class="filter-chip active" data-filter="all">
                    <span class="material-symbols-rounded">all_inclusive</span>
                    All Orders
                </button>
                <button class="filter-chip" data-filter="pending">
                    <span class="material-symbols-rounded">pending</span>
                    Pending
                </button>
                <button class="filter-chip" data-filter="processing">
                    <span class="material-symbols-rounded">sync</span>
                    Processing
                </button>
                <button class="filter-chip" data-filter="delivered">
                    <span class="material-symbols-rounded">check_circle</span>
                    Delivered
                </button>
            </div>
        </div>

        <!-- Orders Grid -->
        <div class="orders-grid">
            <!-- Order Card 1 -->
            <div class="order-card glass">
                <div class="order-header">
                    <div class="order-id">#ORD-2024-001</div>
                    <div class="order-status pending">Pending</div>
                </div>
                <div class="order-body">
                    <div class="order-info">
                        <div class="customer-details">
                            <span class="material-symbols-rounded">person</span>
                            <div>
                                <label>Customer</label>
                                <p>John Smith</p>
                            </div>
                        </div>
                        <div class="order-date">
                            <span class="material-symbols-rounded">calendar_today</span>
                            <div>
                                <label>Order Date</label>
                                <p>March 15, 2024</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-details">
                        <div class="amount-details">
                            <div class="detail-row">
                                <label>Items</label>
                                <span>3 items</span>
                            </div>
                            <div class="detail-row">
                                <label>Total Amount</label>
                                <span class="amount">$245.99</span>
                            </div>
                        </div>
                        <div class="order-actions">
                            <button class="action-btn" title="View Details">
                                <span class="material-symbols-rounded">visibility</span>
                            </button>
                            <button class="action-btn" title="Print Invoice">
                                <span class="material-symbols-rounded">receipt_long</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Card 2 -->
            <div class="order-card glass">
                <div class="order-header">
                    <div class="order-id">#ORD-2024-002</div>
                    <div class="order-status processing">Processing</div>
                </div>
                <div class="order-body">
                    <div class="order-info">
                        <div class="customer-details">
                            <span class="material-symbols-rounded">person</span>
                            <div>
                                <label>Customer</label>
                                <p>Sarah Johnson</p>
                            </div>
                        </div>
                        <div class="order-date">
                            <span class="material-symbols-rounded">calendar_today</span>
                            <div>
                                <label>Order Date</label>
                                <p>March 14, 2024</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-details">
                        <div class="amount-details">
                            <div class="detail-row">
                                <label>Items</label>
                                <span>2 items</span>
                            </div>
                            <div class="detail-row">
                                <label>Total Amount</label>
                                <span class="amount">$189.50</span>
                            </div>
                        </div>
                        <div class="order-actions">
                            <button class="action-btn" title="View Details">
                                <span class="material-symbols-rounded">visibility</span>
                            </button>
                            <button class="action-btn" title="Print Invoice">
                                <span class="material-symbols-rounded">receipt_long</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Card 3 -->
            <div class="order-card glass">
                <div class="order-header">
                    <div class="order-id">#ORD-2024-003</div>
                    <div class="order-status delivered">Delivered</div>
                </div>
                <div class="order-body">
                    <div class="order-info">
                        <div class="customer-details">
                            <span class="material-symbols-rounded">person</span>
                            <div>
                                <label>Customer</label>
                                <p>Michael Brown</p>
                            </div>
                        </div>
                        <div class="order-date">
                            <span class="material-symbols-rounded">calendar_today</span>
                            <div>
                                <label>Order Date</label>
                                <p>March 13, 2024</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-details">
                        <div class="amount-details">
                            <div class="detail-row">
                                <label>Items</label>
                                <span>5 items</span>
                            </div>
                            <div class="detail-row">
                                <label>Total Amount</label>
                                <span class="amount">$456.75</span>
                            </div>
                        </div>
                        <div class="order-actions">
                            <button class="action-btn" title="View Details">
                                <span class="material-symbols-rounded">visibility</span>
                            </button>
                            <button class="action-btn" title="Print Invoice">
                                <span class="material-symbols-rounded">receipt_long</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        class FilterManager {
            constructor() {
                this.searchInput = document.getElementById('searchInput');
                this.filterChips = document.querySelectorAll('.filter-chip');
                this.orders = document.querySelectorAll('.order-card');
            }

            init() {
                this.searchInput.addEventListener('input', () => this.filterOrders());
                this.filterChips.forEach(chip => {
                    chip.addEventListener('click', () => this.toggleFilter(chip));
                });
            }

            toggleFilter(chip) {
                this.filterChips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                this.filterOrders();
            }

            filterOrders() {
                const searchTerm = this.searchInput.value.toLowerCase();
                const activeFilter = document.querySelector('.filter-chip.active').dataset.filter;

                this.orders.forEach(order => {
                    const orderText = order.textContent.toLowerCase();
                    const orderStatus = order.querySelector('.order-status').textContent.toLowerCase();
                    const matchesSearch = orderText.includes(searchTerm);
                    const matchesFilter = activeFilter === 'all' || orderStatus === activeFilter;

                    order.style.display = matchesSearch && matchesFilter ? 'block' : 'none';
                });
            }
        }

        class OrderActions {
            constructor() {
                this.actionButtons = document.querySelectorAll('.action-btn');
            }

            init() {
                this.actionButtons.forEach(btn => {
                    btn.addEventListener('click', (e) => this.handleAction(e));
                });
            }

            handleAction(e) {
                const action = e.currentTarget.getAttribute('title');
                const orderCard = e.currentTarget.closest('.order-card');
                const orderId = orderCard.querySelector('.order-id').textContent;

                switch (action) {
                    case 'View Details':
                        this.viewOrderDetails(orderId);
                        break;
                    case 'Print Invoice':
                        this.printInvoice(orderId);
                        break;
                }
            }

            viewOrderDetails(orderId) {
                // Modal implementation would go here
                console.log(`Viewing details for order ${orderId}`);
            }

            printInvoice(orderId) {
                // Print logic would go here
                console.log(`Printing invoice for order ${orderId}`);
            }
        }

        // Initialize components
        const filterManager = new FilterManager();
        const orderActions = new OrderActions();

        filterManager.init();
        orderActions.init();
    });
</script>

<style>
    .content {
        padding: 2rem;
        background: var(--surface-light);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding: 1.5rem;
        border-radius: 16px;
        background: var(--surface-white);
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

    .search-container {
        padding: 1.5rem;
        margin-bottom: 2rem;
        background: var(--glass-white);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        box-shadow: var(--shadow-md);
    }

    .search-box {
        display: flex;
        align-items: center;
        background: var(--surface-white);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
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

    .orders-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .order-card {
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-light);
    }

    .order-id {
        font-weight: 600;
        color: var(--text-primary);
    }

    .order-status {
        padding: 0.25rem 0.75rem;
        border-radius: 16px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .order-status.pending {
        background: var(--warning-100);
        color: var(--warning-600);
    }

    .order-status.processing {
        background: var(--primary-100);
        color: var(--primary-600);
    }

    .order-status.delivered {
        background: var(--success-100);
        color: var(--success-600);
    }

    .order-status.cancelled {
        background: var(--danger-100);
        color: var(--danger-600);
    }

    .order-body {
        padding: 1.5rem;
    }

    .order-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .customer-details,
    .order-date {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .customer-details span,
    .order-date span {
        color: var(--text-tertiary);
    }

    .customer-details label,
    .order-date label {
        font-size: 0.75rem;
        color: var(--text-tertiary);
        margin-bottom: 0.25rem;
    }

    .customer-details p,
    .order-date p {
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    .order-details {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        padding-top: 1rem;
        border-top: 1px solid var(--border-light);
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .detail-row label {
        color: var(--text-tertiary);
        font-size: 0.75rem;
    }

    .detail-row span {
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    .amount {
        font-weight: 600;
        color: var(--primary-600);
    }

    .order-actions {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        padding: 0.5rem;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: var(--text-tertiary);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        background: var(--secondary-100);
        color: var(--text-primary);
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
        }

        .orders-grid {
            grid-template-columns: 1fr;
        }

        .order-info {
            grid-template-columns: 1fr;
        }
    }
</style>
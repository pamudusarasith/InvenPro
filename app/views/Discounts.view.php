<!-- Discounts.view.php -->
<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>

    <div class="content">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1>Discounts & Coupons</h1>
                <p class="header-description">Manage promotional offers, discounts and coupon codes</p>
            </div>
            <div class="header-actions">
                <button id="new-coupon-btn" class="btn btn-secondary" onclick="openCouponModal()">
                    <span class="material-symbols-rounded">local_activity</span>
                    New Coupon
                </button>
                <button id="new-discount-btn" class="btn btn-primary" onclick="openDiscountModal()">
                    <span class="material-symbols-rounded">percent</span>
                    New Discount
                </button>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="search-container glass">
            <div class="search-box">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="searchInput" placeholder="Search by name, code or description...">
            </div>

            <div class="filters">
                <div class="filter-group">
                    <label>Status</label>
                    <div class="filter-chips">
                        <button class="filter-chip active" data-filter="active">
                            <span class="material-symbols-rounded">check_circle</span>
                            Active
                            <span class="chip-count">12</span>
                        </button>
                        <button class="filter-chip" data-filter="scheduled">
                            <span class="material-symbols-rounded">schedule</span>
                            Scheduled
                            <span class="chip-count">3</span>
                        </button>
                        <button class="filter-chip" data-filter="expired">
                            <span class="material-symbols-rounded">block</span>
                            Expired
                            <span class="chip-count">5</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" data-tab="discounts">
                <span class="material-symbols-rounded">percent</span>
                Discounts
            </button>
            <button class="tab-btn" data-tab="coupons">
                <span class="material-symbols-rounded">local_activity</span>
                Coupons
            </button>
        </div>

        <!-- Discounts Grid -->
        <div id="discounts" class="tab-content active">
            <div class="discounts-grid">
                <!-- Sample Discount Cards -->
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <div class="discount-card glass">
                        <div class="discount-header">
                            <div class="discount-type <?= ['product', 'category', 'bill'][rand(0, 2)] ?>">
                                <span class="material-symbols-rounded">
                                    <?= ['inventory_2', 'category', 'receipt'][rand(0, 2)] ?>
                                </span>
                            </div>
                            <div class="discount-info">
                                <h3>Summer Sale <?= $i ?></h3>
                                <p>20% off on all summer collection</p>
                            </div>
                            <div class="discount-actions">
                                <button class="action-btn" title="Edit">
                                    <span class="material-symbols-rounded">edit</span>
                                </button>
                                <button class="action-btn" title="Deactivate">
                                    <span class="material-symbols-rounded">block</span>
                                </button>
                            </div>
                        </div>

                        <div class="discount-body">
                            <div class="discount-details">
                                <div class="detail">
                                    <span class="material-symbols-rounded">calendar_today</span>
                                    <div>
                                        <label>Valid Period</label>
                                        <p>Jun 1 - Aug 31, 2024</p>
                                    </div>
                                </div>
                                <div class="detail">
                                    <span class="material-symbols-rounded">shopping_cart</span>
                                    <div>
                                        <label>Min. Purchase</label>
                                        <p>Rs. 5,000</p>
                                    </div>
                                </div>
                            </div>

                            <div class="usage-stats">
                                <div class="stat-row">
                                    <label>Usage</label>
                                    <span>45/100</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 45%"></div>
                                </div>
                            </div>

                            <div class="discount-tags">
                                <span class="tag">Combinable</span>
                                <span class="tag">Max Rs. 1,000</span>
                                <span class="tag loyalty">Loyalty Only</span>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Coupons Grid -->
        <div id="coupons" class="tab-content">
            <div class="coupons-grid">
                <!-- Sample Coupon Cards -->
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <div class="coupon-card glass">
                        <div class="coupon-header">
                            <div class="coupon-code">
                                <h3>SUMMER<?= $i ?>2024</h3>
                                <button class="copy-btn" title="Copy code">
                                    <span class="material-symbols-rounded">content_copy</span>
                                </button>
                            </div>
                            <div class="coupon-actions">
                                <button class="action-btn" title="Edit">
                                    <span class="material-symbols-rounded">edit</span>
                                </button>
                                <button class="action-btn" title="Deactivate">
                                    <span class="material-symbols-rounded">block</span>
                                </button>
                            </div>
                        </div>

                        <div class="coupon-body">
                            <p class="coupon-description">Get 15% off on your first purchase</p>

                            <div class="coupon-details">
                                <div class="detail">
                                    <span class="material-symbols-rounded">calendar_today</span>
                                    <div>
                                        <label>Valid Until</label>
                                        <p>Aug 31, 2024</p>
                                    </div>
                                </div>
                                <div class="detail">
                                    <span class="material-symbols-rounded">person</span>
                                    <div>
                                        <label>Created By</label>
                                        <p>John Doe</p>
                                    </div>
                                </div>
                            </div>

                            <div class="usage-stats">
                                <div class="stat-row">
                                    <label>Usage</label>
                                    <span>24/50</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 48%"></div>
                                </div>
                            </div>

                            <div class="coupon-tags">
                                <span class="tag">First Purchase Only</span>
                                <span class="tag">Single Use</span>
                                <span class="tag">Min. Rs. 2,000</span>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <?php
    App\View::render('components/DiscountForm');
    ?>
</div>

<style>
    /* Main Layout */
    .content {
        padding: 2rem;
        background: var(--surface-light);
    }

    /* Header Section */
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

    .header-actions {
        display: flex;
        gap: 1rem;
    }

    /* Search & Filters */
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

    /* Tabs */
    .tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .tab-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        background: var(--surface-white);
        color: var(--text-secondary);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .tab-btn.active {
        background: var(--primary-600);
        color: var(--surface-white);
    }

    /* Grids */
    .discounts-grid,
    .coupons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    /* Card Components */
    .discount-card,
    .coupon-card {
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .discount-card:hover,
    .coupon-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .discount-header,
    .coupon-header {
        display: flex;
        align-items: flex-start;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-light);
    }

    .discount-type {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        margin-right: 1rem;
    }

    .discount-type.product {
        background: var(--primary-100);
        color: var(--primary-600);
    }

    .discount-type.category {
        background: var(--accent-100);
        color: var(--accent-600);
    }

    .discount-type.bill {
        background: var(--success-100);
        color: var(--success-600);
    }

    .discount-info,
    .coupon-code {
        flex: 1;
    }

    .discount-info h3,
    .coupon-code h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .discount-body,
    .coupon-body {
        padding: 1.5rem;
    }

    /* Details Section */
    .discount-details,
    .coupon-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin: 1rem 0;
    }

    .detail {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .detail span {
        color: var(--text-tertiary);
    }

    .detail label {
        font-size: 0.75rem;
        color: var(--text-tertiary);
        margin-bottom: 0.25rem;
    }

    .detail p {
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    /* Usage Stats */
    .usage-stats {
        margin: 1rem 0;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .stat-row label {
        font-size: 0.75rem;
        color: var(--text-tertiary);
    }

    .stat-row span {
        font-size: 0.875rem;
        color: var(--text-primary);
        font-weight: 500;
    }

    .progress-bar {
        height: 4px;
        background: var(--secondary-100);
        border-radius: 2px;
        overflow: hidden;
    }

    .progress {
        height: 100%;
        background: var(--primary-500);
        border-radius: 2px;
        transition: width 0.3s ease;
    }

    /* Tags */
    .discount-tags,
    .coupon-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .tag {
        padding: 0.25rem 0.75rem;
        border-radius: 16px;
        font-size: 0.75rem;
        background: var(--secondary-100);
        color: var(--text-secondary);
    }

    .tag.loyalty {
        background: var(--accent-100);
        color: var(--accent-600);
    }

    /* Buttons */
    .btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: var(--primary-600);
        color: var(--surface-white);
    }

    .btn-secondary {
        background: var(--surface-white);
        color: var(--text-secondary);
        border: 1px solid var(--border-light);
    }

    .btn-primary:hover {
        background: var(--primary-700);
    }

    .btn-secondary:hover {
        background: var(--secondary-50);
    }

    /* Action Buttons */
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
        }

        .header-actions {
            width: 100%;
        }

        .btn {
            flex: 1;
            justify-content: center;
        }

        .discounts-grid,
        .coupons-grid {
            grid-template-columns: 1fr;
        }

        .discount-details,
        .coupon-details {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Tab Management
        class TabManager {
            constructor() {
                this.tabs = document.querySelectorAll('.tab-btn');
                this.contents = document.querySelectorAll('.tab-content');
                this.init();
            }

            init() {
                this.tabs.forEach(tab => {
                    tab.addEventListener('click', () => this.switchTab(tab));
                });
            }

            switchTab(selectedTab) {
                this.tabs.forEach(tab => tab.classList.remove('active'));
                this.contents.forEach(content => content.classList.remove('active'));

                selectedTab.classList.add('active');
                document.getElementById(selectedTab.dataset.tab).classList.add('active');
            }
        }

        // Search & Filter Management
        class FilterManager {
            constructor() {
                this.searchInput = document.getElementById('searchInput');
                this.filterChips = document.querySelectorAll('.filter-chip');
                this.cards = document.querySelectorAll('.discount-card, .coupon-card');

                this.init();
            }

            init() {
                this.searchInput.addEventListener('input', () => this.filterCards());
                this.filterChips.forEach(chip => {
                    chip.addEventListener('click', () => {
                        chip.classList.toggle('active');
                        this.filterCards();
                    });
                });
            }

            filterCards() {
                const searchTerm = this.searchInput.value.toLowerCase();
                const activeFilters = Array.from(this.filterChips)
                    .filter(chip => chip.classList.contains('active'))
                    .map(chip => chip.dataset.filter);

                this.cards.forEach(card => {
                    const title = card.querySelector('h3').textContent.toLowerCase();
                    const description = card.querySelector('p')?.textContent.toLowerCase() || '';

                    const matchesSearch = title.includes(searchTerm) ||
                        description.includes(searchTerm);

                    const status = card.dataset.status;
                    const matchesFilter = activeFilters.length === 0 ||
                        activeFilters.includes(status);

                    card.style.display = matchesSearch && matchesFilter ? '' : 'none';
                });
            }
        }

        // Card Interactions
        class CardManager {
            constructor() {
                this.copyButtons = document.querySelectorAll('.copy-btn');
                this.actionButtons = document.querySelectorAll('.action-btn');
                this.init();
            }

            init() {
                this.copyButtons.forEach(btn => {
                    btn.addEventListener('click', (e) => this.copyCode(e));
                });

                this.actionButtons.forEach(btn => {
                    btn.addEventListener('click', (e) => this.handleAction(e));
                });
            }

            async copyCode(e) {
                const code = e.target.closest('.coupon-code').querySelector('h3').textContent;
                try {
                    await navigator.clipboard.writeText(code);
                    this.showToast('Code copied to clipboard!');
                } catch (err) {
                    console.error('Failed to copy code:', err);
                }
            }

            handleAction(e) {
                const action = e.target.closest('button').title.toLowerCase();
                const card = e.target.closest('.discount-card, .coupon-card');

                switch (action) {
                    case 'edit':
                        this.editItem(card);
                        break;
                    case 'deactivate':
                        this.deactivateItem(card);
                        break;
                }
            }

            editItem(card) {
                const isDiscount = card.classList.contains('discount-card');
                const id = card.dataset.id;

                if (isDiscount) {
                    openDiscountModal(true, {
                        id: id,
                        // Add other properties from card
                    });
                } else {
                    openCouponModal(true, {
                        id: id,
                        // Add other properties from card
                    });
                }
            }

            async deactivateItem(card) {
                const isDiscount = card.classList.contains('discount-card');
                const id = card.dataset.id;

                if (confirm('Are you sure you want to deactivate this item?')) {
                    try {
                        const response = await fetch(`/api/${isDiscount ? 'discounts' : 'coupons'}/${id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                is_active: false
                            })
                        });

                        if (!response.ok) throw new Error('Failed to deactivate');

                        card.dataset.status = 'expired';
                        this.showToast('Item deactivated successfully');
                    } catch (err) {
                        console.error('Deactivation failed:', err);
                        this.showToast('Failed to deactivate item', 'error');
                    }
                }
            }

            showToast(message, type = 'success') {
                // Implement toast notification
                console.log(message);
            }
        }

        // Initialize Components
        new TabManager();
        new FilterManager();
        new CardManager();

        // Modal Functions
        // window.openDiscountModal = (isEdit = false, data = null) => {
        //     const modal = document.getElementById('discount-form-modal');
        //     const form = document.getElementById('discount-form');
        //     const title = modal.querySelector('.modal-title');

        //     title.textContent = isEdit ? 'Edit Discount' : 'Create New Discount';
        //     form.reset();

        //     if (isEdit && data) {
        //         Object.keys(data).forEach(key => {
        //             const input = form.querySelector(`[name="${key}"]`);
        //             if (input) input.value = data[key];
        //         });
        //     }

        //     modal.showModal();
        // };

        // window.openCouponModal = (isEdit = false, data = null) => {
        //     const modal = document.getElementById('coupon-form-modal');
        //     const form = document.getElementById('coupon-form');
        //     const title = modal.querySelector('.modal-title');

        //     title.textContent = isEdit ? 'Edit Coupon' : 'Create New Coupon';
        //     form.reset();

        //     if (isEdit && data) {
        //         Object.keys(data).forEach(key => {
        //             const input = form.querySelector(`[name="${key}"]`);
        //             if (input) input.value = data[key];
        //         });
        //     }

        //     modal.showModal();
        // };
    });
</script>
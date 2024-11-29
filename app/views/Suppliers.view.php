<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>

    <div class="content">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1>Supplier Management</h1>
                <p class="header-description">Manage your suppliers and their details</p>
            </div>
            <div class="header-actions">
                <button id="addSupplierBtn" class="btn btn-primary">
                    <span class="material-symbols-rounded">add</span>
                    Add Supplier
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card glass">
                <div class="stat-icon" style="background: var(--primary-50)">
                    <span class="material-symbols-rounded" style="color: var(--primary-600)">groups</span>
                </div>
                <div class="stat-info">
                    <h3>Total Suppliers</h3>
                    <p id="totalSuppliers">0</p>
                </div>
            </div>
            <div class="stat-card glass">
                <div class="stat-icon" style="background: var(--success-50)">
                    <span class="material-symbols-rounded" style="color: var(--success-600)">check_circle</span>
                </div>
                <div class="stat-info">
                    <h3>Active Suppliers</h3>
                    <p id="activeSuppliers">0</p>
                </div>
            </div>
            <div class="stat-card glass">
                <div class="stat-icon" style="background: var(--warning-50)">
                    <span class="material-symbols-rounded" style="color: var(--warning-600)">inventory</span>
                </div>
                <div class="stat-info">
                    <h3>Products Supplied</h3>
                    <p id="totalProducts">0</p>
                </div>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="search-container glass">
            <div class="search-box">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="searchInput" placeholder="Search suppliers by name, company or products...">
            </div>
            <div class="filter-actions">
                <div class="filter-group">
                    <label>Status</label>
                    <div class="filter-chips">
                        <button class="filter-chip active" data-filter="all">
                            All
                            <span class="chip-count">0</span>
                        </button>
                        <button class="filter-chip" data-filter="active">
                            Active
                            <span class="chip-count">0</span>
                        </button>
                        <button class="filter-chip" data-filter="inactive">
                            Inactive
                            <span class="chip-count">0</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suppliers Table -->
        <div class="table-container glass">
            <table class="supplier-table">
                <thead>
                    <tr>
                        <th>Company Details</th>
                        <th>Contact Person</th>
                        <th>Products</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="supplierTableBody">
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr data-id="<?= $supplier['id'] ?>">
                            <td>
                                <div class="supplier-info">
                                    <h4><?= htmlspecialchars($supplier['company_name']) ?></h4>
                                    <span><?= htmlspecialchars($supplier['email']) ?></span>
                                    <span><?= htmlspecialchars($supplier['phone_number']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($supplier['contact_person']) ?></td>
                            <td>
                                <div class="product-count">
                                    <span class="count"><?= count($supplier['products']) ?></span>
                                    <span class="label">Products</span>
                                </div>
                            </td>
                            <td>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="material-symbols-rounded <?= $i <= $supplier['rating'] ? 'filled' : '' ?>">
                                            star
                                        </span>
                                    <?php endfor; ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge <?= $supplier['is_active'] ? 'active' : 'inactive' ?>">
                                    <?= $supplier['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn" title="Edit" onclick="supplierManager.editSupplier(<?= $supplier['id'] ?>)">
                                        <span class="material-symbols-rounded">edit</span>
                                    </button>
                                    <button class="action-btn" title="Delete" onclick="supplierManager.deleteSupplier(<?= $supplier['id'] ?>)">
                                        <span class="material-symbols-rounded">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Supplier Modal -->
    <dialog id="supplierModal" class="modal">
        <form id="supplierForm" method="dialog">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Supplier</h2>
                <button type="button" class="close-btn">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="supplierId" name="id">

                <div class="form-group">
                    <label for="companyName">Company Name*</label>
                    <input type="text" id="companyName" name="company_name" required>
                </div>

                <div class="form-group">
                    <label for="contactPerson">Contact Person</label>
                    <input type="text" id="contactPerson" name="contact_person">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email*</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone_number">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address*</label>
                    <textarea id="address" name="address" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="paymentTerms">Payment Terms</label>
                        <textarea id="paymentTerms" name="payment_terms"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="creditLimit">Credit Limit</label>
                        <input type="number" id="creditLimit" name="credit_limit" step="0.01">
                    </div>
                </div>

                <div class="form-group">
                    <label>Rating</label>
                    <div class="rating-input">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="material-symbols-rounded" data-rating="<?= $i ?>">star</span>
                        <?php endfor; ?>
                        <input type="hidden" id="rating" name="rating" value="0">
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="isActive" name="is_active" checked>
                        <span>Active Supplier</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Supplier</button>
            </div>
        </form>
    </dialog>
</div>

<script>
    class SupplierManager {
        constructor() {
            this.modal = document.getElementById('supplierModal');
            this.form = document.getElementById('supplierForm');
            this.searchInput = document.getElementById('searchInput');
            this.filterChips = document.querySelectorAll('.filter-chip');
            this.currentId = null;

            this.init();
        }

        init() {
            // Add new supplier button
            document.getElementById('addSupplierBtn').addEventListener('click', () => {
                this.openModal();
            });

            // Form submission
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSubmit();
            });

            // Modal close
            this.modal.querySelector('.close-btn').addEventListener('click', () => {
                this.closeModal();
            });
            document.getElementById('cancelBtn').addEventListener('click', () => {
                this.closeModal();
            });

            // Search functionality
            this.searchInput.addEventListener('input', () => this.filterSuppliers());

            // Filter chips
            this.filterChips.forEach(chip => {
                chip.addEventListener('click', () => {
                    this.filterChips.forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');
                    this.filterSuppliers();
                });
            });

            // Rating system
            this.setupRating();

            this.loadSuppliers();
        }

        setupRating() {
            const ratingInput = this.form.querySelector('.rating-input');
            const stars = ratingInput.querySelectorAll('.material-symbols-rounded');
            const ratingField = document.getElementById('rating');

            stars.forEach(star => {
                star.addEventListener('mouseover', () => {
                    const rating = star.dataset.rating;
                    this.updateStars(stars, rating);
                });

                star.addEventListener('click', () => {
                    const rating = star.dataset.rating;
                    ratingField.value = rating;
                    this.updateStars(stars, rating, true);
                });
            });

            ratingInput.addEventListener('mouseleave', () => {
                this.updateStars(stars, ratingField.value);
            });
        }

        updateStars(stars, rating, permanent = false) {
            stars.forEach(star => {
                const starRating = star.dataset.rating;
                if (starRating <= rating) {
                    star.style.color = 'var(--warning-400)';
                    if (permanent) star.classList.add('active');
                } else {
                    star.style.color = 'var(--text-disabled)';
                    if (permanent) star.classList.remove('active');
                }
            });
        }

        async loadSuppliers() {
            try {
                const response = await fetch('/api/suppliers');
                const suppliers = await response.json();
                this.updateTable(suppliers);
                this.updateStats(suppliers);
            } catch (error) {
                console.error('Error loading suppliers:', error);
                this.showError('Failed to load suppliers');
            }
        }

        async handleSubmit() {
            const formData = new FormData(this.form);
            const data = Object.fromEntries(formData.entries());

            if (!this.validateForm(data)) return;

            try {
                const url = this.currentId ?
                    `/api/suppliers/${this.currentId}` :
                    '/api/suppliers';

                const method = this.currentId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) throw new Error('Failed to save supplier');

                await this.loadSuppliers();
                this.closeModal();
                this.showSuccess(`Supplier successfully ${this.currentId ? 'updated' : 'created'}`);

            } catch (error) {
                this.showError('Failed to save supplier');
                console.error(error);
            }
        }

        validateForm(data) {
            const required = ['company_name', 'email', 'address'];

            for (const field of required) {
                if (!data[field]?.trim()) {
                    this.showError(`${field.replace('_', ' ')} is required`);
                    return false;
                }
            }

            if (data.email && !this.validateEmail(data.email)) {
                this.showError('Invalid email address');
                return false;
            }

            if (data.phone_number && !this.validatePhone(data.phone_number)) {
                this.showError('Invalid phone number');
                return false;
            }

            return true;
        }

        validateEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        validatePhone(phone) {
            return /^\+?[\d\s-]{10,}$/.test(phone);
        }

        async deleteSupplier(id) {
            if (!confirm('Are you sure you want to delete this supplier?')) return;

            try {
                const response = await fetch(`/api/suppliers/${id}`, {
                    method: 'DELETE'
                });

                if (!response.ok) throw new Error('Failed to delete supplier');

                await this.loadSuppliers();
                this.showSuccess('Supplier successfully deleted');

            } catch (error) {
                this.showError('Failed to delete supplier');
                console.error(error);
            }
        }

        editSupplier(id) {
            this.openModal(id);
        }

        openModal(supplierId = null) {
            this.currentId = supplierId;
            this.form.reset();

            const title = this.modal.querySelector('#modalTitle');
            title.textContent = supplierId ? 'Edit Supplier' : 'Add New Supplier';

            if (supplierId) {
                this.loadSupplierDetails(supplierId);
            }

            this.modal.showModal();
        }

        async loadSupplierDetails(id) {
            try {
                const response = await fetch(`/api/suppliers/${id}`);
                if (!response.ok) throw new Error('Failed to load supplier details');

                const supplier = await response.json();
                this.populateForm(supplier);
            } catch (error) {
                this.showError('Failed to load supplier details');
                console.error(error);
            }
        }

        populateForm(supplier) {
            const fields = [
                'company_name', 'contact_person', 'email', 'phone_number',
                'address', 'payment_terms', 'credit_limit', 'rating'
            ];

            fields.forEach(field => {
                const element = this.form.querySelector(`[name="${field}"]`);
                if (element) {
                    element.value = supplier[field] || '';
                }
            });

            document.getElementById('isActive').checked = supplier.is_active;

            // Update rating stars
            const stars = this.form.querySelectorAll('.rating-input .material-symbols-rounded');
            this.updateStars(stars, supplier.rating, true);
        }

        closeModal() {
            this.modal.close();
            this.currentId = null;
        }

        filterSuppliers() {
            const searchTerm = this.searchInput.value.toLowerCase();
            const status = document.querySelector('.filter-chip.active').dataset.filter;

            const rows = document.querySelectorAll('#supplierTableBody tr');
            let visibleCount = 0;
            let activeCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const isActive = row.querySelector('.status-badge').classList.contains('active');

                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = status === 'all' ||
                    (status === 'active' && isActive) ||
                    (status === 'inactive' && !isActive);

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                    if (isActive) activeCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            this.updateStats({
                total: visibleCount,
                active: activeCount
            });
        }

        updateStats(data) {
            document.getElementById('totalSuppliers').textContent = data.total;
            document.getElementById('activeSuppliers').textContent = data.active;
            document.getElementById('totalProducts').textContent =
                data.products?.length || '0';

            // Update filter chips counts
            const counts = {
                'all': data.total,
                'active': data.active,
                'inactive': data.total - data.active
            };

            this.filterChips.forEach(chip => {
                const count = counts[chip.dataset.filter];
                chip.querySelector('.chip-count').textContent = count;
            });
        }

        showSuccess(message) {
            this.showNotification(message, 'success');
        }

        showError(message) {
            this.showNotification(message, 'error');
        }

        showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }

    // Initialize when document loads
    document.addEventListener('DOMContentLoaded', () => {
        window.supplierManager = new SupplierManager();
    });
</script>

<style>
    /* Supplier Management Page */
    .content {
        padding: 2rem;
        background: var(--surface-light);
        height: 100%;
        overflow: auto;
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

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.5rem;
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 12px;
    }

    .stat-info h3 {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    .stat-info p {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
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

    .filter-actions {
        display: flex;
        gap: 2rem;
    }

    .filter-group {
        flex: 1;
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

    /* Supplier Table */
    .supplier-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .supplier-table th {
        background: var(--surface-light);
        padding: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        text-align: left;
        letter-spacing: 0.05em;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-light);
    }

    .supplier-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-light);
    }

    .supplier-info h4 {
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .supplier-info span {
        display: block;
        font-size: 0.85rem;
        color: var(--text-tertiary);
    }

    .product-count {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .product-count .count {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--primary-600);
    }

    .product-count .label {
        font-size: 0.85rem;
        color: var(--text-tertiary);
    }

    /* Rating Stars */
    .rating {
        display: flex;
        gap: 0.25rem;
    }

    .rating .material-symbols-rounded {
        color: var(--text-disabled);
        font-variation-settings: 'FILL' 1;
    }

    .rating .material-symbols-rounded.filled {
        color: var(--warning-400);
    }

    .rating-input {
        display: flex;
        gap: 0.25rem;
    }

    .rating-input .material-symbols-rounded {
        cursor: pointer;
        color: var(--text-disabled);
        font-variation-settings: 'FILL' 1;
        transition: color 0.2s;
    }

    .rating-input .material-symbols-rounded:hover,
    .rating-input .material-symbols-rounded.active {
        color: var(--warning-400);
    }

    /* Status Badge */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-badge.active {
        background: var(--success-50);
        color: var(--success-600);
    }

    .status-badge.inactive {
        background: var(--danger-50);
        color: var(--danger-600);
    }

    /* Action Buttons */
    .actions {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        padding: 0.5rem;
        border: none;
        border-radius: 6px;
        color: var(--text-tertiary);
        background: transparent;
        cursor: pointer;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: var(--surface-light);
        color: var(--text-primary);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
        }

        .filter-actions {
            flex-direction: column;
            gap: 1rem;
        }
    }

    @media (max-width: 768px) {
        .content {
            padding: 1rem;
        }

        .supplier-info {
            flex-direction: column;
        }

        .table-container {
            overflow-x: auto;
        }

        .modal {
            width: 95%;
        }
    }
</style>
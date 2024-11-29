<!-- Product Form Modal -->
<dialog id="productFormModal" class="modal">
    <div class="modal-content">
        <h2 class="modal-title">Add New Product</h2>
        <form id="productForm" action="/product/new" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name*</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="description">Description*</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="measure_unit">Measure Unit*</label>
                    <select id="measure_unit" name="measure_unit" required>
                        <option value="kg">Kilogram (kg)</option>
                        <option value="g">Gram (g)</option>
                        <option value="l">Liter (l)</option>
                        <option value="ml">Milliliter (ml)</option>
                        <option value="items">Items</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="weight">Weight</label>
                    <input type="number" id="weight" name="weight" step="0.001">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="dimensions">Dimensions</label>
                    <input type="text" id="dimensions" name="dimensions" placeholder="L x W x H">
                </div>
                <div class="form-group">
                    <label for="shelf_life_days">Shelf Life (Days)</label>
                    <input type="number" id="shelf_life_days" name="shelf_life_days">
                </div>
            </div>

            <div class="form-group">
                <label for="storage_requirements">Storage Requirements</label>
                <textarea id="storage_requirements" name="storage_requirements"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="barcode">Barcode</label>
                    <input type="text" id="barcode" name="barcode">
                </div>
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="min_threshold">Minimum Threshold*</label>
                    <input type="number" id="min_threshold" name="min_threshold" step="0.001" required>
                </div>
                <div class="form-group">
                    <label for="max_threshold">Maximum Threshold*</label>
                    <input type="number" id="max_threshold" name="max_threshold" step="0.001" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="reorder_point">Reorder Point*</label>
                    <input type="number" id="reorder_point" name="reorder_point" step="0.001" required>
                </div>
                <div class="form-group">
                    <label for="reorder_quantity">Reorder Quantity*</label>
                    <input type="number" id="reorder_quantity" name="reorder_quantity" step="0.001" required>
                </div>
            </div>

            <div class="form-group">
                <label for="alert_email">Alert Email</label>
                <input type="email" id="alert_email" name="alert_email">
            </div>

            <!-- Add this after the alert_email form-group -->
            <div class="form-group">
                <label for="categorySearch">Categories</label>
                <div class="category-search-container">
                    <input type="text" id="categorySearch" placeholder="Search categories...">
                    <div id="categoryDropdown" class="category-dropdown"></div>
                </div>
                <div id="selectedCategories" class="selected-categories"></div>
                <input type="hidden" name="categories" id="categoryIds">
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeProductModal()">Cancel</button>
                <button type="submit" class="btn-submit">Save Product</button>
            </div>
        </form>
    </div>
</dialog>

<style>
    /* Category Search Styles */
    .category-search-container {
        position: relative;
        margin-bottom: 0.5rem;
    }

    .category-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--surface-white);
        border: 1px solid var(--border-medium);
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        max-height: 200px;
        overflow-y: auto;
        z-index: 100;
    }

    .category-dropdown.show {
        display: block;
    }

    .category-option {
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
    }

    .category-option:hover {
        background: var(--primary-50);
        color: var(--primary-600);
    }

    .selected-categories {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        min-height: 32px;
    }

    .category-chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        background: var(--primary-50);
        color: var(--primary-600);
        border-radius: 16px;
        font-size: 0.875rem;
    }

    .remove-chip {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--primary-200);
        color: var(--primary-700);
        cursor: pointer;
        transition: all 0.2s;
    }

    .remove-chip:hover {
        background: var(--primary-300);
        color: var(--primary-800);
    }

    /* Modal Styles */
    .modal {
        padding: 0;
        border: none;
        border-radius: 12px;
        background: var(--glass-white);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow-xl);
        max-width: 800px;
        width: 90%;
    }

    .modal::backdrop {
        background: var(--glass-dark);
    }

    .modal-content {
        padding: 2rem;
    }

    .modal h2 {
        color: var(--text-primary);
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0 0 1.5rem;
    }

    /* Form Layout */
    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }

    /* Form Controls */
    label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-secondary);
        font-weight: 500;
        font-size: 0.875rem;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-light);
        border-radius: 8px;
        background: var(--surface-white);
        color: var(--text-primary);
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    input:hover,
    select:hover,
    textarea:hover {
        border-color: var(--border-medium);
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: var(--primary-600);
        box-shadow: 0 0 0 3px var(--primary-100);
    }

    input::placeholder {
        color: var(--text-tertiary);
    }

    textarea {
        min-height: 100px;
        resize: vertical;
    }

    /* File Input */
    input[type="file"] {
        padding: 0.5rem;
        background: var(--secondary-50);
        border-style: dashed;
    }

    /* Required Fields */
    label[for*="required"]::after,
    input[required]+label::after {
        content: "*";
        color: var(--danger-500);
        margin-left: 4px;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-light);
    }

    .btn-submit,
    .btn-cancel {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-submit {
        background: var(--primary-500);
        color: var(--surface-white);
    }

    .btn-submit:hover {
        background: var(--primary-600);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .btn-submit:active {
        background: var(--primary-700);
        transform: translateY(0);
    }

    .btn-cancel {
        background: var(--danger-500);
        color: var(--surface-white);
    }

    .btn-cancel:hover {
        background: var(--danger-600);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .btn-cancel:active {
        background: var(--danger-700);
        transform: translateY(0);
    }

    /* Disabled States */
    input:disabled,
    select:disabled,
    textarea:disabled {
        background: var(--secondary-100);
        color: var(--text-tertiary);
        cursor: not-allowed;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .modal-content {
            padding: 1.5rem;
        }
    }
</style>

<script>
    function openProductModal(isEdit = false, productData = null) {
        const modal = document.getElementById('productFormModal');
        const form = document.getElementById('productForm');
        const title = modal.querySelector('.modal-title');

        form.action = isEdit ? `/products/update` : '/products/new';
        if (isEdit) {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = productData.id;
            form.appendChild(idInput);
        }
        title.textContent = isEdit ? 'Update Product' : 'New Product';
        form.reset();

        if (isEdit && productData) {
            // Pre-fill form with product data
            Object.keys(productData).forEach(key => {
                const input = form.querySelector(`:is(input, textarea)[name="${key}"]:not(input[type="file"])`);
                if (input) {
                    input.value = productData[key];
                }
            });
        }

        modal.showModal();
    }

    function closeProductModal() {
        const modal = document.getElementById('productFormModal');
        modal.close();
    }

    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Add your form submission logic here
        const formData = new FormData(this);

        // Example AJAX submission
        fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeProductModal();
                    // Add success message or refresh product list
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Handle error case
            });
    });

    // Add this to your existing JavaScript
    class CategoryManager {
        constructor() {
            this.searchInput = document.getElementById('categorySearch');
            this.dropdown = document.getElementById('categoryDropdown');
            this.selectedContainer = document.getElementById('selectedCategories');
            this.hiddenInput = document.getElementById('categoryIds');
            this.selectedCategories = new Map(); // id -> {id, name}

            this.init();
        }

        init() {
            this.searchInput.addEventListener('input', () => this.handleSearch());
            this.searchInput.addEventListener('focus', () => this.showDropdown());

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.category-search-container')) {
                    this.hideDropdown();
                }
            });

            // Initialize hidden input
            this.updateHiddenInput();
        }

        async handleSearch() {
            const searchTerm = this.searchInput.value.trim();
            if (searchTerm.length < 2) {
                this.hideDropdown();
                return;
            }

            try {
                const response = await fetch(`/api/categories/search?q=${encodeURIComponent(searchTerm)}`);
                const categories = (await response.json()).data.results;

                this.renderDropdown(categories);
            } catch (error) {
                console.error('Error searching categories:', error);
            }
        }

        renderDropdown(categories) {
            this.dropdown.innerHTML = '';

            categories.forEach(category => {
                if (this.selectedCategories.has(category.id)) return;

                const option = document.createElement('div');
                option.className = 'category-option';
                option.textContent = category.name;
                option.addEventListener('click', () => this.addCategory(category));

                this.dropdown.appendChild(option);
            });

            this.showDropdown();
        }

        addCategory(category) {
            if (this.selectedCategories.has(category.id)) return;

            this.selectedCategories.set(category.id, category);

            const chip = document.createElement('div');
            chip.className = 'category-chip';
            chip.innerHTML = `
            ${category.name}
            <span class="remove-chip" data-id="${category.id}">&times;</span>
        `;

            chip.querySelector('.remove-chip').addEventListener('click',
                () => this.removeCategory(category.id));

            this.selectedContainer.appendChild(chip);
            this.searchInput.value = '';
            this.hideDropdown();
            this.updateHiddenInput();
        }

        removeCategory(categoryId) {
            this.selectedCategories.delete(categoryId);
            const chip = this.selectedContainer.querySelector(`[data-id="${categoryId}"]`).parentElement;
            chip.remove();
            this.updateHiddenInput();
        }

        updateHiddenInput() {
            this.hiddenInput.value = Array.from(this.selectedCategories.keys()).join(',');
        }

        showDropdown() {
            if (this.dropdown.children.length > 0) {
                this.dropdown.classList.add('show');
            }
        }

        hideDropdown() {
            this.dropdown.classList.remove('show');
        }
    }

    // Initialize category manager when document loads
    document.addEventListener('DOMContentLoaded', () => {
        const categoryManager = new CategoryManager();
    });
</script>
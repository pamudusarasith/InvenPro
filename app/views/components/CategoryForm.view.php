<dialog id="categoryFormModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Category</h2>
            <button type="button" class="close-button" onclick="closeCategoryModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        <form id="categoryForm" method="POST">
            <input type="hidden" id="categoryId" name="id">

            <div class="form-group">
                <label for="categoryName">Category Name*</label>
                <input type="text"
                    id="categoryName"
                    name="name"
                    required
                    placeholder="Enter category name">
            </div>

            <div class="form-group">
                <label for="categoryDescription">Description</label>
                <textarea id="categoryDescription"
                    name="description"
                    rows="4"
                    placeholder="Enter category description"></textarea>
            </div>

            <div class="form-group">
                <label for="parentSearch">Parent Category</label>
                <div class="search-wrapper">
                    <input type="text"
                        id="parentSearch"
                        placeholder="Search parent categories..."
                        autocomplete="off">
                    <div id="searchResults" class="search-results">
                        <div class="search-result-item" data-id="1">Electronics</div>
                        <div class="search-result-item" data-id="2">Clothing</div>
                        <div class="search-result-item" data-id="3">Food & Beverages</div>
                        <div class="search-result-item" data-id="4">Books</div>
                        <div class="search-result-item" data-id="5">Sports Equipment</div>
                    </div>
                </div>
                <div id="selectedParentChips" class="chips-container">
                    <!-- Example of a selected category chip -->
                    <div class="category-chip" data-id="1">
                        Electronics
                        <span class="chip-remove material-symbols-rounded">close</span>
                    </div>
                </div>
                <input type="hidden" id="parentId" name="parent_id">
            </div>

            <div class="form-error" id="formError">
                <span class="material-symbols-rounded">error</span>
                <span class="error-message">Please fill all required fields</span>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeCategoryModal()">Cancel</button>
                <button type="submit" class="btn-submit">Save Category</button>
            </div>
        </form>
    </div>
</dialog>

<style>
    .modal {
        padding: 0;
        border: none;
        border-radius: 12px;
        background: var(--glass-white);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow-xl);
        max-width: 600px;
        width: 90%;
    }

    .modal::backdrop {
        background: var(--glass-dark);
    }

    .modal-content {
        padding: 2rem;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .modal-title {
        color: var(--text-primary);
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }

    /* Form Controls */
    .form-group {
        margin-bottom: 1.25rem;
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-secondary);
        font-weight: 500;
        font-size: 0.875rem;
    }

    input,
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

    /* Search Results */
    .search-wrapper {
        position: relative;
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--surface-white);
        border: 1px solid var(--border-light);
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        margin-top: 4px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
    }

    .search-result-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .search-result-item:hover {
        background: var(--primary-50);
    }

    /* Category Chips */
    .chips-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .category-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        background: var(--primary-100);
        color: var(--primary-700);
        border-radius: 16px;
        font-size: 0.875rem;
    }

    .chip-remove {
        cursor: pointer;
        font-size: 1rem;
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

    /* Error State */
    .form-error {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--danger-600);
        font-size: 0.875rem;
        margin-top: 1rem;
        padding: 0.75rem;
        background: var(--danger-50);
        border-radius: 8px;
    }

    .close-button {
        background: none;
        border: none;
        outline: none;
        cursor: pointer;
    }
</style>

<script>
    let selectedParent = null;
    const mockCategories = [{
            id: 1,
            name: 'Electronics'
        },
        {
            id: 2,
            name: 'Clothing'
        },
        {
            id: 3,
            name: 'Food & Beverages'
        },
        {
            id: 4,
            name: 'Books'
        },
        {
            id: 5,
            name: 'Sports Equipment'
        }
    ];

    function openCategoryModal(isEdit = false, categoryData = null) {
        const modal = document.getElementById('categoryFormModal');
        const form = document.getElementById('categoryForm');
        const title = modal.querySelector('.modal-title');

        title.textContent = isEdit ? 'Update Category' : 'New Category';
        form.reset();

        if (isEdit && categoryData) {
            document.getElementById('categoryId').value = categoryData.id;
            document.getElementById('categoryName').value = categoryData.name;
            document.getElementById('categoryDescription').value = categoryData.description;
            if (categoryData.parent_id) {
                const parent = mockCategories.find(c => c.id === categoryData.parent_id);
                if (parent) {
                    addCategoryChip(parent);
                }
            }
        }

        modal.showModal();
    }

    function closeCategoryModal() {
        const modal = document.getElementById('categoryFormModal');
        modal.close();
        resetForm();
    }

    function resetForm() {
        document.getElementById('categoryForm').reset();
        document.getElementById('selectedParentChips').innerHTML = '';
        selectedParent = null;
        document.getElementById('parentId').value = '';
    }

    function setupSearchListeners() {
        const searchInput = document.getElementById('parentSearch');
        const resultsContainer = document.getElementById('searchResults');

        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filteredCategories = mockCategories.filter(cat =>
                cat.name.toLowerCase().includes(searchTerm)
            );

            resultsContainer.innerHTML = '';
            filteredCategories.forEach(cat => {
                const div = document.createElement('div');
                div.className = 'search-result-item';
                div.textContent = cat.name;
                div.dataset.id = cat.id;
                div.onclick = () => selectParentCategory(cat);
                resultsContainer.appendChild(div);
            });

            resultsContainer.style.display = searchTerm ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-wrapper')) {
                resultsContainer.style.display = 'none';
            }
        });
    }

    function selectParentCategory(category) {
        selectedParent = category;
        document.getElementById('parentId').value = category.id;
        document.getElementById('parentSearch').value = '';
        document.getElementById('searchResults').style.display = 'none';
        addCategoryChip(category);
    }

    function addCategoryChip(category) {
        const chipsContainer = document.getElementById('selectedParentChips');
        chipsContainer.innerHTML = '';

        const chip = document.createElement('div');
        chip.className = 'category-chip';
        chip.innerHTML = `
        ${category.name}
        <span class="chip-remove material-symbols-rounded" onclick="removeParentCategory()">close</span>
    `;
        chipsContainer.appendChild(chip);
    }

    function removeParentCategory() {
        selectedParent = null;
        document.getElementById('parentId').value = '';
        document.getElementById('selectedParentChips').innerHTML = '';
    }

    document.getElementById('categoryForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            const url = data.id ? `/api/categories/${data.id}` : '/api/categories';
            const method = data.id ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) throw new Error('Failed to save category');

            closeCategoryModal();
            // Refresh categories list or update UI
            window.location.reload();
        } catch (error) {
            const errorDiv = document.getElementById('formError');
            errorDiv.style.display = 'flex';
            errorDiv.querySelector('.error-message').textContent = error.message;
        }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        setupSearchListeners();
    });
</script>
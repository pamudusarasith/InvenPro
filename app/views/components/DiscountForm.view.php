<dialog id="discountFormModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">Create New Discount</h2>
      <button type="button" class="close-button" onclick="closeDiscountModal()">
        <span class="material-symbols-rounded">close</span>
      </button>
    </div>

    <form id="discountForm" method="POST">
      <!-- Basic Info -->
      <div class="form-section">
        <h4>Basic Information</h4>

        <div class="form-group">
          <label for="discountName">Discount Name*</label>
          <input type="text" id="discountName" name="name" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="discountType">Type*</label>
            <select id="discountType" name="type" required>
              <option value="bill">Bill Discount</option>
              <option value="product">Product Discount</option>
              <option value="category">Category Discount</option>
            </select>
          </div>
          <div class="form-group">
            <label for="discountValue">Value*</label>
            <div class="value-input">
              <input type="number" id="discountValue" name="value" required min="0" step="0.01">
              <label class="checkbox-label">
                <input type="checkbox" id="isPercentage" name="is_percentage">
                <span>Percentage</span>
              </label>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea id="description" name="description" rows="3"></textarea>
        </div>
      </div>

      <!-- Category Selection -->
      <div class="form-section" id="categorySection" style="display: none;">
        <h4>Select Categories</h4>
        <div class="search-container">
          <div class="search-wrapper">
            <input type="text"
              id="categorySearch"
              placeholder="Search categories..."
              autocomplete="off">
            <div id="categoryResults" class="search-results"></div>
          </div>
        </div>
        <div id="selectedCategories" class="chips-container"></div>
      </div>

      <!-- Product Selection -->
      <div class="form-section" id="productSection" style="display: none;">
        <h4>Select Products</h4>
        <div class="search-container">
          <div class="search-wrapper">
            <input type="text"
              id="productSearch"
              placeholder="Search products..."
              autocomplete="off">
            <div id="productResults" class="search-results"></div>
          </div>
        </div>

        <!-- Quantity Input -->
        <div id="productQuantityInput" class="quantity-input" style="display: none;">
          <div class="form-row">
            <div class="form-group">
              <label for="productQty">Minimum Quantity Required</label>
              <input type="number" id="productQty" min="1" step="1">
            </div>
            <button type="button" class="btn-secondary" id="addProductBtn">
              Add Product
            </button>
          </div>
        </div>

        <!-- Selected Products Table -->
        <table id="selectedProductsTable" style="display: none;">
          <thead>
            <tr>
              <th>Product</th>
              <th>Min. Quantity</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <!-- Validity Period -->
      <div class="form-section">
        <h4>Validity Period</h4>
        <div class="form-row">
          <div class="form-group">
            <label for="validFrom">Valid From*</label>
            <input type="datetime-local" id="validFrom" name="valid_from" required>
          </div>
          <div class="form-group">
            <label for="validUntil">Valid Until*</label>
            <input type="datetime-local" id="validUntil" name="valid_until" required>
          </div>
        </div>
      </div>

      <!-- Constraints -->
      <div class="form-section">
        <h4>Additional Settings</h4>
        <div class="form-row">
          <div class="form-group">
            <label for="minPurchase">Minimum Purchase (Rs.)</label>
            <input type="number" id="minPurchase" name="min_purchase" min="0" step="0.01">
          </div>
          <div class="form-group">
            <label for="maxDiscount">Maximum Discount (Rs.)</label>
            <input type="number" id="maxDiscount" name="max_discount" min="0" step="0.01">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="usageLimit">Usage Limit</label>
            <input type="number" id="usageLimit" name="usage_limit" min="0">
          </div>
          <div class="form-group">
            <label for="priority">Priority</label>
            <input type="number" id="priority" name="priority" min="0">
          </div>
        </div>

        <div class="constraints-group">
          <label class="checkbox-label">
            <input type="checkbox" id="isLoyalty" name="is_loyalty_only">
            <span>Loyalty Members Only</span>
          </label>
          <label class="checkbox-label">
            <input type="checkbox" id="isCombinable" name="combinable">
            <span>Can be combined with other discounts</span>
          </label>
        </div>
      </div>

      <!-- Error Display -->
      <div class="form-error" id="formError">
        <span class="material-symbols-rounded">error</span>
        <span class="error-message"></span>
      </div>

      <!-- Form Actions -->
      <div class="form-actions">
        <button type="button" class="btn-cancel" onclick="closeDiscountModal()">Cancel</button>
        <button type="submit" class="btn-submit">
          <span class="button-text">Save Discount</span>
          <span class="loader"></span>
        </button>
      </div>
    </form>
  </div>
</dialog>

<style>
  /* Modal Base */
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

  .close-button {
    background: none;
    border: none;
    outline: none;
    cursor: pointer;
    color: var(--text-tertiary);
    transition: color 0.2s ease;
  }

  .close-button:hover {
    color: var(--text-primary);
  }

  /* Form Sections */
  .form-section {
    background: var(--surface-white);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .form-section h4 {
    color: var(--text-primary);
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 1rem 0;
  }

  /* Form Controls */
  .form-group {
    margin-bottom: 1.25rem;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
  }

  label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 0.875rem;
  }

  input:not([type="checkbox"]),
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

  textarea {
    min-height: 100px;
    resize: vertical;
  }

  /* Search Components */
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
    display: none;
  }

  .search-result-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: background 0.2s ease;
  }

  .search-result-item:hover {
    background: var(--primary-50);
    color: var(--primary-600);
  }

  /* Chips Styling */
  .chips-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
    min-height: 2rem;
    padding: 0.5rem;
    background: var(--surface-light);
    border-radius: 8px;
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
    animation: scaleIn 0.2s ease;
  }

  .chip-remove {
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.2s ease;
  }

  .chip-remove:hover {
    color: var(--primary-800);
    transform: scale(1.1);
  }

  /* Product Table */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
  }

  th,
  td {
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.875rem;
  }

  th {
    background: var(--secondary-50);
    color: var(--text-secondary);
    font-weight: 500;
  }

  td {
    border-bottom: 1px solid var(--border-light);
    color: var(--text-primary);
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

  /* Error States */
  .form-error {
    display: none;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: var(--danger-50);
    color: var(--danger-600);
    border-radius: 8px;
    margin: 1rem 0;
    font-size: 0.875rem;
  }

  #addProductBtn {
    margin: auto;
    padding: 0.6rem;
    border-radius: 8px;
  }

  /* Animations */
  @keyframes scaleIn {
    from {
      transform: scale(0.9);
      opacity: 0;
    }

    to {
      transform: scale(1);
      opacity: 1;
    }
  }

  /* Responsive Design */
  @media (max-width: 640px) {
    .modal-content {
      padding: 1.5rem;
    }

    .form-row {
      grid-template-columns: 1fr;
    }

    .form-actions {
      flex-direction: column-reverse;
    }

    .btn-submit,
    .btn-cancel {
      width: 100%;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // State Management
    const state = {
      selectedProducts: new Map(),
      selectedCategories: new Set(),
      currentProduct: null,
      form: {
        modal: document.getElementById('discountFormModal'),
        form: document.getElementById('discountForm'),
        type: document.getElementById('discountType'),
        sections: {
          category: document.getElementById('categorySection'),
          product: document.getElementById('productSection')
        },
        search: {
          category: document.getElementById('categorySearch'),
          product: document.getElementById('productSearch')
        },
        results: {
          category: document.getElementById('categoryResults'),
          product: document.getElementById('productResults')
        },
        qty: document.getElementById('productQty'),
        error: document.getElementById('formError')
      }
    };

    // Initialize Event Listeners
    initializeFormHandlers();
    initializeSearchHandlers();
    initializeProductHandlers();

    function initializeFormHandlers() {
      // Type Change Handler
      state.form.type.addEventListener('change', () => {
        Object.values(state.form.sections).forEach(section =>
          section.style.display = 'none'
        );

        if (state.form.type.value === 'product') {
          state.form.sections.product.style.display = 'block';
        } else if (state.form.type.value === 'category') {
          state.form.sections.category.style.display = 'block';
        }
      });

      // Form Submit
      state.form.form.addEventListener('submit', handleFormSubmit);
    }

    function initializeSearchHandlers() {
      setupSearch(state.form.search.category, searchCategories, handleCategorySelect);
      setupSearch(state.form.search.product, searchProducts, handleProductSelect);
    }

    function setupSearch(input, searchFn, selectFn) {
      let debounceTimer;

      input.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(async () => {
          if (input.value.length < 2) return;

          const results = await searchFn(input.value);
          showSearchResults(input, results, selectFn);
        }, 300);
      });

      document.addEventListener('click', (e) => {
        if (!input.contains(e.target)) {
          hideSearchResults(input);
        }
      });
    }

    function showSearchResults(input, results, selectFn) {
      const container = input.id === 'categorySearch' ?
        state.form.results.category :
        state.form.results.product;

      container.innerHTML = '';
      container.style.display = 'block';

      results.forEach(item => {
        const div = document.createElement('div');
        div.className = 'search-result-item';
        div.textContent = item.name;
        div.onclick = () => selectFn(item);
        container.appendChild(div);
      });
    }

    function hideSearchResults(input) {
      const container = input.id === 'categorySearch' ?
        state.form.results.category :
        state.form.results.product;
      container.style.display = 'none';
    }

    function handleCategorySelect(category) {
      if (state.selectedCategories.has(category.id)) return;

      state.selectedCategories.add(category.id);
      addCategoryChip(category);
      state.form.search.category.value = '';
      hideSearchResults(state.form.search.category);
    }

    function handleProductSelect(product) {
      if (state.selectedProducts.has(product.id)) return;

      state.currentProduct = product;
      document.getElementById('productQuantityInput').style.display = 'block';
      state.form.qty.value = '1';
      state.form.search.product.value = '';
      hideSearchResults(state.form.search.product);
    }

    function initializeProductHandlers() {
      document.getElementById('addProductBtn').onclick = () => {
        if (!state.currentProduct) return;

        const qty = parseInt(state.form.qty.value);
        if (!qty || qty < 1) {
          showError('Please enter a valid quantity');
          return;
        }

        state.selectedProducts.set(state.currentProduct.id, {
          name: state.currentProduct.name,
          quantity: qty
        });

        updateProductTable();
        resetProductInput();
      };
    }

    function addCategoryChip(category) {
      const container = document.getElementById('selectedCategories');
      const chip = document.createElement('div');
      chip.className = 'category-chip';
      chip.dataset.id = category.id;
      chip.innerHTML = `
            ${category.name}
            <span class="chip-remove material-symbols-rounded">close</span>
        `;

      chip.querySelector('.chip-remove').onclick = () => {
        state.selectedCategories.delete(category.id);
        chip.remove();
      };

      container.appendChild(chip);
    }

    function updateProductTable() {
      const table = document.getElementById('selectedProductsTable');
      const tbody = table.querySelector('tbody');
      tbody.innerHTML = '';

      state.selectedProducts.forEach((product, id) => {
        const row = tbody.insertRow();
        row.innerHTML = `
                <td>${product.name}</td>
                <td>${product.quantity}</td>
                <td>
                    <button type="button" class="btn-icon" onclick="removeProduct('${id}')">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </td>
            `;
      });

      table.style.display = state.selectedProducts.size ? 'table' : 'none';
    }

    async function handleFormSubmit(e) {
      e.preventDefault();
      const formData = new FormData(e.target);

      if (state.form.type.value === 'product') {
        formData.append('products', JSON.stringify(Array.from(state.selectedProducts)));
      } else if (state.form.type.value === 'category') {
        formData.append('categories', JSON.stringify(Array.from(state.selectedCategories)));
      }

      try {
        const response = await fetch('/api/discounts', {
          method: 'POST',
          body: formData
        });

        if (!response.ok) throw new Error('Failed to create discount');

        closeDiscountModal();
        window.location.reload();
      } catch (error) {
        showError(error.message);
      }
    }

    function showError(message) {
      state.form.error.querySelector('.error-message').textContent = message;
      state.form.error.style.display = 'flex';
    }

    function resetProductInput() {
      state.currentProduct = null;
      document.getElementById('productQuantityInput').style.display = 'none';
      state.form.qty.value = '';
    }

    // Global Functions
    window.openDiscountModal = (isEdit = false, data = null) => {
      resetForm();
      if (isEdit && data) {
        populateFormData(data);
      }
      state.form.modal.showModal();
    };

    window.closeDiscountModal = () => {
      state.form.modal.close();
      resetForm();
    };

    window.removeProduct = (id) => {
      state.selectedProducts.delete(id);
      updateProductTable();
    };

    function resetForm() {
      state.form.form.reset();
      state.selectedProducts.clear();
      state.selectedCategories.clear();
      updateProductTable();
      document.getElementById('selectedCategories').innerHTML = '';
      state.form.error.style.display = 'none';
      Object.values(state.form.sections).forEach(section =>
        section.style.display = 'none'
      );
    }

    // Mock API Functions - Replace with actual implementations
    async function searchCategories(query) {
      return [{
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
        }
      ].filter(cat =>
        cat.name.toLowerCase().includes(query.toLowerCase())
      );
    }

    async function searchProducts(query) {
      return [{
          id: 1,
          name: 'Laptop'
        },
        {
          id: 2,
          name: 'Smartphone'
        },
        {
          id: 3,
          name: 'Headphones'
        }
      ].filter(prod =>
        prod.name.toLowerCase().includes(query.toLowerCase())
      );
    }
  });
</script>
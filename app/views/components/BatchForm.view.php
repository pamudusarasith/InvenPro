<!-- Batch Form Modal -->
<dialog id="batchFormModal" class="modal">
    <div class="modal-content">
        <h2>Add New Product Batch</h2>
        <form id="batchForm" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="product_id">Product*</label>
                    <select id="product_id" name="product_id" required>
                        <!-- Products will be populated dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="batch_no">Batch Number*</label>
                    <input type="text" id="batch_no" name="batch_no" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="supplier_id">Supplier*</label>
                    <select id="supplier_id" name="supplier_id" required>
                        <!-- Suppliers will be populated dynamically -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="purchase_order_id">Purchase Order</label>
                    <input type="text" id="purchase_order_id" name="purchase_order_id">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="manufacture_date">Manufacture Date</label>
                    <input type="date" id="manufacture_date" name="manufacture_date">
                </div>
                <div class="form-group">
                    <label for="expiry_date">Expiry Date</label>
                    <input type="date" id="expiry_date" name="expiry_date">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="purchase_price">Purchase Price*</label>
                    <input type="number" id="purchase_price" name="purchase_price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="selling_price">Selling Price*</label>
                    <input type="number" id="selling_price" name="selling_price" step="0.01" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="initial_quantity">Initial Quantity*</label>
                    <input type="number" id="initial_quantity" name="initial_quantity" step="0.001" required>
                </div>
                <div class="form-group">
                    <label for="storage_location">Storage Location</label>
                    <input type="text" id="storage_location" name="storage_location">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="quality_check_status">Quality Check Status</label>
                    <select id="quality_check_status" name="quality_check_status">
                        <option value="pending">Pending</option>
                        <option value="passed">Passed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="quality_check_notes">Quality Check Notes</label>
                    <textarea id="quality_check_notes" name="quality_check_notes"></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Save Batch</button>
                <button type="button" class="btn-cancel" onclick="closeBatchModal()">Cancel</button>
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

    input, select, textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-light);
        border-radius: 8px;
        background: var(--surface-white);
        color: var(--text-primary);
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: var(--primary-600);
        box-shadow: 0 0 0 3px var(--primary-100);
    }

    textarea {
        min-height: 80px;
        resize: vertical;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-light);
    }

    .btn-submit, .btn-cancel {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-submit {
        background: var(--primary-600);
        color: var(--surface-white);
    }

    .btn-submit:hover {
        background: var(--primary-700);
    }

    .btn-cancel {
        background: var(--danger-500);
        color: var(--surface-white);
    }

    .btn-cancel:hover {
        background: var(--danger-600);
    }

    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    function openBatchModal() {
        const modal = document.getElementById('batchFormModal');
        modal.showModal();
    }

    function closeBatchModal() {
        const modal = document.getElementById('batchFormModal');
        modal.close();
    }

    document.getElementById('batchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('/api/batches', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeBatchModal();
                // Add success message or refresh batch list
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Handle error case
        });
    });

    // Initialize date inputs with today's date
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('manufacture_date').value = today;
    });
</script>
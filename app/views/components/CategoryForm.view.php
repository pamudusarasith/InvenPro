<dialog id="category-form-modal" class="modal">
    <div class="row">
        <span class="material-symbols-rounded modal-close-btn modal-close">close</span>
    </div>
    <h1 class="modal-header">Add New Category</h1>
    <form id="category-form" action="/categories/new" method="post">
        <label for="cat-name">Category Name</label>
        <input id="cat-name" type="text" name="name" required>
        
        <label for="cat-description">Description</label>
        <textarea id="cat-description" name="description" rows="4"></textarea>
        
        <label for="cat-parent">Parent Category (Optional)</label>
        <select id="cat-parent" name="parent_id">
            <option value="">None</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <div class="modal-error">
            <span class="material-symbols-rounded">error</span>
            <span id="error-msg" class="error-msg"></span>
        </div>
        
        <div class="row modal-action-btns">
            <span class="loader" style="margin: 24px 12px 0px; font-size: 12px"></span>
            <button type="button" class="btn btn-secondary modal-close">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </div>
    </form>
</dialog>

<style>
.modal {
    border: none;
    border-radius: 8px;
    padding: 24px;
    max-width: 500px;
    width: 90%;
}

.modal::backdrop {
    background: rgba(0, 0, 0, 0.3);
}

.modal-header {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 24px;
    color: #2d3748;
}

.modal label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #4a5568;
}

.modal input,
.modal textarea,
.modal select {
    width: 100%;
    padding: 8px 12px;
    margin-bottom: 16px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 14px;
}

.modal input:focus,
.modal textarea:focus,
.modal select:focus {
    outline: none;
    border-color: #3182ce;
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
}

.modal-close-btn {
    margin: 4px 4px 0 auto;
    cursor: pointer;
    padding: 4px;
}

.modal-error {
    display: none;
    align-items: center;
    color: #e53e3e;
    margin-bottom: 16px;
    font-size: 14px;
}

.modal-error span {
    margin-right: 8px;
}

.modal-action-btns {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: #3182ce;
    color: white;
    border: none;
}

.btn-secondary {
    background: #edf2f7;
    color: #4a5568;
    border: 1px solid #e2e8f0;
}

.btn-primary:hover {
    background: #2c5282;
}

.btn-secondary:hover {
    background: #e2e8f0;
}
</style>

<script>
document.querySelectorAll('.modal-close').forEach(element => {
    element.addEventListener('click', () => {
        document.getElementById('category-form-modal').close();
        document.getElementById('category-form').reset();
        document.querySelector('.modal-error').style.display = 'none';
    });
});

document.getElementById('category-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Category creation failed');
        }

        document.getElementById('category-form-modal').close();
        form.reset();
        window.location.reload();
    } catch (error) {
        const errorDisplay = document.querySelector('.modal-error');
        errorDisplay.style.display = 'flex';
        document.getElementById('error-msg').textContent = error.message;
    }
});
</script>
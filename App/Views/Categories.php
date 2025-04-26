<?php

use App\Services\RBACService;

$canEditCategory = RBACService::hasPermission('edit_category_Details');
$canDeleteCategory = RBACService::hasPermission('delete_category');
$canAddCategory = RBACService::hasPermission('add_category');
?>

<div class="body">
    <?php App\Core\View::render("Navbar") ?>
    <?php App\Core\View::render("Sidebar") ?>

    <div class="main">
        <!-- Header Section -->
        <div class="card glass page-header">
            <div class="header-content">
                <h1>Category Management</h1>
                <p class="subtitle">Manage product categories and organize inventory efficiently</p>
            </div>
            <?php if ($canAddCategory): ?>
                <button class="btn btn-primary" onclick="openAddCategoryDialog()">
                    <span class="icon">add</span>
                    Add Category
                </button>
            <?php endif; ?>
        </div>

        <!-- Controls Section -->
        <div class="card glass controls">
            <div class="search-bar-with-btn">
                <input type="text" id="searchInput" placeholder="Search categories..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button class="icon-btn" onclick="searchCategories()">
                    <span class="icon">search</span>
                </button>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="table-container">
            <table class="data-table" id="Categories-table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Parent Category</th>
                        <?php if ($canEditCategory || $canDeleteCategory): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($categories)) {
                        echo '<tr><td colspan="6" style="text-align: center;">No categories found</td></tr>';
                    } else {
                        foreach ($categories as $category):
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($category['category_name']) ?></td>
                                <td><?= htmlspecialchars($category['description']) ?></td>
                                <td data-id="<?= $category['parent_id'] ?>"><?= $category['parent_category_name'] ? htmlspecialchars($category['parent_category_name']) : "N/A" ?></td>
                                <td>
                                    <?php if ($canEditCategory): ?>
                                        <button class="icon-btn mr-md" title="Edit" onclick="openEditCategoryDialog(event, <?= $category['id']; ?>)">
                                            <span class="icon">edit</span>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($canDeleteCategory): ?>
                                        <button class="icon-btn danger" title="Delete" onclick="deleteCategory(<?= $category['id']; ?>)">
                                            <span class="icon">delete</span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                    <?php endforeach;
                    } ?>
                </tbody>
            </table>

            <div class="pagination-controls">
                <div class="items-per-page">
                    <span>Show:</span>
                    <select class="items-select" onchange="changeItemsPerPage(this.value)">
                        <option value="5" <?= $itemsPerPage == 5 ? "selected" : "" ?>>5</option>
                        <option value="10" <?= $itemsPerPage == 10 ? "selected" : "" ?>>10</option>
                        <option value="20" <?= $itemsPerPage == 20 ? "selected" : "" ?>>20</option>
                        <option value="50" <?= $itemsPerPage == 50 ? "selected" : "" ?>>50</option>
                    </select>
                    <span>entries</span>
                </div>
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <button class="page-btn" onclick="changePage(<?= $page - 1 ?>)">
                                <span class="icon">chevron_left</span>
                            </button>
                        <?php endif; ?>

                        <div class="page-numbers">
                            <?php
                            $maxButtons = 3;
                            $halfMax = floor($maxButtons / 2);
                            $start = max(1, min($page - $halfMax, $totalPages - $maxButtons + 1));
                            $end = min($totalPages, $start + $maxButtons - 1);

                            if ($start > 1) {
                                echo '<span class="page-number">1</span>';
                                if ($start > 2) {
                                    echo '<span class="page-dots">...</span>';
                                }
                            }

                            for ($i = $start; $i <= $end; $i++) {
                                echo '<span class="page-number ' . ($page == $i ? 'active' : '') . '"
                    onclick="changePage(' . $i . ')">' . $i . '</span>';
                            }

                            if ($end < $totalPages) {
                                if ($end < $totalPages - 1) {
                                    echo '<span class="page-dots">...</span>';
                                }
                                echo '<span class="page-number">' . $totalPages . '</span>';
                            }
                            ?>
                        </div>

                        <?php if ($page < $totalPages): ?>
                            <button class="page-btn" onclick="changePage(<?= $page + 1 ?>)">
                                <span class="icon">chevron_right</span>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (RBACService::hasPermission('add_category')): ?>
    <dialog id="addCategoryDialog" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Category</h2>
                <button class="close-btn" onclick="closeAddCategoryDialog()">
                    <span class="icon">close</span>
                </button>
            </div>

            <form id="addCategoryForm" method="POST" action="/Categories/new" onsubmit="validateForm(event);">
                <div class="form-grid">
                    <div class="form-field span-2">
                        <label for="categoryName">Category Name *</label>
                        <input type="text" id="categoryName" name="category_name" required>
                    </div>

                    <div class="form-field span-2">
                        <label for="description">Description </label>
                        <textarea id="description" name="description"></textarea>
                    </div>

                    <div class="form-field span-2">
                        <label for="parent_category">Parent Category </label>
                        <div id="parent_category" class="search-bar">
                            <span class="icon">search</span>
                            <input type="text" id="parent_category" placeholder="Search Parent category...">
                            <div class="search-results"></div>
                        </div>
                    </div>

                    <div class="form-field span-2 chip-container"></div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeAddCategoryDialog()">Cancel</button>
                    <button type="submit" class="btn btn-primary">save</button>
                </div>
            </form>
        </div>
    </dialog>
<?php endif; ?>

<script src="/js/search.js"></script>
<script>
    function changePage(pageNo) {
        const url = new URL(location.href);
        url.searchParams.set('p', pageNo);
        location.href = url.toString();
    }

    function changeItemsPerPage(itemsPerPage) {
        const url = new URL(location.href);
        url.searchParams.set('ipp', itemsPerPage);
        url.searchParams.delete('p');
        location.href = url.toString();
    }

    function openEditCategoryDialog(e, categoryID) {
        document.querySelector('#addCategoryDialog .modal-header h2').innerHTML = "Edit Category";
        const tr = e.target.closest('tr');
        const categoryName = tr.querySelector('td:nth-child(1)').innerText;
        const description = tr.querySelector('td:nth-child(2)').innerText;
        const parentCategoryName = tr.querySelector('td:nth-child(3)').innerText;
        const parentCategoryID = tr.querySelector('td:nth-child(3)').dataset.id;


        const form = document.getElementById('addCategoryForm');
        form.action = `/categories/${categoryID}/update`;
        form.querySelector('input[name="category_name"]').value = categoryName;
        form.querySelector('textarea[name="description"]').value = description;
        const chipContainer = document.querySelector(
            "#addCategoryForm .chip-container"
        );

        if (parentCategoryName !== "N/A") {
            chipContainer.innerHTML = `
                <div class='chip'>
                    ${parentCategoryName}
                    <button type='button' class='chip-delete' onclick='this.parentElement.remove()'>
                        <span class='icon'>close</span>
                    </button>
                    <input type='hidden' name='parent_id' value='${parentCategoryID}'>
                </div>
            `;
        }
        document.getElementById('addCategoryDialog').showModal();

    }

    function deleteCategory(categoryID) {
        if (!confirm('Are you sure want delete this catogory')) {
            return;
        }
        window.location.href = `/categories/${categoryID}/delete`;
    }



    <?php if (RBACService::hasPermission('add_category')): ?>

        function openAddCategoryDialog() {
            document.querySelector('#addCategoryDialog .modal-header h2').innerHTML = "Add New Category";
            const dialog = document.getElementById('addCategoryDialog');

            const form = document.getElementById('addCategoryForm');
            form.action = '/categories/new';
            form.reset();
            document.querySelector(
                "#addCategoryForm .chip-container"
            ).innerHTML = '';
            dialog.showModal();
        }

        function closeAddCategoryDialog() {
            const dialog = document.getElementById('addCategoryDialog');
            dialog.close();
        }

    <?php endif; ?>

    function createCategoryChip(category) {
        const chipContainer = document.querySelector(
            "#addCategoryForm .chip-container"
        );

        chipContainer.innerHTML = `
            <div class='chip'>
                ${category.category_name}
                <button type='button' class='chip-delete' onclick='this.parentElement.remove()'>
                    <span class='icon'>close</span>
                </button>
                <input type='hidden' name='parent_id' value='${category.id}'>
            </div>
        `;
    }

    function searchCategories() {
        const searchInput = document.getElementById('searchInput').value;
        const url = new URL(location.href);
        url.searchParams.set('q', searchInput);
        url.searchParams.delete('p');
        location.href = url.toString();
    }



    document.addEventListener('DOMContentLoaded', function() {
        const parentcategorySearch = new SearchHandler({
            apiEndpoint: '/api/category/search',
            inputElement: document.querySelector('#parent_category input'),
            resultsContainer: document.querySelector('#parent_category .search-results'),
            itemsPerPage: 5,
            renderResultItem: (category) => {
                const element = document.createElement('div');
                element.classList.add('search-result');
                element.textContent = category.category_name;
                return element;
            },
            onSelect: (category) => {
                const input = document.querySelector('#parent_category input');
                input.value = category.category_name;
                createCategoryChip(category);
                document.querySelector('#parent_category .search-results').innerHTML = '';
            },
        })

        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchCategories();
            }
        });
    });
</script>
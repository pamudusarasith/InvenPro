<?php

use App\Services\RBACService;

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
            <button class="btn btn-primary">
                <span class="icon">add</span>
                Add Category
            </button>
        </div>

        <!-- Controls Section -->
        <div class="card glass controls">
            <div class="search-bar">
                <span class="icon">search</span>
                <input type="text" id="searchInput" placeholder="Search categories...">
            </div>
        </div>

        <!-- Categories Table -->
        <div class="table-container">
            <table class="data-table clickable" id="Categories-table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Parent Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($categories)) {
                        echo '<tr><td colspan="6" style="text-align: center;">No categories found</td></tr>';
                    } else {
                        foreach ($categories as $category):
                    ?>
                            <tr onclick="location.href = '/categories/<?= $category['id']; ?>'">
                                <td><?= htmlspecialchars($category['category_name']) ?></td>
                                <td><?= htmlspecialchars($category['description']) ?></td>
                                <td><?= $category['parent_category_name'] ? htmlspecialchars($category['parent_category_name']) : "N/A" ?></td>
                                <td><?= htmlspecialchars($category['Action']) ?></td>


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
</script>
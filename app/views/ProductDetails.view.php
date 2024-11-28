<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>
    <div class="content">
        <div class="row">
            <h1>Product Details</h1>
            <div id="prod-edit-btn" class="btn btn-secondary">
                <span class="material-symbols-rounded">edit</span>
                Edit
            </div>
        </div>
        <form id="prod-form" action="/product/update?id=<?= htmlspecialchars($product["id"]) ?>" method="post">
            <div class="column image-container">
                <img id="prod-image" src="<?= htmlspecialchars($product["image"]) ?>" alt="<?= htmlspecialchars($product["name"]) ?>">
                <label for="image-input" class="btn btn-secondary">
                    <span class="material-symbols-rounded">edit</span>
                    Edit
                </label>
                <input type="file" id="image-input" name="image" accept="image/*" disabled>
            </div>

            <div class="details-container">
                <label for="prod-id">Product ID</label>
                <input id="prod-id" type="text" name="id" value="<?= htmlspecialchars($product["id"]) ?>" disabled required>

                <label for="prod-name">Name</label>
                <input id="prod-name" type="text" name="name" value="<?= htmlspecialchars($product["name"]) ?>" disabled required>

                <label for="prod-desc">Description</label>
                <textarea id="prod-desc" rows="4" name="description" disabled><?= htmlspecialchars($product["description"]) ?></textarea>

                <label for="prod-unit">Measuring Unit</label>
                <select id="prod-unit" name="unit" disabled required>
                    <option value="items" <?= $product["measure_unit"] === "items" ? "selected" : "" ?>>Items</option>
                    <option value="kg" <?= $product["measure_unit"] === "kg" ? "selected" : "" ?>>Kilogram - kg</option>
                    <option value="l" <?= $product["measure_unit"] === "l" ? "selected" : "" ?>>Liters - l</option>
                </select>
                <label for="prod-category">Categories</label>
                <div id="category-search" class="search-container" style="display: none;">
                    <div class="search-bar">
                        <span class="material-symbols-rounded">search</span>
                        <input type="text" class="" placeholder="Search categories">
                    </div>
                </div>
                <div id="category-chips" class="chips">
                    <?php foreach ($product["categories"] as $category) : ?>
                        <div class="chip" data-id="<?= htmlspecialchars($category["id"]) ?>">
                            <?= htmlspecialchars($category["name"]) ?>
                            <span class="material-symbols-rounded" style="display: none;">close</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="row action-btns" style="display: none;">
                    <span class="loader" style="margin: 24px 12px 0px; font-size: 12px"></span>
                    <button type="button" class="btn btn-secondary reset-btn">Reset</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>

        <div class="column batches-container">
            <div class="row">
                <h1>Batch Details</h1>
            </div>
            <?php

            App\View::render('components/Table', [
                'headers' => ["BNo.", "Quantity", "Price", "MFD", "EXP"],
                'keys' => ["batch_no", "quantity", "price", "manufacture_date", "expiry_date"],
                'rows' => $product["batches"],
                'rowIdField' => "batch_no"
            ]);

            App\View::render('components/BatchEditForm');

            ?>
        </div>
    </div>
</div>

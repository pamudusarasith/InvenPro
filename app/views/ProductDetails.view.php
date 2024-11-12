<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>
    <div class="content">
        <div class="row">
            <h1>Product Details</h1>
            <div id="edit-btn" class="btn btn-secondary">
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

                <div class="row action-btns" style="display: none;">
                    <span class="loader" style="margin: 24px 12px 0px; font-size: 12px"></span>
                    <button type="button" class="btn btn-secondary reset-btn">Reset</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

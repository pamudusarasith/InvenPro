<div class="products-body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>
    <div class="content">
        <div class="row center">
            <h1>Products</h1>
            <div class="btn btn-primary jus-right">Create Product</div>
        </div>
        <div class="row search-bar">
            <span class="material-symbols-rounded">search</span>
            <input type="text" class="" placeholder="Search Products">
        </div>
        <h4>Filters</h4>
        <div id="filters" class="row">
            <div class="btn btn-secondary sp8">In Stock</div>
            <div class="btn btn-secondary sp8">Low Stock</div>
            <div class="btn btn-secondary sp8">Out of Stock</div>
        </div>
        <div class="column">
            <?php if (isset($categories)) {
                foreach ($categories as $category) : ?>
                    <button class="collapsible"><?= $category ?></button>
                    <div class="collapsible-content">
                        <?php if (isset($products)) {
                            App\View::render("components/ProductsTable", ["products" => $products[$category]]);
                        } ?>
                    </div>
                <?php endforeach;
            } ?>
        </div>
    </div>
</div>
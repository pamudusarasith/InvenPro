<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>
    <div class="content">
        <div class="row center">
            <h1>Discounts</h1>
            <div id="new-discount-btn" class="btn btn-primary jus-right">
                <span class="material-symbols-rounded">add</span>
                Discount
            </div>
            <?php if (isset($types)) {
                \App\View::render('components/DiscountForm', [
                    'types' => $types
                ]);
            } ?>
        </div>
        <div class="row search-bar">
            <span class="material-symbols-rounded">search</span>
            <input type="text" class="" placeholder="Search Discounts">
        </div>
        <h4>Filters</h4>
        <div id="filters" class="row">
            <div class="btn btn-secondary sp8">Active</div>
            <div class="btn btn-secondary sp8">Inactive</div>
        </div>
    </div>
</div>

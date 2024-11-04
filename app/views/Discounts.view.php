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
            <?php \App\View::render('components/DiscountForm'); ?>
        </div>
    </div>
</div>

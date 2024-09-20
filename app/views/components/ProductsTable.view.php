<div class="products-tbl">
    <div class="tbl-r">
        <span class="tbl-d">Product</span>
        <span class="tbl-d">Price</span>
        <span class="tbl-d">Quantity</span>
    </div>
    <?php if (isset($products)) {
        foreach ($products as $product) : ?>
            <div class="tbl-r">
                <span class="tbl-d"><?= $product["name"] ?></span>
                <span class="tbl-d"><?= $product["price"] ?></span>
                <span class="tbl-d"><?= $product["quantity"] ?></span>
            </div>
        <?php endforeach;
    } ?>
</div>
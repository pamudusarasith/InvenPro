<div class="container-column">
    <?php App\View::render("components/Navbar") ?>
    <div class="container-row">
        <?php App\View::render("components/Sidebar") ?>
        <div>
            <?php App\View::render("admin/dashboard") ?>
        </div>
    </div>
</div>
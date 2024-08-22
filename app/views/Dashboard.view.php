<div class="container-column">
    <?php App\View::render("components/Navbar") ?>
    <div class="container-row">
        <?php App\View::render("components/Sidebar") ?>
        <div>
            <h1>Dashboard</h1><br>
            <button onclick="window.location.href='/logout'">Logout</button>
        </div>
    </div>
</div>
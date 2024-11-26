<div class="sidebar">
    <div class="logo-container">
        <img class="logo" src="/images/logo-light.png" alt="logo">
    </div>
    <a href="/dashboard" class="sidebar-item <?= $_SERVER["REDIRECT_URL"] == "/dashboard" ? "selected" : "" ?>">
        <span class="material-symbols-rounded">dashboard</span>
        Dashboard
    </a>
    <a href="/products" class="sidebar-item <?= $_SERVER["REDIRECT_URL"] == "/products" ? "selected" : "" ?>">
        <span class="material-symbols-rounded">
            inventory_2
        </span>
        Products
    </a>
    <a href="/orders" class="sidebar-item <?= $_SERVER["REDIRECT_URL"] == "/orders" ? "selected" : "" ?>">
        <span class="material-symbols-rounded">
            orders
        </span>
        Orders
    </a>
    <a href="/discounts" class="sidebar-item <?= $_SERVER["REDIRECT_URL"] == "/discounts" ? "selected" : "" ?>">
        <span class="material-symbols-rounded">
            percent
        </span>
        Discounts
    </a>
</div>
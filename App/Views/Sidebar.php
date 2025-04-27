<aside class="sidebar">
    <nav class="sidebar-nav">
        <?php

        use App\Services\RBACService;

        $menuItems = [
            ['url' => '/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'permission' => 'view_dashboard'],
            ['url' => '/users', 'icon' => 'people', 'label' => 'Users', 'permission' => 'view_users'],
            ['url' => '/roles', 'icon' => 'admin_panel_settings', 'label' => 'Roles', 'permission' => 'view_roles'],
            ['url' => '/branches', 'icon' => 'business', 'label' => 'Branches', 'permission' => 'view_branches'],
            ['url' => '/categories', 'icon' => 'category', 'label' => 'Categories', 'permission' => 'view_categories'],
            ['url' => '/inventory', 'icon' => 'inventory_2', 'label' => 'Inventory', 'permission' => 'view_inventory'],
            ['url' => '/suppliers', 'icon' => 'local_shipping', 'label' => 'Suppliers', 'permission' => 'view_suppliers'],
            ['url' => '/orders', 'icon' => 'orders', 'label' => 'Purchase Orders', 'permission' => 'view_orders'],
            ['url' => '/discounts', 'icon' => 'percent', 'label' => 'Discounts', 'permission' => 'view_discounts'],
            ['url' => '/reports', 'icon' => 'bar_chart', 'label' => 'Reports', 'permission' => 'view_reports'],
        ];

        foreach ($menuItems as $item):
            if (!RBACService::hasPermission($item['permission'])) {
                continue;
            }
            $isActive = $_SERVER["REDIRECT_URL"] == $item['url'];
        ?>
            <a href="<?= $item['url'] ?>" class="nav-item <?= $isActive ? 'active' : '' ?>">
                <span class="nav-icon icon"><?= $item['icon'] ?></span>
                <span class="nav-label"><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>

<script>
    if (localStorage.getItem('sidebar_collapsed') === 'true') {
        document.querySelector('.sidebar').classList.add('collapsed');
    }
</script>

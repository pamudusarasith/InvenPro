<aside class="sidebar">
    <nav class="sidebar-nav">
        <?php

        use App\Services\RBACService;

        $menuItems = [
            ['url' => '/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'permission' => 'view_dashboard'],
            ['url' => '/users', 'icon' => 'people', 'label' => 'Users', 'permission' => 'manage_users'],
            ['url' => '/roles', 'icon' => 'admin_panel_settings', 'label' => 'Roles', 'permission' => 'manage_roles'],
            ['url' => '/categories', 'icon' => 'category', 'label' => 'Categories', 'permission' => 'manage_categories'],
            ['url' => '/inventory', 'icon' => 'inventory_2', 'label' => 'Inventory', 'permission' => 'manage_inventory'],
            ['url' => '/customers', 'icon' => 'people_alt', 'label' => 'Customers', 'permission' => 'manage_customers'],
            ['url' => '/suppliers', 'icon' => 'local_shipping', 'label' => 'Suppliers', 'permission' => 'manage_suppliers'],
            ['url' => '/orders', 'icon' => 'orders', 'label' => 'Orders', 'permission' => 'manage_orders'],
            ['url' => '/discounts', 'icon' => 'percent', 'label' => 'Discounts', 'permission' => 'manage_discounts'],
            ['url' => '/reports', 'icon' => 'bar_chart', 'label' => 'Reports', 'permission' => 'manage_reports'],
            ['url' => '/employees', 'icon' => 'badge', 'label' => 'Employees', 'permission' => 'manage_employees'],
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

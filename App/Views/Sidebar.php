<aside class="sidebar">
    <nav class="sidebar-nav">
        <?php

        use App\Services\RBACService;

        $menuItems = [
            ['url' => '/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'permission' => 'view_dashboard'],
            ['url' => '/users', 'icon' => 'people', 'label' => 'Users', 'permission' => 'user_view'],
            ['url' => '/roles', 'icon' => 'admin_panel_settings', 'label' => 'Roles', 'permission' => 'role_view'],
            ['url' => '/branches', 'icon' => 'business', 'label' => 'Branches', 'permission' => 'branch_view'],
            ['url' => '/categories', 'icon' => 'category', 'label' => 'Categories', 'permission' => 'category_view'],
            ['url' => '/inventory', 'icon' => 'inventory_2', 'label' => 'Inventory', 'permission' => 'product_view'],
            ['url' => '/suppliers', 'icon' => 'local_shipping', 'label' => 'Suppliers', 'permission' => 'supplier_view'],
            ['url' => '/orders', 'icon' => 'orders', 'label' => 'Purchase Orders', 'permission' => 'order_view'],
            ['url' => '/discounts', 'icon' => 'percent', 'label' => 'Discounts', 'permission' => 'discount_view'],
            ['url' => '/reports', 'icon' => 'bar_chart', 'label' => 'Reports', 'permission' => 'report_view'],
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

<!-- Sidebar.view.php -->
<div class="sidebar">
    <?php

    use App\Utils;

    $menuItems = [
        ['url' => '/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'permission' => 'manage_dashboard'],
        ['url' => '/users', 'icon' => 'people', 'label' => 'Users', 'permission' => 'manage_users'],
        ['url' => '/roles', 'icon' => 'people', 'label' => 'Roles', 'permission' => 'manage_roles'],
        ['url' => '/categories', 'icon' => 'category', 'label' => 'Categories', 'permission' => 'manage_categories'],
        ['url' => '/inventory', 'icon' => 'inventory_2', 'label' => 'Inventory', 'permission' => 'manage_inventory'],
        // ['url' => '/customers', 'icon' => 'people_alt', 'label' => 'Customers', 'permission' => 'manage_customers'],
        ['url' => '/suppliers', 'icon' => 'local_shipping', 'label' => 'Suppliers', 'permission' => 'manage_suppliers'],
        ['url' => '/orders', 'icon' => 'orders', 'label' => 'Orders', 'permission' => 'manage_orders'],
        ['url' => '/discounts', 'icon' => 'percent', 'label' => 'Discounts', 'permission' => 'manage_discounts'],
        ['url' => '/reports', 'icon' => 'bar_chart', 'label' => 'Reports', 'permission' => 'manage_reports'],
        // ['url' => '/employees', 'icon' => 'people', 'label' => 'Employees', 'permission' => 'manage_employees'],
    ];

    foreach ($menuItems as $item):
        if (!Utils::can($item['permission'])) {
            continue;
        }
        $isActive = $_SERVER["REDIRECT_URL"] == $item['url'];
    ?>
        <a href="<?= $item['url'] ?>" class="nav-item <?= $isActive ? 'active' : '' ?>">
            <span class="material-symbols-rounded"><?= $item['icon'] ?></span>
            <span class="nav-label"><?= $item['label'] ?></span>
        </a>
    <?php endforeach; ?>
</div>

<style>
    .sidebar {
        background: var(--glass-white);
        backdrop-filter: blur(10px);
        border-right: 1px solid var(--border-light);
        padding: 1.5rem 1rem;
        box-shadow: var(--shadow-lg);
    }

    .logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 48px;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-light);
    }

    .logo {
        height: 100%;
        width: auto;
        object-fit: contain;
    }

    .nav-item {
        display: flex;
        align-items: center;
        padding: 0.875rem 1rem;
        margin: 0.25rem 0;
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .nav-item:hover {
        background: var(--primary-50);
        color: var(--primary-600);
    }

    .nav-item.active {
        background: var(--primary-50);
        color: var(--primary-600);
        font-weight: 500;
        box-shadow: var(--shadow-sm);
    }

    .nav-item .material-symbols-rounded {
        margin-right: 1rem;
        font-size: 1.25rem;
    }

    .nav-label {
        font-size: 0.875rem;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .sidebar {
            display: none;
        }
    }
</style>
<!-- Sidebar.view.php -->
<div class="sidebar">
    <div class="logo-container">
        <img class="logo" src="/images/logo-dark.png" alt="logo">
    </div>
    <?php
    $menuItems = [
        ['url' => '/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['url' => '/categories', 'icon' => 'category', 'label' => 'Categories'],
        ['url' => '/products', 'icon' => 'inventory_2', 'label' => 'Inventory'],
        ['url' => '/suppliers', 'icon' => 'local_shipping', 'label' => 'Suppliers'],
        ['url' => '/orders', 'icon' => 'orders', 'label' => 'Orders'],
        ['url' => '/discounts', 'icon' => 'percent', 'label' => 'Discounts'],
        ['url' => '/reports', 'icon' => 'bar_chart', 'label' => 'Reports'],
        ['url' => '/employees', 'icon' => 'people', 'label' => 'Employees']
    ];

    foreach ($menuItems as $item):
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
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        width: 280px;
        background: var(--sidebar-bg);
        border-right: 1px solid var(--border-color);
        padding: 1.25rem 1rem;
        transition: width 0.3s ease;
    }

    .logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 48px;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
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
        border-radius: 0.75rem;
        transition: all 0.2s ease;
    }

    .nav-item:hover {
        background: var(--item-hover);
        color: var(--text-primary);
    }

    .nav-item.active {
        background: var(--item-active);
        color: var(--primary);
        font-weight: 500;
    }

    .nav-item .material-symbols-rounded {
        margin-right: 1rem;
        font-size: 1.25rem;
    }

    .nav-label {
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 72px;
        }

        .nav-item {
            padding: 0.75rem;
            justify-content: center;
        }

        .nav-item .material-symbols-rounded {
            margin: 0;
        }

        .nav-label {
            display: none;
        }
    }
</style>
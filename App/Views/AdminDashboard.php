<?php
use App\Services\RBACService;

// Validate data from controller to prevent undefined variable errors
$dashboardData = isset($dashboardData) && is_array($dashboardData) ? $dashboardData : [];

?>

<?php if (RBACService::hasPermission('view_dashboard')): ?>
<div class="body">

  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>
    <div class="main">
        <!-- Header Section -->
    <div class="card glass page-header">
      <div class="header-content">
        <h1><?=$dashboardData['greeting'] ?></h1>
        <p class="subtitle">Central control panel for tracking inventory, orders, and system performance in real time.</p>
      </div>
    </div>
    

    <!-- Main Content Section -->
    <div class="content">
      <div class="dashboard-grid">

        <!-- Sales Overview Card -->
        <div class="dashboard-card sales">
          <div class="card-header">
            <span class="icon">trending_up</span>
            <h3>Today's Sales</h3>
          </div>
          <div class="card-content">
            <h2><?= $dashboardData['sales']['value'] ?></h2>
            <p class="trend <?= $dashboardData['sales']['trendType'] ?>"><?= $dashboardData['sales']['trend'] ?> from yesterday</p>
          </div>
        </div>

        <!-- Low Stock Card -->
        <div class="dashboard-card low-stock warning">
          <div class="card-header">
            <span class="icon">inventory</span>
            <h3>Low Stock Items</h3>
          </div>
          <div class="card-content">
            <h2><?= $dashboardData['lowStock']['value'] ?></h2>
            <p>Items need reordering</p>
          </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="dashboard-card approved warning">
          <div class="card-header">
            <span class="icon">shopping_cart</span>
            <h3>Pending Orders</h3>
          </div>
          <div class="card-content">
            <h2><?= $dashboardData['pendingOrders']['value'] ?></h2>
            <p>Purchase orders to be approved</p>
          </div>
        </div>

        <!-- Approved Orders Card -->
        <div class="dashboard-card orders">
          <div class="card-header">
            <span class="icon">order_approve</span>
            <h3>Approved Orders</h3>
          </div>
          <div class="card-content">
            <h2><?= $dashboardData['approvedOrders']['value'] ?></h2>
            <p>Approved purchase orders</p>
          </div>
        </div>

        <!-- Staff Card -->
        <div class="dashboard-card staff">
          <div class="card-header">
            <span class="icon">group</span>
            <h3>System Users</h3>
          </div>
          <div class="card-content">
            <h2><?= $dashboardData['systemUsers']['value'] ?></h2>
            <p>Active employees</p>
          </div>
        </div>

        <!-- Revenue Card -->
        <div class="dashboard-card revenue">
          <div class="card-header">
            <span class="icon">payments</span>
            <h3>Monthly Revenue</h3>
          </div>
          <div class="card-content">
            <h2><?= $dashboardData['monthlyRevenue']['value'] ?></h2>
            <p>This month's earnings</p>
          </div>
        </div>

        <!-- Customer Card -->
        <div class="dashboard-card customers">
          <div class="card-header">
            <span class="icon">people</span>
            <h3>Active Customers</h3>
          </div>
          <div class="card-content">
            <h2><?= $dashboardData['activeCustomers']['value'] ?></h2>
            <p>Registered users</p>
          </div>
        </div>

        <!-- Returns Card -->
        <div class="dashboard-card returns warning">
          <div class="card-header">
            <span class="icon">assignment_return</span>
            <h3>Pending Returns</h3>
          </div>
          <div class="card-content">
            <h2><?= $dashboardData['pendingReturns']['value'] ?></h2>
            <p>Pending customer returns</p>
          </div>
        </div>

        <!-- Out of Stock Card -->
        <div class="dashboard-card out-of-stock warning">
          <div class="card-header">
            <span class="icon">block</span>
            <h3>Out of Stock</h3>
          </div>
          <div class="card-content">
            <h2><?= $dashboardData['outOfStock']['value'] ?></h2>
            <p>Items unavailable</p>
          </div>
        </div>

        <!-- Total Products Card -->
        <div class="dashboard-card products">
          <div class="card-header">
            <span class="icon">category</span>
            <h3>Total Products</h3>
          </div>
          <div class="card-content">
            <h2><?= $dashboardData['totalProducts']['value'] ?></h2>
            <p>Active products</p>
          </div>
        </div>

        <!-- Suppliers Card -->
        <div class="dashboard-card suppliers">
          <div class="card-header">
            <span class="icon">local_shipping</span>
            <h3>Active Suppliers</h3>
          </div>
          <div class="card-content">
            <h2><?= $dashboardData['activeSuppliers']['value'] ?></h2>
            <p>Registered suppliers</p>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
<?php endif; ?>
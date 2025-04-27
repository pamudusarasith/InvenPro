<?php

use App\Services\RBACService;

// Get current filters from request
$currentStatus = $_GET['status'] ?? '';
$fromDate = $_GET['from'] ?? '';
$toDate = $_GET['to'] ?? '';
$searchQuery = $_GET['q'] ?? '';
$paymentMethod = $_GET['payment'] ?? '';
$period = $_GET['period'] ?? 'monthly'; // Default to monthly view
$branchId = $_GET['branch'] ?? '';

// Check permissions
$canViewDetails = RBACService::hasPermission('view_sale_details');
$canPrintReceipt = RBACService::hasPermission('print_sale_receipt');
$canViewSalesAnalytics = RBACService::hasPermission('view_sales_analytics');

// Sample data for demonstration
$totalSales = 245;
$totalsToday = 5650.25;
$totalThisWeek = 32410.75;
$totalThisMonth = 125680.50;



// Helper function to get badge class based on status
function getStatusBadgeClass($status)
{
  switch ($status) {
    case 'completed':
      return 'success';
    case 'pending':
      return 'warning';
    case 'cancelled':
      return 'danger';
    case 'refunded':
      return 'accent';
    default:
      return 'secondary';
  }
}

// Sample sale items for demonstration
$saleItems = [
  // Sample sale item data
];
?>

<link rel="stylesheet" href="/css/pages/sales.css">

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <div class="card glass page-header">
      <div class="header-content">
        <h1>Sales Analytics</h1>
        <p class="subtitle">View sales insights and performance metrics</p>
      </div>
    </div>

    <!-- Sales statistics summary -->
    <div class="sales-stats">
      <div class="stat-card card glass">
        <div class="stat-header">
          <span class="icon" style="color: var(--primary-600);">receipt_long</span>
          <span class="stat-title">Total Sales</span>
        </div>
        <div class="stat-value"><?= number_format($totalSales) ?></div>
        <div class="stat-trend positive">
          <span class="icon">trending_up</span>
          <span>12.5% vs. last month</span>
        </div>
      </div>
      <div class="stat-card card glass">
        <div class="stat-header">
          <span class="icon" style="color: var(--success-600);">today</span>
          <span class="stat-title">Today's Sales</span>
        </div>
        <div class="stat-value">Rs. <?= number_format($totalsToday, 2) ?></div>
        <div class="stat-trend positive">
          <span class="icon">trending_up</span>
          <span>8.3% vs. yesterday</span>
        </div>
      </div>
      <div class="stat-card card glass">
        <div class="stat-header">
          <span class="icon" style="color: var(--accent-600);">date_range</span>
          <span class="stat-title">This Week</span>
        </div>
        <div class="stat-value">Rs. <?= number_format($totalThisWeek, 2) ?></div>
        <div class="stat-trend positive">
          <span class="icon">trending_up</span>
          <span>5.2% vs. last week</span>
        </div>
      </div>
      <div class="stat-card card glass">
        <div class="stat-header">
          <span class="icon" style="color: var(--warning-600);">calendar_month</span>
          <span class="stat-title">This Month</span>
        </div>
        <div class="stat-value">Rs. <?= number_format($totalThisMonth, 2) ?></div>
        <div class="stat-trend negative">
          <span class="icon">trending_down</span>
          <span>2.1% vs. last month</span>
        </div>
      </div>
    </div>

    <!-- Filters and period selection -->
    <div class="card glass controls">
      <div class="filters">
        <select id="periodFilter" onchange="changePeriod(this.value)">
          <option value="daily" <?= $period === 'daily' ? 'selected' : '' ?>>Daily</option>
          <option value="weekly" <?= $period === 'weekly' ? 'selected' : '' ?>>Weekly</option>
          <option value="monthly" <?= $period === 'monthly' ? 'selected' : '' ?>>Monthly</option>
          <option value="yearly" <?= $period === 'yearly' ? 'selected' : '' ?>>Yearly</option>
        </select>

        <div class="date-filter">
          <input type="date" class="date-input" id="fromDate" placeholder="From date"
            value="<?= $fromDate ?>" onchange="applyFilters()">
          <span class="icon">arrow_forward</span>
          <input type="date" class="date-input" id="toDate" placeholder="To date"
            value="<?= $toDate ?>" onchange="applyFilters()">
        </div>

        <select id="branchFilter" onchange="applyFilters()">
          <option value="">All Branches</option>
          <option value="1" <?= $branchId === '1' ? 'selected' : '' ?>>Main Branch</option>
          <option value="2" <?= $branchId === '2' ? 'selected' : '' ?>>Downtown Branch</option>
          <option value="3" <?= $branchId === '3' ? 'selected' : '' ?>>Uptown Branch</option>
        </select>
      </div>
    </div>

    <!-- Recent Sales Section -->
    <div class="recent-sales-section">
      <div class="section-header">
        <h2>Recent Sales</h2>
      </div>

      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Sale ID</th>
              <th>Date</th>
              <th>Customer</th>
              <th>Items</th>
              <th>Payment</th>
              <th>Total</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($sales)): ?>
              <tr>
                <td colspan="7" style="text-align: center;">No sales found</td>
              </tr>
            <?php else: ?>
              <?php
              // Only show 5 most recent sales
              $recentSales = array_slice($sales, 0, 5);
              foreach ($recentSales as $sale):
              ?>
                <tr class="sale-item" data-sale-id="<?= $sale['id'] ?>">
                  <td>#<?= str_pad($sale['id'], 5, '0', STR_PAD_LEFT) ?></td>
                  <td><?= date('M d, Y H:i', strtotime($sale['sale_date'])) ?></td>
                  <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                  <td><?= $sale['items'] ?></td>
                  <td>
                    <span class="payment-method">
                      <span class="icon"><?= $sale['payment_method'] === 'cash' ? 'payments' : 'credit_card' ?></span>
                      <?= ucfirst($sale['payment_method']) ?>
                    </span>
                  </td>
                  <td>Rs. <?= number_format($sale['total'], 2) ?></td>
                  <td>
                    <span class="badge <?= getStatusBadgeClass($sale['status']) ?>">
                      <?= ucfirst($sale['status']) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  // Global variable to store the current sale ID in the modal
  let currentModalSaleId = null;

  // Function to apply filters
  function applyFilters() {
    const fromDate = document.getElementById("fromDate").value;
    const toDate = document.getElementById("toDate").value;
    const branch = document.getElementById("branchFilter").value;
    const period = document.getElementById("periodFilter").value;

    const url = new URL(window.location.href);
    fromDate ? url.searchParams.set("from", fromDate) : url.searchParams.delete("from");
    toDate ? url.searchParams.set("to", toDate) : url.searchParams.delete("to");
    branch ? url.searchParams.set("branch", branch) : url.searchParams.delete("branch");
    url.searchParams.set("period", period);

    window.location.href = url.href;
  }

  // Function to change time period
  function changePeriod(period) {
    const url = new URL(window.location.href);
    url.searchParams.set("period", period);
    window.location.href = url.href;
  }
</script>
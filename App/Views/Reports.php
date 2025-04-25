<?php

use App\Services\RBACService;

// Simulate data coming from controller
$reportTypes = [
    'sales' => 'Sales Report',
    'inventory' => 'Inventory Report',
    'suppliers' => 'Supplier Performance',
    'orders' => 'Purchase Orders',
    'low_stock' => 'Low Stock Analysis',
    'customers' => 'Customer Analysis',
    'product_category' => 'Product Category Analysis',
    'inventory_value' => 'Inventory Valuation',
    'batch_expiry' => 'Batch Expiry Report'
];

$timePeriods = [
    'today' => 'Today',
    'yesterday' => 'Yesterday',
    'this_week' => 'This Week',
    'last_week' => 'Last Week',
    'this_month' => 'This Month',
    'last_month' => 'Last Month',
    'this_year' => 'This Year',
    'last_year' => 'Last Year',
    'custom' => 'Custom Range'
];

// Selected filters (simulated data)
$selectedReportType = $_GET['report_type'] ?? 'sales';
$selectedTimePeriod = $_GET['time_period'] ?? 'this_month';
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// KPI Metrics (simulated data)
$kpiMetrics = [
    [
        'label' => 'Total Sales',
        'value' => 'LKR 427,350.00',
        'trend' => '+12.5%',
        'trend_type' => 'positive',
        'icon' => 'payments',
        'type' => 'primary'
    ],
    [
        'label' => 'Total Orders',
        'value' => '142',
        'trend' => '+8.3%',
        'trend_type' => 'positive',
        'icon' => 'shopping_cart',
        'type' => 'success'
    ],
    [
        'label' => 'Average Order Value',
        'value' => 'LKR 3,009.51',
        'trend' => '+4.2%',
        'trend_type' => 'positive',
        'icon' => 'inventory',
        'type' => 'accent'
    ],
    [
        'label' => 'Profit Margin',
        'value' => '24.6%',
        'trend' => '-1.8%',
        'trend_type' => 'negative',
        'icon' => 'trending_up',
        'type' => 'warning'
    ]
];

// Top selling products (simulated data)
$topSellingProducts = [
    ['product_name' => 'Organic Ceylon Tea 250g', 'quantity' => 152, 'revenue' => 'LKR 45,600.00'],
    ['product_name' => 'Fresh Milk 1L', 'quantity' => 135, 'revenue' => 'LKR 33,750.00'],
    ['product_name' => 'Whole Wheat Bread 700g', 'quantity' => 124, 'revenue' => 'LKR 24,800.00'],
    ['product_name' => 'Free-Range Eggs (12pk)', 'quantity' => 103, 'revenue' => 'LKR 20,600.00'],
    ['product_name' => 'Basmati Rice 5kg', 'quantity' => 89, 'revenue' => 'LKR 44,500.00']
];

// Daily sales data (simulated data)
$dailySalesData = [
    ['date' => 'Apr 01', 'sales' => 12500],
    ['date' => 'Apr 05', 'sales' => 17800],
    ['date' => 'Apr 10', 'sales' => 14300],
    ['date' => 'Apr 15', 'sales' => 21000],
    ['date' => 'Apr 20', 'sales' => 15600],
    ['date' => 'Apr 25', 'sales' => 19200],
];

// Stock status (simulated data)
$stockStatus = [
    'in_stock' => 268,
    'low_stock' => 43,
    'out_of_stock' => 17,
    'total_value' => 'LKR 2,432,750.00'
];

// Recent purchase orders (simulated data)
$recentPurchaseOrders = [
    ['reference' => 'PO-20250420-12345', 'supplier' => 'Ceylon Tea Suppliers', 'date' => '2025-04-20', 'status' => 'completed', 'total' => 'LKR 125,000.00'],
    ['reference' => 'PO-20250418-12344', 'supplier' => 'Fresh Farm Dairies', 'date' => '2025-04-18', 'status' => 'open', 'total' => 'LKR 87,500.00'],
    ['reference' => 'PO-20250415-12343', 'supplier' => 'Organic Grains Ltd', 'date' => '2025-04-15', 'status' => 'completed', 'total' => 'LKR 103,750.00'],
    ['reference' => 'PO-20250410-12342', 'supplier' => 'Island Rice Mills', 'date' => '2025-04-10', 'status' => 'completed', 'total' => 'LKR 145,000.00'],
    ['reference' => 'PO-20250405-12341', 'supplier' => 'Global Spice Traders', 'date' => '2025-04-05', 'status' => 'canceled', 'total' => 'LKR 76,250.00']
];

// Additional data for new reports
$categoryData = [
    ['name' => 'Beverages', 'count' => 42],
    ['name' => 'Dairy', 'count' => 38],
    ['name' => 'Bakery', 'count' => 24],
    ['name' => 'Grains', 'count' => 19],
    ['name' => 'Spices', 'count' => 31]
];

$supplierPerformance = [
    ['name' => 'Ceylon Tea Suppliers', 'on_time' => 92, 'quality' => 88],
    ['name' => 'Fresh Farm Dairies', 'on_time' => 85, 'quality' => 95],
    ['name' => 'Organic Grains Ltd', 'on_time' => 78, 'quality' => 92],
    ['name' => 'Island Rice Mills', 'on_time' => 90, 'quality' => 85],
    ['name' => 'Global Spice Traders', 'on_time' => 72, 'quality' => 90]
];

$categoryRevenueData = [
    ['name' => 'Beverages', 'revenue' => 145000],
    ['name' => 'Dairy', 'revenue' => 98000],
    ['name' => 'Bakery', 'revenue' => 76500],
    ['name' => 'Grains', 'revenue' => 68000],
    ['name' => 'Spices', 'revenue' => 39850]
];

$expiringBatches = [
    ['product_name' => 'Fresh Milk 1L', 'batch_code' => 'FM2504001', 'expiry_date' => '2025-05-15', 'quantity' => 45, 'days_left' => 20],
    ['product_name' => 'Yogurt 500g', 'batch_code' => 'YG2504002', 'expiry_date' => '2025-05-10', 'quantity' => 36, 'days_left' => 15],
    ['product_name' => 'Cottage Cheese 250g', 'batch_code' => 'CC2504003', 'expiry_date' => '2025-05-07', 'quantity' => 24, 'days_left' => 12],
    ['product_name' => 'Whole Wheat Bread 700g', 'batch_code' => 'WWB2504001', 'expiry_date' => '2025-05-03', 'quantity' => 18, 'days_left' => 8],
    ['product_name' => 'Organic Butter 200g', 'batch_code' => 'OB2504001', 'expiry_date' => '2025-05-08', 'quantity' => 12, 'days_left' => 13]
];

$customerAnalytics = [
    'loyal_customers' => 87,
    'new_customers' => 34,
    'avg_purchase_frequency' => 2.4,
    'avg_order_value' => 'LKR 3,010',
    'top_customers' => [
        ['name' => 'Hotel Seaside', 'total_purchases' => 'LKR 52,350', 'total_orders' => 12],
        ['name' => 'Green Leaf Restaurant', 'total_purchases' => 'LKR 48,750', 'total_orders' => 15],
        ['name' => 'City Supermarket', 'total_purchases' => 'LKR 43,200', 'total_orders' => 8],
        ['name' => 'Royal Bakery', 'total_purchases' => 'LKR 36,500', 'total_orders' => 10],
        ['name' => 'Wellness Cafe', 'total_purchases' => 'LKR 29,800', 'total_orders' => 9]
    ]
];

$lowStockItems = [
    ['product_name' => 'Basmati Rice 5kg', 'current_stock' => 8, 'reorder_level' => 15, 'days_to_out' => 6],
    ['product_name' => 'Ceylon Black Tea 250g', 'current_stock' => 12, 'reorder_level' => 20, 'days_to_out' => 5],
    ['product_name' => 'Coconut Oil 1L', 'current_stock' => 6, 'reorder_level' => 12, 'days_to_out' => 4],
    ['product_name' => 'Brown Sugar 1kg', 'current_stock' => 10, 'reorder_level' => 18, 'days_to_out' => 7],
    ['product_name' => 'Curry Powder 200g', 'current_stock' => 5, 'reorder_level' => 15, 'days_to_out' => 3]
];

// Monthly sales by day (simulated data for line chart)
$monthlySalesData = [];
for ($i = 1; $i <= 30; $i++) {
    $date = sprintf('Apr %02d', $i);
    $sales = rand(8000, 22000);
    $monthlySalesData[] = ['date' => $date, 'sales' => $sales];
}

?>

<div class="body">
    <?php App\Core\View::render("Navbar") ?>
    <?php App\Core\View::render("Sidebar") ?>

    <div class="main">
        <!-- Header Section -->
        <div class="card glass page-header">
            <div class="header-content">
                <h1>Reports & Analytics</h1>
                <p class="subtitle">View detailed reports and analytics for your business</p>
            </div>
        </div>

        <div class="reports-container">
            <!-- Filters Section -->
            <div class="card glass">
                <div class="content">
                    <div class="report-filters">
                        <div class="form-field">
                            <label for="report-type">Report Type</label>
                            <select id="report-type" name="report_type">
                                <?php foreach ($reportTypes as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= $selectedReportType === $value ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-divider"></div>

                        <div class="form-field">
                            <label for="time-period">Time Period</label>
                            <select id="time-period" name="time_period" onchange="toggleDateRange(this.value)">
                                <?php foreach ($timePeriods as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= $selectedTimePeriod === $value ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-divider"></div>

                        <div id="date-range-container" class="date-range" style="<?= $selectedTimePeriod === 'custom' ? '' : 'display: none;' ?>">
                            <div class="form-field">
                                <label for="start-date">Start Date</label>
                                <input type="date" id="start-date" name="start_date" value="<?= $startDate ?>">
                            </div>
                            <span>to</span>
                            <div class="form-field">
                                <label for="end-date">End Date</label>
                                <input type="date" id="end-date" name="end_date" value="<?= $endDate ?>">
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary" onclick="generateReport()">
                            <span class="icon">analytics</span>
                            Generate Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- KPI Metrics -->
            <div class="kpi-grid">
                <?php foreach ($kpiMetrics as $metric): ?>
                    <div class="kpi-card <?= $metric['type'] ?>">
                        <div class="kpi-header">
                            <span class="icon"><?= $metric['icon'] ?></span>
                            <h4 class="kpi-label"><?= $metric['label'] ?></h4>
                        </div>
                        <div class="kpi-value"><?= $metric['value'] ?></div>
                        <div class="kpi-trend <?= $metric['trend_type'] ?>">
                            <span class="icon"><?= $metric['trend_type'] === 'positive' ? 'trending_up' : 'trending_down' ?></span>
                            <span><?= $metric['trend'] ?> from last period</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Report Sections -->
            <div class="report-sections">
                <!-- Sales Overview -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Sales Overview</h3>
                        <div class="dropdown">
                            <button class="icon-btn dropdown-trigger">
                                <span class="icon">more_vert</span>
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" onclick="exportReport('sales', 'pdf')">
                                    <span class="icon">picture_as_pdf</span>
                                    Export as PDF
                                </button>
                                <button class="dropdown-item" onclick="exportReport('sales', 'excel')">
                                    <span class="icon">table_view</span>
                                    Export as Excel
                                </button>
                                <button class="dropdown-item" onclick="printReport('sales')">
                                    <span class="icon">print</span>
                                    Print Report
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="report-card-body">
                        <div class="report-summary">
                            <div class="summary-item">
                                <div class="summary-value">142</div>
                                <div class="summary-label">Total Orders</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value">LKR 427,350</div>
                                <div class="summary-label">Total Revenue</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value">LKR 3,010</div>
                                <div class="summary-label">Avg. Order Value</div>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Selling Products -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Top Selling Products</h3>
                        <div class="dropdown">
                            <button class="icon-btn dropdown-trigger">
                                <span class="icon">more_vert</span>
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" onclick="exportReport('products', 'pdf')">
                                    <span class="icon">picture_as_pdf</span>
                                    Export as PDF
                                </button>
                                <button class="dropdown-item" onclick="exportReport('products', 'excel')">
                                    <span class="icon">table_view</span>
                                    Export as Excel
                                </button>
                                <button class="dropdown-item" onclick="printReport('products')">
                                    <span class="icon">print</span>
                                    Print Report
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="report-card-body">
                        <div class="report-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topSellingProducts as $product): ?>
                                        <tr>
                                            <td><?= $product['product_name'] ?></td>
                                            <td><?= $product['quantity'] ?></td>
                                            <td><?= $product['revenue'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="report-actions">
                            <button class="btn btn-secondary" onclick="viewAllProducts()">
                                <span class="icon">visibility</span>
                                View All Products
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Inventory Status -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Inventory Status</h3>
                        <div class="dropdown">
                            <button class="icon-btn dropdown-trigger">
                                <span class="icon">more_vert</span>
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" onclick="exportReport('inventory', 'pdf')">
                                    <span class="icon">picture_as_pdf</span>
                                    Export as PDF
                                </button>
                                <button class="dropdown-item" onclick="exportReport('inventory', 'excel')">
                                    <span class="icon">table_view</span>
                                    Export as Excel
                                </button>
                                <button class="dropdown-item" onclick="printReport('inventory')">
                                    <span class="icon">print</span>
                                    Print Report
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="report-card-body">
                        <div class="report-summary">
                            <div class="summary-item">
                                <div class="summary-value"><?= $stockStatus['in_stock'] ?></div>
                                <div class="summary-label">In Stock</div>
                                <div class="summary-trend positive">
                                    <span class="icon">check_circle</span>
                                </div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value"><?= $stockStatus['low_stock'] ?></div>
                                <div class="summary-label">Low Stock</div>
                                <div class="summary-trend">
                                    <span class="icon text-warning">warning</span>
                                </div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value"><?= $stockStatus['out_of_stock'] ?></div>
                                <div class="summary-label">Out of Stock</div>
                                <div class="summary-trend negative">
                                    <span class="icon">error</span>
                                </div>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                        <div class="report-actions">
                            <button class="btn btn-secondary" onclick="viewInventory()">
                                <span class="icon">visibility</span>
                                View Inventory
                            </button>
                            <div class="export-options">
                                <button class="btn btn-secondary" onclick="exportInventory('excel')">
                                    <span class="icon">download</span>
                                    Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Purchase Orders -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Recent Purchase Orders</h3>
                        <div class="dropdown">
                            <button class="icon-btn dropdown-trigger">
                                <span class="icon">more_vert</span>
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" onclick="exportReport('orders', 'pdf')">
                                    <span class="icon">picture_as_pdf</span>
                                    Export as PDF
                                </button>
                                <button class="dropdown-item" onclick="exportReport('orders', 'excel')">
                                    <span class="icon">table_view</span>
                                    Export as Excel
                                </button>
                                <button class="dropdown-item" onclick="printReport('orders')">
                                    <span class="icon">print</span>
                                    Print Report
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="report-card-body">
                        <div class="report-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Supplier</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentPurchaseOrders as $order): ?>
                                        <tr>
                                            <td><?= $order['reference'] ?></td>
                                            <td><?= $order['supplier'] ?></td>
                                            <td><?= date('M d, Y', strtotime($order['date'])) ?></td>
                                            <td>
                                                <span class="badge <?= $order['status'] === 'completed' ? 'success' : ($order['status'] === 'open' ? 'accent' : 'danger') ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= $order['total'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="report-actions">
                            <button class="btn btn-secondary" onclick="viewAllOrders()">
                                <span class="icon">visibility</span>
                                View All Orders
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Category Analysis -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Product Category Analysis</h3>
                        <div class="dropdown">
                            <button class="icon-btn dropdown-trigger">
                                <span class="icon">more_vert</span>
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" onclick="exportReport('category', 'pdf')">
                                    <span class="icon">picture_as_pdf</span>
                                    Export as PDF
                                </button>
                                <button class="dropdown-item" onclick="exportReport('category', 'excel')">
                                    <span class="icon">table_view</span>
                                    Export as Excel
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="report-card-body">
                        <div class="chart-container">
                            <canvas id="categoryChart"></canvas>
                        </div>
                        <div class="report-summary mt-md">
                            <div class="summary-item">
                                <div class="summary-value">5</div>
                                <div class="summary-label">Categories</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value">154</div>
                                <div class="summary-label">Total Products</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value">30.8</div>
                                <div class="summary-label">Avg Products/Category</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales by Category -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Revenue by Category</h3>
                        <div class="dropdown">
                            <button class="icon-btn dropdown-trigger">
                                <span class="icon">more_vert</span>
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" onclick="exportReport('category_sales', 'pdf')">
                                    <span class="icon">picture_as_pdf</span>
                                    Export as PDF
                                </button>
                                <button class="dropdown-item" onclick="exportReport('category_sales', 'excel')">
                                    <span class="icon">table_view</span>
                                    Export as Excel
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="report-card-body">
                        <div class="chart-container">
                            <canvas id="salesByCategoryChart"></canvas>
                        </div>
                        <div class="report-summary mt-md">
                            <div class="summary-item">
                                <div class="summary-value">LKR 427,350</div>
                                <div class="summary-label">Total Revenue</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value">LKR 85,470</div>
                                <div class="summary-label">Avg Revenue/Category</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supplier Performance -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Supplier Performance Metrics</h3>
                        <div class="dropdown">
                            <button class="icon-btn dropdown-trigger">
                                <span class="icon">more_vert</span>
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" onclick="exportReport('supplier_performance', 'pdf')">
                                    <span class="icon">picture_as_pdf</span>
                                    Export as PDF
                                </button>
                                <button class="dropdown-item" onclick="exportReport('supplier_performance', 'excel')">
                                    <span class="icon">table_view</span>
                                    Export as Excel
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="report-card-body">
                        <div class="chart-container">
                            <canvas id="supplierChart"></canvas>
                        </div>
                        <div class="report-summary mt-md">
                            <div class="summary-item">
                                <div class="summary-value">83.4%</div>
                                <div class="summary-label">Avg On-Time Delivery</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value">90%</div>
                                <div class="summary-label">Avg Quality Rating</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Batch Expiry Report -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Upcoming Batch Expiry</h3>
                        <div class="dropdown">
                            <button class="icon-btn dropdown-trigger">
                                <span class="icon">more_vert</span>
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" onclick="exportReport('batch_expiry', 'pdf')">
                                    <span class="icon">picture_as_pdf</span>
                                    Export as PDF
                                </button>
                                <button class="dropdown-item" onclick="exportReport('batch_expiry', 'excel')">
                                    <span class="icon">table_view</span>
                                    Export as Excel
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="report-card-body">
                        <div class="report-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Batch Code</th>
                                        <th>Expiry Date</th>
                                        <th>Quantity</th>
                                        <th>Days Left</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expiringBatches as $batch): ?>
                                        <tr>
                                            <td><?= $batch['product_name'] ?></td>
                                            <td><?= $batch['batch_code'] ?></td>
                                            <td><?= date('M d, Y', strtotime($batch['expiry_date'])) ?></td>
                                            <td><?= $batch['quantity'] ?></td>
                                            <td>
                                                <span class="badge <?= $batch['days_left'] <= 10 ? 'danger' : ($batch['days_left'] <= 15 ? 'warning' : 'success') ?>">
                                                    <?= $batch['days_left'] ?> days
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="report-actions">
                            <button class="btn btn-secondary" onclick="markForDiscount()">
                                <span class="icon">sell</span>
                                Mark for Discount
                            </button>
                            <div class="export-options">
                                <button class="btn btn-secondary" onclick="viewAllExpiryAlerts()">
                                    <span class="icon">notification_important</span>
                                    View All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Products -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Low Stock Products</h3>
                        <div class="dropdown">
                            <button class="icon-btn dropdown-trigger">
                                <span class="icon">more_vert</span>
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" onclick="exportReport('low_stock', 'pdf')">
                                    <span class="icon">picture_as_pdf</span>
                                    Export as PDF
                                </button>
                                <button class="dropdown-item" onclick="exportReport('low_stock', 'excel')">
                                    <span class="icon">table_view</span>
                                    Export as Excel
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="report-card-body">
                        <div class="report-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Reorder Level</th>
                                        <th>Days to Out</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lowStockItems as $item): ?>
                                        <tr>
                                            <td><?= $item['product_name'] ?></td>
                                            <td><?= $item['current_stock'] ?></td>
                                            <td><?= $item['reorder_level'] ?></td>
                                            <td>
                                                <span class="badge <?= $item['days_to_out'] <= 3 ? 'danger' : ($item['days_to_out'] <= 7 ? 'warning' : 'success') ?>">
                                                    <?= $item['days_to_out'] ?> days
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm" onclick="createPurchaseOrder('<?= $item['product_name'] ?>')">
                                                    Reorder
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="report-actions">
                            <button class="btn btn-secondary" onclick="reorderAllLowStock()">
                                <span class="icon">autorenew</span>
                                Reorder All
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle custom date range based on selected time period
    function toggleDateRange(value) {
        const dateRangeContainer = document.getElementById('date-range-container');
        if (value === 'custom') {
            dateRangeContainer.style.display = 'flex';
        } else {
            dateRangeContainer.style.display = 'none';
        }
    }

    // Generate report based on selected filters
    function generateReport() {
        const reportType = document.getElementById('report-type').value;
        const timePeriod = document.getElementById('time-period').value;
        let url = `/reports?report_type=${reportType}&time_period=${timePeriod}`;
        
        if (timePeriod === 'custom') {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            url += `&start_date=${startDate}&end_date=${endDate}`;
        }
        
        window.location.href = url;
    }

    // Export report functions
    function exportReport(reportType, format) {
        // In a real implementation, this would trigger a download
        console.log(`Exporting ${reportType} report as ${format}`);
        alert(`${reportType} report will be exported as ${format}`);
    }

    // Print report function
    function printReport(reportType) {
        // In a real implementation, this would open the print dialog
        console.log(`Printing ${reportType} report`);
        alert(`${reportType} report will be printed`);
    }

    // View all products function
    function viewAllProducts() {
        window.location.href = "/inventory";
    }

    // View inventory function
    function viewInventory() {
        window.location.href = "/inventory";
    }

    // View all orders function
    function viewAllOrders() {
        window.location.href = "/purchase-orders";
    }

    // Export inventory function
    function exportInventory(format) {
        // In a real implementation, this would trigger a download
        console.log(`Exporting inventory report as ${format}`);
        alert(`Inventory report will be exported as ${format}`);
    }

    // Mark expiring products for discount
    function markForDiscount() {
        console.log('Marking expiring products for discount');
        alert('Expiring products have been marked for discount');
    }

    // View all expiry alerts
    function viewAllExpiryAlerts() {
        window.location.href = "/inventory?filter=expiring";
    }

    // Create purchase order for a specific product
    function createPurchaseOrder(productName) {
        console.log(`Creating purchase order for ${productName}`);
        alert(`Purchase order will be created for ${productName}`);
    }

    // Reorder all low stock items
    function reorderAllLowStock() {
        console.log('Reordering all low stock items');
        alert('Purchase orders have been created for all low stock items');
    }

    // Add Chart.js for interactive visualizations
    document.addEventListener('DOMContentLoaded', function() {
        // Only load charts if Chart.js is available
        if (typeof Chart !== 'undefined') {
            initializeCharts();
        } else {
            // If Chart.js isn't loaded, load it dynamically
            loadChartJS();
        }
    });

    // Function to dynamically load Chart.js
    function loadChartJS() {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = function() {
            console.log('Chart.js loaded successfully');
            initializeCharts();
        };
        script.onerror = function() {
            console.error('Failed to load Chart.js');
            setupChartPlaceholders();
        };
        document.head.appendChild(script);
    }

    // Initialize all charts on the page
    function initializeCharts() {
        // Sales trend chart
        const salesCtx = document.getElementById('salesTrendChart')?.getContext('2d');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_column($dailySalesData, 'date')) ?>,
                    datasets: [{
                        label: 'Daily Sales',
                        data: <?= json_encode(array_column($dailySalesData, 'sales')) ?>,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'LKR ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Inventory distribution chart
        const inventoryCtx = document.getElementById('inventoryChart')?.getContext('2d');
        if (inventoryCtx) {
            new Chart(inventoryCtx, {
                type: 'doughnut',
                data: {
                    labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                    datasets: [{
                        data: [
                            <?= $stockStatus['in_stock'] ?>, 
                            <?= $stockStatus['low_stock'] ?>, 
                            <?= $stockStatus['out_of_stock'] ?>
                        ],
                        backgroundColor: [
                            'rgba(22, 163, 74, 0.7)',  // success
                            'rgba(217, 119, 6, 0.7)',  // warning
                            'rgba(220, 38, 38, 0.7)'   // danger
                        ],
                        borderColor: [
                            'rgba(22, 163, 74, 1)',
                            'rgba(217, 119, 6, 1)',
                            'rgba(220, 38, 38, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Category distribution chart
        const categoryCtx = document.getElementById('categoryChart')?.getContext('2d');
        if (categoryCtx) {
            new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($categoryData, 'name')) ?>,
                    datasets: [{
                        label: 'Products',
                        data: <?= json_encode(array_column($categoryData, 'count')) ?>,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        // Supplier performance chart
        const supplierCtx = document.getElementById('supplierChart')?.getContext('2d');
        if (supplierCtx) {
            new Chart(supplierCtx, {
                type: 'radar',
                data: {
                    labels: <?= json_encode(array_column($supplierPerformance, 'name')) ?>,
                    datasets: [{
                        label: 'On-Time Delivery',
                        data: <?= json_encode(array_column($supplierPerformance, 'on_time')) ?>,
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgb(59, 130, 246)',
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(59, 130, 246)'
                    }, {
                        label: 'Quality Rating',
                        data: <?= json_encode(array_column($supplierPerformance, 'quality')) ?>,
                        backgroundColor: 'rgba(168, 85, 247, 0.2)',
                        borderColor: 'rgb(168, 85, 247)',
                        pointBackgroundColor: 'rgb(168, 85, 247)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(168, 85, 247)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            min: 0,
                            max: 100,
                            ticks: {
                                stepSize: 20
                            }
                        }
                    }
                }
            });
        }
        
        // Sales by category chart
        const salesByCategoryCtx = document.getElementById('salesByCategoryChart')?.getContext('2d');
        if (salesByCategoryCtx) {
            new Chart(salesByCategoryCtx, {
                type: 'pie',
                data: {
                    labels: <?= json_encode(array_column($categoryRevenueData, 'name')) ?>,
                    datasets: [{
                        data: <?= json_encode(array_column($categoryRevenueData, 'revenue')) ?>,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.6)',
                            'rgba(168, 85, 247, 0.6)',
                            'rgba(234, 88, 12, 0.6)',
                            'rgba(22, 163, 74, 0.6)',
                            'rgba(217, 119, 6, 0.6)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: LKR ${value.toLocaleString()}`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Function to set up interactive chart placeholders if Chart.js isn't available
    function setupChartPlaceholders() {
        const placeholders = document.querySelectorAll('.chart-placeholder');
        placeholders.forEach(placeholder => {
            placeholder.addEventListener('click', function() {
                alert('Charts would be loaded here in the final implementation');
            });
        });
    }
</script>
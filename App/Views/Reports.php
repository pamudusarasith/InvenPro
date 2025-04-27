<?php
use App\Services\RBACService;
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
                    <select id="report-type" name="report_type" onchange="updateTimePeriodOptions(this.value)">
                        <?php foreach ($reportTypes as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $selectedReportType === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="form-field">
                    <label for="time-period">Time Period</label>
                    <select id="time-period" name="time_period" onchange="toggleDateRange(this.value)">
                        <?php if ($selectedReportType === 'batch_expiry'): ?>
                            <?php foreach ($expiryTimePeriods as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $selectedTimePeriod === $value ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php foreach ($timePeriods as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $selectedTimePeriod === $value ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                

<script>
    // JavaScript to update time period options based on report type
    function updateTimePeriodOptions(reportType) {
        const timePeriodSelect = document.getElementById('time-period');
        const currentValue = timePeriodSelect.value; // Store current selection
        timePeriodSelect.innerHTML = ''; // Clear existing options

        let options = [];
        if (reportType === 'batch_expiry') {
            options = [
                { value: 'next_week', label: 'Next Week' },
                { value: 'next_month', label: 'Next Month' }
            ];
        } else {
            options = [
                { value: 'today', label: 'Today' },
                { value: 'yesterday', label: 'Yesterday' },
                { value: 'this_week', label: 'This Week' },
                { value: 'last_week', label: 'Last Week' },
                { value: 'this_month', label: 'This Month' },
                { value: 'last_month', label: 'Last Month' },
                { value: 'this_year', label: 'This Year' },
                { value: 'last_year', label: 'Last Year' },
                { value: 'custom', label: 'Custom Range' }
            ];
        }

        // Populate the dropdown with new options
        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option.value;
            opt.textContent = option.label;
            timePeriodSelect.appendChild(opt);
        });

        // Restore the previous selection if it exists in the new options
        const validOption = options.find(opt => opt.value === currentValue);
        timePeriodSelect.value = validOption ? currentValue : options[0].value;

        // Update date range visibility
        toggleDateRange(timePeriodSelect.value);
    }

    // JavaScript to toggle date range visibility
    function toggleDateRange(timePeriod) {
        const dateRangeContainer = document.getElementById('date-range-container');
        dateRangeContainer.style.display = timePeriod === 'custom' ? 'block' : 'none';
    }
</script>


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

                            <button type="button" class="btn btn-primary margin-report" onclick="generateReport()">
                                <span class="icon">analytics</span>
                                Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Metrics -->
            <?php if ($selectedReportType === 'sales' || $selectedReportType === '' ): ?>
            <div class="kpi-grid">
                <?php foreach ($kpiMetrics as $metric): ?>
                    <div class="kpi-card card <?= $metric['type'] ?>">
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
            <?php endif; ?>

            <!-- Report Sections -->
            <div class="chart-sections">
                <!-- Sales Overview -->
                <?php if ($selectedReportType === 'sales' || $selectedReportType === '' ): ?>
                <div class="report-card" id="chart">
                    <div class="report-card-header">
                        <h3>Sales Overview</h3>
                    </div>
                    <div class="report-card-body">
                        <div class="report-summary">
                            <div class="summary-item">
                                <div class="summary-value"><?= $salesStats['total_orders'] ?></div>
                                <div class="summary-label">Total Orders</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value"><?= $salesStats['total_revenue'] ?></div>
                                <div class="summary-label">Total Revenue</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value"><?= $salesStats['avg_order_value'] ?></div>
                                <div class="summary-label">Avg. Order Value</div>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>
                </div>   
                <?php endif; ?>
                <?php if ($selectedReportType === 'inventory'): ?>
                <!-- Inventory Status -->
                <div class="report-card" id="chart">
                    <div class="report-card-header">
                        <h3>Inventory Status</h3>
    
                    </div>
                    <div class="report-card-body">
                        <div class="report-summary">
                            <div class="summary-item">
                                <div class="summary-value"><?= $inventoryStats['in_stock'] ?></div>
                                <div class="summary-label">In Stock</div>
                                <div class="summary-trend positive">
                                    <span class="icon">check_circle</span>
                                </div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value"><?= $inventoryStats['low_stock'] ?></div>
                                <div class="summary-label">Low Stock</div>
                                <div class="summary-trend">
                                    <span class="icon text-warning">warning</span>
                                </div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-value"><?= $inventoryStats['out_of_stock'] ?></div>
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
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($selectedReportType === 'inventory'): ?>
                <!-- Category Analysis -->
                <div class="report-card" id="chart">
                    <div class="report-card-header">
                        <h3>Product Category Analysis</h3>
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
                <?php endif; ?>

                <?php if ($selectedReportType === 'suppliers'): ?>
                <!-- Supplier Performance -->
                <!--
                <div class="report-card" id="chart">
                    <div class="report-card-header">
                        <h3>Supplier Performance Metrics</h3>
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
                -->
                <?php endif; ?>

                <?php if ($selectedReportType === 'sales' || $selectedReportType === '' ): ?>
                <!-- Sales by Category -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Revenue by Category</h3>
                    </div>
                    <div class="report-card-body">
                        <div class="chart-container">
                            <canvas id="salesByCategoryChart"></canvas>
                        </div>
                        <div class="report-summary mt-md">
                            <div class="summary-item">
                                <div class="summary-value">LKR 7489.30</div>
                                <div class="summary-label">Avg Revenue/Category</div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($selectedReportType === 'sales' || $selectedReportType === '' ): ?>
                <!-- Top Product Combinations -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Top Product Combinations</h3>
                    </div>
                    <div class="report-card-body">
                        <div class="report-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product1</th>
                                        <th>Product2</th>
                                        <th>Frequency</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($basketAnalysisResults as $combination): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($combination['product1']) ?></td>
                                            <td><?= htmlspecialchars($combination['product2']) ?></td>
                                            <td><?= $combination['frequency'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($basketAnalysisResults)): ?>
                                        <tr>
                                            <td colspan="3" style="text-align: center;">No product combinations found for the selected time period.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="table-sections">
                <?php if ($selectedReportType === 'sales' || $selectedReportType === '' ): ?>
                <!-- Top Selling Products -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Top Selling Products</h3>
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
                                    <?php if (empty($topSellingProducts)): ?>
                                        <tr>
                                            <td colspan="3" style="text-align: center;">No top selling products found for the selected time period.</td>
                                        </tr>
                                    <?php endif; ?>
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
                <?php endif; ?>

                <?php if ($selectedReportType === 'orders'): ?>
                <!-- Recent Purchase Orders -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Recent Purchase Orders</h3>
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
                <?php endif; ?>

                <?php if ($selectedReportType === 'batch_expiry'): ?>
                <!-- Batch Expiry Report -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Upcoming Batch Expiry</h3>
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
                <?php endif; ?>

                <?php if ($selectedReportType === 'inventory'): ?>
                <!-- Low Stock Products -->
                <div class="report-card">
                    <div class="report-card-header">
                        <h3>Low Stock Products</h3>
                    </div>
                    <div class="report-card-body">
                        <div class="report-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Reorder Level</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lowStockItems as $item): ?>
                                        <tr>
                                            <td><?= $item['product_name'] ?></td>
                                            <td><?= $item['is_int'] ? (int)$item['current_stock'] . ' ' . $item['unit'] : number_format((float)$item['current_stock'], 2) . ' ' . $item['unit'] ?></td>
                                            <td><?= $item['is_int'] ? (int)$item['reorder_level'] . ' ' . $item['unit'] : number_format((float)$item['reorder_level'], 2) . ' ' . $item['unit'] ?></td>
                                            <!--
                                            <td>
                                                <span class="badge <?= $item['days_to_out'] <= 3 ? 'danger' : ($item['days_to_out'] <= 7 ? 'warning' : 'success') ?>">
                                                    <?= $item['days_to_out'] ?> days
                                                </span>
                                            </td>
                                    -->
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
                <?php endif; ?>
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

    // Main initialization function to set up all charts
    function initializeCharts() {
        // Initialize each chart if its canvas exists
        drawSalesTrendChart();
        drawInventoryChart();
        drawCategoryChart();
        drawSupplierChart();
        drawSalesByCategoryChart();
    }

    // Draw line chart for sales trend
    function drawSalesTrendChart() {
        const canvas = document.getElementById('salesTrendChart');
        if (!canvas) return;
        
        // Set larger canvas dimensions
        canvas.width = 800;
        canvas.height = 400;
        canvas.style.width = '100%';
        canvas.style.height = 'auto';
        canvas.style.maxHeight = '400px';
        
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        // Get data from PHP (in production, this would be parsed from JSON)
        // Sample data for demonstration
        const dates = JSON.parse('<?= json_encode(array_column($salesData, "date")) ?>');
        const salesData = JSON.parse('<?= json_encode(array_column($salesData, "sales")) ?>');
        
        // Clear canvas
        ctx.clearRect(0, 0, width, height);
        
        // Draw chart background and grid
        drawChartBackground(ctx, width, height);
        
        // Calculate scales
        const maxSales = Math.max(...salesData) * 1.1; // Add 10% padding
        const minSales = 0;
        const dataPoints = salesData.length;
        
        // Draw axes with larger margins
        const margins = {
            left: 80,
            right: 60,
            top: 60,
            bottom: 80
        };
        
        drawAxes(ctx, width, height, 'Date', 'Sales (LKR)', minSales, maxSales, margins);
        
        // Prepare for drawing data
        const chartWidth = width - margins.left - margins.right;
        const chartHeight = height - margins.top - margins.bottom;
        const xStep = chartWidth / (dataPoints - 1);
        const yScale = chartHeight / maxSales;
        
        // Create array of points
        const points = [];
        for (let i = 0; i < dataPoints; i++) {
            const x = margins.left + i * xStep;
            const y = height - margins.bottom - (salesData[i] - minSales) * yScale;
            points.push({x, y});
        }
        
        // Start drawing the curve
        ctx.beginPath();
        ctx.strokeStyle = '#2563eb';
        ctx.fillStyle = 'rgba(37, 99, 235, 0.1)';
        ctx.lineWidth = 3;
        
        // Draw using Catmull-Rom spline (ensuring curve passes through all points)
        if (points.length > 1) {
            // Move to first point
            ctx.moveTo(points[0].x, points[0].y);
            
            // Handle the first segment separately (using the first point twice)
            if (points.length === 2) {
                // For only two points, just draw a line
                ctx.lineTo(points[1].x, points[1].y);
            } else {
                // First segment with duplicate first point
                const p0 = points[0];
                const p1 = points[0];
                const p2 = points[1];
                const p3 = points[2];
                
                // Draw first curve
                drawCatmullRomSegment(ctx, p0, p1, p2, p3);
                
                // Draw middle segments
                for (let i = 1; i < points.length - 2; i++) {
                    const p0 = points[i-1];
                    const p1 = points[i];
                    const p2 = points[i+1];
                    const p3 = points[i+2];
                    
                    drawCatmullRomSegment(ctx, p0, p1, p2, p3);
                }
                
                // Handle the last segment separately (using the last point twice)
                const lastIdx = points.length - 1;
                const pLast0 = points[lastIdx-2];
                const pLast1 = points[lastIdx-1];
                const pLast2 = points[lastIdx];
                const pLast3 = points[lastIdx];
                
                drawCatmullRomSegment(ctx, pLast0, pLast1, pLast2, pLast3);
            }
        }
        
        // Stroke the line
        ctx.stroke();
        
        // Fill area under the line
        ctx.lineTo(points[points.length-1].x, height - margins.bottom);
        ctx.lineTo(points[0].x, height - margins.bottom);
        ctx.closePath();
        ctx.fill();
        
        // Draw points
        for (let i = 0; i < points.length; i++) {
            ctx.beginPath();
            ctx.arc(points[i].x, points[i].y, 6, 0, Math.PI * 2);
            ctx.fillStyle = '#2563eb';
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 2;
            ctx.stroke();
        }
        
        // Draw x-axis labels (dates) with larger font
        ctx.fillStyle = '#333';
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        
        for (let i = 0; i < dataPoints; i += Math.ceil(dataPoints / 6)) {
            const x = margins.left + i * xStep;
            ctx.fillText(dates[i], x, height - margins.bottom + 30);
        }
        
        // Draw y-axis labels (sales values) with larger font
        ctx.textAlign = 'right';
        const ySteps = 5;
        for (let i = 0; i <= ySteps; i++) {
            const y = height - margins.bottom - (i / ySteps) * chartHeight;
            const value = Math.round(i / ySteps * maxSales);
            ctx.fillText('LKR ' + value.toLocaleString(), margins.left - 10, y + 5);
        }
            
    }

    // Helper function to draw a Catmull-Rom segment between points
    function drawCatmullRomSegment(ctx, p0, p1, p2, p3, tension = 0.5) {
        // Catmull-Rom to Cubic Bezier conversion
        // Calculate control points (using tension parameter to control curve tightness)
        const controlPoints = getCatmullRomControlPoints(p0, p1, p2, p3, tension);
        
        // Draw the curve segment
        ctx.bezierCurveTo(
            controlPoints.cp1x, controlPoints.cp1y,
            controlPoints.cp2x, controlPoints.cp2y,
            p2.x, p2.y
        );
    }

    // Helper function to calculate Catmull-Rom control points for bezier curve
    function getCatmullRomControlPoints(p0, p1, p2, p3, tension = 0.5) {
        // Calculate control points for bezier curve that approximates Catmull-Rom
        const d1 = Math.sqrt(Math.pow(p1.x - p0.x, 2) + Math.pow(p1.y - p0.y, 2));
        const d2 = Math.sqrt(Math.pow(p2.x - p1.x, 2) + Math.pow(p2.y - p1.y, 2));
        const d3 = Math.sqrt(Math.pow(p3.x - p2.x, 2) + Math.pow(p3.y - p2.y, 2));
        
        // Scale factors
        const s1 = tension * d2 / (d1 + d2);
        const s2 = tension * d2 / (d2 + d3);
        
        // Control point 1
        const cp1x = p1.x + s1 * (p2.x - p0.x);
        const cp1y = p1.y + s1 * (p2.y - p0.y);
        
        // Control point 2
        const cp2x = p2.x - s2 * (p3.x - p1.x);
        const cp2y = p2.y - s2 * (p3.y - p1.y);
        
        return { cp1x, cp1y, cp2x, cp2y };
    }
    // Draw doughnut chart for inventory status
    function drawInventoryChart() {
        const canvas = document.getElementById('inventoryChart');
        if (!canvas) return;
        
        // Set larger canvas dimensions
        canvas.width = 700;
        canvas.height = 400;
        canvas.style.width = '100%';
        canvas.style.height = 'auto';
        canvas.style.maxHeight = '400px';
        
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        // Get data
        const inStock = <?= $stockStatus['in_stock'] ?>;
        const lowStock = <?= $stockStatus['low_stock'] ?>;
        const outOfStock = <?= $stockStatus['out_of_stock'] ?>;
        const total = inStock + lowStock + outOfStock;
        
        // Define colors
        const colors = [
            {fill: 'rgba(22, 163, 74, 0.7)', stroke: 'rgba(22, 163, 74, 1)'}, // success/green
            {fill: 'rgba(217, 119, 6, 0.7)', stroke: 'rgba(217, 119, 6, 1)'}, // warning/orange
            {fill: 'rgba(220, 38, 38, 0.7)', stroke: 'rgba(220, 38, 38, 1)'}  // danger/red
        ];
        
        // Clear canvas
        ctx.clearRect(0, 0, width, height);
        
        // Draw doughnut chart
        const centerX = width / 2;
        const centerY = height / 2 - 30; // Adjust for legend space
        const outerRadius = Math.min(width, height - 100) / 2.5;
        const innerRadius = outerRadius * 0.6; // Hole size
        
        // Data values and labels
        const data = [inStock, lowStock, outOfStock];
        const labels = ['In Stock', 'Low Stock', 'Out of Stock'];
        
        let startAngle = -Math.PI / 2; // Start from top
        
        // Draw each segment
        for (let i = 0; i < data.length; i++) {
            const sliceAngle = (data[i] / total) * (Math.PI * 2);
            const endAngle = startAngle + sliceAngle;
            
            ctx.beginPath();
            ctx.arc(centerX, centerY, outerRadius, startAngle, endAngle);
            ctx.arc(centerX, centerY, innerRadius, endAngle, startAngle, true);
            ctx.closePath();
            
            ctx.fillStyle = colors[i].fill;
            ctx.strokeStyle = colors[i].stroke;
            ctx.lineWidth = 2;
            
            ctx.fill();
            ctx.stroke();
            
            startAngle = endAngle;
        }
        
        // Draw legend with larger font and better spacing
        const legendY = height - 80;
        const itemHeight = 25;
        const spacing = 150; // Increased spacing
        
        for (let i = 0; i < labels.length; i++) {
            const x = (width / 2) - (spacing * (labels.length / 2)) + (spacing * i) + 25;
            
            // Color box
            ctx.fillStyle = colors[i].fill;
            ctx.strokeStyle = colors[i].stroke;
            ctx.fillRect(x - 20, legendY, 15, 15); // Larger color box
            ctx.strokeRect(x - 20, legendY, 15, 15);
            
            // Label
            ctx.fillStyle = '#333';
            ctx.font = '16px Arial'; // Increased font size
            ctx.textAlign = 'left';
            ctx.fillText(labels[i], x, legendY + 12);
            
            // Value and percentage
            const percentage = Math.round((data[i] / total) * 100);
            ctx.font = '14px Arial';
            ctx.fillText(`${data[i]} (${percentage}%)`, x, legendY + 35);
        }
        
        
    }

    // Draw vertical bar chart for category distribution
    function drawCategoryChart() {
        const canvas = document.getElementById('categoryChart');
        if (!canvas) return;
        
        // Set larger canvas dimensions
        canvas.width = 800;
        canvas.height = 400;
        canvas.style.width = '100%';
        canvas.style.height = 'auto';
        canvas.style.maxHeight = '400px';
        
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        // Get data
        const categories = JSON.parse('<?= json_encode(array_column($categoryData, "name")) ?>');
        const counts = JSON.parse('<?= json_encode(array_column($categoryData, "count")) ?>');
        
        // Clear canvas
        ctx.clearRect(0, 0, width, height);
        
        // Define margins
        const margins = {
            left: 80,
            right: 60,
            top: 60,
            bottom: 100  // Larger bottom margin for rotated labels
        };
        
        // Draw chart background and grid
        drawChartBackground(ctx, width, height);
        
        // Calculate scales
        const maxCount = Math.max(...counts) * 1.1; // Add 10% padding
        const dataPoints = categories.length;
        
        // Draw axes
        drawAxes(ctx, width, height, 'Categories', 'Products', 0, maxCount, margins);
        
        // Draw bars
        const chartWidth = width - margins.left - margins.right;
        const barWidth = (chartWidth / dataPoints) * 0.7;
        const barSpacing = (chartWidth / dataPoints) * 0.3;
        const chartHeight = height - margins.top - margins.bottom;
        const yScale = chartHeight / maxCount;
        
        for (let i = 0; i < dataPoints; i++) {
            const x = margins.left + (i * (barWidth + barSpacing)) + (barSpacing / 2);
            const barHeight = counts[i] * yScale;
            const y = height - margins.bottom - barHeight;
            
            // Draw bar with gradient
            const gradient = ctx.createLinearGradient(x, y, x, height - margins.bottom);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');  // Brighter blue at top
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.5)');  // Lighter blue at bottom
            
            ctx.fillStyle = gradient;
            ctx.strokeStyle = 'rgb(59, 130, 246)';
            ctx.lineWidth = 2;
            
            // Draw bar with flat top
            ctx.beginPath();
            ctx.rect(x, y, barWidth, height - margins.bottom - y);
            ctx.fill();
            ctx.stroke();
            
            // Draw category label
            ctx.fillStyle = '#333';
            ctx.font = '14px Arial';
            ctx.textAlign = 'center';
            
            // Rotate text for better readability
            ctx.save();
            ctx.translate(x + barWidth/2, height - margins.bottom + 30 );
            ctx.rotate(-Math.PI / 3);  // Less rotation for better readability
            ctx.fillText(categories[i], 0, 0);
            ctx.restore();
            
            // Draw value on top of bar
            ctx.textAlign = 'center';
            ctx.font = 'bold 14px Arial';
            ctx.fillStyle = '#1e40af';  // Darker blue for value
            ctx.fillText(counts[i], x + barWidth/2, y - 10);
        }
        
        // Draw y-axis labels (count values)
        ctx.textAlign = 'right';
        ctx.fillStyle = '#333';
        const ySteps = 5;
        for (let i = 0; i <= ySteps; i++) {
            const y = height - margins.bottom - (i / ySteps) * chartHeight;
            const value = Math.round(i / ySteps * maxCount);
            ctx.font = '14px Arial';
            ctx.fillText(value, margins.left - 10, y + 5);
        }

    }

    // Draw radar chart for supplier performance
    function drawSupplierChart() {
        const canvas = document.getElementById('supplierChart');
        if (!canvas) return;
        
        // Set larger canvas dimensions
        canvas.width = 700;
        canvas.height = 500;  // Taller for better visibility of radar
        canvas.style.width = '100%';
        canvas.style.height = 'auto';
        canvas.style.maxHeight = '500px';
        
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        // Get data
        const suppliers = JSON.parse('<?= json_encode(array_column($supplierPerformance, "name")) ?>');
        const onTimeData = JSON.parse('<?= json_encode(array_column($supplierPerformance, "on_time")) ?>');
        const qualityData = JSON.parse('<?= json_encode(array_column($supplierPerformance, "quality")) ?>');
        
        // Clear canvas
        ctx.clearRect(0, 0, width, height);
        
        // Draw radar chart
        const centerX = width / 2;
        const centerY = height / 2;
        const radius = Math.min(width, height) / 2 - 100;  // Smaller radius to leave room for labels
        const sides = suppliers.length;
        const angleStep = (Math.PI * 2) / sides;
        
        // Draw concentric circles (levels)
        const levels = 5;
        ctx.strokeStyle = '#ddd';
        ctx.lineWidth = 1;
        
        for (let level = 1; level <= levels; level++) {
            const levelRadius = (radius / levels) * level;
            
            ctx.beginPath();
            for (let i = 0; i <= sides; i++) {
                const angle = -Math.PI / 2 + i * angleStep;
                const x = centerX + levelRadius * Math.cos(angle);
                const y = centerY + levelRadius * Math.sin(angle);
                
                if (i === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            }
            ctx.closePath();
            ctx.stroke();
            
            // Draw level value with larger font
            const value = Math.round((level / levels) * 100);
            ctx.fillStyle = '#666';  // Darker gray for better visibility
            ctx.font = '12px Arial';  // Larger font
            ctx.textAlign = 'center';
            ctx.fillText(value.toString() + '%', centerX, centerY - levelRadius - 5);
        }
        
        // Draw axes
        ctx.strokeStyle = '#999';
        ctx.lineWidth = 1;
        for (let i = 0; i < sides; i++) {
            const angle = -Math.PI / 2 + i * angleStep;
            const x = centerX + radius * Math.cos(angle);
            const y = centerY + radius * Math.sin(angle);
            
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.lineTo(x, y);
            ctx.stroke();
            
            // Draw supplier label with larger font and background
            const labelDistance = radius * 1.15;
            const labelX = centerX + labelDistance * Math.cos(angle);
            const labelY = centerY + labelDistance * Math.sin(angle);
            
            // Add a small background for better readability
            ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
            const labelWidth = suppliers[i].length * 7;  // Estimate width based on text length
            ctx.fillRect(labelX - labelWidth/2, labelY - 10, labelWidth, 20);
            
            ctx.fillStyle = '#333';
            ctx.font = 'bold 14px Arial';  // Larger, bold font
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(suppliers[i], labelX, labelY);
        }
        
        // Draw on-time delivery data
        drawRadarDataset(ctx, centerX, centerY, radius, sides, angleStep, onTimeData, 
            'rgba(59, 130, 246, 0.3)', 'rgb(59, 130, 246)', 2);
        
        // Draw quality rating data
        drawRadarDataset(ctx, centerX, centerY, radius, sides, angleStep, qualityData, 
            'rgba(168, 85, 247, 0.3)', 'rgb(168, 85, 247)', 2);
        
        // Draw legend with larger elements
        const legendY = height - 50;
        
        // On-time delivery
        ctx.fillStyle = 'rgba(59, 130, 246, 0.3)';
        ctx.strokeStyle = 'rgb(59, 130, 246)';
        ctx.lineWidth = 2;
        ctx.fillRect(width / 4 - 60, legendY, 20, 20);  // Larger rectangle
        ctx.strokeRect(width / 4 - 60, legendY, 20, 20);
        
        ctx.fillStyle = '#333';
        ctx.font = '16px Arial';  // Larger font
        ctx.textAlign = 'left';
        ctx.fillText('On-Time Delivery', width / 4 - 35, legendY + 15);
        
        // Quality rating
        ctx.fillStyle = 'rgba(168, 85, 247, 0.3)';
        ctx.strokeStyle = 'rgb(168, 85, 247)';
        ctx.lineWidth = 2;
        ctx.fillRect(width * 3/4 - 90, legendY, 20, 20);  // Larger rectangle
        ctx.strokeRect(width * 3/4 - 90, legendY, 20, 20);
        
        ctx.fillStyle = '#333';
        ctx.textAlign = 'left';
        ctx.font = '16px Arial';  // Larger font
        ctx.fillText('Quality Rating', width * 3/4 - 65, legendY + 15);
        
        
    }


    function drawRadarDataset(ctx, centerX, centerY, radius, sides, angleStep, data, fillColor, strokeColor, lineWidth = 2) {
        ctx.beginPath();
        
        for (let i = 0; i < sides; i++) {
            const angle = -Math.PI / 2 + i * angleStep;
            const value = data[i] / 100; // Normalize to 0-1
            const pointRadius = radius * value;
            const x = centerX + pointRadius * Math.cos(angle);
            const y = centerY + pointRadius * Math.sin(angle);
            
            if (i === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        }
        
        ctx.closePath();
        ctx.fillStyle = fillColor;
        ctx.strokeStyle = strokeColor;
        ctx.lineWidth = lineWidth;
        ctx.fill();
        ctx.stroke();
        
        // Draw points
        for (let i = 0; i < sides; i++) {
            const angle = -Math.PI / 2 + i * angleStep;
            const value = data[i] / 100; // Normalize to 0-1
            const pointRadius = radius * value;
            const x = centerX + pointRadius * Math.cos(angle);
            const y = centerY + pointRadius * Math.sin(angle);
            
            ctx.beginPath();
            ctx.arc(x, y, 5, 0, Math.PI * 2);  // Larger dots (5px radius)
            ctx.fillStyle = '#fff';
            ctx.strokeStyle = strokeColor;
            ctx.lineWidth = 2;
            ctx.fill();
            ctx.stroke();
            
            // Add value labels
            ctx.fillStyle = '#333';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            // Position the text slightly offset from the point
            const textRadius = pointRadius + 15;
            const textX = centerX + textRadius * Math.cos(angle);
            const textY = centerY + textRadius * Math.sin(angle);
            
            ctx.fillText(data[i] + '%', textX, textY);
        }
    }

    function drawSalesByCategoryChart() {
        const canvas = document.getElementById('salesByCategoryChart');
        if (!canvas) return;
        
        // Set canvas dimensions
        canvas.width = 700;
        canvas.height = 400;
        canvas.style.width = '100%';
        canvas.style.height = 'auto';
        canvas.style.maxHeight = '400px';
        
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        // Get data
        const categories = JSON.parse('<?= json_encode(array_column($categoryRevenueData, "name")) ?>');
        const revenues = JSON.parse('<?= json_encode(array_column($categoryRevenueData, "revenue")) ?>');
        const total = revenues.reduce((sum, value) => sum + value, 0);
        
        // Define colors
        const colors = [
            'rgba(59, 130, 246, 0.8)',   // blue
            'rgba(168, 85, 247, 0.8)',    // purple
            'rgba(234, 88, 12, 0.8)',     // orange
            'rgba(22, 163, 74, 0.8)',     // green
            'rgba(217, 119, 6, 0.8)',     // amber
            'rgba(239, 68, 68, 0.8)',     // red
            'rgba(16, 185, 129, 0.8)',    // emerald
            'rgba(14, 165, 233, 0.8)'     // sky blue
        ];
        
        // Clear canvas
        ctx.clearRect(0, 0, width, height);
        
        // Add subtle background
        ctx.fillStyle = 'rgba(249, 250, 251, 0.5)';
        ctx.fillRect(0, 0, width, height);
        
        // Draw pie chart
        const centerX = width / 2;
        const centerY = height / 2 - 40; // Adjust for legend space
        const radius = Math.min(width, height - 150) / 3;
        
        let startAngle = 0;
        
        // Draw each slice without exploding or labels
        for (let i = 0; i < categories.length; i++) {
            const sliceAngle = (revenues[i] / total) * (Math.PI * 2);
            const endAngle = startAngle + sliceAngle;
            
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, startAngle, endAngle);
            ctx.closePath();
            
            // Apply a solid fill
            ctx.fillStyle = colors[i % colors.length];
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 1;
            
            ctx.fill();
            ctx.stroke();
            
            startAngle = endAngle;
        }
        
        // Draw legend
        const legendTop = height - 120;
        const itemHeight = 25;
        const itemsPerColumn = Math.ceil(categories.length / 2);
        
        for (let i = 0; i < categories.length; i++) {
            const column = Math.floor(i / itemsPerColumn);
            const row = i % itemsPerColumn;
            
            const x = (column * width / 2) + 50;
            const y = legendTop + (row * itemHeight);
            
            // Color box
            ctx.fillStyle = colors[i % colors.length];
            ctx.fillRect(x, y, 15, 15);
            ctx.strokeStyle = '#333';
            ctx.lineWidth = 1;
            ctx.strokeRect(x, y, 15, 15);
            
            // Label
            ctx.fillStyle = '#333';
            ctx.font = '14px Arial';
            ctx.textAlign = 'left';
            ctx.textBaseline = 'top';
            ctx.fillText(categories[i], x + 20, y);
            
            // Value
            const percentage = Math.round((revenues[i] / total) * 100);
            ctx.fillText(`LKR ${revenues[i].toLocaleString()} (${percentage}%)`, x + 135, y);
        }
    }

    // Utility function to draw chart background and grid
    // Updated utility function to draw chart background and grid
    function drawChartBackground(ctx, width, height) {
        // Light background
        ctx.fillStyle = 'rgba(249, 250, 251, 0.5)';
        ctx.fillRect(0, 0, width, height);
        
        // Draw grid lines
        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = 1;
        
        const margins = {
            left: 80,
            right: 60,
            top: 60,
            bottom: 80
        };
        
        // Vertical grid lines
        const chartWidth = width - margins.left - margins.right;
        const xStep = chartWidth / 10;
        for (let i = 0; i <= 10; i++) {
            const x = margins.left + i * xStep;
            ctx.beginPath();
            ctx.moveTo(x, margins.top);
            ctx.lineTo(x, height - margins.bottom);
            ctx.stroke();
        }
        
        // Horizontal grid lines
        const chartHeight = height - margins.top - margins.bottom;
        const yStep = chartHeight / 5;
        for (let i = 0; i <= 5; i++) {
            const y = margins.top + i * yStep;
            ctx.beginPath();
            ctx.moveTo(margins.left, y);
            ctx.lineTo(width - margins.right, y);
            ctx.stroke();
        }
    }

    function lightenColor(color) {
        // Convert rgba(r,g,b,a) to a lighter version
        const matches = color.match(/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/);
        if (matches) {
            const r = Math.min(255, parseInt(matches[1]) + 50);
            const g = Math.min(255, parseInt(matches[2]) + 50);
            const b = Math.min(255, parseInt(matches[3]) + 50);
            const a = parseFloat(matches[4]);
            return `rgba(${r}, ${g}, ${b}, ${a})`;
        }
        return color;
    }

    // Utility function to draw axes
    function drawAxes(ctx, width, height, xLabel, yLabel, minValue, maxValue, margins) {
        // Draw axes
        ctx.strokeStyle = '#333';
        ctx.lineWidth = 2; // Thicker axes
        
        // X-axis
        ctx.beginPath();
        ctx.moveTo(margins.left, height - margins.bottom);
        ctx.lineTo(width - margins.right, height - margins.bottom);
        ctx.stroke();
        
        // X-axis label
        ctx.fillStyle = '#333';
        ctx.font = '16px Arial'; // Increased font size
        ctx.textAlign = 'center';
        ctx.fillText(xLabel, width / 2, height - margins.bottom / 2 + 10);
        
        // Y-axis
        ctx.beginPath();
        ctx.moveTo(margins.left, height - margins.bottom);
        ctx.lineTo(margins.left, margins.top);
        ctx.stroke();
        
        // Y-axis label
        ctx.save();
        ctx.translate(margins.left / 3, height / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.textAlign = 'center';
        ctx.font = '16px Arial'; // Increased font size
        ctx.fillText(yLabel, 0, 0);
        ctx.restore();
    }

    // Function to set up chart placeholders if drawing fails
    function setupChartPlaceholders() {
        const placeholders = document.querySelectorAll('.chart-placeholder');
        placeholders.forEach(placeholder => {
            // Style placeholder
            placeholder.style.backgroundColor = '#f9fafb';
            placeholder.style.border = '1px dashed #d1d5db';
            placeholder.style.borderRadius = '4px';
            placeholder.style.display = 'flex';
            placeholder.style.justifyContent = 'center';
            placeholder.style.alignItems = 'center';
            placeholder.style.height = '200px';
            placeholder.style.cursor = 'pointer';
            
            // Add placeholder text
            const text = document.createElement('div');
            text.textContent = 'Chart Placeholder - Click to load';
            text.style.color = '#6b7280';
            text.style.fontSize = '14px';
            placeholder.appendChild(text);
            
            // Add click event
            placeholder.addEventListener('click', function() {
                alert('Charts would be loaded here in the final implementation');
            });
        });
    }

    // Function to dynamically load charts
    function loadCharts() {
        console.log('Loading pure JavaScript charts');
        initializeCharts();
        setupResponsiveCharts();
    }

    // Execute when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        loadCharts();
    });

    // Resize handling for responsive charts
    window.addEventListener('resize', function() {
        // Wait a bit before redrawing to avoid too many redraws during resizing
        if (this.resizeTimer) clearTimeout(this.resizeTimer);
        this.resizeTimer = setTimeout(function() {
            initializeCharts();
        }, 250);
    });


</script>
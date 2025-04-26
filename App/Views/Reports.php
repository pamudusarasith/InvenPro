<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <style>

        /* Chart Container (Unchanged) */
        .chart-container {
            position: relative;
            width: 100%;
            height: auto;
            aspect-ratio: 2 / 1;
        }

        .chart-container canvas {
            width: 100%;
            height: 100%;
        }

        .tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            pointer-events: none;
            display: none;
        }     
    </style>
</head>
<body>

<?php
$timePeriod = $_GET['timePeriod'] ?? 'today';
?>


<div class="body">
    <?php App\Core\View::render("Navbar") ?>
    <?php App\Core\View::render("Sidebar") ?>

    <div class="main">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h2>Reports</h2>
                <p class="subtitle">System reports and analysis</p>
            </div>
            <div class="filters">
                <select id="filterRole" name="timePeriod" class="form-select" onchange="filterByTimePeriod()">
                    <option value="all" <?= $timePeriod === 'all' ? 'selected' : '' ?>>All Time</option>
                    <option value="today" <?= $timePeriod === 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="week" <?= $timePeriod === 'week' ? 'selected' : '' ?>>Last Week</option>
                    <option value="month" <?= $timePeriod === 'month' ? 'selected' : '' ?>>Last Month</option>
                    <option value="year" <?= $timePeriod === 'year' ? 'selected' : '' ?>>Last Year</option>
                </select>
                <button type="button" class="btn btn-primary" onclick="refreshData()">Refresh Data</button>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Sales Overview Card -->
            <div class="dashboard-card sales">
                <div class="card-header">
                    <span class="icon">trending_up</span>
                    <h3>Today's Sales</h3>
                </div>
                <div class="card-content">
                    <h2><?= htmlspecialchars($reportData['sales']['value']) ?></h2>
                    <p class="trend <?= htmlspecialchars($reportData['sales']['trendType']) ?>">
                        <?= htmlspecialchars($reportData['sales']['trend']) ?> from yesterday
                    </p>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="dashboard-card revenue">
                <div class="card-header">
                    <span class="icon">payments</span>
                    <h3>Monthly Revenue</h3>
                </div>
                <div class="card-content">
                    <h2><?= htmlspecialchars($reportData['monthlyRevenue']['value']) ?></h2>
                    <p>This month's earnings</p>
                </div>
            </div>

            <!-- Low Stock Card -->
            <div class="dashboard-card low-stock warning">
                <div class="card-header">
                    <span class="icon">inventory</span>
                    <h3>Low Stock Items</h3>
                </div>
                <div class="card-content">
                    <h2><?= htmlspecialchars($reportData['lowStock']['value']) ?></h2>
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
                    <h2><?= htmlspecialchars($reportData['pendingOrders']['value']) ?></h2>
                    <p>Purchase orders to be approved</p>
                </div>
            </div>

            <!-- Total Products Card -->
            <div class="dashboard-card products">
                <div class="card-header">
                    <span class="icon">category</span>
                    <h3>Total Products</h3>
                </div>
                <div class="card-content">
                    <h2><?= htmlspecialchars($reportData['totalProducts']['value']) ?></h2>
                    <p>Active products</p>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card products" style="max-width: 70%;">
                <div class="card-header">
                    <span class="icon">trending_up</span>
                    <h3>Sales Trend</h3>
                </div>
                <div class="chart-container">
                    <canvas id="salesTrendChart"></canvas>
                    <div id="salesTooltip" class="tooltip"></div>
                </div>
            </div>
        </div>

        <div class="dashboard">
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="icon">trending_up</span>
                    <h3>Low stock</h3>
                </div>
        <div class="table-container">
            <table class="data-table clickable" id="users-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Last Login</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (empty($users)) {
                    echo '<tr><td colspan="7" style="text-align: center;">No users found</td></tr>';
                } else {
                    foreach ($users as $user):
                ?>
                    <tr onclick="location.href = '/users/<?= $user['id']; ?>'">
                        <td><?= htmlspecialchars($user['display_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= ucfirst($user['role_name']) ?></td>
                        <td><?= htmlspecialchars($user['branch_name']) ?></td>
                        <td>
                        <span class="badge <?= $user['is_locked'] ? 'danger' : 'success' ?>">
                            <?= htmlspecialchars($user['status']) ?>
                        </span>
                        </td>
                        <td><?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : "N/A" ?></td>
                    </tr>
                <?php endforeach;
                } ?>
                </tbody>
            </table>

            <div class="pagination-controls">
                <div class="items-per-page">
                <span>Show:</span>
                <select class="items-select" onchange="changeItemsPerPage(this.value)">
                    <option value="5" <?= $itemsPerPage == 5 ? "selected" : "" ?>>5</option>
                    <option value="10" <?= $itemsPerPage == 10 ? "selected" : "" ?>>10</option>
                    <option value="20" <?= $itemsPerPage == 20 ? "selected" : "" ?>>20</option>
                    <option value="50" <?= $itemsPerPage == 50 ? "selected" : "" ?>>50</option>
                    <option value="100" <?= $itemsPerPage == 100 ? "selected" : "" ?>>100</option>
                </select>
                <span>entries</span>
                </div>
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                    <button class="page-btn" onclick="changePage(<?= min($totalPages, $page - 1) ?>)">
                        <span class="icon">chevron_left</span>
                    </button>
                    <?php endif; ?>

                    <div class="page-numbers">
                    <?php
                    $maxButtons = 3;
                    $halfMax = floor($maxButtons / 2);
                    $start = max(1, min($page - $halfMax, $totalPages - $maxButtons + 1));
                    $end = min($totalPages, $start + $maxButtons - 1);

                    if ($start > 1) {
                        echo '<span class="page-number">1</span>';
                        if ($start > 2) {
                        echo '<span class="page-dots">...</span>';
                        }
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        echo '<span class="page-number ' . ($page == $i ? 'active' : '') . '"
                            onclick="changePage(' . $i . ')">' . $i . '</span>';
                    }

                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) {
                        echo '<span class="page-dots">...</span>';
                        }
                        echo '<span class="page-number">' . $totalPages . '</span>';
                    }
                    ?>
                    </div>


                    <?php if ($page < $totalPages): ?>
                    <button class="page-btn" onclick="changePage(<?= $page + 1 ?>)">
                        <span class="icon">chevron_right</span>
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
            </div>
        </div>
    </div>

    <script>
        // Sales Trend Chart
        const salesCanvas = document.getElementById('salesTrendChart');
        const salesCtx = salesCanvas.getContext('2d');
        const salesTooltip = document.getElementById('salesTooltip');
        const salesData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Monthly Sales',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: '#3498db'
            }]
        };

        let salesPoints = [];
        let animationProgress = 0;

        // Catmull-Rom spline interpolation
        function getCatmullRomPoint(t, p0, p1, p2, p3, alpha = 0.5) {
            const t0 = 0;
            const t1 = Math.pow(Math.hypot(p1.x - p0.x, p1.y - p0.y), alpha) + t0;
            const t2 = Math.pow(Math.hypot(p2.x - p1.x, p2.y - p1.y), alpha) + t1;
            const t3 = Math.pow(Math.hypot(p3.x - p2.x, p3.y - p2.y), alpha) + t2;

            const tScaled = t1 + (t2 - t1) * t;

            const A1x = (t1 - tScaled) / (t1 - t0) * p0.x + (tScaled - t0) / (t1 - t0) * p1.x;
            const A1y = (t1 - tScaled) / (t1 - t0) * p0.y + (tScaled - t0) / (t1 - t0) * p1.y;
            const A2x = (t2 - tScaled) / (t2 - t1) * p1.x + (tScaled - t1) / (t2 - t1) * p2.x;
            const A2y = (t2 - tScaled) / (t2 - t1) * p1.y + (tScaled - t1) / (t2 - t1) * p2.y;
            const A3x = (t3 - tScaled) / (t3 - t2) * p2.x + (tScaled - t2) / (t3 - t2) * p3.x;
            const A3y = (t3 - tScaled) / (t3 - t2) * p2.y + (tScaled - t2) / (t3 - t2) * p3.y;

            const B1x = (t2 - tScaled) / (t2 - t0) * A1x + (tScaled - t0) / (t2 - t0) * A2x;
            const B1y = (t2 - tScaled) / (t2 - t0) * A1y + (tScaled - t0) / (t2 - t0) * A2y;
            const B2x = (t3 - tScaled) / (t3 - t1) * A2x + (tScaled - t1) / (t3 - t1) * A3x;
            const B2y = (t3 - tScaled) / (t3 - t1) * A2y + (tScaled - t1) / (t3 - t1) * A3y;

            const Cx = (t2 - tScaled) / (t2 - t1) * B1x + (tScaled - t1) / (t2 - t1) * B2x;
            const Cy = (t2 - tScaled) / (t2 - t1) * B1y + (tScaled - t1) / (t2 - t1) * B2y;

            return { x: Cx, y: Cy };
        }

        function drawSalesChart() {
            const container = salesCanvas.parentElement;
            salesCanvas.width = container.clientWidth;
            salesCanvas.height = container.clientHeight || 300;

            const width = salesCanvas.width;
            const height = salesCanvas.height;
            const padding = 40;
            const data = salesData.datasets[0].data;
            const labels = salesData.labels;
            const maxValue = Math.max(...data);
            const minValue = 0; // Ensure y-axis starts from zero

            salesCtx.clearRect(0, 0, width, height);

            // Draw grid and axes
            salesCtx.beginPath();
            salesCtx.strokeStyle = '#ccc';
            salesCtx.lineWidth = 1;
            const yStep = (height - 2 * padding) / 5;
            const valueStep = (maxValue - 0) / 5; // Ensure y-axis starts from zero
            for (let i = 0; i <= 5; i++) {
                const y = height - padding - i * yStep;
                salesCtx.moveTo(padding, y);
                salesCtx.lineTo(width - padding, y);
                salesCtx.font = '12px Arial'; // Set font style
                salesCtx.fillStyle = '#333'; // Set text color
                salesCtx.fillText(Math.round(0 + i * valueStep), padding - 40, y + 5); // Adjusted x position
            }
            salesCtx.stroke();

            salesCtx.beginPath();
            salesCtx.moveTo(padding, padding);
            salesCtx.lineTo(padding, height - padding);
            salesCtx.lineTo(width - padding, height - padding);
            salesCtx.strokeStyle = '#000';
            salesCtx.stroke();

            // Draw x-axis labels
            const xStep = (width - 2 * padding) / (labels.length - 1);
            salesCtx.font = '12px Arial'; // Set font style
            salesCtx.fillStyle = '#333'; // Set text color
            labels.forEach((label, i) => {
                salesCtx.fillText(label, padding + i * xStep - 10, height - padding + 20);
            });

            // Calculate points
            salesPoints = data.map((value, i) => {
                const x = padding + i * xStep;
                const y = height - padding - ((value - minValue) / (maxValue - minValue)) * (height - 2 * padding);
                return { x, y, value, label: labels[i] };
            });

            // Extend points for Catmull-Rom
            const extendedPoints = [
                { x: salesPoints[0].x - xStep, y: salesPoints[0].y, value: salesPoints[0].value, label: salesPoints[0].label },
                ...salesPoints,
                { x: salesPoints[salesPoints.length - 1].x + xStep, y: salesPoints[salesPoints.length - 1].y, value: salesPoints[salesPoints.length - 1].value, label: salesPoints[salesPoints.length - 1].label }
            ];

            // Draw Catmull-Rom spline
            salesCtx.beginPath();
            salesCtx.strokeStyle = salesData.datasets[0].borderColor;
            salesCtx.lineWidth = 2;

            for (let i = 1; i < extendedPoints.length - 2; i++) {
                const p0 = extendedPoints[i - 1];
                const p1 = extendedPoints[i];
                const p2 = extendedPoints[i + 1];
                const p3 = extendedPoints[i + 2];

                const steps = 20 * animationProgress;
                for (let t = 0; t <= 1; t += 1 / steps) {
                    const point = getCatmullRomPoint(t, p0, p1, p2, p3);
                    if (t === 0 && i === 1) {
                        salesCtx.moveTo(point.x, point.y);
                    } else {
                        salesCtx.lineTo(point.x, point.y);
                    }
                }
            }
            salesCtx.stroke();

            // Draw points
            salesPoints.forEach((point, i) => {
                if (i / salesPoints.length < animationProgress) {
                    salesCtx.beginPath();
                    salesCtx.arc(point.x, point.y, 5, 0, 2 * Math.PI);
                    salesCtx.fillStyle = salesData.datasets[0].borderColor;
                    salesCtx.fill();
                }
            });
        }

        // Animate line chart
        function animateSalesChart() {
            animationProgress += 0.01;
            if (animationProgress <= 1) {
                drawSalesChart();
                requestAnimationFrame(animateSalesChart);
            }
        }

        // Handle tooltips
        salesCanvas.addEventListener('mousemove', (e) => {
            const rect = salesCanvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            let found = false;
            salesPoints.forEach(point => {
                if (Math.hypot(x - point.x, y - point.y) < 10) {
                    salesTooltip.style.display = 'block';
                    salesTooltip.style.left = `${e.clientX + 10}px`;
                    salesTooltip.style.top = `${e.clientY + 10}px`;
                    salesTooltip.innerHTML = `${point.label}: $${point.value}`;
                    found = true;
                }
            });

            if (!found) salesTooltip.style.display = 'none';
        });

        // Handle responsiveness
        const resizeObserver = new ResizeObserver(() => {
            animationProgress = 0;
            animateSalesChart();
        });
        resizeObserver.observe(salesCanvas.parentElement);

        // Initial draw
        animateSalesChart();

        // Table Sorting
        function sortTable(columnIndex) {
            const table = document.getElementById('productsTable');
            let switching = true;
            let direction = 'asc';
            let switchCount = 0;

            while (switching) {
                switching = false;
                const rows = table.rows;

                for (let i = 1; i < rows.length - 1; i++) {
                    let shouldSwitch = false;
                    const x = rows[i].getElementsByTagName('TD')[columnIndex];
                    const y = rows[i + 1].getElementsByTagName('TD')[columnIndex];
                    const xValue = x.innerHTML.toLowerCase();
                    const yValue = y.innerHTML.toLowerCase();

                    if (direction === 'asc' && xValue > yValue) {
                        shouldSwitch = true;
                    } else if (direction === 'desc' && xValue < yValue) {
                        shouldSwitch = true;
                    }

                    if (shouldSwitch) {
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                        switchCount++;
                        break;
                    }
                }

                if (switchCount === 0 && direction === 'asc') {
                    direction = 'desc';
                    switching = true;
                }
            }
        }

        // Pagination Functions
        function changePage(page) {
            console.log(`Change to page ${page}`);
            // Implement server-side pagination logic here
            alert(`Navigating to page ${page}`);
        }

        function changeItemsPerPage(items) {
            console.log(`Show ${items} items per page`);
            // Implement server-side items per page logic here
            alert(`Set items per page to ${items}`);
        }

        // Refresh Data
        function refreshData() {
            alert('Data refreshed!');
            // Implement data refresh logic here
        }

        document.getElementById('filterTimePeriod').addEventListener('change', applyFilters);

        function applyFilters() {
            const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
            const rows = document.querySelectorAll('#users-table tbody tr');
            rows.forEach(row => {
            const status = row.cells[4].textContent.toLowerCase();
            const matchesStatus = statusFilter === '' || status === statusFilter;
            row.style.display = matchesStatus ? '' : 'none';
            });
        }

        function debounce(func, wait) {
            let timeout;
            return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        function updateFilterParams() {
            const search = document.getElementById('filterTimeStamp').value;
            
            const url = new URL(location.href);
            url.pathname = '/reports';
            url.searchParams.set('timePeriod', timePeriod);
            location.href = url.toString();
        }

        function changePage(pageNo) {
            const url = new URL(location.href);
            url.searchParams.set('p', pageNo);
            location.href = url.toString();
        }

        function changeItemsPerPage(itemsPerPage) {
            const url = new URL(location.href);
            url.searchParams.set('ipp', itemsPerPage);
            url.searchParams.delete('p');
            location.href = url.toString();
        }
    </script>
</div>
</body>
</html>
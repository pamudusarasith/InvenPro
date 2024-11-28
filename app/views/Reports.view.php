<div class="body">
  <?php App\View::render("components/Navbar") ?>
  <?php App\View::render("components/Sidebar") ?>
  <div class="content reports">
    <div class="reports-container">
      <h1>InvenPro Reports Dashboard</h1>

      <div class="report-filters">
        <select id="timeRange">
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
          <option value="year">This Year</option>
        </select>
        <button onclick="refreshData()">Refresh Data</button>
      </div>

      <div class="report-section">
        <h2>Sales Summary</h2>
        <div class="stats-grid">
          <div class="stat-card">
            <h3>Today's Sales</h3>
            <p class="amount">$5,487.25</p>
            <p class="trend positive">+12.5%</p>
            <div class="sparkline" id="salesSparkline"></div>
          </div>
          <div class="stat-card">
            <h3>Monthly Revenue</h3>
            <p class="amount">$157,892.00</p>
            <p class="trend positive">+8.3%</p>
            <div class="sparkline" id="revenueSparkline"></div>
          </div>
          <div class="stat-card">
            <h3>Low Stock Items</h3>
            <p class="amount">23</p>
            <p class="trend negative">Critical</p>
            <div class="sparkline" id="stockSparkline"></div>
          </div>
        </div>
      </div>

      <div class="report-grid">
        <div class="report-section">
          <h2>Sales Trends</h2>
          <div class="chart-container">
            <canvas id="salesTrendChart"></canvas>
          </div>
        </div>

        <div class="report-section">
          <h2>Category Distribution</h2>
          <div class="chart-container">
            <canvas id="categoryPieChart"></canvas>
          </div>
        </div>
      </div>

      <div class="report-section">
        <h2>Top Selling Products</h2>
        <table class="report-table" id="productsTable">
          <thead>
            <tr>
              <th onclick="sortTable(0)">Product Name ↕</th>
              <th onclick="sortTable(1)">Units Sold ↕</th>
              <th onclick="sortTable(2)">Revenue ↕</th>
              <th onclick="sortTable(3)">Stock Level ↕</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Fresh Milk 1L</td>
              <td>1,234</td>
              <td>$3,702.00</td>
              <td>89</td>
              <td><span class="status good">Good</span></td>
            </tr>
            <tr>
              <td>Whole Grain Bread</td>
              <td>987</td>
              <td>$2,961.00</td>
              <td>45</td>
              <td><span class="status warning">Low</span></td>
            </tr>
            <tr>
              <td>Organic Eggs</td>
              <td>756</td>
              <td>$2,268.00</td>
              <td>120</td>
              <td><span class="status good">Good</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(salesCtx, {
          type: 'line',
          data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
              label: 'Monthly Sales',
              data: [12000, 19000, 15000, 25000, 22000, 30000],
              borderColor: '#3498db',
              tension: 0.4
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false
          }
        });

        // Category Distribution Chart
        const pieCtx = document.getElementById('categoryPieChart').getContext('2d');
        new Chart(pieCtx, {
          type: 'doughnut',
          data: {
            labels: ['Dairy', 'Bakery', 'Produce', 'Meat', 'Beverages'],
            datasets: [{
              data: [30, 20, 25, 15, 10],
              backgroundColor: ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6']
            }]
          }
        });

        // Table Sorting
        function sortTable(n) {
          const table = document.getElementById("productsTable");
          let switching = true;
          let dir = "asc";
          let switchcount = 0;

          while (switching) {
            switching = false;
            let rows = table.rows;

            for (let i = 1; i < (rows.length - 1); i++) {
              let shouldSwitch = false;
              let x = rows[i].getElementsByTagName("TD")[n];
              let y = rows[i + 1].getElementsByTagName("TD")[n];

              if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                  shouldSwitch = true;
                  break;
                }
              } else {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                  shouldSwitch = true;
                  break;
                }
              }
            }

            if (shouldSwitch) {
              rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
              switching = true;
              switchcount++;
            }
          }
        }

        function refreshData() {
          // Simulate data refresh
          alert('Data refreshed!');
        }
      </script>
    </div>
  </div>
</div>
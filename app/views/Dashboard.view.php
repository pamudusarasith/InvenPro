<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>
    <div class="content">
        <div class="dashboard-grid">
            <!-- Sales Overview Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="material-symbols-rounded">trending_up</span>
                    <h3>Today's Sales</h3>
                </div>
                <div class="card-content">
                    <h2>$12,856</h2>
                    <p class="trend positive">+15% from yesterday</p>
                </div>
            </div>

            <!-- Low Stock Card -->
            <div class="dashboard-card warning">
                <div class="card-header">
                    <span class="material-symbols-rounded">inventory</span>
                    <h3>Low Stock Items</h3>
                </div>
                <div class="card-content">
                    <h2>23</h2>
                    <p>Items need reordering</p>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="material-symbols-rounded">shopping_cart</span>
                    <h3>Pending Orders</h3>
                </div>
                <div class="card-content">
                    <h2>45</h2>
                    <p>Orders to process</p>
                </div>
            </div>

            <!-- Staff Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="material-symbols-rounded">group</span>
                    <h3>Staff On Duty</h3>
                </div>
                <div class="card-content">
                    <h2>12</h2>
                    <p>Active employees</p>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="material-symbols-rounded">payments</span>
                    <h3>Monthly Revenue</h3>
                </div>
                <div class="card-content">
                    <h2>$145,789</h2>
                    <p>This month's earnings</p>
                </div>
            </div>

            <!-- Customer Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="material-symbols-rounded">people</span>
                    <h3>Active Customers</h3>
                </div>
                <div class="card-content">
                    <h2>1,234</h2>
                    <p>Registered users</p>
                </div>
            </div>

            <!-- Returns Card -->
            <div class="dashboard-card warning">
                <div class="card-header">
                    <span class="material-symbols-rounded">assignment_return</span>
                    <h3>Pending Returns</h3>
                </div>
                <div class="card-content">
                    <h2>8</h2>
                    <p>Items to process</p>
                </div>
            </div>

            <!-- Out of Stock Card -->
            <div class="dashboard-card warning">
                <div class="card-header">
                    <span class="material-symbols-rounded">block</span>
                    <h3>Out of Stock</h3>
                </div>
                <div class="card-content">
                    <h2>15</h2>
                    <p>Items unavailable</p>
                </div>
            </div>

            <!-- Total Products Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="material-symbols-rounded">category</span>
                    <h3>Total Products</h3>
                </div>
                <div class="card-content">
                    <h2>2,567</h2>
                    <p>Active products</p>
                </div>
            </div>

            <!-- Suppliers Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="material-symbols-rounded">local_shipping</span>
                    <h3>Active Suppliers</h3>
                </div>
                <div class="card-content">
                    <h2>48</h2>
                    <p>Registered suppliers</p>
                </div>
            </div>
        </div>
    </div>
</div>
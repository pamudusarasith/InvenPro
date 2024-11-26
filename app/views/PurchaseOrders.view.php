<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>

    <div class="content">

        <!-- Flexbox container for heading and button -->
        <div class="header-section">
            <h1>Purchase Orders</h1>
            <a href="/suppliers/add" class="add-btn">Add New Order</a>
        </div>

        <!-- Seasrch and Filter Section -->
        <div id="sup-search" class="search-container">
          <div class="row search-bar">
            <span class="material-symbols-rounded">search</span>
            <input type="text" class="" placeholder="Search here" >
          </div>
        </div>

        <div class="filter-section">

             <div class="dropdown">
                <lable class="filter-lable">Status:</lable>
                <select class="drp" id="status" name="status">
                    <option value="Pending" default>Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Ordered">Ordered</option>
                </select>
            </div>

            <div class="dropdown">
                <lable class="filter-lable">Product Category:</lable>
                <select class="drp" id="product-category" name="product-category">
                    <option value="All" default>All</option>
                    <option value="Vegetables">Vegetable</option>
                    <option value="Fruits">Fruits</option>
                    <option value="Fish">Fish</option>
                    <option value="Meats">Meat</option>
                    <option value="Others">Others</option>
                </select>
            </div>
        </div>

        <!-- Suppliers Table -->
        <table class="purchaseOrders-table">
            <thead>
            <tr>
                <th>Order ID</th>
                <th>Product Category</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Supplier</th>
                <th>Supplier Contact No</th>
                <th>Amonut (Rs.)</th>
                <th>Expected delivery date</th>
                <th>Status</th>
                <th>Notes</th>
                <th>Actions</th>
                
            </tr>
            </thead>

            <tbody>
               
                <tr>
               
                <td>001</td>
                <td>Biscuits</td>
                <td>Chocolate biscuits</td>
                <td>100 packets</td>
                <td>Maliban</td>
                <td>0712345678</td>
                <td>10000</td>
                <td>2024/12/29</td>
                <td>Pending</td>
                <td>No</td>
                <td class='action-btn'>
                  <a class='btn-view' href='/suppliers/details'>View</a>
                  <a class='btn-delete' href='/suppliers'>Delete</a>
                </td>
                </tr>
            

                <tr>
               
                <td>002</td>
                <td>Vegetables</td>
                <td>Carrot</td>
                <td>10 kg</td>
                <td>VEG</td>
                <td>0712371284</td>
                <td>3000</td>
                <td>2024/12/29</td>
                <td>Pending</td>
                <td>No</td>
                <td class='action-btn'>
                  <a class='btn-view' href='/suppliers/details'>View</a>
                  <a class='btn-delete' href='/suppliers'>Delete</a>
                </td>
                </tr>
            
            </tbody>
        </table>


    </div>
</div>



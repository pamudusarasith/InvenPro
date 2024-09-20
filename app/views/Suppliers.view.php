<div class="container-column">
    <?php App\View::render("components/Navbar") ?>
    <div class="container-row">
        <?php App\View::render("components/Sidebar") ?>
        <div class="content">

            <!-- Flexbox container for heading and button -->
            <div class="header-section">
                <h1>Suppliers</h1>
                <a href="/suppliers/addSupplier" class="add-btn">Add New Supplier</a>
            </div>

                <!-- Seasrch and Filter Section -->
                <div class="search-section">
                    <input type="text" placeholder="Search here" class="search-bar">
                </div>

                <div class="filter-section">
                    <div class="dropdown">
                        <lable class="filter-lable">Status:</lable>
                            <select class="drp" id="status" name="status">
                                <option value="Active" default>Active</option>
                                <option value="Inactive">Inactive</option>
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
            <table class="suppliers-table">
                <thead>
                    <tr>
                        <th>SID</th>
                        <th>Name</th>
                        <th>Contact No</th>
                        <th>Product Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example of supplier rows -->
                    <tr>
                        <td>001</td>
                        <td>Munchee</td>
                        <td>0712345678</td>
                        <td>Biscuits</td>
                        <td><span class="status-active">Active</span></td>
                        <td><a href="/suppliers/supplierDetails" class="view-profile">View profile</a></td>
                    </tr>
                    <tr>
                        <td>002</td>
                        <td>Amal Fdo</td>
                        <td>0712345678</td>
                        <td>Fish</td>
                        <td><span class="status-inactive">Inactive</span></td>
                        <td><a href="#" class="view-profile">View profile</a></td>
                    </tr>
                </tbody>
            </table>

        
         </div>
    </div>
</div>

<!-- Updated CSS -->
<style>
    .content {
        padding: 5px;
        flex: 1;
    }

    /* Flex container for heading and button */
    .header-section {
        display: flex;
        justify-content: space-between; /* Align items to the opposite sides */
        align-items: center; /* Vertically align the button with the heading */
    }

    .search-section {
        display: flex;
        margin-bottom: 10px; /* Adjust margin for spacing */
    }

     /* Filter section */
     .filter-section {
        display: flex;
        gap: 50px;
        margin-bottom: 30px;
    }

    .filter-lable{
        font-family: Arial, sans-serif;
        margin-bottom: 5px;
        font-size: 14px;
        padding: 5px
    }

    .drp{
        padding: 5px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f2f4f5;
        width: 150px

    }

    .search-bar {
        padding: 8px;
        width: 800px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

   
    .add-btn {
        background-color: #28a745;
        color: white;
        padding: 8px 12px;
        border: 1px solid #ccc;
        cursor: pointer;
        border-radius: 4px;
    }

    .suppliers-table {
        width: 95%;
        border-collapse: collapse;
        margin: 0 auto;
    }

    .suppliers-table th {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .suppliers-table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .status-active {
        color: white;
        background-color: green;
        padding: 5px 10px;
        border-radius: 4px;
    }

    .status-inactive {
        color: white;
        background-color: red;
        padding: 5px 10px;
        border-radius: 4px;
    }

    .view-profile {
        color: black;
        text-decoration: underline;
        cursor: pointer;
    }


</style>

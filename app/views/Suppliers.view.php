<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>

    <div class="content">

        <!-- Flexbox container for heading and button -->
        <div class="header-section">
            <h1>Suppliers</h1>
            <a href="/suppliers/add" class="add-btn">Add New Supplier</a>
        </div>

        <!-- Seasrch and Filter Section -->
        <div id="sup-search" class="search-container">
            <div class="row search-bar">
                <span class="material-symbols-rounded">search</span>
                <input type="text" class="" placeholder="Search here">
            </div>
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
                <th>Products</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
              <?php
              $servername = "localhost";
              $username = "root";
              $password = "";
              $database = "invenpro";

              //create connection
              $connection = new mysqli($servername, $username, $password, $database);

              //check connection
              if($connection->connect_error){
                die("Connection failed: " . $connection->connect_error);
              }

              //read all row from database supplier table
              $sql = "SELECT * FROM supplier_details";
              $result = $connection->query($sql);

              if(!$result){
                die("Invalid Query: " . $connection->error);
              }

              //read data of each row
              while($row = $result->fetch_assoc()){
                echo 
                "<tr>
               
                <td>" . $row["supplierID"] . "</td>
                <td>" . $row["supplierName"] . "</td>
                <td>" . $row["contactNo"] . "</td>
                <td>" . $row["productCategories"] . "</td>
                <td>" . $row["products"] . "</td>
                <td class='status'>Active</td>
                <td class='action-btn'>
                  <a class='btn-view' href='/suppliers/details?id=" . urlencode($row["supplierID"]) . "'>View</a>
                  <form method='POST' action='/suppliers/delete' style='display: inline;'>
                            <input type='hidden' name='supplier-id' value='" . htmlspecialchars($row["supplierID"]) . "' />
                            <button type='submit' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this supplier?\");'>Delete</button>
                  </form>
                </td>
                </tr>";
              }
  
            
            ?>
        
            </tbody>
        </table>


    </div>
</div>



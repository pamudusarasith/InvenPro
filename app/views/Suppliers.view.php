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
                
                </tr>";
              }
  
            
            ?>
        
            </tbody>
        </table>


    </div>
</div>

<!-- Updated CSS -->
<style>
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

  .filter-lable {
    font-family: Arial, sans-serif;
    margin-bottom: 5px;
    font-size: 14px;
    padding: 5px
  }

  .drp {
    padding: 5px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #f2f4f5;
    width: 150px

  }

  .search-bar {
    margin: 16px 0 8px 0;
    width: 100%;
    padding: 12px;
    border-radius: 24px;
    background-color: #e0e0e0;
    border: 0;
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
    background-color: darkgray;
    text-align: center;
  }

  .suppliers-table td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
  }

  .midcol{
    text-align: center;
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

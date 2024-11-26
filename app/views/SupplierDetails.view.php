<div class="body">
    <?php App\View::render("components/Navbar"); ?>
    <?php App\View::render("components/Sidebar"); ?>
    <div class="content">

        <div class="header-section">
            <h1>Supplier Details</h1>
        </div>

        <!-- Details Section -->
        <div class="details-section">
            <?php
            // Database credentials
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "invenpro";

            // Create connection
            $connection = new mysqli($servername, $username, $password, $database);

            // Check connection
            if ($connection->connect_error) {
                die("<p style='color:red;'>Connection failed: " . htmlspecialchars($connection->connect_error) . "</p>");
            }

            // Retrieve supplier ID from the URL
            $supplierID = isset($_GET['id']) ? trim($_GET['id']) : null;

            if ($supplierID) {
                // Sanitize the supplier ID
                $supplierID = htmlspecialchars($supplierID);

                // Query to fetch supplier details
                $sql = "SELECT * FROM supplier_details WHERE supplierID = ?";
                $stmt = $connection->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("s", $supplierID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Display supplier details if found
                    if ($result && $row = $result->fetch_assoc()) {
                        echo "<p><span>Supplier ID:</span> " . htmlspecialchars($row["supplierID"]) . "</p>";
                        echo "<p><span>Supplier Name:</span> " . htmlspecialchars($row["supplierName"]) . "</p>";
                        echo "<p><span>Product Categories:</span> " . htmlspecialchars($row["productCategories"]) . "</p>";
                        echo "<p><span>Products:</span> " . htmlspecialchars($row["products"]) . "</p>";
                        echo "<p><span>Address:</span> " . htmlspecialchars($row["address"]) . "</p>";
                        echo "<p><span>Email:</span> " . htmlspecialchars($row["email"]) . "</p>";
                        echo "<p><span>Contact No:</span> " . htmlspecialchars($row["contactNo"]) . "</p>";
                        echo "<p><span>Special Notes:</span> " . htmlspecialchars($row["specialNotes"]) . "</p>";
                    } else {
                        echo "<p style='color:red;'>No supplier found with the given ID.</p>";
                    }

                    // Close resources
                    $stmt->close();
                } else {
                    echo "<p style='color:red;'>Error preparing the query.</p>";
                }
            } else {
                echo "<p style='color:red;'>Invalid or missing supplier ID.</p>";
            }

            $connection->close();
            ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="/suppliers" class="btn-cancel">Back to List</a>
        </div>
    </div>
</div>

<!-- CSS Styling -->
<style>
/* Supplier Details Page Styles */
.details-section p {
    padding: 6px;
    font-size: 16px;
    margin-bottom: 10px;
}

.details-section span {
    font-weight: bold;
}

.action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 20px;
}

.btn-cancel {
    background-color: #6c757d;
    color: white;
    padding: 10px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
}

.btn-cancel:hover {
    background-color: #5a6268;
}
</style>

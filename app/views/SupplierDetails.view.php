<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>
    <div class="content">

        <div class="header-section">
            <h1>Supplier Details</h1>
        </div>
    
        <!-- details section -->
        <div class="details-section">
                <p><span>Supplier ID :</span> 001</p>
                <p><span>Supplier Name :</span> Munchee Company</p>
                <p><span>Product Categories :</span> Biscuits</p>
                <p><span>Products :</span> Chocolate biscuits, Gold marie</p>
                <p><span>Address :</span> Rathmalana</p>
                <p><span>Email :</span> munchee@gmail.com</p>
                <p><span>Contact No :</span> 0712345678</p>
                <p><span>Special Notes :</span> -</p>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn-update">Update profile</button>
            <button class="btn-delete">Delete profile</button>
            <a href="/suppliers" class="btn-cancel">Cancel</a>
        </div>
    </div>
</div>


<style>

/* Basic reset and font */

        /* Header */
        .header-section h1 {
            display: flex;
            justify-content: space-between; /* Align items to the opposite sides */
            align-items: center; /* Vertically align the button with the heading */
            margin-bottom: 20px; /* Add extra space below the content */
        }


        /* Supplier details */
        .details-section p {
            padding: 6px;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .details-section span {
            font-weight: bold;
        }

        /* Action buttons */
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 80px;
        }

        .action-buttons button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-update {
            background-color:#28a745;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-update:hover {
            background-color: #218838;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }
    </style>
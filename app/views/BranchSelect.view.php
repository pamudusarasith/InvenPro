<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>

    <div class="content">
        <!-- Add a wrapper for the button to position it -->
        <div class="top-right-btn">
            <a href="/branch/add" class="add-btn">Add New Branch</a>
        </div>

        <div class="center-container">
            <div class="header-section">
                <h1>Select a Branch</h1>
            </div>

            <div class="branchDropdown">
                <select class="drp" id="branches" name="branches">
                    <option value="default" default>Select branch name</option>
                    <option value="b1">Colombo</option>
                    <option value="b2">Moratuwa</option>
                    <option value="b3">Panadura</option>
                    <option value="b4">Dehiwala</option>
                    <option value="b5">Kaluthra</option>
                </select>
            </div>

            <div class="btn">
                <button type="ok" class="ok">OK</button>
            </div>
        </div>
    </div>
</div>

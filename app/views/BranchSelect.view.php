<div class="body">
    <?php App\View::render("components/Navbar") ?>
    <?php App\View::render("components/Sidebar") ?>

    <div class="content">
        <!-- Flexbox container for heading and dropdown -->
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

<style>
    .content {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .center-container {
        text-align: center;
    }

    .header-section h1 {
        font-size: 32px;
        margin-bottom: 30px;
        color: #333;
    }

    .branchDropdown select {
        padding: 10px;
        font-size: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        width: 400px;
        background-color: #fff;
        margin-bottom: 30px;
    }

    .branchDropdown select:focus {
        border-color: #007bff;
        outline: none;
    }

    .ok {
        background-color:#28a745;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 20px;
    }
</style>

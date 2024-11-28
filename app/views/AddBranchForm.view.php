<!-- AddbranchForm.view.php -->
<div class="container">
    <h2>Add New Branch</h2>

    <form  id ="branch-form" class="form-container" action="" method="POST">

        <div class="form-row">
            <div class="form-group">
                <label for="branch-id">Branch ID</label>
                <input type="text" id="branch-id" name="branch-id" required>
            </div>
            <div class="form-group">
                <label for="branch-name">Branch Name</label>
                <input type="text" id="branch-name" name="branch-name" required>
            </div>
        </div>

        <!-- Product Categories and Products -->
        <div class="form-row">
            <div class="form-group">
                <label for="province">Province</label>
                <select id="province" name="province" required>
                    <option value="" disabled selected>Select a Province</option>
                    <option value="Western">Western</option>
                    <option value="North">North</option>
                    <option value="South">South</option>
                </select>
            </div>
            <div class="form-group">
                <label for="distric">Distric</label>
                <select id="distric" name="distric" required>
                    <option value="" disabled selected>Select a Distric</option>
                    <option value="Western">Kaluthara</option>
                    <option value="North">Gampaha</option>
                    <option value="South">Colombo</option>
                </select>
            </div>
        </div>

        <!-- Address -->
        <div class="form-row">
            <div class="form-group full-width">
                <label for="town">Town</label>
                <input type="text" id="town" name="town" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
        </div>

        <!-- Contact No and Email -->
        <div class="form-row">
            <div class="form-group">
                <label for="contact-no">Branch Contact No</label>
                <input type="text" id="contact-no" name="contact-no" required>
            </div>
            <div class="form-group">
                <label for="email">Branch Email</label>
                <input type="email" id="email" name="email" required>
            </div>
        </div>

        <!-- Buttons -->
        <div class="form-actions">
            <button type="submit" class="save-btn">Save</button>
            <a href="/branches" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>


<dialog id="customer-form-modal" class="modal">
    <div class="modal-content">
        <div class="row">
            <span class="material-symbols-rounded modal-close-btn modal-close">close</span>
        </div>
        <h1 class="modal-header">Add New Customer</h1>
        <form id="customer-form" action="/customer/new" method="post">
            <label for="customer-name">Full Name</label>
            <input id="customer-name" type="text" name="name" required>
            <label for="customer-email">Email</label>
            <input id="customer-email" type="email" name="email" required>
            <label for="customer-phone">Phone Number</label>
            <input id="customer-phone" type="text" name="phone" required>
            <label for="customer-address">Address</label>
            <textarea id="customer-address" name="address" rows="3" required></textarea>
            <label for="customer-dob">Date of Birth</label>
            <input id="customer-dob" type="date" name="dob" required>
            <label for="customer-gender">Gender</label>
            <select id="customer-gender" name="gender" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="M">Male</option>5
                <option value="F">Female</option>
                <option value="O">Other</option>
            </select>

            <div class="modal-error">
                <span class="material-symbols-rounded">error</span>
                <span id="error-msg" class="error-msg"></span>
            </div>
            <div class="row modal-action-btns">
                <span class="loader" style="margin: 24px 12px 0px; font-size: 12px"></span>
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </form>
    </div>
</dialog>



<!-- Edit customer form -->

<dialog id="edit-customer-form-modal" class="modal">
    <div class="modal-content">
        <div class="row">
            <span class="material-symbols-rounded modal-close-btn modal-close">close</span>
        </div>
        <h1 class="modal-header">Edit Customer Profile</h1>
        <form id="edit-customer-form" action="/customer/new" method="post">
            <label for="customer-name">Full Name</label>
            <input id="customer-name" type="text" name="name" required>
            <label for="customer-email">Email</label>
            <input id="customer-email" type="email" name="email" required>
            <label for="customer-phone">Phone Number</label>
            <input id="customer-phone" type="text" name="phone" required>
            <label for="customer-address">Address</label>
            <textarea id="customer-address" name="address" rows="3" required></textarea>
            <label for="customer-dob">Date of Birth</label>
            <input id="customer-dob" type="date" name="dob" required>
            <label for="customer-gender">Gender</label>
            <select id="customer-gender" name="gender" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="M">Male</option>
                <option value="F">Female</option>
                <option value="O">Other</option>
            </select>

            <div class="modal-error">
                <span class="material-symbols-rounded">error</span>
                <span id="error-msg" class="error-msg"></span>
            </div>
            <div class="row modal-action-btns">
                <span class="loader" style="margin: 24px 12px 0px; font-size: 12px"></span>
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</dialog>
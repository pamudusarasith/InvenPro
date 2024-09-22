<div class="navbar">
    <img class="logo" src="/images/logo-light.png" alt="logo">
    <div class="navbar-btns">
        <div class="dropdown" id="notifications-dd">
            <div class="icon-btn" onclick="toggleDropdown('notifications-dd')">
                <span class="material-symbols-rounded">notifications</span>
            </div>
            <div class="dd-content">
                <div class="dd-item">
                    <span>Notification 1</span>
                </div>
                <div class="dd-item" onclick="logout()">
                    <span>Notification 2</span>
                </div>
            </div>
        </div>

        <div class="dropdown" id="profile-dd">
            <div class="icon-btn" onclick="toggleDropdown('profile-dd')">
                <span class="material-symbols-rounded">account_circle</span>
            </div>
            <div class="dd-content">
                <div class="dd-item">
                    <span class="material-symbols-rounded">account_circle</span>
                    <span>Profile</span>
                </div>
                <div class="dd-item" onclick="logout()">
                    <span class="material-symbols-rounded">logout</span>
                    <span>Logout</span>
                </div>
            </div>
        </div>
    </div>
</div>
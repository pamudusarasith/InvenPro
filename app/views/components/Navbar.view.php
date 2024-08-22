<div class="navbar">
    <img class="logo" src="images/logo-light.png" alt="logo">

    <div class="navbar-dropdown" id="notifications-dropdown">
        <div class="icon-btn" onclick="toggleDropdown('notifications-dropdown')">
            <span class="material-symbols-rounded">notifications</span>
        </div>
        <div class="dropdown-content">
            <div class="dropdown-item">
                <span>Notification 1</span>
            </div>
            <div class="dropdown-item" onclick="logout()">
                <span>Notification 2</span>
            </div>
        </div>
    </div>

    <div class="navbar-dropdown" id="profile-dropdown">
        <div class="icon-btn" onclick="toggleDropdown('profile-dropdown')">
            <span class="material-symbols-rounded">account_circle</span>
        </div>
        <div class="dropdown-content">
            <div class="dropdown-item">
                <span class="material-symbols-rounded">account_circle</span>
                <span>Profile</span>
            </div>
            <div class="dropdown-item" onclick="logout()">
                <span class="material-symbols-rounded">logout</span>
                <span>Logout</span>
            </div>
        </div>
    </div>
</div>
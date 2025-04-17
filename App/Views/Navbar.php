<nav class="navbar">
    <div class="navbar-content">
        <div class="navbar-left">
            <button class="menu-toggle" id="menuToggle">
                <span class="icon">menu</span>
            </button>
            <img class="navbar-logo" src="/images/logo-light.png" alt="logo">
        </div>

        <div class="navbar-right">
            <div id="notifications" class="dropdown">
                <button class="dropdown-trigger notification-btn">
                    <span class="icon">notifications</span>
                    <span class="notification-badge">2</span>
                </button>
                <div class="dropdown-menu">
                    <div class="dropdown-header">
                        <h4>Notifications</h4>
                        <button class="mark-all-read">Mark all read</button>
                    </div>
                    <div class="dropdown-items">
                        <div class="notification-item unread">
                            <span class="icon">inventory_2</span>
                            <div class="notification-content">
                                <p>Low stock alert: Product XYZ</p>
                                <span class="notification-time">2 mins ago</span>
                            </div>
                        </div>
                        <div class="notification-item">
                            <span class="icon">local_shipping</span>
                            <div class="notification-content">
                                <p>New order received #12345</p>
                                <span class="notification-time">1 hour ago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="navbar-divider"></div>

            <div id="profile" class="dropdown">
                <button class="dropdown-trigger profile-btn">
                    <div class="profile-info">
                        <p class="profile-name"><?= $_SESSION['user']["display_name"] ?></p>
                        <p class="profile-role"><?= $_SESSION['user']["role_name"] ?></p>
                    </div>
                    <span class="icon">expand_more</span>
                </button>
                <div class="dropdown-menu">
                    <a href="/profile" class="dropdown-item">
                        <span class="icon">account_circle</span>
                        <span>My Profile</span>
                    </a>
                    <a href="/logout" class="dropdown-item">
                        <span class="icon">logout</span>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    if (location.pathname === '/pos') {
        document.querySelector('#menuToggle').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('menuToggle');
        const sidebar = document.querySelector('.sidebar');

        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
        });
    });
</script>

<nav class="navbar">
    <div class="navbar-content">
        <div class="navbar-left">
            <img class="logo" src="/images/logo-light.png" alt="logo">
        </div>

        <div class="navbar-right">
            <div id="notifications" class="dropdown">
                <button class="notification-btn">
                    <span class="material-symbols-rounded">notifications</span>
                    <span class="notification-badge">2</span>
                </button>
                <div class="dropdown-menu">
                    <div class="dropdown-header">
                        <h4>Notifications</h4>
                        <button class="mark-all-read">Mark all read</button>
                    </div>
                    <div class="dropdown-items">
                        <div class="notification-item unread">
                            <span class="material-symbols-rounded">inventory_2</span>
                            <div class="notification-content">
                                <p>Low stock alert: Product XYZ</p>
                                <span class="notification-time">2 mins ago</span>
                            </div>
                        </div>
                        <div class="notification-item">
                            <span class="material-symbols-rounded">local_shipping</span>
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
                <button class="profile-btn">
                    <div class="profile-info">
                        <p class="profile-name">John Doe</p>
                        <p class="profile-role">Branch Manager</p>
                    </div>
                    <span class="material-symbols-rounded">expand_more</span>
                </button>
                <div class="dropdown-menu">
                    <a href="/profile" class="dropdown-item">
                        <span class="material-symbols-rounded">account_circle</span>
                        <span>My Profile</span>
                    </a>
                    <button class="dropdown-item" onclick="handleLogout()">
                        <span class="material-symbols-rounded">logout</span>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    .navbar {
        background: var(--glass-white);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid var(--border-light);
        z-index: 100;
    }

    .navbar-content {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    .navbar-left {
        height: 100%;
    }

    .navbar-left img {
        height: 0;
        width: auto;
        min-height: 100%;
        object-fit: contain;
        padding: 0.5rem;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .nav-item {
        position: relative;
    }

    /* Notification Styles */
    .notification-btn {
        padding: 8px;
        aspect-ratio: 1/1;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: var(--text-secondary);
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
    }

    .notification-btn:hover {
        background: var(--secondary-50);
        color: var(--text-primary);
    }

    .notification-badge {
        position: absolute;
        top: 4px;
        right: 4px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 9px;
        background: var(--danger-500);
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Profile Styles */
    .profile-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem;
        border: none;
        border-radius: 8px;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .profile-btn:hover {
        background: var(--secondary-50);
    }

    .profile-info {
        text-align: left;
    }

    .profile-name {
        color: var(--text-primary);
        font-weight: 500;
    }

    .profile-role {
        color: var(--text-secondary);
        font-size: 0.75rem;
    }

    /* Dropdown Styles */
    .dropdown-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        min-width: 240px;
        background: var(--surface-white);
        border-radius: 12px;
        box-shadow: var(--shadow-lg);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s ease;
    }

    .dropdown.active .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-header {
        padding: 1rem;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dropdown-header h4 {
        color: var(--text-primary);
        font-weight: 600;
    }

    .mark-all-read {
        color: var(--primary-600);
        background: none;
        border: none;
        font-size: 0.875rem;
        cursor: pointer;
    }

    .dropdown-items {
        max-height: 300px;
        overflow-y: auto;
    }

    .notification-item {
        padding: 1rem;
        display: flex;
        gap: 0.75rem;
        transition: background 0.2s ease;
    }

    .notification-item:hover {
        background: var(--secondary-50);
    }

    .notification-item.unread {
        background: var(--primary-50);
    }

    .notification-content p {
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .notification-time {
        color: var(--text-tertiary);
        font-size: 0.75rem;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: var(--text-primary);
        text-decoration: none;
        transition: background 0.2s ease;
    }

    .dropdown-item:hover {
        background: var(--secondary-50);
    }

    .navbar-divider {
        width: 1px;
        height: 60%;
        background: var(--border-light);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropdowns = document.querySelectorAll('.dropdown');

        dropdowns.forEach(dropdown => {
            const trigger = dropdown.querySelector('button');

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();

                // Close other dropdowns
                dropdowns.forEach(d => {
                    if (d !== dropdown) {
                        d.classList.remove('active');
                    }
                });

                dropdown.classList.toggle('active');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', () => {
            dropdowns.forEach(d => d.classList.remove('active'));
        });

        // Prevent closing when clicking inside dropdown
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.addEventListener('click', (e) => e.stopPropagation());
        });
    });

    function handleLogout() {
        // Add logout logic here
        console.log('Logging out...');
    }
</script>
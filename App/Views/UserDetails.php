<?php

use App\Services\RBACService;
$roles = $roles ?? [];
$branches = $branches ?? [];
$activities = $activities ?? [];
?>

<div class="body">
    <?php App\Core\View::render("Navbar") ?>
    <?php App\Core\View::render("Sidebar") ?>

    <div class="main">
        <div class="profile-container">
            <div class="details-header">
                <div class="details-header-left">
                    <div class="details-avatar">
                        <span class="icon">person</span>
                    </div>
                    <div class="profile-info">
                        <div class="details-title">
                            <h1 class="title-name"><?= htmlspecialchars($user['display_name']) ?></h1>
                            <span class="badge <?= $user['is_locked'] ? 'danger' : 'success' ?>">
                                <?= htmlspecialchars($user['status']) ?>
                            </span>
                        </div>
                        <div class="details-meta">
                            <div class="meta-item">
                                <span class="icon">email</span>
                                <span class="meta-text"><?= htmlspecialchars($user['email']) ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="icon">badge</span>
                                <span class="meta-text"><?= ucfirst($user['role_name']) ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="icon">store</span>
                                <span class="meta-text"><?= htmlspecialchars($user['branch_name']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="profile-header-right">
                    <div class="edit-actions">
                        <button class="btn btn-secondary" onclick="cancelEdit()">
                            <span class="icon">close</span>
                            Cancel
                        </button>
                        <button class="btn btn-primary" onclick="saveChanges()">
                            <span class="icon">save</span>
                            Save
                        </button>
                    </div>
                    <?php
                    // Debug logging for user and session IDs
                    error_log('Debug User ID: ' . (isset($user['id']) ? $user['id'] : 'Not set'));
                    error_log('Debug Session User ID: ' . (isset($_SESSION['id']) ? $_SESSION['id'] : 'Not set'));
                    ?>
                    <?php if (RBACService::hasPermission('edit_user') || RBACService::hasPermission('delete_user')): ?>
                        <div class="dropdown">
                            <button class="dropdown-trigger icon-btn" title="More options">
                                <span class="icon">more_vert</span>
                            </button>
                            <div class="dropdown-menu">
                                <?php if (RBACService::hasPermission('edit_user')): ?>
                                    <button class="dropdown-item" onclick="enableEditing()">
                                        <span class="icon">edit</span>
                                        Edit Profile
                                    </button>
                                <?php endif; ?>
                                <?php if (RBACService::hasPermission('delete_user') && isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != $user['id']): ?>
                                    <button class="dropdown-item danger" onclick="deleteUser(<?= $user['id'] ?>)">
                                        <span class="icon">delete</span>
                                        Delete User
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="icon">event</span>
                        <span class="stat-label">Member Since</span>
                    </div>
                    <div class="stat-value"><?= date('M d, Y', strtotime($user['created_at'])) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="icon">login</span>
                        <span class="stat-label">Last Login</span>
                    </div>
                    <div class="stat-value">
                        <?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never' ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="icon">location_on</span>
                        <span class="stat-label">Last Login IP</span>
                    </div>
                    <div class="stat-value"><?= $user['last_login_ip'] ?? 'N/A' ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="icon">warning</span>
                        <span class="stat-label">Failed Attempts</span>
                    </div>
                    <div class="stat-value"><?= $user['failed_login_attempts'] ?></div>
                </div>
            </div>
        </div>

        <div class="tab-nav">
            <button class="tab-btn active" onclick="switchTab('overview')">Overview</button>
            <button class="tab-btn" onclick="switchTab('security')">Security</button>
            <button class="tab-btn" onclick="switchTab('activity')">Activity Log</button>
        </div>

        <form id="details-form" method="POST" action="/users/<?= $user['id'] ?>/update">
            <div id="overview" class="tab-content active">
                <div class="card">
                    <h3>Account Information</h3>
                    <div class="content form-grid">
                        <div class="form-field">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" disabled>
                        </div>
                        <div class="form-field">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" disabled>
                        </div>
                        <div class="form-field span-2">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3>Branch Assignment</h3>
                    <div class="content form-grid">
                        <div class="form-field span-2">
                            <label for="branch">Branch</label>
                            <select id="branch" name="branch_id" disabled>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= $branch['branch_name'] === $user['branch_name'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($branch['branch_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="assignment-history span-2">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Branch</th>
                                        <th>Assigned Date</th>
                                        <th>Assigned By</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Branch history -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="security" class="tab-content">
                <div class="card">
                    <h3>Security Settings</h3>
                    <div class="content security-list">
                        <div class="security-item">
                            <div class="security-info">
                                <h4>Password</h4>
                                <p>Last changed 30 days ago</p>
                            </div>
                            <button class="btn btn-secondary">Reset Password</button>
                        </div>
                        <div class="security-item">
                            <div class="security-info">
                                <h4>Login History</h4>
                                <p>View your recent login activity</p>
                            </div>
                            <button class="btn btn-secondary">View History</button>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3>Access Control</h3>
                    <div class="content form-grid">
                        <div class="form-field">
                            <label for="status">Account Status</label>
                            <select id="status" name="status" disabled>
                                <option value="active" <?= !$user['is_locked'] ? 'selected' : '' ?>>Active</option>
                                <option value="locked" <?= $user['is_locked'] ? 'selected' : '' ?>>Locked</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="role">Role Assignment</label>
                            <select id="role" name="role_id" disabled>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= $role['role_name'] === $user['role_name'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="activity" class="tab-content">
                <div class="card">
                    <h3>Activity Log</h3>
                    <div class="activity-list">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Table</th>
                                    <th>Action</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activities as $activity): ?>
                                    <?php
                                    // Parse metadata JSON
                                    $metadata = json_decode($activity['metadata'], true);
                                    $ip = htmlspecialchars($metadata['ip'] ?? 'N/A');
                                    $table_name = htmlspecialchars($activity['table_name'] ?? 'N/A');
                                    $user_agent = htmlspecialchars($metadata['user_agent'] ?? 'N/A');
                                    // Handle empty action_type
                                    $action_type = htmlspecialchars($activity['action_type'] ?: 'Unknown');
                                    // Format timestamp
                                    $timestamp = date('M d, Y H:i', strtotime($activity['created_at']));
                                    ?>
                                    <tr>
                                        <td><?= $timestamp ?></td>
                                        <td><?= $table_name ?></td>
                                        <td><?= $action_type ?></td>
                                        <td><?= $ip ?></td>
                                        <td><?= $user_agent ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function switchTab(tabId) {
        // Remove active class from all tabs and contents
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        // Add active class to selected tab and content
        document.querySelector(`.tab-btn[onclick*="${tabId}"]`).classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }

    function enableEditing() {
        // Add edit mode class to header
        document.querySelector('.details-header').classList.add('edit-mode');

        // Enable all form inputs
        document.querySelectorAll('.form-field :is(input, select, textarea)').forEach(input => {
            input.disabled = false;
        });

        // Scroll to form
        document.querySelector('.tab-content.active').scrollIntoView({
            behavior: 'smooth'
        });
    }

    function cancelEdit() {
        if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
            location.reload();
        }
    }

    // User actions
    function deleteUser(userId) {
        if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            return;
        }
        // Implement delete user logic
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = '/users/<?= $user['id'] ?>/delete';
        document.body.appendChild(form);
        form.submit();
    }

    function saveChanges() {
        if (!confirm('Are you sure you want to save these changes?')) {
            return;
        }
        // Implement save changes logic
        const form = document.getElementById('details-form');
        form.submit();
    }
</script>

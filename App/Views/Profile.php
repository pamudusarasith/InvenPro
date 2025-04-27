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
                    //error_log('Debug User ID: ' . (isset($user['id']) ? $user['id'] : 'Not set'));
                    //error_log('Debug Session User ID: ' . (isset($_SESSION['id']) ? $_SESSION['id'] : 'Not set'));
                    ?>
                    <?php if (isset($_SESSION['user']['id']) && ($_SESSION['user']['id'] == $user['id'])): ?>
                        <div class="dropdown">
                            <button class="dropdown-trigger icon-btn" title="More options">
                                <span class="icon">more_vert</span>
                            </button>
                            <div class="dropdown-menu">
                                <?php if ((isset($_SESSION['user']['id']) && ($_SESSION['user']['id'] == $user['id']))): ?>
                                    <button class="dropdown-item" onclick="enableEditing()">
                                        <span class="icon">edit</span>
                                        Edit Profile
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

        <form id="details-form" method="POST" action="/profile/update">
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
                        <div class="form-field span-2 AccessControl">
                            <label for="branch">Branch</label>
                            <select id="branch" name="branch_id" disabled>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= $branch['branch_name'] === $user['branch_name'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($branch['branch_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
                            </div>
                            <?php if (isset($_SESSION['user']['id']) && ($_SESSION['user']['id'] == $user['id'] || $_SESSION['user']['id'] == 1)): ?>
                                <button type="button" class="btn btn-secondary" onclick="openPasswordResetDialog()">Reset Password</button>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>Reset Password</button>
                                <?php
                                if (!isset($_SESSION['error'])) {
                                    $_SESSION['error'] = 'You do not have permission to reset this password.';
                                    $_SESSION['error_type'] = 'error'; // Optional: Define the type of message
                                }
                                ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3>Access Control</h3>
                    <div class="content form-grid">
                        <div class="form-field AccessControl">
                            <label for="status">Account Status</label>
                            <select id="status" name="status" disabled>
                                <option value="active" <?= !$user['is_locked'] ? 'selected' : '' ?>>Active</option>
                                <option value="locked" <?= $user['is_locked'] ? 'selected' : '' ?>>Locked</option>
                            </select>
                        </div>
                        <div class="form-field AccessControl">
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
                                    <th>User Agent</th>
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

<?php if (isset($_SESSION['user']['id']) && ($_SESSION['user']['id'] == $user['id'] || $_SESSION['user']['id'] == 1)): ?>
    <!-- Password Reset Dialog -->
    <dialog id="passwordResetDialog" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Reset Password</h2>
                <button class="close-btn" onclick="closePasswordResetDialog()">
                    <span class="icon">close</span>
                </button>
            </div>

            <form id="passwordResetForm" method="POST" action="/profile/reset-password" onsubmit="validateFrom(event);">
                <div class="form-grid">
                    <div class="form-field">
                        <label for="new_password">Old Password</label>
                        <input type="password" id="old_password" name="old_password" required>
                    </div>
                    <div class="form-field">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-field">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closePasswordResetDialog()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </dialog>
<?php endif; ?>

<!-- Include any additional scripts here -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="card glass notification <?= htmlspecialchars($_SESSION['message_type']) ?>">
        <p><?= htmlspecialchars($_SESSION['message']) ?></p>
        <button class="close-btn" onclick="this.parentElement.remove()">✕</button>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>


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

        // Enable all inputs except those in AccessControl
        document.querySelectorAll('.form-field:not(.AccessControl) :is(input, select, textarea)').forEach(input => {
            input.disabled = false;
        });

        // Then handle AccessControl fields based on permission
        <?php if (RBACService::hasPermission('delete_user') && isset($_SESSION['user']['id']) && $_SESSION['user']['id'] != $user['id']): ?>
            document.querySelectorAll('.form-field.AccessControl :is(input, select, textarea)').forEach(input => {
                input.disabled = false;
            });
        <?php else: ?>
            document.querySelectorAll('.form-field.AccessControl :is(input, select, textarea)').forEach(input => {
                input.disabled = true;
            });
        <?php endif; ?>


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

    function saveChanges() {
        if (!confirm('Are you sure you want to save these changes?')) {
            return;
        }

        // Implement save changes logic
        const form = document.getElementById('details-form');

        const accessControlFields = document.querySelectorAll('.form-field.AccessControl :is(input, select)');
        accessControlFields.forEach(field => {
            if (field.disabled) {
                // Add the field's name and value to a hidden input
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = field.name;
                hiddenInput.value = field.value;
                form.appendChild(hiddenInput);
            }
        });

        form.submit();
    }



    // Password Reset Dialog Functions
    <?php if (isset($_SESSION['user']['id']) && ($_SESSION['user']['id'] == $user['id'] || $_SESSION['user']['id'] == 1)): ?>

        function openPasswordResetDialog() {
            const dialog = document.getElementById('passwordResetDialog');
            // Reset form
            document.getElementById('passwordResetForm').reset();
            dialog.showModal();
        }

        function closePasswordResetDialog() {
            const dialog = document.getElementById('passwordResetDialog');
            dialog.close();
        }

        function addErrorMessage(field, message) {
            field.classList.add('error');
            let errorMessage = document.createElement('span');
            errorMessage.classList.add('error-message');
            errorMessage.innerText = message;
            field.appendChild(errorMessage);
        }

        function validateFrom(event) {

            const form = event.target;
            const oldPassword = form.querySelector('#old_password');
            const newPassword = form.querySelector('#new_password');
            const confirmPassword = form.querySelector('#confirm_password');
            const submitButton = form.querySelector('button[type="submit"]');

            // Clear existing errors
            const errorFields = form.querySelectorAll('.error');
            errorFields.forEach(field => {
                field.classList.remove('error');
                field.querySelector('.error-message')?.remove();
            });

            // Validate old password
            if (oldPassword.value.trim() === '') {
                showError(oldPassword, 'Old password is required!');
                return;
            }

            // Validate new password
            if (newPassword.value.trim() === '') {
                showError(newPassword, 'New password is required!');
                return;
            } else if (newPassword.value.length < 8) {
                showError(newPassword, 'Password must be at least 8 characters long!');
                return;
            }

            // Validate confirm password
            if (confirmPassword.value.trim() === '') {
                showError(confirmPassword, 'Confirm password is required!');
                return;
            } else if (confirmPassword.value !== newPassword.value) {
                showError(confirmPassword, 'Passwords do not match!');
                return;
            }
        }

    <?php endif; ?>
</script>
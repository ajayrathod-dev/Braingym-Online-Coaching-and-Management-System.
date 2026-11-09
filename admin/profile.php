<?php
include 'includes/header.php';
require_once 'auth_check.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... logic remains ...
}
?>
<div class="admin-content">
                <div class="profile-container">
                    <!-- Left Column: Profile Information -->
                    <div class="profile-card">
                        <div class="profile-header">
                            <div class="profile-pic">
                                <?= strtoupper(substr($_SESSION['admin_username'], 0, 1)) ?>
                            </div>
                            <div class="profile-info">
                                <h1><?= $admin['username'] ?></h1>
                                <p><?= $admin['email'] ?></p>
                                <p><i class="fas fa-shield-alt"></i> <?= ucfirst($admin['role']) ?> Administrator</p>
                            </div>
                        </div>
                        
                        <h2 class="section-title">Profile Information</h2>
                        
                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="alert alert-error">
                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" value="<?= $admin['username'] ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" value="<?= $admin['email'] ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Admin Role</label>
                                <input type="text" value="<?= ucfirst($admin['role']) ?> Administrator" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label>Member Since</label>
                                <input type="text" value="<?= date('M d, Y', strtotime($admin['created_at'])) ?>" disabled>
                            </div>
                            
                            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                    
                    <!-- Right Column: Security Settings -->
                    <div class="profile-card">
                        <h2 class="section-title">Security Settings</h2>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" name="current_password" required>
                            </div>
                            
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" id="newPassword" required>
                                <div class="password-strength">
                                    <div class="strength-meter" id="strengthMeter"></div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Two-Factor Authentication</label>
                                <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    <span>Enabled</span>
                                </div>
                            </div>
                            
                            <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                        </form>
                        
                        <h2 class="section-title" style="margin-top: 2rem;">Recent Activity</h2>
                        
                        <div class="activity-list">
                            <?php while($activity = $activity_result->fetch_assoc()): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <?php switch($activity['action_type']):
                                        case 'login': ?>
                                            <i class="fas fa-sign-in-alt"></i>
                                            <?php break;
                                        case 'update': ?>
                                            <i class="fas fa-edit"></i>
                                            <?php break;
                                        case 'create': ?>
                                            <i class="fas fa-plus-circle"></i>
                                            <?php break;
                                        case 'delete': ?>
                                            <i class="fas fa-trash-alt"></i>
                                            <?php break;
                                        default: ?>
                                            <i class="fas fa-history"></i>
                                    <?php endswitch; ?>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-text"><?= $activity['description'] ?></div>
                                    <div class="activity-time"><?= date('M j, Y \a\t g:i a', strtotime($activity['timestamp'])) ?></div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                            
                            <?php if($activity_result->num_rows === 0): ?>
                                <p>No recent activity found</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Password strength indicator
        document.getElementById('newPassword').addEventListener('input', function() {
            const password = this.value;
            const strengthMeter = document.getElementById('strengthMeter');
            let strength = 0;
            
            // Check password length
            if (password.length >= 8) strength += 25;
            if (password.length >= 12) strength += 25;
            
            // Check for uppercase letters
            if (/[A-Z]/.test(password)) strength += 25;
            
            // Check for numbers and special characters
            if (/[0-9]/.test(password)) strength += 15;
            if (/[^A-Za-z0-9]/.test(password)) strength += 10;
            
            // Update strength meter
            strengthMeter.style.width = strength + '%';
            
            // Update strength meter color
            strengthMeter.className = 'strength-meter';
            if (strength < 50) {
                strengthMeter.classList.add('strength-weak');
            } else if (strength < 80) {
                strengthMeter.classList.add('strength-medium');
            } else {
                strengthMeter.classList.add('strength-strong');
            }
        });
    </script>
</body>
</html>
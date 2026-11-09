<?php
include 'includes/header.php';
require_once 'auth_check.php';

// Handle form submission
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_settings'])) {
    // Validate and sanitize inputs
    $settings = [
        'appointments_enabled' => isset($_POST['appointments_enabled']) ? 1 : 0,
        'appointments_start_time' => $_POST['appointments_start_time'],
        'appointments_end_time' => $_POST['appointments_end_time'],
        'max_appointments_per_day' => filter_var($_POST['max_appointments_per_day'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 100]
        ]),
        'competitions_visible' => isset($_POST['competitions_visible']) ? 1 : 0,
        'auto_close_competitions' => isset($_POST['auto_close_competitions']) ? 1 : 0,
        'default_notice_expiry' => filter_var($_POST['default_notice_expiry'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 365]
        ]),
        'max_file_size' => filter_var($_POST['max_file_size'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 50]
        ])
    ];

    // Validate time range
    if (strtotime($settings['appointments_start_time']) >= strtotime($settings['appointments_end_time'])) {
        $errors[] = "End time must be after start time";
    }

    // Handle validation errors
    foreach ($settings as $key => $value) {
        if ($value === false) {
            $errors[] = "Invalid value for " . str_replace('_', ' ', $key);
        }
    }

    if (empty($errors)) {
        foreach ($settings as $key => $value) {
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) 
                                  VALUES (?, ?) 
                                  ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->bind_param("sss", $key, $value, $value);
            $stmt->execute();
        }
        $success = "Settings updated successfully";
    }
}

// Load current settings
$result = $conn->query("SELECT * FROM settings");
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}


?>

<div class="admin-content">
    <div class="content-header">
        <h1>System Configuration</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="settings-form">
        <!-- Appointment System Section -->
        <div class="settings-section">
            <h3><i class="fas fa-calendar-alt"></i> Appointment Settings</h3>
            
            <div class="form-group">
                <label class="toggle-switch">
                    <input type="checkbox" name="appointments_enabled" 
                        <?= !empty($settings['appointments_enabled']) ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
                Enable Appointment System
            </div>

            <div class="time-range">
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="appointments_start_time" 
                           value="<?= htmlspecialchars($settings['appointments_start_time'] ?? '09:00') ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="appointments_end_time" 
                           value="<?= htmlspecialchars($settings['appointments_end_time'] ?? '17:00') ?>" 
                           required>
                </div>
            </div>

            <div class="form-group">
                <label>Max Appointments/Day</label>
                <input type="number" name="max_appointments_per_day" 
                       value="<?= htmlspecialchars($settings['max_appointments_per_day'] ?? 20) ?>" 
                       min="1" max="100" required>
            </div>
        </div>

        <!-- Competition Settings -->
        <div class="settings-section">
            <h3><i class="fas fa-trophy"></i> Competition Settings</h3>
            
            <div class="form-group">
                <label class="toggle-switch">
                    <input type="checkbox" name="competitions_visible" 
                        <?= !empty($settings['competitions_visible']) ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
                Visible to Students
            </div>

            <div class="form-group">
                <label class="toggle-switch">
                    <input type="checkbox" name="auto_close_competitions" 
                        <?= !empty($settings['auto_close_competitions']) ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
                Auto-close Ended Competitions
            </div>
        </div>

        <!-- Notice & File Settings -->
        <div class="settings-section">
            <h3><i class="fas fa-file-upload"></i> Content Settings</h3>
            
            <div class="form-group">
                <label>Default Notice Expiry (days)</label>
                <input type="number" name="default_notice_expiry" 
                       value="<?= htmlspecialchars($settings['default_notice_expiry'] ?? 30) ?>" 
                       min="1" max="365" required>
            </div>

            <div class="form-group">
                <label>Max File Size (MB)</label>
                <input type="number" name="max_file_size" 
                       value="<?= htmlspecialchars($settings['max_file_size'] ?? 5) ?>" 
                       min="1" max="50" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="save_settings" class="btn-save">
                <i class="fas fa-save"></i> Save All Settings
            </button>
        </div>
    </form>
</div>

<style>
.settings-form {
    background: white;
    padding: 2rem;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.settings-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.settings-section:last-child {
    border-bottom: none;
}

.time-range {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin: 1.5rem 0;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
    margin-right: 1rem;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #4f46e5;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.form-group {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.form-group label {
    width: 250px;
    font-weight: 500;
}

.form-group input[type="number"],
.form-group input[type="time"] {
    width: 200px;
    padding: 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
}
</style>

<?php include 'includes/footer.php'; ?>
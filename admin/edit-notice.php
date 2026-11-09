<?php
session_start();
require_once 'auth_check.php';
require_once __DIR__ . '/../connection.php';

// Get notice ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch notice data
$stmt = $conn->prepare("SELECT * FROM notices WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$notice = $result->fetch_assoc();

if (!$notice) {
    $_SESSION['error'] = "Notice not found";
    header("Location: notices.php");
    exit();
}

$errors = [];
$old_file = $notice['file_path'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $expiry_date = $_POST['expiry_date'];
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $delete_file = isset($_POST['delete_file']);

    // Validate inputs
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    if (empty($content)) {
        $errors[] = "Content is required";
    }

    // File handling
    $file_path = $notice['file_path'];
    
    if ($delete_file && $file_path) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $file_path = null;
    }

    if ($_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        // Validate new file
        $allowed_types = [
            'application/pdf', 
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $detected_type = finfo_file($file_info, $_FILES['attachment']['tmp_name']);
        finfo_close($file_info);

        if (!in_array($detected_type, $allowed_types)) {
            $errors[] = "Only PDF and Word documents allowed";
        }

        if ($_FILES['attachment']['size'] > 10 * 1024 * 1024) {
            $errors[] = "File size exceeds 10MB limit";
        }

        if (empty($errors)) {
            // Delete old file
            if ($file_path && file_exists($file_path)) {
                unlink($file_path);
            }

            // Upload new file
            $upload_dir = 'uploads/notices/';
            $file_name = uniqid('notice_') . '_' . basename($_FILES['attachment']['name']);
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_path)) {
                $file_path = $target_path;
            } else {
                $errors[] = "Failed to upload file";
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("
                UPDATE notices SET
                    title = ?,
                    content = ?,
                    file_path = ?,
                    expiry_date = ?,
                    is_published = ?,
                    publish_date = IF(is_published <> ? AND ? = 1, NOW(), publish_date)
                WHERE id = ?
            ");
            
            $stmt->bind_param("sssssiii", 
                $title,
                $content,
                $file_path,
                $expiry_date,
                $is_published,
                $is_published,
                $is_published,
                $id
            );

            if ($stmt->execute()) {
                $_SESSION['message'] = "Notice updated successfully";
                header("Location: notices.php");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <h1>Edit Notice</h1>
        <a href="notices.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Notices
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="notice-form">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" 
                   value="<?= htmlspecialchars($notice['title']) ?>" 
                   required>
        </div>

        <div class="form-group">
            <label>Content *</label>
            <textarea name="content" rows="6" required><?= 
                htmlspecialchars($notice['content']) 
            ?></textarea>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Expiry Date</label>
                <input type="date" name="expiry_date" 
                       value="<?= $notice['expiry_date'] ?>">
                <small>Leave empty for no expiry</small>
            </div>

            <div class="form-group">
                <label>Current Attachment:</label>
                <?php if ($notice['file_path']): ?>
                    <div class="file-info">
                        <a href="<?= $notice['file_path'] ?>" target="_blank">
                            <i class="fas fa-file-download"></i> 
                            <?= basename($notice['file_path']) ?>
                        </a>
                        <label class="delete-check">
                            <input type="checkbox" name="delete_file">
                            Remove attachment
                        </label>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No attachment</p>
                <?php endif; ?>
                
                <div class="new-file">
                    <label>Upload New Attachment:</label>
                    <input type="file" name="attachment" 
                           accept=".pdf,.doc,.docx">
                    <small>Leave blank to keep current file</small>
                </div>
            </div>
        </div>

        <div class="form-group check-group">
            <label>
                <input type="checkbox" name="is_published" 
                    <?= $notice['is_published'] ? 'checked' : '' ?>>
                Publish Notice
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Update Notice
            </button>
        </div>
    </form>
</div>

<style>
.file-info {
    margin-bottom: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
}

.delete-check {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.9rem;
}

.new-file {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.text-muted {
    color: #6c757d;
    font-style: italic;
}
</style>

<?php include 'includes/footer.php'; ?>
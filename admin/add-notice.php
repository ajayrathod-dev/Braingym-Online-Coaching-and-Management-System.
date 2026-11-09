<?php
session_start();
require_once __DIR__ . '/../connection.php';
require_once 'auth_check.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
$old_data = [
    'title' => '',
    'content' => '',
    'expiry_date' => '',
    'is_published' => 1
];

// Handle form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $expiry_date = $_POST['expiry_date'] ?? null;
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    if (empty($title)) $errors['title'] = "Title is required";
    if (empty($content)) $errors['content'] = "Content is required";

    // File Upload
    $file_path = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $detected_type = finfo_file($file_info, $_FILES['attachment']['tmp_name']);
        finfo_close($file_info);

        if (!in_array($detected_type, $allowed_types)) {
            $errors['attachment'] = "Only PDF and Word files are allowed.";
        } elseif ($_FILES['attachment']['size'] > 5 * 1024 * 1024) {
            $errors['attachment'] = "Max file size is 5MB.";
        } else {
            $upload_dir = 'uploads/notices/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('notice_') . '.' . $ext;
            $target_path = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_path)) {
                $file_path = $target_path; // Save relative path for web access
            } else {
                $errors['attachment'] = "Upload failed. Try again.";
            }
        }
    }

    // Insert if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO notices (title, content, file_path, expiry_date, is_published) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $content, $file_path, $expiry_date, $is_published);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Notice published successfully.";
            header("Location: notices.php");
            exit();
        } else {
            $errors['database'] = "Error: " . $conn->error;
        }
    }

    // Preserve inputs
    $old_data = [
        'title' => $title,
        'content' => $content,
        'expiry_date' => $expiry_date,
        'is_published' => $is_published
    ];
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <h1>Create New Notice</h1>
        <a href="notices.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Notices
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <p><?= $e ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="notice-form">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" value="<?= htmlspecialchars($old_data['title']) ?>" required>
        </div>

        <div class="form-group">
            <label>Content *</label>
            <textarea name="content" rows="6" required><?= htmlspecialchars($old_data['content']) ?></textarea>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Expiry Date</label>
                <input type="date" name="expiry_date" value="<?= $old_data['expiry_date'] ?>">
                <small>Optional – leave blank if permanent</small>
            </div>

            <div class="form-group">
                <label>Attachment (PDF/DOC – max 5MB)</label>
                <input type="file" name="attachment" accept=".pdf,.doc,.docx">
                <?php if (isset($errors['attachment'])): ?>
                    <span class="error"><?= $errors['attachment'] ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group check-group">
            <label>
                <input type="checkbox" name="is_published" <?= $old_data['is_published'] ? 'checked' : '' ?>>
                Publish Immediately
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Save Notice
            </button>
        </div>
    </form>
</div>

<style>
.notice-form {
    background: white;
    padding: 2rem;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    max-width: 800px;
    margin: 0 auto;
}
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin: 1.5rem 0;
}
.form-group input[type="text"],
.form-group input[type="date"],
.form-group input[type="file"],
.form-group textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    font-size: 1rem;
    font-family: inherit;
}
.form-group textarea {
    resize: vertical;
}
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #1a2236;
}
.check-group {
    margin: 1rem 0;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
}
.check-group label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.check-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
}
.error {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
small {
    color: #6c757d;
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
}
</style>

<?php include 'includes/footer.php'; ?>

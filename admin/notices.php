<?php
include 'includes/header.php';
require_once 'auth_check.php';

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    
    // Delete file if exists
    $stmt = $conn->prepare("SELECT file_path FROM notices WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $file_path = $stmt->get_result()->fetch_assoc()['file_path'];
    
    if ($file_path && file_exists($file_path)) {
        unlink($file_path);
    }

    // Delete record
    $stmt = $conn->prepare("DELETE FROM notices WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $_SESSION['message'] = "Notice deleted successfully";
    header("Location: notices.php");
    exit();
}

// Handle Toggle Publish
if (isset($_GET['toggle_publish'])) {
    $id = intval($_GET['toggle_publish']);
    $stmt = $conn->prepare("UPDATE notices SET is_published = NOT is_published WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: notices.php");
    exit();
}

// Fetch all notices
$result = $conn->query("SELECT * FROM notices ORDER BY publish_date DESC");
?>

<div class="admin-content">
    <div class="content-header">
        <div class="header-title">
            <h1>Manage Notices</h1>
            <p>Create and manage important announcements</p>
        </div>
        <a href="add-notice.php" class="btn-primary">
            <i class="fas fa-plus"></i> New Notice
        </a>
    </div>

    <div class="notices-container">
        <?php while($notice = $result->fetch_assoc()): ?>
        <div class="notice-card <?= $notice['is_published'] ? 'published' : 'draft' ?>">
            <div class="notice-header">
                <h3><?= htmlspecialchars($notice['title']) ?></h3>
                <div class="notice-meta">
                    <span class="status-badge">
                        <?= $notice['is_published'] ? 'Published' : 'Draft' ?>
                    </span>
                    <span class="date">
                        <?= date('M d, Y h:i A', strtotime($notice['publish_date'])) ?>
                    </span>
                </div>
            </div>
            
            <div class="notice-content">
                <?= nl2br(htmlspecialchars($notice['content'])) ?>
                
                <?php if($notice['file_path']): ?>
                <div class="notice-attachment">
                    <i class="fas fa-paperclip"></i>
                    <a href="<?= $notice['file_path'] ?>" target="_blank">
                        Download Attachment
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <div class="notice-actions">
                <a href="edit-notice.php?id=<?= $notice['id'] ?>" class="btn-edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form method="POST">
                    <input type="hidden" name="delete_id" value="<?= $notice['id'] ?>">
                    <button type="submit" class="btn-delete" 
                        onclick="return confirm('Permanently delete this notice?')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                <a href="notices.php?toggle_publish=<?= $notice['id'] ?>" 
                   class="btn-toggle">
                    <?= $notice['is_published'] ? 'Unpublish' : 'Publish' ?>
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<style>
.notices-container {
    display: grid;
    gap: 1.5rem;
}

.notice-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.notice-card.draft {
    background: #fff9db;
    border-left: 4px solid #ffd43b;
}

.notice-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.notice-meta {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    background: #e9ecef;
    color: #495057;
}

.published .status-badge {
    background: #d3f9d8;
    color: #2b8a3e;
}

.notice-attachment {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

.notice-actions {
    margin-top: 1.5rem;
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-toggle {
    background: none;
    color: #4f46e5;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    border: 1px solid #4f46e5;
    transition: all 0.2s;
}

.btn-toggle:hover {
    background: #4f46e5;
    color: white;
}
</style>

<?php include 'includes/footer.php'; ?>
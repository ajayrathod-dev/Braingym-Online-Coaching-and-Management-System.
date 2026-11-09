<?php
session_start();
require_once __DIR__ . '/connection.php';
include 'header.php'; // Student header

$current_date = date('Y-m-d');
$result = $conn->query("
    SELECT * FROM notices 
    WHERE is_published = 1 
    AND (expiry_date IS NULL OR expiry_date >= '$current_date')
    ORDER BY publish_date DESC
");
?>

<div class="student-content">
    <h1 class="section-title">📢 Latest Notices</h1>

    <?php if ($result->num_rows > 0): ?>
    <div class="notices-grid">
        <?php while($notice = $result->fetch_assoc()): ?>
        <div class="notice-card">
            <div class="notice-header">
                <h3><?= htmlspecialchars($notice['title']) ?></h3>
                <span class="notice-date">
                    <?= date('d M Y', strtotime($notice['publish_date'])) ?>
                </span>
            </div>
            <div class="notice-body">
                <p><?= nl2br(htmlspecialchars($notice['content'])) ?></p>
            </div>
            <?php if ($notice['file_path']): ?>
            <div class="notice-footer">
                <a href="<?= $notice['file_path'] ?>" class="btn-download" target="_blank">
                    <i class="fas fa-download"></i> Attachment
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
        <p>No active notices available.</p>
    <?php endif; ?>
</div>

<style>
.student-content {
    padding: 2rem;
    background:rgb(190, 235, 238);
    min-height: 100vh;
}

.section-title {
    font-size: 1.8rem;
    margin-bottom: 2rem;
    text-align: center;
    color: #333;
}

.notices-grid {
    display: grid;
    gap: 1.5rem;
    max-width: 900px;
    margin: 0 auto;
}

.notice-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    transition: 0.3s ease;
}

.notice-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
}

.notice-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.notice-header h3 {
    font-size: 1.25rem;
    margin: 0;
    color: #1f2937;
}

.notice-date {
    font-size: 0.9rem;
    color: #6b7280;
}

.notice-body {
    font-size: 1rem;
    color: #374151;
    line-height: 1.6;
    background:rgb(190, 235, 238);
}


.notice-footer {
    margin-top: 1rem;
}

.btn-download {
    display: inline-flex;
    align-items: center;
    background-color: #6366f1;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    transition: background 0.3s ease;
}

.btn-download:hover {
    background-color: #4f46e5;
}

</style>

<?php include 'footer.php'; ?>

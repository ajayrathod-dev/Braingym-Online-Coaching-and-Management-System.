<?php
session_start();
require_once __DIR__ . '/connection.php';
include 'header.php';

$userId = $_SESSION['user_id'] ?? null;

$query = "SELECT g.*, 
          (SELECT COUNT(*) FROM gallery_likes WHERE gallery_id = g.id) AS like_count,
          (SELECT COUNT(*) FROM gallery_likes WHERE gallery_id = g.id AND user_id = ?) AS liked,
          (SELECT COUNT(*) FROM gallery_comments WHERE gallery_id = g.id) AS comment_count
          FROM gallery g
          WHERE g.is_published = 1
          ORDER BY g.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>
<main>
<div class="container">
    <h1 class="page-title">College Gallery</h1>

    <div class="gallery-grid">
        <?php while ($item = $result->fetch_assoc()): ?>
            <div class="gallery-card" data-id="<?= $item['id'] ?>">
                <div class="media-container">
                    <?php if ($item['media_type'] === 'image'): ?>
                        <img src="uploads/gallery/<?= htmlspecialchars($item['file_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                    <?php else: ?>
                        <video controls>
                            <source src="uploads/gallery/<?= htmlspecialchars($item['file_path']) ?>" type="video/mp4">
                        </video>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p class="description"><?= nl2br(htmlspecialchars($item['description'])) ?></p>

                    <div class="interaction">
                        <button class="like-btn <?= $item['liked'] ? 'liked' : '' ?>" onclick="toggleLike(<?= $item['id'] ?>, this)">
                            <i class="fas fa-heart"></i> <span class="like-count"><?= $item['like_count'] ?></span>
                        </button>

                        <div class="comments-section">
                            <form class="comment-form" onsubmit="postComment(event, <?= $item['id'] ?>, this)">
                                <input type="text" placeholder="Add a comment..." required>
                                <button type="submit" class="btn-post">Post</button>
                            </form>
                            <div class="comments-list" id="comments-<?= $item['id'] ?>">
                                <?php
                                    $cid = $item['id'];
                                    $cStmt = $conn->prepare("SELECT gc.comment, gc.created_at, l.username FROM gallery_comments gc JOIN login l ON gc.user_id = l.id WHERE gc.gallery_id = ? ORDER BY gc.created_at DESC LIMIT 3");
                                    $cStmt->bind_param("i", $cid);
                                    $cStmt->execute();
                                    $cRes = $cStmt->get_result();
                                    while ($comment = $cRes->fetch_assoc()):
                                ?>
                                    <div class="comment"><strong><?= htmlspecialchars($comment['username']) ?>:</strong> <?= htmlspecialchars($comment['comment']) ?></div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
function toggleLike(galleryId, btn) {
    fetch('api/like.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'gallery_id=' + galleryId
    })
    .then(res => res.json())
    .then(data => {
        if (data.action === 'liked') {
            btn.classList.add('liked');
        } else {
            btn.classList.remove('liked');
        }
        btn.querySelector('.like-count').textContent = data.likes;
    });
}

function postComment(e, galleryId, form) {
    e.preventDefault();
    const input = form.querySelector('input');
    const comment = input.value.trim();
    if (!comment) return;

    fetch('api/comment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `gallery_id=${galleryId}&comment=${encodeURIComponent(comment)}`
    })
    .then(res => res.json())
    .then(data => {
        const list = document.getElementById('comments-' + galleryId);
        const div = document.createElement('div');
        div.className = 'comment';
        div.innerHTML = `<strong>${data.user}:</strong> ${data.comment}`;
        list.prepend(div);
        input.value = '';
    });
}
</script>

<style>
.liked {
    color: #dc2626 !important;
}
:root {
    --primary-color: #4f46e5;
    --text-color: #1a2236;
    --bg-color: #f8fafc;
}

body {
    font-family: 'Segoe UI', sans-serif;
    background:rgb(190, 235, 238);
    color: var(--text-color);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.page-title {
    font-size: 2rem;
    margin: 2rem 0;
    text-align: center;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    padding-bottom: 2rem;
}

.gallery-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.gallery-card:hover {
    transform: translateY(-5px);
}

.media-container {
    height: 250px;
    overflow: hidden;
}

.media-container img,
.media-container video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-body {
    padding: 1rem;
}

.like-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.like-btn:hover {
    color: #dc2626;
}

.comments-section {
    margin-top: 1rem;
    border-top: 1px solid #e2e8f0;
    padding-top: 1rem;
}

.comment-form {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.comment-form input {
    flex: 1;
    padding: 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
}

.btn-post {
    padding: 0.5rem 1rem;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-post:hover {
    background-color: #4338ca;
}

.comment {
    margin-bottom: 0.5rem;
    background: #f1f5f9;
    padding: 0.5rem;
    border-radius: 4px;
}
</style>

    </div>
</main>

<?php include 'footer.php'; ?>

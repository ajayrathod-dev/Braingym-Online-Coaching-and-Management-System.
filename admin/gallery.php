<?php
session_start();
ob_start();
require_once 'auth_check.php';
require_once __DIR__ . '/../connection.php';

// ─── Handle Upload ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'])) {
    $title       = $conn->real_escape_string(trim($_POST['title'] ?? ''));
    $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $media = $_FILES['media'];

    if ($media['error'] === UPLOAD_ERR_OK) {
        $fileExt  = strtolower(pathinfo($media['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg','jpeg','png','gif','mp4','webm'];

        if (in_array($fileExt, $allowed)) {
            $uniqueName = uniqid('gallery_') . '.' . $fileExt;
            $uploadDir  = __DIR__ . '/../uploads/gallery/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

            if (move_uploaded_file($media['tmp_name'], $uploadDir . $uniqueName)) {
                $media_type = in_array($fileExt, ['mp4','webm']) ? 'video' : 'image';
                $stmt = $conn->prepare("INSERT INTO gallery (title, description, file_path, media_type, is_published) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $title, $description, $uniqueName, $media_type, $is_published);
                $stmt->execute();
                $stmt->close();
                $_SESSION['flash_success'] = "Media uploaded successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to move uploaded file.";
            }
        } else {
            $_SESSION['flash_error'] = "Unsupported file type: $fileExt";
        }
    } else {
        $_SESSION['flash_error'] = "Upload error code: " . $media['error'];
    }
    header("Location: gallery.php");
    exit();
}

// ─── Handle Delete ───
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $r  = $conn->query("SELECT file_path FROM gallery WHERE id = $id");
    if ($r && $r->num_rows > 0) {
        $file = $r->fetch_assoc()['file_path'];
        $fullPath = __DIR__ . '/../uploads/gallery/' . $file;
        if (file_exists($fullPath)) unlink($fullPath);
        $conn->query("DELETE FROM gallery WHERE id = $id");
        $_SESSION['flash_success'] = "Media deleted!";
    } else {
        $_SESSION['flash_error'] = "Media not found.";
    }
    header("Location: gallery.php");
    exit();
}

// ─── Handle Toggle Publish ───
if (isset($_GET['toggle_publish'])) {
    $id = intval($_GET['toggle_publish']);
    $conn->query("UPDATE gallery SET is_published = NOT is_published WHERE id = $id");
    $_SESSION['flash_success'] = "Status updated!";
    header("Location: gallery.php");
    exit();
}

// ─── Filters ───
$filter = $_GET['filter'] ?? 'all';
$where  = '';
switch ($filter) {
    case 'published': $where = " WHERE is_published = 1"; break;
    case 'draft':     $where = " WHERE is_published = 0"; break;
    case 'images':    $where = " WHERE media_type = 'image'"; break;
    case 'videos':    $where = " WHERE media_type = 'video'"; break;
}

$gallery    = $conn->query("SELECT * FROM gallery $where ORDER BY created_at DESC");
$totalCount = $conn->query("SELECT COUNT(*) as c FROM gallery")->fetch_assoc()['c'];
$imageCount = $conn->query("SELECT COUNT(*) as c FROM gallery WHERE media_type='image'")->fetch_assoc()['c'];
$videoCount = $conn->query("SELECT COUNT(*) as c FROM gallery WHERE media_type='video'")->fetch_assoc()['c'];
$pubCount   = $conn->query("SELECT COUNT(*) as c FROM gallery WHERE is_published=1")->fetch_assoc()['c'];
?>

<div class="admin-content">

    <!-- Page Header -->
    <div class="gal-header">
        <div>
            <h1><i class="fas fa-images"></i> Gallery Management</h1>
            <p class="gal-subtitle">Upload and manage photos &amp; videos</p>
        </div>
        <button class="btn-upload" onclick="document.getElementById('uploadPanel').classList.toggle('open')">
            <i class="fas fa-plus"></i> Add Media
        </button>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="flash flash-ok"><i class="fas fa-check-circle"></i> <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="flash flash-err"><i class="fas fa-exclamation-circle"></i> <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="gal-stats">
        <div class="gs-card"><div class="gs-icon" style="background:rgba(79,70,229,.1);color:#4f46e5"><i class="fas fa-photo-film"></i></div><div><div class="gs-val"><?= $totalCount ?></div><div class="gs-lbl">Total</div></div></div>
        <div class="gs-card"><div class="gs-icon" style="background:rgba(6,182,212,.1);color:#06b6d4"><i class="fas fa-image"></i></div><div><div class="gs-val"><?= $imageCount ?></div><div class="gs-lbl">Images</div></div></div>
        <div class="gs-card"><div class="gs-icon" style="background:rgba(168,85,247,.1);color:#a855f7"><i class="fas fa-video"></i></div><div><div class="gs-val"><?= $videoCount ?></div><div class="gs-lbl">Videos</div></div></div>
        <div class="gs-card"><div class="gs-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="fas fa-eye"></i></div><div><div class="gs-val"><?= $pubCount ?></div><div class="gs-lbl">Published</div></div></div>
    </div>

    <!-- Upload Panel (hidden by default) -->
    <div id="uploadPanel" class="upload-panel">
        <form method="POST" enctype="multipart/form-data" class="upload-form">
            <h3><i class="fas fa-cloud-upload-alt"></i> Upload New Media</h3>
            <div class="uf-grid">
                <div class="uf-group">
                    <label for="title">Title <span class="req">*</span></label>
                    <input type="text" id="title" name="title" placeholder="e.g. Annual Day 2025" required>
                </div>
                <div class="uf-group">
                    <label for="media">File <span class="req">*</span></label>
                    <div class="file-drop" id="fileDrop">
                        <input type="file" id="media" name="media" accept="image/*,video/*" required>
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span id="fileLabel">Click or drag file here</span>
                    </div>
                </div>
            </div>
            <div class="uf-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="2" placeholder="Optional description..."></textarea>
            </div>
            <div class="uf-footer">
                <label class="switch-row">
                    <input type="checkbox" name="is_published" checked>
                    <span class="sw-track"><span class="sw-thumb"></span></span>
                    <span>Publish immediately</span>
                </label>
                <button type="submit" class="btn-submit"><i class="fas fa-upload"></i> Upload</button>
            </div>
        </form>
    </div>

    <!-- Filters -->
    <div class="gal-filters">
        <?php
        $tabs = ['all'=>'All','published'=>'Published','draft'=>'Drafts','images'=>'Images','videos'=>'Videos'];
        foreach ($tabs as $key => $label): ?>
            <a href="gallery.php?filter=<?= $key ?>" class="gf-tab <?= $filter === $key ? 'active' : '' ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Gallery Grid -->
    <div class="gal-grid">
        <?php if ($gallery && $gallery->num_rows > 0): ?>
            <?php while ($item = $gallery->fetch_assoc()): ?>
            <div class="gal-card">
                <div class="gc-media">
                    <?php if ($item['media_type'] === 'image'): ?>
                        <img src="../uploads/gallery/<?= htmlspecialchars($item['file_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy">
                    <?php else: ?>
                        <video preload="metadata">
                            <source src="../uploads/gallery/<?= htmlspecialchars($item['file_path']) ?>" type="video/mp4">
                        </video>
                        <div class="gc-play"><i class="fas fa-play"></i></div>
                    <?php endif; ?>
                    <span class="gc-type <?= $item['media_type'] ?>">
                        <i class="fas fa-<?= $item['media_type'] === 'image' ? 'image' : 'video' ?>"></i>
                        <?= ucfirst($item['media_type']) ?>
                    </span>
                </div>
                <div class="gc-body">
                    <h4><?= htmlspecialchars($item['title'] ?: 'Untitled') ?></h4>
                    <?php if ($item['description']): ?>
                        <p class="gc-desc"><?= htmlspecialchars(mb_strimwidth($item['description'], 0, 80, '...')) ?></p>
                    <?php endif; ?>
                    <div class="gc-meta">
                        <span class="gc-status <?= $item['is_published'] ? 'pub' : 'draft' ?>">
                            <span class="dot"></span><?= $item['is_published'] ? 'Published' : 'Draft' ?>
                        </span>
                        <span class="gc-date"><?= date('d M Y', strtotime($item['created_at'])) ?></span>
                    </div>
                </div>
                <div class="gc-actions">
                    <a href="gallery.php?toggle_publish=<?= $item['id'] ?>" class="ga-btn ga-toggle" title="<?= $item['is_published'] ? 'Unpublish' : 'Publish' ?>">
                        <i class="fas fa-<?= $item['is_published'] ? 'eye-slash' : 'eye' ?>"></i>
                    </a>
                    <a href="gallery.php?delete=<?= $item['id'] ?>" class="ga-btn ga-del" title="Delete" onclick="return confirm('Delete this media permanently?')">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="gal-empty">
                <i class="fas fa-photo-film"></i>
                <h3>No media found</h3>
                <p><?= $filter !== 'all' ? 'Try a different filter or ' : '' ?>Upload some images or videos to get started.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* ===== Header ===== */
.gal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem; }
.gal-header h1 { font-size:1.65rem; font-weight:700; color:#1a2236; display:flex; align-items:center; gap:.5rem; margin:0; }
.gal-header h1 i { color:#4f46e5; }
.gal-subtitle { color:#64748b; font-size:.9rem; margin-top:4px; }
.btn-upload { display:inline-flex; align-items:center; gap:.5rem; background:#4f46e5; color:#fff; padding:.6rem 1.2rem; border-radius:.5rem; border:none; font-weight:600; font-size:.9rem; cursor:pointer; transition:all .2s; box-shadow:0 2px 8px rgba(79,70,229,.25); }
.btn-upload:hover { background:#4338ca; transform:translateY(-1px); }

/* ===== Flash ===== */
.flash { padding:.8rem 1.2rem; border-radius:.5rem; margin-bottom:1rem; font-weight:500; font-size:.9rem; display:flex; align-items:center; gap:.5rem; animation:fadeIn .3s; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
.flash-ok  { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
.flash-err { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

/* ===== Stats ===== */
.gal-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
.gs-card { background:#fff; border-radius:.75rem; padding:1rem; display:flex; align-items:center; gap:.75rem; box-shadow:0 1px 3px rgba(0,0,0,.06); transition:transform .2s,box-shadow .2s; }
.gs-card:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,.08); }
.gs-icon { width:44px; height:44px; border-radius:.6rem; display:flex; align-items:center; justify-content:center; font-size:1.15rem; flex-shrink:0; }
.gs-val { font-size:1.4rem; font-weight:700; color:#1a2236; line-height:1.2; }
.gs-lbl { font-size:.75rem; color:#64748b; font-weight:500; text-transform:uppercase; letter-spacing:.5px; }

/* ===== Upload Panel ===== */
.upload-panel { max-height:0; overflow:hidden; transition:max-height .4s ease,opacity .3s; opacity:0; }
.upload-panel.open { max-height:600px; opacity:1; margin-bottom:1.5rem; }
.upload-form { background:#fff; border-radius:.75rem; padding:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,.06); }
.upload-form h3 { font-size:1.1rem; font-weight:600; color:#1a2236; margin-bottom:1.25rem; display:flex; align-items:center; gap:.5rem; }
.upload-form h3 i { color:#4f46e5; }
.uf-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }
.uf-group { margin-bottom:.75rem; }
.uf-group label { display:block; font-weight:500; color:#475569; margin-bottom:.4rem; font-size:.85rem; }
.req { color:#ef4444; }
.uf-group input[type="text"], .uf-group textarea { width:100%; padding:.65rem .85rem; border:1px solid #e2e8f0; border-radius:.4rem; font-family:inherit; font-size:.9rem; transition:border-color .2s; box-sizing:border-box; }
.uf-group input:focus, .uf-group textarea:focus { outline:none; border-color:#4f46e5; box-shadow:0 0 0 3px rgba(79,70,229,.08); }
.file-drop { position:relative; border:2px dashed #cbd5e1; border-radius:.5rem; padding:1.5rem; text-align:center; cursor:pointer; transition:all .2s; background:#f8fafc; }
.file-drop:hover { border-color:#4f46e5; background:#eef2ff; }
.file-drop input { position:absolute; inset:0; opacity:0; cursor:pointer; }
.file-drop i { font-size:1.5rem; color:#94a3b8; display:block; margin-bottom:.4rem; }
.file-drop span { color:#64748b; font-size:.85rem; }
.uf-footer { display:flex; justify-content:space-between; align-items:center; margin-top:.5rem; }
.switch-row { display:flex; align-items:center; gap:.6rem; cursor:pointer; font-size:.85rem; color:#475569; }
.switch-row input { display:none; }
.sw-track { width:36px; height:20px; background:#cbd5e1; border-radius:10px; position:relative; transition:background .2s; }
.sw-thumb { width:16px; height:16px; background:#fff; border-radius:50%; position:absolute; top:2px; left:2px; transition:transform .2s; box-shadow:0 1px 2px rgba(0,0,0,.15); }
.switch-row input:checked + .sw-track { background:#4f46e5; }
.switch-row input:checked + .sw-track .sw-thumb { transform:translateX(16px); }
.btn-submit { background:#4f46e5; color:#fff; padding:.6rem 1.5rem; border:none; border-radius:.4rem; font-weight:600; font-size:.9rem; cursor:pointer; display:flex; align-items:center; gap:.4rem; transition:background .2s; }
.btn-submit:hover { background:#4338ca; }

/* ===== Filters ===== */
.gal-filters { display:flex; gap:.25rem; background:#f1f5f9; border-radius:.5rem; padding:.25rem; margin-bottom:1.25rem; flex-wrap:wrap; }
.gf-tab { padding:.5rem 1rem; text-decoration:none; color:#64748b; font-size:.85rem; font-weight:500; border-radius:.375rem; transition:all .2s; white-space:nowrap; }
.gf-tab:hover { color:#1e293b; background:#fff; }
.gf-tab.active { background:#fff; color:#4f46e5; box-shadow:0 1px 3px rgba(0,0,0,.08); font-weight:600; }

/* ===== Gallery Grid ===== */
.gal-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(270px,1fr)); gap:1.25rem; }
.gal-card { background:#fff; border-radius:.75rem; box-shadow:0 1px 3px rgba(0,0,0,.06); overflow:hidden; transition:transform .2s,box-shadow .2s; }
.gal-card:hover { transform:translateY(-3px); box-shadow:0 8px 20px rgba(0,0,0,.1); }

.gc-media { position:relative; height:200px; overflow:hidden; background:#f1f5f9; }
.gc-media img, .gc-media video { width:100%; height:100%; object-fit:cover; display:block; }
.gc-play { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:50px; height:50px; background:rgba(0,0,0,.55); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.2rem; pointer-events:none; }
.gc-type { position:absolute; top:10px; right:10px; background:rgba(0,0,0,.55); color:#fff; padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:600; display:flex; align-items:center; gap:4px; backdrop-filter:blur(4px); }

.gc-body { padding:1rem 1rem .5rem; }
.gc-body h4 { font-size:.95rem; font-weight:600; color:#1a2236; margin:0 0 .35rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.gc-desc { color:#64748b; font-size:.8rem; margin:0 0 .5rem; line-height:1.4; }
.gc-meta { display:flex; justify-content:space-between; align-items:center; font-size:.78rem; }
.gc-status { display:inline-flex; align-items:center; gap:4px; font-weight:600; }
.gc-status .dot { width:6px; height:6px; border-radius:50%; display:inline-block; }
.gc-status.pub { color:#065f46; } .gc-status.pub .dot { background:#10b981; }
.gc-status.draft { color:#92400e; } .gc-status.draft .dot { background:#f59e0b; }
.gc-date { color:#94a3b8; }

.gc-actions { display:flex; gap:.4rem; padding:.5rem 1rem 1rem; }
.ga-btn { width:32px; height:32px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; text-decoration:none; font-size:.82rem; transition:all .2s; }
.ga-toggle { background:#eff6ff; color:#3b82f6; }
.ga-toggle:hover { background:#dbeafe; transform:translateY(-1px); }
.ga-del { background:#fef2f2; color:#ef4444; }
.ga-del:hover { background:#fee2e2; transform:translateY(-1px); }

/* ===== Empty ===== */
.gal-empty { grid-column:1/-1; text-align:center; padding:4rem 2rem; }
.gal-empty i { font-size:3rem; color:#cbd5e1; margin-bottom:1rem; display:block; }
.gal-empty h3 { color:#334155; font-size:1.15rem; margin-bottom:.5rem; }
.gal-empty p { color:#94a3b8; font-size:.9rem; }

/* ===== Responsive ===== */
@media(max-width:1024px) { .gal-stats{grid-template-columns:repeat(2,1fr);} .uf-grid{grid-template-columns:1fr;} }
@media(max-width:640px) { .gal-stats{grid-template-columns:1fr;} .gal-header{flex-direction:column;align-items:flex-start;} .gal-filters{flex-wrap:wrap;} }
</style>

<script>
// File input label update
document.getElementById('media')?.addEventListener('change', function() {
    const label = document.getElementById('fileLabel');
    if (this.files.length > 0) {
        label.textContent = this.files[0].name;
        document.getElementById('fileDrop').style.borderColor = '#4f46e5';
    }
});
</script>

<?php include 'includes/footer.php'; ?>
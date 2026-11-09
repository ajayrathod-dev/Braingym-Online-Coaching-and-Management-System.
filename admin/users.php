<?php
ob_start(); 
include 'includes/header.php';
require_once 'auth_check.php';

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM admissions WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Student record deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting record";
    }
    header("Location: users.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $uid = intval($_POST['update_status']);
    $newStatus = $_POST['new_status'];
    $stmt = $conn->prepare("UPDATE admissions SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $uid);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Status updated successfully";
    }
    header("Location: users.php");
    exit();
}

// SEARCH & FILTER
$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? 'all';

// Stats
$totalStudents = $conn->query("SELECT COUNT(*) as c FROM admissions")->fetch_assoc()['c'];
$paidStudents = $conn->query("SELECT COUNT(*) as c FROM admissions WHERE status = 'paid'")->fetch_assoc()['c'];
$pendingStudents = $conn->query("SELECT COUNT(*) as c FROM admissions WHERE status = 'pending'")->fetch_assoc()['c'];
$totalRevenue = $conn->query("SELECT COALESCE(SUM(amount), 0) as s FROM admissions WHERE status = 'paid'")->fetch_assoc()['s'];

// Build query
$query = "SELECT * FROM admissions WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ? OR course LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    $types .= 'ssss';
}

if ($statusFilter !== 'all') {
    $query .= " AND status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}

$query .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$studentCount = $result->num_rows;
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h1><i class="fas fa-user-graduate"></i> Student Management</h1>
            <p class="subtitle">Manage enrollments, track payments, and view student records</p>
        </div>
        <div class="page-header-right">
            <a href="admission.php" class="btn-add-student">
                <i class="fas fa-plus"></i> New Admission
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if(isset($_SESSION['message'])): ?>
        <div class="flash flash-success">
            <i class="fas fa-check-circle"></i>
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <div class="flash flash-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(79,70,229,0.1); color: #4f46e5;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $totalStudents ?></div>
                <div class="stat-label">Total Students</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16,185,129,0.1); color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $paidStudents ?></div>
                <div class="stat-label">Paid</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $pendingStudents ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(6,182,212,0.1); color: #06b6d4;">
                <i class="fas fa-indian-rupee-sign"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">₹<?= number_format($totalRevenue) ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
        <form method="GET" class="search-form">
            <?php if ($statusFilter !== 'all'): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
            <?php endif; ?>
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" placeholder="Search by name, email, phone, or course..." 
                       value="<?= htmlspecialchars($search) ?>">
                <?php if (!empty($search)): ?>
                    <a href="users.php<?= $statusFilter !== 'all' ? '?status='.$statusFilter : '' ?>" class="search-clear">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
        <div class="filter-tabs">
            <a href="users.php<?= !empty($search) ? '?search='.urlencode($search) : '' ?>" 
               class="filter-tab <?= $statusFilter === 'all' ? 'active' : '' ?>">All (<?= $totalStudents ?>)</a>
            <a href="users.php?status=paid<?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
               class="filter-tab <?= $statusFilter === 'paid' ? 'active' : '' ?>">Paid (<?= $paidStudents ?>)</a>
            <a href="users.php?status=pending<?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
               class="filter-tab <?= $statusFilter === 'pending' ? 'active' : '' ?>">Pending (<?= $pendingStudents ?>)</a>
        </div>
    </div>

    <!-- Results Count -->
    <div class="results-info">
        Showing <strong><?= $studentCount ?></strong> student<?= $studentCount !== 1 ? 's' : '' ?>
        <?php if (!empty($search)): ?> matching "<em><?= htmlspecialchars($search) ?></em>"<?php endif; ?>
        <?php if ($statusFilter !== 'all'): ?> with status <strong><?= $statusFilter ?></strong><?php endif; ?>
    </div>

    <!-- Student Table -->
    <div class="table-container">
        <table class="student-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Contact</th>
                    <th>Course & Payment</th>
                    <th>Status</th>
                    <th>Enrolled On</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($studentCount > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <?php
                        $colors = ['#4f46e5','#0891b2','#059669','#d97706','#dc2626','#7c3aed','#2563eb','#0d9488'];
                        $colorIndex = ord(strtoupper(substr($row['name'], 0, 1))) % count($colors);
                        $avatarColor = $colors[$colorIndex];
                    ?>
                    <tr>
                        <td>
                            <div class="student-cell">
                                <div class="avatar" style="background: <?= $avatarColor ?>;">
                                    <?= strtoupper(substr($row['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="student-name"><?= htmlspecialchars($row['name']); ?></div>
                                    <div class="student-meta">
                                        <?= $row['gender'] ?? '' ?> · DOB: <?= date('d M Y', strtotime($row['dob'])); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="contact-cell">
                                <div class="contact-row"><i class="fas fa-envelope"></i> <?= htmlspecialchars($row['email']); ?></div>
                                <div class="contact-row"><i class="fas fa-phone"></i> <?= htmlspecialchars($row['phone']); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="course-cell">
                                <span class="course-badge"><?= htmlspecialchars($row['course']); ?></span>
                                <div class="payment-info">
                                    <span class="amount">₹<?= number_format($row['amount']); ?></span>
                                    <span class="installment-type"><?= ucfirst($row['installment']); ?></span>
                                </div>
                                <?php if($row['payment_id'] && $row['payment_id'] != '0'): ?>
                                    <div class="txn-id" title="<?= $row['payment_id'] ?>">
                                        <i class="fas fa-receipt"></i> <?= substr($row['payment_id'], 0, 16) ?>…
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="status-pill status-<?= $row['status']; ?>">
                                <span class="status-dot"></span>
                                <?= ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="date-cell">
                                <?php if ($row['created_at']): ?>
                                    <div class="date-main"><?= date('d M Y', strtotime($row['created_at'])); ?></div>
                                    <div class="date-time"><?= date('h:i A', strtotime($row['created_at'])); ?></div>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="edit-user.php?id=<?= $row['id']; ?>" class="action-btn action-edit" title="Edit Student">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this student record? This cannot be undone.');">
                                    <input type="hidden" name="delete_id" value="<?= $row['id']; ?>">
                                    <button type="submit" class="action-btn action-delete" title="Delete Student">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            <div class="empty-icon"><i class="fas fa-user-slash"></i></div>
                            <?php if (!empty($search)): ?>
                                <h3>No results found</h3>
                                <p>No students match "<strong><?= htmlspecialchars($search) ?></strong>". Try a different search term.</p>
                            <?php else: ?>
                                <h3>No students yet</h3>
                                <p>Get started by adding a new student admission.</p>
                                <a href="admission.php" class="btn-add-student" style="margin-top:12px;display:inline-flex;">
                                    <i class="fas fa-plus"></i> New Admission
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
/* ===== Page Header ===== */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.75rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a2236;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin: 0;
}

.page-header h1 i { color: #4f46e5; }

.subtitle {
    color: #64748b;
    font-size: 0.925rem;
    margin-top: 4px;
}

.btn-add-student {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #4f46e5;
    color: #fff;
    padding: 0.65rem 1.25rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(79,70,229,0.25);
}

.btn-add-student:hover {
    background: #4338ca;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79,70,229,0.35);
}

/* ===== Flash Messages ===== */
.flash {
    padding: 0.85rem 1.25rem;
    border-radius: 0.5rem;
    margin-bottom: 1.25rem;
    font-weight: 500;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity:0; transform:translateY(-10px); }
    to { opacity:1; transform:translateY(0); }
}

.flash-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.flash-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

/* ===== Stat Cards ===== */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.75rem;
}

.stat-card {
    background: #fff;
    border-radius: 0.75rem;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.625rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a2236;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ===== Toolbar ===== */
.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.search-form { flex: 1; min-width: 250px; max-width: 450px; }

.search-box {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.9rem;
}

.search-box input {
    width: 100%;
    padding: 0.7rem 2.5rem 0.7rem 2.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 0.9rem;
    background: #fff;
    transition: all 0.2s;
}

.search-box input:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,0.08);
}

.search-clear {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    text-decoration: none;
    padding: 2px;
}

.search-clear:hover { color: #ef4444; }

.filter-tabs {
    display: flex;
    gap: 0.25rem;
    background: #f1f5f9;
    border-radius: 0.5rem;
    padding: 0.25rem;
}

.filter-tab {
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 500;
    border-radius: 0.375rem;
    transition: all 0.2s;
    white-space: nowrap;
}

.filter-tab:hover { color: #1e293b; background: #fff; }

.filter-tab.active {
    background: #fff;
    color: #4f46e5;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    font-weight: 600;
}

/* ===== Results Info ===== */
.results-info {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 0.75rem;
    padding-left: 4px;
}

/* ===== Table ===== */
.table-container {
    background: #fff;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    overflow-x: auto;
}

.student-table {
    width: 100%;
    border-collapse: collapse;
}

.student-table thead {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}

.student-table th {
    padding: 0.85rem 1.25rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.75px;
    color: #64748b;
    white-space: nowrap;
}

.th-actions { text-align: center; }

.student-table td {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.9rem;
    vertical-align: middle;
}

.student-table tbody tr {
    transition: background 0.15s;
}

.student-table tbody tr:hover {
    background: #fafbff;
}

.student-table tbody tr:last-child td {
    border-bottom: none;
}

/* Student Cell */
.student-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    flex-shrink: 0;
}

.student-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.925rem;
}

.student-meta {
    color: #94a3b8;
    font-size: 0.78rem;
    margin-top: 2px;
}

/* Contact Cell */
.contact-cell { display: flex; flex-direction: column; gap: 4px; }

.contact-row {
    color: #475569;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 220px;
}

.contact-row i {
    color: #94a3b8;
    font-size: 0.75rem;
    width: 14px;
    text-align: center;
}

/* Course Cell */
.course-cell { display: flex; flex-direction: column; gap: 6px; }

.course-badge {
    display: inline-block;
    background: #ede9fe;
    color: #6d28d9;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    width: fit-content;
}

.payment-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
}

.amount {
    font-weight: 700;
    color: #1e293b;
}

.installment-type {
    color: #94a3b8;
    font-size: 0.78rem;
}

.txn-id {
    font-family: 'Courier New', monospace;
    font-size: 0.72rem;
    color: #94a3b8;
    background: #f8fafc;
    padding: 2px 6px;
    border-radius: 4px;
    width: fit-content;
}

.txn-id i { margin-right: 3px; }

/* Status */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
}

.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}

.status-paid { background: #ecfdf5; color: #065f46; }
.status-paid .status-dot { background: #10b981; }

.status-pending { background: #fffbeb; color: #92400e; }
.status-pending .status-dot { background: #f59e0b; }

/* Date Cell */
.date-cell {}
.date-main { font-weight: 500; color: #334155; font-size: 0.875rem; }
.date-time { color: #94a3b8; font-size: 0.78rem; margin-top: 2px; }

/* Actions */
.actions-cell {
    display: flex;
    gap: 0.4rem;
    justify-content: center;
}

.action-btn {
    width: 34px;
    height: 34px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    text-decoration: none;
    font-size: 0.85rem;
}

.action-edit { background: #eff6ff; color: #3b82f6; }
.action-edit:hover { background: #dbeafe; transform: translateY(-1px); }

.action-delete { background: #fef2f2; color: #ef4444; }
.action-delete:hover { background: #fee2e2; transform: translateY(-1px); }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem !important;
}

.empty-icon {
    font-size: 3rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: #334155;
    font-size: 1.15rem;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #94a3b8;
    font-size: 0.9rem;
}

/* ===== Responsive ===== */
@media (max-width: 1024px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
    .toolbar { flex-direction: column; align-items: stretch; }
    .search-form { max-width: 100%; }
}

@media (max-width: 640px) {
    .stat-grid { grid-template-columns: 1fr; }
    .page-header { flex-direction: column; align-items: flex-start; }
    .filter-tabs { flex-wrap: wrap; }
}
</style>

<?php include 'includes/footer.php'; ?>
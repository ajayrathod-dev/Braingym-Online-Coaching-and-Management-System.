<?php
include 'includes/header.php';
require 'auth_check.php';

// Fetch statistics
$total_students_q = $conn->query("SELECT COUNT(*) FROM admissions");
$pending_payments_q = $conn->query("SELECT COUNT(*) FROM admissions WHERE status='pending'");
$active_notices_q = $conn->query("SELECT COUNT(*) FROM notices WHERE is_published=1 AND (expiry_date >= NOW() OR expiry_date IS NULL)");

$stats = [
    'total_students' => $total_students_q ? $total_students_q->fetch_row()[0] : 0,
    'pending_payments' => $pending_payments_q ? $pending_payments_q->fetch_row()[0] : 0,
    'active_notices' => $active_notices_q ? $active_notices_q->fetch_row()[0] : 0,
    'recent_admissions' => $conn->query("SELECT name, course, created_at FROM admissions ORDER BY created_at DESC LIMIT 5")
];

// Chart data (last 7 days admissions)
$chart_data = $conn->query("
    SELECT DATE(created_at) AS date, COUNT(*) AS count 
    FROM admissions 
    WHERE created_at >= CURDATE() - INTERVAL 7 DAY
    GROUP BY DATE(created_at)
    ORDER BY date ASC
");
?>



<style>
/* Dashboard-specific styles */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

/* Fix: Strict Grid Control */
.dashboard-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
    gap: 2rem;
    margin-bottom: 2rem;
    align-items: start;
}

/* Fix: Prevent Chart Overflow */
.chart-container {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    min-width: 0;
    position: relative;
}

.chart-wrapper {
    position: relative;
    height: 320px;
    width: 100%;
}

.activity-container {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    min-width: 0;
}

.activity-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}

.activity-item:last-child {
    border-bottom: none;
}

.quick-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.quick-link {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    text-align: center;
    text-decoration: none;
    transition: transform 0.2s;
    color: #1a2236;
}

.quick-link i {
    display: block;
    font-size: 1.5rem;
    color: #4f46e5;
    margin-bottom: 0.5rem;
}

.quick-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.btn-quick {
    background: #4f46e5;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
}

@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="admin-content">
    <!-- Quick Stats -->
    <div class="dashboard-header">
        <div>
            <h2>Dashboard Overview</h2>
        </div>
        <div class="quick-actions">
            <button class="btn-quick" onclick="location.href='add-notice.php'">
                <i class="fas fa-bullhorn"></i> New Notice
            </button>
            <button class="btn-quick" onclick="location.href='admission.php'">
                <i class="fas fa-user-plus"></i> Add Student
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #4f46e5">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?= number_format($stats['total_students']) ?></h3>
                <p>Total Students</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #10b981">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3><?= number_format($stats['active_notices']) ?></h3>
                <p>Active Notices</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #ef4444">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?= number_format($stats['pending_payments']) ?></h3>
                <p>Pending Payments</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- Admissions Chart -->
        <div class="chart-container">
            <h2 style="margin-top:0; font-size:1.25rem;">Admissions Trend (Last 7 Days)</h2>
            <div class="chart-wrapper">
                <canvas id="admissionsChart"></canvas>
            </div>
        </div>

        <!-- Recent Admissions -->
        <div class="activity-container">
            <h2 style="margin-top:0; font-size:1.25rem;">Recent Admissions</h2>
            <div class="activity-list">
                <?php if ($stats['recent_admissions'] && $stats['recent_admissions']->num_rows > 0): ?>
                    <?php while($admission = $stats['recent_admissions']->fetch_assoc()): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-check text-success" style="color: #10b981;"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-text" style="font-weight: 500;">
                                <?= htmlspecialchars($admission['name']) ?>
                            </div>
                            <div style="font-size: 0.85rem; color: #6b7280;">
                                <?= htmlspecialchars($admission['course']) ?>
                            </div>
                            <div class="activity-time" style="font-size: 0.75rem; color: #9ca3af; margin-top: 2px;">
                                <?= date('M j, h:i A', strtotime($admission['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="activity-item">
                        <div class="activity-text" style="color: #6b7280; font-style: italic;">
                            No recent admissions found.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="quick-links">
        <a href="competitions.php" class="quick-link">
            <i class="fas fa-trophy"></i>
            <span>Competitions</span>
        </a>
        <a href="appointments.php" class="quick-link">
            <i class="fas fa-calendar-alt"></i>
            <span>Appointments</span>
        </a>
        <a href="users.php" class="quick-link">
            <i class="fas fa-user-cog"></i>
            <span>User Management</span>
        </a>
        <a href="settings.php" class="quick-link">
            <i class="fas fa-sliders-h"></i>
            <span>Settings</span>
        </a>
        <a href="profile.php" class="quick-link">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
        </a>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Admissions Chart
const ctx = document.getElementById('admissionsChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php while($row = $chart_data->fetch_assoc()) echo "'".date('D', strtotime($row['date']))."',"; ?>],
        datasets: [{
            label: 'Daily Admissions',
            data: [<?php $chart_data->data_seek(0); 
                   while($row = $chart_data->fetch_assoc()) echo $row['count'].','; ?>],
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
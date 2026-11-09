<?php
include 'includes/header.php';
require_once 'auth_check.php';
// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_id'])) {
    $cancel_id = intval($_POST['cancel_id']);
    $conn->query("DELETE FROM appointments WHERE id = $cancel_id");
    echo "<script>
            alert('Appointment cancelled successfully');
            window.location.href='appointments.php';
          </script>";
    exit();
}

// Fetch appointments grouped by date
$query = "SELECT * FROM appointments ORDER BY appointment_date, appointment_time";
$result = $conn->query($query);

$appointments_by_date = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['appointment_date'];
    $appointments_by_date[$date][] = $row;
}
?>

<div class="admin-content">
    <div class="content-header">
        <div class="header-title">
            <h1>Appointment Management</h1>
            <p>View and manage scheduled appointments</p>
        </div>
    </div>

    <?php if (empty($appointments_by_date)): ?>
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>No Appointments Found</h3>
            <p>There are currently no scheduled appointments.</p>
        </div>
    <?php else: ?>
        <div class="appointments-container">
            <?php foreach ($appointments_by_date as $date => $appointments): ?>
                <div class="date-section">
                    <div class="date-header">
                        <i class="fas fa-calendar-day"></i>
                        <h3><?= date("F j, Y", strtotime($date)) ?></h3>
                    </div>
                    
                    <div class="appointments-grid">
                        <?php foreach ($appointments as $a): ?>
                            <div class="appointment-card">
                                <div class="card-header">
                                    <h4><?= htmlspecialchars($a['name']) ?></h4>
                                    <span class="badge"><?= date("g:i A", strtotime($a['appointment_time'])) ?></span>
                                </div>
                                
                                <div class="card-body">
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span><?= htmlspecialchars($a['email']) ?></span>
                                    </div>
                                    
                                    <?php if (!empty($a['phone'])): ?>
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span><?= htmlspecialchars($a['phone']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-actions">
                                    <form method="POST" 
                                          onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                                        <input type="hidden" name="cancel_id" value="<?= $a['id'] ?>">
                                        <button type="submit" class="btn-danger">
                                            <i class="fas fa-times-circle"></i>
                                            Cancel Appointment
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>

<style>
.content-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.header-title h1 {
    font-size: 1.75rem;
    color: #1a2236;
    margin-bottom: 0.5rem;
}

.header-title p {
    color: #64748b;
    font-size: 0.925rem;
}

.appointments-container {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.date-section {
    padding: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.date-section:last-child {
    border-bottom: none;
}

.date-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.date-header h3 {
    font-size: 1.25rem;
    color: #1a2236;
    margin: 0;
}

.date-header i {
    color: #4f46e5;
    font-size: 1.25rem;
}

.appointments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
}

.appointment-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1.25rem;
    transition: transform 0.2s ease;
}

.appointment-card:hover {
    transform: translateY(-2px);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.card-header h4 {
    margin: 0;
    font-size: 1.1rem;
    color: #1a2236;
}

.badge {
    background: #f0f5ff;
    color: #4f46e5;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    color: #475569;
    font-size: 0.925rem;
}

.info-item i {
    width: 1.25rem;
    color: #94a3b8;
}

.btn-danger {
    background: #fee2e2;
    color: #dc2626;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    width: 100%;
    justify-content: center;
}

.btn-danger:hover {
    background: #fecaca;
    color: #b91c1c;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.empty-state i {
    font-size: 3rem;
    color: #cbd5e1;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    color: #64748b;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #94a3b8;
}
</style>
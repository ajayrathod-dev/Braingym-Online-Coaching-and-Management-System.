<?php
session_start();
// Initialize connection
require_once __DIR__ . '/connection.php';
$conn->set_charset('utf8mb4');

// Get logged-in user ID
$user_id = $_SESSION['user_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BrainGym Competitions</title>
    <style>
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .section h2 {
            color: #2d005f;
            margin-bottom: 1.5rem;
            border-bottom: 3px solid #6e00ff;
            padding-bottom: 0.5rem;
        }

        .competition-card {
            margin-bottom: 1.5rem;
            padding: 1rem;
            border: 1px solid #eee;
            border-radius: 10px;
            transition: transform 0.2s;
        }

        .competition-card:hover {
            transform: translateY(-3px);
        }

        .enrolled-badge {
            background: #4CAF50;
            color: white;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
            float: right;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- All Competitions Section -->
    <div class="section">
        <h2>All Competitions</h2>
        <?php
        // Get all competitions
        $sql = "SELECT * FROM competitions ORDER BY date DESC";
        $result = mysqli_query($conn, $sql);
        
        while($comp = mysqli_fetch_assoc($result)):
        ?>
            <div class="competition-card">
                <h3><?= htmlspecialchars($comp['competition_name']) ?></h3>
                <p>Date: <?= htmlspecialchars($comp['date']) ?></p>
                <p>Level: <?= htmlspecialchars($comp['level']) ?></p>
                <a href="register.php?comp_id=<?= $comp['id'] ?>" class="enroll-btn">
                    Enroll Now
                </a>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- User's Enrollments Section -->
    <?php if($user_id): ?>
    <div class="section">
        <h2>Your Enrollments</h2>
        <?php
        // Get user's enrollments
        $sql = "SELECT c.* 
                FROM competitions c
                JOIN competition_enrollments ce ON c.id = ce.competition_id
                WHERE ce.user_id = $user_id
                ORDER BY ce.enroll_date DESC";
        $result = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($result) > 0):
            while($enrollment = mysqli_fetch_assoc($result)):
            ?>
                <div class="competition-card">
                    <span class="enrolled-badge">Enrolled ✓</span>
                    <h3><?= htmlspecialchars($enrollment['competition_name']) ?></h3>
                    <p>Date: <?= htmlspecialchars($enrollment['date']) ?></p>
                    <p>Your Level: <?= htmlspecialchars($enrollment['level']) ?></p>
                </div>
            <?php endwhile;
        else: ?>
            <p>You haven't enrolled in any competitions yet.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
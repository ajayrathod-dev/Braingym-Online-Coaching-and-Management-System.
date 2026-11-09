<?php
session_start();
// Display flash messages
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            alert('{$flash['message']}');
        });
    </script>";
}

include 'header.php';
require_once __DIR__ . '/connection.php';
$conn->set_charset('utf8mb4');
$user_id = $_SESSION['user_id'] ?? null;

$query = "SELECT * FROM competitions ORDER BY date DESC";
$result = mysqli_query($conn, $query);

// Fetch user enrollments if logged in
$enrolled_comps = [];
if ($user_id) {
  $enroll_query = "SELECT ce.*, c.competition_name, c.date, c.time, c.mode 
                  FROM competition_enrollments ce 
                  JOIN competitions c ON ce.competition_id = c.id 
                  WHERE ce.user_id = $user_id";
  $enroll_result = mysqli_query($conn, $enroll_query);
  while($row = mysqli_fetch_assoc($enroll_result)) {
    $enrolled_comps[] = $row;
  }
}
?>

<style>
    h2 {
      text-align: center;
      color: #333;
      margin: 30px 0;
    }
    .container {
      display: flex;
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    .competition-list, .user-enrollments {
      flex: 1;
    }
    .card {
      background: #fff;
      padding: 1.5rem 2rem;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      position: relative;
    }
    .card h3 {
      margin: 0 0 0.5rem;
      color: #ff4500;
    }
    .card p {
      margin: 4px 0;
      color: #444;
    }
    .enroll-btn {
      position: absolute;
      top: 1.5rem;
      right: 2rem;
      background: #ff4500;
      color: white;
      padding: 8px 16px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s ease;
    }
    .enroll-btn:hover {
      background: #e03e00;
    }
    .disabled-btn {
      background: gray;
      cursor: not-allowed;
    }
    .media-preview {
      margin-top: 15px;
    }
    .media-preview img,
    .media-preview iframe {
      width: 100%;
      max-height: 300px;
      border-radius: 10px;
    }
    .user-enrollments h3 {
      color: #333;
      margin-bottom: 1rem;
    }
</style>

<main>
    <h2>📣 All BrainGym Competitions</h2>
    <div class="container">
      <div class="competition-list">
        <?php while($comp = mysqli_fetch_assoc($result)): ?>
          <div class="card">
            <h3><?= htmlspecialchars($comp['competition_name']) ?></h3>
            <p><strong>Mode:</strong> <?= ucfirst(htmlspecialchars($comp['mode'])) ?></p>
            <p><strong>Level:</strong> <?= htmlspecialchars($comp['level']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($comp['date']) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($comp['time']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($comp['description']) ?></p>

            <?php
              $comp_id = $comp['id'];
              $isEnrolled = false;

              if ($user_id) {
                // Check if user is enrolled in THIS specific competition
                foreach ($enrolled_comps as $enroll) {
                  if ($enroll['competition_id'] == $comp_id) {
                    $isEnrolled = true;
                    break;
                  }
                }
              }
            ?>

            <?php if ($isEnrolled): ?>
              <span class="enroll-btn disabled-btn">Already Enrolled ✅</span>
            <?php else: ?>
              <a href="register.php?comp_id=<?= $comp_id ?>" class="enroll-btn">Enroll Now 🚀</a>
            <?php endif; ?>

            <?php if (!empty($comp['media_url'])): ?>
              <div class="media-preview">
                <?php if (strpos($comp['media_url'], 'youtube.com') !== false || strpos($comp['media_url'], 'youtu.be') !== false): ?>
                  <iframe src="<?= htmlspecialchars($comp['media_url']) ?>" frameborder="0" allowfullscreen></iframe>
                <?php else: ?>
                  <img src="<?= htmlspecialchars($comp['media_url']) ?>" alt="Competition Media">
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      </div>

      <?php if ($user_id): ?>
      <div class="user-enrollments">
        <h3>Your Enrollments</h3>
        <?php if (count($enrolled_comps) > 0): ?>
          <?php foreach ($enrolled_comps as $enroll): ?>
            <div class="card">
              <h3><?= htmlspecialchars($enroll['competition_name']) ?></h3>
              <p><strong>Mode:</strong> <?= ucfirst(htmlspecialchars($enroll['mode'])) ?></p>
              <p><strong>Date:</strong> <?= htmlspecialchars($enroll['date']) ?></p>
              <p><strong>Time:</strong> <?= htmlspecialchars($enroll['time']) ?></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>You haven't enrolled in any competitions yet.</p>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>

</html>
<?php
include 'includes/header.php';
require_once 'auth_check.php';
// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM competitions WHERE id = $id");
    echo "<script>alert('Competition deleted!'); window.location.href='competitions.php';</script>";
    exit();
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $mode = $_POST['mode'];
    $level = $_POST['level'];
    $description = $_POST['description'];
    $media_url = $_POST['media_url'];

    $stmt = $conn->prepare("INSERT INTO competitions (competition_name, date, time, mode, level, description, media_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $title, $date, $time, $mode, $level, $description, $media_url);
    if ($stmt->execute()) {
        echo "<script>alert('Competition created successfully!'); window.location.href='competitions.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
    }
}

// Fetch All Competitions
$competitions = $conn->query("SELECT * FROM competitions ORDER BY date DESC");
?>

<style>
.admin-container {
    display: flex;
    gap: 30px;
    padding: 40px;
    background: #f4f7fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.form-card, .table-card {
    flex: 1;
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}
.form-card h2, .table-card h2 {
    margin-bottom: 20px;
    color: #333;
}
input, select, textarea {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
}
button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}
button:hover {
    background-color: #0056b3;
}
table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #e2e8f0;
}
table th, table td {
    padding: 12px 15px;
    border: 1px solid #e2e8f0;
    text-align: left;
}
table th {
    background-color: #f8fafc;
    font-weight: 600;
    color: #1a2236;
}
.action-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}
.action-btn:hover {
    background: #bd2130;
}
</style>

<div class="admin-container">
    <!-- Left: Form -->
    <div class="form-card">
        <h2>Create New Competition</h2>
        <form method="post">
            <input type="text" name="title" placeholder="Title" required>
            <input type="date" name="date" required>
            <input type="time" name="time" required>
            <select name="mode" required>
                <option value="">-- Select Mode --</option>
                <option value="Online">Online</option>
                <option value="Offline">Offline</option>
            </select>
            <select name="level" required>
                <option value="">-- Select Level --</option>
                <option value="Beginner">Beginner</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Advanced">Advanced</option>
            </select>
            <textarea name="description" placeholder="Description"></textarea>
            <input type="text" name="media_url" placeholder="Media URL (optional)">
            <button type="submit">Create</button>
        </form>
    </div>

    <!-- Right: Competition List -->
    <div class="table-card">
        <h2>All Competitions</h2>
        <table>
            <tr>
                <th>Title</th>
                <th>Date</th>
                <th>Mode</th>
                <th>Level</th>
                <th>Action</th>
            </tr>
            <?php while($row = $competitions->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['competition_name']) ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= $row['mode'] ?></td>
                <td><?= $row['level'] ?></td>
                <td>
                    <a href="competitions.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this competition?')" class="action-btn">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

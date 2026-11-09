<?php include 'header.php'; ?>
<?php require_once __DIR__ . '/connection.php'; ?>

<?php
$email = isset($_GET["email"]) ? $_GET["email"] : "";
$appointment = null;

if ($email) {
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $newDate = $_POST["date"];
    $newTime = $_POST["time"];

    $stmt = $conn->prepare("UPDATE appointments SET appointment_date = ?, appointment_time = ? WHERE email = ?");
    $stmt->bind_param("sss", $newDate, $newTime, $email);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "<script>alert('Appointment rescheduled successfully!'); window.location='book_appointment.php';</script>";
    } else {
        echo "<script>alert('No changes made to the appointment.');</script>";
    }
}

$conn->close();
?>



</head>
<body>

<div class="container">
    <h2>Reschedule Appointment</h2>

    <?php if ($appointment): ?>
        <form method="POST">
            <input type="hidden" name="email" value="<?= htmlspecialchars($appointment['email']) ?>">

            <p><strong>Current Date:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?></p>
            <p><strong>Current Time:</strong> <?= htmlspecialchars($appointment['appointment_time']) ?></p>

            <label for="date">New Date:</label>
            <input type="date" name="date" value="<?= htmlspecialchars($appointment['appointment_date']) ?>" required>

            <label for="time">New Time:</label>
            <input type="time" name="time" value="<?= htmlspecialchars($appointment['appointment_time']) ?>" required>

            <button type="submit">Reschedule</button>
        </form>
    <?php else: ?>
        <p>Invalid appointment or no appointment found for this email.</p>
    <?php endif; ?>
</div>

</body>
<?php include 'footer.php'; ?>
</html>

<?php include 'header.php'; ?>
<?php session_start();
require_once __DIR__ . '/connection.php'; // Included database connection

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? null;

$appointment = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST["user_id"]; // Get user ID from form
    $newDate = $_POST["date"];
    $newTime = $_POST["time"];

    $stmt = $conn->prepare("UPDATE appointments SET appointment_date = ?, appointment_time = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $newDate, $newTime, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "<script>alert('Appointment rescheduled successfully!'); window.location='book_appointment.php';</script>";
    } else {
        echo "<script>alert('No changes made to the appointment.');</script>";
    }
}

$conn->close();
?>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f8f9fa;
    color: #333;
    margin: 0;
    padding: 0;
}

.container {
    width: 50%;
    margin: 50px auto;
    padding: 40px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.1);
}

form {
    display: flex;
    flex-direction: column;
}

label {
    font-weight: 600;
    margin: 10px 0 5px;
}

input {
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
    width: 100%;
    transition: border-color 0.3s ease-in-out;
}

input:focus {
    border-color: #007bff;
    outline: none;
}

button {
    padding: 14px;
    font-size: 18px;
    font-weight: bold;
    color: white;
    background: linear-gradient(to right, #007bff, #0056b3);
    border: none;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 20px;
    transition: background 0.3s ease-in-out, transform 0.2s;
}

button:hover {
    background: linear-gradient(to right, #0056b3, #003580);
    transform: scale(1.05);
}
</style>

</head>
<body>

<div class="container">
    <h2>Reschedule Appointment</h2>

    <?php if ($appointment): ?>
        <form method="POST">
            <!-- Store user_id in hidden field -->
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

            <p><strong>Current Date:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?></p>
            <p><strong>Current Time:</strong> <?= htmlspecialchars($appointment['appointment_time']) ?></p>

            <label for="date">New Date:</label>
            <input type="date" name="date" value="<?= htmlspecialchars($appointment['appointment_date']) ?>" required>

            <label for="time">New Time:</label>
            <input type="time" name="time" value="<?= htmlspecialchars($appointment['appointment_time']) ?>" required>

            <button type="submit">Reschedule</button>
        </form>
    <?php else: ?>
        <p>No appointment found for your account.</p>
    <?php endif; ?>
</div>

</body>
<?php include 'footer.php'; ?>
</html>
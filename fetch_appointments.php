<link rel="stylesheet"  href="style.css">


<?php
session_start();
require_once __DIR__ . '/connection.php';

$query = "SELECT * FROM appointments WHERE status = 'Scheduled' ORDER BY date, time ASC";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['name']} - {$row['date']} at {$row['time']} <button onclick='reschedule({$row['id']})'>Reschedule</button> <button onclick='cancelAppointment({$row['id']})'>Cancel</button></li>";
    }
    echo "</ul>";
} else {
    echo "No upcoming appointments.";
}

$conn->close();
?>

<html>
<body><script src="script.js"></script></body>
</html>
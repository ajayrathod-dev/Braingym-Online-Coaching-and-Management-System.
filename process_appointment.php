<?php
session_start();
require_once __DIR__ . '/connection.php'; // Connect to the database

// Read JSON data sent from JavaScript
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['action'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$action = $data['action'];

if ($action === "book") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    $stmt = $conn->prepare("INSERT INTO appointments (name, email, date, time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $date, $time);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Appointment booked successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to book appointment"]);
    }
}

// Cancel appointment
elseif ($action === "cancel") {
    $email = $data['email'];

    $stmt = $conn->prepare("DELETE FROM appointments WHERE email = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Appointment canceled successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to cancel appointment"]);
    }
}

// Reschedule appointment
elseif ($action === "reschedule") {
    $email = $data['email'];
    $newDate = $data['date'];
    $newTime = $data['time'];

    $stmt = $conn->prepare("UPDATE appointments SET date = ?, time = ? WHERE email = ?");
    $stmt->bind_param("sss", $newDate, $newTime, $email);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Appointment rescheduled successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to reschedule appointment"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid action"]);
}

$conn->close();
?>

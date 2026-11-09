<?php
session_start();
require_once __DIR__ . '/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => '❌ Please login to enroll.'
    ];
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Collect form data
$name = $_POST['name'] ?? '';
$roll = $_POST['roll_no'] ?? '';
$class = $_POST['class'] ?? '';
$age = $_POST['age'] ?? '';
$mode = $_POST['mode'] ?? '';
$comp_id = $_POST['comp_id'] ?? '';

// Validate required fields
if (empty($name) || empty($roll) || empty($class) || empty($age) || empty($mode) || empty($comp_id)) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => '❌ Please fill all the required fields correctly.'
    ];
    header('Location: competition_view.php');
    exit();
}

// Check for duplicate entry in participants table
$check_sql = "SELECT id FROM participants WHERE roll_no = ? AND competition_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("si", $roll, $comp_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    $_SESSION['flash'] = [
        'type' => 'warning',
        'message' => '⚠️ You are already enrolled in this competition.'
    ];
    header('Location: competition_view.php');
    exit();
}
$check_stmt->close();

// Insert into participants table
$stmt = $conn->prepare("INSERT INTO participants (name, roll_no, class, age, mode, competition_id, stage, user_id) VALUES (?, ?, ?, ?, ?, ?, 1, ?)");
$stmt->bind_param("sssisii", $name, $roll, $class, $age, $mode, $comp_id, $user_id);

if ($stmt->execute()) {
    // Also insert into competition_enrollments
    $enroll_stmt = $conn->prepare("INSERT INTO competition_enrollments (competition_id, user_id) VALUES (?, ?)");
    $enroll_stmt->bind_param("ii", $comp_id, $user_id);
    $enroll_stmt->execute();
    $enroll_stmt->close();

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => '🎉 You have successfully enrolled for the competition!'
    ];
    header('Location: competition_view.php');
} else {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => '❌ Error occurred: ' . $stmt->error
    ];
    header('Location: competition_view.php');
}

$stmt->close();
$conn->close();
exit();
?>
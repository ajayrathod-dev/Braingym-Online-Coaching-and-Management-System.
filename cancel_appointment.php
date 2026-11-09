<?php
require_once __DIR__ . '/connection.php';
session_start(); // Added session start

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $user_id = $_SESSION['user_id'] ?? null; // Get user ID

    // ===== ADDED USER_ID CHECK FOR SECURITY ===== //
    if ($user_id) {
        $update_query = "UPDATE appointments SET status = 'Cancelled' WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ii", $id, $user_id);

        if ($stmt->execute()) {
            echo "Appointment cancelled successfully!";
        } else {
            echo "Error cancelling appointment!";
        }

        $stmt->close();
    } else {
        echo "Authentication required!";
    }
    
    $conn->close();
}
?>
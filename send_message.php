<?php
session_start(); // Added session start
require_once __DIR__ . '/connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID if available
    $user_id = isset($_POST['user_id']) && !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null;
    
    // Validate and sanitize inputs
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $inquiry_type = isset($_POST['inquiry_type']) ? trim($_POST['inquiry_type']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Basic validation
    $errors = [];
    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($message)) {
        $errors[] = "Message is required.";
    }

    // If validation errors, show them
    if (!empty($errors)) {
        $error_message = implode("\\n", $errors);
        echo "<script>alert('$error_message'); window.history.back();</script>";
        exit;
    }

    try {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO messages 
                               (user_id, full_name, email, phone, inquiry_type, message, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, NOW())");
        
        // Bind parameters
        $stmt->bind_param("isssss", $user_id, $full_name, $email, $phone, $inquiry_type, $message);
        
        // Execute query
        if ($stmt->execute()) {
            echo "<script>alert('Your message has been sent successfully!'); window.location='contact.php';</script>";
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Log error to server
        error_log($e->getMessage());
        
        // Show user-friendly message
        echo "<script>alert('Something went wrong. Please try again later.'); window.history.back();</script>";
    } finally {
        // Close statement
        if (isset($stmt)) $stmt->close();
        
        // Close connection
        $conn->close();
    }
} else {
    // Not a POST request
    header("Location: contact.php");
    exit;
}
?>
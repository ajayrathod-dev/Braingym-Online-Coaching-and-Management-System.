<?php
session_start();
if (!isset($_GET['payment_id']) || !isset($_SESSION['form_data'])) {
    die("Invalid access.");
}

$payment_id = $_GET['payment_id'];
$formData = $_SESSION['form_data'];
$amount = $_SESSION['amount'];

// DB Connection
require_once __DIR__ . '/connection.php';

// Prepare data
$name = $formData['name'];
$email = $formData['email'];
$phone = $formData['phone'];
$dob = $formData['dob'];
$gender = $formData['gender'];
$address = $formData['address'];
$installment = $formData['installment_type'];
$date = date("Y-m-d H:i:s");

// Insert into DB
$stmt = $conn->prepare("INSERT INTO admissions (name, email, phone, dob, gender, address, installment, amount_paid, payment_id, admission_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssss", $name, $email, $phone, $dob, $gender, $address, $installment, $amount, $payment_id, $date);

if ($stmt->execute()) {
    echo "<h2>🎉 Admission Successful</h2>";
    echo "<p>Thank you, <strong>$name</strong>. Your payment of ₹$amount has been received.</p>";
    echo "<p><strong>Payment ID:</strong> $payment_id</p>";
    echo "<a href='index.php'>🔙 Back to Home</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
// Do not close connection here as it might be needed by other files
// $conn->close();

// Clear session
unset($_SESSION['form_data']);
unset($_SESSION['amount']);
?>

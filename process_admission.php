<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
session_start();
require_once __DIR__ . '/connection.php';

// Process student details
$student_data = [
    'full_name' => $_POST['full_name'],
    'parent_name' => $_POST['parent_name'],
    'email' => $_POST['email'],
    'phone' => $_POST['phone'],
    'dob' => $_POST['dob'],
    'address' => $_POST['address']
];

// Insert student
$stmt = $conn->prepare("INSERT INTO students (".implode(',', array_keys($student_data)).") 
                       VALUES (".str_repeat('?,', count($student_data)-1)."?)");
$stmt->bind_param(str_repeat('s', count($student_data)), ...array_values($student_data));
$stmt->execute();
$student_id = $conn->insert_id;

// Initialize fee structure
$conn->query("INSERT INTO fee_structure (student_id) VALUES ($student_id)");

// Process enrollment fee payment
require 'razorpay-php/Razorpay.php'; // Example with Razorpay
use Razorpay\Api\Api;

$api = new Api('YOUR_KEY_ID', 'YOUR_KEY_SECRET');

$paymentData = [
    'amount' => 500 * 100, // in paise
    'currency' => 'INR',
    'receipt' => 'ENROLL_'.$student_id,
    'payment_capture' => 1,
    'notes' => [
        'student_id' => $student_id,
        'payment_type' => 'enrollment'
    ]
];

try {
    $razorpayOrder = $api->order->create($paymentData);
    $_SESSION['razorpay_order_id'] = $razorpayOrder['id'];
    $_SESSION['student_id'] = $student_id;
    
    // Store payment record
    $conn->query("INSERT INTO payments (student_id, payment_type, amount, status)
                 VALUES ($student_id, 'enrollment', 500.00, 'pending')");
    
    // Redirect to payment page
    header("Location: payment_gateway.php?order_id=".$razorpayOrder['id']);
    exit();
} catch (Exception $e) {
    die("Payment initialization failed: " . $e->getMessage());
}
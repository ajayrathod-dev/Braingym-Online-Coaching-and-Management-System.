<?php
session_start();
// Check if user is logged in
if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
    die("Please login before proceeding with admission.");
}
/*-------------------------------------------------
  1. Collect form data
-------------------------------------------------*/
$name         = $_POST['name']        ?? '';
$dob          = $_POST['dob']         ?? '';
$gender       = $_POST['gender']      ?? '';
$email        = $_POST['email']       ?? '';
$phone        = $_POST['phone']       ?? '';
$address      = $_POST['address']     ?? '';
$aadhar       = $_POST['aadhar']      ?? '';
$father       = $_POST['father_name'] ?? '';
$mother       = $_POST['mother_name'] ?? '';
$qualification= $_POST['qualification']?? '';
$course       = $_POST['course']      ?? '';
$installment  = $_POST['installment_type'] ?? '';

$amount = ($installment === "full") ? 3000 : 1500;
$user_id      = $_POST['user_id']; // Get user ID
$name         = $_POST['name']        ?? '';
/*-------------------------------------------------
  2. Insert a *pending* record into the DB
-------------------------------------------------*/
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    require_once __DIR__ . '/connection.php';
    $conn->set_charset('utf8mb4');

    // Add user_id to the SQL query
    $sql = "INSERT INTO admissions
        (user_id, name, dob, gender, email, phone, address, aadhar, father_name, mother_name,
         qualification, course, installment, amount, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "isssssssssssis", // Added integer type for user_id
        $user_id, $name, $dob, $gender, $email, $phone, $address, $aadhar, $father, $mother,
        $qualification, $course, $installment, $amount
    );
    $stmt->execute();
    $admission_id = $stmt->insert_id;

    $stmt->close();
    // Do not close connection here as it might be needed by other files
    // $conn->close();
} catch (mysqli_sql_exception $e) {
    die("DB insert error: " . $e->getMessage());
}

/*-------------------------------------------------
  3. Save to session for the next step
-------------------------------------------------*/
$_SESSION['student'] = [
    'user_id'       => $user_id, // Store user ID
    'id'            => $admission_id,
    'name'         => $name,
    'dob'          => $dob,
    'gender'       => $gender,
    'email'        => $email,
    'phone'        => $phone,
    'address'      => $address,
    'aadhar'       => $aadhar,
    'father'       => $father,
    'mother'       => $mother,
    'qualification'=> $qualification,
    'course'       => $course,
    'installment'  => $installment,
    'amount'       => $amount
];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Payment</title>
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body style="text-align:center;font-family:sans-serif;padding:50px;">
  <h2>Processing Payment of ₹<?= $amount ?>...</h2>

  <script>
    var options = {
        key: "rzp_test_L3oqIodr8WVfKA",          // your Razorpay Key ID
        amount: <?= $amount * 100 ?>,            // paise
        currency: "INR",
        name: "Student Admission",
        description: "Course Enrollment",
        handler: function (response) {
            // redirect to success page with payment_id
            window.location.href = "success.php?payment_id=" + response.razorpay_payment_id;
        },
        prefill: {
            name:  "<?= htmlspecialchars($name) ?>",
            email: "<?= htmlspecialchars($email) ?>",
            contact: "<?= htmlspecialchars($phone) ?>"
        },
        theme: { color: "#3399cc" }
    };
    new Razorpay(options).open();
  </script>
</body>
</html>

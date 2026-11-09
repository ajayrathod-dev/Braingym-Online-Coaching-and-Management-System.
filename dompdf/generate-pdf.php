<!-- generate-pdf.php -->
<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$amount = $_POST['amount'];
$payment_id = $_POST['payment_id'];

$html = "
    <h1>Admission Receipt</h1>
    <hr>
    <p><strong>Payment ID:</strong> $payment_id</p>
    <p><strong>Name:</strong> $name</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Phone:</strong> $phone</p>
    <p><strong>Amount Paid:</strong> ₹$amount</p>
    <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("receipt_$payment_id.pdf", ["Attachment" => true]);
?>

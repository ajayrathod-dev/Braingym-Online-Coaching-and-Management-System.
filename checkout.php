<!-- checkout.php -->
<?php
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$installment = $_POST['installment'];

$amount = ($installment == '1500-2') ? 1500 : intval($installment);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment | Razorpay</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body onload="payNow()" style="text-align: center; padding: 100px;">
    <h2>Processing Payment for ₹<?= $amount ?>...</h2>

    <script>
        function payNow() {
            var options = {
                "key": "rzp_test_L3oqIodr8WVfKA", // Replace with your Test Key
                "amount": <?= $amount * 100 ?>,
                "currency": "INR",
                "name": "Student Admission",
                "description": "Admission Installment Payment",
                "handler": function (response){
                    window.location.href = "success.php?payment_id=" + response.razorpay_payment_id +
                                            "&name=<?= $name ?>" +
                                            "&email=<?= $email ?>" +
                                            "&phone=<?= $phone ?>" +
                                            "&amount=<?= $amount ?>";
                },
                "prefill": {
                    "name": "<?= $name ?>",
                    "email": "<?= $email ?>",
                    "contact": "<?= $phone ?>"
                },
                "theme": {
                    "color": "#3399cc"
                }
            };
            var rzp = new Razorpay(options);
            rzp.open();
        }
    </script>
</body>
</html>

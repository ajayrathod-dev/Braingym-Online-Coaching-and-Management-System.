<?php
/***************************************************
 * success.php – combined greeting + receipt screen
 * Flow:
 *   Razorpay → success.php?payment_id=XYZ
 *   ① insert into DB
 *   ② show greeting + 5-second countdown
 *   ③ auto-reload same file with ?receipt=1
 *   ④ printable receipt shown
 ***************************************************/
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*-------------------------------------------------
  0.  Basic guards
-------------------------------------------------*/
if (!isset($_GET['payment_id']) || !isset($_SESSION['student'])) {
    die("Required data missing – please restart the process.");
}

/*-------------------------------------------------
  1.  Collect data
-------------------------------------------------*/
$payment_id = $_GET['payment_id'];
$student    = $_SESSION['student'];        // data saved in pay.php
$timestamp  = date('Y-m-d H:i:s');
$student['payment_id'] = $payment_id;      // append
$student['timestamp']  = $timestamp;
$student['status']     = 'paid';

/*-------------------------------------------------
  2.  Insert into DB (only once)
      We guard with a session flag so refresh
      won't create duplicates.
-------------------------------------------------*/
if (empty($_SESSION['insert_done'])) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    require_once __DIR__ . '/../connection.php';
    $conn->set_charset('utf8mb4');
    
    // Update existing record instead of inserting
    $sql = "UPDATE admissions SET
        payment_id = ?,
        status = ?,
        created_at = ?
        WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssi",
        $student['payment_id'],
        $student['status'],
        $student['timestamp'],
        $student['id']
    );
    $stmt->execute();
    $stmt->close();
    $conn->close();

    $_SESSION['insert_done'] = true;  // mark inserted
}

/*-------------------------------------------------
  3.  Check mode: greeting or receipt?
-------------------------------------------------*/
$showReceipt = isset($_GET['receipt']) && $_GET['receipt'] == 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $showReceipt ? 'Admission Receipt' : 'Payment Successful' ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
/* --- shared minimal styles --- */
body{margin:0;font-family:'Inter',sans-serif;background:#C2FFC7;display:flex;justify-content:center;align-items:center;min-height:100vh}
.container{background:#fff;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.08);max-width:700px;width:100%;overflow:hidden;animation:fadeIn .6s}
@keyframes fadeIn{from{opacity:0;transform:translateY(25px)}to{opacity:1;transform:translateY(0)}}
.header{background:linear-gradient(135deg,#4f46e5,#3b82f6);color:#fff;padding:30px 20px;text-align:center}
.badge{font-size:48px;margin-top:10px}
.body{padding:30px 40px}
.info-row{display:flex;justify-content:space-between;margin-bottom:18px;border-bottom:1px solid #eee;padding-bottom:10px}
.info-label{font-weight:600;color:#555}
.info-value{color:#222;text-align:right}
.btn{display:inline-block;background:#4f46e5;color:#fff;padding:12px 28px;margin-top:25px;border:none;border-radius:8px;cursor:pointer;text-decoration:none;transition:.3s}
.btn:hover{background:#3730a3}
.footer{text-align:center;font-size:13px;color:#999;margin:20px}
.no-print{display:block}@media print{.no-print{display:none}}
</style>
<?php if(!$showReceipt): ?>
<!-- redirect after 5 s -->
<meta http-equiv="refresh" content="5;url=success.php?payment_id=<?= $payment_id ?>&receipt=1">
<?php endif; ?>
</head>
<body>

<div class="container">
  <?php if(!$showReceipt): ?>
      <!-- GREETING / COUNTDOWN VIEW -->
      <div class="header">
          <div class="badge"><i class="fa-solid fa-circle-check"></i></div>
          <h1>Payment Successful</h1>
          <p>Thank you, <?= htmlspecialchars($student['name']) ?>!</p>
      </div>
      <div class="body" style="text-align:center">
          <h3>Your Payment ID:</h3>
          <p style="font-size:20px;font-weight:600"><?= $payment_id ?></p>
          <p>We’re creating your receipt…</p>
          <p>You’ll be redirected in <span id="sec">5</span> seconds.</p>
      </div>
      <script>
        /* nice countdown */
        let s=5, el=document.getElementById('sec');
        let iv=setInterval(()=>{s--;el.textContent=s; if(s<=0)clearInterval(iv);},1000);
      </script>
  <?php else: ?>
      <!-- RECEIPT VIEW -->
      <div class="header">
          <div class="badge"><i class="fa-solid fa-circle-check"></i></div>
          <h1>Admission Receipt</h1>
          <p>Payment ID: <?= $payment_id ?></p>
      </div>

      <div class="body">
          <?php
          // convenience variables
          $r = $student;
          ?>
          <div class="info-row"><div class="info-label"><i class="fa-solid fa-calendar-alt"></i> Date</div><div class="info-value"><?= $r['timestamp'] ?></div></div>
          <div class="info-row"><div class="info-label"><i class="fa-solid fa-user"></i> Name</div><div class="info-value"><?= htmlspecialchars($r['name']) ?></div></div>
          <div class="info-row"><div class="info-label"><i class="fa-solid fa-envelope"></i> Email</div><div class="info-value"><?= htmlspecialchars($r['email']) ?></div></div>
          <div class="info-row"><div class="info-label"><i class="fa-solid fa-phone"></i> Phone</div><div class="info-value"><?= htmlspecialchars($r['phone']) ?></div></div>
          <div class="info-row"><div class="info-label"><i class="fa-solid fa-book-open"></i> Course</div><div class="info-value"><?= htmlspecialchars($r['course']) ?></div></div>
          <div class="info-row"><div class="info-label"><i class="fa-solid fa-money-check-dollar"></i> Installment</div><div class="info-value"><?= htmlspecialchars($r['installment']) ?></div></div>
          <div class="info-row"><div class="info-label"><i class="fa-solid fa-indian-rupee-sign"></i> Amount Paid</div><div class="info-value">₹<?= number_format($r['amount'],2) ?></div></div>

          <div style="text-align:center" class="no-print">
              <button class="btn" onclick="window.print()"><i class="fa-solid fa-print"></i> Print / Save</button>
              <a href="index.php" class="btn" style="background:#6b7280"><i class="fa-solid fa-house"></i> Home</a>
          </div>
      </div>

      <div class="footer">
          This receipt was generated digitally and is valid for official use.<br class="no-print">
          <span class="no-print">Thank you!</span>
      </div>
  <?php endif; ?>
</div>

</body>
</html>


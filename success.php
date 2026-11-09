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

    require_once __DIR__ . '/connection.php';
    
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
    // Do not close connection here as it might be needed by other files
    // $conn->close();

    $_SESSION['insert_done'] = true;  // mark inserted
}

/*-------------------------------------------------
  3.  Check mode: greeting or receipt?
-------------------------------------------------*/
$showReceipt = isset($_GET['receipt']) && $_GET['receipt'] == 1;

// Format date for receipt display
$receiptDate = date('d M Y, h:i A', strtotime($student['timestamp']));
$receiptNo   = 'BGA-' . str_pad($student['id'], 6, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $showReceipt ? 'Admission Receipt' : 'Payment Successful' ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<?php if(!$showReceipt): ?>
<!-- redirect after 5 s -->
<meta http-equiv="refresh" content="5;url=success.php?payment_id=<?= urlencode($payment_id) ?>&receipt=1">
<?php endif; ?>
<style>
/* ===== CSS Variables ===== */
:root {
    --primary: #6c2eb9;
    --primary-light: #8b5cf6;
    --primary-dark: #5b21b6;
    --accent: #f59e0b;
    --success: #10b981;
    --success-light: #d1fae5;
    --bg: #f0f4f8;
    --card-bg: #ffffff;
    --text: #1e293b;
    --text-muted: #64748b;
    --border: #e2e8f0;
    --shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
}

/* ===== Reset & Base ===== */
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem 1rem;
}

/* ===== Greeting View ===== */
.greeting-container {
    max-width: 520px;
    width: 100%;
    animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideUp {
    from { opacity:0; transform:translateY(40px); }
    to   { opacity:1; transform:translateY(0); }
}

.greeting-card {
    background: var(--card-bg);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: var(--shadow);
    text-align: center;
}

.greeting-header {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    padding: 3rem 2rem 2.5rem;
    position: relative;
    overflow: hidden;
}

.greeting-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
}

.check-circle {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
    animation: scaleIn 0.5s 0.3s both;
}

@keyframes scaleIn {
    from { transform: scale(0); }
    to   { transform: scale(1); }
}

.check-circle i {
    font-size: 2.5rem;
    color: #fff;
}

.greeting-header h1 {
    color: #fff;
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.greeting-header p {
    color: rgba(255,255,255,0.9);
    font-size: 1.05rem;
}

.greeting-body {
    padding: 2.5rem 2rem;
}

.payment-id-box {
    background: #f8fafc;
    border: 2px dashed var(--border);
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

.payment-id-box label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    display: block;
    margin-bottom: 0.5rem;
}

.payment-id-box .id-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text);
    font-family: 'Courier New', monospace;
    word-break: break-all;
}

.countdown-text {
    color: var(--text-muted);
    font-size: 0.95rem;
}

.countdown-text span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: var(--primary);
    color: #fff;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1rem;
    margin: 0 4px;
}

.loader-bar {
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    margin-top: 1.5rem;
    overflow: hidden;
}

.loader-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
    border-radius: 2px;
    animation: fillBar 5s linear;
}

@keyframes fillBar {
    from { width: 0%; }
    to   { width: 100%; }
}

/* ===== Receipt View ===== */
.receipt-container {
    max-width: 800px;
    width: 100%;
    animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.receipt-card {
    background: var(--card-bg);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow);
    position: relative;
}

/* Top accent bar */
.receipt-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, var(--primary), var(--primary-light), var(--accent));
}

.receipt-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 2.5rem 2.5rem 1.5rem;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
    gap: 1rem;
}

.brand {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.brand-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #fff;
    flex-shrink: 0;
}

.brand-text h2 {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--text);
    line-height: 1.2;
}

.brand-text p {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: 2px;
}

.receipt-meta {
    text-align: right;
}

.receipt-meta h3 {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--primary);
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 0.5rem;
}

.receipt-meta .meta-row {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 2px;
}

.receipt-meta .meta-row strong {
    color: var(--text);
}

/* Status badge */
.status-bar {
    background: var(--success-light);
    padding: 0.85rem 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #065f46;
    font-size: 0.9rem;
}

.status-bar i {
    color: var(--success);
}

/* Sections */
.receipt-body {
    padding: 2rem 2.5rem;
}

.section-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--primary);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i {
    font-size: 0.85rem;
}

/* Info grid */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    margin-bottom: 2rem;
}

.info-cell {
    padding: 0.85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.info-cell:nth-child(odd) {
    border-right: 1px solid #f1f5f9;
}

.info-cell .label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    margin-bottom: 4px;
}

.info-cell .value {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text);
}

/* Payment Summary */
.payment-summary {
    background: #faf5ff;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #ede9fe;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.6rem 0;
    font-size: 0.95rem;
}

.summary-row:not(:last-child) {
    border-bottom: 1px dashed #e9d5ff;
}

.summary-row .s-label {
    color: var(--text-muted);
    font-weight: 500;
}

.summary-row .s-value {
    color: var(--text);
    font-weight: 600;
}

.summary-row.total {
    padding-top: 1rem;
    margin-top: 0.5rem;
    border-top: 2px solid var(--primary);
    border-bottom: none;
}

.summary-row.total .s-label {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text);
}

.summary-row.total .s-value {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--primary);
}

/* Footer */
.receipt-footer {
    border-top: 1px solid var(--border);
    padding: 1.5rem 2.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.footer-note {
    font-size: 0.78rem;
    color: var(--text-muted);
    max-width: 400px;
    line-height: 1.5;
}

.footer-note i {
    color: var(--primary-light);
    margin-right: 4px;
}

.actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1.4rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    font-family: inherit;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: #fff;
    box-shadow: 0 4px 14px rgba(108,46,185,0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(108,46,185,0.4);
}

.btn-secondary {
    background: #f1f5f9;
    color: var(--text);
}

.btn-secondary:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

.btn-outline {
    background: transparent;
    color: var(--primary);
    border: 2px solid var(--primary);
}

.btn-outline:hover {
    background: var(--primary);
    color: #fff;
    transform: translateY(-2px);
}

/* Watermark */
.watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    font-size: 5rem;
    font-weight: 900;
    color: rgba(16,185,129,0.04);
    text-transform: uppercase;
    letter-spacing: 8px;
    pointer-events: none;
    white-space: nowrap;
}

/* ===== Print Styles ===== */
@media print {
    body {
        background: #fff;
        padding: 0;
    }

    .receipt-card {
        box-shadow: none;
        border-radius: 0;
        border: 1px solid #ddd;
    }

    .no-print { display:none !important; }

    .receipt-card::before {
        background: var(--primary);
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .status-bar {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .payment-summary {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .watermark {
        color: rgba(16,185,129,0.06);
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

/* ===== Responsive ===== */
@media (max-width: 640px) {
    .receipt-top {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .receipt-meta {
        text-align: center;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .info-cell:nth-child(odd) {
        border-right: none;
    }

    .receipt-footer {
        flex-direction: column;
        text-align: center;
    }

    .footer-note {
        max-width: 100%;
    }

    .actions {
        justify-content: center;
    }
}
</style>
</head>
<body>

<?php if(!$showReceipt): ?>
<!-- ==================== GREETING / COUNTDOWN VIEW ==================== -->
<div class="greeting-container">
    <div class="greeting-card">
        <div class="greeting-header">
            <div class="check-circle">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1>Payment Successful!</h1>
            <p>Thank you, <?= htmlspecialchars($student['name']) ?>!</p>
        </div>
        <div class="greeting-body">
            <div class="payment-id-box">
                <label>Payment Reference ID</label>
                <div class="id-value"><?= htmlspecialchars($payment_id) ?></div>
            </div>
            <p class="countdown-text">
                Generating your receipt in <span id="sec">5</span> seconds…
            </p>
            <div class="loader-bar">
                <div class="loader-bar-fill"></div>
            </div>
        </div>
    </div>
</div>
<script>
    let s = 5, el = document.getElementById('sec');
    let iv = setInterval(() => { s--; el.textContent = s; if (s <= 0) clearInterval(iv); }, 1000);
</script>

<?php else: ?>
<!-- ==================== RECEIPT VIEW ==================== -->
<div class="receipt-container">
    <div class="receipt-card">
        <!-- Watermark -->
        <div class="watermark">PAID</div>

        <!-- Top -->
        <div class="receipt-top">
            <div class="brand">
                <div class="brand-icon">
                    <i class="fa-solid fa-brain"></i>
                </div>
                <div class="brand-text">
                    <h2>Brain Gym Academy</h2>
                    <p>Developing Young Minds Through Abacus Learning</p>
                </div>
            </div>
            <div class="receipt-meta">
                <h3>Receipt</h3>
                <div class="meta-row"><strong>No:</strong> <?= $receiptNo ?></div>
                <div class="meta-row"><strong>Date:</strong> <?= $receiptDate ?></div>
            </div>
        </div>

        <!-- Status -->
        <div class="status-bar">
            <i class="fa-solid fa-circle-check"></i>
            Payment Verified &amp; Confirmed
        </div>

        <!-- Body -->
        <div class="receipt-body">
            <!-- Student Info -->
            <div class="section-title"><i class="fa-solid fa-user-graduate"></i> Student Information</div>
            <div class="info-grid">
                <div class="info-cell">
                    <div class="label">Full Name</div>
                    <div class="value"><?= htmlspecialchars($student['name']) ?></div>
                </div>
                <div class="info-cell">
                    <div class="label">Email Address</div>
                    <div class="value"><?= htmlspecialchars($student['email']) ?></div>
                </div>
                <div class="info-cell">
                    <div class="label">Phone Number</div>
                    <div class="value"><?= htmlspecialchars($student['phone']) ?></div>
                </div>
                <div class="info-cell">
                    <div class="label">Date of Birth</div>
                    <div class="value"><?= htmlspecialchars($student['dob'] ?? 'N/A') ?></div>
                </div>
                <div class="info-cell">
                    <div class="label">Gender</div>
                    <div class="value"><?= htmlspecialchars(ucfirst($student['gender'] ?? 'N/A')) ?></div>
                </div>
                <div class="info-cell">
                    <div class="label">Qualification</div>
                    <div class="value"><?= htmlspecialchars($student['qualification'] ?? 'N/A') ?></div>
                </div>
                <div class="info-cell">
                    <div class="label">Father's Name</div>
                    <div class="value"><?= htmlspecialchars($student['father'] ?? 'N/A') ?></div>
                </div>
                <div class="info-cell">
                    <div class="label">Mother's Name</div>
                    <div class="value"><?= htmlspecialchars($student['mother'] ?? 'N/A') ?></div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="section-title"><i class="fa-solid fa-credit-card"></i> Payment Summary</div>
            <div class="payment-summary">
                <div class="summary-row">
                    <span class="s-label">Course</span>
                    <span class="s-value"><?= htmlspecialchars($student['course']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="s-label">Installment Type</span>
                    <span class="s-value"><?= htmlspecialchars(ucfirst($student['installment'])) ?></span>
                </div>
                <div class="summary-row">
                    <span class="s-label">Payment Method</span>
                    <span class="s-value">Razorpay (Online)</span>
                </div>
                <div class="summary-row">
                    <span class="s-label">Transaction ID</span>
                    <span class="s-value" style="font-family:'Courier New',monospace;font-size:0.85rem;"><?= htmlspecialchars($payment_id) ?></span>
                </div>
                <div class="summary-row total">
                    <span class="s-label">Total Amount Paid</span>
                    <span class="s-value">₹<?= number_format($student['amount'], 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="footer-note">
                <i class="fa-solid fa-shield-halved"></i>
                This is a computer-generated receipt and does not require a physical signature. 
                For queries, contact <strong>contact@braingym.com</strong>.
            </div>
            <div class="actions no-print">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Print Receipt
                </button>
                <a href="index.php" class="btn btn-outline">
                    <i class="fa-solid fa-house"></i> Home
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

</body>
</html>

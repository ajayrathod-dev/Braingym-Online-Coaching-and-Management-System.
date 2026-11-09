<?php
session_start();
require_once __DIR__ . '/../connection.php';

// Authentication check
require 'auth_check.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch admission data
$stmt = $conn->prepare("SELECT * FROM admissions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admission = $result->fetch_assoc();

if (!$admission) {
    $_SESSION['error'] = "Admission record not found";
    header("Location: users.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $fields = [
        'name' => $_POST['name'],
        'dob' => $_POST['dob'],
        'gender' => $_POST['gender'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'aadhar' => $_POST['aadhar'],
        'father_name' => $_POST['father_name'],
        'mother_name' => $_POST['mother_name'],
        'qualification' => $_POST['qualification'],
        'course' => $_POST['course'],
        'installment' => $_POST['installment'],
        'amount' => intval($_POST['amount']),
        'status' => $_POST['status']
    ];

    // Update query
    $update_stmt = $conn->prepare("
        UPDATE admissions SET
            name = ?,
            dob = ?,
            gender = ?,
            email = ?,
            phone = ?,
            address = ?,
            aadhar = ?,
            father_name = ?,
            mother_name = ?,
            qualification = ?,
            course = ?,
            installment = ?,
            amount = ?,
            status = ?
        WHERE id = ?
    ");

    $update_stmt->bind_param("ssssssssssssisi",
        $fields['name'],
        $fields['dob'],
        $fields['gender'],
        $fields['email'],
        $fields['phone'],
        $fields['address'],
        $fields['aadhar'],
        $fields['father_name'],
        $fields['mother_name'],
        $fields['qualification'],
        $fields['course'],
        $fields['installment'],
        $fields['amount'],
        $fields['status'],
        $id
    );

    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Admission updated successfully";
        header("Location: users.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating record: " . $conn->error;
    }
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <h1>Edit Admission Record</h1>
        <a href="users.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <form method="POST" class="admission-form">
        <!-- Personal Information -->
        <div class="form-section">
            <h3>Personal Information</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($admission['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="<?= $admission['dob'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="Male" <?= $admission['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $admission['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= $admission['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Aadhar Number</label>
                    <input type="text" name="aadhar" pattern="\d{12}" value="<?= $admission['aadhar'] ?>" required>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="form-section">
            <h3>Contact Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $admission['email'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" pattern="[0-9]{10}" value="<?= $admission['phone'] ?>" required>
                </div>
                <div class="form-group full-width">
                    <label>Address</label>
                    <textarea name="address" rows="3" required><?= htmlspecialchars($admission['address']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Family Information -->
        <div class="form-section">
            <h3>Family Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Father's Name</label>
                    <input type="text" name="father_name" value="<?= htmlspecialchars($admission['father_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Mother's Name</label>
                    <input type="text" name="mother_name" value="<?= htmlspecialchars($admission['mother_name']) ?>" required>
                </div>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="form-section">
            <h3>Academic Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Qualification</label>
                    <input type="text" name="qualification" value="<?= htmlspecialchars($admission['qualification']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Course</label>
                    <select name="course" required>
                        <option value="BCA" <?= $admission['course'] == 'BCA' ? 'selected' : '' ?>>BCA</option>
                        <option value="BBA" <?= $admission['course'] == 'BBA' ? 'selected' : '' ?>>BBA</option>
                        <option value="BCom" <?= $admission['course'] == 'BCom' ? 'selected' : '' ?>>BCom</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="form-section">
            <h3>Payment Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Installment</label>
                    <select name="installment" required>
                        <option value="1st" <?= $admission['installment'] == '1st' ? 'selected' : '' ?>>1st Installment</option>
                        <option value="2nd" <?= $admission['installment'] == '2nd' ? 'selected' : '' ?>>2nd Installment</option>
                        <option value="full" <?= $admission['installment'] == 'full' ? 'selected' : '' ?>>Full Payment</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount (₹)</label>
                    <input type="number" name="amount" value="<?= $admission['amount'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="status" required>
                        <option value="pending" <?= $admission['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid" <?= $admission['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                                <option value="Half" <?= $admission['status'] == 'Half' ? 'selected' : '' ?>>Half payed</option>
                        <option value="25%" <?= $admission['status'] == '25%' ? 'selected' : '' ?>>25% Paid</option>

                    </select>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Update Admission
            </button>
        </div>
    </form>
</div>

<style>
.admission-form {
    background: white;
    padding: 2rem;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 1000px;
    margin: 0 auto;
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.form-section:last-child {
    border-bottom: none;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #1a2236;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #4f46e5;
    outline: none;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-actions {
    margin-top: 2rem;
    text-align: right;
}

.btn-save {
    background: #4f46e5;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.2s;
}

.btn-save:hover {
    background: #4338ca;
}
</style>

<?php include 'includes/footer.php'; ?>
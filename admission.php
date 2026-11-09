<?php session_start(); // Ensure session is started ?>

<?php include 'header.php' ; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Student Admission</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h3>Admission Form</h3>
    </div>
    <div class="card-body">
      <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="alert alert-danger">
          <strong>Login Required!</strong> Please login before submitting the admission form.
          <a href="login.php" class="btn btn-sm btn-light ms-2">Login Now</a>
        </div>
      <?php endif; ?>
      
      <form action="pay.php" method="POST">
        <!-- Store user ID in hidden field -->
        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?? '' ?>">
        
        <!-- Bio Data -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="col-md-3 mb-3">
            <label>Date of Birth</label>
            <input type="date" name="dob" class="form-control" required>
          </div>
          <div class="col-md-3 mb-3">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
              <option>Male</option><option>Female</option><option>Other</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" required>
          </div>
          <div class="col-md-12 mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control" required></textarea>
          </div>
          <div class="col-md-6 mb-3">
            <label>Aadhar Number</label>
            <input type="text" name="aadhar" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Father's Name</label>
            <input type="text" name="father_name" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Mother's Name</label>
            <input type="text" name="mother_name" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Highest Qualification</label>
            <input type="text" name="qualification" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Course Selected</label>
            <input type="text" name="course" class="form-control" required>
          </div>
        </div>

        <!-- Fees Section -->
        <hr>
        <div class="bg-light p-3 border rounded mb-3">
          <h5>💸 Fee Information</h5>
          <ul class="mb-2">
            <li><strong>Enrollment Fee:</strong> ₹500 (non-refundable, mandatory)</li>
            <li><strong>1st Installment:</strong> ₹1500</li>
            <li><strong>2nd Installment:</strong> ₹1500</li>
            <li><strong>Full Payment:</strong> ₹3000</li>
          </ul>
          <small class="text-muted">Please select one of the installment options below to proceed with payment.</small>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Choose Installment</label>
            <select name="installment_type" class="form-select" required>
              <option value="">-- Select Installment --</option>
              <option value="1st">1st Installment - ₹1500</option>
              <option value="2nd">2nd Installment - ₹1500</option>
              <option value="full">Full Payment - ₹3000</option>
            </select>
          </div>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-success w-100">💳 Enroll & Pay Now</button>

      </form>
    </div>
  </div>
</div>

</body>
<?php include 'footer.php' ; ?>

</html>

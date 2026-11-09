<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  die("🚫 Please log in to register for a competition.");
}

$comp_id = $_GET['comp_id'] ?? null;
if (!$comp_id) {
  die("❌ Invalid competition ID.");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Competition Registration</title>
  <style>
    .register-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 80vh;
      background: #f5f5f5;
    }
    .form-container {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 400px;
      margin-top: 50px;
      margin-bottom: 50px;
    }
h2 {
  color: #222 !important;
  text-align: center;
  margin-bottom: 20px;
  font-weight: bold;
  font-size: 24px;
  opacity: 1 !important;
  visibility: visible !important;
}
label {
  color: #333 !important;
  font-size: 16px;
  font-weight: 500;
  margin-bottom: 6px;
  display: block;
  opacity: 1 !important;
  visibility: visible !important;
}
    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      margin-top: 20px;
      padding: 10px;
      width: 100%;
      background-color: #28a745;
      color: white;
      border: none;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="register-wrapper">
  <div class="form-container">
    <h2>Competition Registration</h2>
    <form action="register_submit.php" method="POST">
      <input type="hidden" name="comp_id" value="<?= htmlspecialchars($comp_id) ?>">

      <label for="name">Participant Name</label>
      <input type="text" name="name" required>
<input type="text" name="name" required value="<?= htmlspecialchars($_SESSION['username'])?>">

      <label for="roll_no">Roll No</label>
      <input type="text" name="roll_no" required>

      <label for="class">Class</label>
      <input type="text" name="class" required>
      

      <label for="age">Age</label>
      <input type="number" name="age" required>

      <label for="mode">Participation Mode</label>
      <select name="mode" required>
        <option value="">-- Select Mode --</option>
        <option value="online">Online</option>
        <option value="offline">Offline</option>
      </select>

      <button type="submit">Submit ✅</button>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>

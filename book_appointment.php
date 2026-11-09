<?php
session_start();
require_once __DIR__ . '/connection.php';

$msg = "";
$user_id = $_SESSION['user_id'] ?? null;

function addNotification($title, $message) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notifications (title, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $message);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action == "book") {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $appointment_date = $_POST['appointment_date'] ?? '';
        $appointment_time = $_POST['appointment_time'] ?? '';
        
        // Validate inputs
        if (empty($name) || empty($email) || empty($appointment_date) || empty($appointment_time)) {
            $msg = "<div class='alert alert-danger'>Please fill all fields</div>";
        } elseif ($user_id === null) {
            $msg = "<div class='alert alert-danger'>You must be logged in</div>";
        } else {
            // Check for existing appointment at same time
            $check_stmt = $conn->prepare("SELECT id FROM appointments 
                                        WHERE email = ? 
                                        AND appointment_date = ? 
                                        AND appointment_time = ?");
            $check_stmt->bind_param("sss", $email, $appointment_date, $appointment_time);
            $check_stmt->execute();
            $check_stmt->store_result();
            
            if ($check_stmt->num_rows > 0) {
                $msg = "<div class='alert alert-danger'>An appointment already exists for this email at the selected time</div>";
            } else {
                // Insert new appointment
                $stmt = $conn->prepare("INSERT INTO appointments (name, email, appointment_date, appointment_time, user_id) 
                                       VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $name, $email, $appointment_date, $appointment_time, $user_id);
                
                if ($stmt->execute()) {
                    addNotification("New Appointment", "$name booked an appointment on $appointment_date at $appointment_time.");
                    $msg = "<div class='alert alert-success'>Appointment booked successfully!</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
            $check_stmt->close();
        }

    } elseif ($action == "cancel") {
        $appointment_id = $_POST['appointment_id'] ?? 0;
        
        if ($appointment_id > 0 && $user_id) {
            // Verify user owns the appointment
            $result = $conn->query("SELECT name FROM appointments 
                                   WHERE id = $appointment_id 
                                   AND user_id = $user_id");
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $name = $row['name'] ?? 'Unknown';
                
                // Delete appointment
                if ($conn->query("DELETE FROM appointments WHERE id = $appointment_id")) {
                    addNotification("Appointment Cancelled", "$name cancelled their appointment.");
                    $msg = "<div class='alert alert-danger'>Appointment cancelled.</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Error cancelling appointment</div>";
                }
            } else {
                $msg = "<div class='alert alert-danger'>Appointment not found or you don't have permission</div>";
            }
        } else {
            $msg = "<div class='alert alert-danger'>Invalid appointment</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 0;
            margin: 0;
            background:rgb(190, 235, 238);
        }
        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .appointment-form {
            padding: 20px;
            background: #f7f7f7;
            border-radius: 8px;
        }
        .appointments-list {
            padding: 20px;
        }
      
        .duplicate-error {
            color: #dc3545;
            font-size: 0.875em;
            display: none;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row g-0">
        <div class="col-md-6 appointment-form">
          <h3>Book an Appointment</h3>
          <?php echo $msg; ?>
          <form method="post" class="mt-3" id="appointmentForm">
            <input type="hidden" name="action" value="book">
            <div class="mb-2">
              <input type="text" name="name" class="form-control" placeholder="Your Name" required>
            </div>
            <div class="mb-2">
              <input type="email" name="email" class="form-control" placeholder="Your Email" required>
              <div class="duplicate-error" id="emailError">An appointment already exists at this time</div>
            </div>
            <div class="mb-2">
              <input type="date" name="appointment_date" class="form-control" required>
            </div>
            <div class="mb-2">
              <input type="time" name="appointment_time" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Book Appointment</button>
          </form>
        </div>

        <div class="col-md-6 appointments-list">
          <h3>Your Appointments</h3>
          <?php
          if ($user_id) {
              $sql = "SELECT * FROM appointments WHERE user_id = $user_id ORDER BY appointment_date, appointment_time";
              $result = $conn->query($sql);
              
              if ($result && $result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                      echo "
                      <div class='card mb-2'>
                          <div class='card-body'>
                              <h5 class='card-title'>{$row['name']}</h5>
                              <p class='card-text'>Date: {$row['appointment_date']} | Time: {$row['appointment_time']}</p>

                              <form method='post' class='d-inline'>
                                  <input type='hidden' name='action' value='cancel'>
                                  <input type='hidden' name='appointment_id' value='{$row['id']}'>
                                  <button type='submit' class='btn btn-danger btn-sm'>Cancel</button>
                              </form>

                              <form method='get' action='reschedule_appointment.php' class='d-inline'>
                                  <input type='hidden' name='id' value='{$row['id']}'>
                                  <button type='submit' class='btn btn-warning btn-sm'>Update</button>
                              </form>
                          </div>
                      </div>
                      ";
                  }
              } else {
                  echo "<p>No appointments booked yet.</p>";
              }
          } else {
              echo "<p>Please log in to view your appointments.</p>";
          }
          ?>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
</div>

<script>
document.getElementById('appointmentForm').addEventListener('submit', function(e) {
    const email = document.querySelector('input[name="email"]').value;
    const date = document.querySelector('input[name="appointment_date"]').value;
    const time = document.querySelector('input[name="appointment_time"]').value;
    const errorDiv = document.getElementById('emailError');
    
    // Client-side validation
    if (!email || !date || !time) {
        errorDiv.textContent = 'Please fill all fields';
        errorDiv.style.display = 'block';
        e.preventDefault();
    }
});
</script>

</body>
</html>
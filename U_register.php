
<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .form-box { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.2); width: 350px; }
        h2 { text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc; }
        .btn { background: #007BFF; color: white; border: none; cursor: pointer; transition: 0.3s; }
        .btn:hover { background: #0056b3; }
        .message { padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }
        .link { margin-top: 10px; text-align: center; }
        .success-box {
    background: #e6ffe6;
    border: 2px solid #28a745;
    padding: 20px;
    border-radius: 8px;
    margin-top: 15px;
    animation: popIn 0.3s ease-in-out;
}
.success-box h3 {
    color: #155724;
    margin-bottom: 10px;
}
.success-box .info {
    margin-bottom: 15px;
}
.success-box .notice {
    background: #fff3cd;
    padding: 10px;
    border: 1px solid #ffeeba;
    color: #856404;
    border-radius: 5px;
    margin-bottom: 10px;
}
.login-btn {
    display: inline-block;
    padding: 10px 15px;
    background: #007bff;
    color: white;
    border-radius: 4px;
    text-decoration: none;
}
.login-btn:hover {
    background: #0056b3;
}
@keyframes popIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

    </style>
</head>

</html>

<?php  
require_once __DIR__ . '/connection.php'; // Include database connection
session_start(); // Start session for messages

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    } else {
        // Check if user already exists
        $check_sql = "SELECT * FROM login WHERE email = ? OR username = ?";
        $stmt = $conn->prepare($check_sql);

        if ($stmt) {
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $errors[] = "Email or username already registered.";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare insert query
                $insert_sql = "INSERT INTO login (full_name, username, email, password) VALUES (?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);

                if ($insert_stmt) {
                    $insert_stmt->bind_param("ssss", $full_name, $username, $email, $hashed_password);

                    if ($insert_stmt->execute()) {
                        // ===== ADDED CODE START ===== //
                        // Get newly created user ID
                        $user_id = $insert_stmt->insert_id;
                        
                        // Store user ID in session
                        $_SESSION['user_id'] = $user_id;
                        // ===== ADDED CODE END ===== //
                        
                        // Existing success message
                        $success = "
<div class='success-box'>
    <h3>🎉 Welcome, $full_name!</h3>
    <p>Your account has been successfully created.</p>
    <div class='info'>
        <p><strong>👤 Username:</strong> $username</p>
        <p><strong>📧 Email:</strong> $email</p>
    </div>
    <div class='notice'>
        ⚠️ Please keep this information safe. Do not share your credentials with anyone.
        ⚠️ For your security, we don't display your password here.<br>
    Make sure you remember it, or use the 'Forgot Password?' option later.
    </div>
    
    <a href='login.php' class='login-btn'>Go to Login</a>
</div>
";

                    } else {
                        $errors[] = "Insert failed: " . $insert_stmt->error;
                    }
                } else {
                    $errors[] = "Prepare failed: " . $conn->error;
                }
            }
        } else {
            $errors[] = "Prepare failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
<style>
        body { font-family: Arial; background: #f2f2f2; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .form-box { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.2); width: 350px; }
        h2 { text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc; }
        .btn { background: #007BFF; color: white; border: none; cursor: pointer; transition: 0.3s; }
        .btn:hover { background: #0056b3; }
        .message { padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }
        .link { margin-top: 10px; text-align: center; }
        .success-box {
    background: #e6ffe6;
    border: 2px solid #28a745;
    padding: 20px;
    border-radius: 8px;
    margin-top: 15px;
    animation: popIn 0.3s ease-in-out;
}
.success-box h3 {
    color: #155724;
    margin-bottom: 10px;
}
.success-box .info {
    margin-bottom: 15px;
}
.success-box .notice {
    background: #fff3cd;
    padding: 10px;
    border: 1px solid #ffeeba;
    color: #856404;
    border-radius: 5px;
    margin-bottom: 10px;
}
.login-btn {
    display: inline-block;
    padding: 10px 15px;
    background: #007bff;
    color: white;
    border-radius: 4px;
    text-decoration: none;
}
.login-btn:hover {
    background: #0056b3;
}
@keyframes popIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

    </style>
</head>
<body>
    <div class="form-box">
        <h2>Register</h2>

        <?php if (!empty($errors)): ?>
            <div class="message error"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password (min 6 chars)" required>
            <input type="submit" class="btn" value="Register">
        </form>

        <div class="link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
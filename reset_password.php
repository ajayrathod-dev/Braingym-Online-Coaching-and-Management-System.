<?php
session_start();
require_once __DIR__ . '/connection.php';

if (!isset($_SESSION['reset_user'])) {
    header("Location: login.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... existing validation ...
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
       // ===== MODIFIED CODE START ===== //
    // Get user ID from session
    if (isset($_SESSION['reset_user_id'])) {
        $user_id = $_SESSION['reset_user_id'];
        
        // Update password using user ID
        $sql = "UPDATE login SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("si", $hashed_password, $user_id);
            // ... existing execution code ...
    // ===== MODIFIED CODE END ===== //
        }
    } else {
        $errors[] = "User session expired. Please try again.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .card-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .card-header h2 {
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }

        .alert {
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #0066cc;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0052a3;
        }

        .password-rules {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-link a {
            color: #0066cc;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card-header">
            <h2>Reset Your Password</h2>
            <p>Create a new secure password</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php echo implode('<br>', $errors); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="password" name="new_password" placeholder="New Password" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <div class="password-rules">
                <small>Use at least 8 characters with a mix of letters, numbers, and symbols</small>
            </div>

            <button type="submit">Update Password</button>
        </form>

        <div class="login-link">
            <a href="login.php">← Back to Login</a>
        </div>
    </div>
</body>
</html>
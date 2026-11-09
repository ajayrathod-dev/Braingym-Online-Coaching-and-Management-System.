<?php
session_start();
require_once __DIR__ . '/connection.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
// In forgot_password.php after user verification:
$_SESSION['reset_user'] = $username;       // Preserve existing
$_SESSION['reset_user_id'] = $user['id'];  // Add this line
    if (empty($username) || empty($email)) {
        $errors[] = "Both fields are required.";
    } else {
        $sql = "SELECT * FROM login WHERE username = ? AND email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                // User found — safe to reset
                $_SESSION['reset_user'] = $username;
                header("Location: reset_password.php");
                exit;
            } else {
                $errors[] = "Invalid credentials.";
            }
        } else {
            $errors[] = "Something went wrong.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
            <h2>Forgot Password?</h2>
            <p>Enter your username and email to reset your password</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php echo implode('<br>', $errors); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <button type="submit">Reset Password</button>
        </form>

        <div class="login-link">
            <a href="login.php">← Back to Login</a>
        </div>
    </div>
</body>
</html>
<?php
session_start();
require_once __DIR__ . '/../connection.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Only allow registration if no admins exist (first-time setup)
$result = $conn->query("SELECT COUNT(*) as count FROM admin_users");
$admin_count = $result->fetch_assoc()['count'];

$error = '';
$success = '';
$form_data = [
    'username' => '',
    'email' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data = [
        'username' => trim($_POST['username']),
        'email' => trim($_POST['email']),
        'password' => $_POST['password'],
        'invite_code' => trim($_POST['invite_code'])
    ];

    // Validate invitation code (change this secret code)
    $valid_invite_code = "BRAIN-GYM-ADMIN-2024";
    if ($form_data['invite_code'] !== $valid_invite_code) {
        $error = "Invalid invitation code";
    } else {
        // Validate inputs
        $errors = [];
        
        if (empty($form_data['username'])) {
            $errors[] = "Username is required";
        } elseif (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $form_data['username'])) {
            $errors[] = "Username must be 4-20 characters (letters, numbers, underscores)";
        }
        
        if (empty($form_data['email']) || !filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required";
        }
        
        if (empty($form_data['password']) || strlen($form_data['password']) < 8) {
            $errors[] = "Password must be at least 8 characters";
        } elseif (!preg_match('/[A-Z]/', $form_data['password']) || 
                 !preg_match('/[a-z]/', $form_data['password']) || 
                 !preg_match('/[0-9]/', $form_data['password'])) {
            $errors[] = "Password must contain uppercase, lowercase, and numbers";
        }

        // Check existing users
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM admin_users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $form_data['username'], $form_data['email']);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $errors[] = "Username or email already exists";
            }
        }

        if (empty($errors)) {
            $password_hash = password_hash($form_data['password'], PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("
                INSERT INTO admin_users 
                (username, email, password_hash, role) 
                VALUES (?, ?, ?, 'superadmin')
            ");
            $stmt->bind_param("sss", 
                $form_data['username'],
                $form_data['email'],
                $password_hash
            );

            if ($stmt->execute()) {
                $success = "Admin account created successfully!";
                $form_data = ['username' => '', 'email' => '']; // Clear form
            } else {
                $error = "Error creating account: " . $conn->error;
            }
        } else {
            $error = implode("<br>", $errors);
        }
    }
}

// Disable registration if admins already exist
if ($admin_count > 0 && empty($success)) {
    die("Registration is closed");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration | Brain Gym</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <style>
        .register-container {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
        }

        .register-box {
            background: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .register-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-form .form-group {
            margin-bottom: 1.5rem;
        }

        .register-form input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
        }

        .register-form button {
            width: 100%;
            padding: 0.75rem;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            margin-top: 1rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-logo">
                <h1>🧠 Create Admin Account</h1>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <div class="login-link">
                    <a href="login.php">Proceed to Login</a>
                </div>
            <?php else: ?>
                <form class="register-form" method="POST">
                    <div class="form-group">
                        <input type="text" name="username" 
                               placeholder="Username" 
                               value="<?= htmlspecialchars($form_data['username']) ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <input type="email" name="email" 
                               placeholder="Email" 
                               value="<?= htmlspecialchars($form_data['email']) ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <input type="password" name="password" 
                               placeholder="Password" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" name="invite_code" 
                               placeholder="Invitation Code" 
                               required>
                    </div>

                    <button type="submit">Create Account</button>
                </form>
                
                <div class="login-link">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
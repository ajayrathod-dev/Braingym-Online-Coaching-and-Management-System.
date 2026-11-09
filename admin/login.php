<?php
session_start();
require_once __DIR__ . '/../connection.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            
            if (password_verify($password, $admin['password_hash'])) {
                if ($admin['is_active']) {
                    // Update last login
                    $conn->query("UPDATE admin_users SET last_login = NOW() WHERE id = {$admin['id']}");
                    
                    // Set session
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_role'] = $admin['role'];
                    $_SESSION['admin_username'] = $admin['username'];
                    
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Account is disabled";
                }
            } else {
                $error = "Invalid credentials";
            }
        } else {
            $error = "Invalid credentials";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Brain Gym</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --accent: #8b5cf6;
            --error-bg: #fee2e2;
            --error-border: #ef4444;
            --error-text: #b91c1c;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --input-border: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #475569;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 600px;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .graphic-section {
            flex: 1;
            background: linear-gradient(135deg, rgba(79,70,229,0.85) 0%, rgba(139,92,246,0.85) 100%),
                        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=500&q=80') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .graphic-content {
            text-align: center;
            color: white;
            z-index: 2;
        }

        .graphic-content i {
            font-size: 4rem;
            margin-bottom: 30px;
        }

        .graphic-content h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .graphic-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 400px;
            margin: 0 auto;
        }

        .graphic-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 C30,0 0,30 0,50 C0,70 30,100 50,100 C70,100 100,70 100,50 C100,30 70,0 50,0 Z" fill="rgba(255,255,255,0.05)"/></svg>');
            background-size: cover;
        }

        .login-box {
            flex: 1;
            background: var(--card-bg);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-logo h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .login-logo h1 i {
            color: var(--accent);
        }

        .alert.alert-danger {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            color: var(--error-text);
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.8rem;
        }

        .form-group {
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 16px;
            top: 14px;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .login-form label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            padding-left: 10px;
        }

        .login-form input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid var(--input-border);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .login-form input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79,70,229,0.15);
        }

        .login-form button {
            padding: 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3);
            margin-top: 10px;
        }

        .login-form button:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(79, 70, 229, 0.4);
        }

        .login-form button:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .login-footer a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 500px;
            }
            
            .graphic-section {
                padding: 30px 20px;
            }
            
            .graphic-content h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .login-box {
                padding: 2rem;
            }
            
            .graphic-section {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="graphic-section">
            <div class="graphic-overlay"></div>
            <div class="graphic-content">
                <i class="fas fa-brain"></i>
                <h1>Brain Gym Admin Portal</h1>
                <p>Manage your training center with our intuitive administration tools. Securely access your dashboard and control your content.</p>
            </div>
        </div>
        
        <div class="login-box">
            <div class="login-logo">
                <h1><i class="fas fa-dumbbell"></i> Brain Gym Admin</h1>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form class="login-form" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div style="position: relative;">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" 
                               value="<?= htmlspecialchars($username) ?>" required
                               placeholder="Enter your username">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required
                               placeholder="Enter your password">
                    </div>
                </div>

                <button type="submit">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            
            <div class="login-footer">
                <p>Having trouble? <a href="#">Reset Password</a></p>
                <p>© <?= date('Y') ?> Brain Gym. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
        // Enhance form UX
        document.querySelector('form').addEventListener('submit', function() {
            const button = this.querySelector('button');
            button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Authenticating...`;
            button.disabled = true;
        });
        
        // Animation effect on input focus
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.parentElement.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
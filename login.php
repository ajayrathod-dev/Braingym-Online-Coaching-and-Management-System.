<?php
session_start();
require_once __DIR__ . '/connection.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errors[] = "Both fields are required.";
    } else {
        $sql = "SELECT id, username, password FROM login WHERE username = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    header("Location: index.php");
                    exit;
                } else {
                    $errors[] = "Invalid username or password.";
                }
            } else {
                $errors[] = "Invalid username or password.";
            }
            $stmt->close();
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Brain Gym</title>
  <link rel="icon" type="image/png" href="favicon.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    :root {
      --primary: #4361ee;
      --primary-dark: #3a56d4;
      --secondary: #7209b7;
      --accent: #06d6a0;
      --light: #f8f9fa;
      --dark: #1a2236;
      --success: #06d6a0;
      --error: #ef476f;
      --transition: all 0.3s ease;
    }

    body {
      background: linear-gradient(135deg, #2d005f, #6e00ff);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      animation: fadeIn 1.5s ease-in-out;
    }

    .login-container {
      display: flex;
      width: 100%;
      max-width: 1000px;
      min-height: 600px;
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    }

    .brand-section {
      flex: 1;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
      color: white;
      position: relative;
      overflow: hidden;
    }

    .brand-content {
      position: relative;
      z-index: 2;
      text-align: center;
      max-width: 400px;
    }

    .logo {
      font-size: 4rem;
      margin-bottom: 1.5rem;
      color: white;
      text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .brand-title {
      font-size: 2.5rem;
      font-weight: 800;
      margin-bottom: 1rem;
      text-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .brand-description {
      font-size: 1.1rem;
      line-height: 1.6;
      margin-bottom: 2rem;
      opacity: 0.9;
    }

    .brand-stats {
      display: flex;
      gap: 2rem;
      margin-top: 2rem;
    }

    .stat-item {
      text-align: center;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0.25rem;
    }

    .stat-label {
      font-size: 0.9rem;
      opacity: 0.8;
    }

    .decoration {
      position: absolute;
      width: 200px;
      height: 200px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      z-index: 1;
    }

    .decoration:nth-child(1) {
      top: -80px;
      right: -80px;
      width: 300px;
      height: 300px;
    }

    .decoration:nth-child(2) {
      bottom: -50px;
      left: -50px;
      width: 200px;
      height: 200px;
    }

    .decoration:nth-child(3) {
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 100px;
      height: 100px;
    }

    .form-section {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
      background: white;
    }

    .form-header {
      text-align: center;
      margin-bottom: 2.5rem;
    }

    .form-title {
      font-size: 2.2rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 0.5rem;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .form-subtitle {
      color: #666;
      font-size: 1.1rem;
    }

    .form-box {
      width: 100%;
      max-width: 400px;
    }

    .input-group {
      position: relative;
      margin-bottom: 1.8rem;
    }

    .input-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #777;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 15px 15px 15px 45px;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      outline: none;
      transition: var(--transition);
      font-size: 1rem;
    }

    input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .password-box {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      top: 50%;
      right: 15px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #777;
    }

    .remember-forgot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      font-size: 0.95rem;
    }

    .remember {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .remember input {
      accent-color: var(--primary);
    }

    .forgot-password {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
    }

    .forgot-password:hover {
      text-decoration: underline;
    }

    .btn {
      background: linear-gradient(to right, var(--primary), var(--secondary));
      border: none;
      padding: 16px;
      width: 100%;
      border-radius: 10px;
      color: white;
      font-weight: 600;
      font-size: 1.05rem;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.5rem;
      box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 1.5rem 0;
      color: #777;
      font-size: 0.9rem;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e0e0e0;
      margin: 0 10px;
    }

    .social-login {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .social-btn {
      flex: 1;
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
      background: white;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
      transition: var(--transition);
    }

    .social-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .social-btn.google {
      color: #DB4437;
    }

    .social-btn.facebook {
      color: #4267B2;
    }

    .register-link {
      text-align: center;
      font-size: 0.95rem;
      color: #666;
    }

    .register-link a {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
      transition: var(--transition);
    }

    .register-link a:hover {
      text-decoration: underline;
    }

    .message {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 10px;
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      animation: fadeIn 0.5s ease;
    }

    .error {
      background: #fdecea;
      color: var(--error);
      border-left: 4px solid var(--error);
    }

    .error-icon {
      font-size: 1.2rem;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes float {
      0% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-10px);
      }
      100% {
        transform: translateY(0);
      }
    }

    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
        min-height: auto;
      }
      
      .brand-section {
        padding: 30px 20px;
      }
      
      .form-section {
        padding: 30px 20px;
      }
      
      .brand-stats {
        display: none;
      }
    }
  </style>
</head>

<body>

  <div class="login-container">
    <!-- Brand Section -->
    <div class="brand-section">
      <div class="decoration"></div>
      <div class="decoration"></div>
      <div class="decoration"></div>
      
      <div class="brand-content">
        <div class="logo">
          <i class="fas fa-brain"></i>
        </div>
        <h1 class="brand-title">Brain Gym</h1>
        <p class="brand-description">Unlock your brain's potential with our mental math training program. Join thousands of students who have transformed their math skills.</p>
        
        <div class="brand-stats">
          <div class="stat-item">
            <div class="stat-value">10K+</div>
            <div class="stat-label">Students</div>
          </div>
          <div class="stat-item">
            <div class="stat-value">98%</div>
            <div class="stat-label">Success</div>
          </div>
          <div class="stat-item">
            <div class="stat-value">50+</div>
            <div class="stat-label">Trainers</div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Form Section -->
    <div class="form-section">
      <div class="form-header">
        <h2 class="form-title">Welcome Back</h2>
        <p class="form-subtitle">Sign in to access your dashboard</p>
      </div>
      
      <div class="form-box">
        <?php if (!empty($errors)): ?>
        <div class="message error">
          <i class="fas fa-exclamation-circle error-icon"></i>
          <div><?= htmlspecialchars(implode('<br>', $errors)) ?></div>
        </div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="on">
          
          <div class="input-group">
            <i class="fas fa-user input-icon"></i>
            <input type="text" name="username" placeholder="Username" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
          </div>
          
          <div class="input-group">
            <i class="fas fa-lock input-icon"></i>
            <div class="password-box">
              <input type="password" name="password" placeholder="Password" required autocomplete="off" id="passwordField">
              <i class="fas fa-eye password-toggle" id="togglePassword"></i>
            </div>
          </div>
          
          <div class="remember-forgot">
            <label class="remember">
              <input type="checkbox" name="remember">
              Remember me
            </label>
            <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
          </div>
          
          <button type="submit" class="btn">
            <i class="fas fa-sign-in-alt"></i> Login
          </button>
          
          <div class="divider">Or continue with</div>
          
          <div class="social-login">
            <button type="button" class="social-btn google">
              <i class="fab fa-google"></i> Google
            </button>
            <button type="button" class="social-btn facebook">
              <i class="fab fa-facebook-f"></i> Facebook
            </button>
          </div>
          
          <div class="register-link">
            Don't have an account? <a href="U_register.php">Create account</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('#passwordField');

    togglePassword.addEventListener('click', function () {
      const type = passwordField.type === 'password' ? 'text' : 'password';
      passwordField.type = type;
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });
    
    // Add floating animation to decorations
    document.querySelectorAll('.decoration').forEach((el, index) => {
      el.style.animation = `float ${6 + index}s ease-in-out infinite`;
    });
  </script>

</body>
</html>
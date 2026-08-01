<?php
/**
 * EDUsync School Management System - Login Page
 * Refined Human UX/UI Design - Modern, Clean & Authentic.
 */

require_once __DIR__ . '/config/Database.php';

session_start();

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $database = new Database();
        $db = $database->getConnection();

        $authenticated = false;
        $userName = 'Admin User';
        $userRole = 'Administrator';

        if ($db !== null) {
            $stmt = $db->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                // Verify password (plain text or hash check)
                if ($password === $user['password'] || password_verify($password, $user['password'])) {
                    $authenticated = true;
                    $userName = $user['full_name'];
                    $userRole = $user['role'];
                }
            }
        }

        // Allow authentication via DB or fallback for developer testing
        if ($authenticated || str_ends_with($email, '@uwu.ac.lk') || str_ends_with($email, '@edusync.edu')) {
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $userName;
            $_SESSION['user_role'] = $userRole;
            
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password. Please check your credentials.";
        }
    } else {
        $error = "Please enter both email and password to log in.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EDUsync School Management System</title>
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    
    <style>
    :root {
      --font-human-heading: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      --font-human-body: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body.login-body {
      margin: 0 !important;
      padding: 0 !important;
      height: 100vh !important;
      width: 100vw !important;
      overflow: hidden !important;
      background: 
        radial-gradient(circle at 75% 25%, #4f46e5 0%, rgba(79, 70, 229, 0) 52%),
        radial-gradient(circle at 20% 80%, #312e81 0%, rgba(49, 46, 129, 0) 55%),
        linear-gradient(135deg, #0f0c29 0%, #1b1446 40%, #241442 70%, #0d0722 100%) !important;
      font-family: var(--font-human-body) !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .login-container {
      display: flex !important;
      width: 90% !important;
      max-width: 980px !important;
      height: 540px !important;
      background: #ffffff !important;
      border-radius: 28px !important;
      box-shadow: 0 30px 80px -15px rgba(15, 23, 42, 0.65), 0 10px 30px -5px rgba(0, 0, 0, 0.25) !important;
      overflow: hidden !important;
    }

    .login-left {
      flex: 0 0 42% !important;
      width: 42% !important;
      background: #ffffff !important;
      display: flex !important;
      flex-direction: column !important;
      justify-content: center !important;
      padding: 44px 52px !important;
      box-sizing: border-box !important;
    }

    .login-form-wrapper {
      width: 100% !important;
      max-width: 320px !important;
      margin: 0 auto !important;
    }

    .login-header {
      margin-bottom: 24px !important;
    }

    .login-title {
      font-family: var(--font-human-heading) !important;
      font-size: 32px !important;
      font-weight: 800 !important;
      color: #0f172a !important;
      letter-spacing: -0.03em !important;
      margin: 0 0 4px 0 !important;
      line-height: 1.1 !important;
    }

    .login-subtitle {
      font-size: 13px !important;
      color: #64748b !important;
      font-weight: 400 !important;
      margin: 0 !important;
    }

    .login-error-msg {
      background: #fff1f2;
      color: #e11d48;
      border: 1px solid #fecdd3;
      padding: 10px 14px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 500;
      margin-bottom: 18px;
    }

    .input-field-group {
      display: flex !important;
      flex-direction: column !important;
      gap: 6px !important;
      margin-bottom: 16px !important;
    }

    .input-label {
      font-size: 12px !important;
      font-weight: 600 !important;
      color: #334155 !important;
      letter-spacing: -0.01em !important;
    }

    .input-box-wrapper {
      position: relative !important;
      display: flex !important;
      align-items: center !important;
    }

    .input-icon-left {
      position: absolute !important;
      left: 14px !important;
      color: #94a3b8 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      pointer-events: none !important;
      transition: color 0.2s ease !important;
    }

    .input-icon-right {
      position: absolute !important;
      right: 14px !important;
      color: #94a3b8 !important;
      background: none !important;
      border: none !important;
      cursor: pointer !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      padding: 0 !important;
      transition: color 0.2s ease !important;
    }

    .input-icon-right:hover {
      color: #475569 !important;
    }

    .login-input {
      width: 100% !important;
      height: 44px !important;
      background-color: #f8fafc !important;
      border: 1px solid #e2e8f0 !important;
      border-radius: 12px !important;
      padding: 0 44px !important;
      font-size: 13px !important;
      font-weight: 500 !important;
      color: #0f172a !important;
      outline: none !important;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
      box-sizing: border-box !important;
    }

    .login-input::placeholder {
      color: #94a3b8 !important;
      font-size: 13px !important;
      font-weight: 400 !important;
    }

    .login-input:focus {
      background-color: #ffffff !important;
      border-color: #6366f1 !important;
      box-shadow: 0 0 0 3.5px rgba(99, 102, 241, 0.15) !important;
    }

    .login-input:focus ~ .input-icon-left {
      color: #6366f1 !important;
    }

    .login-submit-btn {
      width: 100% !important;
      height: 46px !important;
      background: linear-gradient(180deg, #1e1b4b 0%, #0f172a 100%) !important;
      color: #ffffff !important;
      border: 1px solid rgba(255, 255, 255, 0.1) !important;
      border-radius: 23px !important;
      font-family: var(--font-human-heading) !important;
      font-size: 15px !important;
      font-weight: 700 !important;
      cursor: pointer !important;
      transition: all 0.2s ease !important;
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25) !important;
      margin-top: 8px !important;
    }

    .login-submit-btn:hover {
      background: linear-gradient(180deg, #2e2a72 0%, #1e293b 100%) !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 6px 16px rgba(15, 23, 42, 0.35) !important;
    }

    .login-submit-btn:active {
      transform: translateY(0) !important;
      box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2) !important;
    }

    .login-right {
      flex: 0 0 58% !important;
      width: 58% !important;
      background: #090c2e !important;
      position: relative !important;
      overflow: hidden !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      padding: 0 !important;
    }

    .login-right-full-img {
      width: 100% !important;
      height: 100% !important;
      object-fit: cover !important;
      object-position: center !important;
      display: block !important;
    }
    </style>
</head>
<body class="login-body">
    <div class="login-container">
        <!-- Left Side: Login Form -->
        <div class="login-left">
            <div class="login-form-wrapper">
                <div class="login-header">
                    <h1 class="login-title">Login</h1>
                    <p class="login-subtitle">Sign in to your EDUsync admin portal</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="login-error-msg">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" class="login-form">
                    <!-- Email Field -->
                    <div class="input-field-group">
                        <label class="input-label" for="email">Email</label>
                        <div class="input-box-wrapper">
                            <span class="input-icon-left">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="3"></rect>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                </svg>
                            </span>
                            <input type="email" id="email" name="email" class="login-input" placeholder="Enter your email" required autocomplete="email">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="input-field-group">
                        <label class="input-label" for="password">Password</label>
                        <div class="input-box-wrapper">
                            <span class="input-icon-left">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                            <input type="password" id="password" name="password" class="login-input" placeholder="Enter your password" required>
                            <button type="button" class="input-icon-right" id="togglePasswordBtn" aria-label="Toggle password visibility">
                                <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="login-submit-btn">Sign up</button>
                </form>
            </div>
        </div>

        <!-- Right Side: Dark Navy Hero Graphic -->
        <div class="login-right">
            <img src="assets/img/loginedusync.png" alt="EDUsync Welcome Illustration" class="login-right-full-img">
        </div>
    </div>

    <!-- Password Visibility Toggle Script -->
    <script>
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    eyeIcon.innerHTML = `<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path><line x1="2" y1="2" x2="22" y2="22"></line>`;
                } else {
                    eyeIcon.innerHTML = `<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle>`;
                }
            });
        }
    </script>
</body>
</html>

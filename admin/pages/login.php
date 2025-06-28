<?php
/**
 * Admin Login Page
 * Professional login interface for Virunga Homestay Admin
 */

// Include authentication middleware (which defines ADMIN_ACCESS and starts session)
require_once '../backend/api/utils/auth_middleware.php';

// Redirect if already logged in
if (isAuthenticated()) {
    $redirect_url = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
    unset($_SESSION['redirect_after_login']);
    header("Location: $redirect_url");
    exit();
}

$error_message = '';
$success_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!verifyCSRFToken($csrf_token)) {
        $error_message = 'Invalid security token. Please try again.';
    } else {
        // Attempt login
        $login_result = loginUser($username, $password);
        
        if ($login_result['success']) {
            $success_message = $login_result['message'];

            // Redirect after successful login
            $redirect_url = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
            unset($_SESSION['redirect_after_login']);

            // Add a small delay to show success message
            header("refresh:2;url=$redirect_url");
        } else {
            $error_message = $login_result['message'];
        }
    }
}

// Generate CSRF token
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: var(--primary-color);
            margin: 0;
            padding: 0;
        }
        
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        
        .login-card {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--box-shadow-lg);
            overflow: hidden;
        }
        
        .login-header {
            background: var(--gray-50);
            padding: 40px 30px 30px;
            text-align: center;
        }
        
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .login-logo i {
            font-size: 32px;
            color: var(--primary-color);
        }
        
        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0 0 5px 0;
        }
        
        .login-subtitle {
            color: var(--gray-600);
            font-size: 14px;
            margin: 0;
        }
        
        .login-form {
            padding: 30px;
        }
        
        .login-footer {
            background: var(--gray-50);
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: var(--gray-500);
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">
                        <i class="fas fa-mountain"></i>
                        <span>Virunga Homestay</span>
                    </div>
                    <h1 class="login-title">Admin Login</h1>
                    <p class="login-subtitle">Sign in to your account</p>
                </div>
                
                <div class="login-form">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle alert-icon"></i>
                            <?= htmlspecialchars($error_message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle alert-icon"></i>
                            <?= htmlspecialchars($success_message) ?>
                            <br><small>Redirecting to dashboard...</small>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" data-validate="true">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="form-group">
                            <label for="username" class="form-label required">Username</label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                class="form-control" 
                                required 
                                data-min-length="3"
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                placeholder="Enter your username"
                                autocomplete="username"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label required">Password</label>
                            <div class="password-input-container" style="position: relative;">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="form-control" 
                                    required 
                                    data-min-length="6"
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                >
                                <button 
                                    type="button" 
                                    class="password-toggle" 
                                    onclick="togglePassword()"
                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gray-400); cursor: pointer;"
                                >
                                    <i class="fas fa-eye" id="password-toggle-icon"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input 
                                    type="checkbox" 
                                    id="remember_me" 
                                    name="remember_me" 
                                    class="form-check-input"
                                    <?= isset($_POST['remember_me']) ? 'checked' : '' ?>
                                >
                                <label for="remember_me" class="form-check-label">
                                    Remember me
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                                <i class="fas fa-sign-in-alt"></i>
                                Sign In
                            </button>
                        </div>
                    </form>
                    
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="#" style="color: var(--secondary-color); text-decoration: none; font-size: 14px;">
                            Forgot your password?
                        </a>
                    </div>
                </div>
                
                <div class="login-footer">
                    <p>&copy; <?= date('Y') ?> Virunga Homestay. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../assets/js/forms.js"></script>
    
    <script>
        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Auto-focus username field
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            if (usernameField && !usernameField.value) {
                usernameField.focus();
            }
        });
        
        // Handle form submission with loading state
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            
            // Re-enable button after 5 seconds as fallback
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 5000);
        });
    </script>
</body>
</html>

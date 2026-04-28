<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true) {
    header("Location: dashboard.php");
    exit;
}

$login_error = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once __DIR__ . '/../config/db.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($username !== '' && $password !== '') {
        $stmt = $conn->prepare("SELECT id, password, status FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Temporary direct match check or password_verify if hashed
            $isValid = false;
            // Assuming we use bcrypt, but just in case of raw password match
            if (password_verify($password, $row['password'])) {
                $isValid = true;
            } else if ($row['password'] === $password) { // Temporary fallback if raw
                $isValid = true;
            }

            if ($row['status'] === 'active' && $isValid) {
                $_SESSION['admin_auth'] = true;
                $_SESSION['admin_id'] = $row['id'];
                
                $update = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $update->bind_param("i", $row['id']);
                $update->execute();
                
                header("Location: dashboard.php");
                exit;
            }
        }
        $login_error = true;
    } else {
        $login_error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Secure Access</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

   <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <div class="login-card" id="loginCard">
        <div class="brand-icon">
            <i class="fas fa-house-lock"></i>
        </div>
        
        <h1>Admin Portal</h1>

        <div id="error-message">
            <i class="fas fa-circle-exclamation"></i>
            <span>Invalid credentials. Please try again.</span>
        </div>

        <form id="loginForm" method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Admin username" required>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-key"></i>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="login-btn">
                Log In <i class="fas fa-arrow-right-to-bracket"></i>
            </button>
        </form>
    </div>

    <script>
        const loginCard = document.getElementById('loginCard');
        const errorMsg = document.getElementById('error-message');

        <?php if ($login_error): ?>
        // Show error immediately on page load if PHP flag is set
        showError();
        <?php endif; ?>

        function showError() {
            // 1. Show the error box
            errorMsg.style.display = 'flex';
            
            // 2. Add the shake animation class
            loginCard.classList.add('shake');
            
            // 3. Remove shake class after animation finishes so it can be re-triggered
            setTimeout(() => {
                loginCard.classList.remove('shake');
            }, 400);

            // 4. Highlight the inputs as "error"
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.style.borderColor = 'var(--color-error)';
            });
        }
    </script>
</body>
</html>
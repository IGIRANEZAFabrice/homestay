<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/db.php';

$msg = '';
$adminId = (int)$_SESSION['admin_id'];

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $passwordInput = $_POST['password'] ?? '';

    if (!empty($passwordInput)) {
        $hashedPass = password_hash($passwordInput, PASSWORD_BCRYPT);
        $conn->query("UPDATE admin_users SET 
                      full_name='$full_name', username='$username', email='$email', password='$hashedPass', updated_at=NOW() 
                      WHERE id=$adminId");
    } else {
        $conn->query("UPDATE admin_users SET 
                      full_name='$full_name', username='$username', email='$email', updated_at=NOW() 
                      WHERE id=$adminId");
    }
    
    $msg = "Your personal credentials have been updated successfully.";
}

// Fetch Current Details
$adminData = [
    'full_name' => '', 'username' => '', 'email' => '', 'role' => ''
];
$res = $conn->query("SELECT * FROM admin_users WHERE id = $adminId");
if ($res && $res->num_rows > 0) {
    $adminData = $res->fetch_assoc();
}

$pageTitle = 'Personal Profile Settings — Virunga Homestay CMS';
$currentPage = 'site-settings';

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pageTitle ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="css/header.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <style>
      .crud-form { display:flex; flex-direction:column; gap:20px; max-width:650px; background:var(--surface-1); padding:30px; border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-sm); }
      .crud-form .form-group { display:flex; flex-direction:column; gap:6px; }
      .crud-form label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); }
      .crud-form input, .crud-form select { padding: 12px; border: 1px solid var(--border); background:var(--surface-2); color:var(--text-1); border-radius:8px; font-family: inherit; font-size:14px; transition:border-color 0.2s; box-sizing:border-box;}
      .crud-form input:focus, .crud-form select:focus { border-color:var(--amber); outline:none; }
      .crud-form input[disabled] { opacity: 0.6; cursor: not-allowed; }
      .crud-btn { padding: 12px 20px; border:none; background:var(--amber); color:#000; border-radius:8px; cursor:pointer; font-weight:600; font-size:14px; display:inline-flex; align-items:center; justify-content:center; gap:8px;}
      .crud-btn:hover { filter:brightness(0.95); }
      .alert { padding:14px 20px; background:var(--success); color:#fff; border-radius:8px; margin-bottom:20px; font-size:14px; font-weight:500; display:flex; align-items:center; gap:10px; }
      .flex-row-gap { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
      @media(max-width:768px) { .flex-row-gap { grid-template-columns:1fr; } }
    </style>
  </head>
  <body>
    <?php include 'includes/header.php'; ?>

    <div class="dash-layout" id="dashLayout">
      <?php include 'includes/sidebar.php'; ?>

      <main class="dash-main">
        <div class="page-header">
          <div>
            <h1 class="page-header__title">Personal Profile & Security</h1>
            <p class="page-header__sub">Update your active administrator credentials, email, and password.</p>
          </div>
        </div>

        <div class="dash-grid gap-section" style="grid-template-columns: 1fr;">
          <div class="panel">
            <div class="panel__body">
            
              <?php if (!empty($msg)): ?>
                <div class="alert"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($msg); ?></div>
              <?php endif; ?>

              <form method="POST" action="site-settings.php" class="crud-form">
                
                <div class="form-group">
                  <label>Full Name</label>
                  <input type="text" name="full_name" required value="<?= htmlspecialchars((string)$adminData['full_name']) ?>" />
                </div>

                <div class="flex-row-gap">
                    <div class="form-group">
                      <label>Login Username *</label>
                      <input type="text" name="username" required value="<?= htmlspecialchars((string)$adminData['username']) ?>" />
                    </div>
                    <div class="form-group">
                      <label>Primary Email</label>
                      <input type="email" name="email" value="<?= htmlspecialchars((string)$adminData['email']) ?>" />
                    </div>
                </div>
                
                <div class="flex-row-gap">
                    <div class="form-group">
                      <label>New Password</label>
                      <input type="password" name="password" placeholder="Leave blank to keep current password" />
                    </div>
                    <div class="form-group">
                      <label>Current Role (Read-Only)</label>
                      <input type="text" value="<?= str_replace('_', ' ', strtoupper((string)$adminData['role'])) ?>" disabled />
                    </div>
                </div>
                
                <div class="form-group" style="margin-top:10px;">
                  <button type="submit" class="crud-btn"><i class="fa-solid fa-shield-halved"></i> Update My Credentials</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </main>
    </div>
    <script src="js/index.js"></script>
  </body>
</html>

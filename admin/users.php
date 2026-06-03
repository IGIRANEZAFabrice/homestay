<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';

// Handle Actions
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Prevent self-deletion if we tracked current user ID (assuming $_SESSION doesn't hold ID yet, we'll just guard ID=1 if it's the main super admin)
    if ($id === 1) {
        header("Location: users.php?msg=" . urlencode('Cannot delete the primary Super Admin.'));
        exit;
    }

    $conn->query("DELETE FROM admin_users WHERE id = $id");
    header("Location: users.php?msg=" . urlencode('Admin user deleted successfully.'));
    exit;
}

if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id === 1) {
        header("Location: users.php?msg=" . urlencode('Cannot suspend the primary Super Admin.'));
        exit;
    }

    $res = $conn->query("SELECT status FROM admin_users WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if ($row['status'] === 'active') $ns = 'suspended';
        elseif ($row['status'] === 'suspended') $ns = 'active';
        else $ns = 'active'; // inactive to active
        $conn->query("UPDATE admin_users SET status = '$ns' WHERE id = $id");
    }
    header("Location: users.php?msg=" . urlencode('User status toggled successfully.'));
    exit;
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $role = $conn->real_escape_string($_POST['role']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $passwordInput = $_POST['password'] ?? '';
    
    if ($id > 0) {
        // Update user
        if (!empty($passwordInput)) {
            $hashedPass = password_hash($passwordInput, PASSWORD_BCRYPT);
            $hashSql = "password='$hashedPass', ";
        } else {
            $hashSql = "";
        }
        
        $conn->query("UPDATE admin_users SET 
                      username='$username', email='$email', full_name='$full_name', 
                      role='$role', status='$status', $hashSql
                      updated_at=NOW() 
                      WHERE id=$id");
        $msg = "Admin user updated successfully";
    } else {
        // Insert new user
        $hashedPass = password_hash($passwordInput, PASSWORD_BCRYPT);
        $conn->query("INSERT INTO admin_users 
                      (username, password, email, full_name, role, status) 
                      VALUES 
                      ('$username', '$hashedPass', '$email', '$full_name', '$role', '$status')");
        $msg = "New admin user added successfully";
    }
    $action = 'list';
}

$pageTitle = 'Admin Users Management — Virunga Homestay CMS';
$currentPage = 'users';

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
            <h1 class="page-header__title"><?php echo ($action==='add' || $action==='edit') ? 'Account Editor' : 'Admin Security & Users'; ?></h1>
            <p class="page-header__sub">Create login access, reset passwords, and manage backend privileges.</p>
          </div>
          <div class="page-header__actions">
            <?php if ($action === 'list'): ?>
              <a href="users.php?action=add" class="btn btn-amber"><i class="fa-solid fa-user-plus"></i> Create Admin User</a>
            <?php else: ?>
              <a href="users.php" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Back to Administrators</a>
            <?php endif; ?>
          </div>
        </div>

        <div class="dash-grid gap-section" style="grid-template-columns: 1fr;">
          <div class="panel">
            <div class="panel__body">
            
              <?php if (!empty($msg)): ?>
                <div class="alert"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($msg); ?></div>
              <?php endif; ?>

              <?php if ($action === 'list'): ?>
                <div class="panel__body--tight" style="overflow-x:auto;">
                  <table class="page-table" style="width:100%; border-collapse:collapse; min-width:800px;">
                    <thead style="border-bottom:2px solid var(--border); text-align:left;">
                      <tr>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Identity</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Contact</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Role & Access</th>
                        <th style="padding:15px; text-align:right;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $res = $conn->query("SELECT * FROM admin_users ORDER BY id ASC");
                      if ($res && $res->num_rows > 0):
                          while($row = $res->fetch_assoc()):
                      ?>
                      <tr style="border-bottom:1px solid var(--surface-3);">
                        <td style="padding:15px; display:flex; align-items:center; gap:16px;">
                          <div style="width:40px; height:40px; border-radius:50%; background:var(--surface-3); display:flex; align-items:center; justify-content:center; color:var(--text-2);">
                             <i class="fa-solid fa-user"></i>
                          </div>
                          <div>
                              <strong style="color:var(--text-1); font-size:15px; display:block; margin-bottom:4px;"><?= htmlspecialchars($row['full_name']) ?></strong>
                              <span style="font-size:12px; color:var(--text-3);">@<?= htmlspecialchars($row['username']) ?></span>
                          </div>
                        </td>
                        <td style="padding:15px; font-size:13px; color:var(--text-2);">
                          <div style="margin-bottom:4px;"><i class="fa-solid fa-envelope"></i> <?= $row['email'] ? htmlspecialchars($row['email']) : '-' ?></div>
                        </td>
                        <td style="padding:15px;">
                          <div style="margin-bottom:6px; font-size:13px; font-weight:600; color:var(--amber); text-transform:uppercase;"><i class="fa-solid fa-shield-halved"></i> <?= str_replace('_', ' ', htmlspecialchars($row['role'])) ?></div>
                          <a href="users.php?action=toggle&id=<?= $row['id'] ?>" style="text-decoration:none;">
                              <span class="status-pill <?= $row['status']=='active' ? 'status-pill--live' : ($row['status']=='suspended'?'status-pill--draft':'') ?>" style="<?= $row['status']=='inactive'?'background:var(--surface-3); color:var(--text-3);':'' ?>">
                                  <i class="fa-solid fa-circle" style="font-size: 6px"></i> <?= ucfirst($row['status']) ?>
                              </span>
                          </a>
                        </td>
                        <td style="padding:15px; text-align:right;">
                          <a href="users.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-ghost" style="padding:6px 12px; font-size:12px;"><i class="fa-solid fa-pen"></i></a>
                          <?php if($row['id'] != 1): ?>
                          <a href="users.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to permanently revoke this user?')" class="btn btn-ghost" style="padding:6px 12px; font-size:12px; color:var(--danger)"><i class="fa-solid fa-trash"></i></a>
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php 
                          endwhile; 
                      else:
                      ?>
                          <tr><td colspan="4" style="text-align:center; padding:30px; color:var(--text-3);">No users discovered!</td></tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

              <?php elseif ($action === 'add' || $action === 'edit'): 
                  $r = [
                    'id'=>0, 'username'=>'', 'email'=>'', 'full_name'=>'', 'role'=>'admin', 'status'=>'active'
                  ];
                  if($action === 'edit' && isset($_GET['id'])) {
                      $res = $conn->query("SELECT * FROM admin_users WHERE id=".(int)$_GET['id']);
                      if($res && $res->num_rows>0) $r = $res->fetch_assoc();
                  }
              ?>
                <form method="POST" action="users.php" class="crud-form">
                  <input type="hidden" name="id" value="<?= $r['id'] ?>" />
                  
                  <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" required value="<?= htmlspecialchars((string)$r['full_name']) ?>" placeholder="e.g. John Doe" />
                  </div>

                  <div class="flex-row-gap">
                      <div class="form-group">
                        <label>Login Username *</label>
                        <input type="text" name="username" required placeholder="e.g. jdoe_admin" value="<?= htmlspecialchars((string)$r['username']) ?>" />
                      </div>
                      <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="e.g. user@virungahomestay.com" value="<?= htmlspecialchars((string)$r['email']) ?>" />
                      </div>
                  </div>
                  
                  <div class="flex-row-gap">
                      <div class="form-group">
                        <label>Account Password <?= $action==='edit' ? '' : '*' ?></label>
                        <input type="password" name="password" <?= $action==='add' ? 'required' : '' ?> placeholder="<?= $action==='edit' ? 'Leave blank to retain current password' : 'Create strong password' ?>" />
                      </div>
                      <div class="form-group">
                        <label>System Role *</label>
                        <select name="role" required>
                            <option value="super_admin" <?= $r['role']=='super_admin'?'selected':'' ?>>Super Administrator (Full Access)</option>
                            <option value="admin" <?= $r['role']=='admin'?'selected':'' ?>>Administrator (Content Manager)</option>
                            <option value="moderator" <?= $r['role']=='moderator'?'selected':'' ?>>Moderator (Read & Reply Only)</option>
                        </select>
                      </div>
                  </div>
                  
                  <div class="form-group">
                    <label>Account Status *</label>
                    <select name="status" required>
                        <option value="active" <?= $r['status']=='active'?'selected':'' ?>>Active (Can Login)</option>
                        <option value="suspended" <?= $r['status']=='suspended'?'selected':'' ?>>Suspended (Access Revoked)</option>
                        <option value="inactive" <?= $r['status']=='inactive'?'selected':'' ?>>Inactive</option>
                    </select>
                  </div>
                  
                  <div class="form-group" style="margin-top:10px;">
                    <button type="submit" class="crud-btn"><i class="fa-solid fa-user-shield"></i> Save Admin Profile</button>
                  </div>
                </form>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </main>
    </div>
    <script src="js/index.js"></script>
  </body>
</html>

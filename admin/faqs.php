<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

$msg = '';
$error = '';

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $question = $conn->real_escape_string($_POST['question']);
        $answer = $conn->real_escape_string($_POST['answer']);
        $display_order = (int)$_POST['display_order'];
        $status = $conn->real_escape_string($_POST['status']);

        if ($_POST['action'] === 'add') {
            $sql = "INSERT INTO faqs (question, answer, display_order, status) VALUES ('$question', '$answer', $display_order, '$status')";
            if ($conn->query($sql)) {
                $msg = "FAQ added successfully!";
            } else {
                $error = "Error adding FAQ: " . $conn->error;
            }
        } elseif ($_POST['action'] === 'edit') {
            $id = (int)$_POST['id'];
            $sql = "UPDATE faqs SET question='$question', answer='$answer', display_order=$display_order, status='$status' WHERE id=$id";
            if ($conn->query($sql)) {
                $msg = "FAQ updated successfully!";
            } else {
                $error = "Error updating FAQ: " . $conn->error;
            }
        }
    }
}

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($conn->query("DELETE FROM faqs WHERE id=$id")) {
        $msg = "FAQ deleted successfully!";
    } else {
        $error = "Error deleting FAQ: " . $conn->error;
    }
}

// Fetch FAQs
$faqs = $conn->query("SELECT * FROM faqs ORDER BY display_order ASC, id DESC");

$pageTitle = 'Manage FAQs — Virunga Homestay CMS';
$currentPage = 'faqs';

// Get current FAQ for editing
$editFaq = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT * FROM faqs WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $editFaq = $res->fetch_assoc();
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/header.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <style>
      .crud-form { display:flex; flex-direction:column; gap:20px; max-width:800px; background:var(--surface-1); padding:30px; border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-sm); margin-bottom: 30px; }
      .crud-form .form-group { display:flex; flex-direction:column; gap:6px; }
      .crud-form label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); }
      .crud-form input, .crud-form select, .crud-form textarea { padding: 12px; border: 1px solid var(--border); background:var(--surface-2); color:var(--text-1); border-radius:8px; font-family: inherit; font-size:14px; transition:border-color 0.2s; box-sizing:border-box;}
      .crud-form input:focus, .crud-form select:focus, .crud-form textarea:focus { border-color:var(--amber); outline:none; }
      .crud-btn { padding: 12px 20px; border:none; background:var(--amber); color:#000; border-radius:8px; cursor:pointer; font-weight:600; font-size:14px; display:inline-flex; align-items:center; justify-content:center; gap:8px;}
      .crud-btn:hover { filter:brightness(0.95); }
      .alert { padding:14px 20px; background:var(--success); color:#fff; border-radius:8px; margin-bottom:20px; font-size:14px; font-weight:500; display:flex; align-items:center; gap:10px; }
      .alert--error { background:var(--danger); }
      .faq-table { width:100%; border-collapse:collapse; background:var(--surface-1); border-radius:var(--radius-lg); overflow:hidden; border:1px solid var(--border); }
      .faq-table th { text-align:left; padding:15px; background:var(--surface-2); font-size:12px; font-weight:600; text-transform:uppercase; color:var(--text-2); border-bottom:1px solid var(--border); }
      .faq-table td { padding:15px; border-bottom:1px solid var(--border); color:var(--text-1); font-size:14px; vertical-align: top; }
      .faq-table tr:last-child td { border-bottom:none; }
      .actions { display:flex; gap:10px; }
      .status-badge { padding:4px 8px; border-radius:4px; font-size:11px; font-weight:600; text-transform:uppercase; }
      .status-badge--active { background:rgba(46, 184, 160, 0.15); color:var(--teal); }
      .status-badge--inactive { background:rgba(224, 90, 74, 0.15); color:var(--danger); }
    </style>
  </head>
  <body>
    <?php include 'includes/header.php'; ?>

    <div class="dash-layout" id="dashLayout">
      <?php include 'includes/sidebar.php'; ?>

      <main class="dash-main">
        <div class="page-header">
          <div>
            <h1 class="page-header__title">Frequently Asked Questions</h1>
            <p class="page-header__sub">Manage the FAQs displayed on the contact page.</p>
          </div>
          <div class="page-header__actions">
            <a href="faqs.php" class="btn btn-amber"><i class="fa-solid fa-plus"></i> Add New FAQ</a>
          </div>
        </div>

        <div class="dash-grid gap-section" style="grid-template-columns: 1fr;">
          <div class="panel">
            <div class="panel__body">
            
              <?php if (!empty($msg)): ?>
                <div class="alert"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($msg); ?></div>
              <?php endif; ?>
              
              <?php if (!empty($error)): ?>
                <div class="alert alert--error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
              <?php endif; ?>

              <!-- Form -->
              <form method="POST" action="faqs.php" class="crud-form">
                <input type="hidden" name="action" value="<?= $editFaq ? 'edit' : 'add' ?>" />
                <?php if ($editFaq): ?>
                  <input type="hidden" name="id" value="<?= $editFaq['id'] ?>" />
                <?php endif; ?>

                <div class="form-group">
                  <label>Question</label>
                  <input type="text" name="question" required value="<?= $editFaq ? htmlspecialchars($editFaq['question']) : '' ?>" placeholder="e.g. Is breakfast included?" />
                </div>

                <div class="form-group">
                  <label>Answer</label>
                  <textarea name="answer" required rows="4" placeholder="Enter the detailed answer..."><?= $editFaq ? htmlspecialchars($editFaq['answer']) : '' ?></textarea>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                  <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="display_order" value="<?= $editFaq ? $editFaq['display_order'] : '0' ?>" />
                  </div>
                  <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                      <option value="active" <?= ($editFaq && $editFaq['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                      <option value="inactive" <?= ($editFaq && $editFaq['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                  </div>
                </div>

                <div class="form-group" style="margin-top:10px;">
                  <button type="submit" class="crud-btn">
                    <i class="fa-solid <?= $editFaq ? 'fa-save' : 'fa-plus' ?>"></i> 
                    <?= $editFaq ? 'Update FAQ' : 'Add FAQ' ?>
                  </button>
                  <?php if ($editFaq): ?>
                    <a href="faqs.php" style="margin-left:10px; font-size:14px; color:var(--text-3);">Cancel</a>
                  <?php endif; ?>
                </div>
              </form>

              <!-- Table -->
              <table class="faq-table">
                <thead>
                  <tr>
                    <th style="width:50px;">Order</th>
                    <th>Question / Answer</th>
                    <th style="width:100px;">Status</th>
                    <th style="width:120px;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($faqs && $faqs->num_rows > 0): ?>
                    <?php while($row = $faqs->fetch_assoc()): ?>
                      <tr>
                        <td><?= $row['display_order'] ?></td>
                        <td>
                          <strong><?= htmlspecialchars($row['question']) ?></strong><br/>
                          <p style="margin-top:5px; color:var(--text-2); font-size:13px; line-height:1.4;">
                            <?= nl2br(htmlspecialchars($row['answer'])) ?>
                          </p>
                        </td>
                        <td>
                          <span class="status-badge status-badge--<?= $row['status'] ?>">
                            <?= ucfirst($row['status']) ?>
                          </span>
                        </td>
                        <td class="actions">
                          <a href="faqs.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-ghost" style="padding:6px 10px; font-size:12px;"><i class="fa-solid fa-pen"></i></a>
                          <a href="faqs.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-ghost" style="padding:6px 10px; font-size:12px; color:var(--danger)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" style="text-align:center; padding:40px; color:var(--text-3);">No FAQs found. Add your first one above.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>

            </div>
          </div>
        </div>
      </main>
    </div>
    <script src="js/index.js"></script>
  </body>
</html>

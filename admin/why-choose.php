<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

$action  = $_GET['action'] ?? 'list';
$msg     = $_GET['msg']    ?? '';
$msgType = 'success';

// ── DELETE ──────────────────────────────────────────────────────────────────
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM home_why WHERE id = $id");
    header("Location: why-choose.php?msg=" . urlencode('Item deleted successfully.'));
    exit;
}

// ── TOGGLE STATUS ────────────────────────────────────────────────────────────
if ($action === 'toggle' && isset($_GET['id'])) {
    $id  = (int)$_GET['id'];
    $res = $conn->query("SELECT status FROM home_why WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        $ns = $row['status'] === 'active' ? 'inactive' : 'active';
        $conn->query("UPDATE home_why SET status='$ns' WHERE id=$id");
    }
    header("Location: why-choose.php?msg=" . urlencode('Status updated.'));
    exit;
}

// ── REORDER ──────────────────────────────────────────────────────────────────
if ($action === 'order' && isset($_GET['id'], $_GET['dir'])) {
    $id  = (int)$_GET['id'];
    $dir = $_GET['dir'] === 'up' ? 'up' : 'down';
    $res = $conn->query("SELECT display_order FROM home_why WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        $cur = (int)$row['display_order'];
        if ($dir === 'up') {
            $adj = $conn->query("SELECT id, display_order FROM home_why WHERE display_order < $cur ORDER BY display_order DESC LIMIT 1");
        } else {
            $adj = $conn->query("SELECT id, display_order FROM home_why WHERE display_order > $cur ORDER BY display_order ASC LIMIT 1");
        }
        if ($adjRow = $adj->fetch_assoc()) {
            $adjId  = (int)$adjRow['id'];
            $adjOrd = (int)$adjRow['display_order'];
            $conn->query("UPDATE home_why SET display_order=$adjOrd WHERE id=$id");
            $conn->query("UPDATE home_why SET display_order=$cur WHERE id=$adjId");
        }
    }
    header("Location: why-choose.php?msg=" . urlencode('Order updated.'));
    exit;
}

// ── FORM SUBMIT (ADD / EDIT) ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = (int)($_POST['id'] ?? 0);
    $title         = $conn->real_escape_string(trim($_POST['title'] ?? ''));
    $body          = $conn->real_escape_string(trim($_POST['body']  ?? ''));
    $icon          = $conn->real_escape_string(trim($_POST['icon']  ?? 'fa-solid fa-star'));
    $display_order = (int)($_POST['display_order'] ?? 1);
    $status        = $conn->real_escape_string($_POST['status'] ?? 'active');

    if (empty($title)) {
        $msg     = 'Title is required.';
        $msgType = 'error';
        $action  = $id > 0 ? 'edit' : 'add';
    } else {
        if ($id > 0) {
            $conn->query("UPDATE home_why SET title='$title', body='$body', icon='$icon', display_order=$display_order, status='$status' WHERE id=$id");
            $msg = 'Item updated successfully.';
        } else {
            $conn->query("INSERT INTO home_why (title, body, icon, display_order, status) VALUES ('$title', '$body', '$icon', $display_order, '$status')");
            $msg = 'Item added successfully.';
        }
        $action = 'list';
    }
}

$pageTitle   = 'Why Choose Us — Virunga Homestay CMS';
$currentPage = 'why-choose';
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
      /* ── Form ── */
      .crud-form {
        display: flex; flex-direction: column; gap: 22px;
        max-width: 700px;
        background: var(--surface-1); padding: 32px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border); box-shadow: var(--shadow-sm);
      }
      .crud-form .form-group { display: flex; flex-direction: column; gap: 7px; }
      .crud-form label {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.6px; color: var(--text-2);
      }
      .crud-form input[type="text"],
      .crud-form input[type="number"],
      .crud-form textarea,
      .crud-form select {
        padding: 12px 14px; border: 1px solid var(--border);
        background: var(--surface-2); color: var(--text-1);
        border-radius: 8px; font-family: inherit; font-size: 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
      }
      .crud-form input:focus,
      .crud-form textarea:focus,
      .crud-form select:focus {
        border-color: var(--amber); outline: none;
        box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
      }
      .crud-form textarea { resize: vertical; min-height: 100px; line-height: 1.6; }
      .crud-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 13px 24px; border: none; background: var(--amber); color: #000;
        border-radius: 9px; cursor: pointer; font-weight: 700; font-size: 14px;
        transition: filter 0.2s, transform 0.1s;
      }
      .crud-btn:hover { filter: brightness(0.92); transform: translateY(-1px); }

      /* ── Alert ── */
      .alert {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 20px; border-radius: 9px; margin-bottom: 22px;
        font-size: 14px; font-weight: 500; animation: slideIn 0.3s ease;
      }
      @keyframes slideIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
      .alert--success { background: var(--success); color: #fff; }
      .alert--error   { background: var(--danger);  color: #fff; }

      /* ── Why Cards Grid ── */
      .why-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 22px;
      }
      .why-card-admin {
        background: var(--surface-1);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        transition: box-shadow 0.25s, transform 0.2s;
        position: relative;
      }
      .why-card-admin:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }

      .why-card-admin__header {
        padding: 24px 24px 16px;
        display: flex; align-items: center; gap: 16px;
      }
      .why-card-admin__icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        background: rgba(212,175,55,0.1);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
      }
      .why-card-admin__icon i { font-size: 20px; color: var(--amber); }
      .why-card-admin__title {
        font-size: 16px; font-weight: 600; color: var(--text-1);
        line-height: 1.3;
      }
      .why-card-admin__body {
        padding: 0 24px 20px;
        font-size: 13px; color: var(--text-2); line-height: 1.6;
        display: -webkit-box; -webkit-line-clamp: 3;
        -webkit-box-orient: vertical; overflow: hidden;
      }

      .why-card-admin__order {
        position: absolute; top: 12px; right: 60px;
        background: rgba(0,0,0,.5); backdrop-filter: blur(6px);
        color: #fff; font-size: 11px; font-weight: 700;
        padding: 4px 10px; border-radius: 20px;
      }
      .why-card-admin__status {
        position: absolute; top: 12px; right: 12px;
      }

      .why-card-admin__actions {
        display: flex; align-items: center; gap: 8px;
        padding: 14px 20px;
        border-top: 1px solid var(--surface-3);
        background: var(--surface-2);
      }
      .why-card-admin__actions a,
      .why-card-admin__actions button {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px; border-radius: 7px;
        font-size: 12px; font-weight: 600; text-decoration: none;
        cursor: pointer; border: none;
        transition: background 0.18s, color 0.18s;
      }
      .btn-edit  { background: var(--surface-3); color: var(--text-1); }
      .btn-edit:hover  { background: var(--amber); color: #000; }
      .btn-del   { background: transparent; color: var(--danger); }
      .btn-del:hover   { background: rgba(220,38,38,.12); }
      .btn-order { background: var(--surface-3); color: var(--text-2); padding: 6px 10px; }
      .btn-order:hover { background: var(--surface-1); color: var(--text-1); }
      .ms-auto { margin-left: auto; }

      /* ── Empty ── */
      .empty-state { text-align: center; padding: 60px 20px; color: var(--text-3); }
      .empty-state i { font-size: 48px; margin-bottom: 14px; opacity: 0.4; display: block; }

      /* ── Icon preview ── */
      .icon-hint { font-size: 12px; color: var(--text-3); margin-top: 4px; line-height: 1.5; }
      .icon-hint a { color: var(--amber); text-decoration: underline; }
      .icon-live-preview {
        display: inline-flex; align-items: center; justify-content: center;
        width: 44px; height: 44px; background: rgba(212,175,55,0.1);
        color: var(--amber); border-radius: 10px; font-size: 18px; margin-top: 6px;
      }
      code { font-size: 12px; background: var(--surface-3); padding: 2px 6px; border-radius: 4px; }

      .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
      @media (max-width: 600px) { .form-row-2 { grid-template-columns: 1fr; } }
    </style>
  </head>
  <body>
    <?php include 'includes/header.php'; ?>

    <div class="dash-layout" id="dashLayout">
      <?php include 'includes/sidebar.php'; ?>

      <main class="dash-main">
        <div class="page-header">
          <div>
            <h1 class="page-header__title">
              <?php echo ($action === 'add' || $action === 'edit') ? 'Why Choose Us — Editor' : 'Why Choose Us'; ?>
            </h1>
            <p class="page-header__sub">
              <?php echo ($action === 'list')
                ? 'Manage the "Why Choose Virunga Homestay" cards shown on the homepage.'
                : 'Fill in the details for this card.'; ?>
            </p>
          </div>
          <div class="page-header__actions">
            <?php if ($action === 'list'): ?>
              <a href="why-choose.php?action=add" class="btn btn-amber">
                <i class="fa-solid fa-plus"></i> Add Card
              </a>
            <?php else: ?>
              <a href="why-choose.php" class="btn btn-ghost">
                <i class="fa-solid fa-arrow-left"></i> Back to List
              </a>
            <?php endif; ?>
          </div>
        </div>

        <div class="dash-grid gap-section" style="grid-template-columns: 1fr;">
          <div class="panel">
            <div class="panel__body">

              <?php if (!empty($msg)): ?>
                <div class="alert alert--<?= $msgType === 'error' ? 'error' : 'success' ?>">
                  <i class="fa-solid <?= $msgType === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check' ?>"></i>
                  <?= htmlspecialchars($msg) ?>
                </div>
              <?php endif; ?>

              <!-- ══ LIST ══════════════════════════════════════════════ -->
              <?php if ($action === 'list'): ?>
                <?php
                  $res = $conn->query("SELECT * FROM home_why ORDER BY display_order ASC, id ASC");
                  $items = [];
                  if ($res && $res->num_rows > 0) {
                      while ($row = $res->fetch_assoc()) $items[] = $row;
                  }
                ?>

                <?php if (empty($items)): ?>
                  <div class="empty-state">
                    <i class="fa-solid fa-star-half-stroke"></i>
                    <p>No items yet. Click <strong>Add Card</strong> to get started.</p>
                  </div>
                <?php else: ?>
                  <div class="why-grid">
                    <?php foreach ($items as $idx => $item):
                      $isFirst = ($idx === 0);
                      $isLast  = ($idx === count($items) - 1);
                    ?>
                    <div class="why-card-admin">
                      <span class="why-card-admin__order">#<?= (int)$item['display_order'] ?></span>
                      <span class="why-card-admin__status">
                        <a href="why-choose.php?action=toggle&id=<?= $item['id'] ?>" style="text-decoration:none;" title="Toggle status">
                          <span class="status-pill <?= $item['status'] === 'active' ? 'status-pill--live' : '' ?>"
                                style="<?= $item['status'] === 'inactive' ? 'background:rgba(0,0,0,.5); color:#ccc;' : '' ?>">
                            <i class="fa-solid fa-circle" style="font-size:6px;"></i> <?= ucfirst($item['status']) ?>
                          </span>
                        </a>
                      </span>

                      <div class="why-card-admin__header">
                        <div class="why-card-admin__icon">
                          <i class="<?= htmlspecialchars($item['icon'] ?? 'fa-solid fa-star') ?>"></i>
                        </div>
                        <div class="why-card-admin__title"><?= htmlspecialchars($item['title']) ?></div>
                      </div>
                      <div class="why-card-admin__body"><?= htmlspecialchars($item['body']) ?></div>

                      <div class="why-card-admin__actions">
                        <?php if (!$isFirst): ?>
                          <a href="why-choose.php?action=order&id=<?= $item['id'] ?>&dir=up" class="btn-order" title="Move up">
                            <i class="fa-solid fa-arrow-up"></i>
                          </a>
                        <?php endif; ?>
                        <?php if (!$isLast): ?>
                          <a href="why-choose.php?action=order&id=<?= $item['id'] ?>&dir=down" class="btn-order" title="Move down">
                            <i class="fa-solid fa-arrow-down"></i>
                          </a>
                        <?php endif; ?>
                        <span class="ms-auto"></span>
                        <a href="why-choose.php?action=edit&id=<?= $item['id'] ?>" class="btn-edit">
                          <i class="fa-solid fa-pen"></i> Edit
                        </a>
                        <a href="why-choose.php?action=delete&id=<?= $item['id'] ?>"
                           class="btn-del"
                           onclick="return confirm('Delete this card permanently?')">
                          <i class="fa-solid fa-trash"></i> Delete
                        </a>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

              <!-- ══ ADD / EDIT ════════════════════════════════════════ -->
              <?php elseif ($action === 'add' || $action === 'edit'):
                $r = [
                  'id'            => 0,
                  'title'         => '',
                  'body'          => '',
                  'icon'          => 'fa-solid fa-star',
                  'display_order' => 1,
                  'status'        => 'active',
                ];
                if ($action === 'edit' && isset($_GET['id'])) {
                    $eid = (int)$_GET['id'];
                    $eres = $conn->query("SELECT * FROM home_why WHERE id = $eid");
                    if ($eres && $eres->num_rows > 0) $r = $eres->fetch_assoc();
                }
              ?>
                <form method="POST" action="why-choose.php" class="crud-form" id="whyForm">
                  <input type="hidden" name="id" value="<?= $r['id'] ?>" />

                  <!-- Title -->
                  <div class="form-group">
                    <label>Card Title *</label>
                    <input type="text" name="title" required maxlength="120"
                           value="<?= htmlspecialchars($r['title']) ?>"
                           placeholder="e.g. Authenticity and Warmth" />
                  </div>

                  <!-- Body -->
                  <div class="form-group">
                    <label>Description *</label>
                    <textarea name="body" required maxlength="1000"
                              placeholder="Describe why this makes Virunga Homestay special…"><?= htmlspecialchars($r['body']) ?></textarea>
                  </div>

                  <!-- Icon -->
                  <div class="form-group">
                    <label>Icon Class</label>
                    <input type="text" name="icon" id="iconInput" maxlength="100"
                           value="<?= htmlspecialchars($r['icon']) ?>"
                           placeholder="e.g. fa-solid fa-heart" />
                    <div class="icon-hint">
                      Use a <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" rel="noopener">Font Awesome 6</a> class, e.g. <code>fa-solid fa-shield-heart</code>
                    </div>
                    <div class="icon-live-preview" id="iconPreview">
                      <i class="<?= htmlspecialchars($r['icon']) ?>" id="iconPreviewI"></i>
                    </div>
                  </div>

                  <!-- Order & Status -->
                  <div class="form-row-2">
                    <div class="form-group">
                      <label>Display Order</label>
                      <input type="number" name="display_order" min="1" max="999"
                             value="<?= (int)$r['display_order'] ?>" />
                    </div>
                    <div class="form-group">
                      <label>Status</label>
                      <select name="status">
                        <option value="active"   <?= $r['status'] === 'active'   ? 'selected' : '' ?>>Active &amp; Visible</option>
                        <option value="inactive" <?= $r['status'] === 'inactive' ? 'selected' : '' ?>>Hidden / Draft</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group" style="margin-top:6px;">
                    <button type="submit" class="crud-btn">
                      <i class="fa-solid fa-floppy-disk"></i>
                      <?= $r['id'] ? 'Update Card' : 'Save Card' ?>
                    </button>
                  </div>
                </form>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </main>
    </div>

    <script src="js/index.js"></script>
    <script>
      // Live icon preview
      const iconInput    = document.getElementById('iconInput');
      const iconPreviewI = document.getElementById('iconPreviewI');
      if (iconInput && iconPreviewI) {
        iconInput.addEventListener('input', function () {
          iconPreviewI.className = this.value.trim() || 'fa-solid fa-star';
        });
      }

      // Auto-dismiss alert
      const alertEl = document.querySelector('.alert');
      if (alertEl) {
        setTimeout(() => {
          alertEl.style.transition = 'opacity 0.4s';
          alertEl.style.opacity = '0';
          setTimeout(() => alertEl.remove(), 400);
        }, 5000);
      }
    </script>
  </body>
</html>

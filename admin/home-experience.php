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
$msg     = $_GET['msg'] ?? '';
$msgType = 'success';

// Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT image FROM home_experience WHERE id = $id");
    if ($res && ($row = $res->fetch_assoc())) {
        $img = trim((string)($row['image'] ?? ''));
        if ($img !== '' && strpos($img, 'http') !== 0) {
            $img = preg_replace('/^(\.\/)?img\//', '', ltrim($img, '/'));
            $file = __DIR__ . '/../img/' . $img;
            if (is_file($file)) @unlink($file);
        }
    }
    $conn->query("DELETE FROM home_experience WHERE id = $id");
    header("Location: home-experience.php?msg=" . urlencode('Experience deleted successfully.'));
    exit;
}

// Toggle status
if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT status FROM home_experience WHERE id = $id");
    if ($res && ($row = $res->fetch_assoc())) {
        $ns = $row['status'] === 'active' ? 'inactive' : 'active';
        $conn->query("UPDATE home_experience SET status='$ns' WHERE id=$id");
    }
    header("Location: home-experience.php?msg=" . urlencode('Experience status updated.'));
    exit;
}

// Order
if ($action === 'order' && isset($_GET['id'], $_GET['dir'])) {
    $id = (int)$_GET['id'];
    $dir = $_GET['dir'] === 'up' ? 'up' : 'down';
    $res = $conn->query("SELECT display_order FROM home_experience WHERE id=$id");
    if ($res && ($row = $res->fetch_assoc())) {
        $curOrder = (int)$row['display_order'];
        if ($dir === 'up') {
            $adj = $conn->query("SELECT id, display_order FROM home_experience WHERE display_order < $curOrder ORDER BY display_order DESC LIMIT 1");
        } else {
            $adj = $conn->query("SELECT id, display_order FROM home_experience WHERE display_order > $curOrder ORDER BY display_order ASC LIMIT 1");
        }
        if ($adj && ($adjRow = $adj->fetch_assoc())) {
            $adjId = (int)$adjRow['id'];
            $adjOrder = (int)$adjRow['display_order'];
            $conn->query("UPDATE home_experience SET display_order=$adjOrder WHERE id=$id");
            $conn->query("UPDATE home_experience SET display_order=$curOrder WHERE id=$adjId");
        }
    }
    header("Location: home-experience.php?msg=" . urlencode('Display order updated.'));
    exit;
}

// Add/Edit submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $eyebrow = $conn->real_escape_string(trim($_POST['eyebrow'] ?? ''));
    $badge = $conn->real_escape_string(trim($_POST['badge'] ?? ''));
    $title = $conn->real_escape_string(trim($_POST['title'] ?? ''));
    $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
    $buttonText = $conn->real_escape_string(trim($_POST['button_text'] ?? 'Explore Experiences'));
    $buttonLink = $conn->real_escape_string(trim($_POST['button_link'] ?? 'activity.php'));
    $displayOrder = (int)($_POST['display_order'] ?? 1);
    $status = $conn->real_escape_string(trim($_POST['status'] ?? 'active'));
    $image = $conn->real_escape_string(trim($_POST['existing_image'] ?? ''));

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['image']['tmp_name'];
        $orig = basename($_FILES['image']['name']);
        $safe = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', $orig);
        $uploadDir = __DIR__ . '/../img/experiences/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        if (move_uploaded_file($tmp, $uploadDir . $safe)) {
            if ($id > 0 && !empty($_POST['existing_image'])) {
                $old = trim((string)$_POST['existing_image']);
                $oldRel = preg_replace('/^(\.\/)?img\//', '', ltrim($old, '/'));
                $oldFile = __DIR__ . '/../img/' . $oldRel;
                if (is_file($oldFile)) @unlink($oldFile);
            }
            $image = $conn->real_escape_string('experiences/' . $safe);
        }
    }

    if ($title === '' || $description === '') {
        $msg = 'Title and description are required.';
        $msgType = 'error';
        $action = $id > 0 ? 'edit' : 'add';
    } else {
        if ($id > 0) {
            $sql = "UPDATE home_experience SET
                eyebrow='$eyebrow',
                badge='$badge',
                title='$title',
                description='$description',
                image='$image',
                button_text='$buttonText',
                button_link='$buttonLink',
                display_order=$displayOrder,
                status='$status'
                WHERE id=$id";
            $conn->query($sql);
            $msg = 'Experience updated successfully.';
        } else {
            $sql = "INSERT INTO home_experience
                (eyebrow, badge, title, description, image, button_text, button_link, display_order, status)
                VALUES
                ('$eyebrow', '$badge', '$title', '$description', '$image', '$buttonText', '$buttonLink', $displayOrder, '$status')";
            $conn->query($sql);
            $msg = 'Experience added successfully.';
        }
        $action = 'list';
    }
}

$pageTitle = 'Home Experiences - Virunga Homestay CMS';
$currentPage = 'home-experience';
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
      .crud-form {
        display: flex; flex-direction: column; gap: 18px;
        max-width: 760px;
        background: var(--surface-1);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 30px;
      }
      .crud-form .form-group { display: flex; flex-direction: column; gap: 6px; }
      .crud-form label {
        font-size: 11px; letter-spacing: .06em; text-transform: uppercase;
        font-weight: 700; color: var(--text-2);
      }
      .crud-form input, .crud-form textarea, .crud-form select {
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text-1);
        padding: 11px 13px;
        font-size: 14px;
        font-family: inherit;
      }
      .crud-form textarea { min-height: 120px; resize: vertical; }
      .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
      .crud-btn {
        display: inline-flex; align-items: center; gap: 8px;
        border: none; border-radius: 8px; cursor: pointer;
        padding: 12px 18px; font-weight: 700;
        background: var(--amber); color: #000;
      }
      .alert {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 16px; border-radius: 8px; padding: 12px 16px; color: #fff;
      }
      .alert--success { background: var(--success); }
      .alert--error { background: var(--danger); }
      .exp-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
      }
      .exp-card {
        background: var(--surface-1);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
      }
      .exp-card__img {
        height: 170px;
        width: 100%;
        object-fit: cover;
        background: var(--surface-2);
      }
      .exp-card__body { padding: 16px; display: grid; gap: 8px; }
      .exp-card__title {
        color: var(--text-1);
        font-size: 16px;
        font-weight: 700;
        margin: 0;
      }
      .exp-card__desc {
        color: var(--text-2);
        font-size: 13px;
        line-height: 1.55;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
      }
      .exp-card__meta {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
      }
      .exp-chip {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 999px;
        background: var(--surface-3);
        color: var(--text-2);
      }
      .exp-actions {
        display: flex;
        gap: 8px;
        padding: 12px 16px 16px;
        align-items: center;
      }
      .exp-actions a {
        text-decoration: none;
      }
      .btn-mini {
        font-size: 12px;
        padding: 6px 10px;
        border-radius: 7px;
        background: var(--surface-3);
        color: var(--text-1);
      }
      .btn-mini--danger { color: var(--danger); }
      .spacer { margin-left: auto; }
      @media (max-width: 640px) {
        .form-row-2 { grid-template-columns: 1fr; }
      }
    </style>
  </head>
  <body>
    <?php include 'includes/header.php'; ?>
    <div class="dash-layout" id="dashLayout">
      <?php include 'includes/sidebar.php'; ?>
      <main class="dash-main">
        <div class="page-header">
          <div>
            <h1 class="page-header__title"><?= ($action === 'add' || $action === 'edit') ? 'Experience Editor' : 'Home Experiences' ?></h1>
            <p class="page-header__sub">
              <?= ($action === 'list') ? 'Add, edit, delete, reorder, and toggle experiences shown on the home page.' : 'Configure experience details and publishing settings.' ?>
            </p>
          </div>
          <div class="page-header__actions">
            <?php if ($action === 'list'): ?>
              <a href="home-experience.php?action=add" class="btn btn-amber"><i class="fa-solid fa-plus"></i> Add Experience</a>
            <?php else: ?>
              <a href="home-experience.php" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
            <?php endif; ?>
            <a href="../home" target="_blank" class="btn btn-ghost"><i class="fa-solid fa-eye"></i> View Live</a>
          </div>
        </div>

        <div class="dash-grid gap-section" style="grid-template-columns:1fr;">
          <div class="panel">
            <div class="panel__body">
              <?php if (!empty($msg)): ?>
                <div class="alert alert--<?= $msgType === 'error' ? 'error' : 'success' ?>">
                  <i class="fa-solid <?= $msgType === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check' ?>"></i>
                  <?= htmlspecialchars($msg) ?>
                </div>
              <?php endif; ?>

              <?php if ($action === 'list'): ?>
                <?php
                  $items = [];
                  $res = $conn->query("SELECT * FROM home_experience ORDER BY display_order ASC, id ASC");
                  if ($res) while ($row = $res->fetch_assoc()) $items[] = $row;
                ?>

                <?php if (empty($items)): ?>
                  <div style="text-align:center; padding:50px 16px; color:var(--text-3);">
                    <i class="fa-solid fa-mountain-sun" style="font-size:36px; margin-bottom:10px; display:block; opacity:.45;"></i>
                    No experiences yet. Click <strong>Add Experience</strong> to begin.
                  </div>
                <?php else: ?>
                  <div class="exp-grid">
                    <?php foreach ($items as $idx => $item): ?>
                      <?php
                        $img = trim((string)($item['image'] ?? ''));
                        if ($img === '') {
                            $imgSrc = '../img/hero/2.jpg';
                        } elseif (strpos($img, 'http') === 0) {
                            $imgSrc = $img;
                        } else {
                            $imgSrc = '../img/' . ltrim(preg_replace('/^(\.\/)?img\//', '', $img), '/');
                        }
                        $isFirst = $idx === 0;
                        $isLast = $idx === count($items) - 1;
                      ?>
                      <article class="exp-card">
                        <img class="exp-card__img" src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                        <div class="exp-card__body">
                          <h3 class="exp-card__title"><?= htmlspecialchars($item['title']) ?></h3>
                          <p class="exp-card__desc"><?= htmlspecialchars($item['description']) ?></p>
                          <div class="exp-card__meta">
                            <span class="exp-chip">Order #<?= (int)$item['display_order'] ?></span>
                            <span class="exp-chip"><?= htmlspecialchars($item['button_text']) ?></span>
                            <span class="exp-chip"><?= htmlspecialchars($item['status']) ?></span>
                          </div>
                        </div>
                        <div class="exp-actions">
                          <?php if (!$isFirst): ?>
                            <a class="btn-mini" href="home-experience.php?action=order&id=<?= (int)$item['id'] ?>&dir=up" title="Move up"><i class="fa-solid fa-arrow-up"></i></a>
                          <?php endif; ?>
                          <?php if (!$isLast): ?>
                            <a class="btn-mini" href="home-experience.php?action=order&id=<?= (int)$item['id'] ?>&dir=down" title="Move down"><i class="fa-solid fa-arrow-down"></i></a>
                          <?php endif; ?>
                          <span class="spacer"></span>
                          <a class="btn-mini" href="home-experience.php?action=toggle&id=<?= (int)$item['id'] ?>">
                            <?= $item['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                          </a>
                          <a class="btn-mini" href="home-experience.php?action=edit&id=<?= (int)$item['id'] ?>"><i class="fa-solid fa-pen"></i> Edit</a>
                          <a class="btn-mini btn-mini--danger" href="home-experience.php?action=delete&id=<?= (int)$item['id'] ?>" onclick="return confirm('Delete this experience permanently?')"><i class="fa-solid fa-trash"></i></a>
                        </div>
                      </article>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

              <?php else: ?>
                <?php
                  $r = [
                    'id' => 0,
                    'eyebrow' => '',
                    'badge' => '',
                    'title' => '',
                    'description' => '',
                    'image' => '',
                    'button_text' => 'Explore Experiences',
                    'button_link' => 'activity.php',
                    'display_order' => 1,
                    'status' => 'active'
                  ];
                  if ($action === 'edit' && isset($_GET['id'])) {
                      $eid = (int)$_GET['id'];
                      $res = $conn->query("SELECT * FROM home_experience WHERE id=$eid");
                      if ($res && $res->num_rows) $r = $res->fetch_assoc();
                  }
                ?>
                <form method="POST" action="home-experience.php" class="crud-form" enctype="multipart/form-data">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <input type="hidden" name="existing_image" value="<?= htmlspecialchars($r['image']) ?>">

                  <div class="form-group">
                    <label>Eyebrow</label>
                    <input type="text" name="eyebrow" maxlength="255" value="<?= htmlspecialchars($r['eyebrow']) ?>" placeholder="Short intro line">
                  </div>

                  <div class="form-row-2">
                    <div class="form-group">
                      <label>Title *</label>
                      <input type="text" name="title" maxlength="255" required value="<?= htmlspecialchars($r['title']) ?>" placeholder="Experience title">
                    </div>
                    <div class="form-group">
                      <label>Badge (optional)</label>
                      <input type="text" name="badge" maxlength="20" value="<?= htmlspecialchars($r['badge']) ?>" placeholder="e.g. 4">
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" maxlength="1000" required placeholder="Description shown on home page"><?= htmlspecialchars($r['description']) ?></textarea>
                  </div>

                  <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*">
                    <?php if (!empty($r['image'])): ?>
                      <small style="color:var(--text-3)">Current: <?= htmlspecialchars($r['image']) ?></small>
                    <?php endif; ?>
                  </div>

                  <div class="form-row-2">
                    <div class="form-group">
                      <label>Button Text</label>
                      <input type="text" name="button_text" maxlength="120" value="<?= htmlspecialchars($r['button_text']) ?>">
                    </div>
                    <div class="form-group">
                      <label>Button Link</label>
                      <input type="text" name="button_link" maxlength="255" value="<?= htmlspecialchars($r['button_link']) ?>">
                    </div>
                  </div>

                  <div class="form-row-2">
                    <div class="form-group">
                      <label>Display Order</label>
                      <input type="number" name="display_order" min="1" max="999" value="<?= (int)$r['display_order'] ?>">
                    </div>
                    <div class="form-group">
                      <label>Status</label>
                      <select name="status">
                        <option value="active" <?= $r['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $r['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <button class="crud-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= (int)$r['id'] > 0 ? 'Update Experience' : 'Save Experience' ?></button>
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


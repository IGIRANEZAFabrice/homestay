<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? 'list';
$msg    = $_GET['msg']    ?? '';
$msgType = 'success';

// ── DELETE ──────────────────────────────────────────────────────────────────
if ($action === 'delete' && isset($_GET['id'])) {
    $id  = (int)$_GET['id'];
    $res = $conn->query("SELECT image FROM hero_images WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        $file = __DIR__ . '/../img/hero/' . $row['image'];
        if (is_file($file)) @unlink($file);
    }
    $conn->query("DELETE FROM hero_images WHERE id = $id");
    header("Location: hero-images.php?msg=" . urlencode('Hero image deleted successfully.'));
    exit;
}

// ── TOGGLE STATUS ────────────────────────────────────────────────────────────
if ($action === 'toggle' && isset($_GET['id'])) {
    $id  = (int)$_GET['id'];
    $res = $conn->query("SELECT status FROM hero_images WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        $ns = $row['status'] === 'active' ? 'inactive' : 'active';
        $conn->query("UPDATE hero_images SET status='$ns' WHERE id=$id");
    }
    header("Location: hero-images.php?msg=" . urlencode('Status updated successfully.'));
    exit;
}

// ── REORDER (AJAX-friendly via GET for simplicity) ───────────────────────────
if ($action === 'order' && isset($_GET['id'], $_GET['dir'])) {
    $id  = (int)$_GET['id'];
    $dir = $_GET['dir'] === 'up' ? 'up' : 'down';
    $res = $conn->query("SELECT display_order FROM hero_images WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        $curOrder = (int)$row['display_order'];
        if ($dir === 'up') {
            // Find adjacent item above
            $adj = $conn->query("SELECT id, display_order FROM hero_images WHERE display_order < $curOrder ORDER BY display_order DESC LIMIT 1");
        } else {
            $adj = $conn->query("SELECT id, display_order FROM hero_images WHERE display_order > $curOrder ORDER BY display_order ASC LIMIT 1");
        }
        if ($adjRow = $adj->fetch_assoc()) {
            $adjId    = (int)$adjRow['id'];
            $adjOrder = (int)$adjRow['display_order'];
            $conn->query("UPDATE hero_images SET display_order=$adjOrder WHERE id=$id");
            $conn->query("UPDATE hero_images SET display_order=$curOrder WHERE id=$adjId");
        }
    }
    header("Location: hero-images.php?msg=" . urlencode('Order updated.'));
    exit;
}

// ── FORM SUBMIT (ADD / EDIT) ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = (int)($_POST['id'] ?? 0);
    $title         = $conn->real_escape_string(trim($_POST['title']   ?? ''));
    $paragraph     = $conn->real_escape_string(trim($_POST['paragraph'] ?? ''));
    $display_order = (int)($_POST['display_order'] ?? 1);
    $is_active     = isset($_POST['is_active']) ? 1 : 0;
    $status        = $conn->real_escape_string($_POST['status'] ?? 'active');
    $image         = $conn->real_escape_string($_POST['existing_image'] ?? '');

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmpName  = $_FILES['image']['tmp_name'];
        $origName = basename($_FILES['image']['name']);
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', $origName);
        $uploadDir = __DIR__ . '/../img/hero/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        if (move_uploaded_file($tmpName, $uploadDir . $safeName)) {
            // Delete old file on edit
            if ($id > 0 && !empty($_POST['existing_image'])) {
                $old = $uploadDir . $_POST['existing_image'];
                if (is_file($old)) @unlink($old);
            }
            $image = $conn->real_escape_string($safeName);
        }
    }

    if (empty($image) && $id === 0) {
        $msg     = 'An image file is required for a new hero slide.';
        $msgType = 'error';
        $action  = 'add';
    } else {
        if ($id > 0) {
            $conn->query("UPDATE hero_images SET title='$title', paragraph='$paragraph', image='$image', display_order=$display_order, is_active=$is_active, status='$status' WHERE id=$id");
            $msg = 'Hero image updated successfully.';
        } else {
            $conn->query("INSERT INTO hero_images (title, paragraph, image, display_order, is_active, status) VALUES ('$title', '$paragraph', '$image', $display_order, $is_active, '$status')");
            $msg = 'Hero image added successfully.';
        }
        $action = 'list';
    }
}

$pageTitle   = 'Hero Images — Virunga Homestay CMS';
$currentPage = 'hero-images';
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

    <!-- Core Admin CSS -->
    <link rel="stylesheet" href="css/header.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/dashboard.css" />

    <style>
      /* ── Form Styles ── */
      .crud-form {
        display: flex; flex-direction: column; gap: 22px;
        max-width: 700px;
        background: var(--surface-1);
        padding: 32px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
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
        padding: 12px 14px;
        border: 1px solid var(--border);
        background: var(--surface-2);
        color: var(--text-1);
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
      }
      .crud-form input:focus,
      .crud-form textarea:focus,
      .crud-form select:focus {
        border-color: var(--amber);
        outline: none;
        box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
      }
      .crud-form textarea { resize: vertical; min-height: 100px; line-height: 1.6; }
      .crud-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 13px 24px;
        border: none;
        background: var(--amber);
        color: #000;
        border-radius: 9px;
        cursor: pointer;
        font-weight: 700;
        font-size: 14px;
        transition: filter 0.2s, transform 0.1s;
      }
      .crud-btn:hover { filter: brightness(0.92); transform: translateY(-1px); }
      .crud-btn:active { transform: translateY(0); }

      /* ── Toggle switch ── */
      .toggle-wrap { display: flex; align-items: center; gap: 10px; }
      .toggle-label { font-size: 14px; color: var(--text-2); }
      .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
      .toggle-switch input { opacity: 0; width: 0; height: 0; }
      .toggle-slider {
        position: absolute; inset: 0;
        background: var(--surface-3);
        border-radius: 24px;
        cursor: pointer;
        transition: background 0.25s;
      }
      .toggle-slider::before {
        content: '';
        position: absolute;
        width: 18px; height: 18px;
        left: 3px; bottom: 3px;
        background: #fff;
        border-radius: 50%;
        transition: transform 0.25s;
      }
      .toggle-switch input:checked + .toggle-slider { background: var(--amber); }
      .toggle-switch input:checked + .toggle-slider::before { transform: translateX(20px); }

      /* ── Alert Banner ── */
      .alert {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 20px;
        border-radius: 9px;
        margin-bottom: 22px;
        font-size: 14px; font-weight: 500;
        animation: slideIn 0.3s ease;
      }
      @keyframes slideIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
      .alert--success { background: var(--success); color: #fff; }
      .alert--error   { background: var(--danger);  color: #fff; }

      /* ── Hero Cards Grid ── */
      .hero-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
      }
      .hero-card {
        background: var(--surface-1);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        transition: box-shadow 0.25s, transform 0.2s;
        position: relative;
      }
      .hero-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
      .hero-card__thumb {
        width: 100%; height: 180px;
        object-fit: cover;
        display: block;
        background: var(--surface-2);
      }
      .hero-card__thumb-placeholder {
        width: 100%; height: 180px;
        display: flex; align-items: center; justify-content: center;
        background: var(--surface-2);
        color: var(--text-3);
        font-size: 36px;
      }
      .hero-card__body {
        padding: 18px 20px;
        display: flex; flex-direction: column; gap: 8px;
      }
      .hero-card__order-badge {
        position: absolute; top: 12px; left: 12px;
        background: rgba(0,0,0,0.65);
        backdrop-filter: blur(6px);
        color: #fff;
        font-size: 11px; font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: 0.5px;
      }
      .hero-card__status {
        position: absolute; top: 12px; right: 12px;
      }
      .hero-card__title {
        font-size: 16px; font-weight: 600; color: var(--text-1);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
      }
      .hero-card__paragraph {
        font-size: 13px; color: var(--text-2); line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
      }
      .hero-card__actions {
        display: flex; align-items: center; gap: 8px;
        padding: 14px 20px;
        border-top: 1px solid var(--surface-3);
        background: var(--surface-2);
      }
      .hero-card__actions a,
      .hero-card__actions button {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 12px; font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: background 0.18s, color 0.18s;
      }
      .btn-edit  { background: var(--surface-3); color: var(--text-1); }
      .btn-edit:hover  { background: var(--amber); color: #000; }
      .btn-del   { background: transparent; color: var(--danger); }
      .btn-del:hover   { background: rgba(220,38,38,.12); }
      .btn-order { background: var(--surface-3); color: var(--text-2); padding: 6px 10px; }
      .btn-order:hover { background: var(--surface-1); color: var(--text-1); }
      .ms-auto { margin-left: auto; }

      /* ── Empty state ── */
      .empty-state {
        text-align: center; padding: 60px 20px; color: var(--text-3);
      }
      .empty-state i { font-size: 48px; margin-bottom: 14px; opacity: 0.4; display: block; }
      .empty-state p { font-size: 15px; }

      /* ── Image Preview ── */
      .img-preview-wrap { position: relative; }
      .img-preview {
        width: 100%; max-height: 220px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid var(--border);
        margin-top: 8px;
        display: none;
      }
      .img-preview.visible { display: block; }
      .img-current-label {
        font-size: 12px; color: var(--text-3); margin-top: 6px; display: block;
      }
      code { font-size: 12px; background: var(--surface-3); padding: 2px 6px; border-radius: 4px; }
    </style>
  </head>
  <body>
    <?php include 'includes/header.php'; ?>

    <div class="dash-layout" id="dashLayout">
      <?php include 'includes/sidebar.php'; ?>

      <main class="dash-main">
        <!-- Page Header -->
        <div class="page-header">
          <div>
            <h1 class="page-header__title">
              <?php echo ($action === 'add' || $action === 'edit') ? 'Hero Slide Editor' : 'Hero Images'; ?>
            </h1>
            <p class="page-header__sub">
              <?php echo ($action === 'list')
                ? 'Manage the full-screen hero slideshow displayed on the homepage.'
                : 'Fill in the details below to configure this hero slide.'; ?>
            </p>
          </div>
          <div class="page-header__actions">
            <?php if ($action === 'list'): ?>
              <a href="hero-images.php?action=add" class="btn btn-amber">
                <i class="fa-solid fa-plus"></i> Add Hero Slide
              </a>
            <?php else: ?>
              <a href="hero-images.php" class="btn btn-ghost">
                <i class="fa-solid fa-arrow-left"></i> Back to Slides
              </a>
            <?php endif; ?>
          </div>
        </div>

        <div class="dash-grid gap-section" style="grid-template-columns: 1fr;">
          <div class="panel">
            <div class="panel__body">

              <!-- Alert Banner -->
              <?php if (!empty($msg)): ?>
                <div class="alert alert--<?= $msgType === 'error' ? 'error' : 'success' ?>">
                  <i class="fa-solid <?= $msgType === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check' ?>"></i>
                  <?= htmlspecialchars($msg) ?>
                </div>
              <?php endif; ?>

              <!-- ══ LIST VIEW ══════════════════════════════════════════ -->
              <?php if ($action === 'list'): ?>
                <?php
                  $res = $conn->query("SELECT * FROM hero_images ORDER BY display_order ASC, id ASC");
                  $slides = [];
                  if ($res && $res->num_rows > 0) {
                      while ($row = $res->fetch_assoc()) $slides[] = $row;
                  }
                ?>

                <?php if (empty($slides)): ?>
                  <div class="empty-state">
                    <i class="fa-regular fa-image"></i>
                    <p>No hero slides yet. Click <strong>Add Hero Slide</strong> to get started.</p>
                  </div>
                <?php else: ?>
                  <div class="hero-grid">
                    <?php foreach ($slides as $idx => $slide):
                      $imgFile = $slide['image'];
                      $imgSrc  = '../img/hero/' . $imgFile;
                      $isFirst = ($idx === 0);
                      $isLast  = ($idx === count($slides) - 1);
                    ?>
                    <div class="hero-card">
                      <!-- Order badge -->
                      <span class="hero-card__order-badge">#<?= $slide['display_order'] ?></span>

                      <!-- Status pill -->
                      <span class="hero-card__status">
                        <a href="hero-images.php?action=toggle&id=<?= $slide['id'] ?>" style="text-decoration:none;" title="Click to toggle status">
                          <span class="status-pill <?= $slide['status'] === 'active' ? 'status-pill--live' : '' ?>"
                                style="<?= $slide['status'] === 'inactive' ? 'background:rgba(0,0,0,.5); color:#ccc;' : '' ?>">
                            <i class="fa-solid fa-circle" style="font-size:6px;"></i> <?= ucfirst($slide['status']) ?>
                          </span>
                        </a>
                      </span>

                      <!-- Thumbnail -->
                      <?php if (!empty($imgFile) && file_exists(__DIR__ . '/../img/hero/' . $imgFile)): ?>
                        <img src="<?= htmlspecialchars($imgSrc) ?>" class="hero-card__thumb" alt="<?= htmlspecialchars($slide['title']) ?>" loading="lazy" />
                      <?php else: ?>
                        <div class="hero-card__thumb-placeholder">
                          <i class="fa-regular fa-image"></i>
                        </div>
                      <?php endif; ?>

                      <!-- Body -->
                      <div class="hero-card__body">
                        <div class="hero-card__title"><?= htmlspecialchars($slide['title']) ?></div>
                        <div class="hero-card__paragraph"><?= htmlspecialchars($slide['paragraph']) ?></div>
                        <small style="color:var(--text-3); font-size:11px;">
                          <i class="fa-regular fa-clock"></i>
                          Updated <?= date('M j, Y', strtotime($slide['updated_at'])) ?>
                        </small>
                      </div>

                      <!-- Actions -->
                      <div class="hero-card__actions">
                        <!-- Order controls -->
                        <?php if (!$isFirst): ?>
                          <a href="hero-images.php?action=order&id=<?= $slide['id'] ?>&dir=up" class="btn-order" title="Move up">
                            <i class="fa-solid fa-arrow-up"></i>
                          </a>
                        <?php endif; ?>
                        <?php if (!$isLast): ?>
                          <a href="hero-images.php?action=order&id=<?= $slide['id'] ?>&dir=down" class="btn-order" title="Move down">
                            <i class="fa-solid fa-arrow-down"></i>
                          </a>
                        <?php endif; ?>

                        <span class="ms-auto"></span>

                        <!-- Edit -->
                        <a href="hero-images.php?action=edit&id=<?= $slide['id'] ?>" class="btn-edit">
                          <i class="fa-solid fa-pen"></i> Edit
                        </a>

                        <!-- Delete -->
                        <a href="hero-images.php?action=delete&id=<?= $slide['id'] ?>"
                           class="btn-del"
                           onclick="return confirm('Delete this hero slide permanently? The image file will also be removed.')">
                          <i class="fa-solid fa-trash"></i> Delete
                        </a>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div><!-- /.hero-grid -->
                <?php endif; ?>

              <!-- ══ ADD / EDIT FORM ════════════════════════════════════ -->
              <?php elseif ($action === 'add' || $action === 'edit'):
                $r = [
                  'id'            => 0,
                  'title'         => '',
                  'paragraph'     => '',
                  'image'         => '',
                  'display_order' => 1,
                  'is_active'     => 1,
                  'status'        => 'active',
                ];
                if ($action === 'edit' && isset($_GET['id'])) {
                    $eid = (int)$_GET['id'];
                    $eres = $conn->query("SELECT * FROM hero_images WHERE id = $eid");
                    if ($eres && $eres->num_rows > 0) $r = $eres->fetch_assoc();
                }
              ?>
                <form method="POST" action="hero-images.php" class="crud-form" enctype="multipart/form-data" id="heroForm">
                  <input type="hidden" name="id" value="<?= $r['id'] ?>" />
                  <input type="hidden" name="existing_image" value="<?= htmlspecialchars($r['image']) ?>" />

                  <!-- Title -->
                  <div class="form-group">
                    <label>Slide Title *</label>
                    <input type="text" name="title" required maxlength="255"
                           value="<?= htmlspecialchars($r['title']) ?>"
                           placeholder="e.g. Welcome to Virunga Homestay" />
                  </div>

                  <!-- Paragraph -->
                  <div class="form-group">
                    <label>Slide Description / Paragraph *</label>
                    <textarea name="paragraph" required maxlength="1000"
                              placeholder="e.g. Experience the magic of Rwanda nestled among the volcanic mountains..."><?= htmlspecialchars($r['paragraph']) ?></textarea>
                  </div>

                  <!-- Image Upload -->
                  <div class="form-group">
                    <label>Hero Image <?= empty($r['image']) ? '*' : '' ?></label>
                    <input type="file" name="image" id="imageInput" accept="image/*"
                           <?= empty($r['image']) ? 'required' : '' ?> />

                    <?php if (!empty($r['image'])): ?>
                      <span class="img-current-label">
                        Current: <code><?= htmlspecialchars($r['image']) ?></code>
                        <span style="color:var(--text-3);"> — upload a new file to replace it</span>
                      </span>
                      <img src="../img/hero/<?= htmlspecialchars($r['image']) ?>"
                           class="img-preview visible" id="imgPreview" alt="Current hero image" />
                    <?php else: ?>
                      <img src="" class="img-preview" id="imgPreview" alt="Preview" />
                    <?php endif; ?>
                  </div>

                  <!-- Display Order & Status row -->
                  <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
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

                  <!-- Is Active toggle -->
                  <div class="form-group">
                    <label>Show on Slideshow</label>
                    <div class="toggle-wrap">
                      <label class="toggle-switch">
                        <input type="checkbox" name="is_active" id="isActiveToggle"
                               <?= $r['is_active'] ? 'checked' : '' ?> />
                        <span class="toggle-slider"></span>
                      </label>
                      <span class="toggle-label" id="isActiveLabel">
                        <?= $r['is_active'] ? 'Yes — included in slideshow' : 'No — excluded from slideshow' ?>
                      </span>
                    </div>
                  </div>

                  <!-- Submit -->
                  <div class="form-group" style="margin-top:6px;">
                    <button type="submit" class="crud-btn" id="submitBtn">
                      <i class="fa-solid fa-floppy-disk"></i>
                      <?= $r['id'] ? 'Update Hero Slide' : 'Save Hero Slide' ?>
                    </button>
                  </div>
                </form>
              <?php endif; ?>

            </div><!-- /.panel__body -->
          </div><!-- /.panel -->
        </div>
      </main>
    </div>

    <script src="js/index.js"></script>
    <script>
      // ── Live image preview on file select ─────────────────────────────
      const imgInput   = document.getElementById('imageInput');
      const imgPreview = document.getElementById('imgPreview');
      if (imgInput && imgPreview) {
        imgInput.addEventListener('change', function () {
          const file = this.files[0];
          if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
              imgPreview.src = e.target.result;
              imgPreview.classList.add('visible');
            };
            reader.readAsDataURL(file);
          }
        });
      }

      // ── Toggle label text ─────────────────────────────────────────────
      const toggle = document.getElementById('isActiveToggle');
      const label  = document.getElementById('isActiveLabel');
      if (toggle && label) {
        toggle.addEventListener('change', function () {
          label.textContent = this.checked
            ? 'Yes — included in slideshow'
            : 'No — excluded from slideshow';
        });
      }

      // ── Auto-dismiss alert after 5s ───────────────────────────────────
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

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';

// Handle Actions (Delete/Toggle)
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM activities WHERE id = $id");
    header("Location: activities.php?msg=" . urlencode('Activity deleted successfully.'));
    exit;
}
if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT status FROM activities WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if ($row['status'] === 'active') $ns = 'inactive';
        elseif ($row['status'] === 'inactive') $ns = 'active';
        else $ns = 'active';
        $conn->query("UPDATE activities SET status = '$ns', is_active = IF('$ns'='active', 1, 0) WHERE id = $id");
    }
    header("Location: activities.php?msg=" . urlencode('Status toggled successfully.'));
    exit;
}

// Handle Form Submissions (Add / Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = $conn->real_escape_string($_POST['title']);
    $tag = $conn->real_escape_string($_POST['tag']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $age_group = $conn->real_escape_string($_POST['age_group']);
    $group_size = $conn->real_escape_string($_POST['group_size']);
    $characteristics = $conn->real_escape_string($_POST['characteristics']);
    $price = $conn->real_escape_string($_POST['price']);
    $display_order = (int)($_POST['display_order'] ?? 1);
    $status = $conn->real_escape_string($_POST['status']);
    $is_active = $status === 'active' ? 1 : 0;

    $short_desc = $conn->real_escape_string($_POST['short_description']);
    // long_description now contains HTML from block builder
    $long_desc = $conn->real_escape_string($_POST['long_description']);

    // Image handling
    $image = $conn->real_escape_string($_POST['existing_image'] ?? '');
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        $name = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($_FILES['image']['name']));
        $uploadDir = __DIR__ . '/../img/activities/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        if (move_uploaded_file($tmpName, $uploadDir . $name)) {
            $image = $conn->real_escape_string($name);
        }
    }

    if ($id > 0) {
        $conn->query("UPDATE activities SET 
                      title='$title', tag='$tag', short_description='$short_desc', 
                      long_description='$long_desc', duration='$duration', age_group='$age_group', 
                      group_size='$group_size', characteristics='$characteristics', price='$price', 
                      image='$image', display_order=$display_order, is_active=$is_active, status='$status' 
                      WHERE id=$id");
        $msg = "Activity updated successfully";
    } else {
        $conn->query("INSERT INTO activities 
                      (title, tag, short_description, long_description, duration, age_group, group_size, characteristics, price, image, display_order, is_active, status) 
                      VALUES 
                      ('$title', '$tag', '$short_desc', '$long_desc', '$duration', '$age_group', '$group_size', '$characteristics', '$price', '$image', $display_order, $is_active, '$status')");
        $msg = "Activity added successfully";
    }
    $action = 'list';
}

$pageTitle = 'Activities Management — Virunga Homestay CMS';
$currentPage = 'activities';
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
      .crud-form { display:flex; flex-direction:column; gap:20px; max-width:900px; background:var(--surface-1); padding:30px; border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-sm); }
      .crud-form .form-group { display:flex; flex-direction:column; gap:6px; }
      .crud-form label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); }
      .crud-form input, .crud-form select, .crud-form textarea { padding: 12px; border: 1px solid var(--border); background:var(--surface-2); color:var(--text-1); border-radius:8px; font-family: inherit; font-size:14px; transition:border-color 0.2s;}
      .crud-form input:focus, .crud-form select:focus, .crud-form textarea:focus { border-color:var(--amber); outline:none; }
      .crud-btn { padding: 12px 20px; border:none; background:var(--amber); color:#000; border-radius:8px; cursor:pointer; font-weight:600; font-size:14px; display:inline-flex; align-items:center; gap:8px;}
      .crud-btn:hover { filter:brightness(0.95); }
      .alert { padding:14px 20px; background:var(--success); color:#fff; border-radius:8px; margin-bottom:20px; font-size:14px; font-weight:500; display:flex; align-items:center; gap:10px; }
      .flex-row-gap { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
      @media(max-width:768px) { .flex-row-gap { grid-template-columns:1fr; } }
      /* Block builder styles */
      .b-block { background: var(--surface-2); border: 1px solid var(--border); padding: 20px; border-radius: 8px; position:relative;}
      .b-header { display: flex; justify-content: space-between; margin-bottom: 15px; font-weight: bold; color: var(--amber); }
      .b-actions { display: flex; gap: 10px; }
      .b-action-btn { background: none; border: none; color: var(--text-3); cursor: pointer; font-size:16px;}
      .b-action-btn:hover { color: var(--danger); }
      .b-row { margin-bottom: 10px; }
      .b-row input, .b-row textarea { width: 100%; padding: 12px; border: 1px solid var(--surface-3); border-radius: 6px; background: var(--surface-1); color: var(--text-1); font-family: inherit; font-size:14.5px; line-height:1.5; box-sizing:border-box;}
      .b-row textarea { min-height: 140px; resize: vertical; }
      .b-row input:focus, .b-row textarea:focus { border-color:var(--amber); outline:none; }
      .b-btn { padding: 10px 17px; border-radius: 6px; border: 1px dashed var(--border); background: var(--surface-3); color: var(--text-1); cursor: pointer; font-size: 13px; font-weight:600; font-family:inherit;}
      .b-btn:hover { background: var(--surface-2); border-color:var(--amber);}
    </style>
  </head>
  <body>
    <?php include 'includes/header.php'; ?>
    <div class="dash-layout" id="dashLayout">
      <?php include 'includes/sidebar.php'; ?>
      <main class="dash-main">
        <div class="page-header">
          <div>
            <h1 class="page-header__title"><?php echo ($action==='add' || $action==='edit') ? 'Activity Editor' : 'Activities Management'; ?></h1>
            <p class="page-header__sub">Add, edit, or manage availability for all homestay activities and experiences.</p>
          </div>
          <div class="page-header__actions">
            <?php if ($action === 'list'): ?>
              <a href="activities.php?action=add" class="btn btn-amber"><i class="fa-solid fa-plus"></i> Add New Activity</a>
            <?php else: ?>
              <a href="activities.php" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Back to Activities</a>
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
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Activity Focus</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Overview details</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Price / Status</th>
                        <th style="padding:15px; text-align:right;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $res = $conn->query("SELECT * FROM activities ORDER BY display_order ASC, id DESC");
                      if ($res && $res->num_rows > 0):
                          while($row = $res->fetch_assoc()):
                              $img = $row['image'];
                              if (strpos($img, '/') === false && strpos($img, 'http') !== 0) $img = '../img/activities/'.$img;
                              elseif (strpos($img, './') === 0) $img = '../'.ltrim($img, './');
                      ?>
                      <tr style="border-bottom:1px solid var(--surface-3);">
                        <td style="padding:15px; display:flex; align-items:center; gap:16px;">
                          <img src="<?= htmlspecialchars($img) ?>" style="width:80px; height:56px; object-fit:cover; border-radius:8px; border:1px solid var(--border);" alt="<?= htmlspecialchars($row['title']) ?>"/>
                          <div>
                              <strong style="color:var(--text-1); font-size:15px; display:block; margin-bottom:4px;"><?= htmlspecialchars($row['title']) ?></strong>
                              <span style="font-size:12px; color:var(--text-3);"><i class="fa-solid fa-hashtag"></i> <?= htmlspecialchars($row['tag']) ?> &nbsp;•&nbsp; <i class="fa-solid fa-sort"></i> Order: <?= $row['display_order'] ?></span>
                          </div>
                        </td>
                        <td style="padding:15px; font-size:13px; color:var(--text-2);">
                          <div style="margin-bottom:4px;"><strong>Duration:</strong> <?= $row['duration'] ? htmlspecialchars($row['duration']) : '-' ?></div>
                          <div style="margin-bottom:4px;"><strong>Group:</strong> <?= $row['group_size'] ? htmlspecialchars($row['group_size']) : '-' ?></div>
                          <div><strong>Ages:</strong> <?= $row['age_group'] ? htmlspecialchars($row['age_group']) : '-' ?></div>
                        </td>
                        <td style="padding:15px;">
                          <div style="font-weight:600; color:var(--amber); margin-bottom:8px; font-size:13.5px;"><?= $row['price'] ? htmlspecialchars($row['price']) : 'TBD' ?></div>
                          <a href="activities.php?action=toggle&id=<?= $row['id'] ?>" style="text-decoration:none;">
                              <span class="status-pill <?= $row['status']=='active' ? 'status-pill--live' : ($row['status']=='draft'?'status-pill--draft':'') ?>" style="<?= $row['status']=='inactive'?'background:var(--surface-3); color:var(--text-3);':'' ?>">
                                  <i class="fa-solid fa-circle" style="font-size: 6px"></i> <?= ucfirst($row['status']) ?>
                              </span>
                          </a>
                        </td>
                        <td style="padding:15px; text-align:right;">
                          <a href="activities.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-ghost" style="padding:6px 12px; font-size:12px;"><i class="fa-solid fa-pen"></i></a>
                          <a href="activities.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this activity permanently?')" class="btn btn-ghost" style="padding:6px 12px; font-size:12px; color:var(--danger)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                      </tr>
                      <?php
                          endwhile;
                      else:
                      ?>
                          <tr><td colspan="4" style="text-align:center; padding:30px; color:var(--text-3);">No activities configured yet.</td></tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

              <?php elseif ($action === 'add' || $action === 'edit'):
                  $r = [
                    'id'=>0, 'title'=>'', 'tag'=>'Experience', 'short_description'=>'', 'long_description'=>'',
                    'duration'=>'', 'age_group'=>'All ages welcome', 'group_size'=>'1-6 guests',
                    'characteristics'=>'Practical and interactive', 'price'=>'From $— per guest',
                    'image'=>'', 'display_order'=>1, 'status'=>'active'
                  ];
                  if($action === 'edit' && isset($_GET['id'])) {
                      $res = $conn->query("SELECT * FROM activities WHERE id=".(int)$_GET['id']);
                      if($res && $res->num_rows>0) $r = $res->fetch_assoc();
                  }
              ?>
                <form method="POST" action="activities.php" class="crud-form" enctype="multipart/form-data">
                  <input type="hidden" name="id" value="<?= $r['id'] ?>" />

                  <div class="form-group">
                    <label>Activity Title / Name *</label>
                    <input type="text" name="title" required value="<?= htmlspecialchars($r['title']) ?>" placeholder="e.g. Rwandan Cuisine Cooking Class" />
                  </div>

                  <div class="flex-row-gap">
                      <div class="form-group">
                        <label>Category Tag</label>
                        <input type="text" name="tag" placeholder="e.g. Experience" value="<?= htmlspecialchars((string)$r['tag']) ?>" />
                      </div>
                      <div class="form-group">
                        <label>Base Price / Cost</label>
                        <input type="text" name="price" placeholder="e.g. From $45 per guest" value="<?= htmlspecialchars((string)$r['price']) ?>" />
                      </div>
                  </div>

                  <div class="flex-row-gap">
                      <div class="form-group">
                        <label>Duration</label>
                        <input type="text" name="duration" placeholder="e.g. Half-Day, 3 Hours" value="<?= htmlspecialchars((string)$r['duration']) ?>" />
                      </div>
                      <div class="form-group">
                        <label>Group Size</label>
                        <input type="text" name="group_size" placeholder="e.g. 1-6 guests" value="<?= htmlspecialchars((string)$r['group_size']) ?>" />
                      </div>
                  </div>

                  <div class="flex-row-gap">
                      <div class="form-group">
                        <label>Age Requirement</label>
                        <input type="text" name="age_group" placeholder="e.g. All ages welcome" value="<?= htmlspecialchars((string)$r['age_group']) ?>" />
                      </div>
                      <div class="form-group">
                        <label>Characteristics</label>
                        <input type="text" name="characteristics" placeholder="e.g. Practical and interactive" value="<?= htmlspecialchars((string)$r['characteristics']) ?>" />
                      </div>
                  </div>

                  <div class="form-group">
                    <label>Short Description (Used in Grid Cards) *</label>
                    <textarea name="short_description" rows="3" required placeholder="A brief 1-2 sentence hook..."><?= htmlspecialchars((string)$r['short_description']) ?></textarea>
                  </div>

                  <div class="form-group">
                    <label>Full Activity Content (Rich Sections) *</label>
                    <div id="blockBuilderContainer" style="display:flex; flex-direction:column; gap:15px;"></div>
                    <div style="display:flex; gap:10px; margin-top:10px;">
                        <button type="button" class="b-btn" onclick="addLeadBtn()"><i class="fa-solid fa-paragraph"></i> Add Lead Section</button>
                        <button type="button" class="b-btn" onclick="addStdBtn()"><i class="fa-solid fa-heading"></i> Add Standard Section</button>
                    </div>
                    <textarea name="long_description" id="actualLongDesc" style="display:none;" required><?= htmlspecialchars((string)$r['long_description']) ?></textarea>
                  </div>

                  <div class="flex-row-gap">
                     <div class="form-group">
                       <label>Cover Image <?= empty($r['image']) ? '*' : '' ?></label>
                       <input type="file" name="image" accept="image/*" <?= empty($r['image']) ? 'required' : '' ?> />
                       <input type="hidden" name="existing_image" value="<?= htmlspecialchars($r['image']) ?>" />
                       <?php if($r['image']): ?>
                           <small style="color:var(--text-3); font-size:12px;">Current File: <code><?= htmlspecialchars($r['image']) ?></code> (Leave empty to keep current)</small>
                       <?php endif; ?>
                     </div>
                     <div class="form-group">
                       <label>Display Order</label>
                       <input type="number" name="display_order" value="<?= htmlspecialchars((string)$r['display_order']) ?>" />
                     </div>
                  </div>

                  <div class="form-group">
                    <label>Marketing Status</label>
                    <select name="status">
                        <option value="active" <?= $r['status']=='active'?'selected':'' ?>>Active & Live</option>
                        <option value="inactive" <?= $r['status']=='inactive'?'selected':'' ?>>Inactive / Hidden</option>
                        <option value="draft" <?= $r['status']=='draft'?'selected':'' ?>>Draft</option>
                    </select>
                  </div>

                  <div class="form-group" style="margin-top:10px;">
                    <button type="submit" class="crud-btn"><i class="fa-solid fa-floppy-disk"></i> Save Activity</button>
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
    document.addEventListener('DOMContentLoaded', () => {
        const hiddenTextarea = document.getElementById('actualLongDesc');
        const builderContainer = document.getElementById('blockBuilderContainer');
        const form = document.querySelector('.crud-form');

        if (!hiddenTextarea || !builderContainer) return;

        function createLeadBlock(content = '') {
            const div = document.createElement('div');
            div.className = 'b-block';
            div.dataset.type = 'lead';
            div.innerHTML = `
                <div class="b-header">
                    <span><i class="fa-solid fa-paragraph"></i> Lead Section (Introduction Dropcap)</span>
                    <div class="b-actions"><button type="button" class="b-action-btn b-del" title="Remove block"><i class="fa-solid fa-xmark"></i></button></div>
                </div>
                <div class="b-row">
                    <textarea placeholder="Write your lead introduction paragraph here... Press Enter twice for multiple paragraphs." class="b-lead-text">${content}</textarea>
                </div>
            `;
            div.querySelector('.b-del').onclick = () => div.remove();
            builderContainer.appendChild(div);
        }

        function createStandardBlock(heading = '', paragraphs = '') {
            const div = document.createElement('div');
            div.className = 'b-block';
            div.dataset.type = 'standard';
            div.innerHTML = `
                <div class="b-header">
                    <span><i class="fa-solid fa-heading"></i> Standard Section</span>
                    <div class="b-actions"><button type="button" class="b-action-btn b-del" title="Remove block"><i class="fa-solid fa-xmark"></i></button></div>
                </div>
                <div class="b-row">
                    <input type="text" placeholder="Section Heading (H2) - Optional" class="b-heading" value="${heading.replace(/"/g, '&quot;')}" />
                </div>
                <div class="b-row">
                    <textarea placeholder="Type paragraphs here... Press Enter twice to create separate paragraphs." class="b-paragraphs">${paragraphs}</textarea>
                </div>
            `;
            div.querySelector('.b-del').onclick = () => div.remove();
            builderContainer.appendChild(div);
        }

        // Parse existing content
        const initialHtml = hiddenTextarea.value;
        if (initialHtml.trim()) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(initialHtml, 'text/html');
            doc.querySelectorAll('section.prose-block').forEach(sec => {
                if (sec.classList.contains('prose-block--lead')) {
                    const parts = [];
                    sec.childNodes.forEach(child => {
                        if (child.tagName === 'P') parts.push(child.innerHTML.replace(/<br\s*\/?>/g, '\n'));
                    });
                    createLeadBlock(parts.join('\n\n'));
                } else {
                    let h2 = sec.querySelector('h2');
                    let h2Text = h2 ? h2.innerHTML : '';
                    const parts = [];
                    sec.childNodes.forEach(child => {
                        if (child.tagName === 'P') parts.push(child.innerHTML.replace(/<br\s*\/?>/g, '\n'));
                    });
                    createStandardBlock(h2Text, parts.join('\n\n'));
                }
            });
        } else {
            createLeadBlock();
        }

        window.addLeadBtn = () => createLeadBlock();
        window.addStdBtn = () => createStandardBlock();

        // Serialize on submit
        form.addEventListener('submit', () => {
            let htmlStr = '';
            builderContainer.querySelectorAll('.b-block').forEach(block => {
                if (block.dataset.type === 'lead') {
                    let text = block.querySelector('.b-lead-text').value.trim();
                    if (!text) return;
                    let parts = text.split(/\n\s*\n/);
                    let out = '<section class="prose-block prose-block--lead">';
                    parts.forEach((pText, i) => {
                        let formattedP = pText.trim().replace(/\n/g, '<br/>');
                        if (formattedP) out += `<p${i === 0 ? ' class="lead-dropcap"' : ''}>${formattedP}</p>`;
                    });
                    out += '</section>';
                    htmlStr += out;
                } else if (block.dataset.type === 'standard') {
                    let heading = block.querySelector('.b-heading').value.trim();
                    let params = block.querySelector('.b-paragraphs').value.trim();
                    if (!heading && !params) return;
                    let out = '<section class="prose-block">';
                    if (heading) out += `<h2>${heading}</h2>`;
                    if (params) {
                        params.split(/\n\s*\n/).forEach(pText => {
                            let formattedP = pText.trim().replace(/\n/g, '<br/>');
                            if (formattedP) out += `<p>${formattedP}</p>`;
                        });
                    }
                    out += '</section>';
                    htmlStr += out;
                }
            });
            hiddenTextarea.value = htmlStr;
        });
    });
    </script>
  </body>
</html>

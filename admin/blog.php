<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';

// Handle Actions (Delete/Toggle)
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM blogs WHERE id = $id");
    header("Location: blog.php?msg=" . urlencode('Blog post deleted successfully.'));
    exit;
}
if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT status FROM blogs WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if ($row['status'] === 'published') $ns = 'draft';
        elseif ($row['status'] === 'draft') $ns = 'published';
        else $ns = 'published'; // archived to published
        $conn->query("UPDATE blogs SET status = '$ns' WHERE id = $id");
    }
    header("Location: blog.php?msg=" . urlencode('Status toggled successfully.'));
    exit;
}

// Handle Form Submissions (Add / Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = $conn->real_escape_string($_POST['title']);
    $sub_title = $conn->real_escape_string($_POST['sub_title']);
    $kicker = $conn->real_escape_string($_POST['kicker']);
    $category = $conn->real_escape_string($_POST['category']);
    $slug = $conn->real_escape_string($_POST['slug']);
    
    // Auto-generate slug if empty
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title'])));
        $slug = preg_replace('/-+/', '-', $slug);
    }

    $date_published = $conn->real_escape_string($_POST['date_published']);
    $read_time = $conn->real_escape_string($_POST['read_time']);
    $chips = $conn->real_escape_string($_POST['chips']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Content can be HTML so just escape properly
    $content = $conn->real_escape_string($_POST['content']);
    
    // Image handling
    $thumbnail = $conn->real_escape_string($_POST['existing_thumbnail'] ?? '');
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['thumbnail']['tmp_name'];
        $name = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($_FILES['thumbnail']['name']));
        $uploadDir = __DIR__ . '/../img/blogs/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        if (move_uploaded_file($tmpName, $uploadDir . $name)) {
            $thumbnail = $conn->real_escape_string($name);
        }
    }
    
    if ($id > 0) {
        $conn->query("UPDATE blogs SET 
                      title='$title', sub_title='$sub_title', slug='$slug', kicker='$kicker', 
                      category='$category', date_published='$date_published', read_time='$read_time', 
                      chips='$chips', thumbnail='$thumbnail', content='$content', status='$status' 
                      WHERE id=$id");
        $msg = "Blog post updated successfully";
    } else {
        $conn->query("INSERT INTO blogs 
                      (title, sub_title, slug, kicker, category, date_published, read_time, chips, thumbnail, content, status) 
                      VALUES 
                      ('$title', '$sub_title', '$slug', '$kicker', '$category', '$date_published', '$read_time', '$chips', '$thumbnail', '$content', '$status')");
        $msg = "Blog post newly published";
    }
    // Render list immediately after save
    $action = 'list';
}

$pageTitle = 'Travel Blog Management — Virunga Homestay CMS';
$currentPage = 'blog'; // Highlights active sidebar tab

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
      .content-editor { font-family: 'Courier New', Courier, monospace; font-size: 13px; line-height: 1.5; background:var(--surface-3); }
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
            <h1 class="page-header__title"><?php echo ($action==='add' || $action==='edit') ? 'Blog Post Editor' : 'Travel Blog Management'; ?></h1>
            <p class="page-header__sub">Publish stories, news, and SEO-optimized essays to the homestay portal.</p>
          </div>
          <div class="page-header__actions">
            <?php if ($action === 'list'): ?>
              <a href="blog.php?action=add" class="btn btn-amber"><i class="fa-solid fa-plus"></i> Write New Post</a>
            <?php else: ?>
              <a href="blog.php" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Back to Archive</a>
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
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Article</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Meta Data</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Status</th>
                        <th style="padding:15px; text-align:right;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $res = $conn->query("SELECT * FROM blogs ORDER BY id DESC");
                      if ($res && $res->num_rows > 0):
                          while($row = $res->fetch_assoc()):
                              $img = $row['thumbnail'];
                              if (strpos($img, '/') === false && strpos($img, 'http') !== 0) $img = '../img/blogs/'.$img;
                              elseif (strpos($img, './') === 0) $img = '../'.ltrim($img, './');
                      ?>
                      <tr style="border-bottom:1px solid var(--surface-3);">
                        <td style="padding:15px; display:flex; align-items:center; gap:16px;">
                          <img src="<?= htmlspecialchars($img) ?>" style="width:80px; height:56px; object-fit:cover; border-radius:8px; border:1px solid var(--border);" alt="<?= htmlspecialchars($row['title']) ?>"/>
                          <div>
                              <strong style="color:var(--text-1); font-size:15px; display:block; margin-bottom:4px; max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($row['title']) ?>"><?= htmlspecialchars($row['title']) ?></strong>
                              <span style="font-size:12px; color:var(--text-3);"><i class="fa-solid fa-link"></i> /blogs/<?= htmlspecialchars($row['slug']) ?></span>
                          </div>
                        </td>
                        <td style="padding:15px; font-size:13px; color:var(--text-2);">
                          <div style="margin-bottom:4px;"><strong>Category:</strong> <span style="text-transform:capitalize;"><?= $row['category'] ? htmlspecialchars($row['category']) : '-' ?></span></div>
                          <div style="margin-bottom:4px;"><strong>Date:</strong> <?= $row['date_published'] ? htmlspecialchars($row['date_published']) : '-' ?></div>
                          <div><strong>Read Time:</strong> <?= $row['read_time'] ? htmlspecialchars($row['read_time']) : '-' ?></div>
                        </td>
                        <td style="padding:15px;">
                          <a href="blog.php?action=toggle&id=<?= $row['id'] ?>" style="text-decoration:none;">
                              <span class="status-pill <?= $row['status']=='published' ? 'status-pill--live' : ($row['status']=='archived'?'':'status-pill--draft') ?>" style="<?= $row['status']=='archived'?'background:var(--surface-3); color:var(--text-3);':'' ?>">
                                  <i class="fa-solid fa-circle" style="font-size: 6px"></i> <?= ucfirst($row['status']) ?>
                              </span>
                          </a>
                        </td>
                        <td style="padding:15px; text-align:right;">
                          <a href="../blogdetails.php?slug=<?= $row['slug'] ?>" target="_blank" class="btn btn-ghost" style="padding:6px 12px; font-size:12px;" title="View Live"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                          <a href="blog.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-ghost" style="padding:6px 12px; font-size:12px;"><i class="fa-solid fa-pen"></i></a>
                          <a href="blog.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this blog post permanently?')" class="btn btn-ghost" style="padding:6px 12px; font-size:12px; color:var(--danger)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                      </tr>
                      <?php 
                          endwhile; 
                      else:
                      ?>
                          <tr><td colspan="4" style="text-align:center; padding:30px; color:var(--text-3);">No blog posts found. Start writing!</td></tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

              <?php elseif ($action === 'add' || $action === 'edit'): 
                  $r = [
                    'id'=>0, 'title'=>'', 'sub_title'=>'', 'slug'=>'', 'kicker'=>'',
                    'category'=>'nature', 'date_published'=>date('F j, Y'), 'read_time'=>'5 min read',
                    'chips'=>'Travel,Rwanda', 'thumbnail'=>'', 'content'=>'<section class="prose-block prose-block--lead"><p class="lead-dropcap">Start writing here...</p></section>', 'status'=>'draft'
                  ];
                  if($action === 'edit' && isset($_GET['id'])) {
                      $res = $conn->query("SELECT * FROM blogs WHERE id=".(int)$_GET['id']);
                      if($res && $res->num_rows>0) $r = $res->fetch_assoc();
                  }
              ?>
                <form method="POST" action="blog.php" class="crud-form" enctype="multipart/form-data">
                  <input type="hidden" name="id" value="<?= $r['id'] ?>" />
                  
                  <div class="form-group">
                    <label>Article Title *</label>
                    <input type="text" name="title" required value="<?= htmlspecialchars($r['title']) ?>" placeholder="e.g. Why Staying at a Homestay Makes Your Trip Unforgettable" />
                  </div>
                  
                  <div class="form-group">
                    <label>Subtitle / Excerpt</label>
                    <input type="text" name="sub_title" value="<?= htmlspecialchars((string)$r['sub_title']) ?>" placeholder="A short description that appears on the card..." />
                  </div>
                  
                  <div class="flex-row-gap">
                      <div class="form-group">
                        <label>Category</label>
                        <select name="category">
                            <option value="nature" <?= $r['category']=='nature'?'selected':'' ?>>Nature & Wildlife</option>
                            <option value="culture" <?= $r['category']=='culture'?'selected':'' ?>>Culture & Stay</option>
                            <option value="community" <?= $r['category']=='community'?'selected':'' ?>>Community</option>
                            <option value="travel" <?= $r['category']=='travel'?'selected':'' ?>>Travel Tips</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Kicker (Small top text)</label>
                        <input type="text" name="kicker" placeholder="e.g. Travel Feature" value="<?= htmlspecialchars((string)$r['kicker']) ?>" />
                      </div>
                  </div>

                  <div class="flex-row-gap">
                      <div class="form-group">
                        <label>Publish Date</label>
                        <input type="text" name="date_published" placeholder="e.g. August 14, 2025" value="<?= htmlspecialchars((string)$r['date_published']) ?>" />
                      </div>
                      <div class="form-group">
                        <label>Read Time Estimate</label>
                        <input type="text" name="read_time" placeholder="e.g. 8 min read" value="<?= htmlspecialchars((string)$r['read_time']) ?>" />
                      </div>
                  </div>
                  
                  <div class="flex-row-gap">
                      <div class="form-group">
                        <label>Tags / Chips (Comma separated)</label>
                        <input type="text" name="chips" placeholder="e.g. Gorilla Trekking,Nature,Conservation" value="<?= htmlspecialchars((string)$r['chips']) ?>" />
                      </div>
                      <div class="form-group">
                       <label>URL Slug (Auto-generated if empty)</label>
                       <input type="text" name="slug" value="<?= htmlspecialchars((string)$r['slug']) ?>" placeholder="e.g. wake-up-at-the-foot-of-the-volcanoes" />
                      </div>
                  </div>
                  
                  <div class="form-group">
                    <label>Cover Thumbnail Image <?= empty($r['thumbnail']) ? '*' : '' ?></label>
                    <input type="file" name="thumbnail" accept="image/*" <?= empty($r['thumbnail']) ? 'required' : '' ?> />
                    <input type="hidden" name="existing_thumbnail" value="<?= htmlspecialchars($r['thumbnail']) ?>" />
                    <?php if($r['thumbnail']): ?>
                        <small style="color:var(--text-3); font-size:12px;">Current File: <code><?= htmlspecialchars($r['thumbnail']) ?></code> (Leave empty to keep current)</small>
                    <?php endif; ?>
                  </div>

                  <div class="form-group">
                    <label>Article Sections & Content *</label>
                    <div id="blockBuilderContainer" style="display:flex; flex-direction:column; gap:15px;"></div>
                    <div style="display:flex; gap:10px; margin-top:10px;">
                        <button type="button" class="b-btn" onclick="addLeadBtn()"><i class="fa-solid fa-paragraph"></i> Add Lead Dropcap Section</button>
                        <button type="button" class="b-btn" onclick="addStdBtn()"><i class="fa-solid fa-heading"></i> Add Standard Section</button>
                    </div>
                    <textarea name="content" id="actualContent" style="display:none;" required><?= htmlspecialchars((string)$r['content']) ?></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label>Publish Status</label>
                    <select name="status">
                        <option value="published" <?= $r['status']=='published'?'selected':'' ?>>Live & Published</option>
                        <option value="draft" <?= $r['status']=='draft'?'selected':'' ?>>Saved as Draft</option>
                        <option value="archived" <?= $r['status']=='archived'?'selected':'' ?>>Archived</option>
                    </select>
                  </div>
                  
                  <div class="form-group" style="margin-top:10px;">
                    <button type="submit" class="crud-btn"><i class="fa-solid fa-cloud-arrow-up"></i> Save Article</button>
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
        const hiddenTextarea = document.getElementById('actualContent');
        const builderContainer = document.getElementById('blockBuilderContainer');
        const form = document.querySelector('.crud-form');
        
        if(!hiddenTextarea || !builderContainer) return;
        
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
                    <textarea placeholder="Write your lead introduction paragraph here... Press Enter twice for multiple paragraphs." class="b-lead-text" required>${content}</textarea>
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
                    <textarea placeholder="Type paragraphs here... Press Enter twice to create separate paragraphs." class="b-paragraphs" required>${paragraphs}</textarea>
                </div>
            `;
            div.querySelector('.b-del').onclick = () => div.remove();
            builderContainer.appendChild(div);
        }
        
        // Parse Initial Content
        const initialHtml = hiddenTextarea.value;
        if(initialHtml.trim()) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(initialHtml, 'text/html');
            doc.querySelectorAll('section.prose-block').forEach(sec => {
                if(sec.classList.contains('prose-block--lead')) {
                    let textContent = '';
                    const parts = [];
                    sec.childNodes.forEach(child => {
                        if (child.tagName === 'P') parts.push(child.innerHTML.replace(/<br\s*\/?>/g, '\\n'));
                    });
                    textContent = parts.join('\\n\\n');
                    createLeadBlock(textContent);
                } else {
                    let h2 = sec.querySelector('h2');
                    let h2Text = h2 ? h2.innerHTML : '';
                    const parts = [];
                    sec.childNodes.forEach(child => {
                        if (child.tagName === 'P') parts.push(child.innerHTML.replace(/<br\s*\/?>/g, '\\n'));
                    });
                    let pContent = parts.join('\\n\\n');
                    createStandardBlock(h2Text, pContent);
                }
            });
        } else {
            createLeadBlock();
        }
        
        // Add Buttons
        window.addLeadBtn = () => createLeadBlock();
        window.addStdBtn = () => createStandardBlock();
        
        // Serialize on submit
        form.addEventListener('submit', () => {
            let htmlStr = '';
            builderContainer.querySelectorAll('.b-block').forEach(block => {
                if(block.dataset.type === 'lead') {
                    let text = block.querySelector('.b-lead-text').value.trim();
                    if(!text) return;
                    let parts = text.split(/\\n\\s*\\n/);
                    let out = '<section class="prose-block prose-block--lead">';
                    parts.forEach((pText, i) => {
                        let formattedP = pText.trim().replace(/\\n/g, '<br/>');
                        if(formattedP) {
                            out += `<p${i===0 ? ' class="lead-dropcap"' : ''}>${formattedP}</p>`;
                        }
                    });
                    out += '</section>';
                    if(parts.length > 0) htmlStr += out;
                } else if(block.dataset.type === 'standard') {
                    let heading = block.querySelector('.b-heading').value.trim();
                    let params = block.querySelector('.b-paragraphs').value.trim();
                    if(!heading && !params) return;
                    
                    let out = '<section class="prose-block">';
                    if(heading) out += `<h2>${heading}</h2>`;
                    
                    if(params) {
                        let pArray = params.split(/\\n\\s*\\n/);
                        pArray.forEach(pText => {
                            let formattedP = pText.trim().replace(/\\n/g, '<br/>');
                            if(formattedP) out += `<p>${formattedP}</p>`;
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

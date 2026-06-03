<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';

// Handle Actions
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM shop_items WHERE id = $id");
    header("Location: shop.php?msg=" . urlencode('Item deleted successfully.'));
    exit;
}
if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT status FROM shop_items WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if ($row['status'] === 'active') $ns = 'inactive';
        elseif ($row['status'] === 'inactive') $ns = 'active';
        else $ns = 'active'; // draft to active
        $conn->query("UPDATE shop_items SET status = '$ns' WHERE id = $id");
    }
    header("Location: shop.php?msg=" . urlencode('Status toggled successfully.'));
    exit;
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = (float)($_POST['price'] ?? 0);
    $tag = $conn->real_escape_string($_POST['tag']);
    $category = $conn->real_escape_string($_POST['category']);
    $badge = $conn->real_escape_string($_POST['badge']);
    $meta_1 = $conn->real_escape_string($_POST['meta_1']);
    $meta_2 = $conn->real_escape_string($_POST['meta_2']);
    $meta_3 = $conn->real_escape_string($_POST['meta_3']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Image handling
    $image = $conn->real_escape_string($_POST['existing_image'] ?? '');
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        $name = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($_FILES['image']['name']));
        $uploadDir = __DIR__ . '/../img/shop/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        if (move_uploaded_file($tmpName, $uploadDir . $name)) {
            $image = $conn->real_escape_string($name);
        }
    }
    
    if ($id > 0) {
        $conn->query("UPDATE shop_items SET 
                      title='$title', description='$description', price=$price, tag='$tag', 
                      category='$category', badge='$badge', meta_1='$meta_1', meta_2='$meta_2', meta_3='$meta_3', 
                      image='$image', status='$status' 
                      WHERE id=$id");
        $msg = "Item updated successfully";
    } else {
        $conn->query("INSERT INTO shop_items 
                      (title, description, price, tag, category, badge, meta_1, meta_2, meta_3, image, status) 
                      VALUES 
                      ('$title', '$description', $price, '$tag', '$category', '$badge', '$meta_1', '$meta_2', '$meta_3', '$image', '$status')");
        $msg = "Item added successfully";
    }
    $action = 'list';
}

$pageTitle = 'Shop Items Management — Virunga Homestay CMS';
$currentPage = 'shop';

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
      .crud-form { display:flex; flex-direction:column; gap:20px; max-width:850px; background:var(--surface-1); padding:30px; border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-sm); }
      .crud-form .form-group { display:flex; flex-direction:column; gap:6px; }
      .crud-form label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2); }
      .crud-form input, .crud-form select, .crud-form textarea { padding: 12px; border: 1px solid var(--border); background:var(--surface-2); color:var(--text-1); border-radius:8px; font-family: inherit; font-size:14px; transition:border-color 0.2s; box-sizing:border-box;}
      .crud-form input:focus, .crud-form select:focus, .crud-form textarea:focus { border-color:var(--amber); outline:none; }
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
            <h1 class="page-header__title"><?php echo ($action==='add' || $action==='edit') ? 'Shop Editor' : 'Shop Items Management'; ?></h1>
            <p class="page-header__sub">List and manage crafts, souvenirs, and locally sourced items.</p>
          </div>
          <div class="page-header__actions">
            <?php if ($action === 'list'): ?>
              <a href="shop.php?action=add" class="btn btn-amber"><i class="fa-solid fa-plus"></i> Add New Item</a>
            <?php else: ?>
              <a href="shop.php" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Back to Products</a>
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
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Product</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Category</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Price</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Status</th>
                        <th style="padding:15px; text-align:right;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $res = $conn->query("SELECT * FROM shop_items ORDER BY id DESC");
                      if ($res && $res->num_rows > 0):
                          while($row = $res->fetch_assoc()):
                              $img = $row['image'];
                              if (strpos($img, '/') === false && strpos($img, 'http') !== 0) $img = '../img/shop/'.$img;
                              elseif (strpos($img, './') === 0) $img = '../'.ltrim($img, './');
                      ?>
                      <tr style="border-bottom:1px solid var(--surface-3);">
                        <td style="padding:15px; display:flex; align-items:center; gap:16px;">
                          <img src="<?= htmlspecialchars($img) ?>" style="width:56px; height:56px; object-fit:cover; border-radius:8px; border:1px solid var(--border);" alt="<?= htmlspecialchars($row['title']) ?>"/>
                          <div>
                              <strong style="color:var(--text-1); font-size:15px; display:block; margin-bottom:4px; text-transform:capitalize;"><?= htmlspecialchars($row['title']) ?></strong>
                              <span style="font-size:12px; color:var(--text-3);"><i class="fa-solid fa-hashtag"></i> <?= htmlspecialchars($row['tag']) ?></span>
                          </div>
                        </td>
                        <td style="padding:15px; font-size:13px; color:var(--text-2);">
                          <?= htmlspecialchars($row['category']) ?>
                        </td>
                        <td style="padding:15px;">
                          <div style="font-weight:600; color:var(--amber); font-size:14px;">$<?= $row['price'] ?></div>
                        </td>
                        <td style="padding:15px;">
                          <a href="shop.php?action=toggle&id=<?= $row['id'] ?>" style="text-decoration:none;">
                              <span class="status-pill <?= $row['status']=='active' ? 'status-pill--live' : ($row['status']=='draft'?'status-pill--draft':'') ?>" style="<?= $row['status']=='inactive'?'background:var(--surface-3); color:var(--text-3);':'' ?>">
                                  <i class="fa-solid fa-circle" style="font-size: 6px"></i> <?= ucfirst($row['status']) ?>
                              </span>
                          </a>
                        </td>
                        <td style="padding:15px; text-align:right;">
                          <a href="shop.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-ghost" style="padding:6px 12px; font-size:12px;"><i class="fa-solid fa-pen"></i></a>
                          <a href="shop.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this item?')" class="btn btn-ghost" style="padding:6px 12px; font-size:12px; color:var(--danger)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                      </tr>
                      <?php 
                          endwhile; 
                      else:
                      ?>
                          <tr><td colspan="5" style="text-align:center; padding:30px; color:var(--text-3);">No shop items configured.</td></tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

              <?php elseif ($action === 'add' || $action === 'edit'): 
                  $r = [
                    'id'=>0, 'title'=>'', 'description'=>'', 'price'=>'0.00', 'tag'=>'crafts',
                    'category'=>'Local Crafts', 'badge'=>'', 'meta_1'=>'', 'meta_2'=>'', 'meta_3'=>'',
                    'image'=>'', 'status'=>'active'
                  ];
                  if($action === 'edit' && isset($_GET['id'])) {
                      $res = $conn->query("SELECT * FROM shop_items WHERE id=".(int)$_GET['id']);
                      if($res && $res->num_rows>0) $r = $res->fetch_assoc();
                  }
              ?>
                <form method="POST" action="shop.php" class="crud-form" enctype="multipart/form-data">
                  <input type="hidden" name="id" value="<?= $r['id'] ?>" />
                  
                  <div class="flex-row-gap">
                      <div class="form-group">
                        <label>Product Name / Title *</label>
                        <input type="text" name="title" required value="<?= htmlspecialchars($r['title']) ?>" placeholder="e.g. Traditional Rwandan Drums" />
                      </div>
                      <div class="form-group">
                        <label>Price ($) *</label>
                        <input type="number" step="0.01" name="price" required placeholder="e.g. 50.00" value="<?= htmlspecialchars((string)$r['price']) ?>" />
                      </div>
                  </div>
                  
                  <div class="flex-row-gap">
                      <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" placeholder="e.g. Local Crafts" value="<?= htmlspecialchars((string)$r['category']) ?>" />
                      </div>
                      <div class="form-group">
                        <label>Tag</label>
                        <input type="text" name="tag" placeholder="e.g. crafts" value="<?= htmlspecialchars((string)$r['tag']) ?>" />
                      </div>
                  </div>

                  <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="Describe the item..."><?= htmlspecialchars((string)$r['description']) ?></textarea>
                  </div>

                  <div class="flex-row-gap">
                      <div class="form-group">
                        <label>Meta Detail #1 (Optional)</label>
                        <input type="text" name="meta_1" placeholder="e.g. 100% Handwoven" value="<?= htmlspecialchars((string)$r['meta_1']) ?>" />
                      </div>
                      <div class="form-group">
                        <label>Badge Highlight (Optional)</label>
                        <input type="text" name="badge" placeholder="e.g. Best Seller" value="<?= htmlspecialchars((string)$r['badge']) ?>" />
                      </div>
                  </div>
                  
                  <div class="flex-row-gap">
                     <div class="form-group">
                       <label>Product Image <?= empty($r['image']) ? '*' : '' ?></label>
                       <input type="file" name="image" accept="image/*" <?= empty($r['image']) ? 'required' : '' ?> />
                       <input type="hidden" name="existing_image" value="<?= htmlspecialchars($r['image']) ?>" />
                       <?php if($r['image']): ?>
                           <small style="color:var(--text-3); font-size:12px;">Current: <code><?= htmlspecialchars($r['image']) ?></code></small>
                       <?php endif; ?>
                     </div>
                     <div class="form-group">
                       <label>Status</label>
                       <select name="status">
                           <option value="active" <?= $r['status']=='active'?'selected':'' ?>>Active & Visible</option>
                           <option value="inactive" <?= $r['status']=='inactive'?'selected':'' ?>>Inactive / Sold Out</option>
                           <option value="draft" <?= $r['status']=='draft'?'selected':'' ?>>Draft</option>
                       </select>
                     </div>
                  </div>
                  
                  <div class="form-group" style="margin-top:10px;">
                    <button type="submit" class="crud-btn"><i class="fa-solid fa-floppy-disk"></i> Save Product</button>
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

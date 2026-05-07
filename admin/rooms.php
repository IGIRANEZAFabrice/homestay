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
    $conn->query("DELETE FROM rooms WHERE id = $id");
    header("Location: rooms.php?msg=" . urlencode('Room deleted successfully.'));
    exit;
}
if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT status FROM rooms WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        $ns = $row['status'] === 'active' ? 'inactive' : 'active';
        $conn->query("UPDATE rooms SET status = '$ns' WHERE id = $id");
    }
    header("Location: rooms.php?msg=" . urlencode('Status toggled successfully.'));
    exit;
}

// Handle Form Submissions (Add / Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = $conn->real_escape_string($_POST['title']);
    $meters = (int)($_POST['meters'] ?? 0);
    $guest_number = (int)($_POST['guest_number'] ?? 0);
    $bed_type = $conn->real_escape_string($_POST['bed_type']);
    $price_single = (float)($_POST['price_single'] ?? 0);
    $price_double = (float)($_POST['price_double'] ?? 0);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Convert paths to raw filenames if needed
    $image = $conn->real_escape_string($_POST['existing_image'] ?? '');
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        $name = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($_FILES['image']['name']));
        $uploadDir = __DIR__ . '/../img/rooms/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        if (move_uploaded_file($tmpName, $uploadDir . $name)) {
            $image = $conn->real_escape_string($name);
        }
    }
    
    if ($id > 0) {
        $conn->query("UPDATE rooms SET title='$title', meters=$meters, guest_number=$guest_number, bed_type='$bed_type', price_single=$price_single, price_double=$price_double, image='$image', status='$status' WHERE id=$id");
        $msg = "Room updated successfully";
    } else {
        $conn->query("INSERT INTO rooms (title, meters, guest_number, bed_type, price_single, price_double, image, status) VALUES ('$title', $meters, $guest_number, '$bed_type', $price_single, $price_double, '$image', '$status')");
        $msg = "Room added successfully";
    }
    // Render list immediately after save
    $action = 'list';
}

$pageTitle = 'Rooms Management — Virunga Homestay CMS';
$currentPage = 'rooms'; // Highlights active sidebar tab

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
      .crud-form input, .crud-form select { padding: 12px; border: 1px solid var(--border); background:var(--surface-2); color:var(--text-1); border-radius:8px; font-family: inherit; font-size:14px; transition:border-color 0.2s;}
      .crud-form input:focus, .crud-form select:focus { border-color:var(--amber); outline:none; }
      .crud-btn { padding: 12px 20px; border:none; background:var(--amber); color:#000; border-radius:8px; cursor:pointer; font-weight:600; font-size:14px;}
      .crud-btn:hover { filter:brightness(0.95); }
      .alert { padding:14px 20px; background:var(--success); color:#fff; border-radius:8px; margin-bottom:20px; font-size:14px; font-weight:500; display:flex; align-items:center; gap:10px; }
    </style>
  </head>
  <body>
    <?php include 'includes/header.php'; ?>

    <div class="dash-layout" id="dashLayout">
      <?php include 'includes/sidebar.php'; ?>

      <main class="dash-main">
        <div class="page-header">
          <div>
            <h1 class="page-header__title"><?php echo ($action==='add' || $action==='edit') ? 'Room Editor' : 'Rooms Management'; ?></h1>
            <p class="page-header__sub">Add, edit, or manage availability for all homestay rooms.</p>
          </div>
          <div class="page-header__actions">
            <?php if ($action === 'list'): ?>
              <a href="rooms.php?action=add" class="btn btn-amber"><i class="fa-solid fa-plus"></i> Add New Room</a>
            <?php else: ?>
              <a href="rooms.php" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Back to Listings</a>
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
                <div class="panel__body--tight">
                  <table class="page-table" style="width:100%; border-collapse:collapse;">
                    <thead style="border-bottom:2px solid var(--border); text-align:left;">
                      <tr>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Room Setup</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Capacity</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Size & Price</th>
                        <th style="padding:15px; font-weight:600; color:var(--text-3); font-size:12px; text-transform:uppercase;">Status</th>
                        <th style="padding:15px; text-align:right;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $res = $conn->query("SELECT * FROM rooms ORDER BY id DESC");
                      if ($res && $res->num_rows > 0):
                          while($row = $res->fetch_assoc()):
                              // Dynamic Image formatting mapping
                              $img = $row['image'];
                              if (strpos($img, '/') === false && strpos($img, 'http') !== 0) {
                                  $img = '../img/rooms/'.$img;
                              } elseif (strpos($img, './') === 0) {
                                  $img = '../'.ltrim($img, './');
                              }
                      ?>
                      <tr style="border-bottom:1px solid var(--surface-3);">
                        <td style="padding:15px; display:flex; align-items:center; gap:16px;">
                          <img src="<?= htmlspecialchars($img) ?>" style="width:80px; height:56px; object-fit:cover; border-radius:8px; border:1px solid var(--border);" alt="<?= htmlspecialchars($row['title']) ?>"/>
                          <strong style="color:var(--text-1); font-size:15px;"><?= htmlspecialchars($row['title']) ?></strong>
                        </td>
                        <td style="padding:15px; color:var(--text-2);">
                          <div style="display:flex; align-items:center; gap:6px;"><i class="fa-solid fa-users"></i> <?= $row['guest_number'] ? $row['guest_number'] : '-' ?></div>
                        </td>
                        <td style="padding:15px; font-size:13px; color:var(--text-2);">
                          <div style="margin-bottom:4px;"><strong style="color:var(--text-1);">Bed:</strong> <?= $row['bed_type'] ? htmlspecialchars($row['bed_type']) : '-' ?></div>
                          <div style="margin-bottom:4px;"><strong style="color:var(--text-1);">Size:</strong> <?= $row['meters'] ? $row['meters'].'m²' : '-' ?></div>
                          <div style="margin-bottom:4px;"><strong style="color:var(--text-1);">Single Occ:</strong> <?= $row['price_single'] ? '$'.$row['price_single'] : '-' ?></div>
                          <div><strong style="color:var(--text-1);">Double Occ:</strong> <?= $row['price_double'] ? '$'.$row['price_double'] : '-' ?></div>
                        </td>
                        <td style="padding:15px;">
                          <a href="rooms.php?action=toggle&id=<?= $row['id'] ?>" style="text-decoration:none;">
                              <span class="status-pill <?= $row['status']=='active' ? 'status-pill--live' : ($row['status']=='maintenance'?'status-pill--draft':'') ?>" style="<?= $row['status']=='inactive'?'background:var(--surface-3); color:var(--text-3);':'' ?>">
                                  <i class="fa-solid fa-circle" style="font-size: 6px"></i> <?= ucfirst($row['status']) ?>
                              </span>
                          </a>
                        </td>
                        <td style="padding:15px; text-align:right;">
                          <a href="rooms.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-ghost" style="padding:6px 12px; font-size:12px;"><i class="fa-solid fa-pen"></i></a>
                          <a href="rooms.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this room permanently?')" class="btn btn-ghost" style="padding:6px 12px; font-size:12px; color:var(--danger)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                      </tr>
                      <?php 
                          endwhile; 
                      else:
                      ?>
                          <tr><td colspan="5" style="text-align:center; padding:30px; color:var(--text-3);">No rooms found. Get started by adding a new room configuration!</td></tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

              <?php elseif ($action === 'add' || $action === 'edit'): 
                  $r = ['id'=>0,'title'=>'','meters'=>'','guest_number'=>'','bed_type'=>'','price_single'=>'','price_double'=>'','image'=>'','status'=>'active'];
                  if($action === 'edit' && isset($_GET['id'])) {
                      $res = $conn->query("SELECT * FROM rooms WHERE id=".(int)$_GET['id']);
                      if($res && $res->num_rows>0) $r = $res->fetch_assoc();
                  }
              ?>
                <form method="POST" action="rooms.php" class="crud-form" enctype="multipart/form-data">
                  <input type="hidden" name="id" value="<?= $r['id'] ?>" />
                  
                  <div class="form-group">
                    <label>Room Title / Name *</label>
                    <input type="text" name="title" required value="<?= htmlspecialchars($r['title']) ?>" placeholder="e.g. INGAGI Room" />
                  </div>
                  
                  <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                      <div class="form-group">
                        <label>Size (meters²)</label>
                        <input type="number" name="meters" placeholder="e.g. 32" value="<?= htmlspecialchars((string)$r['meters']) ?>" />
                      </div>
                      <div class="form-group">
                        <label>Max Guests</label>
                        <input type="number" name="guest_number" placeholder="e.g. 2" value="<?= htmlspecialchars((string)$r['guest_number']) ?>" />
                      </div>
                  </div>
                  
                  <div class="form-group">
                    <label>Bed Type</label>
                    <input type="text" name="bed_type" placeholder="e.g. King Bed" value="<?= htmlspecialchars((string)$r['bed_type']) ?>" />
                  </div>
                  
                  <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                      <div class="form-group">
                        <label>Single Occupancy / night</label>
                        <input type="number" step="0.01" name="price_single" placeholder="e.g. 150.00" value="<?= htmlspecialchars((string)$r['price_single']) ?>" />
                      </div>
                      <div class="form-group">
                        <label>Double Occupancy / night</label>
                        <input type="number" step="0.01" name="price_double" placeholder="e.g. 185.00" value="<?= htmlspecialchars((string)$r['price_double']) ?>" />
                      </div>
                  </div>
                  
                  <div class="form-group">
                    <label>Cover Image <?= empty($r['image']) ? '*' : '' ?></label>
                    <input type="file" name="image" accept="image/*" <?= empty($r['image']) ? 'required' : '' ?> />
                    <input type="hidden" name="existing_image" value="<?= htmlspecialchars($r['image']) ?>" />
                    <?php if($r['image']): ?>
                        <small style="color:var(--text-3); font-size:12px;">Current File: <code><?= htmlspecialchars($r['image']) ?></code> (Leave empty to keep current)</small>
                    <?php endif; ?>
                  </div>
                  
                  <div class="form-group">
                    <label>Marketing Status</label>
                    <select name="status">
                        <option value="active" <?= $r['status']=='active'?'selected':'' ?>>Active & Live</option>
                        <option value="inactive" <?= $r['status']=='inactive'?'selected':'' ?>>Hidden / Draft</option>
                        <option value="maintenance" <?= $r['status']=='maintenance'?'selected':'' ?>>Maintenance Lockdown</option>
                    </select>
                  </div>
                  
                  <div class="form-group" style="margin-top:10px;">
                    <button type="submit" class="crud-btn"><i class="fa-solid fa-floppy-disk"></i> Save Room Configuration</button>
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

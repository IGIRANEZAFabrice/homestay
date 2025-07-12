<?php
require_once '../include/connection.php';
$activity = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare('SELECT * FROM activities WHERE id = ? AND is_active = 1 LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $activity = $result->fetch_assoc();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $activity ? htmlspecialchars($activity['title']) : 'Activity Not Found'; ?> - Virunga Homestay</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/activities.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
        font-family: 'Poppins', Arial, sans-serif; 
        margin: 0; background: #f7f7f7; 
    }
      .activity-cover { 
        width: 100%; 
        height: 90vh;
        max-height: 90dvh; 
        object-fit: cover; 
        border-radius: 0 0 24px 24px; 
        box-shadow: 0 4px 24px rgba(0,0,0,0.08); 
    }
      .activity-container { 
        max-width: 700px; 
        margin: -80px auto 32px; 
        background: #fff; 
        border-radius: 24px; 
        box-shadow: 0 8px 32px rgba(0,0,0,0.08); 
        padding: 32px 24px 40px; 
        position: relative; 
        z-index: 2; 
    }
      .activity-title { 
        font-size: 2.2rem; 
        font-weight: 700; 
        margin-bottom: 16px; 
        color: #1a2a36; 
    }
      .activity-description { 
        font-size: 1.1rem; 
        color: #444; 
        line-height: 1.7; 
        margin-bottom: 0; 
        padding: 5px 30px;
    }
      @media (max-width: 900px) { 
        .activity-container { 
            max-width: 96vw; 
            padding: 20px 8vw 32px; 
        } 
    }
      @media (max-width: 600px) { 
        .activity-title { 
            font-size: 1.3rem; 
        } 
        .activity-container { 
            padding: 12px 2vw 24px; 
        } 
        .activity-cover { 
            max-height: 220px; 
        } 
    }
      .back-link { 
        display: inline-flex; 
        align-items: center; 
        gap: 8px; color: #1a2a36; 
        text-decoration: none; 
        font-weight: 500; 
        margin-bottom: 24px; 
        transition: color 0.2s; 
    }
      .back-link:hover { 
        color: #0a7b83; 
    }
    </style>
</head>
<body>
<?php include 'include/header.php'; ?>
<?php if ($activity): ?>
    <img class="activity-cover" src="../<?php echo buildImageUrl($activity['image'], 'activities'); ?>" alt="<?php echo htmlspecialchars($activity['title']); ?> Cover">
    <div class="activity-container">
        <a href="../index.php#experiencesGrid" class="back-link"><i class="fas fa-arrow-left"></i>Back to Home</a>
        <h1 class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></h1>
        
    </div>
    <div class="activity-description">
          <?php 
            // Use 'content' field from DB, not 'description'
            if (!empty($activity['content'])) {
              $desc = $activity['content'];
              $desc = preg_replace('/<script.*?>.*?<\/script>/is', '', $desc); // Remove script tags
              echo nl2br($desc);
            } else {
              echo '<em>No description available.</em>';
            }
          ?>
        </div>
<?php else: ?>
    <div class="activity-container">
        <a href="../index.php#experiencesGrid" class="back-link"><i class="fas fa-arrow-left"></i>Back to Activities</a>
        <h1 class="activity-title">Activity Not Found</h1>
        <div class="activity-description">Sorry, the activity you are looking for does not exist or is no longer available.</div>
    </div>
<?php endif; ?>
<?php include 'include/footer.php'; ?>
</body>
</html>

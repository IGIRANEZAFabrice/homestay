<?php
require_once '../../include/connection.php';
// Fetch contact messages
$messages = [];
$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at
DESC"); if ($result && $result->num_rows > 0) { while ($row =
$result->fetch_assoc()) { $messages[] = $row; } } ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Messages - Admin Panel</title>
    <link rel="stylesheet" href="../../css/admin.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>
    <div class="admin-container">
      <h1>Contact Us Messages</h1>
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($messages) === 0): ?>
          <tr>
            <td colspan="5" style="text-align: center; color: #888">
              No messages found.
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($messages as $i =>
          $msg): ?>
          <tr>
            <td><?php echo $i+1; ?></td>
            <td><?php echo htmlspecialchars($msg['name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($msg['email'] ?? ''); ?></td>
            <td>
              <?php echo nl2br(htmlspecialchars($msg['message'] ?? '')); ?>
            </td>
            <td><?php echo htmlspecialchars($msg['created_at'] ?? ''); ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </body>
</html>
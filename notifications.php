<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user = $_SESSION['user_id'];

$notif_query = mysqli_query($conn, "
  SELECT * FROM notifications 
  WHERE user_id = $current_user 
  ORDER BY created_at DESC
");

// Optionally mark all as read
mysqli_query($conn, "
  UPDATE notifications SET is_read = 1 
  WHERE user_id = $current_user AND is_read = 0
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Notifications</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-4">
  <h2 class="text-2xl font-bold mb-4 text-center">🔔 Notifications</h2>

  <div class="max-w-xl mx-auto space-y-4">
    <?php if (mysqli_num_rows($notif_query) > 0): ?>
      <?php while ($notif = mysqli_fetch_assoc($notif_query)): ?>
        <div class="bg-white shadow rounded-xl p-4">
          <p class="text-gray-800"><?php echo htmlspecialchars($notif['message']); ?></p>
          <p class="text-xs text-gray-400 mt-1"><?php echo date("M d, Y h:i A", strtotime($notif['created_at'])); ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-center text-gray-500">No notifications yet.</p>
    <?php endif; ?>
  </div>
</body>
</html>

<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user = $_SESSION['user_id'];

// Get matches where current user is either user1 or user2
$match_query = mysqli_query($conn, "
    SELECT 
        u.id, u.fullname, u.year, u.bio, u.interest, u.instagram, u.image_path
    FROM matches m
    JOIN users u 
        ON (u.id = IF(m.user1_id = $current_user, m.user2_id, m.user1_id))
    WHERE m.user1_id = $current_user OR m.user2_id = $current_user
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Matches - Campus Connect</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4">
    <nav class="bg-white shadow mb-4 p-4 rounded-lg flex justify-between text-sm">
  <a href="explore.php" class="text-blue-500 font-semibold">Explore</a>
  <a href="matches.php" class="text-green-500 font-semibold">Matches</a>
  <a href="logout.php" class="text-red-500 font-semibold">Logout</a>
</nav>

  <h2 class="text-2xl font-bold text-center mb-6">🎉 Your Matches</h2>

  <div class="grid gap-6 max-w-md mx-auto">

    <?php if (mysqli_num_rows($match_query) > 0): ?>
      <?php while ($match = mysqli_fetch_assoc($match_query)): ?>
        <div class="bg-white rounded-xl shadow p-4">
          <div class="flex items-center space-x-4">
            <img src="<?php echo $match['image_path'] ?? 'https://via.placeholder.com/80'; ?>" class="w-16 h-16 rounded-full object-cover border" />
            <div>
              <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($match['fullname']); ?></h3>
              <p class="text-sm text-gray-500">Year: <?php echo $match['year']; ?></p>
              <p class="text-sm text-gray-600 mt-1"><strong>Bio:</strong> <?php echo htmlspecialchars($match['bio']); ?></p>
              <p class="text-sm text-gray-600"><strong>Interest:</strong> <?php echo htmlspecialchars($match['interest']); ?></p>
              <p class="text-sm text-blue-600 mt-2"><strong>Instagram:</strong> @<?php echo htmlspecialchars($match['instagram']); ?></p>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="bg-white p-6 rounded-xl shadow text-center">
        <p class="text-gray-600 text-sm">You don't have any matches yet. Start swiping!</p>
      </div>
    <?php endif; ?>

  </div>
</body>
</html>

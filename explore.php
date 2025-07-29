<?php
session_start();
include("nav.php");
include("includes/db.php");



if (isset($_SESSION['match_msg'])) {
    echo "<div class='bg-green-100 text-green-800 p-2 rounded mb-4 text-center'>{$_SESSION['match_msg']}</div>";
    unset($_SESSION['match_msg']);
}



if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get current user's gender
$gender_sql = "SELECT gender FROM users WHERE id = $user_id";
$gender_res = mysqli_query($conn, $gender_sql);
$current_gender = mysqli_fetch_assoc($gender_res)['gender'];
$opposite = $current_gender == 'male' ? 'female' : 'male';

// Get users of opposite gender (and not self)
$sql = "SELECT * FROM users WHERE gender = '$opposite' AND id != $user_id";
$result = mysqli_query($conn, $sql);
$profiles = [];
while ($row = mysqli_fetch_assoc($result)) {
    $profiles[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Explore Matches</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="w-full max-w-md px-4 py-6">
  <div id="profile-container">
    <?php foreach ($profiles as $index => $profile): ?>
      <div class="profile-card bg-white p-4 rounded-xl shadow-md mb-4 text-center transition-all duration-300 <?= $index === 0 ? '' : 'hidden' ?>">
        <img src="<?= $profile['image_path'] ?>" class="w-32 h-32 rounded-full mx-auto mb-3 object-cover" />
        <h3 class="text-lg font-semibold"><?= $profile['fullname'] ?> (<?= $profile['year'] ?>)</h3>
        <p class="text-sm text-gray-600 mb-1"><?= $profile['bio'] ?></p>
        <p class="text-xs text-gray-500">Interests: <?= $profile['interest'] ?></p>
        <div class="mt-4 flex justify-around">
          <form method="POST" action="like.php">
            <input type="hidden" name="liked_id" value="<?= $profile['id'] ?>">
            <button type="submit" name="action" value="pass" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">Pass</button>
            <button type="submit" name="action" value="like" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded">Match</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="flex justify-between mt-4">
    <button onclick="prevCard()" class="text-xl">&larr;</button>
    <button onclick="nextCard()" class="text-xl">&rarr;</button>
  </div>
</div>

<script>
  let current = 0;
  const cards = document.querySelectorAll('.profile-card');

  function showCard(index) {
    cards.forEach((card, i) => {
      card.classList.toggle('hidden', i !== index);
    });
  }

  function nextCard() {
    if (current < cards.length - 1) {
      current++;
      showCard(current);
    }
  }

  function prevCard() {
    if (current > 0) {
      current--;
      showCard(current);
    }
  }
</script>
</body>
</html>

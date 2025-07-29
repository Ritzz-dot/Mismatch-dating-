<?php
session_start();
include("includes/db.php");

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if (isset($_POST['save'])) {
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $interest = mysqli_real_escape_string($conn, $_POST['interest']);
    $instagram = mysqli_real_escape_string($conn, $_POST['instagram']);

    // Handle image upload
    $image_path = null;
    if (!empty($_FILES['profile_image']['name'])) {
        $img_name = "profile_" . time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_dir = "uploads/profiles/";
        $target_file = $target_dir . $img_name;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    // Update user profile
    $update_sql = "UPDATE users SET 
        gender = '$gender',
        year = '$year',
        bio = '$bio',
        interest = '$interest',
        instagram = '$instagram'";

    if ($image_path !== null) {
        $update_sql .= ", image_path = '$image_path'";
    }

    $update_sql .= " WHERE id = $user_id";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: explore.php");
        exit;
    } else {
        $error = "Failed to save profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Setup Profile - Campus Connect</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
  <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-4 text-center">Complete Your Profile</h2>

    <?php if (isset($error)) { ?>
      <div class="bg-red-100 text-red-700 p-2 mb-4 text-sm rounded">
        <?php echo $error; ?>
      </div>
    <?php } ?>

    <form action="setup_profiles.php" method="POST" enctype="multipart/form-data">
      <label class="block text-sm mb-1">Gender</label>
      <select name="gender" required class="w-full mb-3 px-4 py-2 border rounded-md">
        <option value="">Select</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
      </select>

      <label class="block text-sm mb-1">Select Year</label>
      <select name="year" required class="w-full mb-3 px-4 py-2 border rounded-md">
        <option value="">Select</option>
        <option value="FY">FY</option>
        <option value="SY">SY</option>
        <option value="TY">TY</option>
      </select>

      <label class="block text-sm mb-1">Bio</label>
      <textarea name="bio" rows="3" placeholder="Something about you..." required class="w-full mb-3 px-4 py-2 border rounded-md"></textarea>

      <label class="block text-sm mb-1">Interests</label>
      <input type="text" name="interest" placeholder="e.g. Music, Movies" required class="w-full mb-3 px-4 py-2 border rounded-md" />

      <label class="block text-sm mb-1">Instagram Handle</label>
      <input type="text" name="instagram" placeholder="@yourhandle" required class="w-full mb-3 px-4 py-2 border rounded-md" />

      <label class="block text-sm mb-1">Profile Picture (optional)</label>
      <input type="file" name="profile_image" accept="image/*" class="w-full mb-4 px-4 py-2 border rounded-md" />

      <button type="submit" name="save" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition">Save Profile</button>
    </form>
  </div>
</body>
</html>

<?php
session_start();
include("includes/db.php");

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");
    if (mysqli_num_rows($query) == 1) {
        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];

            // Check if profile is complete (year, bio, interest, instagram filled)
            if (empty($user['year']) || empty($user['bio']) || empty($user['interest']) || empty($user['instagram'])) {
                header("Location: setup_profiles.php");
                exit;
            } else {
                header("Location: explore.php");
                exit;
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Campus Connect</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
  <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-4 text-center">Welcome Back</h2>

    <?php if (isset($error)) { ?>
      <div class="bg-red-100 text-red-700 p-2 mb-4 text-sm rounded">
        <?php echo $error; ?>
      </div>
    <?php } ?>

    <form action="login.php" method="POST">
      <input type="email" name="email" placeholder="College Email" required class="w-full mb-3 px-4 py-2 border rounded-md" />
      <input type="password" name="password" placeholder="Password" required class="w-full mb-4 px-4 py-2 border rounded-md" />
      <button type="submit" name="login" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Login</button>
    </form>

    <p class="text-sm mt-4 text-center">Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Register</a></p>
  </div>
</body>
</html>

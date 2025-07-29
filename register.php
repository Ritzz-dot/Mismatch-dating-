<?php
session_start();
include("includes/db.php"); // Make sure this file has your DB connection

if (isset($_POST['register'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered. Please login.";
        } else {
            // Create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (fullname, email, password) VALUES ('$fullname', '$email', '$hashed_password')";
            if (mysqli_query($conn, $query)) {
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                header("Location: login.php"); // Move to profile setup
                exit;
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - Campus Connect</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
  <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-4 text-center">Create Your Account</h2>

    <?php if (isset($error)) { ?>
      <div class="bg-red-100 text-red-700 p-2 mb-4 text-sm rounded">
        <?php echo $error; ?>
      </div>
    <?php } ?>

    <form action="register.php" method="POST">
      <input type="text" name="fullname" placeholder="Full Name" required class="w-full mb-3 px-4 py-2 border rounded-md" />
      <input type="email" name="email" placeholder="College Email" required class="w-full mb-3 px-4 py-2 border rounded-md" />
      <input type="password" name="password" placeholder="Password" required class="w-full mb-3 px-4 py-2 border rounded-md" />
      <input type="password" name="confirm_password" placeholder="Confirm Password" required class="w-full mb-4 px-4 py-2 border rounded-md" />
      <button type="submit" name="register" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Register</button>
    </form>

    <p class="text-sm mt-4 text-center">Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login</a></p>
  </div>
</body>
</html>

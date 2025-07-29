<?php
$host = 'sql105.infinityfree.com';
$user = 'if0_39515939';
$password = '27Bk04tO5lpON';  // ✅ MAMP default password is 'root'
$database = 'mismatch_db'; // ✅ Replace this with your actual DB name

$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

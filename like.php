<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id']) || !isset($_POST['action']) || !isset($_POST['liked_id'])) {
    header("Location: login.php");
    exit;
}

$liker_id = $_SESSION['user_id'];
$liked_id = intval($_POST['liked_id']);
$action = $_POST['action'];

if ($liker_id === $liked_id) {
    echo "You can't match with yourself.";
    exit;
}

// Handle Like Action
if ($action === 'like') {
    // Check if this like already exists
    $check_like = $conn->prepare("SELECT * FROM likes WHERE liker_id = ? AND liked_id = ?");
    $check_like->bind_param("ii", $liker_id, $liked_id);
    $check_like->execute();
    $like_result = $check_like->get_result();

    if ($like_result->num_rows === 0) {
        // Insert new like
        $insert_like = $conn->prepare("INSERT INTO likes (liker_id, liked_id) VALUES (?, ?)");
        $insert_like->bind_param("ii", $liker_id, $liked_id);
        $insert_like->execute();
        echo "You liked user $liked_id.<br>";
    } else {
        echo "You already liked this user.<br>";
    }

    // Check if mutual like exists
    $mutual = $conn->prepare("SELECT * FROM likes WHERE liker_id = ? AND liked_id = ?");
    $mutual->bind_param("ii", $liked_id, $liker_id);
    $mutual->execute();
    $mutual_result = $mutual->get_result();

    if ($mutual_result->num_rows > 0) {
        // Check if match already exists
        $check_match = $conn->prepare("SELECT * FROM matches WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)");
        $check_match->bind_param("iiii", $liker_id, $liked_id, $liked_id, $liker_id);
        $check_match->execute();
        $match_result = $check_match->get_result();

        if ($match_result->num_rows === 0) {
            // Insert match
            $insert_match = $conn->prepare("INSERT INTO matches (user1_id, user2_id) VALUES (?, ?)");
            $insert_match->bind_param("ii", $liker_id, $liked_id);
            $insert_match->execute();
            echo "<strong>It's a Match!</strong><br>";

            // Show Instagram handle of matched user
            $get_user = $conn->prepare("SELECT instagram FROM users WHERE user_id = ?");
            $get_user->bind_param("i", $liked_id);
            $get_user->execute();
            $user_result = $get_user->get_result();
            $user_data = $user_result->fetch_assoc();

            if (!empty($user_data['instagram'])) {
                echo "Matched user's Instagram: <strong>@" . htmlspecialchars($user_data['instagram']) . "</strong><br>";
            }
        } else {
            echo "Match already exists!<br>";

            // Also show Instagram if already matched
            $get_user = $conn->prepare("SELECT instagram FROM users WHERE id = ?");
            $get_user->bind_param("i", $liked_id);
            $get_user->execute();
            $user_result = $get_user->get_result();
            $user_data = $user_result->fetch_assoc();

            if (!empty($user_data['instagram'])) {
                echo "Matched user's Instagram: <strong>@" . htmlspecialchars($user_data['instagram']) . "</strong><br>";
            }
        }
    } else {
        echo "Waiting for the other user to like you back.<br>";
    }
}

// Handle Pass
elseif ($action === 'pass') {
    echo "You passed on user $liked_id.<br>";
} else {
    echo "Invalid action.";
}

echo '<br><a href="explore.php">Back to Explore</a>';
?>

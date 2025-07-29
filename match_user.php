<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id']) || !isset($_POST['target_id'])) {
    echo "Unauthorized request.";
    exit;
}

$liker_id = $_SESSION['user_id'];
$liked_id = intval($_POST['target_id']);

// Prevent self-like
if ($liker_id == $liked_id) {
    echo "Invalid match.";
    exit;
}

// Check if it's already a mutual like
$check_mutual = mysqli_query($conn, "
    SELECT * FROM likes 
    WHERE liker_id = $liked_id AND liked_id = $liker_id
");

if (mysqli_num_rows($check_mutual) > 0) {
    // Mutual match found — insert into matches
    $check_existing_match = mysqli_query($conn, "
        SELECT * FROM matches 
        WHERE (user1_id = $liker_id AND user2_id = $liked_id) 
           OR (user1_id = $liked_id AND user2_id = $liker_id)
    ");

    if (mysqli_num_rows($check_existing_match) == 0) {
        mysqli_query($conn, "
            INSERT INTO matches (user1_id, user2_id, matched_at) 
            VALUES ($liker_id, $liked_id, NOW())
        ");
    }

    // Optionally, delete both likes to keep likes table clean
    mysqli_query($conn, "
        DELETE FROM likes 
        WHERE (liker_id = $liker_id AND liked_id = $liked_id) 
           OR (liker_id = $liked_id AND liked_id = $liker_id)
    ");

    echo "🎉 It's a Match!";
} else {
    // Just store the like if not already done
    $already_liked = mysqli_query($conn, "
        SELECT * FROM likes WHERE liker_id = $liker_id AND liked_id = $liked_id
    ");
    if (mysqli_num_rows($already_liked) == 0) {
        mysqli_query($conn, "
            INSERT INTO likes (liker_id, liked_id) 
            VALUES ($liker_id, $liked_id)
        ");
    }

    echo "Like recorded.";
}
?>

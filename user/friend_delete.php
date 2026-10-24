<?php
session_start();
include "../database/dbconn.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get friend ID from URL
$friend_id = $_GET['id'];

// Get photo path before deleting
$sql = "SELECT photo FROM friends WHERE friend_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $friend_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$friend = mysqli_fetch_assoc($result);

if ($friend) {
    // Delete photo if not default
    if ($friend['photo'] && $friend['photo'] != 'default.png') {
        $photo_path = "../uploads/friends/" . $friend['photo'];
        if (file_exists($photo_path)) {
            unlink($photo_path);
        }
    }
    
    // Delete friend
    $sql = "DELETE FROM friends WHERE friend_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $friend_id, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Friend deleted successfully!');</script>";
        echo "<script>window.location.href='friends_list.php';</script>";
    } else {
        echo "<script>alert('Error deleting friend.');</script>";
        echo "<script>window.location.href='friends_list.php';</script>";
    }
} else {
    echo "<script>alert('Friend not found.');</script>";
    echo "<script>window.location.href='friends_list.php';</script>";
}
?>
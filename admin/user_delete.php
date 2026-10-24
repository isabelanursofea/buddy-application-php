<?php
// Start session & connect database
session_start();
include "../database/dbconn.php"; 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../signin.php");
    exit();
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../signin.php");
    exit();
}

// Get user ID from URL
$user_id = $_GET['id'];

// Admin cannot delete their account
if ($user_id == $_SESSION['user_id']) {
    echo "<script>alert('You cannot delete your own admin account.');</script>";
    echo "<script>window.location.href='user_list.php';</script>";
    exit();
}

// Delete selected user (from manage users page on the first column)
// Friends will also be deleted because of ON DELETE CASCADE
$sql = "DELETE FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('User deleted successfully!');</script>";
    echo "<script>window.location.href='user_list.php';</script>";
} else {
    echo "<script>alert('Error deleting user.');</script>";
    echo "<script>window.location.href='user_list.php';</script>";
}
?>
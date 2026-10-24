<?php
session_start();
include "../database/dbconn.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../signin.php");
    exit();
}

// Check admin role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../signin.php");
    exit();
}

$error = "";

// Get user id from URL
$user_id = $_GET['id'];

// Get current user details
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Update user
if (isset($_POST['update_user'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $new_password = $_POST['password'];

    // If password field is empty, keep old password
    if (empty($new_password)) {
        $update_sql = "UPDATE users 
                       SET full_name = ?, email = ?, role = ?
                       WHERE user_id = ?";

        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "sssi", $full_name, $email, $role, $user_id);
    } else {
        // No hash because lecturer did not teach hash
        $password = $new_password;

        $update_sql = "UPDATE users 
                       SET full_name = ?, email = ?, password = ?, role = ?
                       WHERE user_id = ?";

        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "ssssi", $full_name, $email, $password, $role, $user_id);
    }

    if (mysqli_stmt_execute($update_stmt)) {
        echo "<script>alert('User updated successfully!');</script>";
        echo "<script>window.location.href='user_list.php';</script>";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>


<!-- ======================================= -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User | Buddy App</title>

    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=11">
</head>

<body class="dashboard-body">

    <!-- header -->
    <?php include "../header.php"; ?>
    
    <main class="dashboard-main">
        
        <!-- Intro -->
        <section class="welcome-box">
            <h1>Edit User</h1>
            <p>Admin can update user information.</p>
        </section>
    
        <!-- Edit user form -->
        <section class="form-section">
    
            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
    
            <?php if ($user): ?>
                <form action="user_edit.php?id=<?php echo $user_id; ?>" method="post" class="admin-form">
    
                    <label>Full Name:</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
    
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    
                    <label>New Password:</label>
                    <input type="text" name="password" placeholder="Leave empty to keep old password">
    
                    <label>Role:</label>
                    <select name="role" required>
                        <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                        <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                    </select>
    
                    <!-- Submit & back button -->
                    <button type="submit" name="update_user">Update User</button>
                    <a class="back-btn" href="user_list.php">Back</a>
    
                </form>
            <?php else: ?>
                <p>User not found.</p>
                <a class="back-btn" href="user_list.php">Back</a>
            <?php endif; ?>
        </section>
    </main>
    
    <!-- footer -->
    <?php include "../footer.php"; ?>

</html>
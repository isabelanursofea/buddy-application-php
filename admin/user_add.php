<?php
// Start session & connect database
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

// Add user
if (isset($_POST['add_user'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // No hash because lecturer did not teach hash
    $role = $_POST['role'];

    // Insert new user
    $sql = "INSERT INTO users (full_name, email, password, role)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $password, $role);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('User added successfully!');</script>";
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
    <title>Add User | Buddy App</title>

    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=9">
</head>

<body class="dashboard-body">
    <!-- header -->
    <?php include "../header.php"; ?>
    
    <main class="dashboard-main">
    
        <section class="welcome-box">
            <h1>Add User</h1>
            <p>Admin can create a new user account.</p>
        </section>
    
        <!-- Add new user form -->
        <section class="form-section">
    
            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
    
            <form action="user_add.php" method="post" class="admin-form">
    
                <label>Full Name:</label>
                <input type="text" name="full_name" required>
    
                <label>Email:</label>
                <input type="email" name="email" required>
    
                <label>Password:</label>
                <input type="text" name="password" required>
    
                <label>Role:</label>
                <select name="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
    
                <!-- Submit & Back button -->
                <button type="submit" name="add_user">Add User</button>
                <a class="back-btn" href="user_list.php">Back</a>
    
            </form>
        </section>
    
    </main>
    
    <!-- footer -->
    <?php include "../footer.php"; ?>

</html>
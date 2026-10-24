<?php
session_start(); // Start session
include 'database/dbconn.php'; // Connect database

$error = ""; // Variable to show error message

// Check if user clicked 'Sign in' button
if (isset($_POST['signin'])) {
    
    // Get email and password from the form
    $email = $_POST['email'];
    $password = $_POST['pw'];

    // Search user in the database
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);

    // Check if sql statement is prepared successfully
    if ($stmt) {

        // Bind email to the ? in SQL
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check whether email exists
        if (mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_assoc($result);

            // Check password after email is found
            if ($password == $row['password']) {

                // Save login information
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['role'] = $row['role'];

                // Redirect based on role
                if ($row['role'] == 'admin') {
                    header("Location: admin/admin_dashboard.php");
                    exit();
                } else {
                    header("Location: user/user_dashboard.php");
                    exit();
                }

            } else $error = "Incorrect password.";
        } else $error = "Email not found.";
    } else $error = "Database query error: " . mysqli_error($conn);
}
?>


<!-- ======================================= -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Sign in | Buddy App </title>

    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <main class="sign-main">
        <section class="sign-container">
        
            <!-- logo -->
            <div class="sign-header">
                <img class="sign-logo" src="assets/images/logo.png" alt="Buddy App Logo">
            </div>
            
            <!-- title -->
            <div class="sign-title">
                <h1> Sign in </h1>
            </div>

            <!-- Show error message if login fails -->
            <?php if (!empty($error)): ?>
                <p style="color: red; text-align: center;">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>

            <!-- sign in form -->
            <form class="signin-form" id="signinform" action="signin.php" method="post">

                <label for="email"> Email: </label>
                <input type="email" id="email" name="email" autofocus required>

                <label for="npw"> Password: </label>
                <input type="password" id="pw" name="pw" required>

                <input type="submit" name="signin" value="Sign in">
                <input type="reset" value="Reset">
            </form>

            <div class="sign-link">
                <a href="signup.php">Don't have any account yet?</a>
            </div>

            <!-- footer -->
            <div class="sign-footer">
                <p> Disclaimer & Copyright </p>
                <p>&copy; 2026 Buddy App. All Rights Reserved. </p>
            </div>

        </section>
    </main>
</body>
</html>
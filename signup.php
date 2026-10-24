<?php 
include 'database/dbconn.php'; // Connect database
$error = ""; // Variable to show error message

// Insert new user into database
if (isset($_POST['signup'])) { 
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $npw = $_POST['npw'];
    $rpw = $_POST['rpw'];

    // Check if both password & confirm password are match
    if ($npw !== $rpw) {
        $error = "Passwords do not match";
    }
    else {
        // Hash password before saving into database
        $password = $npw;
        $role = "user";
        
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $password, $role);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                        alert('Sign up successful! Please sign in.');
                  </script>";
            echo "<script>
                        window.location.href='signin.php';
                  </script>";
        } 
        else $error = "Error: " . mysqli_error($conn);
    }
}
?>


<!-- ======================================= -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Sign up | Buddy App </title>

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
                <h1> Sign up </h1>
            </div>

            <!-- Show signup error -->
            <?php if (!empty($error)): ?>
                <p style="color: red; text-align: center;">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>

            <!-- sign up form -->
            <form class="signup-form" id="signupform" action="signup.php" method="post">

                <label for="full_name"> Full Name: </label>
                <input type="text" id="full_name" name="full_name" autofocus required>

                <label for="email"> Email: </label>
                <input type="email" id="email" name="email" placeholder="ali@gmail.com" required>

                <label for="npw"> Password: </label>
                <input type="password" id="npw" name="npw" minlength="8"
                    pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}"
                    title="At least 8 characters, uppercase, lowercase, number, special character" required>

                <p class="note">
                    *At least 8 characters, including uppercase, lowercase, digit and special characters.
                </p>

                <label for="rpw"> Re-enter password: </label>
                <input type="password" id="rpw" name="rpw" minlength="8" required>

                <input type="submit" name="signup" value="Sign up">
                <input type="reset" value="Reset">
            </form>

            <div class="sign-link">
                <a href="signin.php">Already have an account?</a>
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

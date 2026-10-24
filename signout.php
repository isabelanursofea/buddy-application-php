<?php
session_start(); // Start session before destroying it
session_unset(); // Remove all session data
session_destroy();// Destroy session
?>


<!-- ======================================= -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Sign out | Buddy App </title>

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
                <h1> You have signed out. </h1>
            </div><br><br>
            
            <!-- sign up & sign in link -->
            <div class="signout-container">
                                    
                    <a class="sign-btn" href="signin.php"> 
                        Sign in again ? 
                    </a>
                    
                    <br><br><strong>or</strong><br><br>
                        
                    <a class="sign-btn" href="signup.php">
                        Create new account ?
                    </a>
            </div>
            <br>

            <!-- footer -->
            <div class="sign-footer">
                <p> Disclaimer & Copyright </p>
                <p>&copy; 2026 Buddy App. All Rights Reserved. </p>
            </div>

        </section>
    </main>
</body>
</html>

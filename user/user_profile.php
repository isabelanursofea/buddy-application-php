<?php
session_start();
include "../database/dbconn.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Get user details
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header("Location: ../signin.php");
    exit();
}

// Update profile
if (isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate
    $has_error = false;
    
    if (empty($full_name)) {
        $error = "Full name is required!";
        $has_error = true;
    } elseif (empty($email)) {
        $error = "Email is required!";
        $has_error = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
        $has_error = true;
    }
    
    // Check if email already exists for another user
    if (!$has_error) {
        $check_sql = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "si", $email, $user_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Email already registered by another user!";
            $has_error = true;
        }
    }
    
    // Validate password if provided
    if (!$has_error && !empty($new_password)) {
        if (strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters!";
            $has_error = true;
        } elseif ($new_password !== $confirm_password) {
            $error = "Passwords do not match!";
            $has_error = true;
        }
    }
    
    // Update profile if no errors
    if (!$has_error) {
        if (!empty($new_password)) {
            $update_sql = "UPDATE users SET full_name = ?, email = ?, password = ? WHERE user_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "sssi", $full_name, $email, $new_password, $user_id);
        } else {
            $update_sql = "UPDATE users SET full_name = ?, email = ? WHERE user_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "ssi", $full_name, $email, $user_id);
        }
        
        if (mysqli_stmt_execute($update_stmt)) {
            $success = "Profile updated successfully!";
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;
            $user['full_name'] = $full_name;
            $user['email'] = $email;
        } else {
            $error = "Error updating profile: " . mysqli_error($conn);
        }
    }
}

// Get friend count
$friend_count_sql = "SELECT COUNT(*) AS total FROM friends WHERE user_id = ?";
$friend_stmt = mysqli_prepare($conn, $friend_count_sql);
mysqli_stmt_bind_param($friend_stmt, "i", $user_id);
mysqli_stmt_execute($friend_stmt);
$friend_result = mysqli_stmt_get_result($friend_stmt);
$friend_count = mysqli_fetch_assoc($friend_result)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Buddy App</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="dashboard-body">
    <!-- header -->
    <?php include "../header.php"; ?>
    
    <main class="dashboard-main">
        <section class="welcome-box">
            <h1><i class="fas fa-user-circle"></i> My Profile</h1>
            <p>View and update your profile information.</p>
        </section>

        <section class="form-section-wide">
            <?php if (!empty($error)): ?>
                <p class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></p>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <p class="success-message"><i class="fas fa-check-circle"></i> <?php echo $success; ?></p>
            <?php endif; ?>

            <form action="user_profile.php" method="post" class="admin-form">
                
                <div class="form-two-column">
                    <!-- LEFT COLUMN - PROFILE -->
                    <div class="form-left">
                        <h3><i class="fas fa-user-circle"></i> Profile</h3>
                        <hr>
                        <div class="photo-container">
                            <div class="profile-avatar-frame">
                                <?php 
                                $initial = strtoupper(substr($user['full_name'], 0, 1));
                                ?>
                                <span class="avatar-initial"><?php echo $initial; ?></span>
                            </div>
                        </div>
                        <div class="profile-info-left">
                            <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                            <span class="role-badge <?php echo $user['role']; ?>">
                                <i class="fas fa-user-shield"></i> <?php echo strtoupper($user['role']); ?>
                            </span>
                            <div class="profile-stats-mini">
                                <div class="stat-mini">
                                    <i class="fas fa-users"></i>
                                    <span class="stat-num"><?php echo $friend_count; ?></span>
                                    <span class="stat-lbl">Friends</span>
                                </div>
                                <div class="stat-divider-mini"></div>
                                <div class="stat-mini">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span class="stat-num"><?php echo date('d M Y', strtotime($user['created_at'] ?? 'now')); ?></span>
                                    <span class="stat-lbl">Joined</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- VERTICAL DIVIDER -->
                    <div class="vertical-divider"></div>

                    <!-- RIGHT COLUMN - EDIT FORM -->
                    <div class="form-right">
                        <div class="form-row-single">
                            <label><i class="fas fa-user"></i> Full Name </label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>

                        <div class="form-row-single">
                            <label><i class="fas fa-envelope"></i> Email </label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="form-row-single">
                            <label><i class="fas fa-lock"></i> New Password</label>
                            <input type="password" name="new_password" placeholder="Leave empty to keep current password">
                            <small><i class="fas fa-info-circle"></i> Minimum 6 characters</small>
                        </div>

                        <div class="form-row-single">
                            <label><i class="fas fa-check-circle"></i> Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Confirm new password">
                        </div>
                    </div>
                </div>

                <button type="submit" name="update_profile"><i class="fas fa-save"></i> Update Profile</button>
                <a class="back-btn" href="user_dashboard.php"><i class="fas fa-arrow-left"></i> Back</a>

            </form>
        </section>
    </main>
    
    <!-- footer -->
    <?php include "../footer.php"; ?>
</html>
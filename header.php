<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session
}

$role = $_SESSION['role'] ?? "";
?>


<!-- ======================================= -->


<!-- dashboard header -->
<header class="dashboard-header">
    <div class="dashboard-area">

        <!-- logo redirect to admin dashboard-->
        <?php if ($role === "admin"): ?>
        <a href="../admin/admin_dashboard.php" class="logo-box">
            <img src="../assets/images/logo.png" alt="Buddy App Logo" class="logo">
        </a>

        <!-- logo redirect to user dashboard-->
        <?php elseif ($role === "user"): ?>
        <a href="../user/user_dashboard.php" class="logo-box">
            <img src="../assets/images/logo.png" alt="Buddy App Logo" class="logo">
        </a>

        <?php endif; ?>


        <nav class="nav-box">

            <!-- navigation bar for admin -->
            <?php if ($role === "admin"): ?>
                <ul>
                    <li><a href="../admin/admin_dashboard.php">Dashboard</a></li>
                    <li><a href="../admin/user_list.php">Manage Users</a></li>
                    <li><a href="../signout.php">Sign Out</a></li>
                </ul>

            <!-- navigation bar for user -->
            <!-- marsha edit this part -->
            <?php elseif ($role === "user"): ?>
                <ul>
                    <li><a href="../user/user_dashboard.php">Dashboard</a></li>
                    <li><a href="user_profile.php">Profile</a></li>
                    <li><a href="friends_list.php">My Friend</a></li>
                    <li><a href="../signout.php">Sign Out</a></li>
                </ul>
            <?php endif; ?>
        </nav>
    </div>
</header>
<?php
// Start session and connect to database
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

// Get admin's full name for dashboard welcome
$full_name = $_SESSION['full_name'] ?? "Admin";

// Count all accounts (including admin)
$user_sql = "SELECT COUNT(*) AS total_accounts FROM users";
$user_result = mysqli_query($conn, $user_sql);
$user_row = mysqli_fetch_assoc($user_result);
$total_accounts = $user_row['total_accounts'];

// Count friends
$friend_sql = "SELECT COUNT(*) AS total_friends FROM friends";
$friend_result = mysqli_query($conn, $friend_sql);
$friend_row = mysqli_fetch_assoc($friend_result);
$total_friends = $friend_row['total_friends'];

// [Doughnut chart] User role distribution
$roleLabels = [];
$roleCounts = [];
$roleSql = "SELECT role, COUNT(*) AS total FROM users GROUP BY role";
$roleResult = mysqli_query($conn, $roleSql);

while ($row = mysqli_fetch_assoc($roleResult)) {
    $roleLabels[] = ucfirst($row['role']);
    $roleCounts[] = $row['total'];
}

// [Doughnut chart] Friend data overview excluding admin
$friendLabels = ['Users with Friends', 'Users without Friends'];
$friendCounts = [0, 0];
$friendSql = "
    SELECT 
        SUM(CASE WHEN friend_count > 0 THEN 1 ELSE 0 END) AS users_with_friends,
        SUM(CASE WHEN friend_count = 0 THEN 1 ELSE 0 END) AS users_without_friends
    FROM (
        SELECT 
            u.user_id, 
            COUNT(f.friend_id) AS friend_count 
        FROM users u
        LEFT JOIN friends f ON u.user_id = f.user_id
        WHERE u.role = 'user'
        GROUP BY u.user_id
    ) AS friend_summary
";
$friendResult = mysqli_query($conn, $friendSql);

if ($friendRow = mysqli_fetch_assoc($friendResult)) {
    $friendCounts[0] = $friendRow['users_with_friends'] ?? 0;
    $friendCounts[1] = $friendRow['users_without_friends'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Buddy App</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="dashboard-body">

    <!-- header -->
    <?php include "../header.php"; ?>
    
    <!-- dashboard body -->
    <main class="dashboard-main">
    
        <!-- Welcome admin -->
        <section class="welcome-box">
            <h1><i class="fas fa-user-shield"></i> Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
            <p>Admin Dashboard</p>
        </section>
    
        <!-- Dashboard Grid - Same style as user dashboard -->
        <section class="admin-dash-grid-modern">
            <!-- Card 1: Total Accounts -->
            <div class="admin-dash-card-modern card-accounts">
                <br><br>
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-content">
                    <span class="card-number"><?php echo $total_accounts; ?></span>
                    <span class="card-label">Total Accounts</span>
                    <p class="card-desc">Total admin and user accounts</p>
                </div>
            </div>

            <!-- Card 2: User Role Distribution -->
            <div class="admin-dash-card-modern card-chart-admin">
                <div class="card-header">
                    <i class="fas fa-user-tag"></i>
                    <span>User Role Distribution</span>
                </div>
                <div class="chart-wrapper">
                    <div class="chart-box">
                        <canvas id="roleChart"></canvas>
                    </div>
                </div>
                <div class="chart-stats">
                    <?php foreach($roleLabels as $index => $label): ?>
                        <span class="stat-badge role-<?php echo strtolower($label); ?>">
                            <i class="fas fa-user"></i> <?php echo $label; ?>: <?php echo $roleCounts[$index]; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Card 3: Total Friends -->
            <div class="admin-dash-card-modern card-friends">
                <br><br>
                <div class="card-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="card-content">
                    <span class="card-number"><?php echo $total_friends; ?></span>
                    <span class="card-label">Total Friends</span>
                    <p class="card-desc">Total friends added by users</p>
                </div>
            </div>

            <!-- Card 4: Friend Data Overview -->
            <div class="admin-dash-card-modern card-chart-admin">
                <div class="card-header">
                    <i class="fas fa-chart-pie"></i>
                    <span>Friend Data Overview</span>
                </div>
                <br>
                <div class="chart-wrapper">
                    <div class="chart-box">
                        <canvas id="friendChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    const roleLabels = <?php echo json_encode($roleLabels); ?>;
    const roleCounts = <?php echo json_encode($roleCounts); ?>;

    const friendLabels = <?php echo json_encode($friendLabels); ?>;
    const friendCounts = <?php echo json_encode($friendCounts); ?>;

    // User Role Distribution Chart
    new Chart(document.getElementById('roleChart'), {
        type: 'doughnut',
        data: {
            labels: roleLabels,
            datasets: [{
                data: roleCounts,
                backgroundColor: ['#2f6b35', '#7a551b', '#8b2f2f'],
                borderColor: '#efefef',
                borderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '50%',
            radius: '80%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 11, weight: 'bold' },
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });

    // Friend Data Overview Chart
    new Chart(document.getElementById('friendChart'), {
        type: 'doughnut',
        data: {
            labels: friendLabels,
            datasets: [{
                data: friendCounts,
                backgroundColor: ['#2196F3', '#FF5722'],
                borderColor: '#efefef',
                borderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '50%',
            radius: '105%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 11, weight: 'bold' },
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });
    </script>

    <!-- footer -->
    <?php include "../footer.php"; ?>

</html>
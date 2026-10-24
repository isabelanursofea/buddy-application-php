<?php
// user/user_dashboard.php
session_start();
include "../database/dbconn.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? "User";

// Get statistics
$error = "";

// Total friends
$sql = "SELECT COUNT(*) AS total_friends FROM friends WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total_friends = mysqli_fetch_assoc($result)['total_friends'];

// Gender statistics
$sql = "SELECT gender, COUNT(*) AS count FROM friends WHERE user_id = ? GROUP BY gender";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$gender_result = mysqli_stmt_get_result($stmt);

$male_count = 0;
$female_count = 0;
while ($row = mysqli_fetch_assoc($gender_result)) {
    if ($row['gender'] == 'Male') $male_count = $row['count'];
    if ($row['gender'] == 'Female') $female_count = $row['count'];
}

// State statistics
$sql = "SELECT address4 AS state, COUNT(*) AS count FROM friends 
        WHERE user_id = ? AND address4 IS NOT NULL AND address4 != '' 
        GROUP BY address4 ORDER BY count DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$state_result = mysqli_stmt_get_result($stmt);
$state_data = mysqli_fetch_all($state_result, MYSQLI_ASSOC);
$total_states = count($state_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Buddy App</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="dashboard-body">
    <!-- header -->
    <?php include "../header.php"; ?>
    
    <main class="dashboard-main">
        <!-- Welcome user -->
        <section class="welcome-box">
            <h1><i class="fas fa-user-circle"></i> Welcome Back, <?php echo htmlspecialchars($full_name); ?>!</h1>
            <p>User Dashboard</p>
        </section>

        <!-- Dashboard Grid -->
        <section class="user-dash-grid-modern">
            <!-- Card 1: Total Friends -->
            <div class="user-dash-card-modern card-total">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-content">
                    <span class="card-number"><?php echo $total_friends; ?></span>
                    <span class="card-label">Total Friends</span>
                    <p class="card-desc">Total friends saved in your list.</p>
                </div>
                <a href="friends_list.php" class="card-link">
                    <i class="fas fa-arrow-right"></i> View All
                </a>
            </div>

            <!-- Card 2: Gender Distribution -->
            <div class="user-dash-card-modern card-chart">
                <div class="card-header">
                    <i class="fas fa-venus-mars"></i>
                    <span>Gender Distribution</span>
                </div>
                <div class="chart-wrapper">
                    <div class="chart-box">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
                <div class="chart-stats">
                    <span class="stat-badge male"><i class="fas fa-mars"></i> <?php echo $male_count; ?></span>
                    <span class="stat-badge female"><i class="fas fa-venus"></i> <?php echo $female_count; ?></span>
                </div>
            </div>

            <!-- Card 3: State Distribution -->
            <div class="user-dash-card-modern card-chart">
                <div class="card-header">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>State Distribution</span>
                </div>
                <div class="chart-wrapper">
                    <div class="chart-box">
                        <canvas id="stateChart"></canvas>
                    </div>
                </div>
                <div class="chart-stats">
                    <span class="stat-badge state"><i class="fas fa-map-pin"></i> <?php echo $total_states; ?> States</span>
                </div>
            </div>

            <!-- Card 4: Reports -->
            <div class="user-dash-card-modern card-reports">
                <div class="card-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="card-content">
                    <span class="card-label" style="font-size:20px; font-weight:bold;">Reports</span>
                    <p class="card-desc">View and generate detailed reports of your friends data.</p>
                </div>
                <a href="user_reports.php" class="card-link primary">
                    <i class="fas fa-chart-bar"></i> View Reports
                </a>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    // Gender Distribution Chart
    const genderLabels = ['Male', 'Female'];
    const genderCounts = [<?php echo $male_count; ?>, <?php echo $female_count; ?>];

    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: genderLabels,
            datasets: [{
                data: genderCounts,
                backgroundColor: ['#0241ff', '#ff2222'],
                borderColor: '#efefef',
                borderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
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

    // State Distribution Chart
    <?php if(!empty($state_data)): ?>
    const stateLabels = <?php echo json_encode(array_column($state_data, 'state')); ?>;
    const stateCounts = <?php echo json_encode(array_column($state_data, 'count')); ?>;

    const stateColors = [
        '#2f6b35', '#8b2f2f', '#7a551b', '#3f3424',
        '#2196F3', '#FF5722', '#4CAF50', '#9C27B0',
        '#FF9800', '#00BCD4', '#E91E63', '#8BC34A',
        '#795548', '#607D8B', '#FFEB3B', '#9E9E9E'
    ];

    new Chart(document.getElementById('stateChart'), {
        type: 'doughnut',
        data: {
            labels: stateLabels,
            datasets: [{
                data: stateCounts,
                backgroundColor: stateColors.slice(0, stateLabels.length),
                borderColor: '#efefef',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
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
    <?php endif; ?>
    </script>

    <!-- footer -->
    <?php include "../footer.php"; ?>
</html>
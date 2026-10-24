<?php
session_start();
include "../database/dbconn.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$friend_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($friend_id <= 0) {
    header("Location: friends_list.php");
    exit();
}

// Get friend details
$sql = "SELECT * FROM friends WHERE friend_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $friend_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$friend = mysqli_fetch_assoc($result);

if (!$friend) {
    header("Location: friends_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Friend | Buddy App</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="dashboard-body">
    <!-- header -->
    <?php include "../header.php"; ?>
    
    <main class="dashboard-main">
        <section class="welcome-box">
            <h1><i class="fas fa-user-circle"></i> Friend Details</h1>
            <p>View complete information about your friend.</p>
        </section>

        <section class="view-friend-container">
            <div class="view-friend-grid">
                <!-- Left Column - Profile Card -->
                <div class="view-friend-profile">
                    <div class="profile-card-view">
                        <div class="profile-image-frame">
                            <?php if ($friend['photo'] && $friend['photo'] != 'default.png'): ?>
                                <img src="../uploads/friends/<?php echo htmlspecialchars($friend['photo']); ?>" 
                                     class="profile-image-view" alt="Photo">
                            <?php else: ?>
                                <div class="profile-image-placeholder">
                                    <span><?php echo strtoupper(substr($friend['friend_name'], 0, 1)); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h2><?php echo htmlspecialchars($friend['friend_name']); ?></h2>
                        <span class="gender-badge-view <?php echo $friend['gender'] == 'Male' ? 'male' : 'female'; ?>">
                            <i class="fas <?php echo $friend['gender'] == 'Male' ? 'fa-mars' : 'fa-venus'; ?>"></i>
                            <?php echo htmlspecialchars($friend['gender']); ?>
                        </span>
                        <div class="quick-stats">
                            <div class="quick-stat">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($friend['phone_no']); ?></span>
                            </div>
                            <div class="quick-stat">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo htmlspecialchars($friend['email']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Details -->
                <div class="view-friend-details">
                    <div class="details-header">
                        <h3><i class="fas fa-info-circle"></i> Personal Information</h3>
                    </div>
                    
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-user"></i> Name</span>
                            <span class="detail-value"><?php echo htmlspecialchars($friend['friend_name']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-venus-mars"></i> Gender</span>
                            <span class="detail-value"><?php echo htmlspecialchars($friend['gender']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-phone"></i> Phone Number</span>
                            <span class="detail-value"><?php echo htmlspecialchars($friend['phone_no']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-envelope"></i> Email</span>
                            <span class="detail-value"><?php echo htmlspecialchars($friend['email']); ?></span>
                        </div>
                    </div>

                    <div class="details-header" style="margin-top: 20px;">
                        <h3><i class="fas fa-map-marker-alt"></i> Address Details</h3>
                    </div>

                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-home"></i> Address 1</span>
                            <span class="detail-value"><?php echo htmlspecialchars($friend['address1'] ?? '-'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-road"></i> Address 2</span>
                            <span class="detail-value"><?php echo htmlspecialchars($friend['address2'] ?? '-'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-mailbox"></i> Address 3</span>
                            <span class="detail-value"><?php echo htmlspecialchars($friend['address3'] ?? '-'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-flag"></i> State</span>
                            <span class="detail-value"><?php echo htmlspecialchars($friend['address4'] ?? '-'); ?></span>
                        </div>
                        <div class="detail-item full-width">
                            <span class="detail-label"><i class="fas fa-map-pin"></i> Full Address</span>
                            <span class="detail-value">
                                <?php 
                                $address = array_filter([
                                    $friend['address1'] ?? '',
                                    $friend['address2'] ?? '',
                                    $friend['address3'] ?? '',
                                    $friend['address4'] ?? ''
                                ]);
                                echo !empty($address) ? htmlspecialchars(implode(', ', $address)) : '<span class="empty-value">No address provided</span>';
                                ?>
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="view-friend-actions">
                        <a href="friend_edit.php?id=<?php echo $friend['friend_id']; ?>" class="btn-action edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="friend_delete.php?id=<?php echo $friend['friend_id']; ?>" class="btn-action delete" 
                           onclick="return confirm('Are you sure you want to delete this friend?');">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                        <a href="friends_list.php" class="btn-action back">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <!-- footer -->
    <?php include "../footer.php"; ?>
</html>
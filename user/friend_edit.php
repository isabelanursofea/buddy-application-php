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

$error = "";

// Update friend
if (isset($_POST['update_friend'])) {
    $friend_name = trim($_POST['friend_name']);
    $gender = $_POST['gender'];
    $phone_no = trim($_POST['phone_no']);
    $email = trim($_POST['email']);
    $address1 = trim($_POST['address1']);
    $address2 = trim($_POST['address2']);
    $address3 = trim($_POST['address3']);
    $address4 = trim($_POST['address4']);
    
    // Validate
    if (empty($friend_name) || empty($gender) || empty($phone_no) || empty($email)) {
        $error = "Name, Gender, Phone, and Email are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        // Handle photo upload
        $photo = $friend['photo'];
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $upload_dir = "../uploads/friends/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $photo = time() . '_' . basename($_FILES['photo']['name']);
                move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo);
                
                // Delete old photo
                if ($friend['photo'] && $friend['photo'] != 'default.png') {
                    $old_path = $upload_dir . $friend['photo'];
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
            } else {
                $error = "Invalid file type. Allowed: JPG, PNG, GIF";
            }
        }
        
        if (empty($error)) {
            $sql = "UPDATE friends SET photo = ?, friend_name = ?, gender = ?, phone_no = ?, email = ?, 
                    address1 = ?, address2 = ?, address3 = ?, address4 = ? 
                    WHERE friend_id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssssssii", $photo, $friend_name, $gender, $phone_no, $email, 
                                   $address1, $address2, $address3, $address4, $friend_id, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Friend updated successfully!');</script>";
                echo "<script>window.location.href='friend_view.php?id=$friend_id';</script>";
                exit();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Friend | Buddy App</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="dashboard-body">
    <!-- header -->
    <?php include "../header.php"; ?>
    
    <main class="dashboard-main">
        <section class="welcome-box">
            <h1><i class="fas fa-user-edit"></i> Edit Friend</h1>
            <p>Update your friend's information.</p>
        </section>

        <section class="edit-friend-container">
            <?php if (!empty($error)): ?>
                <div class="error-box">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="friend_edit.php?id=<?php echo $friend_id; ?>" method="post" enctype="multipart/form-data">
                
                <div class="edit-friend-grid">
                    <!-- LEFT COLUMN - PHOTO -->
                    <div class="edit-photo-section">
                        <div class="photo-card">
                            <div class="photo-frame">
                                <img src="../uploads/friends/<?php echo htmlspecialchars($friend['photo'] ?? 'default.png'); ?>" 
                                     class="friend-avatar-big" alt="Photo">
                            </div>
                            <div class="photo-upload-area">
                                <label for="photo" class="upload-btn">
                                    <i class="fas fa-camera"></i> Change Photo
                                </label>
                                <input type="file" id="photo" name="photo" accept="image/*" style="display:none;">
                                <small><i class="fas fa-info-circle"></i> Leave empty to keep current photo</small>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN - FORM -->
                    <div class="edit-form-section">
                        <div class="form-grid">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Friend Name *</label>
                                <input type="text" name="friend_name" value="<?php echo htmlspecialchars($friend['friend_name']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-venus-mars"></i> Gender *</label>
                                <select name="gender" required>
                                    <option value="Male" <?php echo $friend['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo $friend['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Phone No. *</label>
                                <input type="tel" name="phone_no" value="<?php echo htmlspecialchars($friend['phone_no']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email *</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($friend['email']); ?>" required>
                            </div>
                        </div>

                        <!-- Address Section -->
                        <div class="address-section">
                            <h4><i class="fas fa-map-marker-alt"></i> Address Details</h4>
                            <div class="address-grid">
                                <div class="form-group">
                                    <label>Address 1 <span>(House/Lot No, Floor, Building)</span></label>
                                    <input type="text" name="address1" value="<?php echo htmlspecialchars($friend['address1'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label>Address 2 <span>(Street/District)</span></label>
                                    <input type="text" name="address2" value="<?php echo htmlspecialchars($friend['address2'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label>Address 3 <span>(Postcode and Locality)</span></label>
                                    <input type="text" name="address3" value="<?php echo htmlspecialchars($friend['address3'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label>Address 4 <span>(State)</span></label>
                                    <input type="text" name="address4" value="<?php echo htmlspecialchars($friend['address4'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="update_friend" class="btn-update">
                                <i class="fas fa-save"></i> Update Friend
                            </button>
                            <a href="friend_view.php?id=<?php echo $friend_id; ?>" class="btn-back">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </main>
    
    <!-- footer -->
    <?php include "../footer.php"; ?>
</html>
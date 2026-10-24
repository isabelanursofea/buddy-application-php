<?php
// user/friend_add.php
session_start();
include "../database/dbconn.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = "";

// Add friend
if (isset($_POST['add_friend'])) {
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
        $photo = 'default.png';
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
            } else {
                $error = "Invalid file type. Allowed: JPG, PNG, GIF";
            }
        }
        
        if (empty($error)) {
            $sql = "INSERT INTO friends (user_id, photo, friend_name, gender, phone_no, email, address1, address2, address3, address4) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "isssssssss", $user_id, $photo, $friend_name, $gender, $phone_no, $email, 
                                   $address1, $address2, $address3, $address4);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Friend added successfully!');</script>";
                echo "<script>window.location.href='friends_list.php';</script>";
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
    <title>Add Friend | Buddy App</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="dashboard-body">
    <!-- header -->
    <?php include "../header.php"; ?>
    
    <main class="dashboard-main">
        <section class="welcome-box">
            <h1><i class="fas fa-user-plus"></i> Add New Friend</h1>
            <p>Fill in the details to add a friend.</p>
        </section>

        <section class="add-friend-container">
            <?php if (!empty($error)): ?>
                <div class="error-box">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="friend_add.php" method="post" enctype="multipart/form-data" class="admin-form">
                
                <!-- Photo & Friend Name -->
                <div class="form-row">
                    <div class="form-col">
                        <label><i class="fas fa-camera"></i> Photo:</label>
                        <div class="photo-upload-wrapper">
                            <div class="photo-preview-add">
                                <img src="../uploads/friends/default.png" id="photoPreview" alt="Photo Preview">
                            </div>
                            <div class="file-input-wrapper">
                                <label for="photo" class="file-label">
                                    <i class="fas fa-upload"></i> Choose File
                                </label>
                                <input type="file" name="photo" id="photo" accept="image/*" onchange="previewPhoto(event)">
                                <small><i class="fas fa-info-circle"></i> JPG, PNG, GIF (Max 2MB)</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-col">
                        <label><i class="fas fa-user"></i> Friend Name *:</label>
                        <input type="text" name="friend_name" placeholder="Enter friend's name" required>
                    </div>
                </div>

                <!-- Gender & Phone -->
                <div class="form-row">
                    <div class="form-col">
                        <label><i class="fas fa-venus-mars"></i> Gender *:</label>
                        <select name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label><i class="fas fa-phone"></i> Phone No. *:</label>
                        <input type="tel" name="phone_no" placeholder="Enter phone number" required>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email *:</label>
                    <input type="email" name="email" placeholder="Enter email address" required>
                </div>

                <!-- Address Details -->
                <div class="address-section-add">
                    <h3><i class="fas fa-map-marker-alt"></i> Address Details</h3>
                    <hr>

                    <div class="form-group">
                        <label>Address 1 <span>(House/Lot No, Floor, Building):</span></label>
                        <input type="text" name="address1" placeholder="e.g., No. 66, Level 2">
                    </div>

                    <div class="form-group">
                        <label>Address 2 <span>(Street/District):</span></label>
                        <input type="text" name="address2" placeholder="e.g., Jalan Kenari">
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <label>Address 3 <span>(Postcode and Locality):</span></label>
                            <input type="text" name="address3" placeholder="e.g., 43000, Kajang">
                        </div>
                        <div class="form-col">
                            <label>Address 4 <span>(State):</span></label>
                            <input type="text" name="address4" placeholder="e.g., Selangor">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions-add">
                    <button type="submit" name="add_friend" class="btn-add">
                        <i class="fas fa-user-plus"></i> Add Friend
                    </button>
                    <a href="friends_list.php" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

            </form>
        </section>
    </main>

    <script>
    function previewPhoto(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('photoPreview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>
    
    <!-- footer -->
    <?php include "../footer.php"; ?>
</html>
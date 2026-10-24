<?php
session_start();
include "../database/dbconn.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../signin.php");
    exit();
}

// Check admin role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../signin.php");
    exit();
}

// Get user id from URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get user details
$user_sql = "SELECT * FROM users WHERE user_id = ?";
$user_stmt = mysqli_prepare($conn, $user_sql);
mysqli_stmt_bind_param($user_stmt, "i", $user_id);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user = mysqli_fetch_assoc($user_result);

// Count user's friends
$count_sql = "SELECT COUNT(*) AS total_friends FROM friends WHERE user_id = ?";
$count_stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($count_stmt, "i", $user_id);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count_row = mysqli_fetch_assoc($count_result);
$total_friends = $count_row['total_friends'];

// Pagination for friends table
$friends_per_page = 2;
$friend_page = isset($_GET['friend_page']) ? intval($_GET['friend_page']) : 1;

if ($friend_page < 1) {
    $friend_page = 1;
}

$total_pages = ceil($total_friends / $friends_per_page);

if ($total_pages < 1) {
    $total_pages = 1;
}

if ($friend_page > $total_pages) {
    $friend_page = $total_pages;
}

$offset = ($friend_page - 1) * $friends_per_page;

// Get sort column and order from URL
$sort = $_GET['sort'] ?? "friend_id";
$order = $_GET['order'] ?? "DESC";

// Only allow these columns to be sorted
$allowed_sort_columns = [
    "friend_id",
    "friend_name",
    "photo",
    "gender",
    "phone_no",
    "email",
    "address1",
    "address2",
    "address3",
    "address4"
];

// If sort column is invalid, use friend_id
if (!in_array($sort, $allowed_sort_columns)) {
    $sort = "friend_id";
}

// Only allow ASC or DESC
if ($order !== "ASC" && $order !== "DESC") {
    $order = "DESC";
}

// Function to create sortable friend table header link
function friend_sort_link($column, $label, $current_sort, $current_order, $user_id) {

    // If current column is clicked, change order
    if ($current_sort == $column && $current_order == "ASC") {
        $next_order = "DESC";
    } else {
        $next_order = "ASC";
    }

    // Show arrow beside active column
    $arrow = "";
    if ($current_sort == $column) {
        $arrow = ($current_order == "ASC") ? " ▲" : " ▼";
    }

    return '<a class="sort-link" href="user_view.php?id=' . $user_id . 
           '&sort=' . $column . 
           '&order=' . $next_order . 
           '&friend_page=1">' . $label . $arrow . '</a>';
}

// Get user's friends with pagination and sorting
$friend_sql = "SELECT * FROM friends 
               WHERE user_id = ? 
               ORDER BY $sort $order, friend_id DESC
               LIMIT ? OFFSET ?";

$friend_stmt = mysqli_prepare($conn, $friend_sql);
mysqli_stmt_bind_param($friend_stmt, "iii", $user_id, $friends_per_page, $offset);
mysqli_stmt_execute($friend_stmt);
$friend_result = mysqli_stmt_get_result($friend_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View User | Buddy App</title>

    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=13">
</head>

<body class="dashboard-body">

    <!-- header -->
    <?php include "../header.php"; ?>
    
    <main class="dashboard-main">
        
        <!-- intro -->
        <section class="welcome-box">
            <h1>View User</h1>
            <p>Details of selected user.</p>
        </section>
    
        <!-- User details -->
        <?php if ($user): ?>
            <section class="details-section">
                <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                <p><strong>User ID:</strong> <?php echo $user['user_id']; ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Role:</strong> <?php echo $user['role']; ?></p>
                <p><strong>Total Friends:</strong> <?php echo $total_friends; ?></p>
                <br>
                
                <!-- Submit & back button -->
                <a class="dashboard-btn" href="user_edit.php?id=<?php echo $user['user_id']; ?>">Edit User</a>
                <a class="back-btn" href="user_list.php">Back</a>
            </section>

            <br>
    
            <!-- Display users' friends -->
            <section class="table-section dashboard-gap">
                <h2>User's Friends</h2><br>
    
                <table class="admin-table">
                    <tr>
                        <th><?php echo friend_sort_link("friend_id", "Friend ID", $sort, $order, $user_id); ?></th>
                        <th><?php echo friend_sort_link("friend_name", "Friend Name", $sort, $order, $user_id); ?></th>
                        <th><?php echo friend_sort_link("photo", "Photo", $sort, $order, $user_id); ?></th>
                        <th><?php echo friend_sort_link("gender", "Gender", $sort, $order, $user_id); ?></th>
                        <th><?php echo friend_sort_link("phone_no", "Phone No", $sort, $order, $user_id); ?></th>
                        <th><?php echo friend_sort_link("email", "Email", $sort, $order, $user_id); ?></th>
                        <th><?php echo friend_sort_link("address1", "Address 1", $sort, $order, $user_id); ?></th>
                        <th><?php echo friend_sort_link("address2", "Address 2", $sort, $order, $user_id); ?></th>
                        <th><?php echo friend_sort_link("address3", "Address 3", $sort, $order, $user_id); ?></th>
                        <th><?php echo friend_sort_link("address4", "Address 4", $sort, $order, $user_id); ?></th>
                    </tr>
                    
                    <?php if (mysqli_num_rows($friend_result) > 0): ?>
                        <?php while ($friend = mysqli_fetch_assoc($friend_result)): ?>
                            <tr>
                                <td><?php echo $friend['friend_id']; ?></td>
                                <td><?php echo htmlspecialchars($friend['friend_name']); ?></td>
                                <td>
                                    <!-- Display friends photo-->
                                    <?php if (!empty($friend['photo'])): ?>
                                        <img src="../uploads/friends/<?php echo htmlspecialchars($friend['photo']); ?>" alt="Friend Photo" class="friend-photo">
                                    <!-- If no friend photo available -->
                                    <?php else: ?> No photo
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $friend['gender']; ?></td>
                                <td><?php echo htmlspecialchars($friend['phone_no']); ?></td>
                                <td><?php echo htmlspecialchars($friend['email']); ?></td>
                                <td><?php echo htmlspecialchars($friend['address1']); ?></td>
                                <td><?php echo htmlspecialchars($friend['address2']); ?></td>
                                <td><?php echo htmlspecialchars($friend['address3']); ?></td>
                                <td><?php echo htmlspecialchars($friend['address4']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="10">This user has no friends yet.</td></tr>
                    <?php endif; ?>
                </table>
    
                <!-- Pagination-->
                <?php if ($total_friends > $friends_per_page): ?>
                    <div class="pagination-box">
                        <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                            <a 
                                href="user_view.php?id=<?php echo $user_id; ?>&friend_page=<?php echo $page; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>"
                                class="<?php echo ($page == $friend_page) ? 'active-page' : ''; ?>"
                            >
                                <?php echo $page; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
    
            </section>
    
        <?php else: ?>
            <section class="details-section">                    
                <p>User not found.</p>
                <a class="back-btn" href="user_list.php">Back</a>
            </section>

        <?php endif; ?>
    </main>
    
    <!-- footer -->
    <?php include "../footer.php"; ?>
    
</html>
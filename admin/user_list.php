<?php
session_start();
include "../database/dbconn.php"; // Connect database

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

// Select to delete multiple users
if (isset($_POST['delete_selected'])) {

    if (!empty($_POST['selected_users'])) {

        $selected_users = array_map('intval', $_POST['selected_users']);
        $current_admin_id = $_SESSION['user_id'];

        // Prevent admin from deleting own account
        $selected_users = array_filter($selected_users, function($id) use ($current_admin_id) {
            return $id != $current_admin_id;
        });

        if (!empty($selected_users)) {
            $ids = implode(",", $selected_users);

            // Friends will also be deleted because of ON DELETE CASCADE
            $delete_sql = "DELETE FROM users WHERE user_id IN ($ids)";

            if (mysqli_query($conn, $delete_sql)) {
                echo "<script>alert('Selected users deleted successfully!');</script>";
                echo "<script>window.location.href='user_list.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error deleting users.');</script>";
            }

        } else {
            echo "<script>alert('You cannot delete your own admin account.');</script>";
        }

    } else {
        echo "<script>alert('Please select at least one user to delete.');</script>";
    }
}


// To search / To sort / To page the records
$limit = 5; // 5 records per page
$keyword = $_GET['keyword'] ?? ""; // Get search keyword
$search = "%" . $keyword . "%";

// Get sort column and order from URL
$sort = $_GET['sort'] ?? "total_friends";
$order = $_GET['order'] ?? "DESC";

// Only allow these columns to be sorted
$allowed_sort_columns = [
    "user_id",
    "full_name",
    "email",
    "role",
    "total_friends",
];

// If sort column is invalid, use user_id
if (!in_array($sort, $allowed_sort_columns)) {
    $sort = "user_id";
}

// Only allow ASC or DESC
if ($order !== "ASC" && $order !== "DESC") {
    $order = "ASC";
}

// Function to create sortable table header link
function sort_link($column, $label, $current_sort, $current_order, $keyword) {

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

    return '<a class="sort-link" href="user_list.php?sort=' . $column . 
           '&order=' . $next_order . 
           '&keyword=' . urlencode($keyword) . 
           '">' . $label . $arrow . '</a>';
}

// Count total users based on search
$count_sql = "SELECT COUNT(*) AS total_rows 
              FROM users 
              WHERE full_name LIKE ? OR email LIKE ?";

$count_stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($count_stmt, "ss", $search, $search);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count_row = mysqli_fetch_assoc($count_result);

$total_rows = $count_row['total_rows'];
$total_pages = ceil($total_rows / $limit);

// Get current page number
if (!isset($_GET['page'])) {
    $page_number = 1;
} else {
    $page_number = (int)$_GET['page'];
}

// Prevent invalid page number
if ($page_number < 1) {
    $page_number = 1;
}

$initial_page = ($page_number - 1) * $limit;


// To display users' friends count
$sql = "SELECT 
            users.user_id,
            users.full_name,
            users.email,
            users.role,
            COUNT(friends.friend_id) AS total_friends
        FROM users
        LEFT JOIN friends 
        ON users.user_id = friends.user_id
        WHERE users.full_name LIKE ? OR users.email LIKE ?
        GROUP BY 
            users.user_id,
            users.full_name,
            users.email,
            users.role
        ORDER BY $sort $order, users.user_id ASC
        LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssii", $search, $search, $limit, $initial_page);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>


<!-- ======================================= -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | Buddy App</title>

    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=10">
</head>

<body class="dashboard-body">

<!-- header -->
<?php include "../header.php"; ?>

<main class="dashboard-main">

    <section class="welcome-box">
        <h1>Manage Users</h1>
        <p>Search, view, add, edit, delete, and sort users.</p>
    </section>

    <div class="admin-action-row">

        <a class="dashboard-btn" href="user_add.php">+ Add User</a>

        <form class="search-form" action="user_list.php" method="get">
            <input 
                type="text" 
                name="keyword" 
                placeholder="Search name or email"
                value="<?php echo htmlspecialchars($keyword); ?>"
            >

            <!-- Keep current sorting when searching -->
            <input type="hidden" name="sort" value="<?php echo $sort; ?>">
            <input type="hidden" name="order" value="<?php echo $order; ?>">

            <!-- Search & Reset -->
            <button type="submit">Search</button>
            <a class="back-btn" href="user_list.php">Reset</a>
        </form>

    </div>

    <section class="table-section">
        <form action="user_list.php" method="post">
            <table class="admin-table">
                <tr>
                    <th>Select</th>
                    <th><?php echo sort_link("user_id", "User ID", $sort, $order, $keyword); ?></th>
                    <th><?php echo sort_link("full_name", "Full Name", $sort, $order, $keyword); ?></th>
                    <th><?php echo sort_link("email", "Email", $sort, $order, $keyword); ?></th>
                    <th><?php echo sort_link("role", "Role", $sort, $order, $keyword); ?></th>
                    <th><?php echo sort_link("total_friends", "Total Friends", $sort, $order, $keyword); ?></th>
                    <th>Action</th>
                </tr>

                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <input 
                                    type="checkbox" 
                                    name="selected_users[]" 
                                    value="<?php echo $row['user_id']; ?>"
                                >
                            </td>

                            <td><?php echo $row['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo $row['role']; ?></td>
                            <td>
                                <?php 
                                if ($row['role'] == 'admin') echo "-";
                                else echo $row['total_friends'];
                                ?>
                            </td>

                            <td>
                                <a class="small-btn" href="user_view.php?id=<?php echo $row['user_id']; ?>">
                                    View
                                </a>

                                <a class="small-btn" href="user_edit.php?id=<?php echo $row['user_id']; ?>">
                                    Edit
                                </a>

                                <a 
                                    class="small-btn delete-btn"
                                    href="user_delete.php?id=<?php echo $row['user_id']; ?>"
                                    onclick="return confirm('Are you sure you want to delete this user?');"
                                >
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="7">No users found.</td>
                    </tr>
                <?php endif; ?>

            </table>

            <br>

            <button 
                type="submit" 
                name="delete_selected" 
                class="delete-selected-btn"
                onclick="return confirm('Are you sure you want to delete selected users?');"
            >
                Delete Selected Users
            </button>

        </form>

    </section>


    <!-- Pagination -->
    <div class="pagination-box">

        <?php if ($total_pages > 1): ?>

            <?php for ($page = 1; $page <= $total_pages; $page++): ?>

                <a class="<?php echo ($page == $page_number) ? 'active-page' : ''; ?>"
                   href="user_list.php?page=<?php echo $page; ?>&keyword=<?php echo urlencode($keyword); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">
                    <?php echo $page; ?>
                </a>

            <?php endfor; ?>
        <?php endif; ?>
    </div>
    </main>

    <!-- footer -->
    <?php include "../footer.php"; ?>
    
</html>
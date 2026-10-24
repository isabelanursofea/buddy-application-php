<?php
session_start();
include "../database/dbconn.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Delete selected friends
if (isset($_POST['delete_selected'])) {
    if (!empty($_POST['selected_friends'])) {
        $selected_friends = array_map('intval', $_POST['selected_friends']);
        
        if (!empty($selected_friends)) {
            $ids = implode(",", $selected_friends);
            
            // Get photo paths before deleting
            $photo_sql = "SELECT photo FROM friends WHERE friend_id IN ($ids) AND user_id = $user_id";
            $photo_result = mysqli_query($conn, $photo_sql);
            while ($photo_row = mysqli_fetch_assoc($photo_result)) {
                if ($photo_row['photo'] && $photo_row['photo'] != 'default.png') {
                    $photo_path = "../uploads/friends/" . $photo_row['photo'];
                    if (file_exists($photo_path)) {
                        unlink($photo_path);
                    }
                }
            }
            
            // Delete friends
            $delete_sql = "DELETE FROM friends WHERE friend_id IN ($ids) AND user_id = $user_id";
            if (mysqli_query($conn, $delete_sql)) {
                echo "<script>alert('Selected friends deleted successfully!');</script>";
                echo "<script>window.location.href='friends_list.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error deleting friends.');</script>";
            }
        } else {
            echo "<script>alert('You cannot delete your own friends.');</script>";
        }
    } else {
        echo "<script>alert('Please select at least one friend to delete.');</script>";
    }
}

// Search, Sort, Pagination
$limit = 2; // 2 records per page
$keyword = $_GET['keyword'] ?? "";
$search = "%" . $keyword . "%";

$sort = $_GET['sort'] ?? "friend_id";
$order = $_GET['order'] ?? "ASC";

$allowed_sort_columns = [
    "friend_id",
    "friend_name",
    "gender",
    "phone_no",
    "email",
    "address4"
];

if (!in_array($sort, $allowed_sort_columns)) {
    $sort = "friend_id";
}

if ($order !== "ASC" && $order !== "DESC") {
    $order = "ASC";
}

function sort_link($column, $label, $current_sort, $current_order, $keyword) {
    if ($current_sort == $column && $current_order == "ASC") {
        $next_order = "DESC";
    } else {
        $next_order = "ASC";
    }

    $arrow = "";
    if ($current_sort == $column) {
        $arrow = ($current_order == "ASC") ? " ▲" : " ▼";
    }

    return '<a class="sort-link" href="friends_list.php?sort=' . $column . 
           '&order=' . $next_order . 
           '&keyword=' . urlencode($keyword) . 
           '">' . $label . $arrow . '</a>';
}

// Count total
$count_sql = "SELECT COUNT(*) AS total_rows 
              FROM friends 
              WHERE user_id = ? AND (friend_name LIKE ? OR email LIKE ? OR phone_no LIKE ?)";

$count_stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($count_stmt, "isss", $user_id, $search, $search, $search);
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
if ($page_number > $total_pages && $total_pages > 0) {
    $page_number = $total_pages;
}

$initial_page = ($page_number - 1) * $limit;

// Get friends data
$sql = "SELECT * FROM friends 
        WHERE user_id = ? AND (friend_name LIKE ? OR email LIKE ? OR phone_no LIKE ?)
        ORDER BY $sort $order
        LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isssii", $user_id, $search, $search, $search, $limit, $initial_page);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Friends List | Buddy App</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=9">
</head>

<body class="dashboard-body">
    <!-- header -->
    <?php include "../header.php"; ?>
    
    <main class="dashboard-main">
        <section class="welcome-box">
            <h1>Manage Friends</h1>
            <p>Search, view, add, edit, delete, and sort friends.</p>
        </section>

        <div class="admin-action-row">
            <a class="dashboard-btn" href="friend_add.php">+ Add Friend</a>

            <form class="search-form" action="friends_list.php" method="get">
                <input 
                    type="text" 
                    name="keyword" 
                    placeholder="Search name, email, or phone"
                    value="<?php echo htmlspecialchars($keyword); ?>"
                >
                <input type="hidden" name="sort" value="<?php echo $sort; ?>">
                <input type="hidden" name="order" value="<?php echo $order; ?>">
                <button type="submit">Search</button>
                <a class="back-btn" href="friends_list.php">Reset</a>
            </form>
        </div>

        <section class="table-section">
            <form action="friends_list.php" method="post">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th><?php echo sort_link("friend_id", "Friend ID", $sort, $order, $keyword); ?></th>
                            <th>Photo</th>
                            <th><?php echo sort_link("friend_name", "Friend Name", $sort, $order, $keyword); ?></th>
                            <th><?php echo sort_link("gender", "Gender", $sort, $order, $keyword); ?></th>
                            <th><?php echo sort_link("phone_no", "Phone No", $sort, $order, $keyword); ?></th>
                            <th><?php echo sort_link("email", "Email", $sort, $order, $keyword); ?></th>
                            <th><?php echo sort_link("address4", "State", $sort, $order, $keyword); ?></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <input 
                                            type="checkbox" 
                                            name="selected_friends[]" 
                                            value="<?php echo $row['friend_id']; ?>"
                                        >
                                    </td>
                                    <td><?php echo $row['friend_id']; ?></td>
                                    <td>
                                        <img src="../uploads/friends/<?php echo htmlspecialchars($row['photo'] ?? 'default.png'); ?>" 
                                             class="friend-photo" alt="Photo">
                                    </td>
                                    <td><?php echo htmlspecialchars($row['friend_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone_no']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['address4'] ?? '-'); ?></td>
                                    <td>
                                        <a class="small-btn" href="friend_view.php?id=<?php echo $row['friend_id']; ?>">
                                            View
                                        </a>
                                        <a class="small-btn" href="friend_edit.php?id=<?php echo $row['friend_id']; ?>">
                                            Edit
                                        </a>
                                        <a 
                                            class="small-btn delete-btn"
                                            href="friend_delete.php?id=<?php echo $row['friend_id']; ?>"
                                            onclick="return confirm('Are you sure you want to delete this friend?');"
                                        >
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9">No friends found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <br>

                <button 
                    type="submit" 
                    name="delete_selected" 
                    class="delete-selected-btn"
                    onclick="return confirm('Are you sure you want to delete selected friends?');"
                >
                    Delete Selected Friends
                </button>
            </form>
        </section>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-box">
                <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                    <a class="<?php echo ($page == $page_number) ? 'active-page' : ''; ?>"
                       href="friends_list.php?page=<?php echo $page; ?>&keyword=<?php echo urlencode($keyword); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">
                        <?php echo $page; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- footer -->
    <?php include "../footer.php"; ?>
</html>
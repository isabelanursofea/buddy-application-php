<?php
session_start();
include "../database/dbconn.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$search_gender = isset($_GET['search_gender']) ? trim($_GET['search_gender']) : '';
$search_state = isset($_GET['search_state']) ? trim($_GET['search_state']) : '';

// Build relational logic dynamically
$query = "SELECT * FROM friends WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if ($search_name !== '') {
    $query .= " AND name LIKE ?";
    $params[] = "%" . $search_name . "%";
    $types .= "s";
}
if ($search_gender !== '') {
    $query .= " AND gender = ?";
    $params[] = $search_gender;
    $types .= "s";
}
if ($search_state !== '') {
    $query .= " AND address4 LIKE ?";
    $params[] = "%" . $search_state . "%";
    $types .= "s";
}

$query .= " ORDER BY name ASC";
$stmt = mysqli_prepare($conn, $query);

if (count($params) > 1) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Filters Engine</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="dashboard-body">
    <?php include "../header.php"; ?>
    <main class="dashboard-main">
        <div class="form-section" style="max-width: 100%; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom:15px;">
                <h2 style="color: #3f3424; margin:0;">🔍 Advanced Criteria Engine</h2>
                <a href="friends_list.php" class="back-btn" style="background-color:#7a551b; margin:0; text-decoration:none;">← Standard List</a>
            </div>
            
            <form action="friend_search.php" method="GET" class="search-form" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                <div>
                    <label style="font-weight:bold; font-size:14px; display:block; margin-bottom:5px;">Name String Descriptor:</label>
                    <input type="text" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>" style="width:100%;">
                </div>
                <div>
                    <label style="font-weight:bold; font-size:14px; display:block; margin-bottom:5px;">Select Gender:</label>
                    <select name="search_gender" style="width:100%; padding:9px; border-radius:10px; border:1px solid #c9b996; background:#fffdf8;">
                        <option value="">-- View All Genders --</option>
                        <option value="Male" <?php if($search_gender == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if($search_gender == 'Female') echo 'selected'; ?>>Female</option>
                    </select>
                </div>
                <div>
                    <label style="font-weight:bold; font-size:14px; display:block; margin-bottom:5px;">State Target Line (Address 4):</label>
                    <input type="text" name="search_state" placeholder="e.g. SELANGOR" value="<?php echo htmlspecialchars($search_state); ?>" style="width:100%;">
                </div>
                <div style="display: flex; gap: 5px;">
                    <button type="submit" style="flex:1; margin:0;">Execute</button>
                    <a href="friend_search.php" class="back-btn" style="margin:0; flex:1; text-align:center; line-height:20px; text-decoration:none;">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-section">
            <h3>Discovered Match Data (<?php echo mysqli_num_rows($result); ?> total)</h3>
            <table class="admin-table" style="margin-top:15px;">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Connection Details</th>
                        <th>Address Field Breakdown</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($row['photo'] ?: '../uploads/default.png'); ?>" class="friend-photo" alt="Photo" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
                                </td>
                                <td style="font-weight:bold;"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                <td>
                                    <div>📞 <?php echo htmlspecialchars($row['hp_no']); ?></div>
                                    <div style="font-size:12px; color:#6f6252;">✉️ <?php echo htmlspecialchars($row['email']); ?></div>
                                </td>
                                <td>
                                    <div style="font-size:11px;"><?php echo htmlspecialchars($row['address1'] . ', ' . $row['address2']); ?></div>
                                    <div style="color: #861a4c; font-weight: bold;">📍 <?php echo htmlspecialchars($row['address4']); ?></div>
                                </td>
                                <td>
                                    <a href="friend_view.php?id=<?php echo $row['friend_id']; ?>" class="small-btn" style="text-decoration:none;">Profile View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding:25px; color:#6f6252;">No custom datasets inside your repository matched your criteria.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <?php include "../footer.php"; ?>
</body>
</html>
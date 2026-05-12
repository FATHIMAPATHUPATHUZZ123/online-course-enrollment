<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include '../database.php';

// Delete individual instructor securely
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM instructors WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: view_instructors.php");
    exit;
}

// Fetch all instructors
$result = $conn->query("SELECT id, name, email, status FROM instructors ORDER BY id DESC");
if(!$result) die("Query failed: " . $conn->error);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Instructors</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
<style>
body { font-family: 'Poppins', sans-serif; background: #f0f8ff; padding: 30px; }
h2 { color: #003366; }
.table-container { overflow-x: auto; }
table { border-collapse: collapse; width: 100%; background: white; border-radius: 8px; overflow: hidden; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
th { background: #4da3ff; color: white; }
tr.inactive { background-color: #ffe6e6; }
a, button { text-decoration: none; color: #4da3ff; margin-right: 10px; cursor: pointer; }
a:hover, button:hover { text-decoration: underline; }
button { background: none; border: none; font-family: inherit; font-size: 1em; }
</style>
</head>
<body>
<h2>All Instructors</h2>

<form method="post" action="delete_inactive_instructors.php" onsubmit="return confirm('Delete all inactive instructors?');">
    <button type="submit">Delete Inactive Instructors</button>
</form>

<div class="table-container">
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr class="<?php echo $row['status']=='inactive'?'inactive':''; ?>">
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo htmlspecialchars($row['email']); ?></td>
    <td><?php echo $row['status']; ?></td>
    <td>
        <a href="edit_instructor.php?id=<?php echo $row['id']; ?>">Edit</a>
        <a href="view_instructors.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this instructor?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<p><a href="dashboard.php">← Back to Dashboard</a></p>
</body>
</html>

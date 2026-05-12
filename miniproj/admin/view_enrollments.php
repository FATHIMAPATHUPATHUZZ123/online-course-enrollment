<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include '../database.php';

// Delete individual enrollment
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM enrollments WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: view_enrollments.php");
    exit;
}

// Fetch all enrollments with student and course info
$result = $conn->query("
    SELECT e.id, s.name AS student_name, s.email AS student_email,
           c.title AS course_title, c.duration AS course_duration, e.enrolled_at
    FROM enrollments e
    JOIN students s ON e.student_id = s.id
    JOIN courses c ON e.course_id = c.id
    ORDER BY e.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Enrollments</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
<style>
body { font-family:'Poppins',sans-serif; background:#f0f8ff; padding:30px; }
h2 { color:#003366; }
.table-container { overflow-x:auto; }
table { border-collapse:collapse; width:100%; background:white; border-radius:8px; overflow:hidden; }
th, td { border:1px solid #ccc; padding:10px; text-align:left; }
th { background:#4da3ff; color:white; }
a { text-decoration:none; color:#4da3ff; margin-right:10px; }
a:hover { text-decoration:underline; }
</style>
</head>
<body>
<h2>All Enrollments</h2>

<div class="table-container">
<table>
<thead>
<tr>
<th>ID</th>
<th>Student Name</th>
<th>Student Email</th>
<th>Course Title</th>
<th>Course Duration</th>
<th>Enrolled At</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo htmlspecialchars($row['student_name']); ?></td>
<td><?php echo htmlspecialchars($row['student_email']); ?></td>
<td><?php echo htmlspecialchars($row['course_title']); ?></td>
<td><?php echo htmlspecialchars($row['course_duration']); ?></td>
<td><?php echo $row['enrolled_at']; ?></td>
<td>
<a href="view_enrollments.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this enrollment?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<p><a href="dashboard.php">← Back to Dashboard</a></p>
</body>
</html>

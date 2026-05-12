<?php
session_start();
include '../database.php';

/* ---------- ADMIN LOGIN CHECK ---------- */
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

/* ---------- CHECK ASSIGNMENT ID ---------- */
if (!isset($_GET['assignment_id']) || !is_numeric($_GET['assignment_id'])) {
    die("Invalid Assignment ID.");
}

$assignment_id = (int)$_GET['assignment_id'];

/* ---------- GET ASSIGNMENT TITLE ---------- */
$stmt = $conn->prepare("SELECT title FROM assignments WHERE id=?");
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    die("Assignment not found.");
}

$assignment = $res->fetch_assoc();
$assignment_title = $assignment['title'];
$stmt->close();

/* ---------- FETCH SUBMISSIONS ---------- */
$sql = "
SELECT 
sub.id AS submission_id,
sub.student_id,
sub.file_path,
sub.submitted_at,
sub.grade,
sub.feedback,
stu.name AS student_name,
stu.email AS student_email
FROM submissions sub
LEFT JOIN students stu ON sub.student_id = stu.id
WHERE sub.assignment_id = ?
ORDER BY sub.submitted_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin - Submissions</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:25px;
}

h2{
margin-bottom:20px;
}

table{
width:100%;
border-collapse:collapse;
background:white;
}

th,td{
padding:10px;
border:1px solid #ccc;
text-align:left;
}

th{
background:#4da3ff;
color:white;
}

tr:nth-child(even){
background:#f9f9f9;
}

.download{
color:#007bff;
text-decoration:none;
}

.download:hover{
text-decoration:underline;
}

.no-file{
color:#888;
}

.back{
display:inline-block;
margin-top:20px;
padding:8px 14px;
background:#555;
color:white;
text-decoration:none;
border-radius:4px;
}

.back:hover{
background:#333;
}

</style>

</head>

<body>

<h2> Submissions for Assignment: <?php echo htmlspecialchars($assignment_title); ?></h2>

<?php

if ($result->num_rows > 0) {

echo "<table>";

echo "<tr>
<th>Student Name</th>
<th>Email</th>
<th>File</th>
<th>Submitted At</th>
<th>Grade</th>
<th>Feedback</th>
</tr>";

while ($row = $result->fetch_assoc()) {

echo "<tr>";

/* ---------- STUDENT NAME ---------- */

echo "<td>";

if (!empty($row['student_name'])) {
echo htmlspecialchars($row['student_name']);
} else {
echo "Student ID: " . htmlspecialchars($row['student_id']);
}

echo "</td>";

/* ---------- EMAIL ---------- */

echo "<td>";

if (!empty($row['student_email'])) {
echo htmlspecialchars($row['student_email']);
} else {
echo "-";
}

echo "</td>";

/* ---------- FILE DOWNLOAD ---------- */

echo "<td>";

if (!empty($row['file_path'])) {

$file = "../uploads/" . $row['file_path'];

if (file_exists($file)) {

echo "<a class='download' href='" . htmlspecialchars($file) . "' download>Download</a>";

} else {

echo "<span class='no-file'>File Missing</span>";

}

} else {

echo "<span class='no-file'>No File</span>";

}

echo "</td>";

/* ---------- SUBMISSION TIME ---------- */

echo "<td>" . htmlspecialchars($row['submitted_at']) . "</td>";

/* ---------- GRADE ---------- */

echo "<td>";

if ($row['grade'] !== NULL) {
echo htmlspecialchars($row['grade']);
} else {
echo "Not Graded";
}

echo "</td>";

/* ---------- FEEDBACK ---------- */

echo "<td>";

if (!empty($row['feedback'])) {
echo htmlspecialchars($row['feedback']);
} else {
echo "No Feedback";
}

echo "</td>";

echo "</tr>";

}

echo "</table>";

} else {

echo "<p>No submissions found.</p>";

}

?>

<a class="back" href="admin_assignments.php"> Back to Assignments</a>

</body>
</html>

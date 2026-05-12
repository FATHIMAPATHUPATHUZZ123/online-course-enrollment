<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    die("Please login first.");
}

$student_id = $_SESSION['student_id'];

if (!isset($_GET['course_id'])) {
    die("Course ID missing.");
}

$course_id = intval($_GET['course_id']);

// Fetch assignments
$stmt = $conn->prepare("SELECT * FROM assignments WHERE course_id=? ORDER BY deadline ASC");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$assignments = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Assignments</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>

body{
background:#f1f5f9;
font-family:Arial;
}

.container-box{
max-width:1000px;
margin:60px auto;
}

.card{
border:none;
border-radius:12px;
box-shadow:0 8px 18px rgba(0,0,0,0.08);
}

.table th{
background:#0f4c75;
color:white;
}

.status-green{
color:green;
font-weight:bold;
}

.status-red{
color:red;
font-weight:bold;
}

</style>

</head>

<body>

<div class="container container-box">

<div class="card p-4">

<h3 class="mb-4">📘 Assignments</h3>

<?php if ($assignments->num_rows == 0) { ?>

<div class="alert alert-info">
No assignments available.
</div>

<?php } else { ?>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>
<tr>
<th>Assignment</th>
<th>Deadline</th>
<th>Status</th>
<th>Action / Download</th>
<th>Grade</th>
<th>Feedback</th>
</tr>
</thead>

<tbody>

<?php
while ($row = $assignments->fetch_assoc()) {

$stmt2 = $conn->prepare("SELECT * FROM submissions WHERE assignment_id=? AND student_id=?");
$stmt2->bind_param("ii", $row['id'], $student_id);
$stmt2->execute();
$submission = $stmt2->get_result()->fetch_assoc();
$stmt2->close();

$statusText = "Not Submitted";
$statusClass = "";

if ($submission) {
if ($submission['submitted_at'] <= $row['deadline']) {
$statusText = "On Time";
$statusClass = "status-green";
} else {
$statusText = "Late";
$statusClass = "status-red";
}
}

$actionText = $submission ? "Resubmit" : "Submit";
$actionLink = 'submit_assignment.php?assign_id=' . $row['id'] . '&course_id=' . $course_id;

$downloadLink = '';
if ($submission && !empty($submission['file_path'])) {
$downloadLink = ' | <a href="../uploads/' . htmlspecialchars($submission['file_path']) . '" target="_blank">Download</a>';
}

$grade = isset($submission['grade']) ? $submission['grade'] : '-';
$feedback = isset($submission['feedback']) ? $submission['feedback'] : '-';

echo "<tr>";

echo "<td>".htmlspecialchars($row['title'])."</td>";

echo "<td>".htmlspecialchars($row['deadline'])."</td>";

echo "<td class='$statusClass'>$statusText</td>";

echo "<td>
<a class='btn btn-sm btn-primary' href='$actionLink'>$actionText</a>
$downloadLink
</td>";

echo "<td>$grade</td>";

echo "<td>$feedback</td>";

echo "</tr>";

}
?>

</tbody>

</table>

</div>

<?php } ?>

<hr>

<a href="course_details.php?course_id=<?php echo $course_id; ?>" class="btn btn-secondary">
⬅ Back to Course
</a>

</div>

</div>

</body>
</html>
<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

$instructor_id = $_SESSION['instructor_id'];

if (!isset($_GET['assignment_id'])) {
    die("Assignment ID missing.");
}

$assignment_id = intval($_GET['assignment_id']);

/* Fetch submissions */

$sql = "SELECT s.*, st.name AS student_name, e.enrolled_at
        FROM submissions s
        JOIN students st ON s.student_id = st.id
        JOIN assignments a ON s.assignment_id = a.id
        JOIN courses c ON a.course_id = c.id
        JOIN enrollments e ON e.student_id = st.id AND e.course_id = c.id
        WHERE s.assignment_id=? AND c.instructor_id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $assignment_id, $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

/* Save marks + feedback */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id'])) {

    $submission_id = intval($_POST['submission_id']);
    $marks = intval($_POST['marks']);
    $feedback = $_POST['feedback'];

    $update = $conn->prepare("UPDATE submissions SET grade=?, feedback=? WHERE id=?");
    $update->bind_param("isi", $marks, $feedback, $submission_id);
    $update->execute();
    $update->close();

    header("Location: view_submissions.php?assignment_id=$assignment_id");
    exit;
}

/* Grade function */

function calculateGrade($marks){
    if ($marks >= 90) return 'A+';
    if ($marks >= 80) return 'A';
    if ($marks >= 70) return 'B';
    if ($marks >= 60) return 'C';
    if ($marks >= 50) return 'D';
    return 'F';
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Assignment Submissions</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#eef5ff;
font-family:Arial;
padding:40px;
}

.page-title{
text-align:center;
font-size:28px;
font-weight:bold;
color:#003366;
margin-bottom:30px;
}

.submission-table{
background:white;
border-radius:8px;
overflow:hidden;
box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

.table thead{
background:#0d6efd;
color:white;
}

.save-btn{
background:#198754;
color:white;
border:none;
padding:6px 10px;
border-radius:4px;
}

.save-btn:hover{
background:#157347;
}

.back-btn{
display:block;
width:220px;
margin:30px auto 0;
text-align:center;
padding:10px;
background:#0d6efd;
color:white;
border-radius:6px;
text-decoration:none;
}

.back-btn:hover{
background:#084298;
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

<div class="page-title">
Assignment Submissions
</div>

<div class="container">

<?php

if ($result->num_rows == 0) {

echo "<p class='text-center'>No submissions yet.</p>";

} else {

echo '<div class="submission-table">';
echo '<table class="table table-bordered table-hover text-center">';

echo '<thead>
<tr>
<th>Student</th>
<th>File</th>
<th>Submitted At</th>
<th>Status</th>
<th>Marks (Grade)</th>
<th>Feedback</th>
<th>Action</th>
</tr>
</thead>';

echo '<tbody>';

while ($row = $result->fetch_assoc()) {

$fileLink = '<a href="../uploads/'.htmlspecialchars($row['file_path']).'" download>Download</a>';

$marks = isset($row['grade']) ? $row['grade'] : '';
$feedback = isset($row['feedback']) ? $row['feedback'] : '';

/* Calculate deadline */

$deadline = date("Y-m-d H:i:s", strtotime($row['enrolled_at']." +30 days"));

/* Check if late */

$status = (strtotime($row['submitted_at']) <= strtotime($deadline))
? '<span class="status-green">On Time</span>'
: '<span class="status-red">Late</span>';

echo '<tr>';

echo '<td>'.htmlspecialchars($row['student_name']).'</td>';

echo '<td>'.$fileLink.'</td>';

echo '<td>'.htmlspecialchars($row['submitted_at']).'</td>';

echo '<td>'.$status.'</td>';

echo '<td>';

echo '<form method="post">';

echo '<input type="number" name="marks" value="'.htmlspecialchars($marks).'" min="0" max="100" style="width:70px;">';

if ($marks !== '') echo ' ('.calculateGrade($marks).')';

echo '</td>';

echo '<td>
<textarea name="feedback" rows="2" cols="25">'.htmlspecialchars($feedback).'</textarea>
</td>';

echo '<td>';

echo '<input type="hidden" name="submission_id" value="'.$row['id'].'">';

echo '<button class="save-btn" type="submit">Save</button>';

echo '</form>';

echo '</td>';

echo '</tr>';

}

echo '</tbody>';
echo '</table>';
echo '</div>';

}

?>

<a href="instructor_assignments.php" class="back-btn">
Back to My Assignments
</a>

</div>

</body>
</html>
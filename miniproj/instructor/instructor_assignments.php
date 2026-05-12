<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

$instructor_id = $_SESSION['instructor_id'];

/* Fetch assignments created by instructor */
$sql = "SELECT a.*, c.title AS course_title
        FROM assignments a
        JOIN courses c ON a.course_id = c.id
        WHERE c.instructor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
<title>My Assignments</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#eef5ff;
font-family:Arial;
padding:40px;
}

/* Heading */

.page-title{
text-align:center;
font-size:28px;
font-weight:bold;
color:#003366;
margin-bottom:30px;
}

/* Table Design */

.assignment-table{
background:white;
border-radius:8px;
overflow:hidden;
box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

/* Table header */

.table thead{
background:#0d6efd;
color:white;
}

/* View button */

.view-btn{
background:#198754;
color:white;
padding:6px 12px;
border-radius:5px;
text-decoration:none;
font-size:13px;
}

.view-btn:hover{
background:#157347;
}

/* Back button */

.back-btn{
display:block;
width:200px;
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

/* No data message */

.no-data{
text-align:center;
font-size:18px;
color:#555;
}

</style>

</head>

<body>

<div class="page-title">
My Assignments
</div>

<div class="container">

<?php
if ($result->num_rows == 0) {

echo '<p class="no-data">No assignments created yet.</p>';

} else {

echo '<div class="assignment-table">';

echo '<table class="table table-bordered table-hover text-center">';

echo '<thead>
<tr>
<th>Course</th>
<th>Assignment</th>
<th>Deadline</th>
<th>Submissions</th>
<th>Action</th>
</tr>
</thead>';

echo '<tbody>';

while ($row = $result->fetch_assoc()) {

/* Count student submissions */

$stmt2 = $conn->prepare("SELECT COUNT(*) AS total_submissions FROM submissions WHERE assignment_id=?");
$stmt2->bind_param("i", $row['id']);
$stmt2->execute();
$count_res = $stmt2->get_result();
$count_row = $count_res->fetch_assoc();
$total_submissions = $count_row['total_submissions'];
$stmt2->close();

$submissionsText = $total_submissions > 0 
? $total_submissions . " Submission(s)" 
: "No Submissions";

$viewSubmissionsLink = 'view_submissions.php?assignment_id=' . $row['id'];

echo '<tr>';

echo '<td>'.htmlspecialchars($row['course_title']).'</td>';

echo '<td>'.htmlspecialchars($row['title']).'</td>';

/* Deadline rule */

echo '<td>30 Days After Student Enrollment</td>';

echo '<td>'.$submissionsText.'</td>';

echo '<td>
<a class="view-btn" href="'.$viewSubmissionsLink.'">
View Submissions
</a>
</td>';

echo '</tr>';

}

echo '</tbody>';
echo '</table>';
echo '</div>';

}
?>

<a href="dashboard_instructor.php" class="back-btn">
 Back to Dashboard
</a>

</div>

</body>
</html>
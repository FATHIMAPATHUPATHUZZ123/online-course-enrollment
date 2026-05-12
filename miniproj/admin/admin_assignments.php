<?php
session_start();
include '../database.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Fetch assignments with correct submission counts
$sql = "
SELECT 
    a.id AS assignment_id,
    a.course_id,
    a.title,
    a.deadline,
    a.file_path AS teacher_file,

    COUNT(CASE 
        WHEN s.file_path IS NOT NULL AND s.file_path != '' 
        THEN 1 
    END) AS total_submissions,

    COUNT(CASE 
        WHEN s.file_path IS NOT NULL AND s.file_path != '' 
        THEN 1 
    END) AS submissions_with_file

FROM assignments a
LEFT JOIN submissions s ON a.id = s.assignment_id
GROUP BY a.id
ORDER BY a.id DESC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin - All Assignments</title>

<style>

body{
font-family: Arial;
background:#f4f6f9;
padding:30px;
}

h2{
text-align:center;
margin-bottom:25px;
}

table{
width:100%;
background:white;
border-collapse:collapse;
box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

th,td{
padding:12px;
border:1px solid #ddd;
text-align:center;
}

th{
background:#007bff;
color:white;
}

tr:nth-child(even){
background:#f9f9f9;
}

.btn{
padding:6px 12px;
background:#28a745;
color:white;
text-decoration:none;
border-radius:4px;
font-size:14px;
}

.btn:hover{
background:#218838;
}

.no-file{
color:#888;
}

.back{
display:inline-block;
margin-top:20px;
padding:8px 15px;
background:#555;
color:white;
text-decoration:none;
border-radius:4px;
}

.back:hover{
background:#333;
}

.download{
color:#007bff;
text-decoration:none;
}

.download:hover{
text-decoration:underline;
}

</style>
</head>

<body>

<h2>📘 All Assignments</h2>

<table>

<tr>
<th>ID</th>
<th>Course ID</th>
<th>Title</th>
<th>Deadline</th>
<th>Teacher Attachment</th>
<th>Total Submissions</th>
<th>Submissions with File</th>
<th>Action</th>
</tr>

<?php

if ($result && mysqli_num_rows($result) > 0) {

while ($row = mysqli_fetch_assoc($result)) {

echo "<tr>";

echo "<td>".(int)$row['assignment_id']."</td>";

echo "<td>".htmlspecialchars($row['course_id'])."</td>";

echo "<td>".htmlspecialchars($row['title'])."</td>";

echo "<td>".htmlspecialchars($row['deadline'])."</td>";

echo "<td>";

if (!empty($row['teacher_file'])) {

echo "<a class='download' href='../uploads/".htmlspecialchars($row['teacher_file'])."' download>Download</a>";

} else {

echo "<span class='no-file'>No File</span>";

}

echo "</td>";

echo "<td>".(int)$row['total_submissions']."</td>";

echo "<td>".(int)$row['submissions_with_file']."</td>";

echo "<td>";

if ((int)$row['total_submissions'] > 0) {

echo "<a class='btn' href='admin_view_submissions.php?assignment_id=".(int)$row['assignment_id']."'>View Submissions</a>";

} else {

echo "<span class='no-file'>No Submissions</span>";

}

echo "</td>";

echo "</tr>";

}

} else {

echo "<tr><td colspan='8'>No assignments found</td></tr>";

}

?>

</table>

<a class="back" href="dashboard.php">← Back to Dashboard</a>

</body>
</html>
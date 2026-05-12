<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include '../database.php';

// Delete inactive students (not enrolled within 30 days)
$inactive_days = 30;
$threshold_date = date('Y-m-d H:i:s', strtotime("-$inactive_days days"));

// Find students with no enrollments
$inactive_students = $conn->query("
    SELECT s.id 
    FROM students s
    LEFT JOIN enrollments e ON s.id = e.student_id
    WHERE e.id IS NULL AND TIMESTAMPDIFF(DAY, s.id, NOW()) >= $inactive_days
");

// Delete inactive students
while($row = $inactive_students->fetch_assoc()) {
    $conn->query("DELETE FROM students WHERE id=".$row['id']);
}

header("Location: view_students.php");
exit;

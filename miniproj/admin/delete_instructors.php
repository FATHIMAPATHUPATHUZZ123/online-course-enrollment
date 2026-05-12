<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include '../database.php';

// Delete inactive instructors (no course created within 30 days)
$inactive_days = 30;
$threshold_date = date('Y-m-d H:i:s', strtotime("-$inactive_days days"));

// Find instructors with no courses
$inactive_instructors = $conn->query("
    SELECT i.id 
    FROM instructors i
    LEFT JOIN courses c ON i.id = c.instructor_id
    WHERE c.id IS NULL AND TIMESTAMPDIFF(DAY, i.created_at, NOW()) >= $inactive_days
");

// Delete inactive instructors
while($row = $inactive_instructors->fetch_assoc()) {
    $conn->query("DELETE FROM instructors WHERE id=".$row['id']);
}

header("Location: view_instructors.php");
exit;

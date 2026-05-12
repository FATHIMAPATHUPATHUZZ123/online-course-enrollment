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

// Get progress value
$stmt = $conn->prepare("SELECT progress FROM course_progress WHERE student_id=? AND course_id=?");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$progress = $stmt->get_result()->fetch_assoc();
$stmt->close();

$percentage = $progress ? $progress['progress'] : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Course Progress</title>
    <style>
        .bar {
            width: 300px;
            height: 25px;
            background: #ccc;
            border-radius: 5px;
            overflow: hidden;
        }
        .fill {
            height: 100%;
            background: green;
            width: <?= $percentage ?>%;
        }
    </style>
</head>
<body>

<h2>Your Progress</h2>

<div class="bar">
    <div class="fill"></div>
</div>

<p><strong><?= $percentage ?>% Completed</strong></p>

</body>
</html>

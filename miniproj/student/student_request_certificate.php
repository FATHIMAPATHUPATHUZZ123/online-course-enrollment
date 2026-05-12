<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if ($course_id <= 0) {
    die("Invalid course.");
}

// Check if student already submitted a request
$stmt = $conn->prepare("SELECT id FROM certificate_requests WHERE student_id=? AND course_id=?");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existing) {
    header("Location: course_details.php?course_id=$course_id&msg=req_exists");
    exit;
}

// Get instructor_id from course
$stmt = $conn->prepare("SELECT instructor_id FROM courses WHERE id=?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (empty($course['instructor_id'])) {
    die("Instructor not assigned for this course.");
}

$instructor_id = (int)$course['instructor_id'];

// Insert certificate request with instructor_id
$stmt = $conn->prepare("
    INSERT INTO certificate_requests (student_id, course_id, status, instructor_id) 
    VALUES (?, ?, 'pending', ?)
");
$stmt->bind_param("iii", $student_id, $course_id, $instructor_id);
$stmt->execute();
$stmt->close();

header("Location: course_details.php?course_id=$course_id&msg=req_sent");
exit;
?>

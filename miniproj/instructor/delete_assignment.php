<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Assignment ID missing.");
}

$assignment_id = (int)$_GET['id'];

// Get course ID for redirect
$stmt = $conn->prepare("SELECT course_id, file_path FROM assignments WHERE id=?");
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$result) {
    die("Assignment not found.");
}

// Delete uploaded file
if ($result['file_path'] && file_exists("../uploads/".$result['file_path'])) {
    unlink("../uploads/".$result['file_path']);
}

// Delete assignment
$stmt = $conn->prepare("DELETE FROM assignments WHERE id=?");
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$stmt->close();

header("Location: edit_course.php?id=".$result['course_id']);
exit;

<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    echo "<p>Please <a href='login.student.php'>login</a> to delete submission.</p>";
    exit;
}

$student_id = $_SESSION['student_id'];

if (!isset($_GET['assign_id']) || !isset($_GET['course_id'])) {
    die("Assignment or course ID missing.");
}

$assign_id = intval($_GET['assign_id']);
$course_id = intval($_GET['course_id']);

// Get the file path first
$stmt = $conn->prepare("SELECT file_path FROM submissions WHERE student_id=? AND assignment_id=?");
$stmt->bind_param("ii", $student_id, $assign_id);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();
$stmt->close();

if($submission) {
    $file = "../uploads/" . $submission['file_path'];
    if(file_exists($file)) unlink($file); // delete physical file

    // Delete from database
    $stmt2 = $conn->prepare("DELETE FROM submissions WHERE student_id=? AND assignment_id=?");
    $stmt2->bind_param("ii", $student_id, $assign_id);
    $stmt2->execute();
    $stmt2->close();
}

header("Location: course_details.php?course_id=$course_id&msg=submission_deleted");
exit;
?>

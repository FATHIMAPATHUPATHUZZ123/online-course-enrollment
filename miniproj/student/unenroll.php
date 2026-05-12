<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login_student.php");
    exit;
}

if (!isset($_GET['course_id'])) {
    die("Course ID missing.");
}

$student_id = $_SESSION['student_id'];
$course_id = intval($_GET['course_id']);

// Delete enrollment record
$stmt = $conn->prepare("DELETE FROM enrollments WHERE student_id=? AND course_id=?");
$stmt->bind_param("ii", $student_id, $course_id);

if ($stmt->execute()) {
    $stmt->close();
    // ✅ Instead of redirecting, show success message
    echo "<p style='color:green;font-weight:bold;'>You have successfully unenrolled from this course.</p>";
    echo "<br><a href='view_courses.php' style='
        display:inline-block;
        background-color:#007BFF;
        color:white;
        padding:10px 15px;
        text-decoration:none;
        border-radius:5px;
    '>← Back to Courses</a>";
} else {
    echo "Error while unenrolling: " . $stmt->error;
    $stmt->close();
    exit;
}
?>

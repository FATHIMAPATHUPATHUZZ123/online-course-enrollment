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

// Check if already enrolled
$stmt = $conn->prepare("SELECT id FROM enrollments WHERE student_id=? AND course_id=?");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Not enrolled yet → insert
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id, enrolled_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $student_id, $course_id);
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: course_details.php?course_id=$course_id&msg=enrolled_success");
        exit;
    } else {
        // Handle insert error
        echo "Error enrolling: " . $stmt->error;
        $stmt->close();
        exit;
    }
} else {
    // Already enrolled → redirect with message
    $stmt->close();
    header("Location: course_details.php?course_id=$course_id&msg=already_enrolled");
    exit;
}
?>

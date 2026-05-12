<?php
include '../database.php';

function updateProgress($student_id, $course_id, $conn) {

    // Total assignments
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM assignments WHERE course_id=?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $total_assign = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Completed assignments
    $stmt = $conn->prepare("SELECT COUNT(*) AS completed FROM submissions WHERE student_id=? AND status='approved'");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $completed = $stmt->get_result()->fetch_assoc()['completed'];
    $stmt->close();

    // Calculate progress
    $progress = ($total_assign > 0) ? ($completed / $total_assign) * 100 : 0;
    $progress = round($progress);

    // Update table
    $stmt = $conn->prepare("UPDATE course_progress SET progress=? WHERE student_id=? AND course_id=?");
    $stmt->bind_param("iii", $progress, $student_id, $course_id);
    $stmt->execute();
    $stmt->close();
}
?>

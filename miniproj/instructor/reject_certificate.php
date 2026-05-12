<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

$request_id = intval($_GET['request_id']);
$instructor_id = intval($_SESSION['instructor_id']);

// Reject request only if it belongs to this instructor and is pending
$stmt = $conn->prepare("
    SELECT cr.id 
    FROM certificate_requests cr
    JOIN courses c ON cr.course_id = c.id
    WHERE cr.id=? AND c.instructor_id=? AND cr.status='pending'
");
$stmt->bind_param("ii", $request_id, $instructor_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$request) {
    die("Invalid or already processed certificate request.");
}

// Update request to rejected
$stmt = $conn->prepare("
    UPDATE certificate_requests 
    SET status='rejected', approved_at=NULL
    WHERE id=?
");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$stmt->close();

header("Location: view_certificate_requests.php?msg=rejected");
exit;
?>

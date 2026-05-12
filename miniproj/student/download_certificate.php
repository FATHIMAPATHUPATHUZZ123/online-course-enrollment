<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.student.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
if ($course_id <= 0) {
    die("Invalid course.");
}

// Check if student has an approved certificate
$stmt = $conn->prepare("
    SELECT certificate_file 
    FROM certificates c
    JOIN certificate_requests r ON c.student_id=r.student_id AND c.course_id=r.course_id
    WHERE c.student_id=? AND c.course_id=? AND r.status='approved'
");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$cert = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cert) {
    die("Certificate not available or not approved yet.");
}

$filepath = "../certificates/" . $cert['certificate_file'];

if (!file_exists($filepath)) {
    die("Certificate file not found.");
}

// Send file for download
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit;
?>

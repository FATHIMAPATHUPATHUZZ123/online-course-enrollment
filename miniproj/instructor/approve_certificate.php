<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;
$instructor_id = intval($_SESSION['instructor_id']);

if ($request_id <= 0) {
    die("Invalid request ID.");
}

/* 1) Fetch the pending request and verify instructor ownership */
$stmt = $conn->prepare("
    SELECT cr.student_id, cr.course_id, cr.status, c.title AS course_title, s.name AS student_name
    FROM certificate_requests cr
    JOIN courses c ON cr.course_id = c.id
    JOIN students s ON cr.student_id = s.id
    WHERE cr.id = ? AND c.instructor_id = ? AND cr.status = 'pending'
");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ii", $request_id, $instructor_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$request) {
    die("Invalid or already processed certificate request.");
}

$student_id = (int)$request['student_id'];
$course_id  = (int)$request['course_id'];
$student_name = $request['student_name'];
$course_title = $request['course_title'];

/* 2) Ensure certificates directory exists and is writable */
// Build a directory path relative to this file, but use an absolute path for reliability
$certDirRelative = dirname(__DIR__) . '/certificates'; // ../certificates from the instructor folder
if (!is_dir($certDirRelative)) {
    // try create it
    if (!mkdir($certDirRelative, 0755, true)) {
        die("Failed to create certificates directory: " . htmlspecialchars($certDirRelative));
    }
}

// Verify writable
if (!is_writable($certDirRelative)) {
    // try to set permission (best-effort)
    @chmod($certDirRelative, 0755);
    if (!is_writable($certDirRelative)) {
        die("Certificates directory is not writable: " . htmlspecialchars($certDirRelative));
    }
}

/* 3) Generate certificate file name and full path */
$filename = 'certificate_' . $student_id . '_' . $course_id . '_' . time() . '.pdf';
$filepath_full = $certDirRelative . DIRECTORY_SEPARATOR . $filename;

/* 4) Create PDF using FPDF (make sure fpdf.php is present at ../fpdf/fpdf.php) */
require_once('../fpdf/fpdf.php');

try {
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',24);
    $pdf->Cell(0, 40, "Certificate of Completion", 0, 1, 'C');
    $pdf->SetFont('Arial','',18);
    $pdf->Ln(20);
    $pdf->Cell(0, 10, "This is to certify that", 0, 1, 'C');
    $pdf->SetFont('Arial','B',20);
    $pdf->Cell(0, 15, $student_name, 0, 1, 'C');
    $pdf->SetFont('Arial','',18);
    $pdf->Ln(5);
    $pdf->Cell(0, 10, "has successfully completed the course", 0, 1, 'C');
    $pdf->SetFont('Arial','B',20);
    $pdf->Cell(0, 15, $course_title, 0, 1, 'C');
    $pdf->Ln(20);
    $pdf->SetFont('Arial','',14);
    $pdf->Cell(0, 10, "Approved by Instructor", 0, 1, 'C');

    // Use Output('F', fullpath) to save file
    $pdf->Output('F', $filepath_full);
} catch (Exception $e) {
    die("PDF generation failed: " . htmlspecialchars($e->getMessage()));
}

/* 5) Insert into certificates table (store filename or relative path) */
/* You currently store certificate_file as varchar, earlier code stored filename; we keep that */
$stmt = $conn->prepare("
    INSERT INTO certificates (student_id, course_id, certificate_file) 
    VALUES (?, ?, ?)
");
if (!$stmt) {
    // cleanup created file if db insert not possible
    if (file_exists($filepath_full)) @unlink($filepath_full);
    die("Prepare failed (insert certificate): " . $conn->error);
}
$stmt->bind_param("iis", $student_id, $course_id, $filename);
if (!$stmt->execute()) {
    if (file_exists($filepath_full)) @unlink($filepath_full);
    $stmt->close();
    die("Failed to save certificate record: " . $stmt->error);
}
$stmt->close();

/* 6) Update request status to approved and approved_at timestamp */
$stmt = $conn->prepare("
    UPDATE certificate_requests 
    SET status='approved', approved_at=NOW() 
    WHERE id=?
");
if (!$stmt) {
    die("Prepare failed (update request): " . $conn->error);
}
$stmt->bind_param("i", $request_id);
$stmt->execute();
$stmt->close();

/* 7) Redirect back to requests list with success message */
header("Location: view_certificate_requests.php?msg=approved");
exit;
?>

<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    exit("Unauthorized");
}

$assignment_id = $_GET['assignment_id'];

$stmt = $conn->prepare("SELECT file_path FROM submissions WHERE assignment_id=?");
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();

$zipname = "submissions_$assignment_id.zip";
$zip = new ZipArchive;

$zip->open($zipname, ZipArchive::CREATE);

while ($row = $result->fetch_assoc()) {
    $file = "../uploads/" . $row['file_path'];
    if (file_exists($file)) {
        $zip->addFile($file, basename($file));
    }
}

$zip->close();

header('Content-Type: application/zip');
header("Content-Disposition: attachment; filename=$zipname");
readfile($zipname);
unlink($zipname);
exit;
?>

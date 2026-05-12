<?php
session_start();
include '../database.php';

$student_id = $_SESSION['student_id'];
$assignment_id = $_POST['assignment_id'];

$target_dir = "../uploads/";
$filename = "submission_" . time() . "_" . basename($_FILES["file"]["name"]);
$target_file = $target_dir . $filename;

if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {

    $stmt = $conn->prepare("
        INSERT INTO submissions (assignment_id, student_id, file_path)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iis", $assignment_id, $student_id, $filename);
    $stmt->execute();

    echo "Uploaded successfully!";
} else {
    echo "Error uploading file!";
}
?>

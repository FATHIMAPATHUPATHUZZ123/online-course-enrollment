<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) exit;
$student_id = $_SESSION['student_id'];

if (!isset($_GET['video_id'])) exit;
$video_id = intval($_GET['video_id']);

// Check if already marked
$stmt = $conn->prepare("SELECT id FROM video_progress WHERE student_id=? AND video_id=?");
$stmt->bind_param("ii", $student_id, $video_id);
$stmt->execute();
$res = $stmt->get_result();
$stmt->close();

if ($res->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO video_progress (student_id, video_id, watched) VALUES (?, ?, 1)");
    $stmt->bind_param("ii", $student_id, $video_id);
    $stmt->execute();
    $stmt->close();
}
?>

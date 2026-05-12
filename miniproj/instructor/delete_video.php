<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

if (!isset($_GET['id'])) die("Video ID missing.");

$video_id = (int)$_GET['id'];

// Get course ID and file path
$stmt = $conn->prepare("SELECT course_id, video_path FROM course_videos WHERE id=?");
$stmt->bind_param("i", $video_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$result) die("Video not found.");

// Delete video file
if ($result['video_path'] && file_exists("../uploads/".$result['video_path'])) {
    unlink("../uploads/".$result['video_path']);
}

// Delete video record
$stmt = $conn->prepare("DELETE FROM course_videos WHERE id=?");
$stmt->bind_param("i", $video_id);
$stmt->execute();
$stmt->close();

header("Location: edit_course.php?id=".$result['course_id']);
exit;

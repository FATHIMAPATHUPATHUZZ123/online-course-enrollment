<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    die("Please login first.");
}

$student_id = $_SESSION['student_id'];

if (!isset($_GET['course_id']) || !isset($_GET['video_id'])) {
    die("Course or video missing.");
}

$course_id = intval($_GET['course_id']);
$video_id = intval($_GET['video_id']);

// Fetch video
$stmt = $conn->prepare("SELECT * FROM course_videos WHERE id=? AND course_id=?");
$stmt->bind_param("ii", $video_id, $course_id);
$stmt->execute();
$video = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$video) {
    die("Video not found.");
}

// Mark as watched (if not already)
$vp_stmt = $conn->prepare("SELECT id FROM video_progress WHERE student_id=? AND video_id=?");
$vp_stmt->bind_param("ii", $student_id, $video_id);
$vp_stmt->execute();
$vp_res = $vp_stmt->get_result()->fetch_assoc();
$vp_stmt->close();

if ($vp_res) {
    // Already exists, update watched
    $update = $conn->prepare("UPDATE video_progress SET watched=1, watched_at=NOW() WHERE id=?");
    $update->bind_param("i", $vp_res['id']);
    $update->execute();
    $update->close();
} else {
    // Insert new record
    $insert = $conn->prepare("INSERT INTO video_progress(student_id, course_id, video_id, watched) VALUES(?,?,?,1)");
    $insert->bind_param("iii", $student_id, $course_id, $video_id);
    $insert->execute();
    $insert->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Watch Video: <?php echo htmlspecialchars($video['title']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h2><?php echo htmlspecialchars($video['title']); ?></h2>
        <video width="100%" controls>
            <source src="<?php echo htmlspecialchars($video['video_path']); ?>" type="video/mp4">
            Your browser does not support HTML5 video.
        </video>
        <br><br>
        <a href="course_details.php?course_id=<?php echo $course_id; ?>" class="btn btn-primary">Back to Course</a>
    </div>
</body>
</html>

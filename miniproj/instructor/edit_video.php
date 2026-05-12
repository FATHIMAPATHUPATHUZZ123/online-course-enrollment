<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Video ID missing.");
}

$video_id = (int)$_GET['id'];

// Fetch video
$stmt = $conn->prepare("SELECT * FROM course_videos WHERE id=?");
$stmt->bind_param("i", $video_id);
$stmt->execute();
$video = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$video) die("Video not found.");

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $sort_order = $_POST['sort_order'];
    $video_path = $video['video_path'];

    if (isset($_FILES['video']) && $_FILES['video']['name'] != "") {
        $target_dir = "../uploads/";
        $file_name = time() . "_" . basename($_FILES['video']['name']);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['video']['tmp_name'], $target_file)) {
            $video_path = $file_name;
        }
    }

    $update = $conn->prepare("UPDATE course_videos SET title=?, sort_order=?, video_path=? WHERE id=?");
    $update->bind_param("sisi", $title, $sort_order, $video_path, $video_id);
    $update->execute();
    $update->close();

    echo "<script>alert('Video updated!'); window.location.href='edit_course.php?id=".$video['course_id']."';</script>";
    exit;
}
?>

<h2>Edit Video</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($video['title']); ?>" required><br><br>

    <label>Sort Order:</label><br>
    <input type="number" name="sort_order" value="<?php echo htmlspecialchars($video['sort_order']); ?>" required><br><br>

    <label>Video File: (optional)</label><br>
    <input type="file" name="video"><br>
    <?php if($video['video_path']) echo "Current video: <a href='../uploads/".$video['video_path']."' target='_blank'>".$video['video_path']."</a>"; ?><br><br>

    <button type="submit">Update Video</button>
</form>
<a href="edit_course.php?id=<?php echo $video['course_id']; ?>">&larr; Back to Course</a>

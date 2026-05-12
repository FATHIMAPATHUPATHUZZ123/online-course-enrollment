<?php
session_start();
include '../database.php';

// Check if instructor is logged in
if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

$instructor_id = intval($_SESSION['instructor_id']);
$message = "";
$error = "";

// Fetch instructor courses
$courses_result = mysqli_query($conn, "SELECT id, title FROM courses WHERE instructor_id = $instructor_id");
$courses = array();
while ($row = mysqli_fetch_assoc($courses_result)) {
    $courses[] = $row;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : 1;

    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {

        $projectRoot = realpath(__DIR__ . '/..');
        $uploadDir = $projectRoot . '/uploads/videos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $tmpName = $_FILES['video']['tmp_name'];
        $originalName = basename($_FILES['video']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = array('mp4','mov','avi','mkv');

        if (in_array($ext, $allowed)) {

            $video_name = time() . '_' . preg_replace("/[^a-zA-Z0-9_-]/", "", pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $ext;
            $destination = $uploadDir . $video_name;

            if (move_uploaded_file($tmpName, $destination)) {

                $relativePath = 'uploads/videos/' . $video_name;

                $stmt = mysqli_prepare($conn, "INSERT INTO course_videos (course_id, title, video_path, sort_order) VALUES (?, ?, ?, ?)");

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "issi", $course_id, $title, $relativePath, $sort_order);

                    if (mysqli_stmt_execute($stmt)) {
                        $message = "✅ Video uploaded successfully!";
                    } else {
                        $error = "DB error: " . mysqli_stmt_error($stmt);
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    $error = "DB prepare error: " . mysqli_error($conn);
                }

            } else {
                $error = "❌ Failed to move uploaded video.";
            }

        } else {
            $error = "❌ Invalid video type. Allowed: mp4, mov, avi, mkv.";
        }

    } else {
        $error = "❌ No video selected or upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Course Video</title>

<style>

body{
font-family: Arial, sans-serif;
background:#f0f2f5;
padding:20px;
}

.container{
max-width:600px;
margin:0 auto;
background:#fff;
padding:20px;
border-radius:10px;
box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

h2{
text-align:center;
color:#1e3a8a;
}

label{
display:block;
margin-top:12px;
font-weight:600;
}

input[type=text],
input[type=number],
select,
input[type=file]{
width:100%;
padding:10px;
margin-top:6px;
border-radius:6px;
border:1px solid #ccc;
}

input[type=submit]{
margin-top:14px;
width:100%;
padding:12px;
border-radius:6px;
border:none;
background:#1d4ed8;
color:#fff;
cursor:pointer;
}

input[type=submit]:hover{
background:#2563eb;
}

.msg{
padding:10px;
border-radius:6px;
margin-bottom:12px;
}

.success{
background:#e6ffed;
color:#036b2f;
border:1px solid #b6f0c9;
}

.error{
background:#fff0f0;
color:#b71c1c;
border:1px solid #f5c2c2;
}

.back-btn{
display:block;
margin-top:15px;
text-align:center;
padding:10px;
background:#6b7280;
color:white;
border-radius:6px;
text-decoration:none;
}

.back-btn:hover{
background:#4b5563;
}

</style>
</head>

<body>

<div class="container">

<h2>Add Video to Course</h2>

<?php if ($message) echo "<div class='msg success'>{$message}</div>"; ?>
<?php if ($error) echo "<div class='msg error'>{$error}</div>"; ?>

<form method="POST" enctype="multipart/form-data">

<label>Course</label>
<select name="course_id" required>
<option value="">-- Select Course --</option>
<?php
foreach($courses as $c){
echo "<option value='".$c['id']."'>".htmlspecialchars($c['title'])."</option>";
}
?>
</select>

<label>Video Title</label>
<input type="text" name="title" required maxlength="255">

<label>Sort Order</label>
<input type="number" name="sort_order" value="1" min="1">

<label>Video File</label>
<input type="file" name="video" required>

<input type="submit" value="Upload Video">

</form>

<a href="dashboard_instructor.php" class="back-btn">← Back to Dashboard</a>

</div>

</body>
</html>
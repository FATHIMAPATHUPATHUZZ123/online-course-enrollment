<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

$instructor_id = intval($_SESSION['instructor_id']);
$courses_result = $conn->query(
"SELECT * FROM courses WHERE instructor_id=$instructor_id ORDER BY created_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Courses</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#eef6ff;
font-family:Arial;
padding:30px;
}

/* PAGE HEADING */

.page-title{
text-align:center;
font-weight:bold;
font-size:28px;
color:#003366;
margin-bottom:25px;
}

/* DASHBOARD BUTTON */

.back-btn{
position:absolute;
right:40px;
top:30px;
background:#0d6efd;
color:white;
padding:8px 15px;
border-radius:6px;
text-decoration:none;
font-size:14px;
}

.back-btn:hover{
background:#084298;
}

/* CREATE BUTTON */

.create-btn{
display:block;
width:200px;
margin:0 auto 30px;
padding:10px;
text-align:center;
background:#198754;
color:white;
border-radius:6px;
text-decoration:none;
font-weight:bold;
}

.create-btn:hover{
background:#157347;
}

/* COURSE GRID */

.course-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
gap:25px;
}

/* COURSE CARD */

.course-card{
background:white;
border-radius:10px;
box-shadow:0 4px 12px rgba(0,0,0,0.1);
overflow:hidden;
}

.course-card img{
width:100%;
height:150px;
object-fit:cover;
}

.course-body{
padding:15px;
}

.course-body h5{
color:#0d6efd;
font-weight:bold;
}

.course-body p{
font-size:14px;
color:#444;
}

/* VIDEO SMALL SIZE */

.course-video{
width:200px;
height:110px;
border-radius:6px;
margin-top:6px;
}

/* LISTS */

.video-list li,
.assignment-list li{
font-size:13px;
margin-bottom:8px;
}

/* BUTTON AREA */

.card-buttons{
margin-top:10px;
}

.card-buttons a{
margin:3px;
font-size:12px;
}

</style>

</head>

<body>

<a href="dashboard_instructor.php" class="back-btn">Back to Dashboard</a>

<div class="page-title">
My Courses
</div>

<a href="create_course.php" class="create-btn">+ Create New Course</a>

<div class="course-grid">

<?php while($row = $courses_result->fetch_assoc()): ?>

<?php
$course_id = $row['id'];

$course_image = $row['image']
? "../student/images/courses/".$row['image']
: "https://via.placeholder.com/400x150?text=Course";

$videos_result = $conn->query(
"SELECT * FROM course_videos WHERE course_id=$course_id ORDER BY sort_order ASC"
);

$assignments_result = $conn->query(
"SELECT * FROM assignments WHERE course_id=$course_id ORDER BY id ASC"
);
?>

<div class="course-card">

<img src="<?php echo $course_image; ?>">

<div class="course-body">

<h5><?php echo htmlspecialchars($row['title']); ?></h5>

<p><?php echo htmlspecialchars($row['description']); ?></p>

<!-- VIDEOS -->

<?php if ($videos_result->num_rows > 0): ?>

<strong>Videos:</strong>

<ul class="video-list">

<?php while($v=$videos_result->fetch_assoc()): ?>

<li>

<?php echo htmlspecialchars($v['title']); ?>

<br>

<video class="course-video" controls>
<source src="../<?php echo htmlspecialchars($v['video_path']); ?>" type="video/mp4">
</video>

</li>

<?php endwhile; ?>

</ul>

<?php endif; ?>

<!-- ASSIGNMENTS -->

<?php if ($assignments_result->num_rows > 0): ?>

<strong>Assignments:</strong>

<ul class="assignment-list">

<?php while($a=$assignments_result->fetch_assoc()): ?>

<li>

<?php echo htmlspecialchars($a['title']); ?>

<?php if(!empty($a['file_path'])): ?>

- <a href="../<?php echo htmlspecialchars($a['file_path']); ?>" target="_blank">
Download
</a>

<?php endif; ?>

|

<a href="view_submissions.php?assignment_id=<?php echo $a['id']; ?>">
View Submissions
</a>

</li>

<?php endwhile; ?>

</ul>

<?php endif; ?>

<div class="card-buttons">

<a href="edit_course.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>

<a href="delete_course.php?id=<?php echo $row['id']; ?>" 
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this course?');">Delete</a>

<a href="add_course_video.php?course_id=<?php echo $row['id']; ?>" 
class="btn btn-success btn-sm">Add Video</a>

<a href="add_assignment.php?course_id=<?php echo $row['id']; ?>" 
class="btn btn-warning btn-sm">Add Assignment</a>

</div>

</div>
</div>

<?php endwhile; ?>

</div>

</body>
</html>
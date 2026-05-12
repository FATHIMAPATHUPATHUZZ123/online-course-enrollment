<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid course ID'); window.location.href='view_courses.php';</script>";
    exit;
}

$course_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT id, title, description, duration FROM courses WHERE id=?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo "<script>alert('Course not found'); window.location.href='view_courses.php';</script>";
    exit;
}

$stmt->bind_result($id, $title, $description, $duration);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_course'])) {

    $new_title = $_POST['title'];
    $new_description = $_POST['description'];
    $new_duration = $_POST['duration'];

    $update = $conn->prepare("UPDATE courses SET title=?, description=?, duration=? WHERE id=?");
    $update->bind_param("sssi", $new_title, $new_description, $new_duration, $course_id);
    $update->execute();
    $update->close();

    echo "<script>alert('Course updated successfully!'); window.location.href='edit_course.php?id=$course_id';</script>";
    exit;
}

$assignments = $conn->query("SELECT * FROM assignments WHERE course_id=$course_id ORDER BY id DESC");
$videos = $conn->query("SELECT * FROM course_videos WHERE course_id=$course_id ORDER BY sort_order ASC");
?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Course</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#eef2f7;
font-family:'Segoe UI';
}

.sidebar{
height:100vh;
position:fixed;
left:0;
top:0;
width:220px;
background:#1f2937;
padding-top:20px;
}

.sidebar h4{
color:white;
text-align:center;
margin-bottom:30px;
}

.sidebar a{
color:#d1d5db;
display:block;
padding:12px 20px;
text-decoration:none;
}

.sidebar a:hover{
background:#374151;
color:white;
}

.main{
margin-left:240px;
padding:25px;
}

.page-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
}

.card{
border:none;
box-shadow:0 2px 8px rgba(0,0,0,0.08);
border-radius:8px;
}

.table{
margin-bottom:0;
}

.btn-sm{
padding:4px 8px;
font-size:13px;
}

.section-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:10px;
}

</style>

</head>

<body>

<div class="sidebar">

<h4>Instructor Panel</h4>

<a href="dashboard_instructor.php">Dashboard</a>
<a href="view_courses.php">Courses</a>
<a href="create_course.php">Add Course</a>
<a href="logout.php">Logout</a>

</div>


<div class="main">

<div class="page-header">

<h3>Edit Course: <?php echo htmlspecialchars($title); ?></h3>

<div>
<a href="dashboard_instructor.php" class="btn btn-secondary btn-sm">Dashboard</a>
<a href="view_courses.php" class="btn btn-dark btn-sm">Back to Courses</a>
</div>

</div>


<!-- Course Edit -->

<div class="card p-3 mb-3">

<form method="POST">

<input type="hidden" name="update_course" value="1">

<div class="row">

<div class="col-md-4">
<label class="form-label">Title</label>
<input type="text" name="title" class="form-control form-control-sm"
value="<?php echo htmlspecialchars($title); ?>" required>
</div>

<div class="col-md-4">
<label class="form-label">Duration</label>
<input type="text" name="duration" class="form-control form-control-sm"
value="<?php echo htmlspecialchars($duration); ?>" required>
</div>

<div class="col-md-4 d-flex align-items-end">
<button class="btn btn-primary btn-sm">Update</button>
</div>

</div>

<div class="mt-3">
<label>Description</label>
<textarea name="description" class="form-control form-control-sm" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
</div>

</form>

</div>



<!-- Assignments -->

<div class="card p-3 mb-3">

<div class="section-header">

<h5>Assignments</h5>

<a href="add_assignment.php?course_id=<?php echo $course_id; ?>" class="btn btn-success btn-sm">
+ Add
</a>

</div>

<table class="table table-sm table-striped">

<thead class="table-light">

<tr>
<th>Title</th>
<th>Deadline</th>
<th width="150">Action</th>
</tr>

</thead>

<tbody>

<?php while($a = $assignments->fetch_assoc()): ?>

<tr>

<td><?php echo htmlspecialchars($a['title']); ?></td>

<td><?php echo htmlspecialchars($a['deadline']); ?></td>

<td>

<a href="edit_assignment.php?id=<?php echo $a['id']; ?>" class="btn btn-info btn-sm">Edit</a>

<a href="delete_assignment.php?id=<?php echo $a['id']; ?>" 
class="btn btn-danger btn-sm"
onclick="return confirm('Delete assignment?')">

Delete

</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>



<!-- Videos -->

<div class="card p-3">

<div class="section-header">

<h5>Course Videos</h5>

<a href="add_course_video.php?course_id=<?php echo $course_id; ?>" class="btn btn-success btn-sm">
+ Add
</a>

</div>

<table class="table table-sm table-striped">

<thead class="table-light">

<tr>
<th>Title</th>
<th>Order</th>
<th width="150">Action</th>
</tr>

</thead>

<tbody>

<?php while($v = $videos->fetch_assoc()): ?>

<tr>

<td><?php echo htmlspecialchars($v['title']); ?></td>

<td><?php echo htmlspecialchars($v['sort_order']); ?></td>

<td>

<a href="edit_video.php?id=<?php echo $v['id']; ?>" class="btn btn-info btn-sm">Edit</a>

<a href="delete_video.php?id=<?php echo $v['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete video?')">

Delete

</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>


</div>

</body>
</html>
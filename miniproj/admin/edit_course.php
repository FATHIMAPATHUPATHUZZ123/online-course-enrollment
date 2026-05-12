<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
include '../database.php';

if(!isset($_GET['id'])) { header("Location: view_courses.php"); exit; }

$id=intval($_GET['id']);
$stmt=$conn->prepare("SELECT id,title,duration,status,instructor_id FROM courses WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result=$stmt->get_result();
$course=$result->fetch_assoc();
$stmt->close();

$instructors=$conn->query("SELECT id,name FROM instructors ORDER BY name ASC");

if($_SERVER['REQUEST_METHOD']=='POST'){
$title=trim($_POST['title']);
$duration=trim($_POST['duration']);
$status=$_POST['status'];
$instructor_id=intval($_POST['instructor_id']);

$stmt=$conn->prepare("UPDATE courses SET title=?,duration=?,status=?,instructor_id=? WHERE id=?");
$stmt->bind_param("sssii",$title,$duration,$status,$instructor_id,$id);
$stmt->execute();
$stmt->close();
header("Location: view_courses.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Course</title>
<style>
body{font-family:'Poppins',sans-serif;background:#f0f8ff;padding:30px;}
form{background:white;padding:20px;border-radius:8px;width:400px;}
label{display:block;margin-top:10px;}
input,select{width:100%;padding:8px;margin-top:5px;}
button{margin-top:15px;padding:8px 12px;background:#4da3ff;color:white;border:none;border-radius:4px;cursor:pointer;}
button:hover{background:#1a75ff;}
</style>
</head>
<body>
<h2>Edit Course</h2>
<form method="post">
<label>Title:<input type="text" name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required></label>
<label>Duration:<input type="text" name="duration" value="<?php echo htmlspecialchars($course['duration']); ?>" required></label>
<label>Status:<select name="status">
<option value="active" <?php echo $course['status']=='active'?'selected':'';?>>Active</option>
<option value="inactive" <?php echo $course['status']=='inactive'?'selected':'';?>>Inactive</option>
</select></label>
<label>Instructor:<select name="instructor_id">
<?php while($inst=$instructors->fetch_assoc()): ?>
<option value="<?php echo $inst['id'];?>" <?php echo $course['instructor_id']==$inst['id']?'selected':'';?>><?php echo htmlspecialchars($inst['name']);?></option>
<?php endwhile;?>
</select></label>
<button type="submit">Update Course</button>
</form>
<p><a href="view_courses.php">← Back to Courses</a></p>
</body>
</html>

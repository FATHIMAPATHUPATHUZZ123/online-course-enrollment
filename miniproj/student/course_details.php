```php
<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    die("Please <a href='login.student.php'>login</a>.");
}

$student_id = (int)$_SESSION['student_id'];
$course_id  = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

/* COURSE */
$stmt = $conn->prepare("SELECT * FROM courses WHERE id=?");
$stmt->bind_param("i",$course_id);
$stmt->execute();
$course=$stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$course){
die("Course not found");
}

/* ENROLLMENT */
$stmt=$conn->prepare("SELECT enrolled_at FROM enrollments WHERE student_id=? AND course_id=?");
$stmt->bind_param("ii",$student_id,$course_id);
$stmt->execute();
$enroll=$stmt->get_result()->fetch_assoc();
$stmt->close();

$is_enrolled=$enroll?true:false;
$enrolled_at=$enroll?$enroll['enrolled_at']:null;

/* DEADLINE */
$deadline=null;
$remaining_days=null;

if($enrolled_at){
$deadline=date("Y-m-d",strtotime($enrolled_at." +30 days"));
$today=date("Y-m-d");
$remaining_days=ceil((strtotime($deadline)-strtotime($today))/86400);
}

/* ASSIGNMENTS */
$stmt=$conn->prepare("
SELECT a.*,s.id AS submission_id
FROM assignments a
LEFT JOIN submissions s
ON a.id=s.assignment_id AND s.student_id=?
WHERE a.course_id=?
");

$stmt->bind_param("ii",$student_id,$course_id);
$stmt->execute();
$res=$stmt->get_result();
$stmt->close();

$assignment_list=array();
$submitted_assignments=0;

while($r=$res->fetch_assoc()){
$assignment_list[]=$r;
if($r['submission_id']){
$submitted_assignments++;
}
}

/* VIDEOS */
$stmt=$conn->prepare("SELECT * FROM course_videos WHERE course_id=? ORDER BY sort_order");
$stmt->bind_param("i",$course_id);
$stmt->execute();
$res=$stmt->get_result();
$stmt->close();

$video_list=array();
$watched_videos=0;

while($v=$res->fetch_assoc()){

$video_list[]=$v;

$s=$conn->prepare("SELECT id FROM video_progress WHERE student_id=? AND video_id=? AND watched=1");
$s->bind_param("ii",$student_id,$v['id']);
$s->execute();

if($s->get_result()->num_rows>0){
$watched_videos++;
}

$s->close();
}

/* PROGRESS */
$total_items=count($assignment_list)+count($video_list);

$progress=0;

if($total_items>0){
$progress=round((($submitted_assignments+$watched_videos)/$total_items)*100);
}

/* CERTIFICATE REQUEST */
$stmt=$conn->prepare("
SELECT status FROM certificate_requests
WHERE student_id=? AND course_id=? LIMIT 1
");

$stmt->bind_param("ii",$student_id,$course_id);
$stmt->execute();
$cert=$stmt->get_result()->fetch_assoc();
$stmt->close();

/* CERTIFICATE FILE */
$stmt=$conn->prepare("
SELECT certificate_file FROM certificates
WHERE student_id=? AND course_id=? LIMIT 1
");

$stmt->bind_param("ii",$student_id,$course_id);
$stmt->execute();
$cert_file=$stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">
<title><?php echo htmlspecialchars($course['title']); ?></title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>

body{
margin:0;
background:#eef3f8;
font-family:Arial;
}

.sidebar{
width:250px;
position:fixed;
top:0;
bottom:0;
background:#1e3a5f;
padding:20px;
color:white;
}

.sidebar .box{
background:#2d4f7a;
padding:12px;
border-radius:8px;
margin-bottom:15px;
}

.main{
margin-left:250px;
padding:30px;
}

.card{
background:white;
border-radius:10px;
box-shadow:0 4px 10px rgba(0,0,0,0.05);
margin-bottom:25px;
border:none;
}

.video-box{
max-width:520px;
}

video{
width:100%;
height:280px;
border-radius:8px;
background:black;
}

</style>

</head>

<body>

<div class="sidebar">

<h5><?php echo htmlspecialchars($course['title']); ?></h5>

<div class="box">

<strong>Progress</strong>

<div class="progress mt-2">

<div class="progress-bar bg-success"
style="width:<?php echo $progress;?>%">

<?php echo $progress;?>%

</div>

</div>

</div>

<div class="box">

📘 Assignments: <?php echo count($assignment_list);?><br>
✅ Submitted: <?php echo $submitted_assignments;?>

</div>

<div class="box">

🎥 Videos: <?php echo count($video_list);?><br>
👁 Watched: <?php echo $watched_videos;?>

</div>

<a href="view_courses.php" class="btn btn-light w-100 mb-2">
← All Courses
</a>

<?php if($is_enrolled){ ?>

<a href="unenroll.php?course_id=<?php echo $course_id; ?>"
class="btn btn-danger w-100"
onclick="return confirm('Are you sure you want to unenroll from this course?');">
❌ Unenroll
</a>

<?php } ?>

</div>

<div class="main">

<?php if($is_enrolled){ ?>

<div class="alert alert-success">

<strong>✔ Enrolled</strong><br>

You enrolled on:
<?php echo date("d M Y", strtotime($enrolled_at)); ?>

</div>

<div class="card p-4">

<h3><?php echo htmlspecialchars($course['title']);?></h3>

<p><?php echo nl2br(htmlspecialchars($course['description']));?></p>

</div>

<!-- ACTION BUTTONS -->

<div class="card p-3">

<div class="d-flex flex-wrap gap-2">

<a href="submit_review.php?course_id=<?php echo $course_id;?>" 
class="btn btn-outline-success btn-sm">
⭐ Review
</a>

<a href="view_reviews.php?course_id=<?php echo $course_id;?>" 
class="btn btn-outline-primary btn-sm">
👀 Reviews
</a>

<?php if($progress==100){ ?>

<?php if(!$cert){ ?>

<a href="student_request_certificate.php?course_id=<?php echo $course_id;?>" 
class="btn btn-outline-warning btn-sm">
🎓 Certificate
</a>

<?php } elseif($cert['status']=="pending"){ ?>

<span class="badge bg-warning text-dark">
⏳ Certificate Pending
</span>

<?php } elseif($cert['status']=="rejected"){ ?>

<span class="badge bg-danger">
❌ Rejected
</span>

<?php } elseif($cert['status']=="approved" && $cert_file){ ?>

<a class="btn btn-outline-success btn-sm"
href="../certificates/<?php echo $cert_file['certificate_file'];?>" download>
⬇ Download Certificate
</a>

<?php } ?>

<?php } ?>

</div>

</div>

<!-- ASSIGNMENTS -->

<div class="card p-4">

<h5>Assignments</h5>

<table class="table table-hover">

<tr>
<th>Title</th>
<th>Description</th>
<th>File</th>
<th>Deadline</th>
<th>Remaining</th>
<th>Action</th>
</tr>

<?php

if($assignment_list){

foreach($assignment_list as $a){

echo "<tr>";

echo "<td>".htmlspecialchars($a['title'])."</td>";

echo "<td>".htmlspecialchars($a['description'])."</td>";

if(!empty($a['file_path'])){
echo "<td>
<a class='btn btn-outline-secondary btn-sm'
href='../uploads/".$a['file_path']."' target='_blank'>
Download
</a>
</td>";
}else{
echo "<td>No File</td>";
}

echo "<td>".$deadline."</td>";

if($remaining_days>0){
echo "<td><span class='badge bg-success'>".$remaining_days." days left</span></td>";
}elseif($remaining_days==0){
echo "<td><span class='badge bg-warning text-dark'>Last Day</span></td>";
}else{
echo "<td><span class='badge bg-danger'>Expired</span></td>";
}

echo "<td>";

if($a['submission_id']){

echo "<span class='badge bg-success'>Submitted</span> ";

echo "<a class='btn btn-info btn-sm ms-2'
href='view_submission.php?assign_id=".$a['id']."&course_id=".$course_id."'>
View
</a>";

}else{

echo "<a class='btn btn-primary btn-sm'
href='submit_assignment.php?assign_id=".$a['id']."&course_id=".$course_id."'>
Submit
</a>";

}

echo "</td>";

echo "</tr>";

}

}else{

echo "<tr><td colspan='6'>No assignments</td></tr>";

}

?>

</table>

</div>

<!-- VIDEOS -->

<div class="card p-4">

<h5>Course Videos</h5>

<?php

if($video_list){

foreach($video_list as $v){

echo "

<div class='video-box mb-4'>

<h6>".htmlspecialchars($v['title'])."</h6>

<video controls
onended='markWatched(".$v['id'].")'
ontimeupdate='checkProgress(this,".$v['id'].")'>

<source src='../".$v['video_path']."' type='video/mp4'>

</video>

</div>";

}

}else{

echo "<p>No videos available.</p>";

}

?>

</div>

<?php } ?>

</div>

<script>

function markWatched(id){
var x=new XMLHttpRequest();
x.open("GET","mark_video_watched.php?video_id="+id,true);
x.send();
}

function checkProgress(video,id){
if(video.currentTime>=video.duration-1){
markWatched(id);
}
}

</script>

</body>
</html>
```


<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    die("Login required");
}

$student_id = $_SESSION['student_id'];

if (!isset($_GET['assign_id']) || !isset($_GET['course_id'])) {
    die("Assignment or Course ID missing.");
}

$assign_id = intval($_GET['assign_id']);
$course_id = intval($_GET['course_id']);

/* -------------------- FUNCTION TO CONVERT MARKS TO GRADE -------------------- */
function calculateGrade($marks) {
    if ($marks >= 90) return 'A+';
    if ($marks >= 80) return 'A';
    if ($marks >= 70) return 'B';
    if ($marks >= 60) return 'C';
    if ($marks >= 50) return 'D';
    return 'F';
}

/* -------------------- GET ENROLL DATE -------------------- */
$stmt = $conn->prepare("SELECT enrolled_at FROM enrollments WHERE student_id=? AND course_id=?");
$stmt->bind_param("ii",$student_id,$course_id);
$stmt->execute();
$enroll = $stmt->get_result()->fetch_assoc();
$stmt->close();

$deadline = null;

if($enroll){
$deadline = date("Y-m-d H:i:s", strtotime($enroll['enrolled_at']." +30 days"));
}

/* -------------------- FETCH SUBMISSION -------------------- */
$stmt = $conn->prepare("SELECT * FROM submissions WHERE assignment_id=? AND student_id=?");
$stmt->bind_param("ii", $assign_id, $student_id);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* -------------------- FETCH ASSIGNMENT DETAILS -------------------- */
$assign_q = $conn->prepare("SELECT title FROM assignments WHERE id=?");
$assign_q->bind_param("i", $assign_id);
$assign_q->execute();
$assign_res = $assign_q->get_result();
$assign_row = $assign_res->fetch_assoc();
$assign_q->close();

$assignment_title = $assign_row['title'];

/* -------------------- CHECK LATE -------------------- */
$late = false;

if ($submission && $deadline && $submission['submitted_at'] > $deadline) {
    $late = true;
}

/* -------------------- SAFE GRADE & FEEDBACK -------------------- */

if (isset($submission['grade']) && $submission['grade'] !== null) {
    $marks = $submission['grade'];
    $grade = calculateGrade($marks) . " ($marks/100)";
} else {
    $grade = 'Not graded yet';
}

$feedback = isset($submission['feedback']) ? $submission['feedback'] : 'No feedback yet';

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">
<title>View Submission</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>

body{
background:#eef3f8;
font-family:Arial;
}

.container-box{
max-width:700px;
margin:80px auto;
}

.card{
border:none;
border-radius:12px;
box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

</style>

</head>

<body>

<div class="container container-box">

<div class="card p-4">

<h4 class="mb-3">📄 Assignment Submission</h4>

<p><strong>Assignment:</strong>
<?php echo htmlspecialchars($assignment_title); ?>
</p>

<p><strong>Deadline:</strong>
<?php echo date("d M Y H:i", strtotime($deadline)); ?>
</p>

<?php if ($submission){ ?>

<p><strong>File:</strong>

<a href="../uploads/<?php echo htmlspecialchars($submission['file_path']); ?>" 
target="_blank"
class="btn btn-sm btn-outline-primary">
Download File
</a>

</p>

<p><strong>Submitted At:</strong>
<?php echo date("d M Y H:i", strtotime($submission['submitted_at'])); ?>
</p>

<?php if ($late){ ?>

<div class="alert alert-danger">
⚠ Late Submission
</div>

<?php } else { ?>

<div class="alert alert-success">
✔ Submitted On Time
</div>

<?php } ?>

<hr>

<p><strong>Grade:</strong>
<?php echo htmlspecialchars($grade); ?>
</p>

<p><strong>Instructor Feedback:</strong></p>

<div class="border p-3 bg-light rounded">
<?php echo nl2br(htmlspecialchars($feedback)); ?>
</div>

<hr>

<a href="submit_assignment.php?assign_id=<?php echo $assign_id; ?>&course_id=<?php echo $course_id; ?>" 
class="btn btn-warning w-100">
Resubmit Assignment
</a>

<?php } else { ?>

<div class="alert alert-warning">
No submission found.
</div>

<a href="submit_assignment.php?assign_id=<?php echo $assign_id; ?>&course_id=<?php echo $course_id; ?>" 
class="btn btn-primary w-100">
Submit Assignment
</a>

<?php } ?>

<hr>

<a href="course_details.php?course_id=<?php echo $course_id; ?>" 
class="btn btn-secondary w-100">
⬅ Back to Course
</a>

</div>

</div>

</body>
</html>
```

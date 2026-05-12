<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.student.php");
    exit;
}

$student_id = $_SESSION['student_id'];

if (!isset($_GET['course_id'])) {
    die("Course ID is missing.");
}

$course_id = intval($_GET['course_id']);

/* Get course title */
$course_query = $conn->prepare("SELECT title FROM courses WHERE id=?");

if(!$course_query){
    die("Query Error: " . $conn->error);
}

$course_query->bind_param("i",$course_id);
$course_query->execute();
$course_result = $course_query->get_result();
$course = $course_result->fetch_assoc();

$course_name = $course['title'];

/* Handle review submit */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$review_text = trim($_POST['review']);

if ($review_text === '') {

$error = "Review cannot be empty.";

} else {

$stmt = $conn->prepare("INSERT INTO reviews (student_id, course_id, review) VALUES (?, ?, ?)");

$stmt->bind_param("iis", $student_id, $course_id, $review_text);

if ($stmt->execute()) {

$success = "Review submitted successfully.";

} else {

$error = "Error submitting review.";

}

$stmt->close();

}

}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Submit Review</title>

<style>

body{
font-family: Arial;
background:#f2f2f2;
height:100vh;
display:flex;
justify-content:center;
align-items:center;
}

.container{
background:white;
padding:30px;
width:420px;
border-radius:10px;
box-shadow:0 0 12px rgba(0,0,0,0.2);
text-align:center;
}

h2{
color:#333;
margin-bottom:15px;
}

textarea{
width:100%;
padding:10px;
border-radius:6px;
border:1px solid #ccc;
resize:none;
}

button{
margin-top:10px;
background:#007bff;
color:white;
border:none;
padding:10px 18px;
border-radius:6px;
cursor:pointer;
}

button:hover{
background:#0056b3;
}

.message{
margin-top:10px;
font-weight:bold;
}

.success{
color:green;
}

.error{
color:red;
}

a{
display:inline-block;
margin-top:15px;
text-decoration:none;
color:#007bff;
}

a:hover{
text-decoration:underline;
}

</style>

</head>

<body>

<div class="container">

<h2>Submit Review for: <?php echo htmlspecialchars($course_name); ?></h2>

<?php if (!empty($error)) { echo "<p class='message error'>$error</p>"; } ?>

<?php if (!empty($success)) { echo "<p class='message success'>$success</p>"; } ?>

<form method="post">

<textarea name="review" rows="5" placeholder="Write your review here..." required></textarea>

<br>

<button type="submit">Submit Review</button>

</form>

<p><a href="view_courses.php">⬅ Back to Courses</a></p>

</div>

</body>
</html>
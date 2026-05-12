<?php
session_start();
if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

include '../database.php';

$instructor_id = $_SESSION['instructor_id'];
$message = "";

// Fetch courses of this instructor
$courses = $conn->query("SELECT * FROM courses WHERE instructor_id = $instructor_id");

// Build array of courses
$courses_array = array();
while($row = $courses->fetch_assoc()){
    $courses_array[$row['id']] = $row;
}

// Handle form submission
if (isset($_POST['add_assignment'])) {

    $course_id = intval($_POST['course_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    /* Automatically set deadline = today + 30 days */
    $deadline = date('Y-m-d', strtotime('+30 days'));

    $file_path = NULL;

    // Handle optional file upload
    if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == 0) {

        $ext = pathinfo($_FILES['assignment_file']['name'], PATHINFO_EXTENSION);
        $filename = 'assignment_' . time() . '.' . $ext;

        $target_dir = "../uploads/";
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $target_file)) {
            $file_path = $filename;
        } else {
            $message = "Failed to upload assignment file.";
        }
    }

    if ($message == "") {

        $stmt = $conn->prepare("INSERT INTO assignments (course_id, title, description, file_path, deadline) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $course_id, $title, $description, $file_path, $deadline);

        if ($stmt->execute()) {
            $message = "Assignment added successfully! (Deadline automatically set to 30 days)";
        } else {
            $message = "Database error: " . $conn->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Assignment</title>

<style>

body{
font-family:Arial;
padding:20px;
background:#ffffff;
}

h2{
color:#000080;
}

label{
display:block;
margin:10px 0 5px;
font-weight:bold;
}

input,textarea,select{
width:100%;
padding:8px;
border:1px solid #000080;
border-radius:3px;
font-size:14px;
}

button{
margin-top:15px;
padding:8px 15px;
background:#000080;
color:white;
border:none;
border-radius:3px;
cursor:pointer;
}

button:hover{
background:#0000b0;
}

.message{
margin:15px 0;
font-weight:bold;
color:green;
}

a.back{
display:inline-block;
margin-top:15px;
text-decoration:none;
color:#000080;
font-weight:bold;
}

a.back:hover{
text-decoration:underline;
}

textarea[disabled]{
background:#f0f0f0;
}

</style>
</head>

<body>

<h2>Add Assignment</h2>

<?php if($message) { ?>
<div class="message"><?php echo htmlspecialchars($message); ?></div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">

<?php if(!empty($courses_array)) { ?>

<?php $first_course = current($courses_array); ?>

<label>Course</label>

<select name="course_id" id="course_select" required>

<?php foreach($courses_array as $id=>$course) { ?>

<option value="<?php echo $id; ?>">
<?php echo htmlspecialchars($course['title']); ?>
</option>

<?php } ?>

</select>

<label>Course Description</label>

<textarea id="course_description" rows="3" disabled>
<?php echo htmlspecialchars($first_course['description']); ?>
</textarea>

<?php } else { ?>

<p style="color:red;">No course found. Please create a course first.</p>

<?php } ?>

<label>Assignment Title</label>
<input type="text" name="title" required>

<label>Assignment Description</label>
<textarea name="description" rows="4"></textarea>

<label>Attachment (optional)</label>
<input type="file" name="assignment_file">

<button type="submit" name="add_assignment">Add Assignment</button>

</form>

<a class="back" href="dashboard_instructor.php">⬅ Back to Dashboard</a>

<script>

const courseDescriptions = {

<?php foreach($courses_array as $id=>$course){

echo $id . ":" . json_encode($course['description']) . ",";

} ?>

};

const select = document.getElementById('course_select');
const description = document.getElementById('course_description');

select.addEventListener('change',function(){

const selectedId=this.value;

description.value = courseDescriptions[selectedId] || '';

});

</script>

</body>
</html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.student.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$message = "";

// Fetch student data
$sql = "SELECT * FROM students WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if($_SERVER['REQUEST_METHOD']=="POST"){

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

if(!empty($password)){

$hash = md5($password);

$update = $conn->prepare("UPDATE students SET name=?,email=?,password=? WHERE id=?");
$update->bind_param("sssi",$name,$email,$hash,$student_id);

}else{

$update = $conn->prepare("UPDATE students SET name=?,email=? WHERE id=?");
$update->bind_param("ssi",$name,$email,$student_id);

}

if($update->execute()){
$message = "Profile updated successfully!";
$_SESSION['student_name'] = $name;
}else{
$message = "Failed to update profile.";
}

}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Edit Profile</title>

<style>

body{
font-family:Arial;
background:#f2f2f2;
display:flex;
flex-direction:column;
align-items:center;
}

.container{
margin-top:40px;
background:white;
padding:25px;
border-radius:10px;
box-shadow:0 0 10px #ccc;
width:320px;
}

h2{
text-align:center;
margin-bottom:10px;
}

.message{
text-align:center;
color:green;
font-weight:bold;
margin-bottom:10px;
}

input{
width:100%;
padding:8px;
margin:8px 0;
}

button{
width:100%;
padding:10px;
background:#007bff;
color:white;
border:none;
border-radius:5px;
}

a{
text-decoration:none;
color:#007bff;
}

</style>

</head>

<body>

<div class="container">

<h2>Edit Profile</h2>

<?php
if($message!=""){
echo "<div class='message'>$message</div>";
}
?>

<form method="POST" autocomplete="off">

<input type="text" name="fakeuser" style="display:none">
<input type="password" name="fakepass" style="display:none">

<label>Name:</label>
<input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>

<label>Email:</label>
<input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required autocomplete="off">

<label>New Password (optional):</label>
<input type="password" name="password" placeholder="Leave empty to keep old password" autocomplete="new-password">

<button type="submit">Update Profile</button>

</form>

<p style="text-align:center;margin-top:15px;">
<a href="view_courses.php">Back to My Courses</a>
</p>

</div>

</body>
</html>
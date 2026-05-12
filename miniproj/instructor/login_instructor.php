<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../database.php';

if (isset($_SESSION['instructor_id'])) {
    header("Location: dashboard_instructor.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Instructor Login</title>

<style>

body{
font-family: Arial, sans-serif;
background-color:#eaf4fc;
}

form{
width:400px;
margin:80px auto;
background:#fff;
padding:25px;
border-radius:10px;
box-shadow:0 0 10px #b3d9ff;
}

h2{
text-align:center;
color:#004080;
}

label{
display:block;
margin-top:10px;
color:#004080;
}

input{
width:100%;
padding:10px;
margin-top:5px;
border:1px solid #ccc;
border-radius:5px;
}

button{
background:#007bff;
color:white;
border:none;
padding:10px;
border-radius:5px;
margin-top:15px;
width:100%;
}

button:hover{
background:#0056b3;
cursor:pointer;
}

.register-link{
text-align:center;
margin-top:15px;
}

.register-link a{
color:#007bff;
text-decoration:none;
}

.register-link a:hover{
text-decoration:underline;
}

.error-msg{
color:red;
text-align:center;
margin-top:10px;
}

</style>

</head>

<body>

<form method="POST" autocomplete="off">

<h2>Instructor Login</h2>

<!-- hidden fields to disable browser autofill -->
<input type="text" name="fakeuser" style="display:none">
<input type="password" name="fakepassword" style="display:none">

<label>Email:</label>
<input type="email" name="email" required autocomplete="off">

<label>Password:</label>
<input type="password" name="password" required autocomplete="new-password">

<button type="submit" name="login">Login</button>

<div class="register-link">
Don't have an account? <a href="register_instructor.php">Register here</a><br>
<a href="../index.html">Back to Home</a>
</div>

</form>

<?php

if (isset($_POST['login'])) {

$email = mysqli_real_escape_string($conn,$_POST['email']);
$password = md5($_POST['password']);

$stmt = $conn->prepare("SELECT * FROM instructors WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();
$instructor = $result->fetch_assoc();

if($instructor && $instructor['password']===$password){

$_SESSION['instructor_id']=$instructor['id'];
$_SESSION['instructor_name']=$instructor['name'];

header("Location: dashboard_instructor.php");
exit;

}else{

echo "<p class='error-msg'>Invalid Email or Password!</p>";

}

$stmt->close();

}

?>

</body>
</html>
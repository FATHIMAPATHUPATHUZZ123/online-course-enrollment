<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include '../database.php';

if($_SERVER['REQUEST_METHOD']=='POST')
{

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

if($name=="" || $email=="" || $password=="")
{
echo "<script>alert('Please fill all fields');window.location='register_instructor.php';</script>";
exit;
}

/* password encryption for PHP 5 */
$password_hash = md5($password);

/* check email in instructors */
$check1 = $conn->prepare("SELECT id FROM instructors WHERE email=?");
$check1->bind_param("s",$email);
$check1->execute();
$check1->store_result();

/* check email in students */
$check2 = $conn->prepare("SELECT id FROM students WHERE email=?");
$check2->bind_param("s",$email);
$check2->execute();
$check2->store_result();

/* if email exists */
if($check1->num_rows>0 || $check2->num_rows>0)
{
echo "<script>alert('Email already exists');window.location='register_instructor.php';</script>";
exit;
}

$check1->close();
$check2->close();

/* insert instructor */
$stmt=$conn->prepare("INSERT INTO instructors(name,email,password,created_at) VALUES(?,?,?,NOW())");
$stmt->bind_param("sss",$name,$email,$password_hash);

if($stmt->execute())
{
echo "<script>alert('Registration successful!');window.location='login_instructor.php';</script>";
}
else
{
echo "<script>alert('Registration failed');</script>";
}

$stmt->close();
$conn->close();

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Instructor Registration</title>

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

.login-link{
text-align:center;
margin-top:15px;
}

.login-link a{
color:#007bff;
text-decoration:none;
}

.login-link a:hover{
text-decoration:underline;
}

</style>

</head>

<body>

<form method="POST" autocomplete="off">

<h2>Instructor Registration</h2>

<label>Name:</label>
<input type="text" name="name" required autocomplete="off">

<label>Email:</label>
<input type="email" name="email" required autocomplete="off">

<label>Password:</label>
<input type="password" name="password" required autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false">

<button type="submit">Register</button>

<div class="login-link">
Already registered? <a href="login_instructor.php">Login here</a><br>
<a href="../index.html">Back to Home</a>
</div>

</form>

</body>
</html>
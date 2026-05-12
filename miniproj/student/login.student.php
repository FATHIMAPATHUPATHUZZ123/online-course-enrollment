<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../database.php';

if (isset($_SESSION['student_id'])) {
    header("Location: view_courses.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
body {
font-family: 'Poppins', sans-serif;
height: 100vh;
display: flex;
justify-content: center;
align-items: center;
background: linear-gradient(rgba(255,255,255,0.75), rgba(255,255,255,0.75)),
url('https://images.unsplash.com/photo-1581091215360-7f2a9fa34871?auto=format&fit=crop&w=1950&q=80')
no-repeat center center/cover;
}

.login-card {
width: 400px;
background: #ffffffcc;
border-radius: 20px;
padding: 40px 30px;
box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.login-title {
text-align: center;
font-size: 2rem;
font-weight: 600;
color: #0073e6;
margin-bottom: 25px;
}

.form-label {
font-weight: 500;
color: #0059b3;
}

.form-control {
border-radius: 12px;
border: 1px solid #a3d2ff;
padding: 12px;
}

.btn-primary {
background: linear-gradient(90deg,#3399ff,#66ccff);
border:none;
font-weight:600;
padding:12px;
border-radius:12px;
}

.message {
text-align: center;
margin-top: 15px;
font-weight: 500;
font-size: 0.95rem;
}

.error { color: red; }

.register-link {
display: block;
text-align: center;
margin-top: 15px;
font-size: 0.9rem;
color: #0073e6;
text-decoration: none;
}

.register-link:hover { text-decoration: underline; }

a.course-link {
display:inline-block;
margin-top:15px;
padding:8px 20px;
background-color:#66ccff;
color:white;
border-radius:12px;
text-decoration:none;
font-weight:500;
}

a.course-link:hover { background-color:#3399ff; }
</style>
</head>

<body>

<div class="login-card">

<div class="login-title">Student Login</div>

<form method="POST" autocomplete="off">

<!-- Hidden fake inputs to stop browser autofill -->
<input type="text" name="fakeuser" style="display:none">
<input type="password" name="fakepass" style="display:none">

<div class="mb-3">
<label class="form-label">Email</label>
<input type="email"
name="email"
class="form-control"
placeholder="Enter your email"
autocomplete="off"
required>
</div>

<div class="mb-3">
<label class="form-label">Password</label>
<input type="password"
name="password"
class="form-control"
placeholder="Enter your password"
autocomplete="new-password"
required>
</div>

<button type="submit" name="login" class="btn btn-primary w-100">
Login
</button>

<a href="registerstudent.php" class="register-link">
Don’t have an account? Register here
</a>

</form>

<?php

if (isset($_POST['login'])) {

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = md5($_POST['password']);

$query = "SELECT * FROM students WHERE email='$email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {

$student = mysqli_fetch_assoc($result);

if ($student['password'] == $password) {

$_SESSION['student_id'] = $student['id'];
$_SESSION['student_name'] = $student['name'];

header("Location: view_courses.php");
exit;

} else {

echo "<p class='message error'>Incorrect password!</p>";

}

} else {

echo "<p class='message error'>Invalid email or password!</p>";

}

}

echo "<a href='../index.html' class='course-link' style='display:block; margin-top:15px; text-align:center;'>⬅ Back to Home</a>";

?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
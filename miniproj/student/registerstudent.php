<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-image: 
        linear-gradient(rgba(255, 255, 255, 0.7), rgba(224, 240, 255, 0.7)),
        url('bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;

    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Poppins', sans-serif;
}

.card {
    width: 400px;
    border: none;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(8px);
    box-shadow: 0 8px 25px rgba(0, 51, 102, 0.25);
}

.card-header {
    background: linear-gradient(90deg, #4da3ff, #80c1ff);
    color: white;
    text-align: center;
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    font-weight: 600;
    font-size: 22px;
}

.form-label { color: #003366; font-weight: 500; }

.btn-primary {
    background: linear-gradient(to right, #66b3ff, #4da3ff);
    border: none;
    border-radius: 10px;
}

.btn-secondary {
    background: linear-gradient(to right, #cccccc, #999999);
    border: none;
    border-radius: 10px;
    color: #003366;
}

.message { margin-top: 15px; font-weight: 500; text-align: center; }
.success { color: green; }
.error { color: red; }

.login-link { display: block; margin-top: 10px; text-align: center; }

.login-link a {
    color: #007bff;
    text-decoration: none;
}

.back-home-btn {
    display: block;
    width: 100%;
    margin-top: 15px;
    padding: 10px;
    font-weight: 600;
    color: white;
    background: #4da3ff;
    border-radius: 10px;
    text-align: center;
    text-decoration: none;
}
</style>

</head>

<body>

<div class="card">

<div class="card-header">
Student Registration
</div>

<div class="card-body">

<form method="POST" autocomplete="off">

<div class="mb-3">
<label class="form-label">Name</label>
<input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
</div>

<div class="mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
</div>

<div class="mb-3">
<label class="form-label">Password</label>
<input type="password" name="password" class="form-control"
placeholder="Create a strong password"
autocomplete="new-password"
required>
</div>

<button type="submit" name="register" class="btn btn-primary w-100">
Register
</button>

<a href="login.student.php" class="btn btn-secondary w-100 mt-2">
Already Registered? Login
</a>

</form>

<?php

if(isset($_POST['register'])){

$name = mysqli_real_escape_string($conn,$_POST['name']);
$email = mysqli_real_escape_string($conn,$_POST['email']);

/* PHP 5 compatible password encryption */
$password = md5($_POST['password']);

$check = "SELECT * FROM students WHERE email='$email'";
$result = mysqli_query($conn,$check);

if(mysqli_num_rows($result) > 0){

echo "<p class='message error'>Email already registered!</p>";

}
else{

$insert = "INSERT INTO students(name,email,password)
VALUES('$name','$email','$password')";

if(mysqli_query($conn,$insert)){

echo "<p class='message success'>Registration successful!</p>";

echo "<div class='login-link'>
<a href='login.student.php'>Click here to Login</a>
</div>";

}
else{

echo "<p class='message error'>Error: ".mysqli_error($conn)."</p>";

}

}

}

?>

<a href="../index.html" class="back-home-btn">
⬅ Back to Home
</a>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
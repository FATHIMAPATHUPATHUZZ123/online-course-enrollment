<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../database.php';
require '../PHPMailer/class.phpmailer.php';
require '../PHPMailer/class.smtp.php';

$msg = '';

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email exists
    $res = mysqli_query($conn, "SELECT * FROM students WHERE email='$email'");
    if (mysqli_num_rows($res) == 1) {
        $student = mysqli_fetch_assoc($res);

        // Generate token (compatible with old PHP)
        $token = md5(uniqid(rand(), true));
        $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        // Store token in DB
        mysqli_query($conn, "UPDATE students SET reset_token='$token', token_expiry='$expiry' WHERE id=".$student['id']);

        // Send email via PHPMailer
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // change if needed
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // your email
        $mail->Password = 'your_email_password'; // your email app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your_email@gmail.com', 'Course Platform');
        $mail->addAddress($email, $student['name']);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "Hi {$student['name']},<br><br>
            Click the link below to reset your password:<br>
            <a href='http://localhost/miniproj/student/reset_password.php?token=$token'>Reset Password</a><br><br>
            This link will expire in 30 minutes.<br><br>Thanks.";

        if ($mail->send()) {
            $msg = "<p class='text-success'>Reset link sent to your email!</p>";
        } else {
            $msg = "<p class='text-danger'>Failed to send email. Try again later.</p>";
        }

    } else {
        $msg = "<p class='text-danger'>Email not found!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.85)), url('bg.jpg') no-repeat center center/cover;
}
.card {
    width: 400px;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    background-color: #ffffffcc;
}
.card-header {
    font-size: 1.5rem;
    font-weight: 600;
    text-align: center;
    color: #0073e6;
    margin-bottom: 20px;
}
.form-control {
    border-radius: 12px;
}
.btn-primary {
    width: 100%;
    border-radius: 12px;
    background: linear-gradient(90deg, #3399ff, #66ccff);
    border: none;
    font-weight: 600;
}
.btn-primary:hover { background: linear-gradient(90deg, #1a75ff, #3399ff); }
a.back-login {
    display: block;
    margin-top: 15px;
    text-align: center;
    color: #3399ff;
    text-decoration: none;
}
a.back-login:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="card">
    <div class="card-header">Forgot Password</div>
    <form method="POST" action="">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your registered email" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Send Reset Link</button>
    </form>

    <?php if($msg) echo $msg; ?>

    <a href="login.student.php" class="back-login">⬅ Back to Login</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

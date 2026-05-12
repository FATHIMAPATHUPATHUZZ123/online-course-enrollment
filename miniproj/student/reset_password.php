<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../database.php';

if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

// Check token validity
$res = mysqli_query($conn, "SELECT * FROM students WHERE reset_token='$token' AND token_expiry >= NOW()");
if (mysqli_num_rows($res) != 1) {
    die("Invalid or expired token.");
}

$student = mysqli_fetch_assoc($res);

if (isset($_POST['reset'])) {
    $password = md5($_POST['password']);
    mysqli_query($conn, "UPDATE students SET password='$password', reset_token=NULL, token_expiry=NULL WHERE id=".$student['id']);
    echo "<p style='color:green;'>Password reset successfully! <a href='login.student.php'>Login now</a></p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
<h2>Reset Password for <?php echo htmlspecialchars($student['email']); ?></h2>

<form method="POST" action="">
    <label>New Password:</label>
    <input type="password" name="password" required>
    <button type="submit" name="reset">Reset Password</button>
</form>
</body>
</html>

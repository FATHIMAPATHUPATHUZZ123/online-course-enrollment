<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}

include '../database.php';

if(!isset($_GET['id'])){
    header("Location: view_students.php");
    exit;
}

$id = intval($_GET['id']);
$error = "";

// Fetch student info
$result = $conn->query("SELECT * FROM students WHERE id=$id");
if($result->num_rows == 0){
    header("Location: view_students.php");
    exit;
}
$student = $result->fetch_assoc();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    if(empty($name) || empty($email)){
        $error = "All fields are required.";
    } else {
        $conn->query("UPDATE students SET name='$name', email='$email' WHERE id=$id");
        header("Location: view_students.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Student</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
<style>
body { font-family: 'Poppins', sans-serif; background: #f0f8ff; display: flex; justify-content: center; padding: 50px; }
.form-box { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
h2 { color: #003366; margin-bottom: 20px; text-align: center; }
input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 6px; border: 1px solid #ccc; }
button { width: 100%; padding: 12px; background: #4da3ff; color: white; border: none; border-radius: 6px; cursor: pointer; }
button:hover { background: #3c8fe6; }
.error { color: red; font-size: 14px; margin-bottom: 10px; text-align: center; }
a { display: block; text-align: center; margin-top: 15px; color: #4da3ff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="form-box">
<h2>Edit Student</h2>
<?php if($error != "") echo "<div class='error'>$error</div>"; ?>
<form method="POST">
<input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($student['name']) ?>" required>
<input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($student['email']) ?>" required>
<button type="submit">Update Student</button>
</form>
<a href="view_students.php">← Back to Students</a>
</div>
</body>
</html>

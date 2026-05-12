<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include '../database.php';

if(!isset($_GET['id'])) {
    header("Location: view_students.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch student details
$stmt = $conn->prepare("SELECT id, name, email, status FROM students WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if(!$student) die("Student not found.");

if($_SERVER['REQUEST_METHOD']=='POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE students SET name=?, email=?, status=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: view_students.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Student</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
<style>
body { font-family: 'Poppins', sans-serif; background: #f0f8ff; padding: 30px; }
h2 { color: #003366; }
form { background: white; padding: 20px; border-radius: 8px; width: 400px; }
label { display: block; margin-top: 10px; }
input, select { width: 100%; padding: 8px; margin-top: 5px; }
button { margin-top: 15px; padding: 8px 12px; background: #4da3ff; color: white; border: none; border-radius: 4px; cursor: pointer; }
button:hover { background: #1a75ff; }
</style>
</head>
<body>
<h2>Edit Student</h2>
<form method="post">
    <label>Name:
        <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
    </label>
    <label>Email:
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
    </label>
    <label>Status:
        <select name="status">
            <option value="active" <?php echo $student['status']=='active'?'selected':''; ?>>Active</option>
            <option value="inactive" <?php echo $student['status']=='inactive'?'selected':''; ?>>Inactive</option>
        </select>
    </label>
    <button type="submit">Update Student</button>
</form>
<p><a href="view_students.php">← Back to Students</a></p>
</body>
</html>

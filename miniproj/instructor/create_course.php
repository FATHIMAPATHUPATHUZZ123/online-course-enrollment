<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../database.php';

// Check if instructor is logged in
if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

$instructor_id = intval($_SESSION['instructor_id']);
$message = "";
$error = "";

// Initialize variables
$title = '';
$description = '';
$duration = '';
$content = '';
$image_name = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Use isset() instead of ?? for compatibility with PHP 5.x
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $duration = isset($_POST['duration']) ? trim($_POST['duration']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../student/images/courses/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $tmpName = $_FILES['image']['tmp_name'];
        $originalName = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($ext, $allowed)) {
            $image_name = time() . '_' . preg_replace("/[^a-zA-Z0-9_-]/", "", pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $ext;
            if (!move_uploaded_file($tmpName, $uploadDir . $image_name)) {
                $error = "Failed to move uploaded image. Check folder permissions.";
            }
        } else {
            $error = "Invalid image type. Allowed: jpg, jpeg, png, gif.";
        }
    }

    // Insert course if no errors
    if (!$error) {
        if ($title === '') {
            $error = "Course title is required.";
        } else {
            $sql = "INSERT INTO courses (instructor_id, title, description, duration, content, image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                $error = "Prepare failed: " . mysqli_error($conn);
            } else {
                mysqli_stmt_bind_param($stmt, "isssss", $instructor_id, $title, $description, $duration, $content, $image_name);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header("Location: view_courses.php?msg=course_created");
                    exit;
                } else {
                    $error = "Database error: " . mysqli_stmt_error($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Create Course</title>
<style>
body { font-family: Arial, sans-serif; background: linear-gradient(180deg,#dbeafe,#bfdbfe); margin:0; padding:30px; }
.container { max-width:800px; margin:30px auto; background:#fff; padding:24px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.08);}
h2 { color:#1e3a8a; text-align:center; }
label{ display:block; margin-top:12px; font-weight:600; color:#1e3a8a; }
input[type=text], textarea, input[type=file] { width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; margin-top:6px; }
input[type=submit] { background:#1e40af; color:#fff; border:none; padding:12px 20px; border-radius:6px; cursor:pointer; margin-top:14px; width:100%; }
input[type=submit]:hover { background:#1d4ed8; }
.msg { padding:10px; border-radius:6px; margin-bottom:12px; }
.success { background:#e6ffed; color:#036b2f; border:1px solid #b6f0c9; }
.error { background:#fff0f0; color:#b71c1c; border:1px solid #f5c2c2; }
.back-btn { display:inline-block; margin-top:16px; text-decoration:none; background:#2563eb; color:#fff; padding:10px 18px; border-radius:6px; text-align:center; }
.back-btn:hover { background:#1d4ed8; }
</style>
</head>
<body>
<div class="container">
<h2>Create a New Course</h2>

<?php if ($message): ?>
    <div class="msg success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="msg error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
    <label for="title">Course Title</label>
    <input type="text" id="title" name="title" required maxlength="255" value="<?php echo htmlspecialchars($title); ?>">

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>

    <label for="duration">Duration (e.g. 4 weeks)</label>
    <input type="text" id="duration" name="duration" required maxlength="100" value="<?php echo htmlspecialchars($duration); ?>">

    <label for="content">Content (outline / topics)</label>
    <textarea id="content" name="content" rows="6"><?php echo htmlspecialchars($content); ?></textarea>

    <label for="image">Course Image (optional)</label>
    <input type="file" id="image" name="image" accept="image/*">

    <input type="submit" value="Create Course">
</form>

<!-- ✅ Back to Dashboard button -->
<a class="back-btn" href="dashboard_instructor.php">← Back to Dashboard</a>
</div>
</body>
</html>

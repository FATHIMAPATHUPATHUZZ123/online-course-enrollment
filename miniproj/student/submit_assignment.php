<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    die("Please login first.");
}

$student_id = $_SESSION['student_id'];

if (!isset($_GET['assign_id']) || !isset($_GET['course_id'])) {
    die("Assignment or Course ID missing.");
}

$assign_id = intval($_GET['assign_id']);
$course_id = intval($_GET['course_id']);
$error = "";

/* FILE UPLOAD */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['submission_file'])) {

    $file = $_FILES['submission_file'];

    if ($file['error'] === 0 && $file['size'] > 0) {

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'submission_' . time() . '_' . $student_id . '.' . $ext;

        $target_dir = "../uploads/";
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {

            $stmt = $conn->prepare("INSERT INTO submissions 
            (student_id, assignment_id, file_path, submitted_at) 
            VALUES (?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE file_path=?, submitted_at=NOW()");

            $stmt->bind_param("iiss", $student_id, $assign_id, $filename, $filename);

            if (!$stmt->execute()) {
                $error = "Database error: " . $stmt->error;
            } else {
                header("Location: view_assignments.php?course_id=$course_id");
                exit;
            }

            $stmt->close();

        } else {
            $error = "Failed to upload file.";
        }

    } else {
        $error = "No file selected or upload error.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Submit Assignment</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>

body{
background:#f1f5f9;
font-family:Arial;
}

.container-box{
max-width:500px;
margin:80px auto;
}

.card{
border:none;
border-radius:12px;
box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

</style>

</head>

<body>

<div class="container container-box">

<div class="card p-4">

<h4 class="mb-3">📄 Submit / Resubmit Assignment</h4>

<?php if ($error){ ?>

<div class="alert alert-danger">
<?php echo $error; ?>
</div>

<?php } ?>

<form method="post" enctype="multipart/form-data">

<div class="mb-3">

<label class="form-label">Select File</label>

<input type="file" name="submission_file" class="form-control" required>

</div>

<button type="submit" class="btn btn-primary w-100">
Submit Assignment
</button>

</form>

<hr>

<a href="view_assignments.php?course_id=<?php echo $course_id; ?>" class="btn btn-secondary w-100">
⬅ Back to Assignments
</a>

</div>

</div>

</body>
</html>
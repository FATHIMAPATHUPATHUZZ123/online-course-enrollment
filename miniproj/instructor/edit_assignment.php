<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Assignment ID missing.");
}

$assignment_id = (int)$_GET['id'];

// Fetch assignment
$stmt = $conn->prepare("SELECT * FROM assignments WHERE id=?");
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$assignment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$assignment) {
    die("Assignment not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    // Handle optional file upload
    $file_path = $assignment['file_path'];
    if (isset($_FILES['file']) && $_FILES['file']['name'] != "") {
        $target_dir = "../uploads/";
        $file_name = time() . "_" . basename($_FILES['file']['name']);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file_path = $file_name;
        }
    }

    $update = $conn->prepare("UPDATE assignments SET title=?, description=?, deadline=?, file_path=? WHERE id=?");
    $update->bind_param("ssssi", $title, $description, $deadline, $file_path, $assignment_id);
    $update->execute();
    $update->close();

    echo "<script>alert('Assignment updated!'); window.location.href='edit_course.php?id=".$assignment['course_id']."';</script>";
    exit;
}
?>

<h2>Edit Assignment</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($assignment['title']); ?>" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="4" required><?php echo htmlspecialchars($assignment['description']); ?></textarea><br><br>

    <label>Deadline:</label><br>
    <input type="date" name="deadline" value="<?php echo htmlspecialchars($assignment['deadline']); ?>"><br><br>

    <label>File: (optional)</label><br>
    <input type="file" name="file"><br>
    <?php if($assignment['file_path']) echo "Current file: <a href='../uploads/".$assignment['file_path']."' target='_blank'>".$assignment['file_path']."</a>"; ?><br><br>

    <button type="submit">Update Assignment</button>
</form>
<a href="edit_course.php?id=<?php echo $assignment['course_id']; ?>">&larr; Back to Course</a>

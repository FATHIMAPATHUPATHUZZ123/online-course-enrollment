<?php
session_start();
include '../database.php';

if(!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

if(!isset($_GET['submission_id'])) die("Submission ID missing");

$submission_id = intval($_GET['submission_id']);

if($_SERVER['REQUEST_METHOD']=='POST') {
    $grade = $_POST['grade'];
    $feedback = $_POST['feedback'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE submissions SET grade=?, feedback=?, status=? WHERE id=?");
    $stmt->bind_param("sssi", $grade, $feedback, $status, $submission_id);
    if($stmt->execute()) {
        $msg = "Submission updated successfully!";
    } else {
        $msg = "Error: ".$conn->error;
    }
    $stmt->close();
}

// Fetch submission info
$stmt2 = $conn->prepare("SELECT s.*, st.name, a.title FROM submissions s JOIN students st ON s.student_id=st.id JOIN assignments a ON s.assignment_id=a.id WHERE s.id=?");
$stmt2->bind_param("i", $submission_id);
$stmt2->execute();
$submission = $stmt2->get_result()->fetch_assoc();
$stmt2->close();
?>

<h2>Grade / Approve Submission</h2>

<p>Student: <?= htmlspecialchars($submission['name']); ?></p>
<p>Assignment: <?= htmlspecialchars($submission['title']); ?></p>

<form method="POST">
    <label>Grade:</label>
    <input type="text" name="grade" value="<?= htmlspecialchars($submission['grade']); ?>">

    <label>Feedback:</label>
    <textarea name="feedback"><?= htmlspecialchars($submission['feedback']); ?></textarea>

    <label>Status:</label>
    <select name="status">
        <option value="approved" <?= $submission['status']=='approved'?'selected':''; ?>>Approved</option>
        <option value="rejected" <?= $submission['status']=='rejected'?'selected':''; ?>>Rejected</option>
        <option value="pending" <?= !$submission['status']?'selected':''; ?>>Pending</option>
    </select>

    <button type="submit">Update</button>
</form>

<?php if(isset($msg)) echo "<p>$msg</p>"; ?>

<a href="view_submissions.php?assignment_id=<?= $submission['assignment_id']; ?>">⬅ Back to Submissions</a>

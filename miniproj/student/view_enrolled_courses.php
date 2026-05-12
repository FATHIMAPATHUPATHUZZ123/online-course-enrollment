<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login_student.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Handle messages
$msgText = '';
$color = 'green';
if (isset($_GET['msg'])) {
    switch($_GET['msg']) {
        case 'unenroll_success': $msgText = 'Successfully unenrolled from the course.'; break;
        case 'not_enrolled': $msgText = 'You are not enrolled in that course.'; $color='orange'; break;
        case 'already_enrolled': $msgText = 'You are already enrolled in this course.'; $color='orange'; break;
        case 'enrolled_success': $msgText = 'Successfully enrolled in the course!'; break;
        case 'unenroll_error': $msgText = 'Error while unenrolling.'; $color='red'; break;
        default: $msgText = htmlspecialchars($_GET['msg']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Enrolled Courses</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background:#f9f9f9; }
        table { border-collapse: collapse; width: 90%; margin: auto; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        th { background-color: #007BFF; color: white; }
        a.button { text-decoration: none; padding: 6px 12px; background-color: #28a745; color: white; border-radius: 5px; margin: 3px; display: inline-block; }
        a.button:hover { background-color: #218838; }
        .actions { text-align: center; margin-bottom: 20px; }
        .message { text-align: center; font-weight: bold; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="actions">
    <a href="view_courses.php" class="button" style="background-color:#17a2b8;">View All Courses</a>
</div>

<h2 style="text-align:center;">My Enrolled Courses</h2>

<?php if ($msgText): ?>
    <p class="message" style="color:<?= $color ?>"><?= $msgText ?></p>
<?php endif; ?>

<?php
// Fetch enrolled courses with content column added
$stmt = $conn->prepare("
    SELECT c.id, c.title, c.description, c.duration, c.content, i.name AS instructor_name
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    LEFT JOIN instructors i ON c.instructor_id = i.id
    WHERE e.student_id = ?
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
?>
<table>
    <tr>
        <th>Course ID</th>
        <th>Title</th>
        <th>Instructor</th>
        <th>Description</th>
        <th>Duration</th>
        <th>Content</th> <!-- ✅ Added new column header -->
        <th>Actions</th>
    </tr>
    <?php while ($course = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $course['id'] ?></td>
            <td><?= htmlspecialchars($course['title']) ?></td>
            <td><?= htmlspecialchars($course['instructor_name'] ?? "Not Assigned") ?></td>
            <td><?= nl2br(htmlspecialchars($course['description'])) ?></td>
            <td><?= htmlspecialchars($course['duration']) ?></td>
            <td><?= nl2br(htmlspecialchars($course['content'])) ?></td> <!-- ✅ Added new data cell -->
            <td>
                <a class="button" href="course_details.php?course_id=<?= $course['id'] ?>">View Details</a>
                <a class="button" href="unenroll.php?course_id=<?= $course['id'] ?>" onclick="return confirm('Are you sure you want to unenroll?');">Unenroll</a>
                <a class="button" href="submit_review.php?course_id=<?= $course['id'] ?>">Submit Review</a>
                <a class="button" href="download_certificate.php?course_id=<?= $course['id'] ?>">Download Certificate</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
    <p style="text-align:center;">You are not enrolled in any courses yet.</p>
<?php endif;

$stmt->close();
$conn->close();
?>
</body>
</html>

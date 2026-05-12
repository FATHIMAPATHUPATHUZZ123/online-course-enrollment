<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../database.php';

if (!isset($_GET['course_id'])) {
    die("Course ID is missing.");
}

$course_id = intval($_GET['course_id']);

// Fetch course title
$stmt = $conn->prepare("SELECT title FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->bind_result($course_title);
$stmt->fetch();
$stmt->close();

// Fetch reviews with student names, instructor replies and admin replies
$sql = "SELECT r.review, r.reply AS instructor_reply, r.admin_reply, r.created_at, s.name 
        FROM reviews r 
        JOIN students s ON r.student_id = s.id 
        WHERE r.course_id = ?
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reviews for <?php echo htmlspecialchars($course_title); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f8ff; padding: 20px; }
        h2 { color: #004080; }
        .review-box { background: #fff; padding: 12px 16px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .review-student { font-weight: bold; color: #1e3a8a; }
        .review-text { margin: 8px 0; }
        .review-date { font-size: 0.85rem; color: #555; }
        .reply { margin-top: 8px; padding: 8px; background: #e6f2ff; border-left: 4px solid #0073e6; border-radius: 4px; color: #004080; }
        .admin-reply { margin-top: 8px; padding: 8px; background: #fff4e6; border-left: 4px solid #ff8c00; border-radius: 4px; color: #7a4b00; }
        a.back-link { display:inline-block; margin-top: 20px; text-decoration:none; color:#fff; background:#0073e6; padding:8px 12px; border-radius:6px; }
        a.back-link:hover { background:#0056b3; }
    </style>
</head>
<body>
    <h2>Reviews for "<?php echo htmlspecialchars($course_title); ?>"</h2>
    <hr>

    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='review-box'>";
            echo "<div class='review-student'>" . htmlspecialchars($row['name']) . " said:</div>";
            echo "<div class='review-text'>" . nl2br(htmlspecialchars($row['review'])) . "</div>";
            echo "<div class='review-date'>Posted on: " . htmlspecialchars($row['created_at']) . "</div>";

            // Show instructor reply if exists
            if (!empty($row['instructor_reply'])) {
                echo "<div class='reply'><strong>Instructor Reply:</strong> " . nl2br(htmlspecialchars($row['instructor_reply'])) . "</div>";
            }

            // Show admin reply if exists
            if (!empty($row['admin_reply'])) {
                echo "<div class='admin-reply'><strong>Admin Reply:</strong> " . nl2br(htmlspecialchars($row['admin_reply'])) . "</div>";
            }

            echo "</div>";
        }
    } else {
        echo "<p>No reviews yet for this course.</p>";
    }
    ?>

    <a class='back-link' href='view_courses.php'> Back to Courses</a>
</body>
</html>

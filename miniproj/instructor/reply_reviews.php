<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

$instructor_id = intval($_SESSION['instructor_id']);

// Handle instructor reply submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reply = trim($_POST['reply']);
    $review_id = intval($_POST['review_id']);
    if ($reply !== '') {
        // Update reply and instructor_id
        $stmt = $conn->prepare("UPDATE reviews SET reply=?, instructor_id=? WHERE id=?");
        $stmt->bind_param("sii", $reply, $instructor_id, $review_id);
        $stmt->execute();
        $stmt->close();

        // Fetch student email
        $stmt_email = $conn->prepare("
            SELECT s.email, s.name
            FROM reviews r
            JOIN students s ON r.student_id = s.id
            WHERE r.id=?
        ");
        $stmt_email->bind_param("i", $review_id);
        $stmt_email->execute();
        $result_email = $stmt_email->get_result();
        if($result_email && $result_email->num_rows > 0){
            $student = $result_email->fetch_assoc();
            $student_email = $student['email'];
            $student_name = $student['name'];

            // Send email notification (simple mail fallback — adapt if you use PHPMailer)
            $subject = "Your Review Has a Reply from Instructor";
            $message = "Hello $student_name,\n\nYour review has received a reply from your instructor:\n\n$reply\n\nBest regards,\nOnline Course Team";
            $headers = "From: no-reply@yourdomain.com";
            @mail($student_email, $subject, $message, $headers);
        }
        $stmt_email->close();

        echo "<script>alert('Reply sent successfully!'); window.location.href='reply_reviews.php';</script>";
        exit;
    }
}

// Fetch reviews for instructor's courses, include admin reply
$sql = "SELECT r.id, s.name AS student_name, s.email AS student_email, c.title AS course_title, r.review, r.reply AS instructor_reply, r.admin_reply
        FROM reviews r
        JOIN students s ON r.student_id = s.id
        JOIN courses c ON r.course_id = c.id
        WHERE c.instructor_id = ?
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Instructor: View & Reply Reviews</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: Arial, sans-serif; background-color: #f0f8ff; padding: 30px; }
h2 { text-align: center; color: #004080; margin-bottom: 30px; position: relative; }
.dashboard-link { position:absolute; right:0; top:0; text-decoration:none; background:#2563eb; color:#fff; padding:8px 16px; border-radius:6px; font-weight:500; font-size:0.9rem; }
.dashboard-link:hover { background:#1e40af; }
table { width: 100%; background:white; border-radius:10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
th { background-color:#007bff; color:white; padding:12px; text-align:center; }
td { padding:12px; text-align:center; vertical-align:middle; }
form { display:flex; justify-content:center; gap:5px; }
input[type="text"] { width:70%; padding:5px; border-radius:6px; border:1px solid #ccc; }
button { background-color:#007bff; color:white; border:none; padding:6px 12px; border-radius:6px; }
button:hover { background-color:#0056b3; }
.admin-reply { margin-top:8px; padding:8px; background:#fff4e6; border-left:4px solid #ff8c00; border-radius:4px; color:#7a4b00; text-align:left; }
</style>
</head>
<body>

<h2>
    Student Reviews for Your Courses
    <a href="dashboard_instructor.php" class="dashboard-link">← Back to Dashboard</a>
</h2>

<div class="container mt-4">
<table class="table table-bordered">
<thead>
<tr>
<th>Student</th>
<th>Course</th>
<th>Review</th>
<th>Your Reply</th>
</tr>
</thead>
<tbody>
<?php
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='text-align:left;padding-left:12px;'>" . htmlspecialchars($row['student_name']) . "<br><small>" . htmlspecialchars($row['student_email']) . "</small></td>";
        echo "<td>" . htmlspecialchars($row['course_title']) . "</td>";
        echo "<td style='text-align:left;padding-left:12px;'>" . nl2br(htmlspecialchars($row['review']));
        // show admin reply if exists (read-only)
        if (!empty($row['admin_reply'])) {
            echo "<div class='admin-reply'><strong>Admin Reply:</strong> " . nl2br(htmlspecialchars($row['admin_reply'])) . "</div>";
        }
        echo "</td>";
        echo "<td>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='review_id' value='" . $row['id'] . "'>";
        echo "<input type='text' name='reply' value='" . htmlspecialchars(isset($row['instructor_reply']) ? $row['instructor_reply'] : '') . "' placeholder='Enter reply'>";
        echo "<button type='submit'>Send</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No reviews found for your courses.</td></tr>";
}
?>
</tbody>
</table>
</div>

</body>
</html>

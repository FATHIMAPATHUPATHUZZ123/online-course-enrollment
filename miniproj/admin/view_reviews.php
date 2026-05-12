<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include '../database.php';
require '../PHPMailer/class.phpmailer.php';
require '../PHPMailer/class.smtp.php';

// Check if admin is logged in (using your current session)
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    header("Location: admin_login.php");
    exit;
}

// Optional: Admin email from session
$admin_email = isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : '';
$admin_id = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : null;

// Handle admin reply
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_reply'], $_POST['review_id'], $_POST['admin_reply'])){
    $review_id = intval($_POST['review_id']);
    $admin_reply = trim($_POST['admin_reply']);

    if($admin_reply !== ''){
        if ($admin_id !== null) {
            // Update admin_reply and admin_id
            $stmt = $conn->prepare("UPDATE reviews SET admin_reply=?, admin_id=? WHERE id=?");
            $stmt->bind_param("sii", $admin_reply, $admin_id, $review_id);
        } else {
            // Update only admin_reply
            $stmt = $conn->prepare("UPDATE reviews SET admin_reply=? WHERE id=?");
            $stmt->bind_param("si", $admin_reply, $review_id);
        }
        $stmt->execute();
        $stmt->close();

        // Get student info
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

            // Send email via PHPMailer (replace credentials)
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = "smtp.gmail.com";
            $mail->Username = "your_email@gmail.com"; // replace with your Gmail
            $mail->Password = "your_app_password";    // replace with app password
            $mail->SMTPSecure = "tls";
            $mail->Port = 587;

            $mail->setFrom("your_email@gmail.com", "Online Course");
            $mail->addAddress($student_email, $student_name);
            $mail->Subject = "Your Review Has a Reply from Admin";
            $mail->Body = "Hello ".$student_name.",\n\nYour review has received a reply from admin:\n\n".$admin_reply."\n\nBest regards,\nOnline Course Team";

            $status = ($mail->send()) ? 'success' : 'fail';

            // Log email in email_log table (if exists)
            if ($stmt_log = $conn->prepare("INSERT INTO email_log (recipient, subject, message, status) VALUES (?, ?, ?, ?)")) {
                $stmt_log->bind_param("ssss", $student_email, $mail->Subject, $mail->Body, $status);
                $stmt_log->execute();
                $stmt_log->close();
            }
        }
        $stmt_email->close();

        header("Location: view_reviews.php");
        exit;
    }
}

// Fetch all reviews
$sql = "
SELECT r.id, s.name AS student_name, s.email AS student_email, c.title AS course_title,
r.review, r.reply AS instructor_reply, i.name AS instructor_name, r.admin_reply
FROM reviews r
JOIN students s ON r.student_id = s.id
JOIN courses c ON r.course_id = c.id
LEFT JOIN instructors i ON r.instructor_id = i.id
ORDER BY r.created_at DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin: View & Reply Reviews</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{font-family:Arial, sans-serif;background:#f0f8ff;padding:30px;}
h2{text-align:center;margin-bottom:30px;position:relative;color:#004080;}
.dashboard-link{position:absolute;right:0;top:0;text-decoration:none;background:#2563eb;color:#fff;padding:8px 16px;border-radius:6px;}
table{width:100%;background:white;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.1);}
th{background:#007bff;color:white;padding:12px;text-align:center;}
td{padding:12px;text-align:center;vertical-align:middle;}
form{display:flex;justify-content:center;gap:5px;}
input[type="text"]{width:70%;padding:5px;border-radius:6px;border:1px solid #ccc;}
button{background:#007bff;color:white;border:none;padding:6px 12px;border-radius:6px;}
button:hover{background:#0056b3;}
.delete-btn{background:#dc3545;margin-left:8px;}
.delete-btn:hover{background:#b02a37;}
</style>
<script>
function confirmDelete(form) {
    if (confirm("Are you sure you want to delete this review? This action cannot be undone.")) {
        form.submit();
    }
    return false;
}
</script>
</head>
<body>

<h2>All Student Reviews
<a href="dashboard.php" class="dashboard-link">← Back to Dashboard</a>
</h2>

<div class="container mt-4">
<table class="table table-bordered">
<thead>
<tr>
<th>Student</th>
<th>Course</th>
<th>Review</th>
<th>Instructor Reply</th>
<th>Admin Reply</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php
if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td style='text-align:left;padding-left:12px;'>".htmlspecialchars($row['student_name'])."<br><small>".htmlspecialchars($row['student_email'])."</small></td>";
        echo "<td>".htmlspecialchars($row['course_title'])."</td>";
        echo "<td style='text-align:left;padding-left:12px;'>".nl2br(htmlspecialchars($row['review']))."</td>";
        echo "<td>".(!empty($row['instructor_reply']) ? nl2br(htmlspecialchars($row['instructor_reply'])) : '-')."</td>";
        echo "<td>";
        echo "<form method='POST' style='display:inline-block;margin:0;padding:0;'>";
        echo "<input type='hidden' name='review_id' value='".$row['id']."'>";
        echo "<input type='text' name='admin_reply' value='".(isset($row['admin_reply']) ? htmlspecialchars($row['admin_reply']) : '')."' placeholder='Enter reply' required>";
        echo "<button type='submit' name='send_reply'>Send</button>";
        echo "</form>";

        // Delete form
        echo "<form method='POST' action='delete_review.php' style='display:inline-block;margin:0;padding:0;' onsubmit='return confirmDelete(this);'>";
        echo "<input type='hidden' name='review_id' value='".$row['id']."'>";
        echo "<button type='submit' class='delete-btn'>Delete</button>";
        echo "</form>";

        echo "</td>";
        echo "</tr>";
    }
}else{
    echo "<tr><td colspan='6'>No reviews found.</td></tr>";
}
?>
</tbody>
</table>
</div>

</body>
</html>

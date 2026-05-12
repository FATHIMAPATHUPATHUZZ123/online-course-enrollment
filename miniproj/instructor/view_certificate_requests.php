<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

$instructor_id = (int) $_SESSION['instructor_id'];

// Handle optional message flags
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$message_text = '';
$message_class = '';
if ($msg === 'approved') {
    $message_text = "Request approved successfully! Student can now download the certificate.";
    $message_class = "alert-success";
} elseif ($msg === 'rejected') {
    $message_text = "Request rejected successfully!";
    $message_class = "alert-danger";
}

// Prepare and execute query to fetch certificate requests for this instructor
$sql = "
    SELECT cr.id AS request_id, cr.status, cr.requested_at, cr.approved_at,
           s.name AS student_name, s.email AS student_email,
           c.title AS course_title
    FROM certificate_requests cr
    LEFT JOIN students s ON cr.student_id = s.id
    LEFT JOIN courses c ON cr.course_id = c.id
    WHERE cr.instructor_id = ?
    ORDER BY cr.requested_at DESC
";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Certificate Requests</h2>
    <a href="dashboard_instructor.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>

    <?php
    if (!empty($message_text)) {
        echo '<div class="alert ' . $message_class . '">' . htmlspecialchars($message_text) . '</div>';
    }

    if ($result && $result->num_rows > 0) {
        echo '<table class="table table-bordered table-striped">';
        echo '<thead class="table-light"><tr>';
        echo '<th>Student</th>';
        echo '<th>Email</th>';
        echo '<th>Course</th>';
        echo '<th>Status</th>';
        echo '<th>Requested At</th>';
        echo '<th>Action</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        while ($row = $result->fetch_assoc()) {
            // sanitize values
            $student_name = isset($row['student_name']) ? $row['student_name'] : 'Unknown';
            $student_email = isset($row['student_email']) ? $row['student_email'] : 'Unknown';
            $course_title = isset($row['course_title']) ? $row['course_title'] : 'Unknown';
            $status = isset($row['status']) ? $row['status'] : 'pending';
            $requested_at = isset($row['requested_at']) ? $row['requested_at'] : '';
            $approved_at = isset($row['approved_at']) ? $row['approved_at'] : null;
            $request_id = isset($row['request_id']) ? (int)$row['request_id'] : 0;

            echo '<tr>';

            echo '<td>' . htmlspecialchars($student_name) . '</td>';
            echo '<td>' . htmlspecialchars($student_email) . '</td>';
            echo '<td>' . htmlspecialchars($course_title) . '</td>';

            // Status badge
            echo '<td>';
            if ($status === 'pending') {
                echo '<span class="badge bg-warning text-dark">Pending</span>';
            } elseif ($status === 'approved') {
                echo '<span class="badge bg-success">Approved</span>';
            } else {
                echo '<span class="badge bg-danger">Rejected</span>';
            }
            echo '</td>';

            echo '<td>' . htmlspecialchars($requested_at) . '</td>';

            // Action column
            echo '<td>';
            if ($status === 'pending') {
                echo '<a href="approve_certificate.php?request_id=' . $request_id . '" class="btn btn-success btn-sm">Approve</a> ';
                echo '<a href="reject_certificate.php?request_id=' . $request_id . '" class="btn btn-danger btn-sm">Reject</a>';
            } elseif ($status === 'approved') {
                echo 'Approved at ' . htmlspecialchars($approved_at ? $approved_at : '-');
            } else {
                echo 'Rejected';
            }
            echo '</td>';

            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<div class="alert alert-info">No certificate requests yet.</div>';
    }
    ?>
</div>
</body>
</html>

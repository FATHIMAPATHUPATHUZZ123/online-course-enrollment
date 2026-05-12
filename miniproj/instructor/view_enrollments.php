<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

$instructor_id = (int) $_SESSION['instructor_id'];

$sql = "
SELECT 
    s.name AS student_name,
    s.email AS student_email,
    c.title AS course_title,
    e.enrolled_at
FROM enrollments e
JOIN students s ON e.student_id = s.id
JOIN courses c ON e.course_id = c.id
WHERE c.instructor_id = ?
ORDER BY e.enrolled_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("<p>❌ SQL Error: " . $conn->error . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students Enrolled in My Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 40px;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: white;
            padding: 25px 35px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #0d6efd;
            text-align: center;
            margin-bottom: 25px;
        }
        table th {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- 🔙 BACK BUTTON -->
    <a href="dashboard_instructor.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <h2>Students Enrolled in My Courses</h2>

    <?php
    if ($result->num_rows > 0) {
        echo '<table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Course Title</th>
                        <th>Enrolled At</th>
                    </tr>
                </thead>
                <tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['student_name']) . '</td>
                    <td>' . htmlspecialchars($row['student_email']) . '</td>
                    <td>' . htmlspecialchars($row['course_title']) . '</td>
                    <td>' . htmlspecialchars($row['enrolled_at']) . '</td>
                  </tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<div class="alert alert-info text-center">No students have enrolled in your courses yet.</div>';
    }
    ?>
</div>
</body>
</html>

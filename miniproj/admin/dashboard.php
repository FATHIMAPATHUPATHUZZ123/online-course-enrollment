<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include '../database.php';

// Fetch counts safely (compatible with older PHP versions)

// Students
$result = $conn->query("SELECT COUNT(*) AS c FROM students");
$row = $result->fetch_assoc();
$total_students = isset($row['c']) ? $row['c'] : 0;

// Instructors
$result = $conn->query("SELECT COUNT(*) AS c FROM instructors");
$row = $result->fetch_assoc();
$total_instructors = isset($row['c']) ? $row['c'] : 0;

// Courses
$result = $conn->query("SELECT COUNT(*) AS c FROM courses");
$row = $result->fetch_assoc();
$total_courses = isset($row['c']) ? $row['c'] : 0;

// Enrollments
$result = $conn->query("SELECT COUNT(*) AS c FROM enrollments");
$row = $result->fetch_assoc();
$total_enrollments = isset($row['c']) ? $row['c'] : 0;

// Reviews
$result = $conn->query("SELECT COUNT(*) AS c FROM reviews");
$row = $result->fetch_assoc();
$total_reviews = isset($row['c']) ? $row['c'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
<style>

body {
    margin: 0;
    background: #f4faff;
    font-family: 'Poppins', sans-serif;
}

/* HEADER */
.header {
    background: linear-gradient(135deg, #007bff, #4da3ff);
    padding: 30px;
    text-align: center;
    color: white;
    border-radius: 0 0 25px 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
.header h1 {
    margin: 0;
    font-size: 32px;
    font-weight: 600;
    letter-spacing: 1px;
}

/* GRID WRAPPER */
.container {
    width: 95%;
    max-width: 1300px;
    margin: 40px auto;
}

/* NEW STYLISH GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

/* MODERN CARDS */
.card {
    background: white;
    padding: 30px;
    border-radius: 18px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    transition: 0.35s ease;
    border: 1px solid #e4efff;
}
.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

/* CARD TITLE */
.card h3 {
    margin-bottom: 8px;
    font-size: 22px;
    font-weight: 600;
    color: #003366;
}

/* Stats */
.stats {
    font-size: 20px;
    font-weight: 600;
    color: #0077cc;
    margin-bottom: 15px;
}

/* BUTTON */
.card a {
    display: block;
    padding: 12px;
    margin-top: 10px;
    background: #4da3ff;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.25s;
}
.card a:hover {
    background: #0077ff;
}

/* Delete Buttons */
.card a.delete {
    background: #ff4d4d;
}
.card a.delete:hover {
    background: #d93636;
}

/* Logout Button */
.logout {
    text-align: center;
    margin-top: 40px;
}
.logout a {
    padding: 14px 30px;
    background: #ff4d4d;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
}
.logout a:hover {
    background: #d93636;
}

</style>
</head>

<body>

<div class="header">
    <h1>Welcome Admin <?= htmlspecialchars($_SESSION['admin_email']) ?></h1>
</div>

<div class="container">

    <div class="grid">

        <div class="card">
            <h3>Students</h3>
            <div class="stats"><?= intval($total_students) ?></div>
            <a href="view_students.php">Manage Students</a>
            <a class="delete" href="delete_students.php" onclick="return confirm('Delete inactive students?')">Delete Inactive</a>
        </div>

        <div class="card">
            <h3>Instructors</h3>
            <div class="stats"><?= intval($total_instructors) ?></div>
            <a href="view_instructors.php">Manage Instructors</a>
            <a class="delete" href="delete_instructors.php" onclick="return confirm('Delete inactive instructors?')">Delete Inactive</a>
        </div>

        <div class="card">
            <h3>Courses</h3>
            <div class="stats"><?= intval($total_courses) ?></div>
            <a href="view_courses.php">Manage Courses</a>
        </div>

        <div class="card">
            <h3>Enrollments</h3>
            <div class="stats"><?= intval($total_enrollments) ?></div>
            <a href="view_enrollments.php">View Enrollments</a>
        </div>

        <div class="card">
            <h3>Reviews</h3>
            <div class="stats"><?= intval($total_reviews) ?></div>
            <a href="view_reviews.php">View Reviews</a>
        </div>

        <div class="card">
            <h3>Reports</h3>
            <div class="stats">Summary</div>
            <a href="generate_reports.php">Generate Report</a>
        </div>

        <!-- VIEW ASSIGNMENTS -->
        <div class="card">
            <h3>Assignments</h3>
            <div class="stats">All Assignments</div>
            <a href="admin_assignments.php">View Assignments</a>
        </div>

        <!-- FIXED SUBMISSIONS CARD -->
        <div class="card">
            <h3>Submissions</h3>
            <div class="stats">All Submissions</div>
            <span style="color:#555; font-size:14px;">
                Go to <a href="admin_assignments.php">Assignments</a> to view submissions
            </span>
        </div>

    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

</div>

</body>
</html>

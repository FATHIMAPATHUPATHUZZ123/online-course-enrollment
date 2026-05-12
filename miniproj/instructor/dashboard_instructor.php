<?php
session_start();

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

include '../database.php';
$instructor_name = isset($_SESSION['instructor_name']) ? $_SESSION['instructor_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: #f0f2f5;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100%;
            background: #2c3e50;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 20px;
        }
        .sidebar a {
            padding: 12px 20px;
            color: #f1f1f1;
            text-decoration: none;
            font-weight: 500;
            display: block;
        }
        .sidebar a:hover {
            background: #34495e;
        }

        /* Main content */
        .main {
            margin-left: 220px;
            padding: 20px;
        }

        header {
            background: #fff;
            padding: 12px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        header h1 {
            font-size: 20px;
            margin: 0;
        }

        header .welcome {
            font-size: 14px;
            color: #555;
        }

        /* Cards */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
        }

        .card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .card h3 {
            font-size: 16px;
            margin: 0 0 6px 0;
            color: #2c3e50;
        }

        .card p {
            font-size: 13px;
            margin: 0 0 8px 0;
            color: #555;
        }

        .card a {
            display: inline-block;
            padding: 6px 12px;
            background: #2980b9;
            color: white;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
        }

        .card a:hover {
            background: #1f6699;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Instructor</h2>
    <a href="dashboard_instructor.php">Dashboard</a>
    <a href="create_course.php">Create Course</a>
    <a href="view_courses.php">My Courses</a>
    <a href="instructor_assignments.php">Assignments</a>
    <a href="view_enrollments.php">Enrolled Students</a>
    <a href="reply_reviews.php">Reviews</a>
    <a href="add_course_video.php">Course Videos</a>
    <a href="view_certificate_requests.php">Certificate Requests</a>
    <a href="logout_instructor.php">Logout</a>
</div>

<div class="main">
    <header>
        <h1>Dashboard Overview</h1>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($instructor_name); ?></div>
    </header>

    <div class="card-grid">
        <div class="card">
            <h3>Create Course</h3>
            <p>Add new course content.</p>
            <a href="create_course.php">Go →</a>
        </div>

        <div class="card">
            <h3>My Courses</h3>
            <p>Manage your courses.</p>
            <a href="view_courses.php">Go →</a>
        </div>

        <div class="card">
            <h3>Assignments</h3>
            <p>View and grade submissions.</p>
            <a href="instructor_assignments.php">Go →</a>
        </div>

        <div class="card">
            <h3>Enrolled Students</h3>
            <p>See who joined your courses.</p>
            <a href="view_enrollments.php">Go →</a>
        </div>

        <div class="card">
            <h3>Reviews</h3>
            <p>Read and reply to feedback.</p>
            <a href="reply_reviews.php">Go →</a>
        </div>

        <div class="card">
            <h3>Add Assignment</h3>
            <p>Create assignments for courses.</p>
            <a href="add_assignment.php">Go →</a>
        </div>

        <div class="card">
            <h3>Course Videos</h3>
            <p>Upload and manage videos.</p>
            <a href="add_course_video.php">Go →</a>
        </div>

        <div class="card">
            <h3>Certificate Requests</h3>
            <p>Approve or reject certificates.</p>
            <a href="view_certificate_requests.php">Go →</a>
        </div>
    </div>
</div>

</body>
</html>

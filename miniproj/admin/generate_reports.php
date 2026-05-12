
<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include '../database.php';

// Fetch counts safely
function getCount($conn, $table) {
    $q = $conn->query("SELECT COUNT(*) AS count FROM $table");
    if ($q) {
        $row = $q->fetch_assoc();
        return $row['count'];
    }
    return 0;
}

$total_students = getCount($conn, "students");
$total_instructors = getCount($conn, "instructors");
$total_courses = getCount($conn, "courses");
$total_enrollments = getCount($conn, "enrollments");
$total_reviews = getCount($conn, "reviews");

/* CERTIFICATE REPORTS */

// Certificates issued
$total_certificates = getCount($conn, "certificates");

// Certificates approved
$approved_query = $conn->query("SELECT COUNT(*) AS count FROM certificate_requests WHERE status='approved'");
$approved_row = $approved_query->fetch_assoc();
$total_approved_certificates = $approved_row['count'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Reports</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f0f8ff;
    padding: 50px;
}

h2 {
    color: #003366;
    margin-bottom: 20px;
}

.card {
    display: inline-block;
    background: white;
    padding: 20px;
    margin: 10px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    width: 200px;
    text-align: center;
}

.card h3 {
    margin: 10px 0;
    color: #4da3ff;
}

a {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #4da3ff;
}

a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<h2>Admin Reports</h2>

<div class="card">
    <h3>Total Students</h3>
    <p><?php echo $total_students; ?></p>
</div>

<div class="card">
    <h3>Total Instructors</h3>
    <p><?php echo $total_instructors; ?></p>
</div>

<div class="card">
    <h3>Total Courses</h3>
    <p><?php echo $total_courses; ?></p>
</div>

<div class="card">
    <h3>Total Enrollments</h3>
    <p><?php echo $total_enrollments; ?></p>
</div>

<div class="card">
    <h3>Total Reviews</h3>
    <p><?php echo $total_reviews; ?></p>
</div>

<!-- APPROVED CERTIFICATES -->
<div class="card">
    <h3>Certificates Approved</h3>
    <p><?php echo $total_approved_certificates; ?></p>
</div>

<!-- CERTIFICATES ISSUED -->
<div class="card">
    <h3>Certificates Issued</h3>
    <p><?php echo $total_certificates; ?></p>
</div>

<p><a href="dashboard.php">← Back to Dashboard</a></p>

</body>
</html>


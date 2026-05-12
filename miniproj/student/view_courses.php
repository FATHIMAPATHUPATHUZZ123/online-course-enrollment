<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login_student.php");
    exit;
}

$student_id = $_SESSION['student_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Courses</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(rgba(255,255,255,0.9), rgba(255,255,255,0.9)),
                url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=2000&q=80') 
                no-repeat center center/cover;
    margin: 0;
    padding: 20px;
}

/* Top bar */
.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.top-bar h2 {
    margin: 0;
    font-weight: 600;
    color: #0073e6;
}

.settings-btn i {
    font-size: 24px;
    color: #0073e6;
    cursor: pointer;
}

.settings-btn i:hover {
    color: #005bb5;
}

/* Search bar with icon */
.search-container {
    position: relative;
    width: 100%;
    max-width: 500px;
    margin: 0 auto 25px auto;
}

.search-container input {
    width: 100%;
    padding: 12px 45px 12px 15px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

.search-container .fa-search {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #0073e6;
    font-size: 18px;
}

/* Course Grid */
.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}

.course-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 0 18px rgba(0,0,0,0.12);
    transition: all 0.3s;
    background: #fff;
}

.course-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.18);
}

.course-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.course-card-body {
    padding: 18px;
}

.course-card h5 {
    font-weight: 600;
    margin-bottom: 8px;
    color: #0073e6;
}

.course-card p {
    font-size: 0.9rem;
    margin-bottom: 8px;
}

.course-card .btn {
    margin-right: 5px;
    margin-top: 5px;
}

/* Logout button */
.logout-btn {
    text-align: right;
    margin-bottom: 15px;
}
</style>
</head>
<body>

<div class="top-bar">
    <h2>All Courses</h2>
    <div class="settings-btn">
        <a href="edit_profile.php" title="Edit Profile"><i class="fa-solid fa-gear"></i></a>
    </div>
</div>

<div class="search-container">
    <input type="text" id="searchInput" placeholder="Search courses by title, description, or duration...">
    <i class="fa fa-search"></i>
</div>

<div class="logout-btn">
    <a href="logout_student.php" class="btn btn-danger btn-sm">Logout</a>
</div>

<div class="card-grid" id="courseGrid">
    <!-- Courses will load here -->
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- AJAX Search -->
<script>
const courseGrid = document.getElementById('courseGrid');
const searchInput = document.getElementById('searchInput');

function loadCourses(query = '') {
    fetch('ajax_search_courses.php?search=' + encodeURIComponent(query))
        .then(res => res.text())
        .then(data => {
            courseGrid.innerHTML = data;
        });
}

// Initial load
loadCourses();

// Live search
searchInput.addEventListener('keyup', () => {
    loadCourses(searchInput.value);
});
</script>

</body>
</html>

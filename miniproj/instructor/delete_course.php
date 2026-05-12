<?php
session_start();
include '../database.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login_instructor.php");
    exit;
}

$id = $_GET['id'];
$conn->query("DELETE FROM courses WHERE id=$id");

header("Location: view_courses.php");
exit;
?>

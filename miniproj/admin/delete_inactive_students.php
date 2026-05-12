<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include '../database.php';

// Delete all inactive students
$conn->query("DELETE FROM students WHERE status='inactive'");

header("Location: view_students.php");
exit;
?>
